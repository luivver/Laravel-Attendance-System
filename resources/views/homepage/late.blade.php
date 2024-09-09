@extends('layout')

@section('title', 'Data Karyawan Terlambat')

@section('content')
    <div class="text-center mb-3">
        <div class="px-4 py-3 mb-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <p class="text-l font-semibold text-red-600 dark:text-gray-400">
                Banyak Keterlambatan : {{ $attendances->total() }}
            </p>
        </div>
        <a class="flex items-center justify-between p-5 pr-12 py-3 bg-primary hover:opacity-90 text-sm font-semibold text-white rounded-lg shadow-md focus:outline-none focus:shadow-outline-blue"
                href="{{ route('download.late.records') }}">
            <div class="flex items-center">
                <i class="fas fa-download mr-3 animate-bounce"></i>
                <span>Download Data Karyawan Terlambat {{ $currentYear }} </span>
            </div>
        </a>
    </div>

    <div class="w-full mt-0">
        <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
            <i class="fas fa-list mr-3"></i>Data Karyawan Terlambat {{ $currentYear }}
        </div>
        <div class="bg-white overflow-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Tanggal
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Nama
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            ID
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Tap-In
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Tap-Out
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Status Kehadiran
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
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
                        @endphp
                        <tr>
                            {{-- tanggal --}}
                            <td class="border px-3 py-2 bg-white text-center text-sm">
                                <p class="text-gray-900 whitespace-nowrap">{{ $attendance->date }}</p>
                            </td>
                            {{-- nama --}}
                            <td class="border px-3 py-2 bg-white text-center text-sm">
                                <p class="text-gray-900 whitespace-nowrap">
                                    {{ $attendance->employee ? $attendance->employee->name : '-' }}</p>
                            </td>
                            {{-- ID --}}
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
                            <td class="border px-3 py-2 bg-white text-center text-sm">
                                <span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                    <span class="relative whitespace-nowrap">Terlambat {{ $formattedLate }}</span>
                                </span>
                            </td>
                            {{-- status kuota --}}
                            @if ($attendance->work_dur < 32400)
                                <td class="border px-3 py-2 bg-white text-center text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
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
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination controls -->
        <div class="bg-white px-3 py-2 border-t border-gray-200">
            {{ $attendances->links() }}
        </div>
    </div>
@endsection('content')
