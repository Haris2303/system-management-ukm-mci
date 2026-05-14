{{-- resources/views/landing/_hero.blade.php --}}
<section id="hero" class="relative min-h-screen flex items-center overflow-hidden mesh-bg noise">

    {{-- Decorative blobs — neon mint kiri, violet kanan --}}
    <div class="absolute -top-32 -right-32 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style="background: radial-gradient(circle, #8b5cf622 0%, transparent 70%);"></div>
    <div class="absolute bottom-0 -left-40 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style="background: radial-gradient(circle, #bdf8ec 0%, transparent 70%);"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 w-full py-24">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div class="space-y-8">

                {{-- Badge / pill-tag-soft --}}
                @if ($openRecruitment)
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-brand-200 border border-brand-300/40 text-brand-700 text-xs animate-fade-in"
                        style="font-weight:400; letter-spacing:0.1px; text-transform:uppercase;">
                        <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse-slow inline-block"></span>
                        {{ $openRecruitment->judul }}
                        @if ($openRecruitment->gelombang)
                            · {{ $openRecruitment->gelombang }}
                        @endif
                        · Terbuka untuk Semua Jurusan
                    </div>
                @endif

                {{-- Heading — display-xxl: 56px weight 300 -1.4px tracking --}}
                <div class="space-y-3">
                    <h1 class="font-display text-5xl lg:text-6xl xl:text-7xl leading-[1.03] text-ink"
                        style="font-weight:300; letter-spacing:-1.4px; animation: fadeUp 0.8s 0.1s ease both;">
                        Wujudkan Ide
                        <span class="block gradient-text">Teknologimu</span>
                        Bersama Kami.
                    </h1>
                    <p class="text-lg lg:text-xl text-ink-mute leading-relaxed max-w-lg"
                        style="font-weight:300; animation: fadeUp 0.8s 0.25s ease both;">
                        UKM MCI adalah komunitas mahasiswa yang berdedikasi dalam mengembangkan kemampuan di bidang
                        teknologi, pemrograman, dan inovasi digital.
                    </p>
                </div>

                {{-- Stats — body-tabular with tnum --}}
                <div class="flex items-center gap-8 pt-2" style="animation: fadeUp 0.8s 0.35s ease both;">
                    @foreach ([['200+', 'Anggota Aktif'], ['50+', 'Project Selesai'], ['8', 'Divisi']] as [$n, $l])
                        <div class="text-center">
                            <div class="font-display text-2xl text-brand-600"
                                style="font-weight:300; font-feature-settings:'tnum'; letter-spacing:-0.42px;">
                                {{ $n }}</div>
                            <div class="text-xs text-ink-mute mt-0.5" style="font-weight:400;">{{ $l }}</div>
                        </div>
                        @if (!$loop->last)
                            <div class="w-px h-10 bg-hairline"></div>
                        @endif
                    @endforeach
                </div>

                {{-- CTAs — button-primary-pill: rounded-full 8px 16px --}}
                <div class="flex flex-wrap gap-4" style="animation: fadeUp 0.8s 0.45s ease both;">
                    <a href="#daftar"
                        class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-brand-600 text-white hover:bg-brand-700 shadow-xl shadow-brand-200 hover:shadow-brand-300 hover:-translate-y-1 transition-all duration-200"
                        style="font-weight:400; font-size:16px; line-height:1.0;">
                        Daftar Sekarang
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    {{-- button-secondary: canvas bg, primary text, 1px border --}}
                    <a href="#tentang"
                        class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-white text-brand-600 border border-brand-600 hover:bg-brand-50 hover:-translate-y-1 transition-all duration-200 shadow-sm"
                        style="font-weight:400; font-size:16px; line-height:1.0;">
                        Pelajari Lebih
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Right: Logo + Floating Badges --}}
            <div class="hidden lg:flex justify-center items-center relative"
                style="animation: fadeIn 1s 0.3s ease both;">
                <div class="relative animate-float -translate-y-10">

                    {{-- Logo utama --}}
                    <img src="{{ asset('assets/logo/logo.png') }}" alt="UKM MCI Logo"
                        class="w-96 h-96 object-contain select-none"
                        style="filter: drop-shadow(0 25px 40px rgba(26,79,245,0.2)) drop-shadow(0 8px 16px rgba(139,92,246,0.15));">

                    {{-- Floating badges dinamis dari database (maks 4 divisi) --}}
                    @php
                        $badgePositions = [
                            'absolute -top-4 -right-10',
                            'absolute -bottom-4 -left-10',
                            'absolute top-1/2 -right-14 -translate-y-1/2',
                            'absolute top-1/4 -left-14',
                        ];
                        $badgeColors = [
                            ['bg' => 'bg-brand-100', 'color' => '#1a4ff5'],
                            ['bg' => 'bg-violet-100', 'color' => '#8b5cf6'],
                            ['bg' => 'bg-emerald-100', 'color' => '#059669'],
                            ['bg' => 'bg-amber-100', 'color' => '#d97706'],
                        ];
                    @endphp

                    @foreach ($divisis->take(4) as $divisi)
                        @php $i = $loop->index; @endphp
                        <div class="{{ $badgePositions[$i] }} bg-white rounded-xl shadow-xl shadow-brand-100 px-4 py-3 flex items-center gap-2.5 border border-hairline"
                            style="animation: fadeIn 0.6s {{ 0.4 + $i * 0.15 }}s ease both;">
                            <div class="w-8 h-8 rounded-lg {{ $badgeColors[$i]['bg'] }} flex items-center justify-center shrink-0"
                                style="color: {{ $badgeColors[$i]['color'] }};">
                                <i class="{{ $divisi->icon }} fa-sm"></i>
                            </div>
                            <div>
                                <div class="text-xs text-ink whitespace-nowrap" style="font-weight:400;">
                                    {{ $divisi->nama }}</div>
                                <div class="text-[10px] text-ink-mute">Divisi Aktif</div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Scroll cue --}}
    <div
        class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-ink-mute animate-bounce">
        <span class="text-xs tracking-widest uppercase" style="font-weight:300;">Scroll</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</section>
