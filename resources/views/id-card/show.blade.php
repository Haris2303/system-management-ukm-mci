<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card — {{ $user->name }}</title>

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
            padding-bottom: 10px;
        }
        .id-card-bg-mode .id-card-corner-tl,
        .id-card-bg-mode .id-card-corner-tr,
        .id-card-bg-mode .id-card-corner-bl,
        .id-card-bg-mode .id-card-corner-br { display: none; }

        .id-card-bg-mode .id-card-header {
            background: linear-gradient(180deg, rgba(0,0,0,.32) 0%, rgba(0,0,0,.10) 100%);
        }
        .id-card-bg-mode .id-card-header-org   { color: rgba(255,255,255,.85); }
        .id-card-bg-mode .id-card-header-title { color: #fff; text-shadow: 0 1px 4px rgba(0,0,0,.4); }
        .id-card-bg-mode .id-card-header-logo  { background: rgba(255,255,255,.22); color: #fff; }

        /* Single content panel inset 8px — background image visible as side border strip */
        .id-card-bg-mode .id-card-content {
            margin: 0 8px;
            border-radius: 10px 10px 0 0;
            background: rgba(255,255,255,.97);
        }
        .id-card-bg-mode .id-card-divider {
            background: rgba(226,232,240,.9);
        }
        .id-card-bg-mode .id-card-footer {
            margin: 0 8px 2px;
            border-radius: 0 0 10px 10px;
            background: rgba(248,250,252,.97);
            border-top: 1px solid rgba(226,232,240,.8);
        }

        /* ── Template CSS (injected when no background image) ── */
        @if(!$backgroundImage)
        {!! $template['css'] !!}
        @endif

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
    {{-- Toolbar --}}
    <div class="toolbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="javascript:history.back()" class="btn-back">← Kembali</a>
            <span class="toolbar-title">ID Card — {{ $user->name }}</span>
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
        <div class="id-card {{ $backgroundImage ? 'id-card-bg-mode' : '' }}"
             style="box-shadow:0 20px 60px rgba(0,0,0,0.14);{{ $backgroundImage ? 'background-image:url(\'' . $backgroundImage . '\')' : '' }}">

            {{-- Corner decorations (CSS-template mode only) --}}
            <div class="id-card-corner-tl"></div>
            <div class="id-card-corner-tr"></div>
            <div class="id-card-corner-bl"></div>
            <div class="id-card-corner-br"></div>

            {{-- Header --}}
            <div class="id-card-header">
                <div class="id-card-header-logo">M</div>
                <div class="id-card-header-text">
                    <div class="id-card-header-org">Unit Kegiatan Mahasiswa</div>
                    <div class="id-card-header-title">MCI — Mahasiswa Creative &amp; Innovation</div>
                </div>
            </div>

            {{-- Content panel --}}
            <div class="id-card-content">

                {{-- Foto (centered) --}}
                <div class="id-card-photo-wrap">
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="{{ $user->name }}"
                             class="id-card-photo id-card-photo-ring">
                    @else
                        <div class="id-card-photo-placeholder id-card-photo-ring"
                             style="background:linear-gradient(135deg,#1a4ff5,#3671ff);">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Nama, Role, Divisi (centered) --}}
                <div class="id-card-info">
                    <p class="id-card-name">{{ $user->name }}</p>
                    <div>
                        <span class="id-card-badge">
                            {{ $user->role_label ?? ($user->roles->first()?->name ?? 'Anggota') }}
                        </span>
                    </div>
                    <div style="margin-top:10px;">
                        <div class="id-card-divisi-row">
                            <span class="id-card-divisi-label">Divisi</span>
                            <span class="id-card-divisi-value">{{ $user->divisi?->nama ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="id-card-divider"></div>

                {{-- QR Code (centered) --}}
                <div class="id-card-qr-wrap">
                    <span class="id-card-scan-label">Scan untuk verifikasi</span>
                    <div class="id-card-qr">
                        {!! $qrCode !!}
                    </div>
                </div>

            </div>{{-- end .id-card-content --}}

            {{-- Footer --}}
            <div class="id-card-footer">
                <div class="id-card-footer-text">
                    UKM MCI · Kartu Anggota Resmi · {{ date('Y') }}
                </div>
            </div>

        </div>
    </div>
</body>
</html>
