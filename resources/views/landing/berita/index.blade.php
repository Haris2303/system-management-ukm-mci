{{-- resources/views/berita/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Berita & Kegiatan — UKM MCI')

@section('content')

    {{-- ── HERO HEADER ──────────────────────────────────────────── --}}
    <section class="relative pt-32 pb-20 bg-white overflow-hidden dot-grid">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-100/60 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-sm font-semibold mb-5">
                <i class="fa-regular fa-newspaper"></i> Pusat Informasi UKM MCI
            </div>
            <h1 class="font-display text-4xl lg:text-6xl font-bold text-slate-900 leading-tight mb-4">
                Berita <span class="gradient-text">&amp; Kegiatan</span>
            </h1>
            <p class="text-slate-500 text-lg font-light max-w-xl mx-auto">
                Ikuti perkembangan terbaru, dokumentasi kegiatan, dan pencapaian membanggakan dari UKM MCI.
            </p>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('berita.index') }}" class="max-w-lg mx-auto mt-8">
                <div
                    class="flex items-center gap-3 bg-white border-2 border-slate-200 rounded-2xl px-5 py-3.5 focus-within:border-brand-400 focus-within:shadow-lg focus-within:shadow-brand-50 transition-all">
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="q" value="{{ $search }}"
                        placeholder="Cari berita, kegiatan, atau prestasi…"
                        class="flex-1 bg-transparent text-slate-700 placeholder:text-slate-400 text-sm outline-none font-body">
                    @if ($kategori && $kategori !== 'semua')
                        <input type="hidden" name="kategori" value="{{ $kategori }}">
                    @endif
                    @if ($tag)
                        <input type="hidden" name="tag" value="{{ $tag }}">
                    @endif
                    <button type="submit"
                        class="px-4 py-1.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition-colors">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- ── FILTER KATEGORI ─────────────────────────────────────── --}}
    <div class="sticky top-16 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center gap-2 overflow-x-auto py-3 scrollbar-none">
                @foreach ($kategoris as $kat)
                    @php
                        $isActive = $kategori === $kat || ($kat === 'semua' && !$kategori);
                        $iconClass = match ($kat) {
                            'Berita' => 'fa-regular fa-newspaper',
                            'Kegiatan' => 'fa-regular fa-calendar',
                            'Prestasi' => 'fa-solid fa-trophy',
                            'Pengumuman' => 'fa-solid fa-bullhorn',
                            default => 'fa-solid fa-folder-open',
                        };
                    @endphp
                    <a href="{{ route('berita.index', array_filter(['kategori' => $kat === 'semua' ? null : $kat, 'q' => $search])) }}"
                        class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
               {{ $isActive ? 'bg-brand-600 text-white shadow-md shadow-brand-200' : 'text-slate-600 hover:bg-brand-50 hover:text-brand-600' }}">
                        <i class="{{ $iconClass }}"></i>
                        <span>{{ ucfirst($kat) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── KONTEN UTAMA ─────────────────────────────────────────── --}}
    <section class="py-14 bg-slate-50/60">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            {{-- Featured post (hanya tampil di halaman pertama tanpa filter/search) --}}
            @if ($featured && !$search && !$tag && (!$kategori || $kategori === 'semua') && $posts->currentPage() === 1)
                <div class="mb-10">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-1 h-5 rounded-full bg-brand-600"></div>
                        <span class="text-sm font-bold text-slate-700">Berita Utama</span>
                    </div>
                    <a href="{{ route('berita.show', $featured->slug) }}"
                        class="group grid lg:grid-cols-2 gap-0 rounded-3xl overflow-hidden bg-white border border-slate-100 hover:shadow-2xl hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-1">
                        <div class="h-64 lg:h-auto overflow-hidden bg-brand-100 relative">
                            @if ($featured->thumbnail)
                                <img src="{{ asset('storage/' . $featured->thumbnail) }}" alt="{{ $featured->judul }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div
                                    class="w-full h-full bg-gradient-to-br from-brand-400 to-brand-700 flex items-center justify-center text-7xl">
                                    {!! $featured->getKategoriEmoji() !!}
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-amber-400 text-white text-xs font-bold shadow">
                                    <i class="fa-solid fa-star fa-xs"></i> Featured</span>
                            </div>
                        </div>
                        <div class="p-8 lg:p-10 flex flex-col justify-center gap-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-brand-50 text-brand-600">
                                    {!! $featured->getKategoriEmoji() !!} {{ $featured->kategori }}
                                </span>
                                <span class="text-slate-400 text-xs">{{ $featured->readTime() }}</span>
                            </div>
                            <h2
                                class="font-display text-2xl lg:text-3xl font-bold text-slate-900 group-hover:text-brand-600 transition-colors leading-tight">
                                {{ $featured->judul }}
                            </h2>
                            @if ($featured->ringkasan)
                                <p class="text-slate-500 leading-relaxed line-clamp-3">{{ $featured->ringkasan }}</p>
                            @endif
                            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2 text-sm text-slate-500">
                                    <div
                                        class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center font-bold text-brand-600 text-xs">
                                        {{ substr($featured->author->name ?? 'A', 0, 1) }}
                                    </div>
                                    <span>{{ $featured->author->name ?? 'Admin MCI' }}</span>
                                    <span class="text-slate-300">·</span>
                                    <span>{{ $featured->published_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1.5 text-brand-600 font-semibold text-sm group-hover:gap-2.5 transition-all">
                                    Baca
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- Result info --}}
            @if ($search || ($kategori && $kategori !== 'semua') || $tag)
                <div class="flex items-center gap-3 mb-6">
                    <p class="text-slate-600 text-sm">
                        <span class="font-semibold">{{ $posts->total() }}</span> hasil
                        @if ($search)
                            untuk "<span class="font-semibold text-brand-600">{{ $search }}</span>"
                        @endif
                        @if ($kategori && $kategori !== 'semua')
                            dalam kategori <span class="font-semibold text-brand-600">{{ $kategori }}</span>
                        @endif
                        @if ($tag)
                            dengan tag <span class="font-semibold text-brand-600">#{{ $tag }}</span>
                        @endif
                    </p>
                    <a href="{{ route('berita.index') }}"
                        class="text-sm text-slate-400 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-xmark mr-0.5"></i> Reset
                    </a>
                </div>
            @endif

            {{-- Grid berita --}}
            @if ($posts->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <a href="{{ route('berita.show', $post->slug) }}"
                            class="group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:border-brand-200 hover:shadow-xl hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-1 flex flex-col">

                            {{-- Thumbnail --}}
                            <div class="h-48 overflow-hidden relative bg-brand-50 flex-shrink-0">
                                @if ($post->thumbnail)
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->judul }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center text-5xl
                        @switch($post->kategori)
                            @case('Prestasi')  bg-gradient-to-br from-amber-100 to-amber-200 @break
                            @case('Kegiatan')  bg-gradient-to-br from-emerald-100 to-emerald-200 @break
                            @case('Pengumuman') bg-gradient-to-br from-rose-100 to-rose-200 @break
                            @default           bg-gradient-to-br from-brand-100 to-brand-200
                        @endswitch">
                                        {!! $post->getKategoriEmoji() !!}
                                    </div>
                                @endif
                                {{-- Badge --}}
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                            @switch($post->kategori)
                                @case('Prestasi')  bg-amber-400 text-white @break
                                @case('Kegiatan')  bg-emerald-500 text-white @break
                                @case('Pengumuman') bg-rose-500 text-white @break
                                @default           bg-brand-600 text-white
                            @endswitch">
                                        {{ $post->kategori }}
                                    </span>
                                </div>
                                @if ($post->is_featured)
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-amber-400 text-white"><i
                                                class="fa-solid fa-star fa-xs"></i></span>
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-5 flex flex-col flex-1">
                                <h3
                                    class="font-display font-bold text-slate-800 text-base leading-snug group-hover:text-brand-600 transition-colors line-clamp-2 mb-2">
                                    {{ $post->judul }}
                                </h3>
                                @if ($post->ringkasan)
                                    <p class="text-slate-400 text-sm leading-relaxed line-clamp-2 mb-4 flex-1">
                                        {{ $post->ringkasan }}
                                    </p>
                                @else
                                    <div class="flex-1"></div>
                                @endif

                                {{-- Footer --}}
                                <div
                                    class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-100 mt-auto">
                                    <div class="flex items-center gap-1.5">
                                        <div
                                            class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center font-bold text-brand-600 text-[9px]">
                                            {{ substr($post->author->name ?? 'A', 0, 1) }}
                                        </div>
                                        <span>{{ Str::words($post->author->name ?? 'Admin', 1, '') }}</span>
                                        <span>·</span>
                                        <span>{{ $post->published_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ number_format($post->views) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($posts->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $posts->links('vendor.pagination.simple-tailwind') }}
                    </div>
                @endif
            @else
                <div class="text-center py-24">
                    <div class="text-6xl mb-4 text-slate-300"><i class="fa-regular fa-envelope-open"></i></div>
                    <h3 class="font-display text-xl font-bold text-slate-700 mb-2">Tidak Ada Berita Ditemukan</h3>
                    <p class="text-slate-400 mb-6">Coba kata kunci lain atau hapus filter yang aktif.</p>
                    <a href="{{ route('berita.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold text-sm hover:bg-brand-700 transition-colors">
                        Lihat Semua Berita
                    </a>
                </div>
            @endif
        </div>
    </section>

@endsection
