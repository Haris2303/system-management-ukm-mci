@php
    $chunksSelesai = $document->total_chunks;
    $recentChunks  = $document->chunks()->latest()->take(5)->get();
    $isReady       = $document->status === 'ready';
    $isError       = $document->status === 'error';
@endphp

<div style="display:flex; flex-direction:column; gap:16px; padding:4px 0;">

    {{-- Status banner --}}
    @if ($isReady)
        <div style="display:flex; align-items:center; gap:12px; background:#f0fdf4; border:1px solid #86efac; border-radius:10px; padding:12px 16px;">
            <svg style="width:22px; height:22px; color:#16a34a; flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p style="font-size:14px; font-weight:600; color:#14532d; margin:0;">Dokumen berhasil diproses!</p>
                <p style="font-size:12px; color:#16a34a; margin:4px 0 0;">Semua chunk sudah di-embed dan siap digunakan oleh chatbot.</p>
            </div>
        </div>

    @elseif ($isError)
        <div style="display:flex; align-items:center; gap:12px; background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:12px 16px;">
            <svg style="width:22px; height:22px; color:#dc2626; flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p style="font-size:14px; font-weight:600; color:#7f1d1d; margin:0;">Gagal memproses dokumen</p>
                <p style="font-size:12px; color:#dc2626; margin:4px 0 0;">Terjadi error saat proses. Gunakan tombol "Proses Ulang" pada baris dokumen.</p>
            </div>
        </div>

    @else
        <div style="display:flex; align-items:center; gap:12px; background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; padding:12px 16px;">
            <svg style="width:20px; height:20px; color:#f59e0b; flex-shrink:0; animation:spin 1s linear infinite;"
                fill="none" viewBox="0 0 24 24">
                <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <div>
                <p style="font-size:14px; font-weight:600; color:#92400e; margin:0;">Sedang memproses dokumen...</p>
                <p style="font-size:12px; color:#b45309; margin:4px 0 0;">Tutup dan buka kembali modal ini untuk melihat progress terbaru.</p>
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div style="background:{{ $isReady ? '#f0fdf4' : '#eff6ff' }}; border:1px solid {{ $isReady ? '#86efac' : '#bfdbfe' }}; border-radius:10px; padding:16px; text-align:center;">
            <p style="font-size:30px; font-weight:700; color:{{ $isReady ? '#16a34a' : '#1d4ed8' }}; margin:0;">{{ $chunksSelesai }}</p>
            <p style="font-size:11px; color:{{ $isReady ? '#16a34a' : '#3b82f6' }}; margin:4px 0 0;">
                {{ $isReady ? 'Total chunk berhasil' : 'Chunks berhasil diproses' }}
            </p>
        </div>
        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:16px; text-align:center;">
            <p style="font-size:30px; font-weight:700; color:#6b7280; margin:0;">
                {{ $recentChunks->isNotEmpty() ? $recentChunks->first()->chunk_index : '—' }}
            </p>
            <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">Chunk terakhir diproses</p>
        </div>
    </div>

    {{-- Recent chunks --}}
    @if ($recentChunks->isNotEmpty())
        <div>
            <p style="font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">
                5 Chunk Terakhir
            </p>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach ($recentChunks as $chunk)
                    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <span style="font-size:12px; font-weight:600; color:#374151;">Chunk #{{ $chunk->chunk_index }}</span>
                            <span style="font-size:11px; color:#9ca3af; background:#e5e7eb; padding:2px 8px; border-radius:999px;">{{ $chunk->token_count }} token</span>
                        </div>
                        <p style="font-size:12px; color:#6b7280; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $chunk->content }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div style="text-align:center; padding:24px 0; color:#9ca3af; font-size:14px;">
            Belum ada chunk yang selesai diproses.
        </div>
    @endif

</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
