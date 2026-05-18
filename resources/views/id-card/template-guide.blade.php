<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Desain Background ID Card — UKM MCI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: 'DM Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }

        .tg-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 32px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
        }
        .tg-header-left { display: flex; align-items: center; gap: 12px; }
        .tg-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            font-size: 13px; font-weight: 500; color: #475569;
            text-decoration: none;
        }
        .tg-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: #0f172a; }
        .tg-download {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 20px; border-radius: 10px;
            background: #1a4ff5; color: #fff;
            font-size: 13px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer;
            transition: background .2s;
        }
        .tg-download:hover { background: #1340e1; }

        .tg-body {
            max-width: 1100px; margin: 40px auto; padding: 0 32px;
            display: grid; grid-template-columns: 380px 1fr; gap: 48px;
            align-items: start;
        }
        @media (max-width: 800px) { .tg-body { grid-template-columns: 1fr; } }

        .tg-preview-wrap {
            background: #fff; border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 24px; text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,.06);
        }
        .tg-preview-label {
            font-size: 11px; font-weight: 600; letter-spacing: .06em;
            text-transform: uppercase; color: #94a3b8; margin-bottom: 16px;
        }
        .tg-preview-wrap svg { width: 100%; max-width: 300px; height: auto; filter: drop-shadow(0 8px 24px rgba(0,0,0,.12)); }
        .tg-preview-note { margin-top: 14px; font-size: 11px; color: #94a3b8; line-height: 1.6; }
        .tg-legend-dot {
            display: inline-block; width: 12px; height: 12px;
            border-radius: 2px; vertical-align: middle; margin-right: 4px;
        }

        .tg-info-section { margin-bottom: 32px; }
        .tg-info-section h2 {
            font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700;
            color: #0f172a; margin-bottom: 16px;
            padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;
        }

        .tg-zone-table { width: 100%; border-collapse: collapse; }
        .tg-zone-table th {
            text-align: left; font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .06em; color: #64748b;
            padding: 8px 12px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        .tg-zone-table td {
            padding: 10px 12px; font-size: 13px; color: #374151;
            border-bottom: 1px solid #f1f5f9; vertical-align: top;
        }
        .tg-zone-table tr:last-child td { border-bottom: none; }
        .badge-ok {
            display: inline-block; padding: 2px 8px; border-radius: 999px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;
        }
        .badge-frosted {
            display: inline-block; padding: 2px 8px; border-radius: 999px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe;
        }
        .badge-overlay {
            display: inline-block; padding: 2px 8px; border-radius: 999px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            background: #fef3c7; color: #b45309; border: 1px solid #fde68a;
        }

        .tg-steps { list-style: none; }
        .tg-steps li {
            display: flex; gap: 14px; align-items: flex-start;
            padding: 12px 0; border-bottom: 1px solid #f1f5f9; font-size: 13.5px;
        }
        .tg-steps li:last-child { border-bottom: none; }
        .tg-step-num {
            width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
            background: #1a4ff5; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; margin-top: 1px;
        }
        .tg-steps li strong { font-weight: 600; }
        .tg-steps li small { display: block; color: #64748b; font-size: 12px; margin-top: 2px; }

        .tg-tips { display: flex; flex-direction: column; gap: 10px; }
        .tg-tip {
            display: flex; gap: 10px; align-items: flex-start;
            padding: 10px 14px; border-radius: 10px;
            background: #f0f9ff; border: 1px solid #bae6fd;
            font-size: 13px; color: #0c4a6e;
        }
        .tg-tip-icon { flex-shrink: 0; font-size: 16px; }
        .tg-tip-warn { background: #fff7ed; border-color: #fed7aa; color: #7c2d12; }
        .tg-tip-ok   { background: #f0fdf4; border-color: #bbf7d0; color: #14532d; }
        .tg-tip-purple { background: #f5f3ff; border-color: #ddd6fe; color: #4c1d95; }

        .tg-spec-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .tg-spec { padding: 12px 14px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .tg-spec-key { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
        .tg-spec-val { font-size: 14px; font-weight: 600; color: #0f172a; }
    </style>
</head>

<body>

    <div class="tg-header">
        <div class="tg-header-left">
            <a href="javascript:window.close()" class="tg-back">← Tutup Tab</a>
            <span class="tg-title">Panduan Desain Background ID Card</span>
        </div>
        <a href="/templates/id-card-border-template.svg"
           download="id-card-border-template.svg"
           class="tg-download">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Template SVG
        </a>
    </div>

    <div class="tg-body">

        <!-- Left: SVG preview -->
        <div class="tg-preview-wrap">
            <p class="tg-preview-label">Layout ID Card — Mode Background Image</p>

            <svg width="300" height="548" viewBox="0 0 300 548" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <!-- Background gradient simulasi background image -->
                <linearGradient id="bg-grad" x1="0" y1="0" x2="300" y2="534" gradientUnits="userSpaceOnUse">
                  <stop offset="0%"   stop-color="#1a4ff5"/>
                  <stop offset="50%"  stop-color="#8b5cf6"/>
                  <stop offset="100%" stop-color="#0ff4c6"/>
                </linearGradient>
                <!-- Frosted glass pattern -->
                <filter id="blur-sm">
                  <feGaussianBlur stdDeviation="2"/>
                </filter>
                <clipPath id="card-clip">
                  <rect width="300" height="534" rx="14"/>
                </clipPath>
              </defs>

              <g clip-path="url(#card-clip)">

                <!-- FULL BACKGROUND — tampil di seluruh kartu -->
                <rect x="0" y="0" width="300" height="534" fill="url(#bg-grad)"/>

                <!-- Blob dekoratif (simulasi background image) -->
                <ellipse cx="240" cy="80"  rx="90" ry="70"  fill="rgba(255,255,255,0.08)"/>
                <ellipse cx="60"  cy="400" rx="80" ry="100" fill="rgba(255,255,255,0.06)"/>
                <ellipse cx="280" cy="320" rx="60" ry="80"  fill="rgba(15,244,198,0.12)"/>

                <!-- ZONA 1: HEADER — dark overlay 45% (y:0–64) -->
                <rect x="0" y="0" width="300" height="64"
                      fill="url(#bg-grad)" opacity="0.5"/>
                <rect x="0" y="0" width="300" height="64"
                      fill="rgba(0,0,0,0.45)"/>
                <!-- Logo box -->
                <rect x="18" y="15" width="34" height="34" rx="9" fill="rgba(255,255,255,0.22)"/>
                <text x="35" y="37" text-anchor="middle" font-family="Arial" font-size="13" font-weight="800" fill="#fff">M</text>
                <!-- Header text placeholders -->
                <rect x="60" y="21" width="90" height="6"  rx="3" fill="rgba(255,255,255,0.5)"/>
                <rect x="60" y="33" width="160" height="9" rx="3" fill="rgba(255,255,255,0.8)"/>
                <!-- Label -->
                <text x="150" y="58" text-anchor="middle" font-family="Arial" font-size="7.5" font-weight="700" fill="rgba(255,255,255,0.9)">
                  ✓ HEADER — DARK OVERLAY 45% (64px)
                </text>

                <!-- ZONA 2: FOTO — background image langsung (y:64–196) -->
                <!-- Tidak ada panel putih — background kelihatan -->
                <rect x="102" y="82" width="96" height="96" rx="14"
                      fill="rgba(30,41,59,0.3)"
                      stroke="rgba(255,255,255,0.9)" stroke-width="3"/>
                <text x="150" y="129" text-anchor="middle" font-family="Arial" font-size="9" fill="rgba(255,255,255,0.9)">📷 FOTO</text>
                <text x="150" y="141" text-anchor="middle" font-family="Arial" font-size="7" fill="rgba(255,255,255,0.6)">96×96px · border putih</text>
                <!-- Drop shadow indicator -->
                <text x="150" y="157" text-anchor="middle" font-family="Arial" font-size="7" fill="rgba(255,255,255,0.5)">box-shadow gelap</text>
                <!-- Label zona -->
                <text x="150" y="186" text-anchor="middle" font-family="Arial" font-size="7" font-weight="700" fill="rgba(255,255,255,0.75)">
                  BACKGROUND BEBAS TERLIHAT
                </text>

                <!-- ZONA 3: INFO — frosted glass per elemen (y:196–320) -->
                <!-- Nama: teks putih dengan text-shadow -->
                <rect x="60" y="200" width="180" height="16" rx="4" fill="rgba(0,0,0,0.25)"/>
                <text x="150" y="213" text-anchor="middle" font-family="Arial" font-size="8.5" font-weight="700" fill="#fff">NAMA ANGGOTA</text>
                <!-- Badge jabatan: frosted glass -->
                <rect x="96" y="224" width="108" height="18" rx="9"
                      fill="rgba(255,255,255,0.18)"
                      stroke="rgba(255,255,255,0.35)" stroke-width="1"/>
                <text x="150" y="237" text-anchor="middle" font-family="Arial" font-size="8" fill="#fff">JABATAN (frosted)</text>
                <!-- Divisi row: frosted glass -->
                <rect x="72" y="252" width="156" height="22" rx="8"
                      fill="rgba(255,255,255,0.18)"
                      stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
                <text x="96"  y="267" text-anchor="middle" font-family="Arial" font-size="7"   fill="rgba(255,255,255,0.7)">DIVISI</text>
                <line x1="112" y1="256" x2="112" y2="270" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
                <text x="185" y="267" text-anchor="middle" font-family="Arial" font-size="8"   fill="#fff">Nama Divisi</text>
                <text x="150" y="305" text-anchor="middle" font-family="Arial" font-size="7" font-weight="700" fill="rgba(255,255,255,0.75)">
                  BACKGROUND BEBAS TERLIHAT
                </text>

                <!-- DIVIDER: garis putih transparan (y:318) -->
                <line x1="24" y1="318" x2="276" y2="318" stroke="rgba(255,255,255,0.25)" stroke-width="1"/>

                <!-- ZONA 4: QR CODE — frosted glass kecil hanya di sekitar QR (y:318–496) -->
                <!-- Label scan -->
                <text x="150" y="338" text-anchor="middle" font-family="Arial" font-size="7.5" font-weight="600"
                      fill="rgba(255,255,255,0.75)" letter-spacing="1">SCAN UNTUK VERIFIKASI</text>
                <!-- QR box: frosted white -->
                <rect x="82" y="350" width="136" height="136" rx="12"
                      fill="rgba(255,255,255,0.92)"
                      stroke="none"/>
                <!-- QR pattern simulasi -->
                <rect x="94"  y="362" width="24" height="24" rx="2" fill="#334155"/>
                <rect x="122" y="362" width="24" height="24" rx="2" fill="#334155"/>
                <rect x="150" y="362" width="24" height="24" rx="2" fill="#334155"/>
                <rect x="94"  y="390" width="8"  height="8"  rx="1" fill="#334155"/>
                <rect x="106" y="390" width="8"  height="8"  rx="1" fill="#334155"/>
                <rect x="118" y="390" width="8"  height="8"  rx="1" fill="#334155"/>
                <rect x="150" y="390" width="24" height="24" rx="2" fill="#334155"/>
                <rect x="94"  y="402" width="24" height="24" rx="2" fill="#334155"/>
                <text x="150" y="460" text-anchor="middle" font-family="Arial" font-size="7.5" fill="#64748b">QR · 120×120px</text>
                <text x="150" y="488" text-anchor="middle" font-family="Arial" font-size="7" font-weight="700" fill="rgba(255,255,255,0.75)">
                  BACKGROUND BEBAS TERLIHAT
                </text>

                <!-- ZONA 5: FOOTER — transparan (y:496–524) -->
                <line x1="0" y1="496" x2="300" y2="496" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                <text x="150" y="514" text-anchor="middle" font-family="Arial" font-size="8" fill="rgba(255,255,255,0.55)">
                  UKM MCI · Kartu Anggota Resmi · 2025
                </text>
                <text x="150" y="527" text-anchor="middle" font-family="Arial" font-size="7" font-weight="700" fill="rgba(255,255,255,0.6)">
                  FOOTER TRANSPARAN
                </text>

              </g>

              <!-- Card border -->
              <rect x="0.75" y="0.75" width="298.5" height="532.5" rx="13.5"
                    fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="1.5"/>

              <!-- Dimension annotation -->
              <line x1="0" y1="537" x2="300" y2="537" stroke="#64748b" stroke-width="0.75"/>
              <line x1="0"   y1="534" x2="0"   y2="540" stroke="#64748b" stroke-width="0.75"/>
              <line x1="300" y1="534" x2="300" y2="540" stroke="#64748b" stroke-width="0.75"/>
              <text x="150" y="548" text-anchor="middle" font-family="Arial" font-size="8.5" fill="#475569">300 px</text>
            </svg>

            <p class="tg-preview-note">
                <span class="tg-legend-dot" style="background:linear-gradient(135deg,#1a4ff5,#8b5cf6);"></span>Background image tampil di seluruh kartu &nbsp;
                <span class="tg-legend-dot" style="background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.4);"></span>Frosted glass per elemen &nbsp;
                <span class="tg-legend-dot" style="background:rgba(255,255,255,0.92);border:1px solid #e2e8f0;"></span>Putih hanya di sekitar QR
            </p>
        </div>

        <!-- Right: Instructions -->
        <div>

            <!-- Spesifikasi -->
            <div class="tg-info-section">
                <h2>Spesifikasi Gambar</h2>
                <div class="tg-spec-grid">
                    <div class="tg-spec">
                        <div class="tg-spec-key">Lebar</div>
                        <div class="tg-spec-val">300 px</div>
                    </div>
                    <div class="tg-spec">
                        <div class="tg-spec-key">Tinggi</div>
                        <div class="tg-spec-val">≥ 534 px</div>
                    </div>
                    <div class="tg-spec">
                        <div class="tg-spec-key">Format Export</div>
                        <div class="tg-spec-val">PNG / JPG</div>
                    </div>
                    <div class="tg-spec">
                        <div class="tg-spec-key">Ukuran File Maks</div>
                        <div class="tg-spec-val">5 MB</div>
                    </div>
                </div>
            </div>

            <!-- Zona -->
            <div class="tg-info-section">
                <h2>Zona ID Card</h2>
                <table class="tg-zone-table">
                    <thead>
                        <tr>
                            <th>Zona</th>
                            <th>Tinggi</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Header</strong></td>
                            <td>64 px</td>
                            <td><span class="badge-overlay">Dark Overlay</span></td>
                            <td>Background tampil dengan dark overlay 45% di atasnya agar teks logo dan nama organisasi tetap terbaca. Desain bebas — warna cerah sangat dianjurkan di area ini.</td>
                        </tr>
                        <tr>
                            <td><strong>Foto</strong></td>
                            <td>~132 px</td>
                            <td><span class="badge-ok">Background Penuh</span></td>
                            <td>Background image tampil bebas. Foto anggota memiliki border putih 3px + drop shadow gelap agar menonjol di atas background apapun.</td>
                        </tr>
                        <tr>
                            <td><strong>Nama · Jabatan · Divisi</strong></td>
                            <td>~120 px</td>
                            <td><span class="badge-ok">Background Penuh</span></td>
                            <td>Background tampil bebas. Nama menggunakan teks putih dengan text-shadow tebal. Badge jabatan dan divisi menggunakan <em>frosted glass</em> — putih transparan 18% + blur.</td>
                        </tr>
                        <tr>
                            <td><strong>Divider</strong></td>
                            <td>1 px</td>
                            <td><span class="badge-ok">Background Penuh</span></td>
                            <td>Garis putih transparan tipis (rgba 25%). Background tetap terlihat.</td>
                        </tr>
                        <tr>
                            <td><strong>QR Code</strong></td>
                            <td>~178 px</td>
                            <td><span class="badge-frosted">Frosted QR</span></td>
                            <td>Background tampil bebas di seluruh area. Hanya di sekitar QR code ada kotak putih 92% dengan border-radius — cukup untuk keterbacaan scanner. Area di luar kotak QR tetap terlihat background-nya.</td>
                        </tr>
                        <tr>
                            <td><strong>Footer</strong></td>
                            <td>28 px</td>
                            <td><span class="badge-ok">Transparan</span></td>
                            <td>Background tampil penuh. Teks footer putih 55% opacity, border-top putih 20% opacity.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cara penggunaan -->
            <div class="tg-info-section">
                <h2>Cara Membuat Background</h2>
                <ol class="tg-steps">
                    <li>
                        <div class="tg-step-num">1</div>
                        <div>
                            <strong>Download template SVG</strong> di tombol kanan atas.
                            <small>SVG berisi kerangka zona layout sesuai posisi asli ID card.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">2</div>
                        <div>
                            <strong>Buka di Canva / Figma / Photoshop</strong>
                            <small>Di Canva: Upload → pilih SVG. Di Figma: File → Import. Di Photoshop: Open as Smart Object.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">3</div>
                        <div>
                            <strong>Desain background bebas di seluruh area kartu</strong>
                            <small>Background sekarang tampil di seluruh kartu — gunakan gradien, foto, tekstur, atau ilustrasi di semua area. Tidak ada zona yang tertutup panel putih besar.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">4</div>
                        <div>
                            <strong>Perhatikan kontras di area teks</strong>
                            <small>Nama anggota ditampilkan dengan teks putih + text-shadow. Pastikan background tidak terlalu terang di area tengah (nama, jabatan, divisi) agar teks tetap terbaca.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">5</div>
                        <div>
                            <strong>Area QR: hindari pola terlalu ramai</strong>
                            <small>QR code punya kotak putih kecil di sekitarnya, tapi background di luar kotak itu tetap terlihat. Pola atau warna terlalu padat di area QR bisa mengurangi estetika.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">6</div>
                        <div>
                            <strong>Hapus layer template, ekspor PNG/JPG 300×534px</strong>
                            <small>Kelipatan yang diizinkan: 600×1068px (2×), 900×1602px (3×).</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">7</div>
                        <div>
                            <strong>Upload di Admin Panel → Template ID Card</strong>
                            <small>Klik "Ganti Background Image" → upload hasil desain Anda.</small>
                        </div>
                    </li>
                </ol>
            </div>

            <!-- Tips -->
            <div class="tg-info-section">
                <h2>Tips Desain</h2>
                <div class="tg-tips">
                    <div class="tg-tip tg-tip-ok">
                        <span class="tg-tip-icon">✅</span>
                        <div>
                            <strong>Background dominan — bebas di seluruh kartu</strong><br>
                            Background image sekarang tampil dari atas ke bawah tanpa tertutup panel putih besar. Manfaatkan ini dengan gradien kaya, foto, atau ilustrasi yang mengisi seluruh kartu.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-ok">
                        <span class="tg-tip-icon">✅</span>
                        <div>
                            <strong>Warna gelap atau sedang = kontras terbaik</strong><br>
                            Karena teks nama menggunakan warna putih dengan text-shadow, background gelap (navy, ungu, hijau tua) akan membuat teks paling terbaca dan estetis.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-purple">
                        <span class="tg-tip-icon">🔮</span>
                        <div>
                            <strong>Frosted glass otomatis di elemen konten</strong><br>
                            Badge jabatan dan row divisi menggunakan <em>backdrop-filter blur</em> — tampil seperti kaca buram transparan di atas background Anda. Tidak perlu khawatir soal ini, sistem menanganinya otomatis.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-warn">
                        <span class="tg-tip-icon">⚠️</span>
                        <div>
                            <strong>Hindari background terlalu terang di area tengah</strong><br>
                            Teks nama, jabatan, dan divisi tampil putih. Jika background di area tengah terlalu terang (putih, kuning pucat), teks akan sulit terbaca. Gunakan gradien dari gelap ke terang atau warna solid yang kontras.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-warn">
                        <span class="tg-tip-icon">⚠️</span>
                        <div>
                            <strong>Ukuran harus tepat: 300×534px</strong><br>
                            Jika ukuran berbeda, gambar akan stretch dan zona tidak sesuai. Kelipatan yang aman: 600×1068px atau 900×1602px.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
