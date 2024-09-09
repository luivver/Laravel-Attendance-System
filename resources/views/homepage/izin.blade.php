@extends('layout')

@section('title', 'Data Karyawan Izin')

@section('content')

    <div class="text-center mb-3">
        <div class="px-4 py-3 mb-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <p class="text-l font-semibold text-red-600 dark:text-gray-400">
                Banyak Izin: {{ $leaves->total() }}
            </p>
        </div>
        <a class="flex items-center justify-between p-5 pr-12 py-3 bg-primary hover:opacity-90 text-sm font-semibold text-white rounded-lg shadow-md focus:outline-none focus:shadow-outline-blue"
           href="{{ route('download.leave.records') }}">
            <div class="flex items-center">
                <i class="fas fa-download mr-3 animate-bounce"></i>
                <span>Download Data Izin {{ $currentYear }}</span>
            </div>
        </a>
    </div>

    {{-- Tabel Data Izin --}}
    <div class="w-full mt-0">
        <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
            <i class="fas fa-list mr-3"></i>Data Izin {{ $currentYear }}
        </div>
        <div class="bg-white overflow-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        {{-- <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                        </th> --}}
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nama</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">ID</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal Mulai</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal Akhir</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu Mulai</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu Akhir</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Subjek</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Alasan</th>
                        <th class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaves as $leave)
                        @if ($leave->status == 'acc')
                            <tr>
                                {{-- <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    <a class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-3 p-1 mx-1 rounded mb-3" 
                                       href="{{ route('leaves.printPreview', $leave->id) }}" 
                                       target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td> --}}
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ $leave->employee->name }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ $leave->num_lvs }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ \Carbon\Carbon::parse($leave->end_date)->format('d-m-Y') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ \Carbon\Carbon::parse($leave->start_time)->format('H:m:s') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ \Carbon\Carbon::parse($leave->end_time)->format('H:m:s') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ $leave->jenis_izin }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">{{ $leave->reason }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded bg-green-500 text-white">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination controls -->
        <div class="bg-white px-3 py-2 border-t border-gray-200">
            {{ $leaves->links() }}
        </div>
    </div>
@endsection('content')
