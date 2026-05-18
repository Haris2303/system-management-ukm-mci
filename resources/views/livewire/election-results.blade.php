<div @if($election->status === 'aktif') wire:poll.3s @endif>

    {{-- Header: status + total suara --}}
    <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px; margin-bottom:20px;">
        <div>
            @if($election->status === 'aktif')
                <x-filament::badge color="success" size="lg">
                    <span style="display:inline-flex; align-items:center; gap:6px;">
                        <span style="
                            display:inline-block; width:8px; height:8px; border-radius:50%;
                            background:#22c55e;
                            animation: ping-dot 1.2s cubic-bezier(0,0,0.2,1) infinite;
                        "></span>
                        Live — memperbarui setiap 3 detik
                    </span>
                </x-filament::badge>
            @else
                <x-filament::badge color="info" size="lg">
                    Pemilihan Selesai
                </x-filament::badge>
            @endif
        </div>

        <x-filament::badge color="gray" size="lg">
            Total: {{ $totalSuara }} suara masuk
        </x-filament::badge>
    </div>

    {{-- Candidate list --}}
    <div style="display:flex; flex-direction:column; gap:12px;">
        @forelse($candidates as $index => $candidate)
            @php $isLeading = $index === 0 && $totalSuara > 0; @endphp

            <div style="
                border-radius: 12px;
                border: 1px solid {{ $isLeading ? 'rgb(253 224 71)' : 'rgb(229 231 235)' }};
                background: {{ $isLeading ? 'rgb(254 252 232)' : '#fff' }};
                padding: 16px;
                box-shadow: 0 1px 3px rgba(0,0,0,.07);
            ">
                {{-- Top row --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        {{-- Rank badge --}}
                        <div style="
                            width:32px; height:32px; border-radius:50%;
                            background:{{ $isLeading ? '#facc15' : '#e5e7eb' }};
                            display:flex; align-items:center; justify-content:center;
                            font-weight:700; font-size:14px;
                            color:{{ $isLeading ? '#78350f' : '#6b7280' }};
                            flex-shrink:0;
                        ">{{ $index + 1 }}</div>

                        <div>
                            <p style="font-weight:700; font-size:15px; color:rgb(17 24 39); margin:0 0 2px 0;">
                                {{ $candidate['nama'] }}
                            </p>
                            <p style="font-size:12px; color:rgb(156 163 175); margin:0;">
                                Kandidat #{{ $candidate['urut'] }}
                            </p>
                        </div>
                    </div>

                    <div style="text-align:right;">
                        <p style="
                            font-size:22px; font-weight:800; margin:0 0 2px 0;
                            color:{{ $isLeading ? 'rgb(161 98 7)' : 'rgb(31 41 55)' }};
                        ">{{ $candidate['persentase'] }}%</p>
                        <p style="font-size:12px; color:rgb(156 163 175); margin:0;">
                            {{ $candidate['jumlah_suara'] }} suara
                        </p>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div style="width:100%; background:rgb(229 231 235); border-radius:999px; height:10px; overflow:hidden;">
                    <div style="
                        height:10px; border-radius:999px;
                        background:{{ $isLeading ? '#facc15' : 'rgb(59 130 246)' }};
                        width:{{ $candidate['persentase'] }}%;
                        transition: width .7s ease-in-out;
                    "></div>
                </div>
            </div>
        @empty
            <div style="
                border: 1px dashed rgb(209 213 219);
                border-radius: 12px;
                padding: 32px;
                text-align: center;
                color: rgb(156 163 175);
            ">
                Belum ada kandidat terdaftar.
            </div>
        @endforelse
    </div>

    @if($totalSuara === 0 && count($candidates) > 0)
        <p style="text-align:center; font-size:13px; color:rgb(156 163 175); margin-top:16px;">
            Belum ada suara yang masuk.
        </p>
    @endif

    <style>
        @keyframes ping-dot {
            75%, 100% { transform: scale(1.8); opacity: 0; }
        }
    </style>
</div>
