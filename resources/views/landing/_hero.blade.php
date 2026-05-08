{{-- resources/views/landing/_hero.blade.php --}}
<section id="hero" class="relative min-h-screen flex items-center overflow-hidden mesh-bg dot-grid noise">

    {{-- Decorative blobs --}}
    <div
        class="absolute -top-32 -right-32 w-[600px] h-[600px] rounded-full bg-brand-100/60 blur-3xl pointer-events-none">
    </div>
    <div class="absolute bottom-0 -left-40 w-[500px] h-[500px] rounded-full bg-accent/10 blur-3xl pointer-events-none">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 w-full py-24">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div class="space-y-8">

                {{-- Badge --}}
                @if ($openRecruitment)
                    <div
                        class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-sm font-medium animate-fade-in">
                        <span class="w-2 h-2 rounded-full bg-accent animate-pulse-slow inline-block"></span>
                        {{ $openRecruitment->judul }}
                        @if ($openRecruitment->gelombang)
                            · {{ $openRecruitment->gelombang }}
                        @endif
                        · Terbuka untuk Semua Jurusan
                    </div>
                @endif

                {{-- Heading --}}
                <div class="space-y-3">
                    <h1 class="font-display text-5xl lg:text-6xl xl:text-7xl font-bold leading-[1.08] tracking-tight text-slate-900"
                        style="animation: fadeUp 0.8s 0.1s ease both;">
                        Wujudkan Ide
                        <span class="block gradient-text">Teknologimu</span>
                        Bersama Kami.
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-500 font-light leading-relaxed max-w-lg"
                        style="animation: fadeUp 0.8s 0.25s ease both;">
                        UKM MCI adalah komunitas mahasiswa yang berdedikasi dalam mengembangkan kemampuan di bidang
                        teknologi, pemrograman, dan inovasi digital.
                    </p>
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-8 pt-2" style="animation: fadeUp 0.8s 0.35s ease both;">
                    @foreach ([['200+', 'Anggota Aktif'], ['50+', 'Project Selesai'], ['8', 'Divisi']] as [$n, $l])
                        <div class="text-center">
                            <div class="font-display text-2xl font-bold text-brand-600">{{ $n }}</div>
                            <div class="text-xs text-slate-400 font-medium mt-0.5">{{ $l }}</div>
                        </div>
                        @if (!$loop->last)
                            <div class="w-px h-10 bg-slate-200"></div>
                        @endif
                    @endforeach
                </div>

                {{-- CTAs --}}
                <div class="flex flex-wrap gap-4" style="animation: fadeUp 0.8s 0.45s ease both;">
                    <a href="#daftar"
                        class="inline-flex items-center gap-2 px-7 py-4 rounded-2xl bg-brand-600 text-white font-semibold text-base hover:bg-brand-700 shadow-xl shadow-brand-200 hover:shadow-brand-300 hover:-translate-y-1 transition-all duration-200">
                        Daftar Sekarang
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="#tentang"
                        class="inline-flex items-center gap-2 px-7 py-4 rounded-2xl bg-white text-slate-700 font-semibold text-base border border-slate-200 hover:border-brand-200 hover:text-brand-600 hover:bg-brand-50 hover:-translate-y-1 transition-all duration-200 shadow-sm">
                        Pelajari Lebih
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Right: Illustration / Visual --}}
            <div class="hidden lg:flex justify-center items-center relative"
                style="animation: fadeIn 1s 0.3s ease both;">
                {{-- Main card floating --}}
                <div class="relative animate-float">
                    <div
                        class="w-80 h-80 rounded-3xl bg-gradient-to-br from-brand-600 to-brand-400 shadow-2xl shadow-brand-300 flex items-center justify-center relative overflow-hidden">
                        {{-- Circuit pattern inside --}}
                        <div class="absolute inset-0 opacity-10">
                            <svg viewBox="0 0 320 320" class="w-full h-full" fill="none" stroke="white"
                                stroke-width="1">
                                <line x1="0" y1="80" x2="320" y2="80" />
                                <line x1="0" y1="160" x2="320" y2="160" />
                                <line x1="0" y1="240" x2="320" y2="240" />
                                <line x1="80" y1="0" x2="80" y2="320" />
                                <line x1="160" y1="0" x2="160" y2="320" />
                                <line x1="240" y1="0" x2="240" y2="320" />
                                <circle cx="80" cy="80" r="6" fill="white" />
                                <circle cx="160" cy="80" r="6" fill="white" />
                                <circle cx="240" cy="160" r="6" fill="white" />
                                <circle cx="80" cy="240" r="6" fill="white" />
                                <circle cx="160" cy="160" r="16" fill="white" opacity="0.3" />
                                <circle cx="160" cy="160" r="8" fill="white" />
                            </svg>
                        </div>
                        {{-- Scan line animation --}}
                        <div class="absolute left-0 right-0 h-0.5 bg-accent/70 blur-[1px]"
                            style="animation: scan 3s linear infinite;"></div>

                        {{-- Center logo --}}
                        <div class="relative z-10 text-center text-white">
                            <div class="font-display text-7xl font-bold">MCI</div>
                            <div class="text-sm font-light tracking-[0.25em] opacity-80 mt-1">TEKNOLOGI</div>
                        </div>
                    </div>

                    {{-- Floating badges --}}
                    <div
                        class="absolute -top-6 -right-8 bg-white rounded-2xl shadow-xl shadow-slate-200 px-4 py-3 flex items-center gap-2.5 border border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-lg">💻
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">Web Dev</div>
                            <div class="text-[10px] text-slate-400">Divisi Aktif</div>
                        </div>
                    </div>
                    <div
                        class="absolute -bottom-6 -left-8 bg-white rounded-2xl shadow-xl shadow-slate-200 px-4 py-3 flex items-center gap-2.5 border border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center text-lg">🤖</div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">AI & ML</div>
                            <div class="text-[10px] text-slate-400">Divisi Aktif</div>
                        </div>
                    </div>
                    <div
                        class="absolute top-1/2 -right-16 -translate-y-1/2 bg-white rounded-2xl shadow-xl shadow-slate-200 px-4 py-3 flex items-center gap-2.5 border border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center text-lg">🎨</div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">UI/UX</div>
                            <div class="text-[10px] text-slate-400">Divisi Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll cue --}}
    <div
        class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-slate-400 animate-bounce">
        <span class="text-xs font-medium tracking-widest uppercase">Scroll</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</section>
