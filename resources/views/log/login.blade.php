<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login')</title>
    <meta name="description" content="">

    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

        .font-family-karla {
            font-family: karla;
        }

        .bg-sidebar {
            background: #0A5485;
        }

        .bg-sidebar:hover {
            background-color: #1a5f8d !important;
        }

    </style>
</head>

<body class="h-screen font-sans login bg-white">
    <div class="flex justify-center items-center min-h-screen">
        <div class="w-full max-w-lg">
            <div class="text-center">
                <div class="flex justify-center">
                    <img src="{{ asset('images/logocentrin.png') }}" class="img-fluid"
                        style="max-width: 40%; height: auto;">
                </div>
            </div>

            <div class="leading-loose">

                {{-- print message dari withMessage di registController itu --}}
                {{-- @if ($msg = session()->get('message'))
                    <x-input-error :messages="$msg" class="mt-2" />
                @endif --}}

                <form method="POST" action="/auth" class="max-w-xl m-4 p-10 bg-white rounded shadow-md">
                    @csrf
                    <div class="form-group mb-3 uppercase text-s font-semibold text-gray-700 text-center">
                        Log In
                    </div>
                    {{-- email --}}
                    <div class="form-group mb-3">
                        <label class="block text-sm text-gray-700" for="email">Email</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror w-full px-5 py-1 text-gray-700 bg-gray-200 rounded"
                            value="{{ old('email') }}" required="" placeholder="Masukkan Email" aria-label="Email" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- password --}}
                    <div class="form-group mb-3">
                        <label class="block text-sm text-gray-700">Password</label>
                        <input type="password" name="password" placeholder="Masukkan Password"
                            class="form-control @error('password') is-invalid @enderror w-full px-5 py-1 text-gray-700 bg-gray-200 rounded" />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center px-3 py-3 mt-4">
                        <button
                            class="btn btn-success bg-sidebar text-white text-s font-semibold py-1 px-20 p-1 mx-1 rounded">
                            Login
                        </button>
                    </div>

                    <div class="text-center">
                        <p>Belum memiliki akun?
                            <a href="/register" class="text-blue-500 hover:text-blue-700">
                                Register</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
