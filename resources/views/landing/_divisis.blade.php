{{-- resources/views/landing/_programs.blade.php --}}
<section id="program" class="py-28 bg-slate-50/80 relative overflow-hidden">
    <div class="absolute inset-0 dot-grid opacity-50"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <span
                class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 mb-4">
                Divisi
            </span>
            <h2 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                Temukan Minat
                <br><span class="gradient-text"> Divisimu</span>
            </h2>
            <p class="text-slate-500 mt-4 text-lg font-light">
                Kami memiliki berbagai divisi yang dapat disesuaikan dengan minat dan kemampuan Anda.
            </p>
        </div>

        {{-- Cards --}}
        @if ($divisis->isNotEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($divisis as $divisi)
                    <div
                        class="reveal reveal-delay-{{ min(($loop->index % 4) + 1, 4) }} bg-white rounded-2xl p-6 border border-slate-100 tech-card shadow-sm group">
                        <div
                            class="w-12 h-12 rounded-xl bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center text-2xl mb-5 transition-colors duration-200">
                            {{ $divisi->icon }}
                        </div>
                        <div
                            class="inline-block text-[10px] font-bold tracking-widest uppercase text-brand-400 bg-brand-50 px-2 py-0.5 rounded-full mb-2">
                            {{ $divisi->kategori }}
                        </div>
                        <h3
                            class="font-display font-bold text-slate-800 text-lg mb-2 group-hover:text-brand-600 transition-colors">
                            {{ $divisi->nama }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            {{ $divisi->deskripsi }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Default programs jika database kosong --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ([['💻', 'Divisi', 'Web Development', 'Membangun aplikasi web modern dengan framework terkini'], ['🤖', 'Divisi', 'AI & Machine Learning', 'Mengeksplorasi kecerdasan buatan dan data science'], ['📱', 'Divisi', 'Mobile Development', 'Merancang aplikasi Android & iOS yang inovatif'], ['🎨', 'Divisi', 'UI/UX Design', 'Menciptakan pengalaman pengguna yang intuitif dan estetis'], ['🔒', 'Divisi', 'Cyber Security', 'Mempelajari keamanan sistem dan ethical hacking'], ['☁️', 'Divisi', 'Cloud & DevOps', 'Infrastruktur cloud, CI/CD, dan otomasi deployment'], ['🏆', 'Program', 'Lomba & Hackathon', 'Mengikuti kompetisi teknologi tingkat nasional'], ['📚', 'Program', 'Workshop Bulanan', 'Pelatihan intensif dengan instruktur berpengalaman']] as [$icon, $kategori, $nama, $desc])
                    <div
                        class="reveal reveal-delay-{{ min(($loop->index % 4) + 1, 4) }} bg-white rounded-2xl p-6 border border-slate-100 tech-card shadow-sm group">
                        <div
                            class="w-12 h-12 rounded-xl bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center text-2xl mb-5 transition-colors duration-200">
                            {{ $icon }}
                        </div>
                        <div
                            class="inline-block text-[10px] font-bold tracking-widest uppercase text-brand-400 bg-brand-50 px-2 py-0.5 rounded-full mb-2">
                            {{ $kategori }}
                        </div>
                        <h3
                            class="font-display font-bold text-slate-800 text-lg mb-2 group-hover:text-brand-600 transition-colors">
                            {{ $nama }}
                        </h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CTA Strip --}}
        <div class="mt-14 reveal">
            <div
                class="rounded-3xl bg-gradient-to-r from-brand-600 to-brand-800 p-8 lg:p-10 flex flex-col lg:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3">
                </div>
                <div class="absolute bottom-0 left-20 w-40 h-40 bg-accent/10 rounded-full translate-y-1/2"></div>
                <div class="relative z-10 text-center lg:text-left">
                    <h3 class="font-display text-2xl lg:text-3xl font-bold text-white">Siap bergabung dengan divisi
                        pilihan Anda?</h3>
                    <p class="text-brand-200 mt-2 font-light">Daftar sekarang dan mulai perjalanan teknologi Anda
                        bersama MCI.</p>
                </div>
                <a href="#daftar"
                    class="relative z-10 flex-shrink-0 inline-flex items-center gap-2 px-7 py-4 rounded-2xl bg-white text-brand-700 font-bold text-sm hover:bg-brand-50 shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    Daftar Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
