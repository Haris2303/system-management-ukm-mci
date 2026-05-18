<x-filament-panels::page>

    {{-- Info dokumen --}}
    <x-filament::section>
        <x-slot name="heading">Informasi Dokumen</x-slot>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:16px;">

            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:14px 16px;">
                <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Nama File</p>
                <p style="font-size:14px; font-weight:600; color:#111827; margin:0;">{{ $this->record->nama_file }}</p>
            </div>

            <div style="background:#f0fdf4; border:1px solid #86efac; border-radius:10px; padding:14px 16px;">
                <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Status</p>
                <p style="font-size:14px; font-weight:600; color:#16a34a; margin:0;">✅ Siap Digunakan</p>
            </div>

            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 16px;">
                <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Total Chunks</p>
                <p style="font-size:28px; font-weight:700; color:#1d4ed8; margin:0;">{{ $this->record->total_chunks }}</p>
            </div>

            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:14px 16px;">
                <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Diupload</p>
                <p style="font-size:14px; font-weight:600; color:#374151; margin:0;">{{ $this->record->created_at->format('d M Y, H:i') }}</p>
            </div>

        </div>

        @if ($this->record->deskripsi)
            <div style="margin-top:12px; padding:12px 16px; background:#f9fafb; border-radius:8px; border:1px solid #e5e7eb;">
                <p style="font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Deskripsi</p>
                <p style="font-size:14px; color:#374151; margin:0;">{{ $this->record->deskripsi }}</p>
            </div>
        @endif
    </x-filament::section>

    {{-- File Terupload --}}
    <x-filament::section>
        <x-slot name="heading">File Terupload</x-slot>

        @php
            $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($this->record->path_file);
            $fileExists = file_exists($filePath);
            $fileSize = $fileExists ? filesize($filePath) : null;
            $fileSizeFormatted = $fileSize !== null
                ? ($fileSize >= 1048576
                    ? round($fileSize / 1048576, 2) . ' MB'
                    : round($fileSize / 1024, 1) . ' KB')
                : null;
        @endphp

        <div style="display:flex; align-items:center; gap:16px; padding:16px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px;">

            {{-- Ikon PDF --}}
            <div style="flex-shrink:0; width:48px; height:48px; background:#fee2e2; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:26px;height:26px;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="9" y1="13" x2="15" y2="13"/>
                    <line x1="9" y1="17" x2="13" y2="17"/>
                </svg>
            </div>

            {{-- Info File --}}
            <div style="flex:1; min-width:0;">
                <p style="font-size:14px; font-weight:600; color:#111827; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ basename($this->record->path_file) }}
                </p>
                <p style="font-size:12px; color:#9ca3af; margin:4px 0 0;">
                    @if($fileExists)
                        {{ $fileSizeFormatted }} &middot; PDF Document
                    @else
                        <span style="color:#ef4444;">File tidak ditemukan di server</span>
                    @endif
                </p>
            </div>

            {{-- Tombol Download --}}
            @if($fileExists)
                <a href="{{ route('rag-documents.download', $this->record) }}"
                   style="flex-shrink:0; display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#2563eb; color:#fff; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; transition:background 0.15s;"
                   onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download PDF
                </a>
            @endif

        </div>
    </x-filament::section>

    {{-- Tabel chunks --}}
    <x-filament::section>
        <x-slot name="heading">Isi Chunks ({{ $this->record->total_chunks }} chunk)</x-slot>
        <x-slot name="description">Setiap chunk adalah potongan teks dari PDF yang sudah di-embed dan siap digunakan chatbot.</x-slot>

        {{ $this->table }}
    </x-filament::section>

</x-filament-panels::page>
