<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_sch',
        'date',
        'work_days',
        'shift_start',
        'shift_end',
    ];

    public function employee() // defines the inverse relationship to the Employee model using num_sch as FK
    {
        return $this->belongsTo(Employee::class, 'num_sch','employee_num');
    }
    
}
