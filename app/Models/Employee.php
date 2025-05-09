<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'employee_name',
        'employee_photo',
        'employee_position',
        'employee_contact',
        'employee_birth',
        'employee_description'
    ];
}
