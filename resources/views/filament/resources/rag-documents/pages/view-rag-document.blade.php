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

    {{-- Tabel chunks --}}
    <x-filament::section>
        <x-slot name="heading">Isi Chunks ({{ $this->record->total_chunks }} chunk)</x-slot>
        <x-slot name="description">Setiap chunk adalah potongan teks dari PDF yang sudah di-embed dan siap digunakan chatbot.</x-slot>

        {{ $this->table }}
    </x-filament::section>

</x-filament-panels::page>
