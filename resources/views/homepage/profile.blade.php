@extends('layout')

@section('title', 'Halaman Profil')

@section('content')
    @php
        $userName = auth()->user()->name;
        $email = auth()->user()->email;
    @endphp
    <div class="bg-white rounded-lg shadow-xl pb-8">
        <div class="w-full h-[250px]">
            <img src="https://vojislavd.com/ta-template-demo/assets/img/profile-background.jpg"
                class="w-full h-full rounded-tl-lg rounded-tr-lg">
        </div>
        <div class="flex flex-col items-center -mt-20">
            {{-- <img src="https://vojislavd.com/ta-template-demo/assets/img/profile.jpg" --}}
            <img src="{{ asset('images/user.png') }}"
                class="w-40 border-4 border-white rounded-full">
            <div class="flex items-center space-x-2 mt-2">
                <p class="text-2xl">{{ $userName }}</p>
            </div>
            <p class="text-gray-700">{{ $email }}</p>
            <p class="text-sm text-gray-500">Admin</p>
        </div>
    </div>
    <div class="flex-1 bg-white rounded-lg shadow-xl mt-4 p-8">
        <h4 class="text-xl text-gray-900 font-bold">Log Aktivitas Admin</h4>
        <div class="relative px-4">
            <div class="absolute h-full border border-dashed border-opacity-20 border-secondary"></div>

            @foreach ($logs as $log)
            {{-- <pre>{{var_dump($log)}}</pre> --}}
                <div class="flex items-center w-full my-6 -ml-1.5">
                    <div class="w-1/12 z-10">
                        <div class="w-3 h-3 bg-primary rounded-full"></div>
                    </div>
                    <div class="w-11/12">
                        <p class="text-sm">{{ $log->description }}</p>
                        <p class="text-xs text-gray-500">{{ $log->created_at }}</p>
                    </div>
                </div>
            @endforeach

            @if ($logs->isEmpty())
            <div class="flex items-center w-full my-6 -ml-1.5">
                <div class="w-1/12 z-10">
                    <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                </div>
                <div class="w-11/12">
                    <p class="text-sm text-gray-500">Belum ada log aktivitas.</p>
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection
