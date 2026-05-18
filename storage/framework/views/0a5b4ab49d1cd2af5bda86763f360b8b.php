
<section id="berita" class="py-28 bg-white relative overflow-hidden">

    <div class="absolute top-0 right-0 w-80 h-80 bg-brand-50 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-canvas-soft rounded-full translate-y-1/2 -translate-x-1/3 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">

        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14 reveal">
            <div>
                
                <span
                    class="inline-block text-[10px] tracking-widest text-brand-700 uppercase bg-brand-200 px-3 py-1.5 rounded-full mb-4"
                    style="font-weight:400; letter-spacing:0.1px;">
                    Berita & Kegiatan
                </span>
                
                <h2 class="font-display text-4xl lg:text-5xl text-ink leading-tight"
                    style="font-weight:300; letter-spacing:-0.64px;">
                    Selalu Update
                    <span class="gradient-text">Bersama MCI</span>
                </h2>
                <p class="text-ink-mute mt-3 max-w-lg" style="font-weight:300;">
                    Ikuti perkembangan terbaru, hasil kegiatan, dan pencapaian membanggakan dari keluarga besar UKM MCI.
                </p>
            </div>
            
            <a href="<?php echo e(route('berita.index')); ?>"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-2 rounded-full border border-brand-600 text-brand-600 hover:bg-brand-600 hover:text-white transition-all duration-200"
                style="font-weight:400; font-size:14px; line-height:1.0;">
                Lihat Semua Berita
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isNotEmpty()): ?>

            
            <?php
                $featured = $posts->first();
                $rest = $posts->skip(1);
            ?>

            <div class="grid lg:grid-cols-5 gap-6 mb-6">

                
                <a href="<?php echo e(route('berita.show', $featured->slug)); ?>"
                    class="reveal lg:col-span-3 group relative rounded-xl overflow-hidden block bg-brand-900 min-h-96 flex flex-col justify-end shadow-xl shadow-brand-100 hover:shadow-2xl hover:shadow-brand-200 transition-all duration-300 hover:-translate-y-1">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featured->thumbnail): ?>
                        <img src="<?php echo e(asset('storage/' . $featured->thumbnail)); ?>" alt="<?php echo e($featured->judul); ?>"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-60">
                    <?php else: ?>
                        <div class="absolute inset-0 bg-brand-800 group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 opacity-10 dot-grid"></div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="absolute inset-0 bg-gradient-to-t from-brand-900 via-brand-900/60 to-transparent"></div>

                    <div class="relative z-10 p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs
                            <?php switch($featured->kategori):
                                case ('Prestasi'): ?>  bg-amber-400/20 text-amber-300 border border-amber-400/30 <?php break; ?>
                                <?php case ('Kegiatan'): ?>  bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 <?php break; ?>
                                <?php case ('Pengumuman'): ?> bg-rose-400/20 text-rose-300 border border-rose-400/30 <?php break; ?>
                                <?php default: ?>           bg-brand-400/20 text-brand-200 border border-brand-400/30
                            <?php endswitch; ?>"
                                style="font-weight:400;">
                                <?php echo $featured->getKategoriEmoji(); ?> <?php echo e($featured->kategori); ?>

                            </span>
                            <span class="text-white/50 text-xs"><?php echo e($featured->readTime()); ?></span>
                        </div>

                        <h3 class="text-2xl text-white leading-tight mb-3 group-hover:text-brand-200 transition-colors line-clamp-3"
                            style="font-weight:300; letter-spacing:-0.26px;">
                            <?php echo e($featured->judul); ?>

                        </h3>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featured->ringkasan): ?>
                            <p class="text-white/60 text-sm leading-relaxed line-clamp-2 mb-4" style="font-weight:300;">
                                <?php echo e($featured->ringkasan); ?>

                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-white/50 text-xs">
                                <span><?php echo e($featured->author->name ?? 'Admin MCI'); ?></span>
                                <span>·</span>
                                <span><?php echo e($featured->published_at->translatedFormat('d M Y')); ?></span>
                            </div>
                            <span class="w-9 h-9 rounded-lg bg-white/10 group-hover:bg-brand-600 flex items-center justify-center transition-colors duration-200">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>

                
                <div class="lg:col-span-2 flex flex-col gap-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rest->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('berita.show', $post->slug)); ?>"
                            class="reveal reveal-delay-<?php echo e($loop->index + 1); ?> group flex gap-4 p-5 rounded-xl bg-white border border-hairline hover:border-brand-300 hover:shadow-lg hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-0.5 flex-1">

                            <div class="w-24 h-24 rounded-lg overflow-hidden shrink-0 bg-brand-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->thumbnail): ?>
                                    <img src="<?php echo e(asset('storage/' . $post->thumbnail)); ?>" alt="<?php echo e($post->judul); ?>"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full bg-brand-200 flex items-center justify-center text-2xl">
                                        <?php echo $post->getKategoriEmoji(); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="flex flex-col justify-between flex-1 min-w-0">
                                <div>
                                    <span
                                        class="inline-block text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-full mb-2
                                <?php switch($post->kategori):
                                    case ('Prestasi'): ?>  bg-amber-50 text-amber-600 <?php break; ?>
                                    <?php case ('Kegiatan'): ?>  bg-emerald-50 text-emerald-600 <?php break; ?>
                                    <?php case ('Pengumuman'): ?> bg-rose-50 text-rose-600 <?php break; ?>
                                    <?php default: ?>           bg-brand-50 text-brand-700
                                <?php endswitch; ?>"
                                        style="font-weight:400;">
                                        <?php echo e($post->kategori); ?>

                                    </span>
                                    <h3 class="text-ink text-sm leading-snug group-hover:text-brand-600 transition-colors line-clamp-2"
                                        style="font-weight:300;">
                                        <?php echo e($post->judul); ?>

                                    </h3>
                                </div>
                                <div class="flex items-center gap-2 text-ink-mute text-xs mt-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <?php echo e($post->published_at->translatedFormat('d M Y')); ?>

                                    <span>·</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <?php echo e(number_format($post->views)); ?>

                                </div>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rest->count() > 2): ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rest->skip(2)->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('berita.show', $post->slug)); ?>"
                            class="reveal reveal-delay-<?php echo e(min($loop->index + 1, 4)); ?> group bg-white rounded-xl border border-hairline overflow-hidden hover:border-brand-300 hover:shadow-lg hover:shadow-brand-50 transition-all duration-300 hover:-translate-y-1 flex flex-col">

                            <div class="h-44 overflow-hidden bg-brand-50 relative">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->thumbnail): ?>
                                    <img src="<?php echo e(asset('storage/' . $post->thumbnail)); ?>" alt="<?php echo e($post->judul); ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                    <div
                                        class="w-full h-full bg-gradient-to-br
                        <?php switch($post->kategori):
                            case ('Prestasi'): ?>  from-amber-100 to-amber-200 <?php break; ?>
                            <?php case ('Kegiatan'): ?>  from-emerald-100 to-emerald-200 <?php break; ?>
                            <?php case ('Pengumuman'): ?> from-rose-100 to-rose-200 <?php break; ?>
                            <?php default: ?>           from-brand-100 to-brand-200
                        <?php endswitch; ?>
                        flex items-center justify-center text-4xl">
                                        <?php echo $post->getKategoriEmoji(); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="inline-block text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full backdrop-blur-sm
                            <?php switch($post->kategori):
                                case ('Prestasi'): ?>  bg-amber-400/90 text-white <?php break; ?>
                                <?php case ('Kegiatan'): ?>  bg-emerald-500/90 text-white <?php break; ?>
                                <?php case ('Pengumuman'): ?> bg-rose-500/90 text-white <?php break; ?>
                                <?php default: ?>           bg-brand-600/90 text-white
                            <?php endswitch; ?>"
                                        style="font-weight:400;">
                                        <?php echo e($post->kategori); ?>

                                    </span>
                                </div>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="text-ink text-base leading-snug group-hover:text-brand-600 transition-colors line-clamp-2 mb-2 flex-1"
                                    style="font-weight:300; letter-spacing:-0.2px;">
                                    <?php echo e($post->judul); ?>

                                </h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->ringkasan): ?>
                                    <p class="text-ink-mute text-xs leading-relaxed line-clamp-2 mb-4" style="font-weight:300;">
                                        <?php echo e($post->ringkasan); ?>

                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="flex items-center justify-between text-xs text-ink-mute pt-3 border-t border-hairline mt-auto">
                                    <span style="font-feature-settings:'tnum';"><?php echo e($post->published_at->translatedFormat('d M Y')); ?></span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?php echo e($post->readTime()); ?>

                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php else: ?>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    ['fa-regular fa-newspaper', 'Berita',   'Selamat Datang di UKM MCI',   'Berita pertama kami akan segera hadir. Pantau terus halaman ini!'],
                    ['fa-solid fa-trophy',      'Prestasi', 'Juara 1 Hackathon Nasional',   'Tim MCI berhasil meraih podium pertama dalam kompetisi bergengsi.'],
                    ['fa-regular fa-calendar',  'Kegiatan', 'Workshop Web Development',     'Kegiatan workshop intensif untuk meningkatkan skill anggota baru.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$iconClass, $kat, $judul, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="reveal reveal-delay-<?php echo e($loop->index + 1); ?> rounded-xl border-2 border-dashed border-hairline overflow-hidden opacity-60">
                        <div class="h-40 bg-canvas-soft flex items-center justify-center text-5xl text-ink-mute">
                            <i class="<?php echo e($iconClass); ?>"></i>
                        </div>
                        <div class="p-5">
                            <span class="text-[10px] uppercase tracking-wider text-ink-mute" style="font-weight:400;"><?php echo e($kat); ?></span>
                            <h3 class="text-ink text-base mt-1 mb-2" style="font-weight:300;"><?php echo e($judul); ?></h3>
                            <p class="text-ink-mute text-sm" style="font-weight:300;"><?php echo e($desc); ?></p>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            <p class="text-center text-ink-mute text-sm mt-8 reveal" style="font-weight:300;">
                * Berita akan muncul setelah admin mempublikasikan konten.
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->count() >= 4): ?>
            <div class="text-center mt-12 reveal">
                <a href="<?php echo e(route('berita.index')); ?>"
                    class="inline-flex items-center gap-2.5 px-8 py-3 rounded-full border border-hairline text-ink-mute hover:border-brand-600 hover:text-brand-600 transition-all duration-200 hover:-translate-y-0.5"
                    style="font-weight:400; font-size:14px; line-height:1.0;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    Baca Semua Berita & Kegiatan
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
</section>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/landing/_berita.blade.php ENDPATH**/ ?>