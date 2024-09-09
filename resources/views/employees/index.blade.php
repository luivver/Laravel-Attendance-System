@extends('layout')

@section('title', 'Halaman Karyawan')

@section('content')

    <div class="w-full mt-0">
        <div class="relative overflow-hidden">
            <input type="checkbox" class="peer absolute top-0 inset-x-0 w-full opacity-0 h-12 z-10 cursor-pointer">
            <div class="text-sm font-semibold text-white px-2 py-3 bg-primary peer-hover:opacity-90 rounded-lg">
                <i class="fas fa-file-import mr-3 ml-3"></i>
                Import Data Karyawan
            </div>

            <div class="p-2 md:w-1/2 lg:w-1/2 mx-auto transition-all duration-0 max-h-0 peer-checked:max-h-max">
                <form class="w-full border" style="padding:20px" enctype="multipart/form-data" method="POST"
                    action="{{ route('employees.store') }}">
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
                            class="btn btn-success bg-green-500 hover:bg-green-700 text-white text-s font-bold py-1 px-3 p-1 mx-1 rounded mb-3">
                            Import
                        </button>
                        <button
                            class="bg-red-500 hover:bg-red-700 text-white font-bold text-s py-1 px-3 p-1 mx-1 rounded mb-3"
                            type="button" onclick="cancelImportEmployee()">
                            Cancel
                        </button>

                        <script>
                            function cancelImportEmployee() {
                                if (confirm('Are you sure you want to cancel the import?')) {
                                    window.location.href = "{{ route('employees.index') }}";
                                }
                            }
                        </script>
                    </div>
                </form>
            </div>
        </div>

        <div class="w-full mt-0 mb-4">
            <a class="flex items-center justify-between p-5 pr-12 py-3 bg-primary hover:opacity-90 text-sm font-semibold text-white rounded-lg shadow-md focus:outline-none focus:shadow-outline-blue"
                href="{{ route('employee.download.records') }}">
                <div class="flex items-center">
                    <i class="fas fa-download mr-3 animate-bounce"></i>
                    <span>Download Isi Data Karyawan</span>
                </div>
            </a>
        </div>

        {{-- Tabel Seluruh Isi File Data Karyawan --}}
        <div class="w-full mt-0">
            <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
                <i class="fas fa-list mr-3"></i>Isi Data Karyawan
            </div>

            <div class="bg-white overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr
                            class="border-gray-200 bg-gray-100 border-b-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            <th class="p-3"> No. </th>
                            <th class="p-3"> Nama </th>
                            <th class="p-3"> Gender </th>
                            <th class="p-3"> ID </th>
                            <th class="p-3"> NIK </th>
                            <th class="p-3"> NPWP </th>
                            <th class="p-3"> Rekening </th>
                            <th class="p-3"> Lokasi </th>
                            <th class="p-3"> Departemen </th>
                            <th class="p-3"> Jabatan </th>
                            <th class="p-3"> Hari Pertama </th>
                            <th class="p-3"> Hari Kerja </th>
                            <th class="p-3"> Hari Makan </th>
                            <th class="p-3"> Pot Keterlambatan </th>
                            <th class="p-3"> Pot Kuota Kerja</th>
                            <th class="p-3"> Pot Cuti </th>
                            <th class="p-3"> Cuti </th>
                            <th class="p-3"> Izin </th>
                            <th class="p-3"> Sakit </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $i => $employee)
                            @php
                                $lateSeconds = $employee->total_late_seconds;
                                $hours = floor($lateSeconds / 3600);
                                $minutes = floor(($lateSeconds % 3600) / 60);
                                $seconds = $lateSeconds % 60;
                                $formattedLate = sprintf('%02d : %02d : %02d', $hours, $minutes, $seconds);

                                $fqhours = floor($employee->sum_work_time / 3600);
                                $fqminutes = floor(($employee->sum_work_time % 3600) / 60);
                                $fqseconds = $employee->sum_work_time % 60;
                                $formattedFulfilledQuota = sprintf('%02d:%02d:%02d', $fqhours, $fqminutes, $fqseconds);
                            @endphp
                            <tr class="bg-white hover:bg-gray-100 text-center text-sm text-gray-900 whitespace-nowrap">
                                {{-- nomor --}}
                                <td class="p-2 border"> {{ $loop->iteration }} </td>
                                {{-- nama --}}
                                <td class="p-2 border"> {{ $employee->name }} </td>
                                {{-- gender --}}
                                <td class="p-2 border"> {{ $employee->gender }} </td>
                                {{-- nomor karyawan --}}
                                <td class="p-2 border"> {{ $employee->employee_num }} </td>
                                {{-- NIK karyawan --}}
                                <td class="p-2 border"> {{ $employee->nik }} </td>
                                {{-- NPWP --}}
                                <td class="p-2 border"> {{ $employee->npwp }} </td>
                                {{-- nomor rekening --}}
                                <td class="p-2 border"> {{ $employee->no_rek }} </td>
                                {{-- lokasi --}}
                                <td class="p-2 border"> {{ $employee->location }} </td>
                                {{-- department --}}
                                <td class="p-2 border"> {{ $employee->department }} </td>
                                {{-- jabatan --}}
                                <td class="p-2 border"> {{ $employee->position }} </td>
                                {{-- tgl pertama kerja --}}
                                <td class="p-2 border"> {{ $employee->hari_pertama }} </td>
                                {{-- hari kerja --}}
                                <td class="p-2 border"> {{ $employee->total_hari ?? 0 }} hari </td>
                                {{-- hari makan --}}
                                <td class="p-2 border"> {{ $employee->total_hari ?? 0 }} hari </td>
                                {{-- pot keterlambatan --}}
                                <td class="p-2 border"> {{ $formattedLate }} </td>
                                {{-- pot hari kerja --}}
                                <td class="p-2 border"> {{ $formattedFulfilledQuota }} </td>
                                {{-- pot cuti --}}
                                <td class="p-2 border"> {{ $employee->cuti->curr_cuti + $employee->cuti->temp_cuti }} hari </td>
                                {{-- cuti --}}
                                <td class="p-2 border"> {{ $employee->cuti_days }} </td>
                                {{-- izin --}}
                                <td class="p-2 border"> {{ $employee->izin_days }} </td>
                                {{-- sakit --}}
                                <td class="p-2 border"> {{ $employee->sakit_days }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection('content')
