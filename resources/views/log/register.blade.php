<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Register')</title>
    <meta name="description" content="">

    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

        .font-family-karla {
            font-family: karla;
        }

        .bg-primary {
            background: #0A5485;
        }

        .bg-primary:hover {
            background-color: #1a5f8d !important;
        }

        /* ini ngikutin gerakan kursor */
        .nav-item:hover {
            background: #1a5f8d;
        }

        .account-link:hover {
            background: #1a5f8d;
        } */
    </style>
</head>

<body class="h-screen font-sans login bg-white">
    <div class="flex justify-center items-center min-h-screen">
        <div class="w-full max-w-lg">
            {{-- yg bawah ini headernya gitu jgn diapus --}}
            <div class="leading-loose">
                <form method="POST" action="/register" class="max-w-xl m-4 p-10 bg-white rounded shadow-md">
                    @csrf
                    <div class="form-group mb-3 uppercase text-s font-semibold text-gray-700 text-center">
                        Registrasi
                    </div>
                    {{-- nama --}}
                    <div class="form-group mb-3">
                        <label class="block text-sm text-gray-00" for="name">Nama</label>
                        <input type="text" name="name"
                            class="form-control w-full px-5 py-1 text-gray-700 bg-gray-200 rounded"
                            value="{{ old('name') }}" required="" placeholder="Nama Anda" aria-label="Nama" />
                    </div>
                    {{-- email --}}
                    <div class="form-group mb-3">
                        <label class="block text-sm text-gray-00" for="email">Email</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror w-full px-5 py-1 text-gray-700 bg-gray-200 rounded"
                            value="{{ old('email') }}" required="" placeholder="Email Anda" aria-label="Email" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- password --}}
                    <div class="form-group mb-3">
                        <label class="block text-sm text-gray-00" for="password">Kata Sandi</label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror w-full px-5 py-1 text-gray-700 bg-gray-200 rounded"
                            required="" placeholder="Kata Sandi Anda" aria-label="Password" />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- confirm password --}}
                    <div class="form-group">
                        <label class="block text-sm text-gray-00" for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation"
                            class="form-control w-full px-5 py-1 text-gray-700 bg-gray-200 rounded" required=""
                            placeholder="Konfirmasi Kata Sandi Anda" aria-label="Konfirmasi Password" />
                    </div>

                    <div class="text-center px-3 py-3 mt-4">
                        <button
                            class="btn btn-success bg-primary text-white text-s font-semibold py-1 px-20 p-1 mx-1 rounded"
                            type="submit">
                            Registrasi
                        </button>
                    </div>
                    <div class="text-center">
                        <p>Sudah memiliki akun?
                            <a href="/" class="text-blue-500 hover:text-blue-700">
                                Login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
