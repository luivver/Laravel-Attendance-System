@extends('layout')

@section('title', 'Halaman Utama')

@section('content')

    @php
        $hour = date('G');
        // $msg = 'Today is ' . date('l, M d, Y.');

        if ($hour >= 5 && $hour < 11) {
            $greet = 'Selamat Pagi,';
        } elseif ($hour >= 11 && $hour < 15) {
            $greet = 'Selamat Siang,';
        } elseif ($hour >= 15 && $hour < 18) {
            $greet = 'Selamat Sore,';
        } else {
            $greet = 'Selamat Malam,';
        }
        $userName = auth()->user()->name;
    @endphp

    <div class="page-wrapper">
        <div class="content container-fluid">
            {{-- Header --}}
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="text-4xl font-bold text-primary shadow-black px-4">
                            {{ $greet }}
                            {{ $userName }}!
                        </h1>
                        {{-- <p class="text-l font-bold text-primary shadow-black p-4"> {{ $msg }} </p> --}}
                        <p class="text-l font-bold text-primary shadow-black p-4"> Selamat Datang di Halaman Utama</p>
                    </div>
                </div>
            </div>

            {{-- Statistics Section 1 --}}
            <div class="flex flex-1 flex-col md:flex-row lg:flex-row mx-2">
                <div
                    class="shadow-lg rounded-lg bg-primary border-l-8 opacity-100 hover:opacity-90 border-sky-900 mb-2 p-2 md:w-1/3 mx-2">
                    <div class="p-4 flex flex-col">
                        <a href="{{ route('homepage.late') }}">
                            <span class="no-underline text-white text-sm uppercase font-bold"> Sebanyak </span>
                            <span class="no-underline text-white text-2xl font-bold"> {{ $lateCount }} </span>
                            <span class="no-underline text-white text-sm uppercase font-bold"> karyawan </span>
                        </a>
                        <a href="{{ route('homepage.late') }}" class="no-underline text-white text-sm uppercase">
                            Hadir Terlambat Tahun Ini
                        </a>
                    </div>
                </div>

                <div
                    class="shadow-lg rounded-lg bg-primary border-l-8 opacity-100 hover:opacity-90 border-sky-900 mb-2 p-2 md:w-1/3 mx-2">
                    <div class="p-4 flex flex-col">
                        <a href="{{ route('homepage.quota') }}">
                            <span class="no-underline text-white text-sm uppercase font-bold"> Sebanyak </span>
                            <span class="no-underline text-white text-2xl font-bold"> {{ $quotaCount }} </span>
                            <span class="no-underline text-white text-sm uppercase font-bold"> kuota hari kerja </span>
                        </a>
                        <a href="{{ route('homepage.quota') }}" class="no-underline text-white text-sm uppercase">
                            Tidak Terpenuhi Tahun Ini
                        </a>
                    </div>
                </div>

                <div
                    class="shadow-lg rounded-lg bg-primary border-l-8 opacity-100 hover:opacity-90 border-sky-900 mb-2 p-2 md:w-1/3 mx-2">
                    <div class="p-4 flex flex-col">
                        <a href="{{ route('homepage.izin') }}">
                            <span class="no-underline text-white text-sm uppercase font-bold"> Sebanyak </span>
                            <span class="no-underline text-white text-2xl font-bold"> {{ $totalIzin }} </span>
                            <span class="no-underline text-white text-sm uppercase font-bold"> izin </span>
                        </a>
                        <a href="{{ route('homepage.izin') }}">
                            <span class="no-underline text-white text-sm uppercase"> disetujui tahun ini </span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Chart Section --}}
            <div class="flex flex-1 flex-col md:flex-row lg:flex-row mx-2">
                <div
                    class="shadow-lg rounded-lg border-l-8 border-sky-900 mb-2 p-2 md:w-1/2 mx-2">
                    <div class="p-4 flex flex-col">
                        <canvas id="latechartMonth"></canvas>
                    </div>
                </div>
                <div
                    class="shadow-lg rounded-lg border-l-8 border-sky-900 mb-2 p-2 md:w-1/2 mx-2">
                    <div class="p-4 flex flex-col">
                        <canvas id="latechartYear"></canvas>
                    </div>
                </div>
            </div>
            {{-- ini scriptnya chart --}}
            <script>
                var ctx = document.getElementById('latechartYear').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($labels) !!},
                        datasets: {!! json_encode($datasets) !!}
                    },
                });
            </script>
            <script>
                var ctx = document.getElementById('latechartMonth').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($labels_m) !!},
                        datasets: {!! json_encode($datasets_m) !!}
                    },
                });
            </script>

            {{-- Yang Izin Hari Ini --}}
            <div class="flex flex-1 flex-col md:flex-row lg:flex-row mx-2">
                <div
                    class="shadow-lg rounded-lg bg-primary border-l-8 opacity-100 hover:opacity-100 border-sky-900 mb-2 p-2 md:w-full mx-2">
                    <div class="p-4 flex flex-col text-white">
                        <h4 class="ml-3 no-underline text-white font-semibold text-2xl mb-2 uppercase">Absensi Hari Ini</h4>
                        <div class="flex flex-wrap">
                            @forelse ($absentees as $absentee)
                                <div
                                    class="flex-1 flex flex-col min-w-[200px] max-w-[calc(50%-1rem)] p-4 text-primary bg-white rounded-lg shadow-xs m-2">
                                    <div class="">
                                        <div class="px-2 py-0 text-primary text-lg font-bold uppercase">
                                            {{ $absentee->employee->name }}
                                        </div>
                                        <div class="col-6">
                                            <span class="px-2 py-1 text-primary font-semibold text-sm text-muted"> Waktu:
                                                {{ \Carbon\Carbon::parse($absentee->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($absentee->end_time)->format('H:i') }}
                                            </span>
                                            <p class="px-2 py-1 text-primary font-semibold text-sm"> Keterangan:
                                                {{ $absentee->jenis_izin }}</p>
                                            {{-- <p class="px-2 py-1 text-primary font-semibold text-sm"> Leave:
                                                {{ $absentee->start_date->format('d M Y') }}</p> --}}
                                        </div>
                                        <div class="col-6 mt-1">
                                            @if ($absentee->status == 'acc')
                                                <span
                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                                    <span aria-hidden
                                                        class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                                    <span class="relative whitespace-nowrap">Approved</span>
                                                </span>
                                            @else
                                                <span
                                                    class="relative inline-block px-3 py-1 font-semibold text-orange-900 leading-tight">
                                                    <span aria-hidden
                                                        class="absolute inset-0 bg-orange-200 opacity-50 rounded-full"></span>
                                                    <span class="relative whitespace-nowrap">Waiting</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="no-underline text-white text-sm">Tidak ada Karyawan izin hari ini</p>
                            @endforelse
                        </div>
                        {{-- Pagination Controls --}}
                        <div class="flex justify-center mt-4 text-white">
                            {{ $absentees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
