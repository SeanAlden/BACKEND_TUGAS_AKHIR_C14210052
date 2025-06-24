<?php

namespace App\Http\Controllers\API;

use Psy\Util\Str;
use App\Models\User;
use App\Rules\ReChaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //// Validasi data input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'usertype' => 'employee', // default value
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users,email|max:255|email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|confirmed|min:5|max:255'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'usertype' => 'employee' // default value
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $reesponse = [
            'user' => $user,
            'token' => $token
        ];
        return response($reesponse, 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->email)->plainTextToken;
    }

    public function signin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'g-recaptcha-response' => [new ReChaptcha()],
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Bad login crads'
            ], 401); 
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout(Request $request)
    {
        // Jika menggunakan Sanctum (token-based auth)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'status' => 201,
            'message' => 'Logged Out'
        ];
    }

    // Menampilkan semua user
    public function showAllUser()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'message' => 'All users retrieved successfully',
            'data' => $users
        ]);
    }

    // Menampilkan user berdasarkan ID
    public function showUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        // /**
        //  * @var \App\Models\User $user
        //  */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        // /**
        //  * @var \App\Models\User $user
        //  */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Verifikasi password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    // public function uploadProfileImage(Request $request)
    // {
    //     /**
    //      * @var \App\Models\User $user
    //      */
    //     $user = Auth::user();

    //     if ($request->hasFile('image')) {
    //         $file = $request->file('image');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $path = $file->storeAs('public/profile_images', $filename);
    //         Log::info("Image stored at path: " . $path);

    //         $user->profile_image = $filename;
    //         // $user->profile_image = $path;
    //         $user->save();

    //         return response()->json(['message' => 'Image uploaded', 'filename' => $filename]);
    //     }

    //     return response()->json(['error' => 'No image uploaded'], 400);
    // }

    public function updateProfileImage(Request $request)
    {
        // /**
        //  * @var \App\Models\User $user
        //  */
        // // $user = auth()->user();
        $user = Auth::user();

        $request->validate([
            'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            // Hapus gambar lama jika ada
            if ($user->profile_image) {
                Storage::disk('public')->delete('profile_images/' . $user->profile_image);
            }

            $image = $request->file('profile_image');
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('profile_images', $filename, 'public');

            $user->profile_image = $filename;
            $user->save();

            return response()->json([
                'message' => 'Profile image updated successfully',
                'profile_image' => $filename,
            ]);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }

    // public function getProfileImage(Request $request) 
    // {

    //     $user = Auth::user();

    //     if ($user && $user->profile_image) {
    //         return response()->json([
    //             'profile_image' => $user->profile_image,
    //         ]);
    //     }

    //     return response()->json([
    //         'profile_image' => null,
    //     ], 404);
    // }
    public function getProfileImage(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'profile_image' => $user ? $user->profile_image : null,
        ], 200);
    }

    // Step 1: Kirim kode verifikasi ke email
    public function getVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $code = random_int(1000, 9999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $code,
                'created_at' => now()
            ]
        );

        // Kirim email ke user (simulasi)
        Mail::raw("Your reset code is: $code", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Password Reset Code');
        });

        return response()->json(['message' => 'Reset code sent to email.']);
    }

    // Step 2: Verifikasi kode dari user
    public function validateVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired code'], 400);
        }

        // Opsional: expired check (misal 15 menit)
        $expired = Carbon::parse($record->created_at)->addMinutes(15);
        if (now()->greaterThan($expired)) {
            return response()->json(['message' => 'Code expired'], 400);
        }

        return response()->json(['message' => 'Code verified']);
    }

    // Step 3: Simpan password baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Hapus kode verifikasi dari tabel
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset']);
    }
}


