<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Layout')</title>
    <meta name="description" content="">
    @vite('resources/css/app.css')

    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');
    </style>

    {{-- untuk barchart di home --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    
</head>

<body class="bg-gray-100 font-family-karla flex" x-data="{ isDrawerOpen: flase }">

    {{-- navigasi --}}

    <div class="min-h-screen bg-primary">
        <div
            class="sidebar min-h-screen w-[3.35rem] overflow-hidden border-r hover:w-56 hover:bg-primary hover:shadow-lg">
            <div class="flex h-screen flex-col justify-between pt-2 pb-6">
                <div>
                    <div class="w-max p-2.5 flex items-center text-white font-bold">
                        <img src="{{ asset('images/cop_100.png') }}" class="w-8 rounded-full mr-3" alt="">
                        Centrin Online Prima
                    </div>
                    <ul class="mt-6 space-y-2 tracking-wide">
                        <li class="min-w-max">
                            <a href="/homepage"
                                class="group flex items-center space-x-4 px-4 py-3 text-white opacity-75 hover:opacity-100 nav-item">
                                <i class="fas fa-window-restore mr-6"></i>
                                Halaman Utama
                            </a>
                        </li>
                        <li class="min-w-max">
                            <a href="/employees"
                                class="group flex items-center space-x-4  px-4 py-3 text-white opacity-75 hover:opacity-100 nav-item">
                                <i class="fas fa-user mr-6"></i>
                                Karyawan
                            </a>
                        </li>
                        <li class="min-w-max">
                            <a href="/schedules"
                                class="group flex items-center space-x-4  px-4 py-3 text-white opacity-75 hover:opacity-100 nav-item">
                                <i class="fas fa-calendar mr-6"></i>
                                Jadwal
                            </a>
                        </li>
                        <li class="min-w-max">
                            <a href="/attendance"
                                class="group flex items-center space-x-4  px-4 py-3 text-white opacity-75 hover:opacity-100 nav-item">
                                <i class="fas fa-table mr-6"></i>
                                Absensi
                            </a>
                        </li>
                        <li class="min-w-max">
                            <a href="/leaves"
                                class="group flex items-center space-x-4 px-4 py-3 text-white opacity-75 hover:opacity-100 nav-item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                                    class="mr-6 w-6 h-6 fill-current">
                                    <!--! Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc. -->
                                    <path
                                        d="M208 96a48 48 0 1 0 0-96 48 48 0 1 0 0 96zM123.7 200.5c1-.4 1.9-.8 2.9-1.2l-16.9 63.5c-5.6 21.1-.1 43.6 14.7 59.7l70.7 77.1 22 88.1c4.3 17.1 21.7 27.6 38.8 23.3s27.6-21.7 23.3-38.8l-23-92.1c-1.9-7.8-5.8-14.9-11.2-20.8l-49.5-54 19.3-65.5 9.6 23c4.4 10.6 12.5 19.3 22.8 24.5l26.7 13.3c15.8 7.9 35 1.5 42.9-14.3s1.5-35-14.3-42.9L281 232.7l-15.3-36.8C248.5 154.8 208.3 128 163.7 128c-22.8 0-45.3 4.8-66.1 14l-8 3.5c-32.9 14.6-58.1 42.4-69.4 76.5l-2.6 7.8c-5.6 16.8 3.5 34.9 20.2 40.5s34.9-3.5 40.5-20.2l2.6-7.8c5.7-17.1 18.3-30.9 34.7-38.2l8-3.5zm-30 135.1L68.7 398 9.4 457.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L116.3 441c4.6-4.6 8.2-10.1 10.6-16.1l14.5-36.2-40.7-44.4c-2.5-2.7-4.8-5.6-7-8.6zM550.6 153.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L530.7 224 384 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l146.7 0-25.4 25.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l80-80c12.5-12.5 12.5-32.8 0-45.3l-80-80z" />
                                </svg>
                                Izin
                            </a>
                        </li>
                        <li class="min-w-max">
                            <a href="/cuti"
                                class="group flex items-center space-x-4  px-4 py-3 text-white opacity-75 hover:opacity-100 nav-item">
                                <i class="fas fa-business-time mr-6"></i>
                                Cuti
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="relative w-full flex flex-col h-screen overflow-y-hidden">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex z-50">
            <div class="w-1/2"></div>
            <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end">
                <button @click="isOpen = !isOpen"
                    class="relative z-10 w-12 h-12 rounded-full overflow-hidden border-4">
                     <img src="{{ asset('images/user.png') }}">
                </button>
                <button x-show="isOpen" @click="isOpen = false"
                    class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-0 mt-16">
                    <a href="/home/profile" class="px-4 py-2 account-link flex items-center justify-center w-full">Account</a>
                    <form action="/logout" method="POST"
                        class="block px-4 py-2 account-link">
                        @csrf
                        <button type="submit" class="flex items-center justify-center w-full">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="w-full h-screen overflow-x-hidden border-t flex flex-col">
            <main class="w-full flex-grow p-6">
                @yield('content')
            </main>
        </div>

    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"
        integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</body>


</html>
