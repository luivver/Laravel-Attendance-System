<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    use HasFactory;
    // LogsActivity;

    protected $fillable = [
        'num_atd',
        'date',
        'check_in',
        'check_out',
        'late_seconds',
        'work_dur',
        'file_name'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'num_atd', 'employee_num');
        // mendefinisikan hubungan many-to-one dengan model Employee
    }

    public function schedule()
    {
        return $this->hasOneThrough(
            Schedule::class,
            Employee::class,
            'employee_num', // fk di Employee
            'num_sch',  // fk di Schedule
            'num_atd', // lk di Attendance
            'employee_num' // lk di Employee
        )->where('date', $this->date);
        // menambahkan kondisi untuk mengambil jadwal berdasarkan tanggal yang sama
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('Attendance Log')
    //         ->logOnly(['file_name']);
    // }
    
}
