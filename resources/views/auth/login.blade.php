@extends('layouts.app')

@section('content')
    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden bg-black">

        {{-- Animated diagonal grid --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
            style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px);
                   background-size: 48px 48px;">
        </div>

        {{-- Drifting glow (Tailwind's built-in pulse, timing tuned via arbitrary property) --}}
        <div
            class="pointer-events-none absolute -left-40 -top-40 h-[32rem] w-[32rem] rounded-full bg-white/10 blur-3xl animate-pulse [animation-duration:5s]">
        </div>
        <div
            class="pointer-events-none absolute -right-40 -bottom-40 h-[32rem] w-[32rem] rounded-full bg-white/10 blur-3xl animate-pulse [animation-duration:7s] [animation-delay:1s]">
        </div>

        {{-- Floating monochrome pills (Tailwind's built-in bounce, timing tuned via arbitrary property) --}}
        <div class="pointer-events-none absolute inset-0">
            <div
                class="absolute left-[15%] top-[18%] h-24 w-9 rounded-full bg-white/10 ring-1 ring-white/30 animate-bounce [animation-duration:6s]">
            </div>
            <div
                class="absolute left-[8%] top-[42%] h-28 w-9 rounded-full bg-white/10 ring-1 ring-white/30 animate-bounce [animation-duration:7s] [animation-delay:.5s]">
            </div>
            <div
                class="absolute left-[13%] top-[68%] h-20 w-9 rounded-full bg-white/10 ring-1 ring-white/30 animate-bounce [animation-duration:8s] [animation-delay:1s]">
            </div>

            <div
                class="absolute right-[15%] top-[18%] h-24 w-9 rounded-full bg-white/10 ring-1 ring-white/30 animate-bounce [animation-duration:7s] [animation-delay:.3s]">
            </div>
            <div
                class="absolute right-[8%] top-[42%] h-28 w-9 rounded-full bg-white/10 ring-1 ring-white/30 animate-bounce [animation-duration:6s] [animation-delay:.8s]">
            </div>
            <div
                class="absolute right-[13%] top-[68%] h-20 w-9 rounded-full bg-white/10 ring-1 ring-white/30 animate-bounce [animation-duration:9s] [animation-delay:.2s]">
            </div>
        </div>

        {{-- Logo --}}
        <div class="relative z-10 mb-8">
            <div
                class="flex h-32 w-32 items-center justify-center rounded-full bg-white p-4 shadow-[0_0_0_1px_rgba(255,255,255,0.15),0_20px_60px_-15px_rgba(255,255,255,0.25)]">
                <img src="{{ asset('images/Logo SMK Airlangga.png') }}" alt="Logo" class="h-full w-full object-contain">
            </div>
        </div>

        {{-- Card --}}
        <div
            class="relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-white p-8 shadow-[0_30px_80px_-20px_rgba(0,0,0,0.9)]">

            {{-- Accent bar --}}
            <div class="absolute -top-px left-8 right-8 h-[3px] rounded-full bg-black"></div>

            <div class="mb-6 text-center">
                <p class="mb-1 text-xs font-semibold uppercase tracking-[0.3em] text-black/40 plus-jakarta-sans-bold">
                    Selamat datang kembali
                </p>
                <h2 class="text-2xl font-bold text-black plus-jakarta-sans-bold">
                    LOGIN
                </h2>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-black bg-black px-4 py-3 text-sm text-white">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-black plus-jakarta-sans-bold">
                        Email
                    </label>

                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username"
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-4 py-3 text-black transition focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20">

                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mt-5">
                    <label for="password" class="block text-sm font-medium text-black plus-jakarta-sans-bold">
                        Password
                    </label>

                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="mt-1 w-full rounded-lg border border-black/20 bg-white px-4 py-3 text-black transition focus:border-black focus:outline-none focus:ring-2 focus:ring-black/20 plus-jakarta-sans-reguler">

                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="mt-5 flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="h-4 w-4 rounded border-black/30 text-black focus:ring-black plus-jakarta-sans-reguler">

                    <label for="remember_me" class="ml-2 text-sm text-black/70 plus-jakarta-sans-reguler">
                        Remember me
                    </label>
                </div>

                {{-- Action --}}
                <div class="mt-8 flex items-center justify-between">

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-black/70 underline decoration-black/30 underline-offset-4 transition hover:text-black hover:decoration-black plus-jakarta-sans-reguler">
                            Forgot your password?
                        </a>
                    @endif

                    <button type="submit"
                        class="group relative overflow-hidden rounded-lg bg-black px-6 py-3 font-semibold text-white transition plus-jakarta-sans-bold">
                        <span class="relative z-10">Log in</span>
                        <span
                            class="absolute inset-0 -translate-x-full bg-white/10 transition-transform duration-300 group-hover:translate-x-0"></span>
                    </button>

                </div>

            </form>

        </div>

        <p class="relative z-10 mt-8 text-xs tracking-widest text-white/30 plus-jakarta-sans-reguler">
            SMK AIRLANGGA
        </p>

    </div>
@endsection
