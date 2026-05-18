<x-filament-panels::page>

<style>
    .ps-header {
        display: flex; align-items: center; gap: 20px;
        padding: 20px 24px; background: #fff;
        border: 1px solid #e5e7eb; border-radius: 14px;
        margin-bottom: 20px;
    }
    .ps-avatar-current {
        width: 72px; height: 72px; border-radius: 50%;
        object-fit: cover; border: 3px solid #dbeafe;
        flex-shrink: 0;
    }
    .ps-avatar-emoji {
        display: flex; align-items: center; justify-content: center;
        font-size: 38px; line-height: 1;
    }
    .ps-avatar-initials {
        display: flex; align-items: center; justify-content: center;
        background: #1a4ff5; color: #fff;
        font-size: 28px; font-weight: 700;
    }
    .ps-emoji-circle {
        width: 100%; height: 100%; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; line-height: 1;
    }
    .ps-header-info { flex: 1; }
    .ps-header-name { font-size: 16px; font-weight: 700; color: #111827; }
    .ps-header-email { font-size: 13px; color: #6b7280; margin-top: 2px; }

    .ps-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 4px 0 20px;
    }
    .ps-divider-line { flex: 1; height: 1px; background: #e5e7eb; }
    .ps-divider-text { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; }

    .ps-picker {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 14px; padding: 20px 24px;
        margin-bottom: 20px;
    }
    .ps-picker-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
    .ps-picker-sub { font-size: 12px; color: #9ca3af; margin-bottom: 16px; }

    .ps-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
    }
    @media (max-width: 640px) {
        .ps-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .ps-avatar-btn {
        position: relative; border-radius: 50%; overflow: hidden;
        width: 64px; height: 64px; border: 3px solid transparent;
        cursor: pointer; background: none; padding: 0;
        transition: border-color .2s, transform .15s, box-shadow .2s;
    }
    .ps-avatar-btn:hover { border-color: #93c5fd; transform: scale(1.08); }
    .ps-avatar-btn.selected {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.2);
        transform: scale(1.1);
    }
    .ps-avatar-btn img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .ps-check {
        position: absolute; inset: 0;
        background: rgba(37,99,235,.25);
        display: flex; align-items: center; justify-content: center;
    }
    .ps-check svg { width: 20px; height: 20px; color: #fff; filter: drop-shadow(0 1px 2px rgba(0,0,0,.4)); }
</style>

{{-- Header: avatar aktif + info user --}}
@php $display = $this->getCurrentAvatarDisplay(); @endphp
<div class="ps-header">
    @if($display['type'] === 'emoji')
        <div class="ps-avatar-current ps-avatar-emoji" style="background:#{{ $display['bg'] }};">
            {{ $display['emoji'] }}
        </div>
    @elseif($display['type'] === 'img')
        <img class="ps-avatar-current" src="{{ $display['url'] }}" alt="Avatar">
    @else
        <div class="ps-avatar-current ps-avatar-initials">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    @endif
    <div class="ps-header-info">
        <div class="ps-header-name">{{ auth()->user()->name }}</div>
        <div class="ps-header-email">{{ auth()->user()->email }}</div>
    </div>
</div>

{{-- Form: foto upload + info + password --}}
<form wire:submit="save">
    {{ $this->form }}

    {{-- Pemisah: atau pilih avatar emoji --}}
    <div class="ps-divider" style="margin-top: 20px;">
        <div class="ps-divider-line"></div>
        <span class="ps-divider-text">atau pilih avatar emoji</span>
        <div class="ps-divider-line"></div>
    </div>

    {{-- Avatar emoji picker --}}
    <div class="ps-picker">
        <div class="ps-picker-title">Avatar Emoji</div>
        <div class="ps-picker-sub">Klik untuk memilih. Memilih emoji akan menggantikan foto yang diupload.</div>

        {{-- Foto yang pernah diupload — tombol cepat kembali ke foto tanpa upload ulang --}}
        @if($this->uploadedPhotoPath)
            @php
                $uploadedUrl = str_starts_with($this->uploadedPhotoPath, 'http')
                    ? $this->uploadedPhotoPath
                    : asset('storage/' . $this->uploadedPhotoPath);
                $isPhotoSelected = $this->selectedAvatar === $this->uploadedPhotoPath;
            @endphp
            <div style="margin-bottom: 20px;">
                <p style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px;">Foto yang diupload</p>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <button
                        type="button"
                        wire:click="selectUploadedPhoto()"
                        wire:loading.attr="disabled"
                        class="ps-avatar-btn {{ $isPhotoSelected ? 'selected' : '' }}"
                    >
                        <img src="{{ $uploadedUrl }}" alt="Foto Profil">
                        @if($isPhotoSelected)
                            <div class="ps-check">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @endif
                    </button>
                    <p style="font-size: 12px; color: #6b7280;">
                        Klik untuk menggunakan kembali foto ini
                        @if(! $isPhotoSelected)
                            tanpa upload ulang.
                        @else
                            <span style="color: #2563eb; font-weight: 600;">— Aktif</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="ps-divider" style="margin-bottom: 16px;">
                <div class="ps-divider-line"></div>
                <span class="ps-divider-text">atau pilih emoji</span>
                <div class="ps-divider-line"></div>
            </div>
        @endif

        <div class="ps-grid">
            @foreach($this->getPresetAvatars() as $i => $preset)
                @php $id = "emoji:{$preset['emoji']}:{$preset['bg']}"; @endphp
                <button
                    type="button"
                    wire:click="selectAvatar({{ $i }})"
                    wire:loading.attr="disabled"
                    class="ps-avatar-btn {{ $this->selectedAvatar === $id ? 'selected' : '' }}"
                >
                    <div class="ps-emoji-circle" style="background:#{{ $preset['bg'] }};">
                        {{ $preset['emoji'] }}
                    </div>
                    @if($this->selectedAvatar === $id)
                        <div class="ps-check">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <div style="margin-top: 24px;">
        <x-filament::button type="submit" icon="heroicon-o-check">
            Simpan Perubahan
        </x-filament::button>
    </div>
</form>

</x-filament-panels::page>
