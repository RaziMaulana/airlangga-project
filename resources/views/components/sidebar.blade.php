<div class="fixed left-0 top-0 flex h-screen w-64 flex-col bg-black text-white shadow-2xl tracking-wide">

    <!-- Logo -->
    <div class="flex h-40 flex-col items-center justify-center gap-3 border-b border-white/10">
        <img src="{{ asset('images/Logo SMK Airlangga.png') }}" alt="Logo" class="max-h-20 max-w-20 object-contain">
        <p class="text-[10px] uppercase tracking-[0.3em] text-white/40 plus-jakarta-sans-reguler">
            SMK Airlangga
        </p>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-4 py-8 plus-jakarta-sans-reguler">
        <p class="mb-4 px-4 text-[10px] font-semibold uppercase tracking-[0.25em] text-white/30 plus-jakarta-sans-bold">
            Menu
        </p>

        <ul class="space-y-2">

            <li>
                <a href="{{ route('dashboard.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 transition
                    {{ request()->routeIs('dashboard.*')
                        ? 'bg-white text-black'
                        : 'text-white/60 hover:bg-white/5 hover:text-white' }}">

                    <i class="fa-solid fa-gauge w-5 text-center text-base"></i>

                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('report.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 transition
                    {{ request()->routeIs('report.*') ? 'bg-white text-black' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">

                    <i class="fa-solid fa-chart-column w-5 text-center text-base"></i>

                    <span class="text-sm font-medium">Report</span>
                </a>
            </li>

            <li>
                <a href="{{ route('access.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 transition
                    {{ request()->routeIs('access.*') ? 'bg-white text-black' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">

                    <i class="fa-solid fa-users w-5 text-center text-base"></i>

                    <span class="text-sm font-medium">Access</span>
                </a>
            </li>

            <li>
                <a href="{{ route('feedback.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 transition
        {{ request()->routeIs('feedback.*') ? 'bg-white text-black' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">

                    <!-- Ikon diubah menjadi fa-comment-dots -->
                    <i class="fa-solid fa-comment-dots w-5 text-center text-base"></i>

                    <span class="text-sm font-medium">Feedback</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- Logout -->
    <div class="border-t border-white/10 p-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit"
                class="flex w-full items-center justify-center gap-3 rounded-xl border border-white/20 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-black plus-jakarta-sans-bold">

                <i class="fa-solid fa-right-from-bracket text-sm"></i>

                Logout
            </button>
        </form>
    </div>

</div>
