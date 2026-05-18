<?php $__env->startSection('title', 'UKM MCI — Daftar Pengurus'); ?>

<?php $__env->startSection('content'); ?>

    

    <section id="daftar" class="py-28 bg-white relative overflow-hidden">

        <div
            class="absolute top-0 left-0 w-96 h-96 bg-brand-50 rounded-full -translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute bottom-0 right-0 w-72 h-72 bg-accent/10 rounded-full translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none">
        </div>

        
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-start">

                
                <div class="space-y-8 reveal lg:sticky lg:top-28">
                    <div>
                        <span
                            class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100 mb-4">
                            <?php echo e($openRecruitment
                                ? $openRecruitment->judul . ($openRecruitment->gelombang ? ' · ' . $openRecruitment->gelombang : '')
                                : 'Close Recruitment'); ?>

                        </span>
                        <h2 class="font-display text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                            Mulai Perjalanan
                            <span class="gradient-text"> Teknologimu</span>
                        </h2>
                        <p class="text-slate-500 mt-4 font-light text-lg leading-relaxed">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openRecruitment?->deskripsi): ?>
                                <?php echo e($openRecruitment->deskripsi); ?>

                            <?php else: ?>
                                Bergabunglah bersama ratusan mahasiswa yang telah memilih MCI sebagai rumah berkembang di
                                dunia
                                teknologi.
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openRecruitment): ?>
                            <div class="flex items-center gap-4 mt-4 text-sm text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-slate-400"></i> Ditutup <strong
                                        class="text-slate-700"><?php echo e($openRecruitment->waktu_selesai->translatedFormat('d M Y, H:i')); ?></strong>
                                </span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                        ['fa-solid fa-rocket',    'Akses Workshop Eksklusif', 'Pelatihan coding, design, dan tech leadership setiap bulan'],
                        ['fa-solid fa-trophy',    'Ikut Kompetisi Bergengsi', 'Dukungan penuh untuk lomba nasional & internasional'],
                        ['fa-solid fa-briefcase', 'Bangun Portofolio Nyata',  'Project kolaboratif yang siap masuk CV Anda'],
                        ['fa-solid fa-globe',     'Jaringan Alumni Luas',     'Terhubung dengan alumni di perusahaan tech terkemuka'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$iconClass, $title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div
                                class="flex gap-4 items-start p-4 rounded-2xl hover:bg-brand-50/50 transition-colors group">
                                <div
                                    class="w-11 h-11 rounded-xl bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center text-brand-500 flex-shrink-0 transition-colors">
                                    <i class="<?php echo e($iconClass); ?>"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800 text-sm"><?php echo e($title); ?></div>
                                    <div class="text-slate-500 text-sm mt-0.5 font-light"><?php echo e($desc); ?></div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($divisis->count() > 0): ?>
                        <div>
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Divisi Tersedia</p>
                            <div class="flex flex-wrap gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $divisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $div): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold border border-brand-100">
                                        <i class="<?php echo e($div->icon); ?> fa-xs"></i> <?php echo e($div->nama); ?>

                                    </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>

                
                <div class="reveal reveal-delay-1">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$openRecruitment): ?>
                        
                        <div
                            class="flex flex-col items-center justify-center text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-12 gap-5 h-full min-h-[420px]">
                            <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center text-4xl text-slate-400">
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
                    <?php elseif($divisis->count() === 0): ?>
                        
                        <div
                            class="flex flex-col items-center justify-center text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-12 gap-5 h-full min-h-[420px]">
                            <div class="w-20 h-20 rounded-2xl bg-amber-50 flex items-center justify-center text-4xl text-amber-400">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h3 class="font-display text-xl font-bold text-slate-700 mb-2">
                                    Segera Dibuka
                                </h3>
                                <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                    Periode <strong class="text-slate-600"><?php echo e($openRecruitment->judul); ?></strong> sedang
                                    disiapkan.
                                    Divisi akan segera aktif, cek kembali dalam beberapa saat.
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('sukses')): ?>
                            <div
                                class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-3">
                                <i class="fa-solid fa-circle-check text-2xl text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <div>
                                    <div class="font-semibold text-emerald-800">Pendaftaran Berhasil!</div>
                                    <div class="text-emerald-600 text-sm mt-1 leading-relaxed"><?php echo e(session('sukses')); ?></div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                            <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5">
                                <div class="font-semibold text-red-800 mb-2 flex items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Mohon periksa kembali:
                                </div>
                                <ul class="space-y-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <li class="text-red-600 text-sm flex items-center gap-2">
                                            <span
                                                class="w-1 h-1 rounded-full bg-red-400 flex-shrink-0"></span><?php echo e($error); ?>

                                        </li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php
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
                        ?>

                        <div x-data="formPendaftaran(<?php echo e($divisiJson); ?>)" x-cloak
                            class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100 p-8 lg:p-10">

                            
                            <div class="mb-7">
                                <h3 class="font-display text-2xl font-bold text-slate-900">Formulir Pendaftaran</h3>
                                <p class="text-slate-400 text-sm mt-1">Isi semua data dengan lengkap dan benar.</p>

                                
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

                            <form action="<?php echo e(route('daftar')); ?>" method="POST" @submit="submitForm">
                                <?php echo csrf_field(); ?>

                                
                                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0">

                                    <div class="space-y-4">

                                        
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                Nama Lengkap <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text" name="nama" x-model="form.nama"
                                                placeholder="Sesuai KTM/KTP"
                                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                value="<?php echo e(old('nama')); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="text-red-500 text-xs mt-1.5"><?php echo e($message); ?></p>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        
                                        <div class="grid sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                    NIM <span class="text-red-400">*</span>
                                                </label>
                                                <input type="text" name="nim" x-model="form.nim"
                                                    placeholder="Nomor Induk Mahasiswa"
                                                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 <?php $__errorArgs = ['nim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    value="<?php echo e(old('nim')); ?>">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <p class="text-red-500 text-xs mt-1.5"><?php echo e($message); ?></p>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                    Angkatan <span class="text-red-400">*</span>
                                                </label>
                                                <select name="angkatan" x-model="form.angkatan"
                                                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:border-brand-400 focus:bg-white transition-all duration-200 <?php $__errorArgs = ['angkatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                    <option value="">Pilih angkatan</option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['2025', '2024', '2023', '2022', '2021', '2020']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                        <option value="<?php echo e($a); ?>"
                                                            <?php echo e(old('angkatan') === $a ? 'selected' : ''); ?>>
                                                            <?php echo e($a); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </select>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['angkatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <p class="text-red-500 text-xs mt-1.5"><?php echo e($message); ?></p>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                Email Aktif <span class="text-red-400">*</span>
                                            </label>
                                            <input type="email" name="email" x-model="form.email"
                                                placeholder="contoh@email.com"
                                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                value="<?php echo e(old('email')); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="text-red-500 text-xs mt-1.5"><?php echo e($message); ?></p>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                No. HP / WhatsApp <span class="text-red-400">*</span>
                                            </label>
                                            <input type="tel" name="no_hp" x-model="form.no_hp"
                                                placeholder="08xxxxxxxxxx"
                                                class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 <?php $__errorArgs = ['no_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 bg-red-50 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                value="<?php echo e(old('no_hp')); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['no_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="text-red-500 text-xs mt-1.5"><?php echo e($message); ?></p>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>

                                    
                                    <button type="button" @click="goNext()"
                                        class="w-full mt-6 flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5 transition-all duration-200">
                                        Lanjut: Pilih Divisi
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </button>
                                </div>

                                
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
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['divisi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-xs mb-3"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    
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

                                                <span class="text-xl mb-2 block text-brand-500"><i :class="div.icon"></i></span>
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
                                        <i class="fa-regular fa-face-sad-tear mr-1"></i> Belum ada divisi yang membuka pendaftaran saat ini.
                                    </div>

                                    
                                    <div x-show="form.divisi_id && selectedDivisi?.pertanyaan?.length === 0"
                                        class="mt-3 flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
                                        <i class="fa-solid fa-circle-info text-lg flex-shrink-0 mt-0.5"></i>
                                        <span>
                                            Divisi ini tidak memiliki pertanyaan seleksi.
                                            Klik <strong>Kirim Pendaftaran</strong> untuk langsung mendaftar tanpa mengisi
                                            jawaban.
                                        </span>
                                    </div>

                                    
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

                                                
                                                
                                                <textarea :name="'jawaban[' + pertanyaan.id + ']'" x-model="form.jawaban[pertanyaan.id]" rows="4"
                                                    placeholder="Tulis jawaban Anda di sini..."
                                                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm placeholder:text-slate-300 focus:border-brand-400 focus:bg-white transition-all duration-200 resize-none leading-relaxed"></textarea>

                                                
                                                <p class="text-xs text-slate-400 mt-1 text-right"
                                                    x-text="(form.jawaban[pertanyaan.id] || '').length + ' karakter'"></p>
                                            </div>
                                        </template>
                                    </div>

                                    
                                    <p class="text-xs text-slate-400 leading-relaxed mt-5">
                                        <i class="fa-solid fa-lock mr-1 text-slate-400"></i> Data Anda disimpan secara aman. Jawaban akan dinilai oleh Ketua Divisi terkait.
                                    </p>

                                    
                                    <div class="flex gap-3 mt-6">
                                        <button type="button" @click="currentStep = 2"
                                            class="flex-1 px-6 py-4 rounded-2xl border-2 border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all">
                                            ← Kembali
                                        </button>
                                        <button type="submit" :disabled="isSubmitting"
                                            class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-brand-600 text-white font-bold text-sm hover:bg-brand-700 shadow-lg shadow-brand-200 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:translate-y-0">
                                            <span x-show="!isSubmitting">Kirim Pendaftaran <i class="fa-solid fa-paper-plane ml-0.5"></i></span>
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

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    

                </div>

            </div>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($divisis->count() > 0): ?>
        <?php $__env->startPush('scripts'); ?>
            <script>
                function formPendaftaran(divisis) {
                    return {
                        divisis: divisis,
                        currentStep: 1,
                        isSubmitting: false,

                        // Definisi nama step
                        steps: ['Data Diri', 'Pilih Divisi', 'Pertanyaan'],

                        form: {
                            nama: '<?php echo e(old('nama')); ?>',
                            nim: '<?php echo e(old('nim')); ?>',
                            angkatan: '<?php echo e(old('angkatan')); ?>',
                            email: '<?php echo e(old('email')); ?>',
                            no_hp: '<?php echo e(old('no_hp')); ?>',
                            divisi_id: <?php echo e(old('divisi_id') ? old('divisi_id') : 'null'); ?>,
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
                            <?php if($errors->has('divisi_id') || $errors->has('nim')): ?>
                                this.currentStep = 2;
                            <?php elseif($errors->any()): ?>
                                this.currentStep = 1;
                            <?php endif; ?>

                            // Restore divisi_id dari old input jika ada
                            <?php if(old('divisi_id')): ?>
                                this.form.divisi_id = <?php echo e(old('divisi_id')); ?>;
                            <?php endif; ?>
                        }
                    }
                }
            </script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/landing/daftar/index.blade.php ENDPATH**/ ?>