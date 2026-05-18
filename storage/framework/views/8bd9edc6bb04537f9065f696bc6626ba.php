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
    .ps-header {
        display: flex; align-items: center; gap: 20px;
        padding: 20px 24px; background: #fff;
        border: 1px solid #e5e7eb; border-radius: 14px;
        margin-bottom: 20px;
    }
    .ps-avatar-current {
        width: 72px; height: 72px; border-radius: 50%;
        object-fit: cover; border: 3px solid #dbeafe;
        flex-shrink: 0;
    }
    .ps-avatar-emoji {
        display: flex; align-items: center; justify-content: center;
        font-size: 38px; line-height: 1;
    }
    .ps-avatar-initials {
        display: flex; align-items: center; justify-content: center;
        background: #1a4ff5; color: #fff;
        font-size: 28px; font-weight: 700;
    }
    .ps-emoji-circle {
        width: 100%; height: 100%; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; line-height: 1;
    }
    .ps-header-info { flex: 1; }
    .ps-header-name { font-size: 16px; font-weight: 700; color: #111827; }
    .ps-header-email { font-size: 13px; color: #6b7280; margin-top: 2px; }

    .ps-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 4px 0 20px;
    }
    .ps-divider-line { flex: 1; height: 1px; background: #e5e7eb; }
    .ps-divider-text { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; }

    .ps-picker {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 14px; padding: 20px 24px;
        margin-bottom: 20px;
    }
    .ps-picker-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
    .ps-picker-sub { font-size: 12px; color: #9ca3af; margin-bottom: 16px; }

    .ps-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
    }
    @media (max-width: 640px) {
        .ps-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .ps-avatar-btn {
        position: relative; border-radius: 50%; overflow: hidden;
        width: 64px; height: 64px; border: 3px solid transparent;
        cursor: pointer; background: none; padding: 0;
        transition: border-color .2s, transform .15s, box-shadow .2s;
    }
    .ps-avatar-btn:hover { border-color: #93c5fd; transform: scale(1.08); }
    .ps-avatar-btn.selected {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.2);
        transform: scale(1.1);
    }
    .ps-avatar-btn img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ps-check {
        position: absolute; inset: 0;
        background: rgba(37,99,235,.25);
        display: flex; align-items: center; justify-content: center;
    }
    .ps-check svg { width: 20px; height: 20px; color: #fff; filter: drop-shadow(0 1px 2px rgba(0,0,0,.4)); }
</style>


<?php $display = $this->getCurrentAvatarDisplay(); ?>
<div class="ps-header">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($display['type'] === 'emoji'): ?>
        <div class="ps-avatar-current ps-avatar-emoji" style="background:#<?php echo e($display['bg']); ?>;">
            <?php echo e($display['emoji']); ?>

        </div>
    <?php elseif($display['type'] === 'img'): ?>
        <img class="ps-avatar-current" src="<?php echo e($display['url']); ?>" alt="Avatar">
    <?php else: ?>
        <div class="ps-avatar-current ps-avatar-initials">
            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="ps-header-info">
        <div class="ps-header-name"><?php echo e(auth()->user()->name); ?></div>
        <div class="ps-header-email"><?php echo e(auth()->user()->email); ?></div>
    </div>
</div>


<form wire:submit="save">
    <?php echo e($this->form); ?>


    
    <div class="ps-divider" style="margin-top: 20px;">
        <div class="ps-divider-line"></div>
        <span class="ps-divider-text">atau pilih avatar emoji</span>
        <div class="ps-divider-line"></div>
    </div>

    
    <div class="ps-picker">
        <div class="ps-picker-title">Avatar Emoji</div>
        <div class="ps-picker-sub">Klik untuk memilih. Memilih emoji akan menggantikan foto yang diupload.</div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->uploadedPhotoPath): ?>
            <?php
                $uploadedUrl = str_starts_with($this->uploadedPhotoPath, 'http')
                    ? $this->uploadedPhotoPath
                    : asset('storage/' . $this->uploadedPhotoPath);
                $isPhotoSelected = $this->selectedAvatar === $this->uploadedPhotoPath;
            ?>
            <div style="margin-bottom: 20px;">
                <p style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px;">Foto yang diupload</p>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <button
                        type="button"
                        wire:click="selectUploadedPhoto()"
                        wire:loading.attr="disabled"
                        class="ps-avatar-btn <?php echo e($isPhotoSelected ? 'selected' : ''); ?>"
                    >
                        <img src="<?php echo e($uploadedUrl); ?>" alt="Foto Profil">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPhotoSelected): ?>
                            <div class="ps-check">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                    <p style="font-size: 12px; color: #6b7280;">
                        Klik untuk menggunakan kembali foto ini
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $isPhotoSelected): ?>
                            tanpa upload ulang.
                        <?php else: ?>
                            <span style="color: #2563eb; font-weight: 600;">— Aktif</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="ps-divider" style="margin-bottom: 16px;">
                <div class="ps-divider-line"></div>
                <span class="ps-divider-text">atau pilih emoji</span>
                <div class="ps-divider-line"></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="ps-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->getPresetAvatars(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php $id = "emoji:{$preset['emoji']}:{$preset['bg']}"; ?>
                <button
                    type="button"
                    wire:click="selectAvatar(<?php echo e($i); ?>)"
                    wire:loading.attr="disabled"
                    class="ps-avatar-btn <?php echo e($this->selectedAvatar === $id ? 'selected' : ''); ?>"
                >
                    <div class="ps-emoji-circle" style="background:#<?php echo e($preset['bg']); ?>;">
                        <?php echo e($preset['emoji']); ?>

                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->selectedAvatar === $id): ?>
                        <div class="ps-check">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    <div style="margin-top: 24px;">
        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['type' => 'submit','icon' => 'heroicon-o-check']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','icon' => 'heroicon-o-check']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Simpan Perubahan
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
    </div>
</form>

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
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/filament/pages/profil-saya.blade.php ENDPATH**/ ?>