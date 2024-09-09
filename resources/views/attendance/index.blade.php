@extends('layout')

@section('title', 'Halaman Absensi')

@section('content')

    <div class="w-full mt-0">
        <div class="relative overflow-hidden">
            <input type="checkbox" class="peer absolute top-0 inset-x-0 w-full opacity-0 h-12 z-10 cursor-pointer">
            <div class="text-sm font-semibold text-white px-2 py-3 bg-primary peer-hover:opacity-90 rounded-lg">
                <i class="fas fa-file-import mr-3 ml-3"></i>
                Import File Absensi
            </div>

            <div class="p-2 md:w-1/2 lg:w-1/2 mx-auto transition-all duration-0 max-h-0 peer-checked:max-h-max">
                <form class="w-full border" style="padding:20px" enctype="multipart/form-data" method="POST"
                    action="{{ route('attendance.store') }}">
                    @csrf
                    <div class="form-group md:flex md:items-center mb-6">
                        <div class="md:w-1/3"></div>
                        <label class="mr-2 leading-tight"> File </label>
                        <label class="md:w-2/3 block text-grey font-regular">
                            <input class="form-control mr-2 leading-tight @error('file') is-invalid @enderror"
                                type="file" name="file">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </label>
                    </div>

                    <div class="text-center mb-3">
                        <button
                            class="btn btn-info bg-green-500 hover:bg-green-700 text-white text-s font-bold py-1 px-3 p-1 mx-1 rounded mb-3">
                            Import
                        </button>
                        <button
                            class="bg-red-500 hover:bg-red-700 text-white font-bold text-s py-1 px-3 p-1 mx-1 rounded mb-3"
                            type="button" onclick="cancelImportAttendance()">
                            Cancel
                        </button>

                        <script>
                            function cancelImportAttendance() {
                                if (confirm('Are you sure you want to cancel the import?')) {
                                    window.location.href = "{{ route('attendance.index') }}";
                                }
                            }
                        </script>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Nama File Data Absensi --}}
        <div class="w-full mt-0">
            <div class="relative overflow-hidden">
                <input type="checkbox" class="peer absolute top-0 inset-x-0 w-full opacity-0 h-12 z-10 cursor-pointer">
                <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b peer-hover:bg-gray-300">
                    <i class="fas fa-list ml-3 mr-3"></i>
                    File Absensi
                </div>

                {{-- Isi Tabel --}}
                <div class="bg-white overflow-hidden transition-all duration-0 max-h-0 peer-checked:max-h-max">
                    <div class="bg-white overflow-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Nama File
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fileNames as $fileName)
                                    <tr>
                                        {{-- Nama File --}}
                                        <td class="border px-5 py-5 bg-white text-center text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">{{ $fileName }}</p>
                                        </td>
                                        {{-- Aksi --}}
                                        <td class="border px-4 py-2 bg-white text-center text-sm whitespace-nowrap">
                                            <a href="{{ route('attendance.show', ['attendance' => $fileName]) }}"
                                                class="btn btn-info bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 p-1 mx-1 rounded mb-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a
                                                class="btn btn-info bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 p-1 mx-1 rounded mb-3">
                                                <form method="post"
                                                    action="{{ route('attendance.destroy', ['attendance' => $fileName]) }}"
                                                    onsubmit="return confirm('Are you sure you want to delete this file?')"
                                                    style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="font-bold">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="text-center mb-3">
            <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-xl dark:bg-gray-800">
                <p class="text-l font-bold text-gray-600 dark:text-gray-800">
                    Banyak Keterlambatan : {{ $lateCount }}
                </p>
                <p class="text-l font-bold text-gray-600 dark:text-gray-800">
                    Banyak Kuota Harian Tidak Terpenuhi : {{ $quotaCount }}
                </p>
            </div>
        </div>

        {{-- filter utk tanggal & bulan & tanggal --}}
        <div class="w-full p-3 overflow-x-auto">
            <form method="GET" action="{{ route('attendance.index') }}">
                <div class="flex justify-left space-x-4">

                    {{-- Filter Tahun --}}
                    <div class="form-group">
                        <label for="year" class="font-thin whitespace-nowrap">Pilih Tahun:</label>
                        <select id="year" name="year"
                            class="form-control w-full px-5 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded">
                            <option value="">Semua</option>
                            @foreach (range(now()->year, now()->year - 10) as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Bulan --}}
                    <div class="form-group">
                        <label for="month" class="font-thin whitespace-nowrap">Pilih Bulan:</label>
                        <select id="month" name="month"
                            class="form-control w-full px-5 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded">
                            <option value="">Semua</option>
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $month)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tanggal --}}
                    <div class="form-group">
                        <label for="date" class="font-thin whitespace-nowrap">Pilih Tanggal:</label>
                        <select id="date" name="date"
                            class="form-control w-full px-5 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded">
                            <option value="">Semua</option>
                            @foreach (range(1, 31) as $date)
                                <option value="{{ $date }}" {{ request('date') == $date ? 'selected' : '' }}>
                                    {{ $date }}
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
                            href="{{ route('attendance.download.records', ['year' => request('year'),'month' => request('month'), 'date' => request('date')]) }}">
                            <i class="fas fa-download animate-bounce"></i>
                        </a>
                    </span>

                </div>
            </form>
        </div>

        {{-- Tabel Seluruh Isi File Data Absensi --}}
        <div class="w-full mt-0">
            <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
                <i class="fas fa-list mr-3"></i>Isi File Absensi
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

                                // ambil jadwal shift dari schedule
                                $schedule = $attendance->schedule;
                                $shiftStart = $schedule ? $schedule->shift_start : 'Off';
                                // {{ dd(['employee' => $attendance->employee->name,
                                // 'schedule' => $schedule,
                                // 'tap in' => $shiftStart]); }}
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
                                    {{-- Nama Karyawan --}}
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
