<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    {{-- @include('partials.head') --}}
    <title>Login - Smartin App</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-lg overflow-hidden flex flex-col md:flex-row">
 
        <div class="md:w-1/2 bg-blue-50 flex items-center justify-center p-8">
            <img src="{{ asset('asset/ilustrasi.png') }}" alt="Smartin Illustration" class="max-w-full h-auto">
        </div>

        <div class="md:w-1/2 p-8">
            <div class="flex items-center justify-center">
                <img src="{{ asset('asset/SmartIn-removebg-preview.png') }}" alt="SmartIn Logo" class="w-auto h-15">
            </div>
            <h2 class="text-3xl font-bold text-cyan-500 mb-2">Selamat Datang!</h2>
            <p class="text-yellow-300 mb-6 ">Masukkan account anda, selamat bekerja</p>

            @if($message = Session::get('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                    {{ $message }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email_or_username" class="block text-sm font-medium text-cyan-500 mb-1">Username</label>
                    <input type="text" name="email_or_username" id="email_or_username" value="{{ old('email_or_username') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Masukkan username">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-cyan-500 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Masukkan password">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">Login</button>
            </form>
        </div>
    </div>
</body>
</html>