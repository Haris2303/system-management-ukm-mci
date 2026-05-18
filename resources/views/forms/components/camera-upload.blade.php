@php
    $disabled = $isDisabled();
@endphp

{{-- Render standard FileUpload (label, wrapper, filepond input) --}}
@include('filament-forms::components.file-upload')

{{-- Camera capture UI --}}
@unless($disabled)
<div
    x-data="{
        open: false,
        uploading: false,
        stream: null,

        getPond() {
            const prev = this.$el.previousElementSibling;
            const parent = this.$el.parentElement;
            const input = (prev && prev.querySelector('input[aria-labelledby]'))
                       || (parent && parent.querySelector('input[aria-labelledby]'));
            return (input && window.FilePond) ? window.FilePond.find(input) : null;
        },

        openCamera() {
            if (! window.isSecureContext || ! navigator.mediaDevices) {
                alert('Kamera memerlukan koneksi HTTPS.\n\nAktifkan SSL di Laragon atau akses melalui https://');
                return;
            }
            this.open = true;
            navigator.mediaDevices
                .getUserMedia({ video: { facingMode: 'user' }, audio: false })
                .then(s => {
                    this.stream = s;
                    this.$nextTick(() => {
                        this.$refs.video.srcObject = s;
                        this.$refs.video.play();
                    });
                })
                .catch(err => {
                    alert('Tidak dapat mengakses kamera: ' + err.message);
                    this.open = false;
                });
        },

        closeCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            this.open = false;
        },

        capture() {
            const v = this.$refs.video;
            const c = this.$refs.canvas;
            c.width  = v.videoWidth;
            c.height = v.videoHeight;
            const ctx = c.getContext('2d');
            ctx.translate(c.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(v, 0, 0);

            this.closeCamera();
            this.uploading = true;

            c.toBlob(blob => {
                const file = new File([blob], 'foto-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                const pond = this.getPond();

                if (pond) {
                    pond.addFile(file)
                        .then(() => { this.uploading = false; })
                        .catch(() => {
                            this.uploading = false;
                            alert('Gagal mengupload foto. Silakan coba lagi.');
                        });
                } else {
                    this.uploading = false;
                    alert('Komponen upload tidak ditemukan. Silakan refresh halaman.');
                }
            }, 'image/jpeg', 0.9);
        }
    }"
    style="margin-top: 8px;"
>
    {{-- Tombol buka kamera --}}
    <button
        type="button"
        x-show="!open && !uploading"
        x-on:click="openCamera()"
        style="
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            background: #2563eb;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        "
        onmouseover="this.style.background='#1d4ed8'"
        onmouseout="this.style.background='#2563eb'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
            <circle cx="12" cy="13" r="4"/>
        </svg>
        Ambil dari Kamera
    </button>

    {{-- Indikator uploading --}}
    <span x-show="uploading" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#6b7280;">
        <svg style="animation:spin 1s linear infinite;width:15px;height:15px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
        Mengupload foto...
    </span>

    {{-- Overlay kamera --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        style="
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0,0,0,0.85);
            display: flex;
            align-items: center;
            justify-content: center;
        "
        x-on:keydown.escape.window="closeCamera()"
    >
        <div style="
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            width: min(90vw, 520px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        ">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <p style="font-size:15px;font-weight:700;color:#111827;margin:0;">
                    📷 Ambil Foto Kandidat
                </p>
                <button type="button" x-on:click="closeCamera()"
                    style="width:30px;height:30px;border:none;background:#f3f4f6;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;color:#6b7280;">
                    ✕
                </button>
            </div>

            <div style="border-radius:10px;overflow:hidden;background:#000;aspect-ratio:4/3;position:relative;">
                <video
                    x-ref="video"
                    autoplay
                    playsinline
                    style="width:100%;height:100%;object-fit:cover;display:block;transform:scaleX(-1);"
                ></video>
                <div style="position:absolute;inset:0;pointer-events:none;border:2px solid rgba(255,255,255,0.3);border-radius:10px;"></div>
            </div>

            <canvas x-ref="canvas" style="display:none;"></canvas>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button
                    type="button"
                    x-on:click="capture()"
                    style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 20px;font-size:14px;font-weight:700;color:#fff;background:#2563eb;border:none;border-radius:10px;cursor:pointer;"
                    onmouseover="this.style.background='#1d4ed8'"
                    onmouseout="this.style.background='#2563eb'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Ambil Foto
                </button>

                <button
                    type="button"
                    x-on:click="closeCamera()"
                    style="padding:11px 20px;font-size:14px;font-weight:600;color:#374151;background:#f3f4f6;border:none;border-radius:10px;cursor:pointer;"
                    onmouseover="this.style.background='#e5e7eb'"
                    onmouseout="this.style.background='#f3f4f6'"
                >
                    Batal
                </button>
            </div>

            <p style="font-size:11px;color:#9ca3af;text-align:center;margin-top:12px;margin-bottom:0;">
                Foto diambil langsung dari kamera perangkat Anda
            </p>
        </div>
    </div>
</div>

<style>
    @@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endunless
