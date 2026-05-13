{{-- resources/views/landing/_gallery.blade.php --}}
<section id="galeri" class="py-28 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center max-w-xl mx-auto mb-16 reveal">
            <span
                class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 mb-4">
                Galeri Kegiatan
            </span>
            <h2 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                Momen Bersama
                <span class="gradient-text"> MCI</span>
            </h2>
            <p class="text-slate-500 mt-4 font-light">Dokumentasi kegiatan, lomba, dan perjalanan kami.</p>
        </div>

        @if ($galleries->isNotEmpty())
            {{-- Masonry-style grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                @foreach ($galleries as $item)
                    <div class="gallery-item reveal reveal-delay-{{ min(($loop->index % 4) + 1, 4) }} rounded-2xl overflow-hidden
                        {{ $loop->first ? 'md:col-span-2 md:row-span-2' : '' }}
                        {{ $loop->index === 3 ? 'md:col-span-2' : '' }} relative group cursor-pointer"
                        onclick="openLightbox('{{ asset('storage/' . $item->foto) }}', '{{ $item->judul }}')">
                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->judul }}"
                            class="w-full h-full object-cover {{ $loop->first ? 'min-h-[320px]' : 'min-h-[180px]' }}">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-brand-900/70 via-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <div>
                                <div class="text-[10px] font-bold text-brand-300 uppercase tracking-widest">
                                    {{ $item->kategori }}</div>
                                <div class="text-white font-semibold text-sm mt-0.5">{{ $item->judul }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Placeholder grid saat belum ada foto --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                @php
                    $placeholders = [
                        ['Hackathon 2024',   'Lomba',       true,  'from-brand-400 to-brand-700',    'fa-solid fa-trophy'],
                        ['Workshop Web Dev', 'Workshop',    false, 'from-violet-400 to-violet-700',  'fa-solid fa-laptop-code'],
                        ['Ospek Divisi',     'Orientasi',   false, 'from-emerald-400 to-emerald-700','fa-solid fa-graduation-cap'],
                        ['Demo Day',         'Showcase',    false, 'from-amber-400 to-orange-600',   'fa-solid fa-rocket'],
                        ['Team Building',    'Kebersamaan', false, 'from-pink-400 to-rose-600',      'fa-solid fa-handshake'],
                        ['CTF Competition',  'Lomba',       false, 'from-cyan-400 to-brand-600',     'fa-solid fa-lock'],
                        ['UI/UX Bootcamp',   'Workshop',    false, 'from-fuchsia-400 to-purple-700', 'fa-solid fa-palette'],
                        ['Tech Talk',        'Seminar',     false, 'from-teal-400 to-teal-700',      'fa-solid fa-microphone'],
                        ['Project Demo',     'Showcase',    false, 'from-indigo-400 to-brand-700',   'fa-solid fa-mobile-screen'],
                    ];
                @endphp

                @foreach ($placeholders as [$judul, $kategori, $big, $grad, $iconClass])
                    <div
                        class="gallery-item reveal reveal-delay-{{ min(($loop->index % 4) + 1, 4) }} rounded-2xl overflow-hidden
                        {{ $big ? 'md:col-span-2 md:row-span-2' : '' }}
                        relative group cursor-pointer">
                        <div
                            class="w-full bg-gradient-to-br {{ $grad }} flex items-center justify-center
                            {{ $big ? 'min-h-[320px]' : 'min-h-[180px]' }} relative">
                            <div class="text-center text-white">
                                <div class="text-5xl mb-3"><i class="{{ $iconClass }}"></i></div>
                                <div class="font-display font-bold text-lg">{{ $judul }}</div>
                                <div class="text-xs opacity-70 mt-1">{{ $kategori }}</div>
                            </div>
                            <div
                                class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Lightbox --}}
    <div id="lightbox"
        class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-sm hidden items-center justify-center p-4"
        onclick="closeLightbox()">
        <div class="relative max-w-5xl w-full" onclick="event.stopPropagation()">
            <button onclick="closeLightbox()"
                class="absolute -top-12 right-0 text-white/70 hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="lightbox-img" src="" alt="" class="w-full max-h-[80vh] object-contain rounded-2xl">
            <p id="lightbox-caption" class="text-white/70 text-center mt-4 text-sm"></p>
        </div>
    </div>

    @push('scripts')
        <script>
            function openLightbox(src, caption) {
                document.getElementById('lightbox-img').src = src;
                document.getElementById('lightbox-caption').textContent = caption;
                const lb = document.getElementById('lightbox');
                lb.classList.remove('hidden');
                lb.classList.add('flex');
            }

            function closeLightbox() {
                const lb = document.getElementById('lightbox');
                lb.classList.add('hidden');
                lb.classList.remove('flex');
            }
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeLightbox();
            });
        </script>
    @endpush
</section>
