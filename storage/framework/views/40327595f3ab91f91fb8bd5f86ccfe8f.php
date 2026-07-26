<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan E-Kas</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitle { color: #6b7280; margin-bottom: 18px; }
        h2 { font-size: 13px; margin-top: 22px; margin-bottom: 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 5px 8px; border: 1px solid #e5e7eb; text-align: left; }
        th { background-color: #f3f4f6; }
        .text-right { text-align: right; }
        .ringkasan td:first-child { width: 60%; }
        .badge-lunas { color: #15803d; font-weight: bold; }
        .badge-belum { color: #b91c1c; font-weight: bold; }
        .badge-masuk { color: #15803d; font-weight: bold; }
        .badge-keluar { color: #b91c1c; font-weight: bold; }
        .empty { color: #6b7280; font-style: italic; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan E-Kas</h1>
    <div class="subtitle">Dicetak pada <?php echo e($periode); ?></div>

    <h2>Ringkasan</h2>
    <table class="ringkasan">
        <tr><td>Saldo Kas Saat Ini</td><td class="text-right"><?php echo e($kas->formatCurrency($ringkasan['total_saldo'])); ?></td></tr>
        <tr><td>Total Iuran Lunas</td><td class="text-right"><?php echo e($kas->formatCurrency($ringkasan['total_iuran_lunas'])); ?></td></tr>
        <tr><td>Total Kas Masuk</td><td class="text-right"><?php echo e($kas->formatCurrency($ringkasan['total_kas_masuk'])); ?></td></tr>
        <tr><td>Total Kas Keluar</td><td class="text-right"><?php echo e($kas->formatCurrency($ringkasan['total_kas_keluar'])); ?></td></tr>
        <tr><td>Total Tunggakan</td><td class="text-right"><?php echo e($kas->formatCurrency($ringkasan['total_tunggakan'])); ?></td></tr>
    </table>

    <h2>Detail Tagihan Kas</h2>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tagihan->isEmpty()): ?>
        <p class="empty">Belum ada data tagihan kas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>Bulan Tagihan</th>
                    <th class="text-right">Nominal</th>
                    <th>Status</th>
                    <th>Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tagihan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e($item->user?->name ?? '—'); ?></td>
                        <td><?php echo e($item->bulan_tagihan_format); ?></td>
                        <td class="text-right"><?php echo e($kas->formatCurrency($item->nominal)); ?></td>
                        <td class="<?php echo e($item->status === 'lunas' ? 'badge-lunas' : 'badge-belum'); ?>">
                            <?php echo e($item->status === 'lunas' ? 'Lunas' : 'Belum Dibayar'); ?>

                        </td>
                        <td><?php echo e($item->tanggal_bayar?->timezone('Asia/Jayapura')->format('d-m-Y H:i') ?? '—'); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <h2>Detail Arus Kas</h2>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($transaksi->isEmpty()): ?>
        <p class="empty">Belum ada data arus kas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th class="text-right">Nominal</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e($item->tanggal->format('d-m-Y')); ?></td>
                        <td class="<?php echo e($item->jenis === 'masuk' ? 'badge-masuk' : 'badge-keluar'); ?>">
                            <?php echo e($item->jenis === 'masuk' ? 'Kas Masuk' : 'Kas Keluar'); ?>

                        </td>
                        <td><?php echo e($item->keterangan ?? '—'); ?></td>
                        <td class="text-right"><?php echo e($kas->formatCurrency($item->nominal)); ?></td>
                        <td><?php echo e($item->pencatat?->name ?? '—'); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/pdf/laporan-keuangan.blade.php ENDPATH**/ ?>