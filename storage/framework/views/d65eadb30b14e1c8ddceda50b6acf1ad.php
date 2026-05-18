
<section id="program" class="py-28 bg-canvas-soft relative overflow-hidden">
    <div class="absolute inset-0 dot-grid opacity-40"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">

        
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            
            <span
                class="inline-block text-[10px] tracking-widest text-brand-700 uppercase bg-brand-200 px-3 py-1.5 rounded-full mb-4"
                style="font-weight:400; letter-spacing:0.1px;">
                Divisi
            </span>
            
            <h2 class="font-display text-4xl lg:text-5xl text-ink leading-tight"
                style="font-weight:300; letter-spacing:-0.64px;">
                Temukan Minat
                <br><span class="gradient-text">Divisimu</span>
            </h2>
            <p class="text-ink-mute mt-4 text-lg" style="font-weight:300;">
                Kami memiliki berbagai divisi yang dapat disesuaikan dengan minat dan kemampuan Anda.
            </p>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($divisis->isNotEmpty()): ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $divisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $divisi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    
                    <div
                        class="reveal reveal-delay-<?php echo e(min(($loop->index % 4) + 1, 4)); ?> bg-white rounded-xl p-6 border border-hairline tech-card shadow-sm group">
                        <div
                            class="w-12 h-12 rounded-lg bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center text-xl mb-5 transition-colors duration-200 text-brand-600">
                            <i class="<?php echo e($divisi->icon); ?>"></i>
                        </div>
                        <h3 class="text-ink text-lg mb-2 group-hover:text-brand-600 transition-colors"
                            style="font-weight:300; letter-spacing:-0.22px;">
                            <?php echo e($divisi->nama); ?>

                        </h3>
                        <p class="text-ink-mute text-sm leading-relaxed" style="font-weight:300;">
                            <?php echo e($divisi->deskripsi); ?>

                        </p>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    ['fa-solid fa-laptop-code',    'Web Development',   'Membangun aplikasi web modern dengan framework terkini'],
                    ['fa-solid fa-robot',           'AI & Machine Learning', 'Mengeksplorasi kecerdasan buatan dan data science'],
                    ['fa-solid fa-mobile-screen',   'Mobile Development','Merancang aplikasi Android & iOS yang inovatif'],
                    ['fa-solid fa-palette',         'UI/UX Design',      'Menciptakan pengalaman pengguna yang intuitif dan estetis'],
                    ['fa-solid fa-shield-halved',   'Cyber Security',    'Mempelajari keamanan sistem dan ethical hacking'],
                    ['fa-solid fa-cloud',           'Cloud & DevOps',    'Infrastruktur cloud, CI/CD, dan otomasi deployment'],
                    ['fa-solid fa-trophy',          'Lomba & Hackathon', 'Mengikuti kompetisi teknologi tingkat nasional'],
                    ['fa-solid fa-book-open',       'Workshop Bulanan',  'Pelatihan intensif dengan instruktur berpengalaman'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$iconClass, $nama, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div
                        class="reveal reveal-delay-<?php echo e(min(($loop->index % 4) + 1, 4)); ?> bg-white rounded-xl p-6 border border-hairline tech-card shadow-sm group">
                        <div
                            class="w-12 h-12 rounded-lg bg-brand-50 group-hover:bg-brand-100 flex items-center justify-center text-xl mb-5 transition-colors duration-200 text-brand-600">
                            <i class="<?php echo e($iconClass); ?>"></i>
                        </div>
                        <h3 class="text-ink text-lg mb-2 group-hover:text-brand-600 transition-colors"
                            style="font-weight:300; letter-spacing:-0.22px;">
                            <?php echo e($nama); ?>

                        </h3>
                        <p class="text-ink-mute text-sm leading-relaxed" style="font-weight:300;"><?php echo e($desc); ?></p>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="mt-14 reveal">
            
            <div
                class="rounded-xl bg-brand-900 p-8 lg:p-10 flex flex-col lg:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-20 w-40 h-40 bg-brand-600/20 rounded-full translate-y-1/2"></div>
                <div class="relative z-10 text-center lg:text-left">
                    <h3 class="text-2xl lg:text-3xl text-white"
                        style="font-weight:300; letter-spacing:-0.64px;">
                        Siap bergabung dengan divisi pilihan Anda?
                    </h3>
                    <p class="text-brand-200 mt-2" style="font-weight:300;">
                        Daftar sekarang dan mulai perjalanan teknologi Anda bersama MCI.
                    </p>
                </div>
                
                <a href="#daftar"
                    class="relative z-10 shrink-0 inline-flex items-center gap-2 px-7 py-3 rounded-full bg-white text-brand-700 hover:bg-brand-50 shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                    style="font-weight:400; font-size:16px; line-height:1.0;">
                    Daftar Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/landing/_divisis.blade.php ENDPATH**/ ?>