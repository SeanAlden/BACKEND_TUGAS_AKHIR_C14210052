<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class EmployeeController extends Controller
{
    // Menampilkan data karyawan
    public function index()
    {
        $employees = Employee::all();
        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    // Menambah data karyawan
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'code' => 'required|unique:employees|string|max:255',
    //         'employee_name' => 'required|string|max:255',
    //         'employee_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'employee_position' => 'required|string|max:255',
    //         'employee_birth' => 'required|date',
    //         'employee_contact' => 'required|numeric',
    //         'employee_description' => 'required|string',
    //     ]);

    //     if ($request->hasFile('employee_photo')) {
    //         $photoPath = $request->file('employee_photo')->store('employees', 'public');
    //     } else {
    //         $photoPath = null;
    //     }

    //     $employee = Employee::create([
    //         'code' => $request->code,
    //         'employee_name' => $request->employee_name,
    //         'employee_photo' => $photoPath,
    //         'employee_position' => $request->employee_position,
    //         'employee_birth' => $request->employee_birth,
    //         'employee_contact' => $request->employee_contact,
    //         'employee_description' => $request->employee_description,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Employee created successfully',
    //         'data' => $employee
    //     ], 201);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'code' => 'required|unique:employees|string|max:255',
    //         'employee_name' => 'required|string|max:255',
    //         'employee_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'employee_position' => 'required|string|max:255',
    //         'employee_birth' => 'required|date',
    //         'employee_contact' => 'required|numeric',
    //         'employee_description' => 'required|string',
    //     ]);

    //     // Upload Cloudinary
    //     if ($request->hasFile('employee_photo')) {
    //         $upload = Cloudinary::upload(
    //             $request->file('employee_photo')->getRealPath(),
    //             ['folder' => 'employees']
    //         );

    //         $photoUrl = $upload->getSecurePath(); // URL HTTPS
    //     } else {
    //         $photoUrl = null;
    //     }

    //     $employee = Employee::create([
    //         'code' => $request->code,
    //         'employee_name' => $request->employee_name,
    //         'employee_photo' => $photoUrl,
    //         'employee_position' => $request->employee_position,
    //         'employee_birth' => $request->employee_birth,
    //         'employee_contact' => $request->employee_contact,
    //         'employee_description' => $request->employee_description,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Employee created successfully',
    //         'data' => $employee
    //     ], 201);
    // }

    // Menampilkan data detail karyawan berdasarkan id nya
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }

    //
    // public function update(Request $request, $id)
    // {
    //     $employee = Employee::find($id);

    //     if (!$employee) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Employee not found'
    //         ], 404);
    //     }

    //     $request->validate([
    //         'employee_name' => 'required|string|max:255',
    //         'employee_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'employee_position' => 'required|string|max:255',
    //         'employee_birth' => 'required|date',
    //         'employee_contact' => 'required|numeric',
    //         'employee_description' => 'required|string',
    //     ]);

    //     if ($request->hasFile('employee_photo')) {
    //         if ($employee->employee_photo) {
    //             Storage::disk('public')->delete($employee->employee_photo);
    //         }
    //         $photoPath = $request->file('employee_photo')->store('employees', 'public');
    //         $employee->employee_photo = $photoPath;
    //     }

    //     $employee->update([
    //         'employee_name' => $request->employee_name,
    //         'employee_position' => $request->employee_position,
    //         'employee_birth' => $request->employee_birth,
    //         'employee_contact' => $request->employee_contact,
    //         'employee_description' => $request->employee_description,
    //         'employee_photo' => $employee->employee_photo ?? null,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Employee updated successfully',
    //         'data' => $employee
    //     ]);
    // }

    // Mengubah data karyawan
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        // Gunakan `merge()` agar tidak ada field yang kosong
        $data = $request->except(['employee_photo']);

        if ($request->hasFile('employee_photo')) {
            if ($employee->employee_photo) {
                Storage::disk('public')->delete($employee->employee_photo);
            }
            $photoPath = $request->file('employee_photo')->store('employees', 'public');
            $data['employee_photo'] = $photoPath;
        }

        $employee->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $employee = Employee::find($id);

    //     if (!$employee) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Employee not found'
    //         ], 404);
    //     }

    //     $data = $request->except(['employee_photo']);

    //     if ($request->hasFile('employee_photo')) {

    //         // Upload foto baru ke Cloudinary
    //         $upload = Cloudinary::upload(
    //             $request->file('employee_photo')->getRealPath(),
    //             ['folder' => 'employees']
    //         );

    //         $data['employee_photo'] = $upload->getSecurePath();
    //     }

    //     $employee->update($data);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Employee updated successfully',
    //         'data' => $employee
    //     ]);
    // }

    // Menghapus data karyawan
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        if ($employee->employee_photo) {
            Storage::disk('public')->delete($employee->employee_photo);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }
}
