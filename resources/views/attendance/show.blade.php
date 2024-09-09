@extends('layout')

@section('title', 'Halaman Absensi (File)')

@section('content')

    <div class="w-full mt-0">
        <div class="text-center mb-3">
            <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
                <p class="text-l font-semibold text-gray-600 dark:text-gray-400">
                    Banyak Keterlambatan : {{ $lateCount }}
                </p>
                <p class="text-l font-semibold text-gray-600 dark:text-gray-400">
                    Banyak Kuota Harian Tidak Terpenuhi : {{ $quotaCount }}
                </p>
            </div>
        </div>

        <div class="w-full p-3">
            <form method="GET" action="{{ route('attendance.show', $fileName) }}" class="text-left">
                <div class="flex justify-between space-x-4">

                    <div class="flex space-x-4">
                        <!-- Filter Department -->
                        <div class="form-group">
                            <label for="filter_department" class="font-thin">Pilih Departemen:</label>
                            <select id="filter_department" name="department"
                                class="form-control w-full px-5 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded">
                                <option value="" {{ request('department') == '' ? 'selected' : '' }}>Semua</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department }}"
                                        {{ request('department') == $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <span class="inline-block mt-6">
                            <a
                                class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-4 p-1 mx-1 rounded mb-3">
                                <button> Filter </button>
                            </a>
                        </span>

                        <span class="inline-block mt-6">
                            <a class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-4 p-1 mx-1 rounded mb-3"
                                href="{{ route('attendance.download.file', ['fileName' => $fileName, 'department' => request('department')]) }}">
                                <button class="animate-bounce"><i class="fas fa-download"></i>
                                </button>
                            </a>
                        </span>

                    </div>
        
                    <span class="inline-block mt-6">
                        <a class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-4 p-1 mx-1 rounded mb-3"
                            href="{{ route('attendance.index') }}">
                            <button type="button">Kembali</button>
                        </a>
                    </span>

                </div>
            </form>
        </div>

        {{-- Tabel Seluruh Isi File Data Absensi --}}
        <div class="w-full mt-0">
            <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
                <i class="fas fa-list mr-3"></i> Isi File Absensi : "{{ $fileName }}"
            </div>

            <div class="bg-white overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Tanggal
                            </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Nama
                            </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                ID
                            </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Tap-In
                            </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Tap-Out
                            </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Status Kehadiran
                            </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Waktu Kerja
                            </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Status Kuota
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            @php
                                $lateSeconds = $attendance->late_seconds;
                                $hours = floor($lateSeconds / 3600);
                                $minutes = floor(($lateSeconds % 3600) / 60);
                                $seconds = $lateSeconds % 60;
                                $formattedLate = sprintf('%02d : %02d : %02d', $hours, $minutes, $seconds);

                                $workTime = $attendance->work_dur;
                                $whours = floor($workTime / 3600);
                                $wminutes = floor(($workTime % 3600) / 60);
                                $wseconds = $workTime % 60;
                                $formattedWorkTime = sprintf('%02d : %02d : %02d', $whours, $wminutes, $wseconds);

                                $schedule = $attendance->schedule;
                                $shiftStart = $schedule ? $schedule->shift_start : 'Off';
                                $shiftEnd = $schedule ? $schedule->shift_end : 'Off';
                                $shiftInfo =
                                    $shiftStart == '08:00:00'
                                        ? 'Shift Pagi (08:00 - 17:00)'
                                        : ($shiftStart == '11:00:00'
                                            ? 'Shift Siang (11:00 - 20:00)'
                                            : 'Off');
                            @endphp
                            <tr>
                                {{-- tanggal --}}
                                <td class="border px-3 py-2 bg-white text-center text-sm">
                                    <p class="text-gray-900 whitespace-nowrap">{{ $attendance->date }}</p>
                                </td>
                                {{-- nama --}}
                                <td class="border px-3 py-2 bg-white text-center text-sm relative">
                                    <p class="text-gray-900 whitespace-nowrap inline">
                                        {{ $attendance->employee ? $attendance->employee->name : '-' }}
                                    </p>
                                    {{-- Bullet Circle with Custom Tooltip --}}
                                    <div class="relative inline-block">
                                        <span
                                            class="inline-block w-2 h-2 rounded-full cursor-pointer
                                            {{ $shiftStart == '08:00:00' ? 'bg-blue-300' : ($shiftStart == '11:00:00' ? 'bg-yellow-300' : 'bg-gray-200') }}">
                                        </span>
                                        <div
                                            class="hidden absolute left-0 bottom-full mb-2 px-3 py-1 
                                            {{ $shiftStart == '08:00:00' ? 'bg-blue-200' : ($shiftStart == '11:00:00' ? 'bg-yellow-200' : 'bg-gray-200') }} 
                                            opacity-90 text-gray-900 text-xs rounded shadow-lg tooltip-content whitespace-nowrap">
                                            {{ $shiftInfo }}
                                        </div>
                                    </div>
                                </td>
                                {{-- nomor karyawan --}}
                                <td class="border px-3 py-2 bg-white text-center text-sm">
                                    <p class="text-gray-900 whitespace-nowrap">{{ $attendance->num_atd }}</p>
                                </td>
                                {{-- waktu cek-in --}}
                                <td class="border px-3 py-2 bg-white text-center text-sm">
                                    <p class="text-gray-900 whitespace-nowrap">{{ $attendance->check_in }}</p>
                                </td>
                                {{-- waktu cek-out --}}
                                <td class="border px-3 py-2 bg-white text-center text-sm">
                                    <p class="text-gray-900 whitespace-nowrap">{{ $attendance->check_out }}</p>
                                </td>
                                {{-- status kehadiran --}}
                                @if ($lateSeconds != 0)
                                    <td class="border px-3 py-2 bg-white text-center text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                            <span aria-hidden
                                                class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                            <span class="relative whitespace-nowrap">Terlambat {{ $formattedLate }}</span>
                                        </span>
                                    </td>
                                @else
                                    <td class="border px-3 py-2 bg-white text-center text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <span aria-hidden
                                                class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                            <span class="relative whitespace-nowrap">Tepat Waktu</span>
                                        </span>
                                    </td>
                                @endif
                                {{-- waktu kerja --}}
                                <td class="border px-3 py-2 bg-white text-center text-sm">
                                    @if ($attendance->employee)
                                        <p class="text-gray-900 whitespace-nowrap">{{ $formattedWorkTime }}</p>
                                    @else
                                        <p class="text-gray-900 whitespace-nowrap">-</p>
                                    @endif
                                </td>
                                {{-- status kuota --}}
                                @if ($workTime < 32400)
                                    <td class="border px-3 py-2 bg-white text-center text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                            <span aria-hidden
                                                class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                            <span class="relative whitespace-nowrap">Kuota Tidak Terpenuhi</span>
                                        </span>
                                    </td>
                                @else
                                    <td class="border px-3 py-2 bg-white text-center text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <span aria-hidden
                                                class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                            <span class="relative whitespace-nowrap">Kuota Terpenuhi</span>
                                        </span>
                                    </td>
                                @endif
                            </tr>

                            {{-- jsnya hover circle samping nama --}}
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    document.querySelectorAll('.relative.inline-block').forEach(function(element) {
                                        element.addEventListener('mouseenter', function() {
                                            const tooltip = this.querySelector('.tooltip-content');
                                            tooltip.classList.remove('hidden');
                                        });

                                        element.addEventListener('mouseleave', function() {
                                            const tooltip = this.querySelector('.tooltip-content');
                                            tooltip.classList.add('hidden');
                                        });
                                    });
                                });
                            </script>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination controls -->
            <div class="bg-white px-3 py-2 border-t border-gray-200">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>

@endsection('content')
