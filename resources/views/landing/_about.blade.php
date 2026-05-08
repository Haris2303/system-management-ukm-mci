{{-- resources/views/landing/_about.blade.php --}}
<section id="tentang" class="py-28 bg-white relative overflow-hidden">

    <div
        class="absolute top-0 right-0 w-72 h-72 bg-brand-50 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl pointer-events-none">
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-20 items-center">

            {{-- Left: Visual blocks --}}
            <div class="relative reveal">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Main big block --}}
                    <div
                        class="col-span-2 rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-white relative overflow-hidden min-h-[220px] flex flex-col justify-between">
                        <div
                            class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-32 h-32 bg-accent/10 rounded-full translate-y-1/2 -translate-x-1/2">
                        </div>
                        <div class="relative z-10">
                            <div class="text-4xl mb-3">🎓</div>
                            <h3 class="font-display text-xl font-bold">Didirikan 2018</h3>
                            <p class="text-brand-200 text-sm mt-1 font-light">Berkembang bersama teknologi selama 7 tahun</p>
                        </div>
                        <div class="relative z-10 flex gap-6 mt-4">
                            <div>
                                <div class="font-display font-bold text-2xl">7</div>
                                <div class="text-brand-200 text-xs">Tahun Berdiri</div>
                            </div>
                            <div>
                                <div class="font-display font-bold text-2xl">{{ $jumlahDivisi }}</div>
                                <div class="text-brand-200 text-xs">Divisi</div>
                            </div>
                            <div>
                                <div class="font-display font-bold text-2xl">
                                    {{ $jumlahAlumni > 0 ? $jumlahAlumni . '+' : '—' }}
                                </div>
                                <div class="text-brand-200 text-xs">Alumni</div>
                            </div>
                        </div>
                    </div>

                    {{-- Value cards --}}
                    @foreach ([['💡', 'Inovasi', 'Mendorong ide kreatif dan solusi teknologi terdepan'], ['🤝', 'Kolaborasi', 'Membangun tim solid lintas jurusan dan angkatan']] as [$icon, $title, $desc])
                        <div
                            class="reveal reveal-delay-{{ $loop->index + 1 }} rounded-2xl bg-slate-50 border border-slate-100 p-6 tech-card">
                            <div class="text-3xl mb-3">{{ $icon }}</div>
                            <div class="font-display font-bold text-slate-800 text-base">{{ $title }}</div>
                            <div class="text-slate-500 text-sm mt-1 leading-relaxed">{{ $desc }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Text --}}
            <div class="space-y-7">
                <div class="reveal">
                    <span
                        class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 mb-4">
                        Tentang Kami
                    </span>
                    <h2 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
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

                <div class="space-y-4 text-slate-500 leading-relaxed reveal reveal-delay-1">
                    @if ($profil?->deskripsi)
                        @foreach (explode("\n\n", $profil->deskripsi) as $paragraf)
                            <p>{{ trim($paragraf) }}</p>
                        @endforeach
                    @else
                        <p>
                            <strong class="text-slate-700 font-semibold">UKM MCI (Mahasiswa Creative & Innovation)</strong>
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
                            ['icon' => '✅', 'teks' => 'Workshop & Pelatihan rutin setiap bulan'],
                            ['icon' => '✅', 'teks' => 'Bimbingan langsung dari senior & alumni'],
                            ['icon' => '✅', 'teks' => 'Akses ke berbagai kompetisi nasional'],
                            ['icon' => '✅', 'teks' => 'Sertifikat keikutsertaan program'],
                        ];
                        $keunggulan = $profil?->keunggulan ?: $defaultKeunggulan;
                    @endphp
                    @foreach ($keunggulan as $item)
                        <div class="flex items-center gap-3">
                            <span class="text-sm">{{ $item['icon'] ?? '✅' }}</span>
                            <span class="text-slate-600 text-sm font-medium">{{ $item['teks'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="reveal reveal-delay-3">
                    <a href="#daftar"
                        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5 transition-all duration-200">
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
