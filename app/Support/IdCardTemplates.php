<?php

namespace App\Support;

class IdCardTemplates
{
    /**
     * Semua template border yang tersedia.
     * Setiap template mendefinisikan CSS yang di-inject ke halaman ID card.
     */
    public static function all(): array
    {
        return [
            'biru-klasik'   => self::biruKlasik(),
            'teknologi'     => self::teknologi(),
            'elegan-emas'   => self::eleganEmas(),
            'ungu-gradien'  => self::unguGradien(),
            'merah-energi'  => self::merahEnergi(),
            'hijau-alam'    => self::hijauAlam(),
        ];
    }

    public static function find(string $slug): array
    {
        return self::all()[$slug] ?? self::biruKlasik();
    }

    public static function options(): array
    {
        return collect(self::all())->map(fn($t) => $t['label'])->all();
    }

    // ── Template Definitions ──────────────────────────────────────

    private static function biruKlasik(): array
    {
        return [
            'slug'        => 'biru-klasik',
            'label'       => 'Biru Klasik',
            'preview_bg'  => 'linear-gradient(135deg,#1a4ff5,#3671ff)',
            'css'         => '
                .id-card { background:#fff; border:3px solid #1a4ff5; border-radius:16px; }
                .id-card-header { background:linear-gradient(135deg,#1340e1,#3671ff); }
                .id-card-accent { color:#1a4ff5; }
                .id-card-badge { background:#eef5ff; color:#1a4ff5; border:1px solid #bdd8ff; }
                .id-card-divider { background:#1a4ff5; }
                .id-card-corner-tl, .id-card-corner-tr,
                .id-card-corner-bl, .id-card-corner-br {
                    position:absolute; width:22px; height:22px;
                    border-color:#1a4ff5; border-style:solid;
                }
                .id-card-corner-tl { top:6px; left:6px; border-width:3px 0 0 3px; border-radius:4px 0 0 0; }
                .id-card-corner-tr { top:6px; right:6px; border-width:3px 3px 0 0; border-radius:0 4px 0 0; }
                .id-card-corner-bl { bottom:6px; left:6px; border-width:0 0 3px 3px; border-radius:0 0 0 4px; }
                .id-card-corner-br { bottom:6px; right:6px; border-width:0 3px 3px 0; border-radius:0 0 4px 0; }
            ',
        ];
    }

    private static function teknologi(): array
    {
        return [
            'slug'        => 'teknologi',
            'label'       => 'Teknologi',
            'preview_bg'  => 'linear-gradient(135deg,#0f172a,#1e293b)',
            'css'         => '
                .id-card {
                    background:#0f172a;
                    border:2px solid #0ff4c6;
                    border-radius:12px;
                    box-shadow:0 0 20px rgba(15,244,198,0.2), inset 0 0 40px rgba(15,244,198,0.03);
                    color:#e2e8f0;
                }
                .id-card-header { background:linear-gradient(135deg,#0c1445,#0f172a); border-bottom:1px solid rgba(15,244,198,0.3); }
                .id-card-header-title { color:#0ff4c6 !important; }
                .id-card-header-org { color:rgba(255,255,255,0.7) !important; }
                .id-card-label { color:rgba(255,255,255,0.4) !important; }
                .id-card-value { color:#e2e8f0 !important; }
                .id-card-accent { color:#0ff4c6; }
                .id-card-badge { background:rgba(15,244,198,0.1); color:#0ff4c6; border:1px solid rgba(15,244,198,0.3); }
                .id-card-divider { background:rgba(15,244,198,0.3); }
                .id-card-member-id { color:#0ff4c6 !important; }
                .id-card-photo-ring { border-color:#0ff4c6 !important; box-shadow:0 0 12px rgba(15,244,198,0.4); }
                .id-card-footer { border-top:1px solid rgba(15,244,198,0.2) !important; background:rgba(15,244,198,0.03); }
                .id-card-footer-text { color:rgba(255,255,255,0.3) !important; }
                /* Dot pattern */
                .id-card::before {
                    content:""; position:absolute; inset:0; border-radius:inherit; pointer-events:none;
                    background-image:radial-gradient(circle,rgba(15,244,198,0.15) 1px,transparent 1px);
                    background-size:18px 18px; z-index:0;
                }
                .id-card > * { position:relative; z-index:1; }
            ',
        ];
    }

    private static function eleganEmas(): array
    {
        return [
            'slug'        => 'elegan-emas',
            'label'       => 'Elegan Emas',
            'preview_bg'  => 'linear-gradient(135deg,#b45309,#f59e0b)',
            'css'         => '
                .id-card {
                    background:#fffbf0;
                    border:2px solid #f59e0b;
                    border-radius:14px;
                    outline:1px solid #fde68a;
                    outline-offset:4px;
                }
                .id-card-header { background:linear-gradient(135deg,#92400e,#b45309,#d97706); }
                .id-card-accent { color:#b45309; }
                .id-card-badge { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
                .id-card-divider { background:linear-gradient(90deg,#f59e0b,#fbbf24,#f59e0b); }
                .id-card-member-id { color:#b45309 !important; }
                .id-card-photo-ring { border-color:#f59e0b !important; }
                .id-card-corner-tl, .id-card-corner-tr,
                .id-card-corner-bl, .id-card-corner-br {
                    position:absolute; width:18px; height:18px;
                    border-color:#f59e0b; border-style:solid;
                }
                .id-card-corner-tl { top:8px; left:8px; border-width:2px 0 0 2px; }
                .id-card-corner-tr { top:8px; right:8px; border-width:2px 2px 0 0; }
                .id-card-corner-bl { bottom:8px; left:8px; border-width:0 0 2px 2px; }
                .id-card-corner-br { bottom:8px; right:8px; border-width:0 2px 2px 0; }
            ',
        ];
    }

    private static function unguGradien(): array
    {
        return [
            'slug'        => 'ungu-gradien',
            'label'       => 'Ungu Gradien',
            'preview_bg'  => 'linear-gradient(135deg,#4f46e5,#7c3aed)',
            'css'         => '
                .id-card {
                    background:#fff;
                    border-radius:16px;
                    border:0;
                    position:relative;
                }
                /* Gradient border via pseudo-element */
                .id-card::before {
                    content:""; position:absolute; inset:-3px; border-radius:18px; z-index:-1;
                    background:linear-gradient(135deg,#4f46e5,#7c3aed,#a855f7,#6366f1);
                }
                .id-card-header { background:linear-gradient(135deg,#3730a3,#4f46e5,#7c3aed); }
                .id-card-accent { color:#6d28d9; }
                .id-card-badge { background:#f5f3ff; color:#5b21b6; border:1px solid #ddd6fe; }
                .id-card-divider { background:linear-gradient(90deg,#4f46e5,#a855f7,#4f46e5); }
                .id-card-member-id { color:#6d28d9 !important; }
                .id-card-photo-ring { border-color:#7c3aed !important; }
            ',
        ];
    }

    private static function merahEnergi(): array
    {
        return [
            'slug'        => 'merah-energi',
            'label'       => 'Merah Energi',
            'preview_bg'  => 'linear-gradient(135deg,#be123c,#e11d48)',
            'css'         => '
                .id-card { background:#fff; border:3px solid #e11d48; border-radius:14px; }
                .id-card-header { background:linear-gradient(135deg,#9f1239,#be123c,#e11d48); }
                .id-card-accent { color:#be123c; }
                .id-card-badge { background:#fff1f2; color:#9f1239; border:1px solid #fecdd3; }
                .id-card-divider { background:linear-gradient(90deg,#e11d48,#fb7185,#e11d48); }
                .id-card-member-id { color:#be123c !important; }
                .id-card-photo-ring { border-color:#e11d48 !important; }
                /* Diagonal accent stripe */
                .id-card::after {
                    content:""; position:absolute; top:0; right:0; width:60px; height:60px;
                    background:linear-gradient(225deg,#fecdd3 0%,transparent 70%);
                    border-radius:0 14px 0 0; pointer-events:none;
                }
            ',
        ];
    }

    private static function hijauAlam(): array
    {
        return [
            'slug'        => 'hijau-alam',
            'label'       => 'Hijau Alam',
            'preview_bg'  => 'linear-gradient(135deg,#065f46,#059669)',
            'css'         => '
                .id-card { background:#fff; border:3px solid #059669; border-radius:16px; }
                .id-card-header { background:linear-gradient(135deg,#064e3b,#065f46,#059669); }
                .id-card-accent { color:#047857; }
                .id-card-badge { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
                .id-card-divider { background:linear-gradient(90deg,#059669,#34d399,#059669); }
                .id-card-member-id { color:#047857 !important; }
                .id-card-photo-ring { border-color:#059669 !important; }
                .id-card-corner-tl, .id-card-corner-tr,
                .id-card-corner-bl, .id-card-corner-br {
                    position:absolute; width:20px; height:20px;
                    border-color:#059669; border-style:solid;
                }
                .id-card-corner-tl { top:6px; left:6px; border-width:2px 0 0 2px; border-radius:4px 0 0 0; }
                .id-card-corner-tr { top:6px; right:6px; border-width:2px 2px 0 0; border-radius:0 4px 0 0; }
                .id-card-corner-bl { bottom:6px; left:6px; border-width:0 0 2px 2px; border-radius:0 0 0 4px; }
                .id-card-corner-br { bottom:6px; right:6px; border-width:0 2px 2px 0; border-radius:0 0 4px 0; }
            ',
        ];
    }
}
