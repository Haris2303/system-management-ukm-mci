<?php $__env->startSection('title', 'Galeri Kegiatan — UKM MCI'); ?>

<?php $__env->startSection('content'); ?>

    
    <section class="relative pt-32 pb-20 bg-white overflow-hidden dot-grid">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-100/60 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-sm font-semibold mb-5">
                <i class="fa-regular fa-images"></i> Dokumentasi Kegiatan UKM MCI
            </div>
            <h1 class="font-display text-4xl lg:text-6xl font-bold text-slate-900 leading-tight mb-4">
                Galeri <span class="gradient-text">Kegiatan</span>
            </h1>
            <p class="text-slate-500 text-lg font-light max-w-xl mx-auto">
                Dokumentasi kegiatan, lomba, workshop, dan momen berharga bersama UKM MCI.
            </p>
        </div>
    </section>

    
    <div class="sticky top-16 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center gap-2 overflow-x-auto py-3 scrollbar-none">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isActive = $kategori === $kat || ($kat === 'semua' && !$kategori);
                        $iconClass = match ($kat) {
                            'Kegiatan'  => 'fa-regular fa-calendar',
                            'Prestasi'  => 'fa-solid fa-trophy',
                            'Rapat'     => 'fa-solid fa-users',
                            'Pelatihan' => 'fa-solid fa-laptop-code',
                            default     => 'fa-regular fa-images',
                        };
                    ?>
                    <a href="<?php echo e(route('galeri.index', $kat !== 'semua' ? ['kategori' => $kat] : [])); ?>"
                        class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
                            <?php echo e($isActive ? 'bg-brand-600 text-white shadow-md shadow-brand-200' : 'text-slate-600 hover:bg-brand-50 hover:text-brand-600'); ?>">
                        <i class="<?php echo e($iconClass); ?>"></i>
                        <span><?php echo e(ucfirst($kat)); ?></span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>

    
    <section class="py-14 bg-slate-50/60">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kategori && $kategori !== 'semua'): ?>
                <div class="flex items-center gap-3 mb-6">
                    <p class="text-slate-600 text-sm">
                        <span class="font-semibold"><?php echo e($galleries->total()); ?></span> foto
                        dalam kategori <span class="font-semibold text-brand-600"><?php echo e($kategori); ?></span>
                    </p>
                    <a href="<?php echo e(route('galeri.index')); ?>"
                        class="text-sm text-slate-400 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-xmark mr-0.5"></i> Reset
                    </a>
                </div>
            <?php else: ?>
                <p class="text-slate-400 text-sm mb-6">
                    <span class="font-semibold text-slate-600"><?php echo e($galleries->total()); ?></span> foto
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($galleries->isNotEmpty()): ?>
                <div class="columns-2 md:columns-3 lg:columns-4 gap-3 lg:gap-4 space-y-3 lg:space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $galleries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="break-inside-avoid rounded-2xl overflow-hidden relative group cursor-pointer"
                            onclick="openLightbox(
                                '<?php echo e(asset('storage/' . $item->foto)); ?>',
                                '<?php echo e(addslashes($item->judul)); ?>',
                                '<?php echo e(addslashes($item->kategori)); ?>',
                                '<?php echo e(addslashes($item->deskripsi ?? '')); ?>'
                            )">
                            <img src="<?php echo e(asset('storage/' . $item->foto)); ?>"
                                alt="<?php echo e($item->judul); ?>"
                                class="w-full object-cover block">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-brand-900/80 via-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                                <div>
                                    <div class="text-[10px] font-bold text-brand-300 uppercase tracking-widest mb-0.5">
                                        <?php echo e($item->kategori); ?>

                                    </div>
                                    <div class="text-white font-semibold text-sm leading-tight">
                                        <?php echo e($item->judul); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($galleries->hasPages()): ?>
                    <div class="mt-12 flex justify-center">
                        <?php echo e($galleries->links('vendor.pagination.simple-tailwind')); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <div class="text-center py-24">
                    <div class="text-6xl mb-4 text-slate-300"><i class="fa-regular fa-images"></i></div>
                    <h3 class="font-display text-xl font-bold text-slate-700 mb-2">Belum Ada Foto</h3>
                    <p class="text-slate-400 mb-6">Foto kegiatan akan segera ditambahkan.</p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kategori && $kategori !== 'semua'): ?>
                        <a href="<?php echo e(route('galeri.index')); ?>"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold text-sm hover:bg-brand-700 transition-colors">
                            Lihat Semua Foto
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <div id="lightbox"
        class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-sm hidden items-center justify-center p-4"
        onclick="closeLightbox()">
        <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
            <button onclick="closeLightbox()"
                class="absolute -top-12 right-0 text-white/70 hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="lightbox-img" src="" alt="" class="w-full max-h-[80vh] object-contain rounded-2xl">
            <div class="mt-4 text-center">
                <p id="lightbox-kategori" class="text-brand-400 text-xs font-bold uppercase tracking-widest mb-1"></p>
                <p id="lightbox-caption" class="text-white font-semibold text-base"></p>
                <p id="lightbox-desc" class="text-white/60 text-sm mt-1"></p>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function openLightbox(src, caption, kategori, desc) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-caption').textContent = caption;
            document.getElementById('lightbox-kategori').textContent = kategori;
            document.getElementById('lightbox-desc').textContent = desc;
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/landing/galeri/index.blade.php ENDPATH**/ ?>