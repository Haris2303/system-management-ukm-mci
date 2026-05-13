<div @if($election->status === 'aktif') wire:poll.5s @endif class="flex flex-col min-h-screen">

    {{-- ── HEADER ─────────────────────────────────────────────────── --}}
    <header class="relative overflow-hidden mesh-bg dot-grid noise">

        {{-- Decorative blobs --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-100/60 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 -left-32 w-80 h-80 rounded-full pointer-events-none" style="background:rgba(15,244,198,0.08);filter:blur(64px);"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-10 lg:py-14">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">

                {{-- Kiri: Label + Judul + Posisi --}}
                <div class="space-y-3">
                    <span class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100">
                        Rekap Suara · E-Voting UKM MCI
                    </span>
                    <h1 class="font-display text-3xl lg:text-5xl font-bold text-slate-900 leading-tight">
                        {{ $election->judul }}
                    </h1>
                    <p class="text-slate-500 text-sm lg:text-base">
                        Posisi yang dipilih:
                        <span class="font-semibold text-brand-600">{{ $election->posisi }}</span>
                    </p>
                </div>

                {{-- Kanan: Status + Total --}}
                <div class="flex flex-col items-start sm:items-end gap-3 shrink-0">
                    @if($election->status === 'aktif')
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="ping-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            Live — memperbarui setiap 5 detik
                        </div>
                    @elseif($election->status === 'selesai')
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-brand-50 border border-brand-100 text-brand-700">
                            Pemilihan Selesai
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 border border-slate-200 text-slate-500">
                            Belum Dimulai
                        </div>
                    @endif

                    <div class="text-right">
                        <p class="font-display text-4xl lg:text-5xl font-bold gradient-text">
                            {{ number_format($totalSuara) }}
                        </p>
                        <p class="text-slate-400 text-xs mt-0.5">total suara masuk</p>
                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- ── WINNER SECTION (hanya saat selesai & ada pemenang jelas) ── --}}
    @if($election->status === 'selesai' && $hasUniqueLeader)
        @php $winner = $candidates->first(); @endphp

        <section class="relative overflow-hidden py-16 lg:py-24"
                 style="background: linear-gradient(135deg, #0c1445 0%, #141f57 30%, #18338f 60%, #0f2d6b 80%, #070d2e 100%);">

            {{-- Bintang dekoratif --}}
            @foreach([[8,12,0.9,'2.1s'],[92,8,0.7,'0s'],[15,85,0.8,'1.4s'],[85,75,0.6,'0.7s'],[50,5,1,'1s'],[30,60,0.5,'1.8s'],[70,40,0.7,'0.3s'],[20,30,0.6,'2.5s'],[80,55,0.9,'1.1s']] as [$x,$y,$op,$delay])
                <div class="absolute pointer-events-none"
                     style="left:{{$x}}%;top:{{$y}}%;opacity:{{$op}};animation:star-float {{ 3 + ($x % 3) }}s ease-in-out {{ $delay }} infinite;">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M7 0l1.5 5H14l-4.5 3.3 1.7 5.2L7 10.4 1.8 13.5l1.7-5.2L0 5h5.5z" fill="white"/>
                    </svg>
                </div>
            @endforeach

            {{-- Blob dekoratif --}}
            <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full pointer-events-none"
                 style="background:rgba(15,244,198,0.07);filter:blur(60px);"></div>
            <div class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full pointer-events-none"
                 style="background:rgba(26,79,245,0.15);filter:blur(60px);"></div>

            <div class="relative z-10 max-w-xl mx-auto px-6 text-center">

                {{-- Trofi --}}
                <div class="trophy-pop text-6xl lg:text-7xl mb-4 select-none">🏆</div>

                {{-- Label pemenang --}}
                <div class="winner-slide-up inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-bold tracking-widest uppercase mb-6"
                     style="background:rgba(15,244,198,0.15);border:1px solid rgba(15,244,198,0.4);color:#0ff4c6;animation-delay:0.1s;">
                    Pemenang Pemilihan
                </div>

                {{-- Foto pemenang --}}
                <div class="winner-slide-up flex justify-center mb-6" style="animation-delay:0.2s;">
                    <div class="relative">
                        {{-- Ring glow luar --}}
                        <div class="absolute inset-0 rounded-full winner-photo-ring" style="margin:-6px;border-radius:50%;"></div>

                        @if($winner['foto'])
                            <img src="{{ $winner['foto'] }}"
                                 alt="{{ $winner['nama'] }}"
                                 class="w-44 h-44 lg:w-56 lg:h-56 rounded-full object-cover object-top relative z-10"
                                 style="border:4px solid rgba(255,255,255,0.9);">
                        @else
                            <div class="w-44 h-44 lg:w-56 lg:h-56 rounded-full flex items-center justify-center relative z-10"
                                 style="background:linear-gradient(135deg,#1a4ff5,#3671ff);border:4px solid rgba(255,255,255,0.9);">
                                <span class="font-display font-bold text-white" style="font-size:5rem;line-height:1;">
                                    {{ mb_strtoupper(mb_substr($winner['nama'], 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Nama pemenang --}}
                <div class="winner-slide-up space-y-1 mb-8" style="animation-delay:0.3s;">
                    <p class="winner-name-shimmer font-display font-bold leading-tight" style="font-size:clamp(1.8rem,5vw,3rem);">
                        {{ $winner['nama'] }}
                    </p>
                    <p class="text-sm" style="color:rgba(255,255,255,0.5);">
                        Kandidat Nomor {{ $winner['urut'] }}
                    </p>
                </div>

                {{-- Statistik kemenangan --}}
                <div class="winner-slide-up flex items-center justify-center gap-0 mb-2" style="animation-delay:0.4s;">
                    <div class="px-8 py-4 text-center" style="border-right:1px solid rgba(255,255,255,0.15);">
                        <p class="font-display font-bold text-white" style="font-size:2.5rem;line-height:1;">
                            {{ $winner['persentase'] }}%
                        </p>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.5);">persentase suara</p>
                    </div>
                    <div class="px-8 py-4 text-center">
                        <p class="font-display font-bold text-white" style="font-size:2.5rem;line-height:1;">
                            {{ number_format($winner['jumlah_suara']) }}
                        </p>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.5);">suara diperoleh</p>
                    </div>
                </div>

                {{-- Total suara --}}
                <p class="text-xs" style="color:rgba(255,255,255,0.35);">
                    dari total {{ number_format($totalSuara) }} suara yang masuk
                </p>

            </div>
        </section>
    @endif

    {{-- ── KANDIDAT GRID ───────────────────────────────────────────── --}}
    <section class="flex-1 py-10 lg:py-14 bg-slate-50/80 relative">
        <div class="absolute inset-0 dot-grid opacity-40"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6">

            @if($election->status === 'selesai' && !$candidates->isEmpty())
                <p class="text-center text-xs font-bold tracking-widest text-brand-500 uppercase mb-8">
                    Hasil Selengkapnya
                </p>
            @endif

            @if($candidates->isEmpty())
                <div class="flex flex-col items-center justify-center py-32 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-slate-400 text-lg">Belum ada kandidat terdaftar.</p>
                </div>
            @else
                @php
                    $gridClass = match(true) {
                        $candidates->count() === 1 => 'max-w-sm mx-auto',
                        $candidates->count() === 2 => 'grid-cols-1 sm:grid-cols-2 max-w-3xl mx-auto',
                        default                    => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
                    };
                @endphp

                <div class="grid gap-6 {{ $gridClass }}">
                    @foreach($candidates as $index => $candidate)
                        @php $isLeading = $index === 0 && $hasUniqueLeader; @endphp

                        <div class="tech-card bg-white rounded-2xl overflow-hidden border flex flex-col shadow-sm group
                            {{ $isLeading ? 'border-brand-200' : 'border-slate-100' }}"
                             style="{{ $isLeading ? 'box-shadow:0 4px 32px rgba(26,79,245,0.12);' : '' }}">

                            {{-- Foto kandidat --}}
                            <div class="relative w-full overflow-hidden gallery-item" style="aspect-ratio:1/1;">

                                @if($candidate['foto'])
                                    <img src="{{ $candidate['foto'] }}"
                                         alt="{{ $candidate['nama'] }}"
                                         class="w-full h-full object-cover object-top">
                                @else
                                    {{-- Placeholder --}}
                                    <div class="w-full h-full flex items-center justify-center"
                                         style="background: linear-gradient(135deg, #daeaff 0%, #eef5ff 100%);">
                                        <div class="w-24 h-24 rounded-full bg-brand-600 flex items-center justify-center shadow-xl shadow-brand-200">
                                            <span class="text-white font-display font-bold text-4xl">
                                                {{ mb_strtoupper(mb_substr($candidate['nama'], 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Nomor urut badge --}}
                                <div class="absolute top-3 left-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow-lg
                                        {{ $isLeading ? 'bg-brand-600 text-white' : 'bg-white text-slate-600' }}"
                                         style="{{ $isLeading ? 'box-shadow:0 4px 12px rgba(26,79,245,0.3);' : '' }}">
                                        {{ $candidate['urut'] }}
                                    </div>
                                </div>

                                {{-- Leading badge --}}
                                @if($isLeading)
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-brand-600 text-white shadow-lg"
                                              style="box-shadow:0 4px 12px rgba(26,79,245,0.35);">
                                            Terdepan
                                        </span>
                                    </div>
                                @endif

                                {{-- Gradient overlay bawah --}}
                                <div class="absolute inset-x-0 bottom-0 h-2/5"
                                     style="background:linear-gradient(to top, rgba(255,255,255,1), transparent);"></div>
                            </div>

                            {{-- Info & stats --}}
                            <div class="p-5 flex flex-col gap-4 flex-1">

                                {{-- Nama --}}
                                <div>
                                    <p class="font-display font-bold text-xl text-slate-900 group-hover:text-brand-600 transition-colors leading-tight">
                                        {{ $candidate['nama'] }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5">Kandidat Nomor {{ $candidate['urut'] }}</p>
                                </div>

                                {{-- Persentase + suara --}}
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="font-display font-bold text-5xl leading-none {{ $isLeading ? 'gradient-text' : 'text-slate-900' }}">
                                            {{ $candidate['persentase'] }}%
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">persentase suara</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold {{ $isLeading ? 'text-brand-600' : 'text-slate-900' }}">
                                            {{ number_format($candidate['jumlah_suara']) }}
                                        </p>
                                        <p class="text-xs text-slate-400">suara</p>
                                    </div>
                                </div>

                                {{-- Progress bar --}}
                                <div class="w-full bg-slate-100 rounded-full overflow-hidden" style="height:8px;">
                                    <div class="h-full rounded-full"
                                         style="
                                             width:{{ $candidate['persentase'] }}%;
                                             transition:width .9s cubic-bezier(.4,0,.2,1);
                                             background:{{ $isLeading
                                                 ? 'linear-gradient(90deg,#1340e1,#3671ff,#0ff4c6)'
                                                 : 'linear-gradient(90deg,#bdd8ff,#5c98ff)' }};
                                             {{ $isLeading ? 'box-shadow:0 0 10px rgba(26,79,245,0.3);' : '' }}
                                         ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($totalSuara === 0)
                    <p class="text-center text-slate-400 text-sm mt-8">Belum ada suara yang masuk.</p>
                @endif
            @endif
        </div>
    </section>

    {{-- ── FOOTER ──────────────────────────────────────────────────── --}}
    <footer class="py-6 text-center bg-white border-t border-slate-100">
        <div class="flex items-center justify-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-linear-to-br from-brand-600 to-brand-400 flex items-center justify-center shadow-sm shadow-brand-200">
                <span class="text-white font-display font-bold text-xs">M</span>
            </div>
            <p class="text-slate-400 text-xs">
                UKM MCI · Sistem E-Voting
                @if($election->status === 'aktif')
                    · <span class="text-emerald-500">Memperbarui otomatis setiap 5 detik</span>
                @endif
            </p>
        </div>
    </footer>

    <style>
        .gallery-item img { transition: transform 0.5s ease; }
        .gallery-item:hover img { transform: scale(1.05); }
    </style>

</div>
