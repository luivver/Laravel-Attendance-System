<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Cuti;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    public function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $currentDate = Carbon::now()->format('Y-m-d');

            Cuti::whereDate('exp_temp_cuti', $currentDate)->update(['temp_cuti' => 0]);
        })->cron('* * 1-31 3-5 *'); // every 00:00 from 1-31 march-may
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
