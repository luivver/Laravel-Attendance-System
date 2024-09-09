@extends('layout')

@section('title', 'Halaman Cuti')

@section('content')

    @php
        $curr_year = date('Y');
        $last_year = date('Y') - 1;
    @endphp

    {{-- Tabel Cuti --}}
    <div class="w-full mt-0">
        <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
            <i class="fas fa-list mr-3"></i>Informasi Cuti
        </div>

        <div class="bg-white overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr
                        class="border-gray-200 bg-gray-100 border-b-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                        <th class="p-3"> No. </th>
                        <th class="p-3"> Nama </th>
                        <th class="p-3"> ID </th>
                        <th class="p-3"> Pot Cuti {{ $last_year }}</th>
                        <th class="p-3"> Pot Cuti {{ $curr_year }}</th>
                        <th class="p-3"> Cuti </th>
                        <th class="p-3"> Izin </th>
                        <th class="p-3"> Sakit </th>
                        <th class="p-3"> Sisa Pot Cuti </th>
                        <th class="p-3"> Expired Temp </th>
                        <th class="p-3"> Update Expired Temp </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cutis as $i => $cuti)
                        @php
                        @endphp
                        <tr class="bg-white hover:bg-gray-100 text-center text-sm text-gray-900 whitespace-nowrap">
                            {{-- nomor --}}
                            <td class="p-1 border"> {{ $loop->iteration }} </td>
                            {{-- nama --}}
                            <td class="p-1 border"> {{ $cuti->employee->name }} </td>
                            {{-- nomor karyawan --}}
                            <td class="p-1 px-3 border"> {{ $cuti->num_cuti }} </td>
                            {{-- last pot cuti --}}
                            <td class="p-1 border"> {{ $cuti->temp_cuti ?? 0 }} hari </td>
                            {{-- current pot cuti --}}
                            <td class="p-1 border"> {{ $cuti->curr_cuti }} hari </td>
                            {{-- cuti --}}
                            <td class="p-1 border"> {{ $cuti->employee->cuti_days }} </td>
                            {{-- izin --}}
                            <td class="p-1 border"> {{ $cuti->employee->izin_days }} </td>
                            {{-- sakit --}}
                            <td class="p-1 border"> {{ $cuti->employee->sakit_days }} </td>
                            {{-- left pot cuti --}}
                            <td class="p-1 border"> {{ $cuti->curr_cuti + ($cuti->temp_cuti - $cuti->sum_izin) }} hari
                            </td>
                            {{-- expired last pot cuti --}}
                            <td class="p-1 border"> {{ $cuti->exp_temp_cuti }} </td>
                            {{-- update expired cuti --}}
                            <td class="border p-1">
                                <form action="{{ route('cuti.updateExpDate', $cuti->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="date" id="exp_temp" name="exp_temp" 
                                        class="w-1/2 px-2 py-1 text-gray-700 rounded bg-gray-200" required>
                                    <button type="submit"
                                        class="ml-2 bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>

@endsection('content')
