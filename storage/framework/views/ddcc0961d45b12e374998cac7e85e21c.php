<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Suara — <?php echo e($election->judul); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Syne', 'sans-serif'],
                        body:    ['DM Sans', 'sans-serif'],
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
                    },
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; }

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

        @keyframes ping-dot {
            75%, 100% { transform: scale(1.8); opacity: 0; }
        }
        .ping-dot { animation: ping-dot 1.2s cubic-bezier(0, 0, 0.2, 1) infinite; }

        /* ── Winner animations ── */
        @keyframes glow-ring {
            0%, 100% { box-shadow: 0 0 0 4px rgba(255,255,255,0.5), 0 0 32px rgba(15,244,198,0.4); }
            50%       { box-shadow: 0 0 0 8px rgba(255,255,255,0.2), 0 0 64px rgba(15,244,198,0.8); }
        }
        .winner-photo-ring { animation: glow-ring 2.4s ease-in-out infinite; }

        @keyframes winner-slide-up {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .winner-slide-up { animation: winner-slide-up 0.8s cubic-bezier(0.22, 1, 0.36, 1) both; }

        @keyframes star-float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            50%       { transform: translateY(-14px) rotate(20deg) scale(1.15); }
        }

        @keyframes trophy-pop {
            0%   { transform: scale(0.5) rotate(-10deg); opacity: 0; }
            70%  { transform: scale(1.15) rotate(4deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .trophy-pop { animation: trophy-pop 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both; }

        @keyframes shimmer-winner {
            0%   { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .winner-name-shimmer {
            background: linear-gradient(90deg, #fff 20%, #0ff4c6 50%, #fff 80%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer-winner 3s linear infinite;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #3671ff; border-radius: 3px; }
    </style>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body class="font-body text-slate-800 antialiased bg-white min-h-screen flex flex-col">

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('election-realtime-page', ['electionId' => $election->id]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-4262271769-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

    <script>
        function launchVictoryFireworks() {
            if (typeof confetti !== 'function') return;

            confetti({ particleCount: 120, spread: 70, origin: { x: 0.1, y: 0.6 }, colors: ['#1a4ff5','#0ff4c6','#ffffff','#3671ff','#bdd8ff'] });
            confetti({ particleCount: 120, spread: 70, origin: { x: 0.9, y: 0.6 }, colors: ['#1a4ff5','#0ff4c6','#ffffff','#3671ff','#bdd8ff'] });

            const end = Date.now() + 4200;
            const colors = ['#1340e1', '#3671ff', '#0ff4c6', '#ffffff', '#90bdff', '#5c98ff'];

            (function burst() {
                if (Date.now() > end) return;
                confetti({ particleCount: 40, angle: 60,  spread: 55, startVelocity: 50, origin: { x: 0, y: 0.65 }, colors, ticks: 200 });
                confetti({ particleCount: 40, angle: 120, spread: 55, startVelocity: 50, origin: { x: 1, y: 0.65 }, colors, ticks: 200 });
                requestAnimationFrame(burst);
            }());
        }

        window.addEventListener('election-winner-declared', function () {
            if (window._victoryCelebrated) return;
            window._victoryCelebrated = true;
            launchVictoryFireworks();
        });
    </script>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/elections/rekap.blade.php ENDPATH**/ ?>