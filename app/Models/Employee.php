<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_num',
        'name',
        'gender',
        'no_rek',
        'npwp',
        'nik',
        'location',
        'department',
        'position',
        'hari_pertama'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'num_sch', 'employee_num');
    }

    public function cuti() // untuk ambil sumIzin di model Cuti
    {
        return $this->hasOne(Cuti::class, 'num_cuti', 'employee_num');
    }

    public function getTotalLateSecondsAttribute()
    {
        return Attendance::where('num_atd', $this->employee_num)
            ->sum('late_seconds');
    }

    public function getSumWorkTimeAttribute()
    {
        return Attendance::where('num_atd', $this->employee_num)
            ->sum('work_dur');
    }

    public function getTotalHariAttribute()
    {
        $totalKerjaMakan = Attendance::where('num_atd', $this->employee_num)
            ->whereNotNull('check_in')
            ->distinct('date')
            ->count('date');

        return $totalKerjaMakan;
    }

    public function getIzinDaysAttribute()
    {
        $leaves = Leave::where('num_lvs', $this->employee_num)
            ->where('status', 'acc')
            ->where('jenis_izin', 'Izin')
            ->get(); 

        $totalDays = 0;

        foreach ($leaves as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            $startTime = Carbon::parse($leave->start_time);
            $endTime = Carbon::parse($leave->end_time);

            $countLeaveDate = $startDate->diffInDays($endDate) + 1;
            $countLeaveTime = $startTime->diffInHours($endTime);

            $days = 0;
            if ($countLeaveDate == 1 && $countLeaveTime > 2 && $countLeaveTime <= 5 && $endTime->hour <= 13) {
                $days += 0.5;
            } elseif ($countLeaveDate == 1 && $countLeaveTime <= 2) {
                $days += 0;
            }
            else {
                $days += $countLeaveDate;
            }

            $totalDays += $days;
        }

        return (float)$totalDays;
    }

    public function getSakitDaysAttribute()
    {
        $leaves = Leave::where('num_lvs', $this->employee_num)
            ->where('status', 'acc')
            ->where('jenis_izin', 'Sakit')
            ->get(); 

        $totalDays = 0;

        foreach ($leaves as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);

            $countLeaveDate = $startDate->diffInDays($endDate) + 1;

            $days = 0;
            $days += $countLeaveDate;
            $totalDays += $days;
        }

        return (float)$totalDays;
    }

    public function getCutiDaysAttribute()
    {
        $leaves = Leave::where('num_lvs', $this->employee_num)
            ->where('status', 'acc')
            ->where('jenis_izin', 'Cuti')
            ->get(); 

        $totalDays = 0;

        foreach ($leaves as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);

            $countLeaveDate = $startDate->diffInDays($endDate) + 1;

            $days = 0;
            $days += $countLeaveDate;
            $totalDays += $days;
        }

        return (float)$totalDays;
    }
}
