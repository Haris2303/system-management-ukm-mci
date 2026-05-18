<?php $__env->startSection('title', $user->name . ' — Anggota UKM MCI'); ?>

<?php $__env->startSection('content'); ?>
<section class="min-h-screen flex items-center justify-center py-24 mesh-bg dot-grid noise relative overflow-hidden">

    
    <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full bg-brand-100/50 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] rounded-full pointer-events-none" style="background:rgba(15,244,198,0.07);filter:blur(64px);"></div>

    <div class="relative z-10 w-full max-w-sm mx-auto px-6">

        
        <div class="bg-white rounded-3xl shadow-xl shadow-brand-100/30 overflow-hidden border border-slate-100">

            
            <div class="relative h-32 overflow-hidden" style="background:linear-gradient(135deg,#1340e1,#3671ff,#0ff4c6);">
                <div class="absolute inset-0 opacity-20"
                     style="background-image:radial-gradient(circle,rgba(255,255,255,0.4) 1px,transparent 1px);background-size:24px 24px;"></div>
                
                <div class="absolute top-4 right-4">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/20 backdrop-blur-sm border border-white/30">
                        <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-[10px] font-bold text-white tracking-wider uppercase">Terverifikasi</span>
                    </div>
                </div>
            </div>

            
            <div class="flex justify-center -mt-14 mb-4 relative z-10">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fotoUrl): ?>
                    <img src="<?php echo e($fotoUrl); ?>" alt="<?php echo e($user->name); ?>"
                         class="w-28 h-28 rounded-2xl object-cover object-top border-4 border-white shadow-lg shadow-brand-100/50">
                <?php else: ?>
                    <div class="w-28 h-28 rounded-2xl border-4 border-white shadow-lg shadow-brand-100/50 flex items-center justify-center"
                         style="background:linear-gradient(135deg,#1a4ff5,#3671ff);">
                        <span class="font-display font-bold text-white text-4xl">
                            <?php echo e(mb_strtoupper(mb_substr($user->name, 0, 1))); ?>

                        </span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="text-center px-6 mb-5">
                <h1 class="font-display font-bold text-2xl text-slate-900 leading-tight mb-1">
                    <?php echo e($user->name); ?>

                </h1>
                <p class="text-xs font-bold tracking-widest text-brand-500 uppercase mb-3"><?php echo e($memberId); ?></p>

                
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-100">
                    <?php echo e($user->role_label ?? ($user->roles->first()?->name ?? 'Anggota')); ?>

                </span>
            </div>

            
            <div class="mx-6 h-px bg-slate-100 mb-5"></div>

            
            <div class="px-6 space-y-3 mb-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->divisi): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Divisi</p>
                            <p class="text-sm font-semibold text-slate-800"><?php echo e($user->divisi->nama); ?></p>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->no_hp): ?>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">No. HP</p>
                            <p class="text-sm font-semibold text-slate-800"><?php echo e($user->no_hp); ?></p>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Email</p>
                        <p class="text-sm font-semibold text-slate-800 break-all"><?php echo e($user->email); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-brand-600 to-brand-400 flex items-center justify-center shadow-sm">
                        <span class="text-white font-display font-bold text-[10px]">M</span>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">UKM MCI</span>
                </div>
                <span class="text-[10px] text-slate-400">Anggota Resmi <?php echo e(date('Y')); ?></span>
            </div>
        </div>

        
        <p class="text-center mt-6">
            <a href="/" class="text-sm text-slate-400 hover:text-brand-600 transition-colors">
                ← Kembali ke beranda
            </a>
        </p>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/landing/anggota/show.blade.php ENDPATH**/ ?>