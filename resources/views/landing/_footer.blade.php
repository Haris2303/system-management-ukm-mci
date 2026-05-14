{{-- resources/views/landing/_footer.blade.php --}}
{{-- footer-light: canvas white, ink-mute text, caption typography --}}
<footer class="bg-white relative overflow-hidden border-t border-hairline">
    {{-- Top decoration --}}
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-brand-300 to-transparent"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Main footer --}}
        <div class="py-16 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="flex items-center">
                    <img src="{{ asset('assets/logo/brand.png') }}" alt="UKM MCI" class="h-9 w-auto opacity-80">
                </div>
                <p class="text-ink-mute text-sm leading-relaxed max-w-sm" style="font-weight:300;">
                    Komunitas mahasiswa teknologi yang berdedikasi dalam mengembangkan inovasi digital dan membentuk
                    generasi tech leader masa depan.
                </p>
                {{-- Social --}}
                <div class="flex items-center gap-3">
                    @foreach ([
                        [
                            'Instagram',
                            '#',
                            'M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z',
                        ],
                        ['YouTube', '#', 'M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z'],
                        ['LinkedIn', '#', 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
                    ] as [$name, $url, $path])
                        <a href="{{ $url }}" target="_blank" aria-label="{{ $name }}"
                            class="w-9 h-9 rounded-lg border border-hairline text-ink-mute hover:bg-brand-600 hover:text-white hover:border-brand-600 flex items-center justify-center transition-all duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="{{ $path }}" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Quick links --}}
            <div>
                <div class="text-ink text-sm mb-5" style="font-weight:400;">Navigasi</div>
                <ul class="space-y-3">
                    @foreach ([['#tentang', 'Tentang Kami'], ['#program', 'Program & Divisi'], ['#galeri', 'Galeri Kegiatan'], ['#pengurus', 'Struktur Pengurus'], ['#daftar', 'Daftar Anggota']] as [$href, $label])
                        <li>
                            <a href="{{ $href }}"
                                class="text-ink-mute text-sm hover:text-brand-600 transition-colors"
                                style="font-weight:300;">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <div class="text-ink text-sm mb-5" style="font-weight:400;">Kontak</div>
                <ul class="space-y-4">
                    @foreach ([
                        ['fa-regular fa-envelope',    'mci@kampus.ac.id'],
                        ['fa-solid fa-mobile-screen', '+62 812-3456-7890'],
                        ['fa-solid fa-location-dot',  'Gedung Teknik Lt. 2, Universitas Anda'],
                        ['fa-regular fa-clock',       'Senin–Jumat, 09.00–17.00 WIT'],
                    ] as [$iconClass, $text])
                        <li class="flex items-start gap-2.5">
                            <i class="{{ $iconClass }} text-ink-mute mt-0.5 w-4 text-center shrink-0"></i>
                            <span class="text-ink-mute text-sm leading-relaxed" style="font-weight:300;">{{ $text }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Bottom bar — caption typography --}}
        <div class="py-6 border-t border-hairline flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-ink-mute text-xs" style="font-weight:400; letter-spacing:-0.39px; font-feature-settings:'tnum';">
                © {{ date('Y') }} UKM MCI. Dibuat dengan <i class="fa-solid fa-heart text-ruby mx-0.5"></i> oleh Tim Teknologi MCI.
            </p>
            <div class="flex items-center gap-1">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-ink-mute text-xs" style="font-weight:300;">Sistem berjalan normal</span>
            </div>
        </div>
    </div>
</footer>
