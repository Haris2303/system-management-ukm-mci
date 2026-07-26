<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Hasil Seleksi UKM MCI</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">

                
                <tr>
                    <td style="background:linear-gradient(135deg,#374151,#6b7280);padding:36px 40px;text-align:center;">
                        <p style="margin:0 0 8px 0;font-size:36px;">📋</p>
                        <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">
                            Informasi Hasil Seleksi
                        </h1>
                        <p style="margin:10px 0 0 0;color:#d1d5db;font-size:14px;">
                            UKM MCI — Universitas Muhammadiyah Sorong
                        </p>
                    </td>
                </tr>

                
                <tr>
                    <td style="padding:36px 40px;">

                        <p style="margin:0 0 20px 0;font-size:15px;color:#374151;line-height:1.7;">
                            Halo, <strong><?php echo e($nama); ?></strong>.
                        </p>

                        <p style="margin:0 0 20px 0;font-size:15px;color:#374151;line-height:1.7;">
                            Terima kasih telah mendaftar dan mengikuti proses seleksi penerimaan anggota
                            <strong>UKM MCI (Media Creative Informations)</strong> Universitas Muhammadiyah Sorong
                            untuk divisi <strong><?php echo e($divisi); ?></strong>.
                        </p>

                        
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:20px 24px;text-align:center;">
                                    <p style="margin:0 0 8px 0;font-size:28px;">😔</p>
                                    <p style="margin:0;font-size:15px;color:#991b1b;font-weight:600;line-height:1.6;">
                                        Setelah melalui proses evaluasi secara menyeluruh, kami menyampaikan bahwa
                                        <strong>kamu belum dapat kami terima</strong> sebagai anggota pada periode rekrutmen ini.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 28px 0;font-size:14px;color:#6b7280;line-height:1.7;">
                            Keputusan ini bukan berarti kamu tidak memiliki potensi — persaingan periode ini sangat ketat
                            dan kami harus membuat keputusan yang sulit.
                        </p>

                        
                        <h2 style="margin:0 0 16px 0;font-size:16px;color:#111827;font-weight:700;">
                            💪 Jangan Menyerah
                        </h2>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                                'Terus asah kemampuan dan keterampilan di bidang yang kamu minati',
                                'Pantau pengumuman Open Recruitment berikutnya',
                                'Kamu tetap bisa mengikuti kegiatan publik UKM MCI'
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td style="padding:8px 0;border-bottom:1px solid #f3f4f6;">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width:20px;vertical-align:top;padding-top:2px;">
                                                <span style="color:#3671ff;font-size:14px;">✓</span>
                                            </td>
                                            <td style="padding-left:10px;font-size:14px;color:#374151;line-height:1.6;"><?php echo e($poin); ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </table>

                        <p style="margin:0;font-size:14px;color:#374151;line-height:1.7;">
                            Kami menghargai keberanian dan semangat kamu untuk bergabung.
                            Semoga sukses di perjalanan selanjutnya! 🌟
                        </p>

                    </td>
                </tr>

                
                <tr>
                    <td style="background-color:#f9fafb;border-top:1px solid #e5e7eb;padding:24px 40px;text-align:center;">
                        <p style="margin:0 0 4px 0;font-size:13px;color:#374151;font-weight:600;">Pengurus UKM MCI</p>
                        <p style="margin:0;font-size:12px;color:#9ca3af;">Universitas Muhammadiyah Sorong</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/emails/pendaftar-ditolak.blade.php ENDPATH**/ ?>