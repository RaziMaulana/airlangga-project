@extends('layouts.app')

@section('content')
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">

        <h2 class="mb-6 text-center text-3xl font-bold text-gray-800">
            Register
        </h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                    Name
                </label>

                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('name')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mt-5">
                <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                    Email
                </label>

                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="username"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('email')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-5">
                <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                    Password
                </label>

                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('password')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mt-5">
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">
                    Confirm Password
                </label>

                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-8 flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 transition hover:text-blue-800">
                    Already registered?
                </a>

                <button type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700">
                    Register
                </button>
            </div>

        </form>

    </div>
@endsection
