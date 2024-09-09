<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Leave;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_cuti',
        'temp_cuti',
        'curr_cuti',
        'exp_temp_cuti'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'num_cuti', 'employee_num');
    }

    public function generateCuti() // generate cuti pertama kali
    {
        $employees = Employee::all();
        $currentYear = Carbon::now()->year;

        foreach ($employees as $employee) {
            $yearsOfService = Carbon::now()->year - Carbon::parse($employee->hari_pertama)->year;

            if ($yearsOfService < 1) {
                $hireDate = Carbon::parse($employee->hari_pertama);
                $endOfYear = Carbon::create($hireDate->year, 12, 31);
                $monthsWorked = $hireDate->diffInMonths($endOfYear);
                $monthsWorked = $monthsWorked > 0 ? $monthsWorked : 1;
                $curr_cuti = min($monthsWorked, 12);
            } elseif ($yearsOfService >= 1 && $yearsOfService <= 5) {
                $curr_cuti = 12;
            } elseif ($yearsOfService >= 6 && $yearsOfService <= 10) {
                $curr_cuti = 15;
            } else {
                $curr_cuti = 20;
            }

            $exp_temp_cuti = Carbon::create($currentYear, 3, 31)->toDateString();

            Cuti::firstOrCreate(
                ['num_cuti' => $employee->employee_num],
                [
                    'temp_cuti' => null,
                    'curr_cuti' => $curr_cuti,
                    'exp_temp_cuti' => $exp_temp_cuti
                ]
            );
        }
    }

    public function moveCurrToTemp() // menyimpan curr_cuti ke temp_cuti jika sudah akhir tahun
    {
        if (Carbon::now()->isSameDay(Carbon::now()->endOfYear())) {
            $cutis = Cuti::all();

            foreach ($cutis as $cuti) {
                $cuti->temp_cuti = $cuti->curr_cuti;
                $cuti->curr_cuti = 0;
                $cuti->save();

                $this->resetGenerateCurrCuti();
            }
        }
    }

    public function resetGenerateCurrCuti()
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $yearsOfService = Carbon::now()->year - Carbon::parse($employee->hari_pertama)->year;

            if ($yearsOfService < 1) {
                $hireDate = Carbon::parse($employee->hari_pertama);
                $endOfYear = Carbon::create($hireDate->year, 12, 31);
                $monthsWorked = $hireDate->diffInMonths($endOfYear);
                $monthsWorked = $monthsWorked > 0 ? $monthsWorked : 1;
                $curr_cuti = min($monthsWorked, 12);
            } elseif ($yearsOfService >= 1 && $yearsOfService <= 5) {
                $curr_cuti = 12;
            } elseif ($yearsOfService >= 6 && $yearsOfService <= 10) {
                $curr_cuti = 15;
            } else {
                $curr_cuti = 20;
            }

            // update only `curr_cuti`, leaving `temp_cuti` unchanged
            $cuti = Cuti::where('num_cuti', $employee->employee_num)->first();
            if ($cuti) {
                $cuti->curr_cuti = $curr_cuti;
                $cuti->save();
            }
        }
    }

    public function getSumIzinAttribute()
    {
        $leaves = Leave::where('num_lvs', $this->num_cuti)
            // ->where('jenis_izin')
            ->where('status', 'acc')
            ->get();

        $totalDays = 0;

        foreach ($leaves as $leave) {
            $jenis_izin = $leave->jenis_izin;

            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            $startTime = Carbon::parse($leave->start_time);
            $endTime = Carbon::parse($leave->end_time);

            $countLeaveDate = $startDate->diffInDays($endDate) + 1;
            $countLeaveTime = $startTime->diffInHours($endTime);
            $days = 0;

            if ($jenis_izin != 'Sakit') {
                if ($countLeaveDate == 1 && $countLeaveTime <= 5 && $countLeaveTime > 2 && $endTime->hour <= 13) {
                    $days += 0.5; // Izin 1/2 hari
                } elseif ($countLeaveDate == 1 && $countLeaveTime <= 2) {
                    $days += 0; // Terlambat/Meninggalkan kantor
                } else {
                    $days += $countLeaveDate; // Berkurang sesuai banyak hari izin
                }
            }

            $totalDays += $days;
        }

        return $totalDays;
    }
}
