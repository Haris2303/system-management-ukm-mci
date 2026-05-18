<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="UKM MCI â€” Unit Kegiatan Mahasiswa Teknologi & Informatika. Bergabunglah bersama kami dalam mengeksplorasi dunia teknologi.">
    <title><?php echo $__env->yieldContent('title', 'UKM MCI â€” Mahasiswa Creative & Innovation'); ?></title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400&display=swap"
        rel="stylesheet">

    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Inter', 'SF Pro Display', 'system-ui', 'sans-serif'],
                        body: ['Inter', 'SF Pro Display', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#eef5ff',
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
                        accent: '#0ff4c6',
                        violet: {
                            DEFAULT: '#8b5cf6',
                            deep:    '#7c3aed',
                            soft:    '#a78bfa',
                        },
                        ink: {
                            DEFAULT: '#0d253d',
                            secondary: '#273951',
                            mute: '#64748d',
                        },
                        canvas: {
                            DEFAULT: '#ffffff',
                            soft: '#f6f9fc',
                            cream: '#f5e9d4',
                        },
                        hairline: {
                            DEFAULT: '#e3e8ee',
                            input: '#a8c3de',
                        },
                        ruby: '#ea2261',
                        magenta: '#f96bee',
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
                            '0%': { opacity: '0', transform: 'translateY(32px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        float: {
                            '0%,100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-18px)' }
                        },
                        scan: {
                            '0%': { top: '0%' },
                            '100%': { top: '100%' }
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
            font-family: 'Inter', 'SF Pro Display', system-ui, sans-serif;
            font-feature-settings: "ss01";
        }

        /* â”€â”€ Gradient mesh â€” neon mint (kiri) + biru (tengah) + violet (kanan) â”€â”€ */
        .mesh-bg {
            background:
                radial-gradient(ellipse 70% 60% at 5% 15%,  #0ff4c618 0%, transparent 55%),
                radial-gradient(ellipse 80% 60% at 35% 5%,  #daeaff55 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 70% 10%, #bdd8ff33 0%, transparent 55%),
                radial-gradient(ellipse 50% 45% at 98% 20%, #8b5cf620 0%, transparent 55%),
                #f8faff;
        }

        /* â”€â”€ Grid dot pattern â”€â”€ */
        .dot-grid {
            background-image: radial-gradient(circle, #1a4ff520 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* â”€â”€ Section reveal on scroll â”€â”€ */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* â”€â”€ Navbar blur glass â”€â”€ */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(83, 58, 253, 0.07);
        }

        /* â”€â”€ Gradient text â€” neon mint â†’ biru â†’ violet (logo brand) â”€â”€ */
        .gradient-text {
            background: linear-gradient(135deg, #0ff4c6 0%, #3671ff 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* â”€â”€ Tech card hover â”€â”€ */
        .tech-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .tech-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(26, 79, 245, 0.12);
        }

        /* â”€â”€ Gallery hover â”€â”€ */
        .gallery-item { overflow: hidden; }
        .gallery-item img { transition: transform 0.5s ease; }
        .gallery-item:hover img { transform: scale(1.08); }

        /* â”€â”€ Scrollbar â”€â”€ */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #3671ff; border-radius: 3px; }

        /* â”€â”€ Mobile menu â”€â”€ */
        #mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }

        #mobile-menu.open { max-height: 400px; }

        /* Form focus ring */
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(54, 113, 255, 0.15);
        }

        /* â”€â”€ Noise texture overlay â”€â”€ */
        .noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }
    </style>

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="font-body text-ink antialiased bg-white" style="font-weight: 300;">

    
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-18">

                
                <a href="/" class="flex items-center group">
                    <img src="<?php echo e(asset('assets/logo/brand.png')); ?>" alt="UKM MCI"
                        class="h-10 w-auto group-hover:opacity-90 transition-opacity">
                </a>

                
                <?php ($recruitmentOpen = \App\Models\OpenRecruitment::active()->exists()); ?>
                <div class="hidden lg:flex items-center gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['/#tentang', 'Tentang'], ['/#program', 'Program'], ['/berita', 'Berita', 'berita'], ['/galeri', 'Galeri', 'galeri'], ['/pengurus', 'Pengurus']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$href, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e($href); ?>"
                            class="px-4 py-2 rounded-lg text-sm text-ink-mute hover:text-brand-600 hover:bg-brand-50 transition-all duration-200"
                            style="font-weight:300;">
                            <?php echo e($label); ?>

                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <div class="flex items-center gap-3">
                    <a href="/daftar"
                        class="hidden lg:inline-flex items-center gap-2 px-5 py-2 rounded-full text-white text-sm shadow-lg transition-all duration-200 hover:-translate-y-0.5 <?php echo e($recruitmentOpen ? 'bg-brand-600 hover:bg-brand-700 shadow-brand-200 hover:shadow-brand-300' : 'bg-slate-400 hover:bg-slate-500 shadow-slate-200'); ?>"
                        style="font-weight:400;">
                        <?php echo e($recruitmentOpen ? 'Bergabung Sekarang' : 'Pendaftaran Ditutup'); ?>

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <button id="hamburger" class="lg:hidden p-2 rounded-lg hover:bg-brand-50 transition-colors"
                        aria-label="Menu">
                        <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="ham-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path id="ham-close" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        
        <div id="mobile-menu" class="lg:hidden navbar-glass border-t border-hairline">
            <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col gap-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['/#tentang', 'Tentang Kami'], ['/#program', 'Program'], ['/galeri', 'Galeri'], ['/pengurus', 'Pengurus']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$href, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e($href); ?>"
                        class="mobile-link px-4 py-3 rounded-xl text-sm text-ink-mute hover:bg-brand-50 hover:text-brand-600 transition-colors"
                        style="font-weight:300;">
                        <?php echo e($label); ?>

                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="pt-2 border-t border-hairline mt-1">
                    <a href="/daftar"
                        class="flex items-center justify-center gap-2 px-5 py-3 rounded-full text-white text-sm <?php echo e($recruitmentOpen ? 'bg-brand-600' : 'bg-slate-400'); ?>"
                        style="font-weight:400;">
                        <?php echo e($recruitmentOpen ? 'Bergabung Sekarang' : 'Pendaftaran Ditutup'); ?> &rarr;
                    </a>
                </div>
            </div>
        </div>
    </nav>

    
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <?php echo $__env->make('landing._footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('components.chatbot', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <button id="back-top"
        class="fixed bottom-8 right-8 z-40 w-11 h-11 rounded-full bg-brand-600 text-white shadow-lg shadow-brand-200 flex items-center justify-center opacity-0 translate-y-4 transition-all duration-300 hover:bg-brand-700 hover:-translate-y-1"
        aria-label="Kembali ke atas">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    
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
            }, { threshold: 0.12 });
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
            btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

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
                    if (a.getAttribute('href') === '#' + current) a.classList.add('text-brand-600', 'bg-brand-50');
                });
            });
        })();
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/layouts/app.blade.php ENDPATH**/ ?>