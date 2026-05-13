<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Border ID Card — UKM MCI</title>
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

        /* ── Header ── */
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
        .tg-title {
            font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: #0f172a;
        }
        .tg-download {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 20px; border-radius: 10px;
            background: #1a4ff5; color: #fff;
            font-size: 13px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer;
            transition: background .2s;
        }
        .tg-download:hover { background: #1340e1; }

        /* ── Layout ── */
        .tg-body {
            max-width: 1100px; margin: 40px auto; padding: 0 32px;
            display: grid; grid-template-columns: 380px 1fr; gap: 48px;
            align-items: start;
        }
        @media (max-width: 800px) {
            .tg-body { grid-template-columns: 1fr; }
        }

        /* ── Card preview panel ── */
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
        .tg-preview-wrap svg {
            width: 100%; max-width: 300px; height: auto;
            filter: drop-shadow(0 8px 24px rgba(0,0,0,.10));
        }
        .tg-preview-note {
            margin-top: 12px; font-size: 11px; color: #94a3b8; line-height: 1.5;
        }

        /* ── Instructions panel ── */
        .tg-info-section { margin-bottom: 32px; }
        .tg-info-section h2 {
            font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700;
            color: #0f172a; margin-bottom: 16px;
            padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;
        }

        /* Zone table */
        .tg-zone-table { width: 100%; border-collapse: collapse; }
        .tg-zone-table th {
            text-align: left; font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .06em; color: #64748b;
            padding: 8px 12px; background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
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
        .badge-caution {
            display: inline-block; padding: 2px 8px; border-radius: 999px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            background: #fef3c7; color: #b45309; border: 1px solid #fde68a;
        }
        .badge-warn {
            display: inline-block; padding: 2px 8px; border-radius: 999px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;
        }

        /* Steps */
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

        /* Tips */
        .tg-tips { display: flex; flex-direction: column; gap: 10px; }
        .tg-tip {
            display: flex; gap: 10px; align-items: flex-start;
            padding: 10px 14px; border-radius: 10px;
            background: #f0f9ff; border: 1px solid #bae6fd;
            font-size: 13px; color: #0c4a6e;
        }
        .tg-tip-icon { flex-shrink: 0; font-size: 16px; }
        .tg-tip-warn {
            background: #fff7ed; border-color: #fed7aa; color: #7c2d12;
        }
        .tg-tip-ok {
            background: #f0fdf4; border-color: #bbf7d0; color: #14532d;
        }

        /* Spec box */
        .tg-spec-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        }
        .tg-spec {
            padding: 12px 14px; border-radius: 10px;
            background: #f8fafc; border: 1px solid #e2e8f0;
        }
        .tg-spec-key { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
        .tg-spec-val { font-size: 14px; font-weight: 600; color: #0f172a; }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="tg-header">
        <div class="tg-header-left">
            <a href="javascript:history.back()" class="tg-back">← Kembali</a>
            <span class="tg-title">Panduan Desain Border ID Card</span>
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

    <!-- Body -->
    <div class="tg-body">

        <!-- Left: SVG template preview -->
        <div class="tg-preview-wrap">
            <p class="tg-preview-label">Template Layout ID Card</p>

            {{-- Inline the SVG template for preview (300×534px vertical layout) --}}
            <svg width="300" height="548" viewBox="0 0 300 548" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <pattern id="hatch-blue-p" patternUnits="userSpaceOnUse" width="8" height="8" patternTransform="rotate(45)">
                  <rect width="8" height="8" fill="#dbeafe"/>
                  <line x1="0" y1="0" x2="0" y2="8" stroke="#93c5fd" stroke-width="3"/>
                </pattern>
                <pattern id="hatch-side-p" patternUnits="userSpaceOnUse" width="6" height="6" patternTransform="rotate(45)">
                  <rect width="6" height="6" fill="#fef3c7"/>
                  <line x1="0" y1="0" x2="0" y2="6" stroke="#fcd34d" stroke-width="2.5"/>
                </pattern>
                <clipPath id="card-p">
                  <rect width="300" height="534" rx="14"/>
                </clipPath>
              </defs>

              <g clip-path="url(#card-p)">

                <!-- ZONE 1 — HEADER (y:0–64) -->
                <rect x="0" y="0" width="300" height="64" fill="url(#hatch-blue-p)"/>
                <circle cx="34" cy="32" r="17" fill="none" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4,2"/>
                <text x="34" y="38" text-anchor="middle" font-family="Arial" font-size="11" font-weight="bold" fill="#2563eb">M</text>
                <rect x="58" y="21" width="110" height="7"  rx="3" fill="rgba(59,130,246,.25)"/>
                <rect x="58" y="34" width="175" height="11" rx="3" fill="rgba(59,130,246,.35)"/>
                <text x="150" y="57" text-anchor="middle" font-family="Arial" font-size="8.5" font-weight="700" fill="#1d4ed8">
                  ✓ HEADER — BEBAS DESAIN (64px)
                </text>

                <!-- SIDE STRIPS (x:0–8 dan x:292–300, y:64–524) -->
                <rect x="0"   y="64" width="8" height="460" fill="url(#hatch-side-p)"/>
                <rect x="292" y="64" width="8" height="460" fill="url(#hatch-side-p)"/>

                <!-- ZONE 2 — FOTO (x:8–292, y:64–196) -->
                <rect x="8" y="64" width="284" height="132" fill="white"/>
                <rect x="102" y="86" width="96" height="96" rx="14"
                      fill="#eff6ff" stroke="#93c5fd" stroke-width="1.5" stroke-dasharray="4,2"/>
                <text x="150" y="131" text-anchor="middle" font-family="Arial" font-size="9"   fill="#3b82f6">📷 FOTO</text>
                <text x="150" y="144" text-anchor="middle" font-family="Arial" font-size="7.5" fill="#93c5fd">96 × 96 px</text>
                <text x="150" y="155" text-anchor="middle" font-family="Arial" font-size="7"   fill="#bfdbfe">border 3px biru</text>

                <!-- ZONE 3 — INFO (x:8–292, y:196–317) -->
                <rect x="8" y="196" width="284" height="121" fill="white"/>
                <rect x="65" y="208" width="170" height="16" rx="4" fill="#f1f5f9"/>
                <text x="150" y="220" text-anchor="middle" font-family="Arial" font-size="8" fill="#94a3b8">NAMA ANGGOTA</text>
                <rect x="105" y="232" width="90" height="18" rx="9" fill="#dbeafe" stroke="#bfdbfe" stroke-width="1"/>
                <text x="150" y="245" text-anchor="middle" font-family="Arial" font-size="8" fill="#1d4ed8">JABATAN</text>
                <rect x="72" y="258" width="156" height="24" rx="8" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1"/>
                <text x="102" y="275" text-anchor="middle" font-family="Arial" font-size="7" fill="#94a3b8">DIVISI</text>
                <line x1="115" y1="262" x2="115" y2="278" stroke="#e2e8f0" stroke-width="1"/>
                <text x="185" y="275" text-anchor="middle" font-family="Arial" font-size="8.5" fill="#334155">Nama Divisi</text>
                <text x="150" y="308" text-anchor="middle" font-family="Arial" font-size="7.5" font-weight="700" fill="#dc2626">
                  ⚠  ZONA KONTEN — 97% PUTIH
                </text>

                <!-- DIVIDER (y:317) -->
                <rect x="0"   y="317" width="8" height="1" fill="url(#hatch-side-p)"/>
                <rect x="292" y="317" width="8" height="1" fill="url(#hatch-side-p)"/>
                <line x1="8" y1="317" x2="292" y2="317" stroke="#e2e8f0" stroke-width="1"/>

                <!-- ZONE 4 — QR CODE (x:8–292, y:318–496) -->
                <rect x="8"   y="318" width="284" height="178" fill="white"/>
                <rect x="0"   y="318" width="8"   height="178" fill="url(#hatch-side-p)"/>
                <rect x="292" y="318" width="8"   height="178" fill="url(#hatch-side-p)"/>
                <text x="150" y="342" text-anchor="middle" font-family="Arial" font-size="8" fill="#94a3b8" font-weight="600"
                      letter-spacing="1">SCAN UNTUK VERIFIKASI</text>
                <rect x="90" y="352" width="120" height="120" rx="8" fill="#f8fafc" stroke="#94a3b8" stroke-width="1.5"/>
                <rect x="98"  y="360" width="22" height="22" rx="2" fill="#334155"/>
                <rect x="124" y="360" width="22" height="22" rx="2" fill="#334155"/>
                <rect x="150" y="360" width="22" height="22" rx="2" fill="#334155"/>
                <rect x="98"  y="386" width="8"  height="8"  rx="1" fill="#334155"/>
                <rect x="110" y="386" width="8"  height="8"  rx="1" fill="#334155"/>
                <rect x="122" y="386" width="8"  height="8"  rx="1" fill="#334155"/>
                <rect x="150" y="386" width="22" height="22" rx="2" fill="#334155"/>
                <rect x="98"  y="398" width="22" height="22" rx="2" fill="#334155"/>
                <text x="150" y="468" text-anchor="middle" font-family="Arial" font-size="8"   fill="#94a3b8">QR Code · 120 × 120 px</text>
                <text x="150" y="488" text-anchor="middle" font-family="Arial" font-size="7.5" font-weight="700" fill="#dc2626">
                  ⚠  ZONA KONTEN — 97% PUTIH
                </text>

                <!-- ZONE 5 — FOOTER (x:8–292, y:496–524) -->
                <rect x="8"   y="496" width="284" height="28" fill="#f8fafc"/>
                <rect x="0"   y="496" width="8"   height="28" fill="url(#hatch-side-p)"/>
                <rect x="292" y="496" width="8"   height="28" fill="url(#hatch-side-p)"/>
                <line x1="8" y1="496" x2="292" y2="496" stroke="#f1f5f9" stroke-width="1"/>
                <text x="150" y="514" text-anchor="middle" font-family="Arial" font-size="8" fill="#94a3b8">
                  UKM MCI · Kartu Anggota Resmi · 2025
                </text>

                <!-- Bottom visible strip -->
                <rect x="0" y="524" width="300" height="10" fill="url(#hatch-side-p)"/>

              </g>

              <!-- Card border outline -->
              <rect x="0.75" y="0.75" width="298.5" height="532.5" rx="13.5"
                    fill="none" stroke="#334155" stroke-width="1.5"/>

              <!-- Dimension annotation -->
              <line x1="0" y1="537" x2="300" y2="537" stroke="#64748b" stroke-width="0.75"/>
              <line x1="0"   y1="534" x2="0"   y2="540" stroke="#64748b" stroke-width="0.75"/>
              <line x1="300" y1="534" x2="300" y2="540" stroke="#64748b" stroke-width="0.75"/>
              <text x="150" y="548" text-anchor="middle" font-family="Arial" font-size="8.5" fill="#475569">300 px</text>
            </svg>

            <p class="tg-preview-note">
                <span style="display:inline-block;width:12px;height:12px;background:#dbeafe;border:1px solid #93c5fd;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Biru = bebas desain &nbsp;
                <span style="display:inline-block;width:12px;height:12px;background:#fef3c7;border:1px solid #fcd34d;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Kuning = strip border &nbsp;
                <span style="display:inline-block;width:12px;height:12px;background:#fff;border:1px solid #e2e8f0;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Putih = zona konten
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
                            <td><span class="badge-ok">Bebas Desain</span></td>
                            <td>Background tampil ~70%. Sistem menambahkan dark overlay 30% saat mode background image aktif. Gunakan warna/gambar menarik — teks putih tetap terbaca.</td>
                        </tr>
                        <tr>
                            <td><strong>Strip Kiri/Kanan</strong></td>
                            <td>8 px × 2</td>
                            <td><span class="badge-ok">Bebas Desain</span></td>
                            <td>Background image terlihat penuh di sisi kiri dan kanan sebagai border frame. Letakkan elemen dekoratif di sini.</td>
                        </tr>
                        <tr>
                            <td><strong>Foto</strong> (terpusat)</td>
                            <td>132 px</td>
                            <td><span class="badge-warn">97% Putih</span></td>
                            <td>Panel putih menutup hampir seluruh area ini. Foto anggota 96×96px ditampilkan terpusat.</td>
                        </tr>
                        <tr>
                            <td><strong>Info</strong> (Nama · Role · Divisi)</td>
                            <td>121 px</td>
                            <td><span class="badge-warn">97% Putih</span></td>
                            <td>Panel putih menutup area ini. Berisi nama, badge jabatan, dan divisi — semuanya terpusat.</td>
                        </tr>
                        <tr>
                            <td><strong>QR Code</strong> (terpusat)</td>
                            <td>178 px</td>
                            <td><span class="badge-warn">97% Putih</span></td>
                            <td>QR code 120×120px dan label scan. Panel putih menutup area ini.</td>
                        </tr>
                        <tr>
                            <td><strong>Footer</strong></td>
                            <td>28 px</td>
                            <td><span class="badge-warn">97% Putih</span></td>
                            <td>Teks organisasi. Panel putih menutup area ini.</td>
                        </tr>
                        <tr>
                            <td><strong>Strip Bawah</strong></td>
                            <td>10 px</td>
                            <td><span class="badge-ok">Bebas Desain</span></td>
                            <td>Background image terlihat di bawah kartu.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cara penggunaan -->
            <div class="tg-info-section">
                <h2>Cara Penggunaan Template</h2>
                <ol class="tg-steps">
                    <li>
                        <div class="tg-step-num">1</div>
                        <div>
                            <strong>Download template SVG</strong> di tombol kanan atas halaman ini.
                            <small>File SVG berisi kerangka zona yang sudah diposisikan sesuai layout asli ID card.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">2</div>
                        <div>
                            <strong>Buka di Canva / Figma / Photoshop</strong>
                            <small>Di Canva: klik Upload → pilih SVG. Di Figma: File → Import. Di Photoshop: Open as Smart Object.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">3</div>
                        <div>
                            <strong>Posisikan template sebagai layer paling atas</strong>
                            <small>Template berfungsi sebagai panduan — tampilkan di atas layer desain Anda agar Anda bisa melihat batas zona konten.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">4</div>
                        <div>
                            <strong>Desain border di layer bawah</strong>
                            <small>Fokus hiasan di HEADER (atas 64px), STRIP SAMPING (kiri/kanan 8px), dan STRIP BAWAH (10px). Area putih akan tertutup panel konten.</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">5</div>
                        <div>
                            <strong>Sembunyikan/hapus layer template, lalu ekspor</strong>
                            <small>Export sebagai PNG/JPG ukuran 300×534px (atau kelipatan: 600×1068px untuk 2x).</small>
                        </div>
                    </li>
                    <li>
                        <div class="tg-step-num">6</div>
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
                            <strong>Header yang kuat</strong><br>
                            Beri warna gradien, gambar, atau pola menarik di area header (64px teratas). Ini adalah area paling visible — tampil 70% tanpa overlay.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-ok">
                        <span class="tg-tip-icon">✅</span>
                        <div>
                            <strong>Dekorasi di strip samping &amp; bawah</strong><br>
                            Strip kiri-kanan (8px) dan bawah (10px) terlihat penuh. Tambahkan ornamen, garis, atau motif di sini untuk efek frame yang indah.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-ok">
                        <span class="tg-tip-icon">✅</span>
                        <div>
                            <strong>Area body: putih bersih atau gradien halus</strong><br>
                            Di area body/fields/QR, gunakan background putih atau gradien sangat halus ke putih. Warna gelap di sini akan terlihat sedikit (3%) dan bisa mengganggu keterbacaan teks.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-warn">
                        <span class="tg-tip-icon">⚠️</span>
                        <div>
                            <strong>Hindari teks atau logo di area body</strong><br>
                            Teks atau logo yang Anda taruh di area konten (bawah header) hampir tidak terlihat karena tertutup panel putih 97%.
                        </div>
                    </div>
                    <div class="tg-tip tg-tip-warn">
                        <span class="tg-tip-icon">⚠️</span>
                        <div>
                            <strong>Ukuran harus tepat</strong><br>
                            Pastikan ukuran ekspor adalah <strong>300×534px</strong> (atau kelipatan persisnya: 600×1068, 900×1602). Jika ukuran berbeda, gambar akan stretch dan zona tidak sesuai.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
