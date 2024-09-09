@extends('leaves.tempA4')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            {{-- header --}}
            <div class="grid gap-4 grid-cols-2 grid-rows-2">
                <div class="text-left font-bold text-black text-base uppercase ml-8">
                    <p>FORM ADMINISTRASI KEKARYAWANAN</p>
                    <p>PT. CENTRIN ONLINE PRIMA</p>
                </div>
                <div class="ml-20">
                    <img src="{{ asset('images/logocentrin.png') }}" class="ml-20 mt-3" style="max-width: 70%">
                </div>
            </div>

            {{-- isi --}}
            <div class="inline-block-wrapper mx-2">
                <div class="inline-block-item bg-white mb-2 p-2">
                    <div class="p-4">
                        <div class="text-left font-bold text-black text-sm uppercase">
                            <p>NAMA</p>
                            <p>JABATAN</p>
                            <p>JENIS IZIN KARYAWAN</p>
                            <p> </p>
                            <p> </p>
                            <p>KETERANGAN</p>
                            <p>NO. TELEPON YANG BISA DIHUBUNGI</p>
                        </div>
                    </div>
                </div>

                <div class="inline-block-item bg-white mb-2 p-2" style="width: 65%;">
                    <div class="p-4">
                        <div class="text-left font-semibold text-black text-sm">
                            <p>: {{ $leave->employee->name }} </p>
                            <p>: {{ $leave->employee->position }} </p>
                            <span>: {{ $leave->jenis_izin }} </span>
                            <span class="ml-5 font-bold"> Hari/Tanggal </span>
                            <span class="ml-2">: {{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }}
                            </span>
                            <p>: {{ $leave->reason }} </p>
                            <p>: {{ $leave->no_telp }} </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TTD --}}
            <div class="ml-8 grid gap-4 grid-cols-3 grid-rows-3">
                <div class="text-left font-bold text-black text-sm">
                    <p>Pemohon,</p>
                    <br><br><br>
                    <p>....................</p>
                    <p> {{ $leave->employee->name }} </p>
                    <p> {{ $leave->employee->position }} </p>
                </div>
                <div class="text-left font-bold text-black text-sm">
                    <p>Approval Atasan,</p>
                    <br><br><br>
                    <p>....................</p>
                    <p class="italic"> (Nama Jelas & Jabatan) </p>
                </div>
                <div class="text-left font-bold text-black text-sm">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p>....................</p>
                    <p class="italic"> (HRD Depart.) </p>
                </div>
            </div>

            {{-- isi --}}
            <div class="ml-8 grid gap-4 grid-cols-1 grid-rows-1">
                <div class="text-left text-black text-xs">
                    <p class="font-bold italic">Note :</p>
                    <div>
                        <span class="font-bold">*</span><span>Bagi karyawan yang belum bekerja selama 1(satu)
                            tahun tidak memiliki cuti tahunan dan akan memotong upah. </span>
                    </div>
                    <div>
                        <span class="font-bold">*</span><span>Bagi karyawan yang sakit tanpa surat dokter & izin
                            akan memotong cuti tahunan
                            dan jikalau cuti tahunan sudah habis akan memotong upah. </span>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                window.onload = function() {
                    window.print();
                };
            </script>

        </div>
    </div>
@endsection
