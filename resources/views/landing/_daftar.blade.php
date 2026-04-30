{{-- resources/views/landing/_daftar.blade.php --}}
<section id="daftar" class="py-28 bg-white relative overflow-hidden">

    <div
        class="absolute top-0 left-0 w-96 h-96 bg-brand-50 rounded-full -translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 right-0 w-72 h-72 bg-accent/10 rounded-full translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

            {{-- Left: Info --}}
            <div class="space-y-8 reveal lg:sticky lg:top-28">
                <div>
                    <span
                        class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 mb-4">
                        Open Recruitment
                    </span>
                    <h2 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                        Mulai Perjalanan
                        <span class="gradient-text"> Teknologimu</span>
                    </h2>
                    <p class="text-slate-500 mt-4 font-light text-lg leading-relaxed">
                        Bergabunglah bersama ratusan mahasiswa yang telah memilih MCI sebagai rumah berkembang di dunia
                        teknologi.
                    </p>
                </div>

                {{-- Keuntungan --}}
                <div class="space-y-4">
                    @foreach ([['🚀', 'Akses Workshop Eksklusif', 'Pelatihan coding, design, dan tech leadership setiap bulan'], ['🏆', 'Ikut Kompetisi Bergengsi', 'Dukungan penuh untuk lomba nasional & internasional'], ['💼', 'Bangun Portofolio Nyata', 'Project kolaboratif yang siap masuk CV Anda'], ['🌐', 'Jaringan Alumni Luas', 'Terhubung dengan alumni di perusahaan tech terkemuka']] as [$icon, $title, $desc])
                        <div
                            class="flex gap-4 items-start p-4 rounded-2xl hover:bg-brand-50/50 transition-colors group">
                            <div
                                class="w-11 h-11 rounded-xl bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center text-xl flex-shrink-0 transition-colors">
                                {{ $icon }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800 text-sm">{{ $title }}</div>
                                <div class="text-slate-500 text-sm mt-0.5 font-light">{{ $desc }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Quote --}}
                <div class="bg-gradient-to-br from-brand-50 to-white rounded-2xl p-6 border border-brand-100">
                    <div class="text-3xl mb-3">💬</div>
                    <blockquote class="text-slate-600 text-sm italic leading-relaxed">
                        "Bergabung di MCI adalah keputusan terbaik selama kuliah. Skill saya berkembang pesat dan saya
                        mendapat pekerjaan impian sebelum lulus."
                    </blockquote>
                    <div class="mt-3 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-brand-200 flex items-center justify-center font-bold text-brand-700 text-xs">
                            A</div>
                        <div>
                            <div class="text-xs font-bold text-slate-700">Alumni MCI 2022</div>
                            <div class="text-xs text-slate-400">Software Engineer @ Startup Unicorn</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Form --}}
            <div class="reveal reveal-delay-1">
                {{-- Success message --}}
                @if (session('sukses'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-3">
                        <span class="text-2xl">🎉</span>
                        <div>
                            <div class="font-semibold text-emerald-800">Pendaftaran Berhasil!</div>
                            <div class="text-emerald-600 text-sm mt-0.5">{{ session('sukses') }}</div>
                        </div>
                    </div>
                @endif

                {{-- Error messages --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5">
                        <div class="font-semibold text-red-800 mb-2">Mohon periksa kembali:</div>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-600 text-sm flex items-center gap-2">
                                    <span>•</span> {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100 p-8 lg:p-10">
                    <div class="mb-8">
                        <h3 class="font-display text-2xl font-bold text-slate-900">Formulir Pendaftaran</h3>
                        <p class="text-slate-400 text-sm mt-1">Isi data dengan lengkap dan benar.</p>
                    </div>

                    <form action="{{ route('daftar') }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Nama Lengkap <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                placeholder="Masukkan nama lengkap Anda"
                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('nama_lengkap') border-red-300 bg-red-50 @enderror">
                        </div>

                        {{-- NIM & Email --}}
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    NIM <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="nim" value="{{ old('nim') }}"
                                    placeholder="Nomor Induk Mahasiswa"
                                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('nim') border-red-300 bg-red-50 @enderror">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    No. HP <span class="text-red-400">*</span>
                                </label>
                                <input type="tel" name="no_hp" value="{{ old('no_hp') }}"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('no_hp') border-red-300 bg-red-50 @enderror">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Email Aktif <span class="text-red-400">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="contoh@email.com"
                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('email') border-red-300 bg-red-50 @enderror">
                        </div>

                        {{-- Jurusan & Angkatan --}}
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Jurusan <span class="text-red-400">*</span>
                                </label>
                                <select name="jurusan"
                                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:border-brand-400 focus:bg-white transition-all duration-200 @error('jurusan') border-red-300 bg-red-50 @enderror">
                                    <option value="">Pilih jurusan</option>
                                    @foreach (['Teknik Informatika', 'Sistem Informasi', 'Ilmu Komputer', 'Teknik Elektro', 'Teknik Komputer', 'Matematika', 'Lainnya'] as $j)
                                        <option value="{{ $j }}"
                                            {{ old('jurusan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Angkatan <span class="text-red-400">*</span>
                                </label>
                                <select name="angkatan"
                                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:border-brand-400 focus:bg-white transition-all duration-200 @error('angkatan') border-red-300 bg-red-50 @enderror">
                                    <option value="">Pilih angkatan</option>
                                    @foreach (['2024', '2023', '2022', '2021', '2020'] as $a)
                                        <option value="{{ $a }}"
                                            {{ old('angkatan') === $a ? 'selected' : '' }}>{{ $a }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Motivasi --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Motivasi Bergabung
                                <span class="font-normal text-slate-400 ml-1">(opsional)</span>
                            </label>
                            <textarea name="motivasi" rows="4"
                                placeholder="Ceritakan alasan Anda ingin bergabung dengan UKM MCI dan harapan yang ingin dicapai..."
                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 resize-none leading-relaxed">{{ old('motivasi') }}</textarea>
                        </div>

                        {{-- Privacy note --}}
                        <p class="text-xs text-slate-400 leading-relaxed">
                            🔒 Data Anda akan disimpan secara aman dan hanya digunakan untuk keperluan administrasi UKM
                            MCI. Kami tidak akan membagikan data kepada pihak ketiga.
                        </p>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-xl shadow-brand-200 hover:shadow-brand-300 hover:-translate-y-0.5 transition-all duration-200 mt-2">
                            <span>Kirim Pendaftaran</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
