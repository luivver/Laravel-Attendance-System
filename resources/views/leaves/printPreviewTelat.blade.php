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

            {{-- pernyataan --}}
            <div class="ml-8 grid gap-4 grid-cols-1 grid-rows-1">
                <div class="text-left font-bold text-black text-base">
                    <p> Dengan ini saya mengajukan permohonan untuk izin datang terlambat/meninggalkan kantor: </p>
                </div>
            </div>

            {{-- isi --}}
            <div class="inline-block-wrapper mx-2">
                <div class="inline-block-item bg-white mb-2 p-2">
                    <div class="p-4">
                        <div class="text-left font-bold text-black text-base uppercase">
                            <p> Nama </p>
                            <p> Departemen </p>
                            <p> Hari/Tanggal </p>
                            <p> Waktu </p>
                            <p> Keperluan </p>
                        </div>
                    </div>
                </div>

                <div class="inline-block-item bg-white mb-2 p-2" style="width: 65%;">
                    <div class="p-4">
                        <div class="text-left font-semibold text-black text-base">
                            <p>: {{ $leave->employee->name }} </p>
                            <p>: {{ $leave->employee->department }} </p>
                            <p>: {{ \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') }} </p>
                            <p>: {{ \Carbon\Carbon::parse($leave->start_time)->format('H:i') }} </p>
                            <p>: {{ $leave->reason }} </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TTD --}}
            <div class="flex flex-1 flex-col md:flex-row lg:flex-row mx-2">

                <div class=" bg-white mb-2 p-2 md:w-1/3 mx-2">
                    <div class="p-4 flex flex-col">
                        <div class="text-left font-semibold text-black text-base">
                            <br>
                            <p>Yang Mengajukan,</p>
                            <br><br><br>
                            <p>(........................................)</p>
                        </div>
                    </div>
                </div>

                <div class=" bg-white mb-2 p-2 md:w-1/3 mx-2">
                    <div class="p-4 flex flex-col">
                    </div>
                </div>

                <div class=" bg-white mb-2 p-2 md:w-1/3 mx-2">
                    <div class="p-4 flex flex-col">
                        <div class="text-left font-semibold text-black text-base">
                            <p>Bandung, {{ \Carbon\Carbon::today()->format('d-m-Y') }}</p>
                            <p>Menyetujui,</p>
                            <br><br><br>
                            <p>(........................................)</p>
                        </div>
                    </div>
                </div>
            </div>

            <center>
                <a href="{{ route('leaves.printTelat', $leave->id) }}" 
                    class="btn btn-info bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-1.5 px-4 p-1 mx-1 rounded mb-3">
                    <i class="fas fa-print mr-1"></i> Print Preview </a>
            </center>
        </div>
    </div>
@endsection
