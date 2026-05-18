{{-- resources/views/landing/_about.blade.php --}}
<section id="tentang" class="py-28 bg-canvas-soft relative overflow-hidden">

    <div class="absolute top-0 right-0 w-72 h-72 bg-brand-100/40 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-20 items-center">

            {{-- Left: Visual blocks --}}
            <div class="relative reveal">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Main big block --}}
                    <div
                        class="col-span-2 rounded-xl bg-brand-600 p-8 text-white relative overflow-hidden min-h-55 flex flex-col justify-between">
                        <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-brand-200/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                        <div class="relative z-10">
                            <div class="text-4xl mb-3 text-white/80"><i class="fa-solid fa-graduation-cap"></i></div>
                            <h3 class="text-xl" style="font-weight:300; letter-spacing:-0.26px;">Didirikan 2018</h3>
                            <p class="text-brand-200 text-sm mt-1" style="font-weight:300;">Berkembang bersama teknologi selama 7 tahun</p>
                        </div>
                        <div class="relative z-10 flex gap-6 mt-4">
                            <div>
                                <div class="text-2xl" style="font-weight:300; font-feature-settings:'tnum'; letter-spacing:-0.42px;">7</div>
                                <div class="text-brand-200 text-xs">Tahun Berdiri</div>
                            </div>
                            <div>
                                <div class="text-2xl" style="font-weight:300; font-feature-settings:'tnum'; letter-spacing:-0.42px;">{{ $jumlahDivisi }}</div>
                                <div class="text-brand-200 text-xs">Divisi</div>
                            </div>
                            <div>
                                <div class="text-2xl" style="font-weight:300; font-feature-settings:'tnum'; letter-spacing:-0.42px;">
                                    {{ $jumlahAlumni > 0 ? $jumlahAlumni . '+' : '—' }}
                                </div>
                                <div class="text-brand-200 text-xs">Alumni</div>
                            </div>
                        </div>
                    </div>

                    {{-- Value cards —— card-feature-light --}}
                    @foreach ([
                        ['fa-solid fa-lightbulb', 'text-amber-500', 'Inovasi',    'Mendorong ide kreatif dan solusi teknologi terdepan'],
                        ['fa-solid fa-handshake', 'text-brand-500', 'Kolaborasi', 'Membangun tim solid lintas jurusan dan angkatan'],
                    ] as [$iconClass, $iconColor, $title, $desc])
                        <div class="reveal reveal-delay-{{ $loop->index + 1 }} rounded-xl bg-white border border-hairline p-6 tech-card shadow-sm">
                            <div class="text-3xl mb-3 {{ $iconColor }}"><i class="{{ $iconClass }}"></i></div>
                            <div class="text-ink text-base mb-1" style="font-weight:300; letter-spacing:-0.22px;">{{ $title }}</div>
                            <div class="text-ink-mute text-sm leading-relaxed" style="font-weight:300;">{{ $desc }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Text --}}
            <div class="space-y-7">
                <div class="reveal">
                    {{-- pill-tag-soft --}}
                    <span
                        class="inline-block text-[10px] tracking-widest text-brand-700 uppercase bg-brand-200 px-3 py-1.5 rounded-full mb-4"
                        style="font-weight:400; letter-spacing:0.1px;">
                        Tentang Kami
                    </span>
                    {{-- display-lg: 32px weight 300 -0.64px --}}
                    <h2 class="font-display text-4xl lg:text-5xl text-ink leading-tight"
                        style="font-weight:300; letter-spacing:-0.64px;">
                        {{ $profil?->tagline
                            ? implode(' ', array_slice(explode(' ', $profil->tagline), 0, -1))
                            : 'Komunitas yang' }}
                        <span class="gradient-text">
                            {{ $profil?->tagline
                                ? implode(' ', array_slice(explode(' ', $profil->tagline), -1))
                                : 'Menginspirasi' }}
                        </span>
                    </h2>
                </div>

                <div class="space-y-4 text-ink-mute leading-relaxed reveal reveal-delay-1" style="font-weight:300;">
                    @if ($profil?->deskripsi)
                        @foreach (explode("\n\n", $profil->deskripsi) as $paragraf)
                            <p>{{ trim($paragraf) }}</p>
                        @endforeach
                    @else
                        <p>
                            <strong class="text-ink-secondary" style="font-weight:400;">UKM MCI (Mahasiswa Creative & Innovation)</strong>
                            adalah unit kegiatan mahasiswa yang berfokus pada pengembangan kompetensi di bidang teknologi
                            informasi, pemrograman, kecerdasan buatan, dan desain digital.
                        </p>
                        <p>
                            Kami percaya bahwa setiap mahasiswa memiliki potensi untuk menjadi innovator. Melalui berbagai
                            program, workshop, dan kompetisi, kami hadir sebagai wadah untuk mengembangkan skill, membangun
                            portofolio, dan memperluas jaringan.
                        </p>
                    @endif
                </div>

                {{-- Feature list --}}
                <div class="space-y-3 reveal reveal-delay-2">
                    @php
                        $defaultKeunggulan = [
                            ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Workshop & Pelatihan rutin setiap bulan'],
                            ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Bimbingan langsung dari senior & alumni'],
                            ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Akses ke berbagai kompetisi nasional'],
                            ['icon' => 'fa-solid fa-circle-check', 'teks' => 'Sertifikat keikutsertaan program'],
                        ];
                        $keunggulan = $profil?->keunggulan ?: $defaultKeunggulan;
                    @endphp
                    @foreach ($keunggulan as $item)
                        <div class="flex items-center gap-3">
                            <i class="{{ $item['icon'] ?? 'fa-solid fa-circle-check' }} text-brand-600 w-4 text-center flex-shrink-0"></i>
                            <span class="text-ink-mute text-sm" style="font-weight:300;">{{ $item['teks'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="reveal reveal-delay-3">
                    {{-- button-primary-pill --}}
                    <a href="#daftar"
                        class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5 transition-all duration-200"
                        style="font-weight:400; font-size:16px; line-height:1.0;">
                        Bergabung dengan Kami
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
