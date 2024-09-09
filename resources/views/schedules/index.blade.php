@extends('layout')

@section('title', 'Halaman Jadwal')

@section('content')

    <div class="w-full mt-0">
        <div class="relative overflow-hidden">
            <input type="checkbox" class="peer absolute top-0 inset-x-0 w-full opacity-0 h-12 z-10 cursor-pointer">
            <div class="text-sm font-semibold text-white px-2 py-3 bg-primary peer-hover:opacity-90 rounded-lg">
                <i class="fas fa-calendar-plus mr-3 ml-3"></i>
                Update Jadwal Karyawan
            </div>

            <div class="p-2 md:w-1/2 lg:w-1/2 mx-auto transition-all duration-0 max-h-0 peer-checked:max-h-max">
                <form class="w-full border" style="padding:20px" enctype="multipart/form-data" method="POST"
                    action="{{ route('schedules.store') }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label class="block" for="input_num_sch">Nomor Karyawan</label>
                        <input type="text" id="input_num_sch" name="input_num_sch"
                            class="form-control w-full px-2 py-1 text-gray-700 bg-gray-200 rounded" required=""
                            placeholder="Nomor Karyawan">
                    </div>

                    <div class="form-group mb-2">
                        <label for="tgl_shift_start">Tanggal Mulai Shift:</label>
                        <input type="date" id="tgl_shift_start" name="tgl_shift_start"
                            class="w-full px-2 py-1 text-gray-700 rounded bg-gray-200" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="tgl_shift_end">Tanggal Akhir Shift:</label>
                        <input type="date" id="tgl_shift_end" name="tgl_shift_end"
                            class="w-full px-2 py-1 text-gray-700 bg-gray-200 rounded" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="work_days">Hari Kerja</label>
                        <select class="form-control w-full px-2 py-1 text-gray-700 bg-gray-200 rounded" id="work_days"
                            name="work_days" required>
                            <option value="Senin-Jumat">Senin-Jumat</option>
                            <option value="Senin-Sabtu">Senin-Sabtu</option>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label for="shift_start">Start Shift</label>
                        <select class="form-control w-full px-2 py-1 text-gray-700 bg-gray-200 rounded" id="shift_start"
                            name="shift_start" required>
                            <option value="08:00:00">08.00</option>
                            <option value="11:00:00">11.00</option>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label for="shift_end">End Shift</label>
                        <select class="form-control w-full px-2 py-1 text-gray-700 bg-gray-200 rounded" id="shift_end"
                            name="shift_end" required>
                            <option value="17:00:00">17.00</option>
                            <option value="20:00:00">20.00</option>
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit"
                            class="btn btn-info bg-emerald-500 hover:bg-green-600 text-white text-s font-bold py-1 px-3 p-1 mx-1 rounded mb-3">
                            Update Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter Hari Kerja -->
    <div class="w-full p-3 overflow-x-auto">
        <form method="GET" action="{{ route('schedules.index') }}">
            <div class="flex justify-left space-x-4 relative">

                <div class="form-group">
                    <label for="filter_tgl_shift" class="font-thin">Pilih Tanggal Shift:</label>
                    <input type="date" id="filter_tgl_shift" name="filter_tgl_shift"
                        class="form-control w-full px-4 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded mx-auto">
                </div>

                <!-- Filter Department -->
                <div class="form-group">
                    <label for="filter_department" class="font-thin whitespace-nowrap">Pilih Departemen:</label>
                    <select id="filter_department" name="department"
                        class="form-control w-full px-5 py-1.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded mx-auto">
                        <option value="" {{ request('department') == '' ? 'selected' : '' }}>Semua</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department }}"
                                {{ request('department') == $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="filter_hari_kerja" class="font-thin whitespace-nowrap">Pilih Hari Kerja:</label>
                    <select id="filter_hari_kerja" name="work_days"
                        class="form-control w-full px-5 py-1.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded mx-auto">
                        <option value="" {{ request('work_days') == '' ? 'selected' : '' }}>Semua</option>
                        <option value="Senin-Jumat" {{ request('work_days') == 'Senin-Jumat' ? 'selected' : '' }}>
                            Senin-Jumat
                        </option>
                        <option value="Senin-Sabtu" {{ request('work_days') == 'Senin-Sabtu' ? 'selected' : '' }}>
                            Senin-Sabtu
                        </option>
                    </select>
                </div>

                <span class="inline-block mt-7">
                    <a
                        class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-4 p-1 mx-1 rounded mb-3">
                        <button> Filter
                        </button>
                    </a>
                </span>

            </div>
        </form>
    </div>


    <!-- Tabel untuk Jadwal Hari Ini -->

    <div class="w-full mt-0">
        @php
            // $todaySchedule = null;
            $selectedDate = request('filter_tgl_shift') ?? now()->toDateString(); // Gunakan tanggal hari ini sebagai default

            // menggunakan operator null-safe
            $todaySchedule = $employees->first()?->schedules?->where('date', $selectedDate)?->first();
            // $todaySchedule = $employee->schedules->where('date', $selectedDate)->first();
            $todayShiftStart = $todaySchedule?->shift_start;
            $todayShiftEnd = $todaySchedule?->shift_end;
        @endphp

        @if ($todayShiftStart && $todayShiftEnd != NULL)
            <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
                <i class="fas fa-list ml-3 mr-3"></i>
                Jadwal Karyawan
                {{ $selectedDate ? 'untuk tanggal ' . $selectedDate : 'Tidak ada jadwal untuk tanggal ini' }}
            </div> 
        @else
            <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
                <i class="fas fa-list ml-3 mr-3"></i>
                Tidak ada jadwal karyawan untuk tanggal {{ $selectedDate }}
            </div>
        @endif


        {{-- {{ dd($todaySchedule); }} --}}

        <div class="bg-white overflow-x-auto mt-1">
            <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                <thead class="border-t-2 border-b-2 border-neutral-200 font-medium dark:border-white/10">
                    <tr>
                        <th
                            class="px-5 py-3 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nama
                        </th>
                        <th
                            class="px-5 py-3 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th
                            class="px-5 py-3 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Hari Kerja
                        </th>
                        <th
                            class="px-5 py-3 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Shift Start
                        </th>
                        <th
                            class="px-5 py-3 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Shift End
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        @php
                            // $selectedDate = request('filter_tgl_shift') ?? now()->toDateString();
                            // $todaySchedule = $employee->schedules->where('date', $selectedDate)->first();

                            // $todayShiftStart = $todaySchedule ? $todaySchedule->shift_start : 'Off';
                            // $todayShiftEnd = $todaySchedule ? $todaySchedule->shift_end : 'Off';

                            // // Determine the workdays - ensure that this reflects the latest change
                            // $workDays = $todaySchedule ? $todaySchedule->work_days : 'Senin-Jumat';
                            // {{ dd($employees) }}

                            $todaySchedule = $employee->schedules->where('date', $selectedDate)->first();
                            $todayShiftStart = $todaySchedule?->shift_start;
                            $todayShiftEnd = $todaySchedule?->shift_end;
                            $workDays = $todaySchedule?->work_days;
                        @endphp

                        @if ($todayShiftStart && $todayShiftEnd && (request('work_days') == '' || request('work_days') == $workDays))
                            <tr class="border-b border-neutral-200 dark:border-white/10">
                                <td class="whitespace-nowrap px-6 py-2 font-medium text-center">{{ $employee->name }}</td>
                                <td class="whitespace-nowrap px-6 py-2 font-medium text-center">
                                    {{ $employee->employee_num }}</td>
                                <td class="whitespace-nowrap px-6 py-2 font-medium text-center">{{ $workDays }}</td>
                                <td class="whitespace-nowrap px-6 py-2 font-medium text-center">{{ $todayShiftStart }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-2 font-medium text-center">{{ $todayShiftEnd }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
