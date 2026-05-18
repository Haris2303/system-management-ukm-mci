<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


<style>
    .idc-info {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;
        font-size: 13px; line-height: 1.5;
        background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8;
    }
    .idc-info svg { flex-shrink: 0; margin-top: 1px; }

    /* ── Background preview ── */
    .idc-bg-section {
        border-radius: 14px; overflow: hidden;
        border: 2px solid #e5e7eb; background: #fff;
        margin-bottom: 24px;
    }
    .idc-bg-header {
        padding: 12px 16px; background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .idc-bg-header-title { font-size: 13px; font-weight: 600; color: #374151; }
    .idc-bg-body { padding: 16px; display: flex; align-items: center; gap: 16px; }
    .idc-bg-thumb {
        width: 80px; height: 130px; border-radius: 8px; flex-shrink: 0;
        background-size: cover; background-position: center;
        border: 2px solid #e5e7eb; box-shadow: 0 4px 12px rgba(0,0,0,0.10);
    }
    .idc-bg-info { flex: 1; }
    .idc-bg-info p { font-size: 13px; color: #374151; margin-bottom: 4px; }
    .idc-bg-info small { font-size: 11px; color: #94a3b8; }
    .idc-bg-empty {
        padding: 20px; text-align: center;
        font-size: 13px; color: #94a3b8;
    }
    .idc-bg-badge-active {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .05em; padding: 2px 8px; border-radius: 999px;
        background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;
    }
    .idc-bg-badge-inactive {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .05em; padding: 2px 8px; border-radius: 999px;
        background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0;
    }

    /* ── Template grid ── */
    .idc-section-title {
        font-size: 13px; font-weight: 600; color: #374151;
        margin-bottom: 12px;
    }
    .idc-section-sub {
        font-size: 12px; color: #94a3b8; margin-bottom: 16px; margin-top: -8px;
    }

    .idc-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 640px) {
        .idc-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .idc-card {
        border-radius: 14px; overflow: hidden;
        border: 2px solid #e5e7eb;
        cursor: pointer; transition: border-color .2s, box-shadow .2s, transform .15s;
        background: #fff; text-align: left;
        padding: 0; outline: none;
    }
    .idc-card:not(:disabled):hover { border-color: #93c5fd; box-shadow: 0 8px 24px rgba(59,130,246,.12); transform: translateY(-2px); }
    .idc-card.active { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.2); }
    .idc-card:disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

    .idc-swatch {
        height: 96px; display: flex; align-items: center; justify-content: center;
        position: relative; overflow: hidden;
    }
    .idc-mock {
        width: 140px; height: 78px; border-radius: 8px; background: rgba(255,255,255,.92);
        box-shadow: 0 4px 16px rgba(0,0,0,.18);
        display: flex; flex-direction: column; overflow: hidden;
        border: 2px solid rgba(255,255,255,.8);
    }
    .idc-mock-header { height: 22px; width: 100%; opacity: .9; }
    .idc-mock-body { display: flex; align-items: center; gap: 8px; padding: 6px 8px; flex: 1; }
    .idc-mock-avatar { width: 28px; height: 28px; border-radius: 50%; opacity: .5; flex-shrink: 0; }
    .idc-mock-lines { display: flex; flex-direction: column; gap: 5px; flex: 1; }
    .idc-mock-line { height: 6px; border-radius: 4px; }

    .idc-check {
        position: absolute; top: 8px; right: 8px;
        width: 24px; height: 24px; border-radius: 50%; background: #fff;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 6px rgba(0,0,0,.15);
    }
    .idc-check svg { width: 14px; height: 14px; color: #2563eb; }

    .idc-label {
        padding: 10px 12px; background: #fff;
        display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid #f1f5f9;
    }
    .idc-label-name { font-size: 13px; font-weight: 600; color: #374151; }
    .idc-label-name.active { color: #2563eb; }
    .idc-badge {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .05em; padding: 2px 8px; border-radius: 999px;
        background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;
    }

    .idc-footer { display: flex; align-items: center; gap: 12px; }
    .idc-preview-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        background: #f8fafc; border: 1px solid #e2e8f0;
        font-size: 13px; font-weight: 500; color: #475569;
        text-decoration: none; cursor: pointer;
        transition: background .15s, border-color .15s;
    }
    .idc-preview-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .idc-footer-note { font-size: 12px; color: #94a3b8; }
    .idc-footer-note strong { color: #64748b; }
</style>


<div class="idc-info">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>
        Pilih template warna <strong>atau</strong> upload gambar background custom (Canva/Figma/Photoshop).
        Background image akan menggantikan template warna jika diupload.
        Sebelum mendesain, lihat <a href="<?php echo e(route('id-card.template')); ?>" target="_blank" style="font-weight:700;color:#1d4ed8;text-decoration:underline;">panduan zona &amp; template layout</a> agar desain tidak menutupi konten ID card.
    </span>
</div>


<div class="idc-bg-section">
    <div class="idc-bg-header">
        <span class="idc-bg-header-title">Background Image</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->backgroundImage): ?>
            <span class="idc-bg-badge-active">Aktif</span>
        <?php else: ?>
            <span class="idc-bg-badge-inactive">Tidak digunakan</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->backgroundImage): ?>
        <div class="idc-bg-body">
            <div class="idc-bg-thumb"
                 style="background-image: url('<?php echo e($this->backgroundImage); ?>');"></div>
            <div class="idc-bg-info">
                <p><strong>Background custom aktif.</strong></p>
                <p>ID Card seluruh anggota akan menggunakan gambar ini sebagai background.</p>
                <small>Klik "Hapus Background" di kanan atas untuk kembali ke template warna.</small>
            </div>
        </div>
    <?php else: ?>
        <div class="idc-bg-empty">
            Belum ada background image. Klik <strong>Ganti Background Image</strong> di kanan atas untuk upload.
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<p class="idc-section-title">Template Warna</p>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->backgroundImage): ?>
    <p class="idc-section-sub">Template warna tidak aktif saat background image digunakan.</p>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="idc-grid">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->getTemplates(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php $isActive = $slug === $this->activeTemplate && !$this->backgroundImage; ?>

        <button
            wire:click="selectTemplate('<?php echo e($slug); ?>')"
            type="button"
            <?php if($this->backgroundImage): echo 'disabled'; endif; ?>
            class="idc-card <?php echo e($isActive ? 'active' : ''); ?>"
        >
            
            <div class="idc-swatch" style="background: <?php echo e($tpl['preview_bg']); ?>;">
                <div class="idc-mock">
                    <div class="idc-mock-header" style="background: <?php echo e($tpl['preview_bg']); ?>;"></div>
                    <div class="idc-mock-body">
                        <div class="idc-mock-avatar" style="background: <?php echo e($tpl['preview_bg']); ?>;"></div>
                        <div class="idc-mock-lines">
                            <div class="idc-mock-line" style="width:70%; background:<?php echo e($tpl['preview_bg']); ?>;opacity:.35;"></div>
                            <div class="idc-mock-line" style="width:45%; background:<?php echo e($tpl['preview_bg']); ?>;opacity:.2;"></div>
                        </div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActive): ?>
                    <div class="idc-check">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="idc-label">
                <span class="idc-label-name <?php echo e($isActive ? 'active' : ''); ?>"><?php echo e($tpl['label']); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActive): ?>
                    <span class="idc-badge">Aktif</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </button>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>


<div class="idc-footer">
    <a href="<?php echo e(route('id-card.preview')); ?>" target="_blank" class="idc-preview-btn">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Preview ID Card Saya
    </a>

    <span class="idc-footer-note">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->backgroundImage): ?>
            Mode: <strong>Background Image</strong>
        <?php else: ?>
            Template aktif:
            <strong><?php echo e(\App\Support\IdCardTemplates::find($this->activeTemplate)['label']); ?></strong>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
</div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/filament/pages/id-card-setting.blade.php ENDPATH**/ ?>