{{-- resources/views/landing/_berita.blade.php --}}
<section id="berita" class="py-28 bg-white relative overflow-hidden">

    <div
        class="absolute top-0 right-0 w-80 h-80 bg-brand-50 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 w-64 h-64 bg-slate-50 rounded-full translate-y-1/2 -translate-x-1/3 blur-3xl pointer-events-none">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">

        {{-- ── Header ──────────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14 reveal">
            <div>
                <span
                    class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 mb-4">
                    Berita & Kegiatan
                </span>
                <h2 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                    Selalu Update
                    <span class="gradient-text"> Bersama MCI</span>
                </h2>
                <p class="text-slate-500 mt-3 font-light max-w-lg">
                    Ikuti perkembangan terbaru, hasil kegiatan, dan pencapaian membanggakan dari keluarga besar UKM MCI.
                </p>
            </div>
            <a href="{{ route('berita.index') }}"
                class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-brand-200 text-brand-600 font-semibold text-sm hover:bg-brand-600 hover:text-white hover:border-brand-600 transition-all duration-200">
                Lihat Semua Berita
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        @if ($posts->isNotEmpty())

            {{-- ── Featured Post (post pertama ditampilkan besar) ─── --}}
            @php
                $featured = $posts->first();
                $rest = $posts->skip(1);
            @endphp

            <div class="grid lg:grid-cols-5 gap-6 mb-6">

                {{-- Featured Card --}}
                <a href="{{ route('berita.show', $featured->slug) }}"
                    class="reveal lg:col-span-3 group relative rounded-3xl overflow-hidden block bg-slate-900 min-h-[380px] flex flex-col justify-end shadow-xl shadow-slate-200 hover:shadow-2xl hover:shadow-brand-100 transition-all duration-300 hover:-translate-y-1">

                    {{-- Thumbnail --}}
                    @if ($featured->thumbnail)
                        <img src="{{ asset('storage/' . $featured->thumbnail) }}" alt="{{ $featured->judul }}"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-60">
                    @else
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-brand-800 to-brand-600 group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 opacity-10 dot-grid"></div>
                        </div>
                    @endif

                    {{-- Gradient overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

                    {{-- Content --}}
                    <div class="relative z-10 p-8">
                        <div class="flex items-center gap-3 mb-4">
                            {{-- Kategori badge --}}
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                            @switch($featured->kategori)
                                @case('Prestasi')  bg-amber-400/20 text-amber-300 border border-amber-400/30 @break
                                @case('Kegiatan')  bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 @break
                                @case('Pengumuman') bg-rose-400/20 text-rose-300 border border-rose-400/30 @break
                                @default           bg-brand-400/20 text-brand-300 border border-brand-400/30
                            @endswitch">
                                {!! $featured->getKategoriEmoji() !!} {{ $featured->kategori }}
                            </span>
                            <span class="text-white/50 text-xs">{{ $featured->readTime() }}</span>
                        </div>

                        <h3
                            class="font-display text-2xl font-bold text-white leading-tight mb-3 group-hover:text-brand-200 transition-colors line-clamp-3">
                            {{ $featured->judul }}
                        </h3>

                        @if ($featured->ringkasan)
                            <p class="text-white/60 text-sm leading-relaxed line-clamp-2 mb-4">
                                {{ $featured->ringkasan }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-white/50 text-xs">
                                <span>{{ $featured->author->name ?? 'Admin MCI' }}</span>
                                <span>·</span>
                                <span>{{ $featured->published_at->translatedFormat('d M Y') }}</span>
                            </div>
                            <span
                                class="w-9 h-9 rounded-xl bg-white/10 group-hover:bg-brand-500 flex items-center justify-center transition-colors duration-200">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>

                {{-- Side posts (2 cards vertikal) --}}
                <div class="lg:col-span-2 flex flex-col gap-5">
                    @foreach ($rest->take(2) as $post)
                        <a href="{{ route('berita.show', $post->slug) }}"
                            class="reveal reveal-delay-{{ $loop->index + 1 }} group flex gap-4 p-5 rounded-2xl bg-white border border-slate-100 hover:border-brand-200 hover:shadow-lg hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-0.5 flex-1">

                            {{-- Thumbnail kecil --}}
                            <div class="w-24 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-brand-100">
                                @if ($post->thumbnail)
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->judul }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-brand-200 to-brand-400 flex items-center justify-center text-2xl">
                                        {!! $post->getKategoriEmoji() !!}
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col justify-between flex-1 min-w-0">
                                <div>
                                    {{-- Badge --}}
                                    <span
                                        class="inline-block text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full mb-2
                                @switch($post->kategori)
                                    @case('Prestasi')  bg-amber-50 text-amber-600 @break
                                    @case('Kegiatan')  bg-emerald-50 text-emerald-600 @break
                                    @case('Pengumuman') bg-rose-50 text-rose-600 @break
                                    @default           bg-brand-50 text-brand-600
                                @endswitch">
                                        {{ $post->kategori }}
                                    </span>
                                    <h3
                                        class="font-display font-bold text-slate-800 text-sm leading-snug group-hover:text-brand-600 transition-colors line-clamp-2">
                                        {{ $post->judul }}
                                    </h3>
                                </div>
                                <div class="flex items-center gap-2 text-slate-400 text-xs mt-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $post->published_at->translatedFormat('d M Y') }}
                                    <span>·</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ number_format($post->views) }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ── Grid posts lainnya (4 kartu bawah) ──────────────── --}}
            @if ($rest->count() > 2)
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($rest->skip(2)->take(4) as $post)
                        <a href="{{ route('berita.show', $post->slug) }}"
                            class="reveal reveal-delay-{{ min($loop->index + 1, 4) }} group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:border-brand-200 hover:shadow-lg hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-1 flex flex-col">

                            {{-- Thumbnail --}}
                            <div class="h-44 overflow-hidden bg-brand-50 relative">
                                @if ($post->thumbnail)
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->judul }}"
                                        class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br
                        @switch($post->kategori)
                            @case('Prestasi')  from-amber-100 to-amber-200 @break
                            @case('Kegiatan')  from-emerald-100 to-emerald-200 @break
                            @case('Pengumuman') from-rose-100 to-rose-200 @break
                            @default           from-brand-100 to-brand-200
                        @endswitch
                        flex items-center justify-center text-4xl">
                                        {!! $post->getKategoriEmoji() !!}
                                    </div>
                                @endif
                                {{-- Kategori overlay --}}
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="inline-block text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg backdrop-blur-sm
                            @switch($post->kategori)
                                @case('Prestasi')  bg-amber-400/90 text-white @break
                                @case('Kegiatan')  bg-emerald-500/90 text-white @break
                                @case('Pengumuman') bg-rose-500/90 text-white @break
                                @default           bg-brand-600/90 text-white
                            @endswitch">
                                        {{ $post->kategori }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5 flex flex-col flex-1">
                                <h3
                                    class="font-display font-bold text-slate-800 text-base leading-snug group-hover:text-brand-600 transition-colors line-clamp-2 mb-2 flex-1">
                                    {{ $post->judul }}
                                </h3>
                                @if ($post->ringkasan)
                                    <p class="text-slate-400 text-xs leading-relaxed line-clamp-2 mb-4">
                                        {{ $post->ringkasan }}
                                    </p>
                                @endif
                                <div
                                    class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-100 mt-auto">
                                    <span>{{ $post->published_at->translatedFormat('d M Y') }}</span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $post->readTime() }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        @else
            {{-- ── Placeholder saat belum ada berita ──────────────── --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ([
                    ['fa-regular fa-newspaper', 'Berita',   'Selamat Datang di UKM MCI',    'Berita pertama kami akan segera hadir. Pantau terus halaman ini!'],
                    ['fa-solid fa-trophy',      'Prestasi', 'Juara 1 Hackathon Nasional',   'Tim MCI berhasil meraih podium pertama dalam kompetisi bergengsi.'],
                    ['fa-regular fa-calendar',  'Kegiatan', 'Workshop Web Development',     'Kegiatan workshop intensif untuk meningkatkan skill anggota baru.'],
                ] as [$iconClass, $kat, $judul, $desc])
                    <div
                        class="reveal reveal-delay-{{ $loop->index + 1 }} rounded-2xl border-2 border-dashed border-slate-200 overflow-hidden opacity-60">
                        <div
                            class="h-40 bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center text-5xl text-slate-400">
                            <i class="{{ $iconClass }}"></i></div>
                        <div class="p-5">
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $kat }}</span>
                            <h3 class="font-display font-bold text-slate-500 text-base mt-1 mb-2">{{ $judul }}
                            </h3>
                            <p class="text-slate-400 text-sm">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-center text-slate-400 text-sm mt-8 reveal">* Berita akan muncul setelah admin
                mempublikasikan konten.</p>
        @endif

        {{-- ── CTA ke halaman berita ────────────────────────────── --}}
        @if ($posts->count() >= 4)
            <div class="text-center mt-12 reveal">
                <a href="{{ route('berita.index') }}"
                    class="inline-flex items-center gap-2.5 px-8 py-4 rounded-2xl bg-slate-50 border-2 border-slate-200 text-slate-700 font-semibold hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700 transition-all duration-200 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    Baca Semua Berita & Kegiatan
                </a>
            </div>
        @endif

    </div>
</section>
