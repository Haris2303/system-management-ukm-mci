<?php $__env->startSection('title', $post->judul . ' — UKM MCI'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* ── Prose article styling ── */
        .prose-article {
            font-size: 16.5px;
            line-height: 1.85;
            color: #334155;
        }

        .prose-article h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e293b;
            margin: 2.5rem 0 1rem;
            padding-bottom: .5rem;
            border-bottom: 2px solid #e8efff;
        }

        .prose-article h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 2rem 0 .75rem;
        }

        .prose-article p {
            margin-bottom: 1.4rem;
        }

        .prose-article ul,
        .prose-article ol {
            margin: 1rem 0 1.4rem 1.5rem;
        }

        .prose-article li {
            margin-bottom: .4rem;
        }

        .prose-article ul li::marker {
            color: #1a4ff5;
        }

        .prose-article ol li::marker {
            color: #1a4ff5;
            font-weight: 700;
        }

        .prose-article strong {
            font-weight: 700;
            color: #1e293b;
        }

        .prose-article em {
            font-style: italic;
            color: #475569;
        }

        .prose-article blockquote {
            border-left: 4px solid #1a4ff5;
            padding: 1rem 1.5rem;
            background: #f0f4ff;
            border-radius: 0 12px 12px 0;
            margin: 1.5rem 0;
            font-style: italic;
            color: #475569;
        }

        .prose-article a {
            color: #1a4ff5;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .prose-article a:hover {
            color: #1340e1;
        }

        .prose-article img {
            border-radius: 16px;
            margin: 1.5rem 0;
            max-width: 100%;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        }

        .prose-article code {
            background: #e8efff;
            color: #1340e1;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: .875em;
            font-family: monospace;
        }

        .prose-article pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 1.25rem 1.5rem;
            border-radius: 14px;
            overflow-x: auto;
            margin: 1.5rem 0;
            font-size: .875rem;
        }

        .prose-article pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="pt-24 pb-4 bg-white border-b border-slate-100">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-sm text-slate-400">
                <a href="<?php echo e(route('landing')); ?>" class="hover:text-brand-600 transition-colors">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="<?php echo e(route('berita.index')); ?>" class="hover:text-brand-600 transition-colors">Berita</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-slate-600 font-medium truncate max-w-xs"><?php echo e(Str::limit($post->judul, 40)); ?></span>
            </nav>
        </div>
    </div>

    
    <article>
        <header class="py-12 bg-white">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <a href="<?php echo e(route('berita.index', ['kategori' => $post->kategori])); ?>"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-bold
               <?php switch($post->kategori):
                   case ('Prestasi'): ?>  bg-amber-50 text-amber-700 border border-amber-200 <?php break; ?>
                   <?php case ('Kegiatan'): ?>  bg-emerald-50 text-emerald-700 border border-emerald-200 <?php break; ?>
                   <?php case ('Pengumuman'): ?> bg-rose-50 text-rose-700 border border-rose-200 <?php break; ?>
                   <?php default: ?>           bg-brand-50 text-brand-700 border border-brand-200
               <?php endswitch; ?>">
                        <?php echo $post->getKategoriEmoji(); ?> <?php echo e($post->kategori); ?>

                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->is_featured): ?>
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <i class="fa-solid fa-star"></i> Featured
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="text-slate-400 text-sm"><?php echo e($post->readTime()); ?></span>
                    <span class="text-slate-400 text-sm flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <?php echo e(number_format($post->views)); ?> kali dilihat
                    </span>
                </div>

                
                <h1 class="font-display text-3xl lg:text-5xl font-bold text-slate-900 leading-tight mb-6">
                    <?php echo e($post->judul); ?>

                </h1>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->ringkasan): ?>
                    <p class="text-lg text-slate-500 font-light leading-relaxed mb-6 border-l-4 border-brand-200 pl-5">
                        <?php echo e($post->ringkasan); ?>

                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="flex items-center justify-between flex-wrap gap-4 py-5 border-y border-slate-100">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center font-bold text-brand-600">
                            <?php echo e(substr($post->author->name ?? 'A', 0, 1)); ?>

                        </div>
                        <div>
                            <div class="font-semibold text-slate-800 text-sm"><?php echo e($post->author->name ?? 'Admin MCI'); ?>

                            </div>
                            <div class="text-slate-400 text-xs">
                                <?php echo e($post->published_at->translatedFormat('l, d F Y · H:i')); ?> WIT</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-medium">Bagikan:</span>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo e(urlencode($post->judul)); ?>&url=<?php echo e(urlencode(request()->url())); ?>"
                            target="_blank" rel="noopener"
                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-[#1da1f2] hover:text-white flex items-center justify-center text-slate-500 transition-all text-sm">𝕏</a>
                        <a href="https://wa.me/?text=<?php echo e(urlencode($post->judul . ' ' . request()->url())); ?>"
                            target="_blank" rel="noopener"
                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-[#25d366] hover:text-white flex items-center justify-center text-slate-500 transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                        </a>
                        <button
                            onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('Link disalin!'))"
                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-brand-600 hover:text-white flex items-center justify-center text-slate-500 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->thumbnail): ?>
            <div class="max-w-4xl mx-auto px-6 lg:px-8 mb-10">
                <div class="rounded-3xl overflow-hidden max-h-[480px]">
                    <img src="<?php echo e(asset('storage/' . $post->thumbnail)); ?>" alt="<?php echo e($post->judul); ?>"
                        class="w-full object-cover">
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="max-w-4xl mx-auto px-6 lg:px-8 pb-16">
            <div class="grid lg:grid-cols-[1fr_260px] gap-12 items-start">

                
                <div class="prose-article min-w-0">
                    <?php echo $post->konten; ?>


                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->getTags()): ?>
                        <div class="flex flex-wrap gap-2 mt-10 pt-8 border-t border-slate-100">
                            <span class="text-sm text-slate-400 font-medium">Tag:</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $post->getTags(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <a href="<?php echo e(route('berita.index', ['tag' => trim($tag)])); ?>"
                                    class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-sm hover:bg-brand-50 hover:text-brand-600 transition-colors">
                                    #<?php echo e(trim($tag)); ?>

                                </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <aside class="hidden lg:block sticky top-28">
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Tentang Artikel</div>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-slate-400 text-xs mb-0.5">Kategori</dt>
                                <dd class="font-semibold text-slate-700"><?php echo $post->getKategoriEmoji(); ?>

                                    <?php echo e($post->kategori); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs mb-0.5">Diterbitkan</dt>
                                <dd class="font-semibold text-slate-700">
                                    <?php echo e($post->published_at->translatedFormat('d F Y')); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs mb-0.5">Penulis</dt>
                                <dd class="font-semibold text-slate-700"><?php echo e($post->author->name ?? 'Admin MCI'); ?></dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs mb-0.5">Dibaca</dt>
                                <dd class="font-semibold text-slate-700"><?php echo e(number_format($post->views)); ?> kali</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-xs mb-0.5">Estimasi Baca</dt>
                                <dd class="font-semibold text-slate-700"><?php echo e($post->readTime()); ?></dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-4">
                        <a href="<?php echo e(route('berita.index')); ?>"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl border-2 border-slate-200 text-slate-600 font-semibold text-sm hover:border-brand-300 hover:text-brand-600 transition-all">
                            ← Semua Berita
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </article>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->isNotEmpty()): ?>
        <section class="py-16 bg-slate-50 border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1 h-6 bg-brand-600 rounded-full"></div>
                    <h2 class="font-display text-xl font-bold text-slate-900">Berita Terkait</h2>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('berita.show', $rel->slug)); ?>"
                            class="group bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-1 flex flex-col">
                            <div class="h-40 overflow-hidden bg-brand-50">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rel->thumbnail): ?>
                                    <img src="<?php echo e(asset('storage/' . $rel->thumbnail)); ?>" alt="<?php echo e($rel->judul); ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                    <div
                                        class="w-full h-full flex items-center justify-center text-4xl bg-gradient-to-br from-brand-100 to-brand-200">
                                        <?php echo $rel->getKategoriEmoji(); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3
                                    class="font-display font-bold text-slate-800 text-sm line-clamp-2 group-hover:text-brand-600 transition-colors mb-2 flex-1">
                                    <?php echo e($rel->judul); ?>

                                </h3>
                                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <?php echo e($rel->published_at->translatedFormat('d M Y')); ?>

                                </div>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/landing/berita/show.blade.php ENDPATH**/ ?>