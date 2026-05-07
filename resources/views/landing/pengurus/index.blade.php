@extends('layouts.app')

@section('title', 'UKM MCI — Daftar Pengurus')

@section('content')
    {{-- resources/views/landing/_pengurus.blade.php --}}
    <section id="pengurus" class="py-28 bg-slate-50/60 relative overflow-hidden">
        <div class="absolute inset-0 dot-grid opacity-40"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">

            {{-- Header --}}
            <div class="text-center max-w-xl mx-auto mb-16 reveal">
                <span
                    class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 mb-4">
                    Struktur Pengurus
                </span>
                <h2 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                    Digerakkan oleh
                    <span class="gradient-text"> Orang Terbaik</span>
                </h2>
                <p class="text-slate-500 mt-4 font-light">Kenali tim pengurus yang memimpin UKM MCI periode ini.</p>
            </div>

            @if ($pengurus->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($pengurus as $p)
                        <div
                            class="reveal reveal-delay-{{ min(($loop->index % 4) + 1, 4) }} bg-white rounded-2xl overflow-hidden border border-slate-100 tech-card shadow-sm group text-center">
                            {{-- Foto / Inisial --}}
                            <div class="h-52 relative overflow-hidden bg-gradient-to-br from-brand-100 to-brand-200">
                                @if ($p->foto)
                                    <img src="{{ asset('storage/' . $p->foto) }}" alt="{{ $p->nama }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div
                                            class="w-24 h-24 rounded-full bg-brand-600 flex items-center justify-center shadow-xl shadow-brand-300">
                                            <span
                                                class="font-display text-3xl font-bold text-white">{{ substr($p->nama, 0, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                                {{-- Overlay with social icons --}}
                                <div
                                    class="absolute inset-0 bg-brand-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3">
                                    @if ($p->instagram)
                                        <a href="https://instagram.com/{{ $p->instagram }}" target="_blank"
                                            class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center text-white transition-colors"
                                            title="Instagram">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                            </svg>
                                        </a>
                                    @endif
                                    @if ($p->linkedin)
                                        <a href="https://linkedin.com/in/{{ $p->linkedin }}" target="_blank"
                                            class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center text-white transition-colors"
                                            title="LinkedIn">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="p-5">
                                <h3 class="font-display font-bold text-slate-800 text-base">{{ $p->nama }}</h3>
                                <div class="text-brand-600 text-sm font-semibold mt-1">{{ $p->jabatan }}</div>
                                @if ($p->divisi)
                                    <div
                                        class="inline-block mt-2 text-[10px] font-bold tracking-widest uppercase text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                                        {{ $p->divisi }}
                                    </div>
                                @endif
                                @if ($p->angkatan)
                                    <div class="text-slate-400 text-xs mt-2">Angkatan {{ $p->angkatan }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Placeholder cards --}}
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ([['Ketua Umum', 'Inti', '2022'], ['Wakil Ketua', 'Inti', '2022'], ['Sekretaris', 'Inti', '2023'], ['Bendahara', 'Inti', '2023'], ['Kadiv Web Dev', 'Divisi', '2022'], ['Kadiv AI & ML', 'Divisi', '2023'], ['Kadiv UI/UX', 'Divisi', '2022'], ['Kadiv Security', 'Divisi', '2023']] as [$jabatan, $divisi, $angkatan])
                        <div
                            class="reveal reveal-delay-{{ min(($loop->index % 4) + 1, 4) }} bg-white rounded-2xl overflow-hidden border border-slate-100 tech-card shadow-sm text-center group">
                            <div
                                class="h-52 bg-gradient-to-br from-brand-{{ 100 + ($loop->index % 3) * 100 }} to-brand-{{ 200 + ($loop->index % 3) * 100 }} flex items-center justify-center relative overflow-hidden">
                                <div
                                    class="w-24 h-24 rounded-full bg-brand-600 flex items-center justify-center shadow-xl shadow-brand-300">
                                    <span class="font-display text-3xl font-bold text-white">?</span>
                                </div>
                                <div
                                    class="absolute inset-0 bg-brand-900/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">Data belum tersedia</span>
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-display font-bold text-slate-400 text-base">Nama Pengurus</h3>
                                <div class="text-brand-400 text-sm font-semibold mt-1">{{ $jabatan }}</div>
                                <div
                                    class="inline-block mt-2 text-[10px] font-bold tracking-widest uppercase text-slate-300 bg-slate-100 px-2 py-0.5 rounded-full">
                                    {{ $divisi }}</div>
                                <div class="text-slate-300 text-xs mt-2">Angkatan {{ $angkatan }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="text-center text-slate-400 text-sm mt-8 reveal">
                    * Data pengurus akan diperbarui oleh admin melalui panel administrasi.
                </p>
            @endif
        </div>
    </section>
@endsection
