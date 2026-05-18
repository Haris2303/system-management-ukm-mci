<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card — <?php echo e($user->name); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            font-family: 'DM Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── No-print toolbar ── */
        .toolbar {
            width: 100%;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .toolbar-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 15px; color: #1e293b; }
        .btn-print {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px; border-radius: 10px;
            background: #1a4ff5; color: #fff;
            font-size: 13px; font-weight: 600;
            border: none; cursor: pointer;
            transition: background .2s;
        }
        .btn-print:hover { background: #1340e1; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px;
            background: #f1f5f9; color: #475569;
            font-size: 13px; font-weight: 500;
            border: 1px solid #e2e8f0; cursor: pointer; text-decoration: none;
        }

        /* ── Page body ── */
        .page-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        /* ── ID Card shell ── */
        .id-card {
            width: 300px;
            position: relative;
            overflow: hidden;
            user-select: none;
        }

        /* ── Header ── */
        .id-card-header {
            padding: 16px 18px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .id-card-header-logo {
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.2);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-weight: 800; font-size: 15px; color: #fff;
            flex-shrink: 0;
        }
        .id-card-header-text { flex: 1; }
        .id-card-header-org {
            font-size: 9px; font-weight: 600; letter-spacing: .08em;
            text-transform: uppercase; color: rgba(255,255,255,0.75); margin-bottom: 2px;
        }
        .id-card-header-title {
            font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; color: #fff;
            line-height: 1.3;
        }

        /* ── Content panel (replaces body + fields + bottom) ── */
        .id-card-content {
            padding-bottom: 4px;
        }

        /* ── Photo (centered) ── */
        .id-card-photo-wrap {
            padding: 22px 20px 14px;
            display: flex;
            justify-content: center;
        }
        .id-card-photo {
            width: 96px; height: 96px;
            border-radius: 14px;
            object-fit: cover;
            object-position: top;
            border: 3px solid #1a4ff5;
            display: block;
        }
        .id-card-photo-ring { border: 3px solid #1a4ff5; }
        .id-card-photo-placeholder {
            width: 96px; height: 96px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-size: 34px; font-weight: 800;
            color: #fff;
            border: 3px solid #1a4ff5;
        }

        /* ── Info (centered) ── */
        .id-card-info {
            padding: 0 20px 20px;
            text-align: center;
        }
        .id-card-name {
            font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700;
            color: #0f172a; line-height: 1.3; margin-bottom: 8px;
        }
        .id-card-badge {
            display: inline-flex; align-items: center;
            padding: 3px 12px; border-radius: 999px;
            font-size: 10px; font-weight: 600;
            background: #eef5ff; color: #1a4ff5; border: 1px solid #bdd8ff;
            margin-bottom: 12px;
        }
        .id-card-divisi-row {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 8px; padding: 5px 12px;
        }
        .id-card-divisi-label {
            font-size: 9px; font-weight: 600; letter-spacing: .06em;
            text-transform: uppercase; color: #94a3b8;
        }
        .id-card-divisi-value {
            font-size: 12px; font-weight: 600; color: #334155;
        }

        /* ── Divider ── */
        .id-card-divider {
            height: 1px;
            margin: 0 20px;
            background: #e2e8f0;
        }

        /* ── QR section (centered) ── */
        .id-card-qr-wrap {
            padding: 18px 20px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .id-card-scan-label {
            font-size: 8.5px; color: #94a3b8; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
        }
        .id-card-qr {
            width: 120px; height: 120px;
        }
        .id-card-qr svg { width: 100%; height: 100%; border-radius: 8px; }

        /* ── Footer ── */
        .id-card-footer {
            padding: 8px 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .id-card-footer-text {
            font-size: 8px; color: #cbd5e1; letter-spacing: .06em;
        }

        /* ── Background-image mode ── */
        .id-card-bg-mode {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .id-card-bg-mode .id-card-corner-tl,
        .id-card-bg-mode .id-card-corner-tr,
        .id-card-bg-mode .id-card-corner-bl,
        .id-card-bg-mode .id-card-corner-br { display: none; }

        /* Header: subtle dark overlay agar teks terbaca */
        .id-card-bg-mode .id-card-header {
            background: linear-gradient(180deg, rgba(0,0,0,.45) 0%, rgba(0,0,0,.08) 100%);
        }
        .id-card-bg-mode .id-card-header-org   { color: rgba(255,255,255,.85); }
        .id-card-bg-mode .id-card-header-title { color: #fff; text-shadow: 0 1px 4px rgba(0,0,0,.5); }
        .id-card-bg-mode .id-card-header-logo  { background: rgba(255,255,255,.22); color: #fff; }

        /* Content panel: transparan — background image bebas terlihat */
        .id-card-bg-mode .id-card-content { background: transparent; }

        /* Foto: ring putih + shadow agar menonjol di atas background */
        .id-card-bg-mode .id-card-photo-wrap { padding: 24px 20px 16px; }
        .id-card-bg-mode .id-card-photo,
        .id-card-bg-mode .id-card-photo-placeholder {
            border: 3px solid #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,.35);
        }

        /* Nama: teks putih dengan text-shadow tebal */
        .id-card-bg-mode .id-card-name {
            color: #fff;
            text-shadow: 0 1px 8px rgba(0,0,0,.7), 0 0 24px rgba(0,0,0,.4);
        }

        /* Badge jabatan: frosted glass kecil */
        .id-card-bg-mode .id-card-badge {
            background: rgba(255,255,255,.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #fff;
            border: 1px solid rgba(255,255,255,.35);
        }

        /* Divisi row: frosted glass pill */
        .id-card-bg-mode .id-card-divisi-row {
            background: rgba(255,255,255,.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.3);
        }
        .id-card-bg-mode .id-card-divisi-label { color: rgba(255,255,255,.7); }
        .id-card-bg-mode .id-card-divisi-value { color: #fff; text-shadow: 0 1px 3px rgba(0,0,0,.3); }

        /* Divider: garis putih transparan */
        .id-card-bg-mode .id-card-divider {
            background: rgba(255,255,255,.25);
            margin: 0 24px;
        }

        /* QR: kotak putih frosted kecil hanya di sekitar QR */
        .id-card-bg-mode .id-card-qr-wrap { padding: 16px 20px 24px; }
        .id-card-bg-mode .id-card-scan-label { color: rgba(255,255,255,.75); }
        .id-card-bg-mode .id-card-qr {
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,.25);
        }

        /* Footer: transparan, teks putih subtle */
        .id-card-bg-mode .id-card-footer {
            background: transparent;
            border-top: 1px solid rgba(255,255,255,.2);
        }
        .id-card-bg-mode .id-card-footer-text { color: rgba(255,255,255,.55); }

        /* ── Template CSS (injected when no background image) ── */
        <?php if(!$backgroundImage): ?>
        <?php echo $template['css']; ?>

        <?php endif; ?>

        /* ── Print ── */
        @media print {
            html, body { background: white; }
            .toolbar { display: none !important; }
            .page-body { padding: 0; align-items: flex-start; justify-content: flex-start; }
            .id-card { box-shadow: none !important; }
        }
    </style>
</head>

<body>
    
    <div class="toolbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="javascript:history.back()" class="btn-back">← Kembali</a>
            <span class="toolbar-title">ID Card — <?php echo e($user->name); ?></span>
        </div>
        <button class="btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="page-body">
        <div class="id-card <?php echo e($backgroundImage ? 'id-card-bg-mode' : ''); ?>"
             style="box-shadow:0 20px 60px rgba(0,0,0,0.14);<?php echo e($backgroundImage ? 'background-image:url(\'' . $backgroundImage . '\')' : ''); ?>">

            
            <div class="id-card-corner-tl"></div>
            <div class="id-card-corner-tr"></div>
            <div class="id-card-corner-bl"></div>
            <div class="id-card-corner-br"></div>

            
            <div class="id-card-header">
                <div class="id-card-header-logo">M</div>
                <div class="id-card-header-text">
                    <div class="id-card-header-org">Unit Kegiatan Mahasiswa</div>
                    <div class="id-card-header-title">MCI — Mahasiswa Creative &amp; Innovation</div>
                </div>
            </div>

            
            <div class="id-card-content">

                
                <div class="id-card-photo-wrap">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fotoUrl): ?>
                        <img src="<?php echo e($fotoUrl); ?>" alt="<?php echo e($user->name); ?>"
                             class="id-card-photo id-card-photo-ring">
                    <?php else: ?>
                        <div class="id-card-photo-placeholder id-card-photo-ring"
                             style="background:linear-gradient(135deg,#1a4ff5,#3671ff);">
                            <?php echo e(mb_strtoupper(mb_substr($user->name, 0, 1))); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="id-card-info">
                    <p class="id-card-name"><?php echo e($user->name); ?></p>
                    <div>
                        <span class="id-card-badge">
                            <?php echo e($user->role_label ?? ($user->roles->first()?->name ?? 'Anggota')); ?>

                        </span>
                    </div>
                    <div style="margin-top:10px;">
                        <div class="id-card-divisi-row">
                            <span class="id-card-divisi-label">Divisi</span>
                            <span class="id-card-divisi-value"><?php echo e($user->divisi?->nama ?? '—'); ?></span>
                        </div>
                    </div>
                </div>

                
                <div class="id-card-divider"></div>

                
                <div class="id-card-qr-wrap">
                    <span class="id-card-scan-label">Scan untuk verifikasi</span>
                    <div class="id-card-qr">
                        <?php echo $qrCode; ?>

                    </div>
                </div>

            </div>

            
            <div class="id-card-footer">
                <div class="id-card-footer-text">
                    UKM MCI · Kartu Anggota Resmi · <?php echo e(date('Y')); ?>

                </div>
            </div>

        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\ukm-mci-digitalisasi\resources\views/id-card/show.blade.php ENDPATH**/ ?>