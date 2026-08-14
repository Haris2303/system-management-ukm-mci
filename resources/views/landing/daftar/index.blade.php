@extends('layouts.app')

@section('title', 'Pendaftaran Anggota — UKM MCI')
@section('description',
    'Bergabung menjadi anggota UKM MCI (Media Creative Informations). Isi formulir pendaftaran dan
    mulai perjalanan teknologimu bersama kami.')

@section('content')

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
                            {{ $openRecruitment
                                ? $openRecruitment->judul . ($openRecruitment->gelombang ? ' · ' . $openRecruitment->gelombang : '')
                                : 'Close Recruitment' }}
                        </span>
                        <h1 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight"
                            style="line-height: 60px">
                            Mulai Perjalanan
                            <span class="gradient-text"> Teknologimu</span>
                        </h1>
                        <p class="text-slate-500 mt-4 font-light text-lg leading-relaxed">
                            @if ($openRecruitment?->deskripsi)
                                {{ $openRecruitment->deskripsi }}
                            @else
                                Bergabunglah bersama ratusan mahasiswa yang telah memilih MCI sebagai rumah berkembang di
                                dunia teknologi.
                            @endif
                        </p>
                        @if ($openRecruitment)
                            <div class="flex items-center gap-4 mt-4 text-sm text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-slate-400"></i> Ditutup <strong
                                        class="text-slate-700">{{ $openRecruitment->waktu_selesai->translatedFormat('d M Y, H:i') }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Keuntungan --}}
                    <div class="space-y-3">
                        @foreach ([['fa-solid fa-rocket', 'Akses Workshop Eksklusif', 'Pelatihan coding, design, dan tech leadership setiap bulan'], ['fa-solid fa-trophy', 'Ikut Kompetisi Bergengsi', 'Dukungan penuh untuk lomba nasional & internasional'], ['fa-solid fa-briefcase', 'Bangun Portofolio Nyata', 'Project kolaboratif yang siap masuk CV Anda'], ['fa-solid fa-globe', 'Jaringan Alumni Luas', 'Terhubung dengan alumni di perusahaan tech terkemuka']] as [$iconClass, $title, $desc])
                            <div
                                class="flex gap-4 items-start p-4 rounded-2xl hover:bg-brand-50/50 transition-colors group">
                                <div
                                    class="w-11 h-11 rounded-xl bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center text-brand-500 flex-shrink-0 transition-colors">
                                    <i class="{{ $iconClass }}"></i>
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
                                        <i class="{{ $div->icon }} fa-xs"></i> {{ $div->nama }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- ── RIGHT: Form atau Pesan Ditutup ─────────────── --}}
                <div class="reveal reveal-delay-1">

                    @if (!$openRecruitment)
                        <div
                            class="flex flex-col items-center justify-center text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-12 gap-5 h-full min-h-[420px]">
                            <div
                                class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center text-4xl text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <div>
                                <h3 class="font-display text-xl font-bold text-slate-700 mb-2">
                                    Pendaftaran Belum Dibuka
                                </h3>
                                <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                    Saat ini belum ada periode rekrutmen yang dibuka. Pantau terus pengumuman resmi dari UKM
                                    MCI untuk informasi pembukaan pendaftaran berikutnya.
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3 text-xs text-slate-400 mt-2">
                                <div
                                    class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5">
                                    <i class="fa-brands fa-instagram text-pink-400"></i> Ikuti Instagram UKM MCI
                                </div>
                                <div
                                    class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5">
                                    <i class="fa-solid fa-bullhorn text-slate-400"></i> Pantau pengumuman kampus
                                </div>
                            </div>
                        </div>
                    @elseif ($divisis->count() === 0)
                        <div
                            class="flex flex-col items-center justify-center text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-12 gap-5 h-full min-h-[420px]">
                            <div
                                class="w-20 h-20 rounded-2xl bg-amber-50 flex items-center justify-center text-4xl text-amber-400">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h3 class="font-display text-xl font-bold text-slate-700 mb-2">
                                    Segera Dibuka
                                </h3>
                                <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                    Periode <strong class="text-slate-600">{{ $openRecruitment->judul }}</strong> sedang
                                    disiapkan. Divisi akan segera aktif, cek kembali dalam beberapa saat.
                                </p>
                            </div>
                        </div>
                    @else
                        {{-- Notif Sukses --}}
                        @if (session('sukses'))
                            <div
                                class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-2xl text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <div>
                                    <div class="font-semibold text-emerald-800">Pendaftaran Berhasil!</div>
                                    <div class="text-emerald-600 text-sm mt-1 leading-relaxed">{{ session('sukses') }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- Notif Error Validasi Server --}}
                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5">
                                <div class="font-semibold text-red-800 mb-2 flex items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Mohon periksa kembali:
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

                            <form action="{{ route('daftar') }}" method="POST" @submit.prevent="submitForm($event)">
                                @csrf

                                {{-- STEP 1: Data Diri --}}
                                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0">

                                    <div class="space-y-4">

                                        {{-- Nama Lengkap --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                Nama Lengkap <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text" name="nama" x-model="form.nama" @input="validateNama"
                                                placeholder="Nama Lengkap"
                                                :class="errors.nama ? 'border-red-400 bg-red-50' :
                                                    'border-slate-200 bg-slate-50'"
                                                class="w-full px-4 py-3.5 rounded-xl border text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200"
                                                value="{{ old('nama') }}">

                                            {{-- Error Realtime Human Error (Alpine.js) --}}
                                            <p x-show="errors.nama" x-text="errors.nama"
                                                class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                            </p>
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
                                                    @input="validateNim" placeholder="Nomor Induk Mahasiswa (Angka)"
                                                    :class="errors.nim ? 'border-red-400 bg-red-50' :
                                                        'border-slate-200 bg-slate-50'"
                                                    class="w-full px-4 py-3.5 rounded-xl border text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200"
                                                    value="{{ old('nim') }}">

                                                {{-- Error Realtime NIM --}}
                                                <p x-show="errors.nim" x-text="errors.nim"
                                                    class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                                    <i class="fa-solid fa-circle-exclamation"></i>
                                                </p>
                                                @error('nim')
                                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                    Angkatan <span class="text-red-400">*</span>
                                                </label>
                                                <select name="angkatan" x-model="form.angkatan"
                                                    @change="validateAngkatan"
                                                    :class="errors.angkatan ? 'border-red-400 bg-red-50' :
                                                        'border-slate-200 bg-slate-50'"
                                                    class="w-full px-4 py-3.5 rounded-xl border text-slate-800 text-sm focus:border-brand-400 focus:bg-white transition-all duration-200">
                                                    <option value="">Pilih angkatan</option>
                                                    @foreach ($angkatanList as $a)
                                                        <option value="{{ $a }}"
                                                            {{ old('angkatan') === $a ? 'selected' : '' }}>
                                                            {{ $a }}</option>
                                                    @endforeach
                                                </select>

                                                {{-- Error Realtime Angkatan --}}
                                                <p x-show="errors.angkatan" x-text="errors.angkatan" x-cloak
                                                    class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                                    <i class="fa-solid fa-circle-exclamation"></i>
                                                </p>

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
                                                @input="validateEmail" placeholder="contoh@gmail.com"
                                                :class="errors.email ? 'border-red-400 bg-red-50' :
                                                    'border-slate-200 bg-slate-50'"
                                                class="w-full px-4 py-3.5 rounded-xl border text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200"
                                                value="{{ old('email') }}">
                                            {{-- Error Realtime Email (Alpine.js) --}}
                                            <p x-show="errors.email" x-text="errors.email"
                                                class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                            </p>
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
                                                @input="validateNoHp" placeholder="08xxxxxxxxxx (Angka)"
                                                :class="errors.no_hp ? 'border-red-400 bg-red-50' :
                                                    'border-slate-200 bg-slate-50'"
                                                class="w-full px-4 py-3.5 rounded-xl border text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200"
                                                value="{{ old('no_hp') }}">

                                            {{-- Error Realtime No HP --}}
                                            <p x-show="errors.no_hp" x-text="errors.no_hp"
                                                class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                            </p>
                                            @error('no_hp')
                                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tombol Lanjut --}}
                                    <button type="button" @click="goNext()" :disabled="isValidating"
                                        :class="isValidating ? 'opacity-70 cursor-wait translate-y-0 shadow-none' :
                                            'hover:-translate-y-0.5 hover:bg-brand-700 shadow-lg shadow-brand-200'"
                                        class="w-full mt-6 flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-white font-bold text-base transition-all duration-200">
                                        {{-- Tampilan Normal --}}
                                        <span x-show="!isValidating" class="flex items-center gap-2">
                                            Lanjut: Pilih Divisi
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </span>

                                        {{-- Tampilan Loading --}}
                                        <span x-show="isValidating" class="flex items-center gap-2" x-cloak>
                                            <svg class="animate-spin w-5 h-5 text-white" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Mengecek Inputan...
                                        </span>
                                    </button>
                                </div>

                                {{-- STEP 2: Pilih Divisi --}}
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

                                    <input type="hidden" name="divisi_id" :value="form.divisi_id">
                                    @error('divisi_id')
                                        <p class="text-red-500 text-xs mb-3">{{ $message }}</p>
                                    @enderror

                                    <div class="grid sm:grid-cols-2 gap-3 mt-4">
                                        <template x-for="div in divisis" :key="div.id">
                                            <button type="button" @click="pilihDivisi(div)"
                                                :class="[
                                                    'relative text-left p-5 rounded-2xl border-2 transition-all duration-200 hover:-translate-y-0.5',
                                                    form.divisi_id === div.id ?
                                                    'border-brand-500 bg-brand-50 shadow-lg shadow-brand-100' :
                                                    'border-slate-200 bg-white hover:border-brand-300 hover:bg-brand-50/30'
                                                ]">

                                                <div x-show="form.divisi_id === div.id"
                                                    class="absolute top-3 right-3 w-5 h-5 rounded-full bg-brand-600 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>

                                                <span class="text-xl mb-2 block text-brand-500"><i
                                                        :class="div.icon"></i></span>
                                                <p class="font-bold text-slate-800 text-sm" x-text="div.nama"></p>
                                                <p class="text-slate-400 text-xs mt-1 leading-relaxed line-clamp-2"
                                                    x-text="div.deskripsi || 'Klik untuk memilih divisi ini.'"></p>

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

                                    <div x-show="divisis.length === 0" class="text-center py-10 text-slate-400 text-sm">
                                        <i class="fa-regular fa-face-sad-tear mr-1"></i> Belum ada divisi yang membuka
                                        pendaftaran saat ini.
                                    </div>

                                    <div x-show="form.divisi_id && selectedDivisi?.pertanyaan?.length === 0"
                                        class="mt-3 flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
                                        <i class="fa-solid fa-circle-info text-lg flex-shrink-0 mt-0.5"></i>
                                        <span>
                                            Divisi ini tidak memiliki pertanyaan seleksi. Klik <strong>Kirim
                                                Pendaftaran</strong> untuk langsung mendaftar tanpa mengisi jawaban.
                                        </span>
                                    </div>

                                    <div class="flex gap-3 mt-6">
                                        <button type="button" @click="currentStep = 1" :disabled="isValidating"
                                            class="flex-1 px-6 py-4 rounded-2xl border-2 border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                            ← Kembali
                                        </button>
                                        <button type="button" @click="goNext()"
                                            :disabled="!form.divisi_id || isValidating"
                                            :class="[
                                                'flex-1 px-6 py-4 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2',
                                                (!form.divisi_id || isValidating) ?
                                                'bg-slate-100 text-slate-400 cursor-not-allowed shadow-none translate-y-0' :
                                                'bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5'
                                            ]">
                                            {{-- Tampilan Normal --}}
                                            <span x-show="!isValidating"
                                                x-text="selectedDivisi?.pertanyaan?.length > 0 ? 'Lanjut: Pertanyaan Seleksi' : 'Kirim Pendaftaran →'"></span>

                                            {{-- Tampilan Loading --}}
                                            <span x-show="isValidating" class="flex items-center gap-2" x-cloak>
                                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                                Memproses...
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                {{-- STEP 3: Pertanyaan Seleksi --}}
                                <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0">

                                    <div
                                        class="flex items-center gap-3 p-4 rounded-2xl bg-brand-50 border border-brand-100 mb-6">
                                        <span class="text-xl text-brand-500"><i :class="selectedDivisi?.icon"></i></span>
                                        <div>
                                            <p class="font-bold text-brand-800 text-sm"
                                                x-text="'Divisi ' + selectedDivisi?.nama"></p>
                                            <p class="text-brand-600 text-xs"
                                                x-text="selectedDivisi?.pertanyaan?.length + ' pertanyaan wajib dijawab'">
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <template x-for="(pertanyaan, i) in selectedDivisi?.pertanyaan"
                                            :key="pertanyaan.id">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                    <span
                                                        class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-brand-600 text-white text-xs font-bold mr-1.5"
                                                        x-text="i + 1"></span>
                                                    <span x-text="pertanyaan.pertanyaan_teks"></span>
                                                    <span class="text-red-400 ml-1">*</span>
                                                </label>

                                                <textarea :name="'jawaban[' + pertanyaan.id + ']'" x-model="form.jawaban[pertanyaan.id]"
                                                    @input="validateJawaban(pertanyaan.id)" rows="4" placeholder="Tulis jawaban Anda di sini..."
                                                    :class="(errors.jawaban && errors.jawaban[pertanyaan.id]) ?
                                                    'border-red-400 bg-red-50' : 'border-slate-200 bg-slate-50'"
                                                    class="w-full px-4 py-3.5 rounded-xl border text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 resize-none leading-relaxed"></textarea>

                                                {{-- Error Realtime Jawaban --}}
                                                <p x-show="errors.jawaban && errors.jawaban[pertanyaan.id]"
                                                    x-text="errors.jawaban[pertanyaan.id]" x-cloak
                                                    class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                                    <i class="fa-solid fa-circle-exclamation"></i>
                                                </p>

                                                <p class="text-xs text-slate-400 mt-1 text-right"
                                                    x-text="(form.jawaban[pertanyaan.id] || '').length + ' karakter'">
                                                </p>
                                            </div>
                                        </template>
                                    </div>

                                    <p class="text-xs text-slate-400 leading-relaxed mt-5">
                                        <i class="fa-solid fa-lock mr-1 text-slate-400"></i> Data Anda disimpan secara
                                        aman. Jawaban akan dinilai oleh Ketua Divisi terkait.
                                    </p>

                                    <div class="flex gap-3 mt-6">
                                        <button type="button" @click="currentStep = 2"
                                            class="flex-1 px-6 py-4 rounded-2xl border-2 border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all">
                                            ← Kembali
                                        </button>
                                        <button type="submit" :disabled="isSubmitting"
                                            class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-white font-bold text-sm hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:translate-y-0">
                                            <span x-show="!isSubmitting">Kirim Pendaftaran <i
                                                    class="fa-solid fa-paper-plane ml-0.5"></i></span>
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
                        </div>

                    @endif

                </div>

            </div>
        </div>
    </section>

    {{-- Script Alpine.js --}}
    @if ($divisis->count() > 0)
        @push('scripts')
            <script>
                function formPendaftaran(divisis) {
                    return {
                        divisis: divisis,
                        currentStep: 1,
                        isSubmitting: false,
                        isValidating: false,

                        steps: ['Data Diri', 'Pilih Divisi', 'Pertanyaan'],

                        form: {
                            nama: '{{ old('nama') }}',
                            nim: '{{ old('nim') }}',
                            angkatan: '{{ old('angkatan') }}',
                            email: '{{ old('email') }}',
                            no_hp: '{{ old('no_hp') }}',
                            divisi_id: {{ old('divisi_id') ? old('divisi_id') : 'null' }},
                            jawaban: {},
                        },

                        errors: {
                            nama: '',
                            nim: '',
                            email: '',
                            no_hp: '',
                            'jawaban': {}
                        },

                        get selectedDivisi() {
                            if (!this.form.divisi_id) return null;
                            return this.divisis.find(d => d.id === this.form.divisi_id) || null;
                        },

                        validateNama() {
                            const original = this.form.nama;
                            let cleaned = original.replace(/[^a-zA-Z\s\.\,\'\`]/g, '');
                cleaned = cleaned.replace(/\b\w/g, char => char.toUpperCase());

                if (original.replace(/[^a-zA-Z\s\.\,\'\`]/g, '') !== original) {
                                this.errors.nama = 'Nama tidak dapat memuat angka atau simbol!';
                                this.form.nama = cleaned;
                            } else if (!cleaned.trim()) {
                                this.errors.nama = 'Nama lengkap wajib diisi.';
                                this.form.nama = cleaned;
                            } else {
                                this.errors.nama = '';
                                this.form.nama = cleaned;
                            }
                        },

                        validateNim() {
                            const original = this.form.nim;
                            const cleaned = original.replace(/[^0-9]/g, '');

                            if (original !== cleaned) {
                                this.errors.nim = 'NIM tidak dapat memuat huruf atau simbol!';
                                this.form.nim = cleaned;
                            } else if (!cleaned.trim()) {
                                this.errors.nim = 'NIM wajib diisi.';
                            } else if (cleaned.length !== 12) {
                                this.errors.nim = 'NIM harus tepat 12 karakter.';
                            } else {
                                this.errors.nim = '';
                            }
                        },

                        validateEmail() {
                            const email = this.form.email.trim();
                            // RegEx standar untuk pengecekan format email yang sah
                            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                            if (!email) {
                                this.errors.email = 'Email wajib diisi.';
                            } else if (!emailRegex.test(email)) {
                                this.errors.email = 'Format email tidak valid (contoh: nama@gmail.com).';
                            } else {
                                this.errors.email = '';
                            }
                        },

                        validateNoHp() {
                            const original = this.form.no_hp;
                            const cleaned = original.replace(/[^0-9]/g, '');

                            if (original !== cleaned) {
                                this.errors.no_hp = 'Nomor HP harus berupa angka!';
                                this.form.no_hp = cleaned;
                            } else if (!cleaned.trim()) {
                                this.errors.no_hp = 'Nomor HP wajib diisi.';
                            } else if (cleaned.length < 11 || cleaned.length > 13) {
                                this.errors.no_hp = 'Nomor HP harus 11 hingga 13 digit';
                            } else {
                                this.errors.no_hp = '';
                            }
                        },

                        validateAngkatan() {
                            if (!this.form.angkatan) {
                                this.errors.angkatan = 'Angkatan wajib dipilih.';
                            } else {
                                this.errors.angkatan = '';
                            }
                        },

                        validateJawaban(id) {
                            if (!this.form.jawaban[id] || !this.form.jawaban[id].trim()) {
                                this.errors.jawaban[id] = 'Jawaban tidak boleh kosong.';
                            } else {
                                this.errors.jawaban[id] = '';
                            }
                        },

                        pilihDivisi(div) {
                            this.form.divisi_id = div.id;
                            this.form.jawaban = {};
                        },

                        async serverValidate() {
                            this.isValidating = true;
                            try {
                                const response = await fetch('{{ route('daftar.validate') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        nama: this.form.nama,
                                        nim: this.form.nim,
                                        angkatan: this.form.angkatan,
                                        email: this.form.email,
                                        no_hp: this.form.no_hp,
                                        divisi_id: this.form.divisi_id
                                    })
                                });

                                const result = await response.json();

                                if (!response.ok) {
                                    if (result.errors) {
                                        for (const key in result.errors) {
                                            if (this.errors.hasOwnProperty(key)) {
                                                this.errors[key] = result.errors[key][0];
                                            }
                                        }
                                    }
                                    return false;
                                }

                                return true;
                            } catch (err) {
                                console.error('Validation error:', err);
                                return false;
                            } finally {
                                this.isValidating = false;
                            }
                        },

                        async goNext() {
                            // Jalankan validasi frontend terlebih dahulu
                            if (this.currentStep === 1) {
                                this.validateNama();
                                this.validateNim();
                                this.validateEmail();
                                this.validateNoHp();
                                this.validateAngkatan();

                                // if (!this.form.angkatan) this.errors.angkatan = 'Angkatan wajib dipilih.';
                                // else this.errors.angkatan = '';

                                if (this.errors.nama || this.errors.nim || this.errors.email || this.errors.no_hp || this.errors
                                    .angkatan) {
                                    return;
                                }

                                // Cek ke server untuk format email DNS / duplikasi NIM awal
                                const isValid = await this.serverValidate();
                                if (!isValid) return;
                            }

                            if (this.currentStep === 2) {
                                if (!this.form.divisi_id) {
                                    this.errors.divisi_id = 'Pilih divisi terlebih dahulu.';
                                    return;
                                }

                                // Cek ke server untuk duplikasi NIM pada divisi terpilih & keaktifan divisi
                                const isValid = await this.serverValidate();
                                if (!isValid) return;

                                const hasPertanyaan = this.selectedDivisi?.pertanyaan?.length > 0;
                                if (!hasPertanyaan) {
                                    // Langsung submit jika tidak ada pertanyaan
                                    this.submitForm({
                                        target: this.$el.querySelector('form')
                                    });
                                    return;
                                }
                            }

                            if (this.currentStep < 3) {
                                this.currentStep++;
                            }
                        },

                        submitForm(e) {
                            let hasError = false;

                            // Validasi semua jawaban di Step 3 sebelum benar-benar mengirim form
                            if (this.selectedDivisi && this.selectedDivisi.pertanyaan) {
                                this.selectedDivisi.pertanyaan.forEach(p => {
                                    this.validateJawaban(p.id);
                                    if (this.errors.jawaban[p.id]) {
                                        hasError = true;
                                    }
                                });
                            }

                            // Jika ada jawaban yang kosong, jangan teruskan proses kirim form
                            if (hasError) {
                                return;
                            }

                            this.isSubmitting = true;
                            e.target.submit(); // Lanjutkan mengirim data ke server
                        },

                        init() {
                            @if ($errors->has('divisi_id') || $errors->has('nim'))
                                this.currentStep = 2;
                            @elseif ($errors->any())
                                this.currentStep = 1;
                            @endif

                            @if (old('divisi_id'))
                                this.form.divisi_id = {{ old('divisi_id') }};
                            @endif
                        }
                    }
                }
            </script>
        @endpush
    @endif
@endsection
