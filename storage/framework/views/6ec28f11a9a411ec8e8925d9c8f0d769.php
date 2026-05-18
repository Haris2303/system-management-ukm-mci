<div <?php if($election->status === 'aktif'): ?> wire:poll.5s <?php endif; ?> class="flex flex-col min-h-screen">

    
    <header class="relative overflow-hidden mesh-bg dot-grid noise">

        
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brand-100/60 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 -left-32 w-80 h-80 rounded-full pointer-events-none" style="background:rgba(15,244,198,0.08);filter:blur(64px);"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-10 lg:py-14">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">

                
                <div class="space-y-3">
                    <span class="inline-block text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-50 px-3 py-1.5 rounded-full border border-brand-100">
                        Rekap Suara · E-Voting UKM MCI
                    </span>
                    <h1 class="font-display text-3xl lg:text-5xl font-bold text-slate-900 leading-tight">
                        <?php echo e($election->judul); ?>

                    </h1>
                    <p class="text-slate-500 text-sm lg:text-base">
                        Posisi yang dipilih:
                        <span class="font-semibold text-brand-600"><?php echo e($election->posisi); ?></span>
                    </p>
                </div>

                
                <div class="flex flex-col items-start sm:items-end gap-3 shrink-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($election->status === 'aktif'): ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="ping-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            Live — memperbarui setiap 5 detik
                        </div>
                    <?php elseif($election->status === 'selesai'): ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-brand-50 border border-brand-100 text-brand-700">
                            Pemilihan Selesai
                        </div>
                    <?php else: ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-slate-100 border border-slate-200 text-slate-500">
                            Belum Dimulai
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="text-right">
                        <p class="font-display text-4xl lg:text-5xl font-bold gradient-text">
                            <?php echo e(number_format($totalSuara)); ?>

                        </p>
                        <p class="text-slate-400 text-xs mt-0.5">total suara masuk</p>
                    </div>
                </div>

            </div>
        </div>
    </header>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($election->status === 'selesai' && $hasUniqueLeader): ?>
        <?php $winner = $candidates->first(); ?>

        <section class="relative overflow-hidden py-16 lg:py-24"
                 style="background: linear-gradient(135deg, #0c1445 0%, #141f57 30%, #18338f 60%, #0f2d6b 80%, #070d2e 100%);">

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [[8,12,0.9,'2.1s'],[92,8,0.7,'0s'],[15,85,0.8,'1.4s'],[85,75,0.6,'0.7s'],[50,5,1,'1s'],[30,60,0.5,'1.8s'],[70,40,0.7,'0.3s'],[20,30,0.6,'2.5s'],[80,55,0.9,'1.1s']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$x,$y,$op,$delay]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="absolute pointer-events-none"
                     style="left:<?php echo e($x); ?>%;top:<?php echo e($y); ?>%;opacity:<?php echo e($op); ?>;animation:star-float <?php echo e(3 + ($x % 3)); ?>s ease-in-out <?php echo e($delay); ?> infinite;">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M7 0l1.5 5H14l-4.5 3.3 1.7 5.2L7 10.4 1.8 13.5l1.7-5.2L0 5h5.5z" fill="white"/>
                    </svg>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            
            <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full pointer-events-none"
                 style="background:rgba(15,244,198,0.07);filter:blur(60px);"></div>
            <div class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full pointer-events-none"
                 style="background:rgba(26,79,245,0.15);filter:blur(60px);"></div>

            <div class="relative z-10 max-w-xl mx-auto px-6 text-center">

                
                <div class="trophy-pop text-6xl lg:text-7xl mb-4 select-none">🏆</div>

                
                <div class="winner-slide-up inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-bold tracking-widest uppercase mb-6"
                     style="background:rgba(15,244,198,0.15);border:1px solid rgba(15,244,198,0.4);color:#0ff4c6;animation-delay:0.1s;">
                    Pemenang Pemilihan
                </div>

                
                <div class="winner-slide-up flex justify-center mb-6" style="animation-delay:0.2s;">
                    <div class="relative">
                        
                        <div class="absolute inset-0 rounded-full winner-photo-ring" style="margin:-6px;border-radius:50%;"></div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner['foto']): ?>
                            <img src="<?php echo e($winner['foto']); ?>"
                                 alt="<?php echo e($winner['nama']); ?>"
                                 class="w-44 h-44 lg:w-56 lg:h-56 rounded-full object-cover object-top relative z-10"
                                 style="border:4px solid rgba(255,255,255,0.9);">
                        <?php else: ?>
                            <div class="w-44 h-44 lg:w-56 lg:h-56 rounded-full flex items-center justify-center relative z-10"
                                 style="background:linear-gradient(135deg,#1a4ff5,#3671ff);border:4px solid rgba(255,255,255,0.9);">
                                <span class="font-display font-bold text-white" style="font-size:5rem;line-height:1;">
                                    <?php echo e(mb_strtoupper(mb_substr($winner['nama'], 0, 1))); ?>

                                </span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="winner-slide-up space-y-1 mb-8" style="animation-delay:0.3s;">
                    <p class="winner-name-shimmer font-display font-bold leading-tight" style="font-size:clamp(1.8rem,5vw,3rem);">
                        <?php echo e($winner['nama']); ?>

                    </p>
                </div>

                
                <div class="winner-slide-up flex items-center justify-center gap-0 mb-2" style="animation-delay:0.4s;">
                    <div class="px-8 py-4 text-center" style="border-right:1px solid rgba(255,255,255,0.15);">
                        <p class="font-display font-bold text-white" style="font-size:2.5rem;line-height:1;">
                            <?php echo e($winner['persentase']); ?>%
                        </p>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.5);">persentase suara</p>
                    </div>
                    <div class="px-8 py-4 text-center">
                        <p class="font-display font-bold text-white" style="font-size:2.5rem;line-height:1;">
                            <?php echo e(number_format($winner['jumlah_suara'])); ?>

                        </p>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.5);">suara diperoleh</p>
                    </div>
                </div>

                
                <p class="text-xs" style="color:rgba(255,255,255,0.35);">
                    dari total <?php echo e(number_format($totalSuara)); ?> suara yang masuk
                </p>

            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section class="flex-1 py-10 lg:py-14 bg-slate-50/80 relative">
        <div class="absolute inset-0 dot-grid opacity-40"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($election->status === 'selesai' && !$candidates->isEmpty()): ?>
                <p class="text-center text-xs font-bold tracking-widest text-brand-500 uppercase mb-8">
                    Hasil Selengkapnya
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($candidates->isEmpty()): ?>
                <div class="flex flex-col items-center justify-center py-32 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-slate-400 text-lg">Belum ada kandidat terdaftar.</p>
                </div>
            <?php else: ?>
                <?php
                    $gridClass = match(true) {
                        $candidates->count() === 1 => 'max-w-sm mx-auto',
                        $candidates->count() === 2 => 'grid-cols-1 sm:grid-cols-2 max-w-3xl mx-auto',
                        default                    => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
                    };
                ?>

                <div class="grid gap-6 <?php echo e($gridClass); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php $isLeading = $index === 0 && $hasUniqueLeader; ?>

                        <div class="tech-card bg-white rounded-2xl overflow-hidden border flex flex-col shadow-sm group
                            <?php echo e($isLeading ? 'border-brand-200' : 'border-slate-100'); ?>"
                             style="<?php echo e($isLeading ? 'box-shadow:0 4px 32px rgba(26,79,245,0.12);' : ''); ?>">

                            
                            <div class="relative w-full overflow-hidden gallery-item" style="aspect-ratio:1/1;">

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($candidate['foto']): ?>
                                    <img src="<?php echo e($candidate['foto']); ?>"
                                         alt="<?php echo e($candidate['nama']); ?>"
                                         class="w-full h-full object-cover object-top">
                                <?php else: ?>
                                    
                                    <div class="w-full h-full flex items-center justify-center"
                                         style="background: linear-gradient(135deg, #daeaff 0%, #eef5ff 100%);">
                                        <div class="w-24 h-24 rounded-full bg-brand-600 flex items-center justify-center shadow-xl shadow-brand-200">
                                            <span class="text-white font-display font-bold text-4xl">
                                                <?php echo e(mb_strtoupper(mb_substr($candidate['nama'], 0, 1))); ?>

                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLeading): ?>
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-brand-600 text-white shadow-lg"
                                              style="box-shadow:0 4px 12px rgba(26,79,245,0.35);">
                                            Terdepan
                                        </span>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <div class="absolute inset-x-0 bottom-0 h-2/5"
                                     style="background:linear-gradient(to top, rgba(255,255,255,1), transparent);"></div>
                            </div>

                            
                            <div class="p-5 flex flex-col gap-4 flex-1">

                                
                                <div>
                                    <p class="font-display font-bold text-xl text-slate-900 group-hover:text-brand-600 transition-colors leading-tight">
                                        <?php echo e($candidate['nama']); ?>

                                    </p>
                                </div>

                                
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="font-display font-bold text-5xl leading-none <?php echo e($isLeading ? 'gradient-text' : 'text-slate-900'); ?>">
                                            <?php echo e($candidate['persentase']); ?>%
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">persentase suara</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold <?php echo e($isLeading ? 'text-brand-600' : 'text-slate-900'); ?>">
                                            <?php echo e(number_format($candidate['jumlah_suara'])); ?>

                                        </p>
                                        <p class="text-xs text-slate-400">suara</p>
                                    </div>
                                </div>

                                
                                <div class="w-full bg-slate-100 rounded-full overflow-hidden" style="height:8px;">
                                    <div class="h-full rounded-full"
                                         style="
                                             width:<?php echo e($candidate['persentase']); ?>%;
                                             transition:width .9s cubic-bezier(.4,0,.2,1);
                                             background:<?php echo e($isLeading
                                                 ? 'linear-gradient(90deg,#1340e1,#3671ff,#0ff4c6)'
                                                 : 'linear-gradient(90deg,#bdd8ff,#5c98ff)'); ?>;
                                             <?php echo e($isLeading ? 'box-shadow:0 0 10px rgba(26,79,245,0.3);' : ''); ?>

                                         ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalSuara === 0): ?>
                    <p class="text-center text-slate-400 text-sm mt-8">Belum ada suara yang masuk.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <footer class="py-6 text-center bg-white border-t border-slate-100">
        <div class="flex items-center justify-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-linear-to-br from-brand-600 to-brand-400 flex items-center justify-center shadow-sm shadow-brand-200">
                <span class="text-white font-display font-bold text-xs">M</span>
            </div>
            <p class="text-slate-400 text-xs">
                UKM MCI · Sistem E-Voting
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($election->status === 'aktif'): ?>
                    · <span class="text-emerald-500">Memperbarui otomatis setiap 5 detik</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
    </footer>

    <style>
        .gallery-item img { transition: transform 0.5s ease; }
        .gallery-item:hover img { transform: scale(1.05); }
    </style>

</div>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/livewire/election-realtime-page.blade.php ENDPATH**/ ?>