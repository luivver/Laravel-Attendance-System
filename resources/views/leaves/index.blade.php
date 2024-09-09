@extends('layout')

@section('title', 'Halaman Izin')

@section('content')

    <div class="w-full mt-5">
        <div class="relative overflow-hidden">
            <input type="checkbox" class="peer absolute top-0 inset-x-0 w-full opacity-0 h-12 z-10 cursor-pointer">
            {{-- Form Izin Karyawan --}}
            <div class="text-sm font-semibold text-white px-2 py-3 bg-primary peer-hover:opacity-90 rounded bg-gray-200">
                <i class="fas fa-pen mr-3 ml-3"></i>
                Form Izin Karyawan
            </div>

            <div class="p-0 mt-3 mx-auto transition-all duration-0 max-h-0 peer-checked:max-h-max">
                <form class="w-full border" style="padding:20px" enctype="multipart/form-data"
                    action="{{ route('leaves.store') }}" method="POST">
                    @csrf
                    {{-- Input Nomor Karyawan & Display Nama & Departemen --}}
                    <div class="mb-4 flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="employee_num" class="block text-gray-700 mb-2">Nomor Karyawan:</label>
                            <input type="text" id="employee_num" name="input_id_izin"
                                class="w-full px-2 py-1 border rounded bg-gray-200" placeholder="Masukkan Nomor Karyawan"
                                onblur="fetchEmployeeDetails(this.value)" required>
                        </div>

                        <div class="w-full md:w-1/2 px-3">
                            <div id="employeeDetails" class="form-group hidden">
                                <label class="block text-gray-700 mb-1">Nama :</label>
                                <p id="employeeName" class="text-lg font-semibold mb-1"></p>
                                <label class="block text-gray-700 mb-1">Jabatan :</label>
                                <p id="employeePosition" class="text-lg font-semibold"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Input Calendar --}}
                    <div class="mb-4 flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="start_date" class="block text-gray-700 mb-2">Tanggal Mulai Izin:</label>
                            <input type="date" id="start_date" name="start_date"
                                class="w-full px-2 py-1 border rounded bg-gray-200" required>
                        </div>

                        <div class="w-full md:w-1/2 px-3">
                            <label for="end_date" class="block text-gray-700 mb-2">Tanggal Akhir Izin:</label>
                            <input type="date" id="end_date" name="end_date"
                                class="w-full px-2 py-1 border rounded bg-gray-200" required>
                        </div>
                    </div>

                    {{-- Input Time --}}
                    <div class="mb-4 flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="start_time" class="block text-gray-700 mb-2">Waktu Mulai Izin:</label>
                            <input type="time" id="start_time" name="start_time" value="08:00" min="08:00"
                                max="20:00" class="w-full px-2 py-1 border rounded bg-gray-200" required>
                        </div>

                        <div class="w-full md:w-1/2 px-3">
                            <label for="end_time" class="block text-gray-700 mb-2">Waktu Akhir Izin:</label>
                            <input type="time" id="end_time" name="end_time" value="20:00" min="08:00"
                                max="20:00" class="w-full px-2 py-1 border rounded bg-gray-200" required>
                        </div>
                    </div>

                    {{-- Input Keperluan Izin --}}
                    <div class="mb-4 -mx-3">
                        <div class="w-full md:w-full px-3 mb-6 md:mb-0">
                            <label for="start_date" class="block text-gray-700 mb-2">Keterangan Izin:</label>
                            <select class="form-control w-full px-2 py-1 text-gray-700 bg-gray-200 rounded" id="jenis_izin"
                                name="jenis_izin" required>
                                <option value="" disabled selected>Pilih Keterangan Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Cuti">Cuti</option>
                                <option value="Terlambat">Terlambat</option>
                                <option value="Meninggalkan Kantor">Meninggalkan Kantor</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    {{-- Input Alasan --}}
                    <div class="mb-4">
                        <label for="reason" class="block text-gray-700 mb-2">Alasan Izin:</label>
                        <textarea id="reason" name="reason" class="w-full px-2 py-1 border rounded bg-gray-200" rows="1"
                            placeholder="Masukkan Alasan Izin" required></textarea>
                    </div>

                    <div class="mb-4 flex flex-wrap -mx-3">
                        {{-- Input No. Telp --}}
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="no_telp" class="block text-gray-700 mb-2">Nomor Telepon:</label>
                            <textarea id="no_telp" name="no_telp" class="w-full px-2 py-0.5 border rounded bg-gray-200" rows="1"
                                placeholder="Masukkan Nomor Telepon yang Dapat Dihubungi" required></textarea>
                        </div>

                        {{-- Dropdown Button (Status Izin) --}}
                        <div class="w-full md:w-1/2 px-3">
                            <label for="status" class="block text-gray-700 mb-2">Status Izin:</label>
                            <select id="status" name="status" class="w-full px-2 py-1 border rounded bg-gray-200">
                                <option value="acc">Acc</option>
                                <option value="waiting">Waiting</option>
                                <option value="decline">Decline</option>
                            </select>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="text-center">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white  py-1 px-4 rounded">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div class="w-full p-3 overflow-x-auto">
            <form method="GET" action="{{ route('leaves.index') }}" class="inline-block">
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
                            class="form-control w-full px-1 py-1 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded">
                            <option value="">Semua</option>
                            @foreach (range(1, 31) as $date)
                                <option value="{{ $date }}" {{ request('date') == $date ? 'selected' : '' }}>
                                    {{ $date }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <span class="inline-block mt-6">
                        <button type="submit"
                            class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-0.5 px-4 p-0 mx-1 rounded mb-3">
                            Filter
                        </button>
                    </span>

                    <span class="inline-block mt-6">
                        <a class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-4 p-1 mx-1 rounded mb-3"
                            href="{{ route('leaves.download.records', ['year' => request('year'), 'month' => request('month'), 'date' => request('date')]) }}">
                            <i class="fas fa-download animate-bounce"></i>
                        </a>
                    </span>
                </div>
            </form>
        </div>

        {{-- Tabel Data Izin --}}
        <div class="w-full mt-0">
            <div class="bg-gray-200 px-2 py-3 border-solid border-gray-300 border-b">
                <i class="fas fa-list mr-3"></i>Daftar Izin Karyawan
            </div>

            <div class="bg-white overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Aksi </th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Nama</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                ID</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Tanggal Mulai</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Tanggal Akhir</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Waktu Mulai</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Waktu Akhir</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Subjek</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Alasan</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                No. HP</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th
                                class="px-3 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                                Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaves as $leave)
                            <tr>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    @if (in_array($leave->jenis_izin, ['Sakit', 'Izin', 'Cuti']))
                                        <span class="inline-block">
                                            <a class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-3 p-1 mx-1 rounded mb-3"
                                                href="{{ route('leaves.printPreview', $leave->id) }}" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </span>
                                    @else
                                        <span class="inline-block">
                                            <a class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1.5 px-3 p-1 mx-1 rounded"
                                                href="{{ route('leaves.printPreviewTelat', $leave->id) }}"
                                                target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </span>
                                    @endif
                                    <span class="inline-block">
                                        <form method="post"
                                            action="{{ route('leaves.destroy', ['leaf' => $leave->id]) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this?')"
                                            style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-thin py-1 px-3.5 p-1 mx-1 rounded">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </span>
                                </td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ $leave->employee->name }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ $leave->num_lvs }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d-m-Y') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($leave->start_time)->format('H:i') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($leave->end_time)->format('H:i') }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ $leave->jenis_izin }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ $leave->reason }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    {{ $leave->no_telp }}</td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    @if ($leave->status == 'acc')
                                        <span class="px-2 py-1 rounded bg-green-500 text-white">
                                            {{ ucfirst($leave->status) }} </span>
                                    @elseif ($leave->status == 'waiting')
                                        <span class="px-2 py-1 rounded bg-yellow-500 text-white">
                                            {{ ucfirst($leave->status) }} </span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-red-500 text-white">
                                            {{ ucfirst($leave->status) }} </span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2 bg-white text-center text-sm text-gray-900 whitespace-nowrap">
                                    <form action="{{ route('leaves.updateStatus', $leave->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="px-2 py-1 border rounded">
                                            <option value="acc" {{ $leave->status == 'acc' ? 'selected' : '' }}>Acc
                                            </option>
                                            <option value="waiting" {{ $leave->status == 'waiting' ? 'selected' : '' }}>
                                                Waiting</option>
                                            <option value="decline" {{ $leave->status == 'decline' ? 'selected' : '' }}>
                                                Decline</option>
                                        </select>
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
            <!-- Pagination controls -->
            <div class="bg-white px-3 py-2 border-t border-gray-200">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>

    {{-- ambil info setelah input nomor karyawan --}}
    <script>
        async function fetchEmployeeDetails(employeeNum) {
            try {
                let response = await fetch(`/get-employee-details/${employeeNum}`);
                let data = await response.json();

                console.log(data);

                if (data.name && data.position) {
                    document.getElementById('employeeName').textContent = data.name;
                    document.getElementById('employeePosition').textContent = data.position;
                    document.getElementById('employeeDetails').classList.remove('hidden');
                } else {
                    document.getElementById('employeeDetails').classList.add('hidden');
                }
            } catch (error) {
                console.error('Error fetching employee details:', error);
            }
        }
    </script>

@endsection
