{{-- resources/views/landing/_daftar.blade.php --}}
{{-- Form pendaftaran calon anggota dengan pilihan divisi & pertanyaan seleksi dinamis --}}

<section id="daftar" class="py-28 bg-white relative overflow-hidden">

    <div
        class="absolute top-0 left-0 w-96 h-96 bg-brand-50 rounded-full -translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 right-0 w-72 h-72 bg-accent/10 rounded-full translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none">
    </div>

    {{-- Alpine.js CDN (jika belum ada di layout) --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

            {{-- ── LEFT: Info ───────────────────────────────── --}}
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
                <div class="space-y-3">
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

                {{-- Divisi yang tersedia --}}
                @if ($divisis->count() > 0)
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Divisi Tersedia</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($divisis as $div)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold border border-brand-100">
                                    <span>{{ $div->icon }}</span> {{ $div->nama }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

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

            {{-- ── RIGHT: Form atau Pesan Ditutup ─────────────── --}}
            <div class="reveal reveal-delay-1">

                @if ($divisis->count() === 0)
                    {{-- ══════════════════════════════════════════════════
                    REKRUTMEN DITUTUP — semua divisi is_active = false
                ══════════════════════════════════════════════════ --}}
                    <div
                        class="flex flex-col items-center justify-center text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-12 gap-5 h-full min-h-[420px]">
                        <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center text-4xl">
                            🔒
                        </div>
                        <div>
                            <h3 class="font-display text-xl font-bold text-slate-700 mb-2">
                                Pendaftaran Belum Dibuka
                            </h3>
                            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                Saat ini belum ada divisi yang membuka rekrutmen. Pantau terus pengumuman resmi dari UKM
                                MCI untuk informasi pembukaan pendaftaran berikutnya.
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 text-xs text-slate-400 mt-2">
                            <div
                                class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5">
                                <span>📱</span> Ikuti Instagram UKM MCI
                            </div>
                            <div
                                class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5">
                                <span>📢</span> Pantau pengumuman kampus
                            </div>
                        </div>
                    </div>
                @else
                    {{-- ══════════════════════════════════════════════════
                    REKRUTMEN DIBUKA — ada divisi aktif
                ══════════════════════════════════════════════════ --}}

                    {{-- Notif Sukses --}}
                    @if (session('sukses'))
                        <div
                            class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-3">
                            <span class="text-2xl">🎉</span>
                            <div>
                                <div class="font-semibold text-emerald-800">Pendaftaran Berhasil!</div>
                                <div class="text-emerald-600 text-sm mt-1 leading-relaxed">{{ session('sukses') }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Notif Error Validasi --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5">
                            <div class="font-semibold text-red-800 mb-2 flex items-center gap-2">
                                <span>⚠️</span> Mohon periksa kembali:
                            </div>
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-600 text-sm flex items-center gap-2">
                                        <span
                                            class="w-1 h-1 rounded-full bg-red-400 flex-shrink-0"></span>{{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ── FORM dengan Alpine.js ──────────────────── --}}
                    @php
                        // Siapkan data divisi ke JSON untuk Alpine.js
                        $divisiJson = $divisis
                            ->map(
                                fn($d) => [
                                    'id' => $d->id,
                                    'nama' => $d->nama,
                                    'icon' => $d->icon,
                                    'deskripsi' => $d->deskripsi,
                                    'pertanyaan' => $d->pertanyaanSeleksis
                                        ->map(
                                            fn($p) => [
                                                'id' => $p->id,
                                                'pertanyaan_teks' => $p->pertanyaan_teks,
                                                'urut' => $p->urut,
                                            ],
                                        )
                                        ->values()
                                        ->toArray(),
                                ],
                            )
                            ->values()
                            ->toJson();
                    @endphp

                    <div x-data="formPendaftaran({{ $divisiJson }})" x-cloak
                        class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100 p-8 lg:p-10">

                        {{-- Header form --}}
                        <div class="mb-7">
                            <h3 class="font-display text-2xl font-bold text-slate-900">Formulir Pendaftaran</h3>
                            <p class="text-slate-400 text-sm mt-1">Isi semua data dengan lengkap dan benar.</p>

                            {{-- Step indicator --}}
                            <div class="flex items-center gap-3 mt-5">
                                <template x-for="(step, i) in steps" :key="i">
                                    <div class="flex items-center gap-2">
                                        <div
                                            :class="[
                                                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300',
                                                currentStep > i + 1 ? 'bg-emerald-500 text-white' :
                                                currentStep === i + 1 ?
                                                'bg-brand-600 text-white shadow-lg shadow-brand-200' :
                                                'bg-slate-100 text-slate-400'
                                            ]">
                                            <span x-show="currentStep <= i + 1" x-text="i + 1"></span>
                                            <svg x-show="currentStep > i + 1" class="w-3.5 h-3.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <span
                                            :class="['text-xs font-semibold hidden sm:block', currentStep === i + 1 ?
                                                'text-brand-700' : 'text-slate-400'
                                            ]"
                                            x-text="step"></span>
                                        <div x-show="i < steps.length - 1" class="w-8 h-px bg-slate-200 mx-1"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <form action="{{ route('daftar') }}" method="POST" @submit="submitForm">
                            @csrf

                            {{-- ══════════════════════════════════════
                            STEP 1: Data Diri
                        ══════════════════════════════════════ --}}
                            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-x-4"
                                x-transition:enter-end="opacity-100 translate-x-0">

                                <div class="space-y-4">

                                    {{-- Nama --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            Nama Lengkap <span class="text-red-400">*</span>
                                        </label>
                                        <input type="text" name="nama" x-model="form.nama"
                                            placeholder="Sesuai KTM/KTP"
                                            class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('nama') border-red-300 bg-red-50 @enderror"
                                            value="{{ old('nama') }}">
                                        @error('nama')
                                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- NIM + Angkatan --}}
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                NIM <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text" name="nim" x-model="form.nim"
                                                placeholder="Nomor Induk Mahasiswa"
                                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('nim') border-red-300 bg-red-50 @enderror"
                                                value="{{ old('nim') }}">
                                            @error('nim')
                                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                Angkatan <span class="text-red-400">*</span>
                                            </label>
                                            <select name="angkatan" x-model="form.angkatan"
                                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:border-brand-400 focus:bg-white transition-all duration-200 @error('angkatan') border-red-300 bg-red-50 @enderror">
                                                <option value="">Pilih angkatan</option>
                                                @foreach (['2025', '2024', '2023', '2022', '2021', '2020'] as $a)
                                                    <option value="{{ $a }}"
                                                        {{ old('angkatan') === $a ? 'selected' : '' }}>
                                                        {{ $a }}</option>
                                                @endforeach
                                            </select>
                                            @error('angkatan')
                                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            Email Aktif <span class="text-red-400">*</span>
                                        </label>
                                        <input type="email" name="email" x-model="form.email"
                                            placeholder="contoh@email.com"
                                            class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('email') border-red-300 bg-red-50 @enderror"
                                            value="{{ old('email') }}">
                                        @error('email')
                                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- No HP --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            No. HP / WhatsApp <span class="text-red-400">*</span>
                                        </label>
                                        <input type="tel" name="no_hp" x-model="form.no_hp"
                                            placeholder="08xxxxxxxxxx"
                                            class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 @error('no_hp') border-red-300 bg-red-50 @enderror"
                                            value="{{ old('no_hp') }}">
                                        @error('no_hp')
                                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Tombol Lanjut --}}
                                <button type="button" @click="goNext()"
                                    class="w-full mt-6 flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5 transition-all duration-200">
                                    Lanjut: Pilih Divisi
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </button>
                            </div>

                            {{-- ══════════════════════════════════════
                            STEP 2: Pilih Divisi
                        ══════════════════════════════════════ --}}
                            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-x-4"
                                x-transition:enter-end="opacity-100 translate-x-0">

                                <div class="mb-2">
                                    <p class="text-sm font-semibold text-slate-700 mb-1">
                                        Pilih Divisi yang Ingin Dimasuki <span class="text-red-400">*</span>
                                    </p>
                                    <p class="text-xs text-slate-400">Setiap divisi memiliki pertanyaan seleksi yang
                                        berbeda.</p>
                                </div>

                                {{-- Input hidden --}}
                                <input type="hidden" name="divisi_id" :value="form.divisi_id">
                                @error('divisi_id')
                                    <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
                                @enderror

                                {{-- Grid kartu divisi --}}
                                <div class="grid sm:grid-cols-2 gap-3 mt-4">
                                    <template x-for="div in divisis" :key="div.id">
                                        <button type="button" @click="pilihDivisi(div)"
                                            :class="[
                                                'relative text-left p-5 rounded-2xl border-2 transition-all duration-200 hover:-translate-y-0.5',
                                                form.divisi_id === div.id ?
                                                'border-brand-500 bg-brand-50 shadow-lg shadow-brand-100' :
                                                'border-slate-200 bg-white hover:border-brand-300 hover:bg-brand-50/30'
                                            ]">

                                            {{-- Checkmark saat dipilih --}}
                                            <div x-show="form.divisi_id === div.id"
                                                class="absolute top-3 right-3 w-5 h-5 rounded-full bg-brand-600 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>

                                            <span class="text-2xl mb-2 block" x-text="div.icon"></span>
                                            <p class="font-bold text-slate-800 text-sm" x-text="div.nama"></p>
                                            <p class="text-slate-400 text-xs mt-1 leading-relaxed line-clamp-2"
                                                x-text="div.deskripsi || 'Klik untuk memilih divisi ini.'"></p>

                                            {{-- Jumlah pertanyaan --}}
                                            <div x-show="div.pertanyaan.length > 0"
                                                class="mt-3 inline-flex items-center gap-1 text-[10px] font-bold text-brand-600 bg-brand-100 px-2 py-1 rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span x-text="div.pertanyaan.length + ' pertanyaan seleksi'"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                {{-- Info saat tidak ada divisi --}}
                                <div x-show="divisis.length === 0" class="text-center py-10 text-slate-400 text-sm">
                                    😔 Belum ada divisi yang membuka pendaftaran saat ini.
                                </div>

                                {{-- Info: divisi terpilih tidak punya pertanyaan --}}
                                <div x-show="form.divisi_id && selectedDivisi?.pertanyaan?.length === 0"
                                    class="mt-3 flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
                                    <span class="text-lg flex-shrink-0">ℹ️</span>
                                    <span>
                                        Divisi ini tidak memiliki pertanyaan seleksi.
                                        Klik <strong>Kirim Pendaftaran</strong> untuk langsung mendaftar tanpa mengisi
                                        jawaban.
                                    </span>
                                </div>

                                {{-- Navigasi --}}
                                <div class="flex gap-3 mt-6">
                                    <button type="button" @click="currentStep = 1"
                                        class="flex-1 px-6 py-4 rounded-2xl border-2 border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all">
                                        ← Kembali
                                    </button>
                                    <button type="button" @click="goNext()" :disabled="!form.divisi_id"
                                        :class="[
                                            'flex-1 px-6 py-4 rounded-2xl font-bold text-sm transition-all',
                                            form.divisi_id ?
                                            'bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5' :
                                            'bg-slate-100 text-slate-400 cursor-not-allowed'
                                        ]">
                                        <span
                                            x-text="selectedDivisi?.pertanyaan?.length > 0 ? 'Lanjut: Pertanyaan Seleksi' : 'Kirim Pendaftaran →'"></span>
                                    </button>
                                </div>
                            </div>

                            {{-- ══════════════════════════════════════
                            STEP 3: Pertanyaan Seleksi
                            (Hanya muncul jika divisi punya pertanyaan)
                        ══════════════════════════════════════ --}}
                            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-x-4"
                                x-transition:enter-end="opacity-100 translate-x-0">

                                {{-- Info divisi yang dipilih --}}
                                <div
                                    class="flex items-center gap-3 p-4 rounded-2xl bg-brand-50 border border-brand-100 mb-6">
                                    <span class="text-2xl" x-text="selectedDivisi?.icon"></span>
                                    <div>
                                        <p class="font-bold text-brand-800 text-sm"
                                            x-text="'Divisi ' + selectedDivisi?.nama"></p>
                                        <p class="text-brand-600 text-xs"
                                            x-text="selectedDivisi?.pertanyaan?.length + ' pertanyaan wajib dijawab'">
                                        </p>
                                    </div>
                                </div>

                                {{-- Daftar pertanyaan --}}
                                <div class="space-y-6">
                                    <template x-for="(pertanyaan, i) in selectedDivisi?.pertanyaan"
                                        :key="pertanyaan.id">
                                        <div>
                                            {{-- Label pertanyaan --}}
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                <span
                                                    class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-brand-600 text-white text-xs font-bold mr-1.5"
                                                    x-text="i + 1"></span>
                                                <span x-text="pertanyaan.pertanyaan_teks"></span>
                                                <span class="text-red-400 ml-1">*</span>
                                            </label>

                                            {{-- Input hidden untuk pertanyaan_id --}}
                                            {{-- Jawaban dikirim sebagai jawaban[pertanyaan_id] --}}
                                            <textarea :name="'jawaban[' + pertanyaan.id + ']'" x-model="form.jawaban[pertanyaan.id]" rows="4"
                                                placeholder="Tulis jawaban Anda di sini..."
                                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 resize-none leading-relaxed"></textarea>

                                            {{-- Counter karakter --}}
                                            <p class="text-xs text-slate-400 mt-1 text-right"
                                                x-text="(form.jawaban[pertanyaan.id] || '').length + ' karakter'"></p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Privacy note --}}
                                <p class="text-xs text-slate-400 leading-relaxed mt-5">
                                    🔒 Data Anda disimpan secara aman. Jawaban akan dinilai oleh Ketua Divisi terkait.
                                </p>

                                {{-- Navigasi --}}
                                <div class="flex gap-3 mt-6">
                                    <button type="button" @click="currentStep = 2"
                                        class="flex-1 px-6 py-4 rounded-2xl border-2 border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all">
                                        ← Kembali
                                    </button>
                                    <button type="submit" :disabled="isSubmitting"
                                        class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-white font-bold text-sm hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:translate-y-0">
                                        <span x-show="!isSubmitting">Kirim Pendaftaran 🚀</span>
                                        <span x-show="isSubmitting" class="flex items-center gap-2">
                                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Mengirim...
                                        </span>
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>{{-- end x-data --}}

                @endif
                {{-- /end @if ($divisis->count() === 0) ... @else ... @endif --}}

            </div>{{-- end right col --}}

        </div>
    </div>
</section>

{{-- Alpine.js hanya diload jika ada divisi aktif --}}
@if ($divisis->count() > 0)
    @push('scripts')
        <script>
            function formPendaftaran(divisis) {
                return {
                    divisis: divisis,
                    currentStep: 1,
                    isSubmitting: false,

                    // Definisi nama step
                    steps: ['Data Diri', 'Pilih Divisi', 'Pertanyaan'],

                    form: {
                        nama: '{{ old('nama') }}',
                        nim: '{{ old('nim') }}',
                        angkatan: '{{ old('angkatan') }}',
                        email: '{{ old('email') }}',
                        no_hp: '{{ old('no_hp') }}',
                        divisi_id: {{ old('divisi_id') ? old('divisi_id') : 'null' }},
                        jawaban: {}, // { pertanyaan_id: 'teks jawaban' }
                    },

                    // Divisi yang sedang dipilih
                    get selectedDivisi() {
                        if (!this.form.divisi_id) return null;
                        return this.divisis.find(d => d.id === this.form.divisi_id) || null;
                    },

                    // Pilih divisi & reset jawaban
                    pilihDivisi(div) {
                        this.form.divisi_id = div.id;
                        this.form.jawaban = {}; // reset jawaban sebelumnya
                    },

                    // Validasi sederhana per step
                    validateStep(step) {
                        if (step === 1) {
                            if (!this.form.nama.trim()) {
                                alert('Nama lengkap wajib diisi.');
                                return false;
                            }
                            if (!this.form.nim.trim()) {
                                alert('NIM wajib diisi.');
                                return false;
                            }
                            if (!this.form.angkatan) {
                                alert('Angkatan wajib dipilih.');
                                return false;
                            }
                            if (!this.form.email.trim()) {
                                alert('Email wajib diisi.');
                                return false;
                            }
                            if (!this.form.no_hp.trim()) {
                                alert('No. HP wajib diisi.');
                                return false;
                            }
                        }
                        if (step === 2) {
                            if (!this.form.divisi_id) {
                                alert('Pilih divisi terlebih dahulu.');
                                return false;
                            }
                        }
                        return true;
                    },

                    // Pindah ke step berikutnya
                    goNext() {
                        if (!this.validateStep(this.currentStep)) return;

                        // Dari step 2: skip step 3 jika divisi tidak punya pertanyaan
                        if (this.currentStep === 2) {
                            const hasPertanyaan = this.selectedDivisi?.pertanyaan?.length > 0;
                            if (!hasPertanyaan) {
                                // $el adalah div[x-data]. Form ada di dalamnya (child), bukan ancestor.
                                // Gunakan querySelector ke bawah, bukan closest ke atas.
                                this.$nextTick(() => {
                                    const form = this.$el.querySelector('form');
                                    if (form) form.requestSubmit();
                                });
                                return;
                            }
                        }

                        if (this.currentStep < 3) this.currentStep++;
                    },

                    // Handle submit
                    submitForm() {
                        this.isSubmitting = true;
                    },

                    // Jika ada error dari server, kembali ke step yang relevan
                    init() {
                        @if ($errors->has('divisi_id') || $errors->has('nim'))
                            this.currentStep = 2;
                        @elseif ($errors->any())
                            this.currentStep = 1;
                        @endif

                        // Restore divisi_id dari old input jika ada
                        @if (old('divisi_id'))
                            this.form.divisi_id = {{ old('divisi_id') }};
                        @endif
                    }
                }
            }
        </script>
    @endpush
@endif
