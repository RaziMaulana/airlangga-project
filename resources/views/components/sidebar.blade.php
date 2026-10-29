<!-- Topbar Mobile -->
<div
    class="fixed inset-x-0 top-0 z-40 flex h-16 items-center justify-between border-b border-white/10 bg-black px-4 text-white shadow-sm lg:hidden">
    <div class="flex items-center gap-2.5">
        <img src="{{ asset('images/Logo SMK Airlangga.png') }}" alt="Logo" class="h-9 w-9 object-contain">
        <span class="text-[10px] uppercase tracking-[0.25em] text-white/50 plus-jakarta-sans-reguler">
            SMK Airlangga
        </span>
    </div>

    <button id="sidebarToggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="mainSidebar"
        class="flex h-10 w-10 items-center justify-center rounded-lg text-white transition hover:bg-white/10">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>
</div>

<!-- Overlay (mobile only) -->
<div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

<!-- Sidebar -->
<div id="mainSidebar"
    class="fixed left-0 top-0 z-50 flex h-screen w-64 -translate-x-full flex-col bg-black text-white shadow-2xl tracking-wide transition-transform duration-300 ease-in-out lg:translate-x-0">

    <!-- Logo -->
    <div class="relative flex h-40 flex-col items-center justify-center gap-3 border-b border-white/10">
        <button id="sidebarClose" type="button" aria-label="Tutup menu"
            class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-lg text-white/60 transition hover:bg-white/10 hover:text-white lg:hidden">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>

        <img src="{{ asset('images/Logo SMK Airlangga.png') }}" alt="Logo" class="max-h-20 max-w-20 object-contain">
        <p class="text-[10px] uppercase tracking-[0.3em] text-white/40 plus-jakarta-sans-reguler">
            SMK Airlangga
        </p>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto px-4 py-8 plus-jakarta-sans-reguler">
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

<script>
    (function() {
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');

        if (!sidebar) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            toggleBtn?.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            toggleBtn?.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        }

        toggleBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) closeSidebar();
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });
    })();
</script>
