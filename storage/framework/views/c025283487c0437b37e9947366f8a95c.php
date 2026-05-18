<?php $__env->startSection('title', 'Berita & Kegiatan — UKM MCI'); ?>

<?php $__env->startSection('content'); ?>

    
    <section class="relative pt-32 pb-20 bg-white overflow-hidden dot-grid">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-100/60 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-sm font-semibold mb-5">
                <i class="fa-regular fa-newspaper"></i> Pusat Informasi UKM MCI
            </div>
            <h1 class="font-display text-4xl lg:text-6xl font-bold text-slate-900 leading-tight mb-4">
                Berita <span class="gradient-text">&amp; Kegiatan</span>
            </h1>
            <p class="text-slate-500 text-lg font-light max-w-xl mx-auto">
                Ikuti perkembangan terbaru, dokumentasi kegiatan, dan pencapaian membanggakan dari UKM MCI.
            </p>

            
            <form method="GET" action="<?php echo e(route('berita.index')); ?>" class="max-w-lg mx-auto mt-8">
                <div
                    class="flex items-center gap-3 bg-white border-2 border-slate-200 rounded-2xl px-5 py-3.5 focus-within:border-brand-400 focus-within:shadow-lg focus-within:shadow-brand-50 transition-all">
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="q" value="<?php echo e($search); ?>"
                        placeholder="Cari berita, kegiatan, atau prestasi…"
                        class="flex-1 bg-transparent text-slate-700 placeholder:text-slate-400 text-sm outline-none font-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kategori && $kategori !== 'semua'): ?>
                        <input type="hidden" name="kategori" value="<?php echo e($kategori); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tag): ?>
                        <input type="hidden" name="tag" value="<?php echo e($tag); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button type="submit"
                        class="px-4 py-1.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition-colors">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </section>

    
    <div class="sticky top-16 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center gap-2 overflow-x-auto py-3 scrollbar-none">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isActive = $kategori === $kat || ($kat === 'semua' && !$kategori);
                        $iconClass = match ($kat) {
                            'Berita' => 'fa-regular fa-newspaper',
                            'Kegiatan' => 'fa-regular fa-calendar',
                            'Prestasi' => 'fa-solid fa-trophy',
                            'Pengumuman' => 'fa-solid fa-bullhorn',
                            default => 'fa-solid fa-folder-open',
                        };
                    ?>
                    <a href="<?php echo e(route('berita.index', array_filter(['kategori' => $kat === 'semua' ? null : $kat, 'q' => $search]))); ?>"
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

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featured && !$search && !$tag && (!$kategori || $kategori === 'semua') && $posts->currentPage() === 1): ?>
                <div class="mb-10">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-1 h-5 rounded-full bg-brand-600"></div>
                        <span class="text-sm font-bold text-slate-700">Berita Utama</span>
                    </div>
                    <a href="<?php echo e(route('berita.show', $featured->slug)); ?>"
                        class="group grid lg:grid-cols-2 gap-0 rounded-3xl overflow-hidden bg-white border border-slate-100 hover:shadow-2xl hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-1">
                        <div class="h-64 lg:h-auto overflow-hidden bg-brand-100 relative">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featured->thumbnail): ?>
                                <img src="<?php echo e(asset('storage/' . $featured->thumbnail)); ?>" alt="<?php echo e($featured->judul); ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <?php else: ?>
                                <div
                                    class="w-full h-full bg-gradient-to-br from-brand-400 to-brand-700 flex items-center justify-center text-7xl">
                                    <?php echo $featured->getKategoriEmoji(); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="absolute top-4 left-4">
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-amber-400 text-white text-xs font-bold shadow">
                                    <i class="fa-solid fa-star fa-xs"></i> Featured</span>
                            </div>
                        </div>
                        <div class="p-8 lg:p-10 flex flex-col justify-center gap-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-brand-50 text-brand-600">
                                    <?php echo $featured->getKategoriEmoji(); ?> <?php echo e($featured->kategori); ?>

                                </span>
                                <span class="text-slate-400 text-xs"><?php echo e($featured->readTime()); ?></span>
                            </div>
                            <h2
                                class="font-display text-2xl lg:text-3xl font-bold text-slate-900 group-hover:text-brand-600 transition-colors leading-tight">
                                <?php echo e($featured->judul); ?>

                            </h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featured->ringkasan): ?>
                                <p class="text-slate-500 leading-relaxed line-clamp-3"><?php echo e($featured->ringkasan); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2 text-sm text-slate-500">
                                    <div
                                        class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center font-bold text-brand-600 text-xs">
                                        <?php echo e(substr($featured->author->name ?? 'A', 0, 1)); ?>

                                    </div>
                                    <span><?php echo e($featured->author->name ?? 'Admin MCI'); ?></span>
                                    <span class="text-slate-300">·</span>
                                    <span><?php echo e($featured->published_at->translatedFormat('d M Y')); ?></span>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1.5 text-brand-600 font-semibold text-sm group-hover:gap-2.5 transition-all">
                                    Baca
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || ($kategori && $kategori !== 'semua') || $tag): ?>
                <div class="flex items-center gap-3 mb-6">
                    <p class="text-slate-600 text-sm">
                        <span class="font-semibold"><?php echo e($posts->total()); ?></span> hasil
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                            untuk "<span class="font-semibold text-brand-600"><?php echo e($search); ?></span>"
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kategori && $kategori !== 'semua'): ?>
                            dalam kategori <span class="font-semibold text-brand-600"><?php echo e($kategori); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tag): ?>
                            dengan tag <span class="font-semibold text-brand-600">#<?php echo e($tag); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                    <a href="<?php echo e(route('berita.index')); ?>"
                        class="text-sm text-slate-400 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-xmark mr-0.5"></i> Reset
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isNotEmpty()): ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('berita.show', $post->slug)); ?>"
                            class="group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:border-brand-200 hover:shadow-xl hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-1 flex flex-col">

                            
                            <div class="h-48 overflow-hidden relative bg-brand-50 flex-shrink-0">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->thumbnail): ?>
                                    <img src="<?php echo e(asset('storage/' . $post->thumbnail)); ?>" alt="<?php echo e($post->judul); ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                    <div
                                        class="w-full h-full flex items-center justify-center text-5xl
                        <?php switch($post->kategori):
                            case ('Prestasi'): ?>  bg-gradient-to-br from-amber-100 to-amber-200 <?php break; ?>
                            <?php case ('Kegiatan'): ?>  bg-gradient-to-br from-emerald-100 to-emerald-200 <?php break; ?>
                            <?php case ('Pengumuman'): ?> bg-gradient-to-br from-rose-100 to-rose-200 <?php break; ?>
                            <?php default: ?>           bg-gradient-to-br from-brand-100 to-brand-200
                        <?php endswitch; ?>">
                                        <?php echo $post->getKategoriEmoji(); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                            <?php switch($post->kategori):
                                case ('Prestasi'): ?>  bg-amber-400 text-white <?php break; ?>
                                <?php case ('Kegiatan'): ?>  bg-emerald-500 text-white <?php break; ?>
                                <?php case ('Pengumuman'): ?> bg-rose-500 text-white <?php break; ?>
                                <?php default: ?>           bg-brand-600 text-white
                            <?php endswitch; ?>">
                                        <?php echo e($post->kategori); ?>

                                    </span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->is_featured): ?>
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-amber-400 text-white"><i
                                                class="fa-solid fa-star fa-xs"></i></span>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="p-5 flex flex-col flex-1">
                                <h3
                                    class="font-display font-bold text-slate-800 text-base leading-snug group-hover:text-brand-600 transition-colors line-clamp-2 mb-2">
                                    <?php echo e($post->judul); ?>

                                </h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->ringkasan): ?>
                                    <p class="text-slate-400 text-sm leading-relaxed line-clamp-2 mb-4 flex-1">
                                        <?php echo e($post->ringkasan); ?>

                                    </p>
                                <?php else: ?>
                                    <div class="flex-1"></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <div
                                    class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-100 mt-auto">
                                    <div class="flex items-center gap-1.5">
                                        <div
                                            class="w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center font-bold text-brand-600 text-[9px]">
                                            <?php echo e(substr($post->author->name ?? 'A', 0, 1)); ?>

                                        </div>
                                        <span><?php echo e(Str::words($post->author->name ?? 'Admin', 1, '')); ?></span>
                                        <span>·</span>
                                        <span><?php echo e($post->published_at->diffForHumans()); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <?php echo e(number_format($post->views)); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->hasPages()): ?>
                    <div class="mt-12 flex justify-center">
                        <?php echo e($posts->links('vendor.pagination.simple-tailwind')); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <div class="text-center py-24">
                    <div class="text-6xl mb-4 text-slate-300"><i class="fa-regular fa-envelope-open"></i></div>
                    <h3 class="font-display text-xl font-bold text-slate-700 mb-2">Tidak Ada Berita Ditemukan</h3>
                    <p class="text-slate-400 mb-6">Coba kata kunci lain atau hapus filter yang aktif.</p>
                    <a href="<?php echo e(route('berita.index')); ?>"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold text-sm hover:bg-brand-700 transition-colors">
                        Lihat Semua Berita
                    </a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/landing/berita/index.blade.php ENDPATH**/ ?>