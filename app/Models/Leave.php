<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_lvs',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'jenis_izin',
        'reason',
        'no_telp',
        'status',
    ];

    protected $casts = [ //supaya bisa download formatted dateTime
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'num_lvs', 'employee_num');
    }
 
}
