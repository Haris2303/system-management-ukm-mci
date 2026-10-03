<x-filament-panels::page>

    <x-filament::section>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:12px;">
            <label style="display:flex; flex-direction:column; gap:4px; font-size:12px; color:#6b7280;">
                Tanggal
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="tanggal">
                        @forelse ($this->getTanggalOptions() as $tgl => $path)
                            <option value="{{ $tgl }}">{{ $tgl }}</option>
                        @empty
                            <option value="">Belum ada log</option>
                        @endforelse
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>

            <label style="display:flex; flex-direction:column; gap:4px; font-size:12px; color:#6b7280;">
                Level
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="level">
                        <option value="">Semua</option>
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>

            <label style="display:flex; flex-direction:column; gap:4px; font-size:12px; color:#6b7280;">
                Tampilkan
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="limit">
                        <option value="50">50 baris</option>
                        <option value="100">100 baris</option>
                        <option value="300">300 baris</option>
                        <option value="1000">1000 baris</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>

            <label style="display:flex; flex-direction:column; gap:4px; font-size:12px; color:#6b7280; grid-column:span 2;">
                Cari (user id, URL, model, IP, ...)
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari di log..." />
                </x-filament::input.wrapper>
            </label>
        </div>
    </x-filament::section>

    @php
        $entries = $this->getEntries();
        $colors = [
            'info'    => ['#eff6ff', '#1d4ed8'],
            'warning' => ['#fffbeb', '#d97706'],
            'error'   => ['#fef2f2', '#dc2626'],
        ];
    @endphp

    <x-filament::section>
        <x-slot name="heading">Aktivitas Terbaru ({{ count($entries) }})</x-slot>
        <x-slot name="description">Urut dari yang terbaru. Hanya super admin yang dapat melihat halaman ini.</x-slot>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="text-align:left; color:#6b7280; border-bottom:1px solid #e5e7eb;">
                        <th style="padding:8px; white-space:nowrap;">Waktu</th>
                        <th style="padding:8px;">Level</th>
                        <th style="padding:8px;">Aktivitas</th>
                        <th style="padding:8px;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $e)
                        @php [$bg, $fg] = $colors[$e['level']] ?? ['#f9fafb', '#374151']; @endphp
                        <tr style="border-bottom:1px solid #f3f4f6; vertical-align:top;">
                            <td style="padding:8px; white-space:nowrap; color:#6b7280;">{{ $e['time'] }}</td>
                            <td style="padding:8px;">
                                <span style="background:{{ $bg }}; color:{{ $fg }}; padding:2px 8px; border-radius:999px; font-weight:600; text-transform:uppercase; font-size:10px;">{{ $e['level'] }}</span>
                            </td>
                            <td style="padding:8px; font-weight:600; white-space:nowrap;">{{ $e['message'] }}</td>
                            <td style="padding:8px; font-family:ui-monospace,monospace; word-break:break-all; color:#374151;">{{ $e['context'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding:24px; text-align:center; color:#9ca3af;">Tidak ada log yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

</x-filament-panels::page>
