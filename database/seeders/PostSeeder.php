<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{

    public function run(): void
    {
        $author = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['ketua_ukm', 'bendahara', 'ketua_divisi']))
            ->first() ?? User::first();

        if (! $author) {
            $this->command->warn('⚠️  Tidak ada user ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $posts = [
            // ── FEATURED ─────────────────────────────────────────────────
            [
                'judul'        => 'Selamat Datang di Website Resmi UKM MCI',
                'ringkasan'    => 'UKM Media Cipta Informatika (MCI) hadir dengan platform digital baru untuk mempermudah akses informasi, berita, dan pendaftaran anggota bagi seluruh sivitas akademika.',
                'konten'       => '<h2>Platform Digital UKM MCI Resmi Diluncurkan</h2><p>Setelah melalui proses pengembangan selama beberapa bulan, kami dengan bangga mengumumkan peluncuran resmi website dan sistem administrasi digital UKM Media Cipta Informatika (MCI).</p><p>Melalui platform ini, calon anggota dapat mendaftar secara online, mengikuti proses seleksi berbasis divisi, dan memantau perkembangan program kerja yang sedang berjalan.</p><h3>Fitur Unggulan</h3><ul><li>Pendaftaran anggota baru secara online dengan formulir multi-langkah</li><li>Informasi divisi dan pertanyaan seleksi yang transparan</li><li>Berita dan pengumuman terkini dari setiap divisi</li><li>Asisten AI berbasis RAG untuk menjawab pertanyaan umum</li></ul><p>Kami berharap kehadiran platform ini dapat meningkatkan produktivitas dan keterbukaan informasi di lingkungan UKM MCI.</p>',
                'kategori'     => 'Pengumuman',
                'tag'          => 'pengumuman, website, digital',
                'status'       => 'published',
                'is_featured'  => true,
                'views'        => 312,
                'published_at' => now()->subDays(30),
            ],

            // ── PRESTASI ─────────────────────────────────────────────────
            [
                'judul'        => 'Tim MCI Raih Juara 2 Kompetisi Web Design Nasional 2025',
                'ringkasan'    => 'Tiga mahasiswa dari divisi Desain Grafis UKM MCI berhasil meraih Juara 2 pada ajang lomba Web Design tingkat nasional yang diselenggarakan oleh Asosiasi Informatika Indonesia.',
                'konten'       => '<h2>Prestasi Membanggakan di Kancah Nasional</h2><p>Delegasi UKM MCI yang terdiri dari Rizky Anggota, Dinda Setiawati, dan Fajar Nugroho berhasil membawa pulang medali perak pada <strong>Kompetisi Web Design Nasional 2025</strong> yang dihelat di Jakarta, 15–17 Maret 2025.</p><p>Tim yang dibimbing oleh Ketua Divisi Desain Grafis ini mengangkat tema "Aksesibilitas Digital untuk Semua" dengan membuat prototype website pemerintahan yang ramah difabel.</p><h3>Proses Persiapan</h3><p>Selama tiga bulan penuh, tim berlatih setiap hari Sabtu dan Minggu di laboratorium komputer kampus. Mereka mempelajari standar WCAG 2.1 dan menerapkan prinsip-prinsip desain universal.</p><blockquote>"Kemenangan ini bukan milik kami bertiga, tapi milik seluruh anggota MCI yang selalu mendukung."</blockquote><p>Selamat kepada tim, semoga prestasi ini menjadi motivasi bagi seluruh anggota untuk terus berkarya!</p>',
                'kategori'     => 'Prestasi',
                'tag'          => 'kompetisi, desain, juara, nasional',
                'status'       => 'published',
                'is_featured'  => true,
                'views'        => 258,
                'published_at' => now()->subDays(25),
            ],

            // ── KEGIATAN ─────────────────────────────────────────────────
            [
                'judul'        => 'Workshop Flutter Dasar untuk Anggota Baru MCI',
                'ringkasan'    => 'Divisi Programming mengadakan workshop Flutter selama dua hari untuk memperkenalkan pengembangan aplikasi mobile kepada anggota baru yang baru bergabung di semester ini.',
                'konten'       => '<h2>Workshop Flutter: Dari Nol Hingga Hello World</h2><p>Sabtu dan Minggu lalu, laboratorium IT kampus dipenuhi oleh 28 anggota baru MCI yang antusias mengikuti <strong>Workshop Flutter Dasar</strong> yang diselenggarakan oleh Divisi Programming.</p><h3>Agenda Workshop</h3><p><strong>Hari 1 – Pengenalan Dart & Flutter</strong></p><ul><li>Instalasi Flutter SDK dan Android Studio</li><li>Dasar-dasar bahasa Dart: variabel, fungsi, class</li><li>Membangun UI pertama dengan Widget</li><li>Hands-on: membuat aplikasi kalkulator sederhana</li></ul><p><strong>Hari 2 – State Management & API Integration</strong></p><ul><li>Provider dan setState</li><li>Fetching data dari REST API</li><li>Hands-on: membuat aplikasi berita sederhana</li></ul><h3>Testimoni Peserta</h3><p>"Sebelumnya saya tidak pernah menyentuh Flutter sama sekali. Setelah dua hari ini, saya sudah bisa membuat aplikasi sederhana!" — Peserta dari divisi Programming.</p>',
                'kategori'     => 'Kegiatan',
                'tag'          => 'workshop, flutter, programming, mobile',
                'status'       => 'published',
                'is_featured'  => false,
                'views'        => 187,
                'published_at' => now()->subDays(20),
            ],

            [
                'judul'        => 'Open Recruitment Anggota Baru MCI Semester Genap 2025',
                'ringkasan'    => 'UKM MCI membuka pendaftaran anggota baru untuk semester genap tahun akademik 2024/2025. Tersedia 8 divisi dengan fokus keahlian masing-masing.',
                'konten'       => '<h2>Bergabunglah Bersama Kami!</h2><p>UKM Media Cipta Informatika membuka pintu selebar-lebarnya bagi mahasiswa baru yang ingin mengembangkan diri di bidang teknologi informasi dan multimedia.</p><h3>Divisi yang Membuka Rekrutmen</h3><ul><li>💻 <strong>Programming</strong> — Web, Mobile, dan Backend Development</li><li>🎨 <strong>Desain Grafis</strong> — UI/UX, Branding, dan Ilustrasi Digital</li><li>🎬 <strong>Multimedia</strong> — Video Production dan Motion Graphics</li><li>📡 <strong>Networking</strong> — Jaringan Komputer dan Keamanan Siber</li><li>📊 <strong>Data Science</strong> — Analisis Data dan Machine Learning</li><li>🤖 <strong>AI & Robotika</strong> — Kecerdasan Buatan dan Embedded Systems</li><li>📢 <strong>Public Relations</strong> — Komunikasi dan Media Sosial</li><li>🗂️ <strong>Sekretariat</strong> — Administrasi dan Dokumentasi</li></ul><h3>Cara Mendaftar</h3><p>Kunjungi halaman <strong>Daftar</strong> di website ini, isi formulir data diri, pilih divisi yang kamu minati, dan jawab pertanyaan seleksi sesuai divisi yang dipilih.</p><p><strong>Batas pendaftaran: 31 Mei 2025</strong></p>',
                'kategori'     => 'Pengumuman',
                'tag'          => 'rekrutmen, anggota baru, pendaftaran',
                'status'       => 'published',
                'is_featured'  => true,
                'views'        => 441,
                'published_at' => now()->subDays(18),
            ],

            // ── BERITA ────────────────────────────────────────────────────
            [
                'judul'        => 'Rapat Koordinasi Proker Semester Baru Berjalan Lancar',
                'ringkasan'    => 'Seluruh Ketua Divisi dan Ketua UKM MCI mengadakan rapat koordinasi untuk menyusun program kerja semester genap 2024/2025 yang lebih terstruktur dan terukur.',
                'konten'       => '<h2>Sinergi Antar Divisi dalam Rapat Proker</h2><p>Bertempat di Ruang Seminar Gedung B, seluruh pengurus inti UKM MCI menggelar rapat koordinasi program kerja yang berlangsung selama empat jam.</p><p>Rapat yang dipimpin oleh Ketua UKM ini menghasilkan total <strong>24 program kerja</strong> yang akan dilaksanakan sepanjang semester genap, mencakup kegiatan internal maupun eksternal kampus.</p><h3>Poin-Poin Penting Rapat</h3><ul><li>Penetapan timeline pelaksanaan proker per divisi</li><li>Alokasi anggaran kas untuk setiap kegiatan</li><li>Pembentukan panitia lintas divisi untuk proker besar</li><li>Evaluasi proker semester lalu dan lesson learned</li></ul><p>Seluruh program kerja yang telah disepakati akan dipantau secara real-time melalui sistem administrasi digital MCI.</p>',
                'kategori'     => 'Berita',
                'tag'          => 'rapat, program kerja, koordinasi',
                'status'       => 'published',
                'is_featured'  => false,
                'views'        => 95,
                'published_at' => now()->subDays(15),
            ],

            [
                'judul'        => 'MCI Berhasil Mengembangkan Sistem Absensi Berbasis QR Code',
                'ringkasan'    => 'Divisi Programming UKM MCI berhasil menyelesaikan proyek sistem absensi digital menggunakan QR Code yang akan diimplementasikan di laboratorium kampus.',
                'konten'       => '<h2>Inovasi Digital dari Dalam Kampus</h2><p>Setelah dua bulan pengerjaan, tim Divisi Programming UKM MCI berhasil menyelesaikan sistem absensi berbasis QR Code yang siap digunakan oleh civitas akademika.</p><h3>Fitur Sistem Absensi QR</h3><ul><li>Generate QR Code unik per sesi perkuliahan</li><li>Scan via kamera smartphone tanpa aplikasi tambahan</li><li>Rekapitulasi kehadiran otomatis dalam format Excel</li><li>Notifikasi real-time kepada dosen jika kuorum tidak tercapai</li><li>Dashboard monitoring kehadiran per mata kuliah</li></ul><h3>Teknologi yang Digunakan</h3><p>Sistem dibangun menggunakan <strong>Laravel 11</strong> untuk backend, <strong>React.js</strong> untuk dashboard dosen, dan <strong>Vue.js</strong> untuk halaman absensi mahasiswa. QR Code di-generate menggunakan library <code>simplesoftwareio/simple-qrcode</code>.</p><p>Sistem ini rencananya akan diserahterimakan kepada pihak kampus pada bulan Juni 2025.</p>',
                'kategori'     => 'Prestasi',
                'tag'          => 'qr code, absensi, programming, inovasi',
                'status'       => 'published',
                'is_featured'  => false,
                'views'        => 203,
                'published_at' => now()->subDays(12),
            ],

            [
                'judul'        => 'Pelatihan Public Speaking dan Presentasi Teknis untuk Anggota MCI',
                'ringkasan'    => 'Divisi Public Relations mengadakan pelatihan public speaking intensif sehari penuh, menghadirkan narasumber profesional dari industri teknologi.',
                'konten'       => '<h2>Belajar Berbicara di Depan Publik dengan Percaya Diri</h2><p>Tidak hanya hard skill teknis, UKM MCI juga menaruh perhatian besar pada pengembangan soft skill anggotanya. Sabtu kemarin, lebih dari 40 anggota mengikuti <strong>Pelatihan Public Speaking & Technical Presentation</strong>.</p><p>Narasumber utama adalah Kak Dian Pramudita, seorang Software Engineer dari perusahaan teknologi terkemuka yang juga alumni kampus kami.</p><h3>Materi yang Disampaikan</h3><ol><li>Mengatasi demam panggung dan membangun kepercayaan diri</li><li>Struktur presentasi teknis yang efektif</li><li>Cara menjelaskan konsep kompleks kepada non-teknis</li><li>Tips membuat slide yang bersih dan profesional</li><li>Simulasi presentasi dan sesi feedback</li></ol><p>Di akhir sesi, peserta melakukan micro-presentation 3 menit tentang proyek masing-masing dan mendapatkan feedback langsung dari narasumber.</p>',
                'kategori'     => 'Kegiatan',
                'tag'          => 'public speaking, soft skill, pelatihan',
                'status'       => 'published',
                'is_featured'  => false,
                'views'        => 134,
                'published_at' => now()->subDays(9),
            ],

            [
                'judul'        => 'Panduan Penggunaan Sistem Administrasi Digital MCI untuk Anggota',
                'ringkasan'    => 'Panduan lengkap cara menggunakan fitur-fitur dalam sistem administrasi digital MCI, termasuk cara memantau program kerja, melihat tagihan kas, dan mengakses materi organisasi.',
                'konten'       => '<h2>Panduan Lengkap Sistem Digital MCI</h2><p>Sebagai anggota UKM MCI, kamu memiliki akses ke sistem administrasi digital yang memudahkan pengelolaan berbagai kegiatan organisasi. Berikut panduan singkat penggunaannya.</p><h3>1. Login ke Dashboard</h3><p>Akses <code>admin.mci.ac.id</code> dan masukkan email serta password yang kamu terima saat diterima sebagai anggota. Hubungi Ketua Divisi jika belum menerima kredensial.</p><h3>2. Memantau Program Kerja</h3><p>Menu <strong>Program Kerja</strong> menampilkan seluruh proker yang sedang berjalan di divisimu. Kamu bisa melihat progress, daftar tugas, dan deadline masing-masing proker.</p><h3>3. Melihat Tagihan Kas</h3><p>Cek status iuran bulananmu di menu <strong>Tagihan Kas</strong>. Pastikan selalu melunasi iuran tepat waktu agar tidak masuk daftar tunggakan.</p><h3>4. Mengakses Materi</h3><p>Semua materi pelatihan dan workshop tersimpan di menu <strong>Materi</strong>. Kamu bisa mengunduh PDF, video tutorial, dan slide presentasi kapan saja.</p><h3>5. Chatbot AI</h3><p>Punya pertanyaan tentang UKM MCI? Gunakan fitur <strong>Chatbot AI</strong> di pojok kanan bawah website. Chatbot ini dilatih dengan dokumen-dokumen resmi MCI dan dapat menjawab pertanyaan umum 24/7.</p>',
                'kategori'     => 'Pengumuman',
                'tag'          => 'panduan, sistem, anggota, tutorial',
                'status'       => 'published',
                'is_featured'  => false,
                'views'        => 176,
                'published_at' => now()->subDays(7),
            ],

            [
                'judul'        => 'Kunjungan Studi Banding ke UKM Teknologi Universitas Negeri Surabaya',
                'ringkasan'    => 'Sebanyak 15 pengurus inti UKM MCI melakukan kunjungan studi banding ke UKM Teknologi UNS untuk berbagi pengalaman pengelolaan organisasi mahasiswa berbasis IT.',
                'konten'       => '<h2>Studi Banding: Belajar dari Saudara Seperjuangan</h2><p>Dalam rangka meningkatkan kualitas pengelolaan organisasi, 15 pengurus inti UKM MCI melakukan kunjungan ke <strong>UKM Teknologi Universitas Negeri Surabaya</strong> selama dua hari.</p><p>Kunjungan ini difasilitasi oleh pihak kampus dan merupakan bagian dari program pengembangan kelembagaan mahasiswa.</p><h3>Hal-Hal yang Dipelajari</h3><ul><li>Sistem pengelolaan keuangan organisasi yang transparan dan akuntabel</li><li>Model regenerasi kepemimpinan yang berkelanjutan</li><li>Strategi kolaborasi dengan industri dan perusahaan teknologi</li><li>Program mentoring antara anggota senior dan junior</li><li>Pengelolaan media sosial dan branding organisasi</li></ul><p>Berbagai insight yang diperoleh akan segera diimplementasikan dalam program kerja UKM MCI semester berikutnya.</p>',
                'kategori'     => 'Kegiatan',
                'tag'          => 'studi banding, kunjungan, pengembangan',
                'status'       => 'published',
                'is_featured'  => false,
                'views'        => 89,
                'published_at' => now()->subDays(4),
            ],

            [
                'judul'        => 'Anggota MCI Lolos Seleksi Google Developer Student Clubs',
                'ringkasan'    => 'Dua anggota Divisi Programming UKM MCI berhasil lolos seleksi dan resmi menjadi anggota Google Developer Student Clubs (GDSC) chapter kampus.',
                'konten'       => '<h2>Prestasi Individu yang Mengharumkan Nama MCI</h2><p>Kabar membanggakan datang dari dua anggota Divisi Programming UKM MCI — <strong>Bagas Pratama</strong> dan <strong>Nabilah Zahra</strong> — yang berhasil lolos seleksi kompetitif dan resmi bergabung sebagai anggota <strong>Google Developer Student Clubs (GDSC)</strong> chapter kampus kami.</p><p>GDSC adalah program resmi Google yang ditujukan untuk mahasiswa yang ingin mengembangkan skill teknologi dan berkontribusi pada komunitas developer lokal.</p><h3>Apa Itu GDSC?</h3><p>Google Developer Student Clubs adalah komunitas developer berbasis kampus yang didukung langsung oleh Google. Anggota GDSC mendapatkan akses ke:</p><ul><li>Google Cloud credits senilai $300 untuk proyek pembelajaran</li><li>Mentorship langsung dari Google Developer Experts</li><li>Jaringan dengan ribuan developer di seluruh Indonesia</li><li>Kesempatan hadir di Google I/O dan event teknologi global</li></ul><p>Selamat kepada Bagas dan Nabilah! Jadikan pengalaman ini jembatan untuk membawa ilmu kembali ke UKM MCI.</p>',
                'kategori'     => 'Prestasi',
                'tag'          => 'google, gdsc, prestasi, programming',
                'status'       => 'published',
                'is_featured'  => false,
                'views'        => 221,
                'published_at' => now()->subDays(2),
            ],

            // ── DRAFT (belum dipublish) ───────────────────────────────────
            [
                'judul'        => 'Persiapan MCI untuk Ajang Hackathon Internal Kampus 2025',
                'ringkasan'    => 'UKM MCI sedang mempersiapkan tim untuk mengikuti Hackathon Internal Kampus 2025 yang akan diselenggarakan pada bulan Juli mendatang.',
                'konten'       => '<h2>Hackathon Internal: Ajang Unjuk Gigi Anggota MCI</h2><p>Bulan Juli mendatang, kampus kami akan menyelenggarakan Hackathon Internal 2025 dengan hadiah total senilai Rp 15 juta. UKM MCI berencana mengirimkan minimal 5 tim untuk bersaing.</p><p>Artikel ini masih dalam tahap penulisan dan akan segera dipublikasikan.</p>',
                'kategori'     => 'Kegiatan',
                'tag'          => 'hackathon, kompetisi, programming',
                'status'       => 'draft',
                'is_featured'  => false,
                'views'        => 0,
                'published_at' => null,
            ],
        ];

        foreach ($posts as $data) {
            Post::create([...$data, 'author_id' => $author->id]);
        }

        $this->command->info('✅ Berhasil membuat ' . count($posts) . ' post (10 published, 1 draft).');
    }
}
