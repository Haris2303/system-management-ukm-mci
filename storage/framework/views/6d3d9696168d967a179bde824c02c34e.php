<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat! Kamu Dinyatakan LULUS</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">

                
                <tr>
                    <td style="background:linear-gradient(135deg,#1340e1,#3671ff);padding:36px 40px;text-align:center;">
                        <p style="margin:0 0 8px 0;font-size:36px;">🎉</p>
                        <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.3px;">
                            Selamat, <?php echo e($nama); ?>!
                        </h1>
                        <p style="margin:10px 0 0 0;color:#c8d8ff;font-size:14px;">
                            UKM MCI — Universitas Muhammadiyah Sorong
                        </p>
                    </td>
                </tr>

                
                <tr>
                    <td style="padding:36px 40px;">

                        <p style="margin:0 0 20px 0;font-size:15px;color:#374151;line-height:1.7;">
                            Kami dengan bangga memberitahukan bahwa kamu telah
                            <strong style="color:#1340e1;">dinyatakan LULUS</strong>
                            seleksi penerimaan anggota <strong>UKM MCI (Media Creative Informations)</strong>
                            Universitas Muhammadiyah Sorong.
                        </p>

                        
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="background-color:#eff6ff;border-left:4px solid #3671ff;border-radius:6px;padding:16px 20px;">
                                    <p style="margin:0 0 4px 0;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">Divisi</p>
                                    <p style="margin:0;font-size:18px;color:#1340e1;font-weight:700;"><?php echo e($divisi); ?></p>
                                    <p style="margin:4px 0 0 0;font-size:13px;color:#6b7280;">Angkatan <?php echo e($angkatan); ?></p>
                                </td>
                            </tr>
                        </table>

                        
                        <h2 style="margin:0 0 12px 0;font-size:16px;color:#111827;font-weight:700;">
                            Akun Mobile App
                        </h2>
                        <p style="margin:0 0 16px 0;font-size:14px;color:#6b7280;line-height:1.6;">
                            Gunakan kredensial berikut untuk login ke aplikasi mobile UKM MCI:
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding-bottom:12px;">
                                                <p style="margin:0 0 4px 0;font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">Email</p>
                                                <p style="margin:0;font-size:15px;color:#111827;font-weight:600;"><?php echo e($email); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="border-top:1px solid #e5e7eb;padding-top:12px;">
                                                <p style="margin:0 0 4px 0;font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">Password</p>
                                                <p style="margin:0;font-size:15px;color:#111827;font-weight:600;font-family:monospace;background:#e5e7eb;display:inline-block;padding:4px 10px;border-radius:4px;"><?php echo e($password); ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="background-color:#fffbeb;border:1px solid #fde68a;border-radius:6px;padding:12px 16px;">
                                    <p style="margin:0;font-size:13px;color:#92400e;">
                                        ⚠️ <strong>Penting:</strong> Segera ganti password setelah login pertama kali melalui menu <strong>Profil → Ganti Password</strong>.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        
                        <h2 style="margin:0 0 16px 0;font-size:16px;color:#111827;font-weight:700;">
                            Langkah Selanjutnya
                        </h2>
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Download aplikasi mobile UKM MCI', 'Login menggunakan kredensial di atas', 'Lengkapi foto profil kamu', 'Pantau agenda dan kegiatan organisasi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td style="padding:8px 0;">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width:32px;height:32px;background-color:#1340e1;border-radius:50%;text-align:center;vertical-align:middle;">
                                                <span style="color:#ffffff;font-size:13px;font-weight:700;line-height:32px;"><?php echo e($i + 1); ?></span>
                                            </td>
                                            <td style="padding-left:12px;font-size:14px;color:#374151;"><?php echo e($step); ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </table>

                        <p style="margin:0;font-size:14px;color:#374151;line-height:1.7;">
                            Selamat bergabung dan mari bersama berkontribusi untuk UKM MCI! 🚀
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
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/emails/pendaftar-lulus.blade.php ENDPATH**/ ?>