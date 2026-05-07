<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="UKM MCI — Unit Kegiatan Mahasiswa Teknologi & Informatika. Bergabunglah bersama kami dalam mengeksplorasi dunia teknologi.">
    <title>@yield('title', 'UKM MCI — Mahasiswa Creative & Innovation')</title>

    {{-- Google Fonts: Syne (display) + DM Sans (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
        rel="stylesheet">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Syne', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef5ff',
                            100: '#daeaff',
                            200: '#bdd8ff',
                            300: '#90bdff',
                            400: '#5c98ff',
                            500: '#3671ff',
                            600: '#1a4ff5',
                            700: '#1340e1',
                            800: '#1635b6',
                            900: '#18338f',
                            950: '#141f57',
                        },
                        accent: '#0ff4c6', // neon mint untuk aksen tech
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.7s ease forwards',
                        'fade-in': 'fadeIn 0.5s ease forwards',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4,0,0.6,1) infinite',
                        'scan': 'scan 3s linear infinite',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(32px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        float: {
                            '0%,100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-18px)'
                            }
                        },
                        scan: {
                            '0%': {
                                top: '0%'
                            },
                            '100%': {
                                top: '100%'
                            }
                        },
                    },
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            font-family: 'DM Sans', sans-serif;
        }

        /* ── Noise texture overlay ── */
        .noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* ── Grid dot pattern ── */
        .dot-grid {
            background-image: radial-gradient(circle, #1a4ff520 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* ── Gradient mesh ── */
        .mesh-bg {
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, #daeaff55 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 90%, #bdd8ff33 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 60% 40%, #0ff4c610 0%, transparent 50%),
                #f8faff;
        }

        /* ── Section reveal on scroll ── */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 {
            transition-delay: 0.1s;
        }

        .reveal-delay-2 {
            transition-delay: 0.2s;
        }

        .reveal-delay-3 {
            transition-delay: 0.3s;
        }

        .reveal-delay-4 {
            transition-delay: 0.4s;
        }

        /* ── Navbar blur glass ── */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(26, 79, 245, 0.08);
        }

        /* ── Gradient text ── */
        .gradient-text {
            background: linear-gradient(135deg, #1340e1 0%, #3671ff 50%, #0ff4c6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Tech card hover ── */
        .tech-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .tech-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(26, 79, 245, 0.12);
        }

        /* ── Gallery hover ── */
        .gallery-item {
            overflow: hidden;
        }

        .gallery-item img {
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #3671ff;
            border-radius: 3px;
        }

        /* ── Mobile menu ── */
        #mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }

        #mobile-menu.open {
            max-height: 400px;
        }

        /* Form focus ring */
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(54, 113, 255, 0.15);
        }
    </style>

    @stack('styles')
</head>

<body class="font-body text-slate-800 antialiased bg-white">

    {{-- ── NAVBAR ─────────────────────────────────────────────── --}}
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-18">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3 group">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-600 to-brand-400 flex items-center justify-center shadow-lg shadow-brand-200 group-hover:scale-105 transition-transform">
                        <span class="text-white font-display font-bold text-sm">M</span>
                    </div>
                    <div>
                        <span class="font-display font-bold text-slate-900 text-lg tracking-tight">MCI</span>
                        <span class="hidden sm:inline text-xs text-slate-400 block -mt-0.5 font-light">Mahasiswa
                            Creative & Innovation</span>
                    </div>
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden lg:flex items-center gap-1">
                    @foreach ([['#tentang', 'Tentang'], ['#program', 'Program'], ['/berita', 'Berita', 'berita'], ['#galeri', 'Galeri'], ['/pengurus', 'Pengurus'], ['/daftar', 'Daftar']] as [$href, $label])
                        <a href="{{ $href }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-brand-600 hover:bg-brand-50 transition-all duration-200">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- CTA + Hamburger --}}
                <div class="flex items-center gap-3">
                    <a href="#daftar"
                        class="hidden lg:inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 shadow-lg shadow-brand-200 hover:shadow-brand-300 transition-all duration-200 hover:-translate-y-0.5">
                        Bergabung Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <button id="hamburger" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors"
                        aria-label="Menu">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="ham-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path id="ham-close" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="lg:hidden navbar-glass border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col gap-1">
                @foreach ([['#tentang', 'Tentang Kami'], ['#program', 'Program'], ['#galeri', 'Galeri'], ['#pengurus', 'Pengurus'], ['#daftar', 'Daftar Anggota']] as [$href, $label])
                    <a href="{{ $href }}"
                        class="mobile-link px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600 transition-colors">
                        {{ $label }}
                    </a>
                @endforeach
                <div class="pt-2 border-t border-slate-100 mt-1">
                    <a href="#daftar"
                        class="flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-brand-600 text-white text-sm font-semibold">
                        Bergabung Sekarang →
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ── MAIN CONTENT ──────────────────────────────────────── --}}
    <main>
        @yield('content')
    </main>

    {{-- ── FOOTER ────────────────────────────────────────────── --}}
    @include('landing._footer')

    @include('components.chatbot')

    {{-- ── BACK TO TOP ────────────────────────────────────────── --}}
    <button id="back-top"
        class="fixed bottom-8 right-8 z-40 w-11 h-11 rounded-xl bg-brand-600 text-white shadow-lg shadow-brand-200 flex items-center justify-center opacity-0 translate-y-4 transition-all duration-300 hover:bg-brand-700 hover:-translate-y-1"
        aria-label="Kembali ke atas">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    {{-- ── GLOBAL SCRIPTS ──────────────────────────────────────── --}}
    <script>
        (() => {
            // Navbar scroll effect
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                navbar.classList.toggle('navbar-glass', window.scrollY > 10);
                navbar.classList.toggle('shadow-sm', window.scrollY > 10);
            });

            // Hamburger toggle
            const ham = document.getElementById('hamburger');
            const menu = document.getElementById('mobile-menu');
            const open = document.getElementById('ham-open');
            const close = document.getElementById('ham-close');
            ham?.addEventListener('click', () => {
                const isOpen = menu.classList.toggle('open');
                open.classList.toggle('hidden', isOpen);
                close.classList.toggle('hidden', !isOpen);
            });
            // Close menu saat klik link
            document.querySelectorAll('.mobile-link').forEach(l =>
                l.addEventListener('click', () => {
                    menu.classList.remove('open');
                    open.classList.remove('hidden');
                    close.classList.add('hidden');
                })
            );

            // Scroll reveal
            const revealEls = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        observer.unobserve(e.target);
                    }
                });
            }, {
                threshold: 0.12
            });
            revealEls.forEach(el => observer.observe(el));

            // Back to top
            const btn = document.getElementById('back-top');
            window.addEventListener('scroll', () => {
                const show = window.scrollY > 500;
                btn.classList.toggle('opacity-0', !show);
                btn.classList.toggle('translate-y-4', !show);
                btn.classList.toggle('opacity-100', show);
                btn.classList.toggle('translate-y-0', show);
            });
            btn.addEventListener('click', () => window.scrollTo({
                top: 0,
                behavior: 'smooth'
            }));

            // Active nav highlight
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            window.addEventListener('scroll', () => {
                let current = '';
                sections.forEach(s => {
                    if (window.scrollY >= s.offsetTop - 100) current = s.id;
                });
                navLinks.forEach(a => {
                    a.classList.remove('text-brand-600', 'bg-brand-50');
                    if (a.getAttribute('href') === '#' + current) a.classList.add('text-brand-600',
                        'bg-brand-50');
                });
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
