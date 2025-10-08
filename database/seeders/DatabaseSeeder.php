<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LowonganKerja;
use App\Models\ProgramMagang;
use App\Models\Berita;
use App\Models\Tentang;
use App\Models\Kontak;
use App\Models\LanggananNewsletter;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin CDC',
            'email' => 'admin@polibatam.ac.id',
            'password' => 'password123', // Will be hashed by mutator
            'role' => 'admin',
        ]);

        echo "✓ Admin user created (email: admin@polibatam.ac.id, password: password123)\n";

        // Create Sample Job Opportunities
        $this->createSampleJobs($admin);

        // Create Sample Internship Programs
        $this->createSamplePrograms($admin);

        // Create Sample News
        $this->createSampleNews($admin);

        // Create About Page
        $this->createAboutPage($admin);

        // Create Contact Page
        $this->createContactPage($admin);

        // Create Sample Newsletter Subscribers
        $this->createSampleSubscribers();

        echo "\n✓ Database seeding completed successfully!\n";
    }

    private function createSampleJobs($admin)
    {
        $jobs = [
            [
                'judul' => 'Web Developer Full Stack',
                'perusahaan' => 'PT Teknologi Indonesia',
                'deskripsi' => 'Kami mencari web developer berpengalaman dengan keahlian Laravel dan Vue.js untuk bergabung dengan tim development kami.',
                'lokasi' => 'Batam, Kepulauan Riau',
                'tipe' => 'full_time',
                'kategori' => 'teknologi',
                'gaji_min' => '5000000',
                'gaji_max' => '8000000',
                'gaji_negotiable' => false,
                'email_aplikasi' => 'hr@teknologiindonesia.com',
                'tanggal_berakhir' => now()->addDays(30)->toDateString(),
                'status' => true,
            ],
            [
                'judul' => 'Mobile App Developer',
                'perusahaan' => 'PT Digital Solusi',
                'deskripsi' => 'Dibutuhkan mobile developer dengan pengalaman Flutter/React Native untuk project aplikasi mobile enterprise.',
                'lokasi' => 'Jakarta',
                'tipe' => 'kontrak',
                'kategori' => 'teknologi',
                'gaji_min' => '7000000',
                'gaji_max' => '10000000',
                'gaji_negotiable' => false,
                'email_aplikasi' => 'recruitment@digitalsolusi.com',
                'tanggal_berakhir' => now()->addDays(45)->toDateString(),
                'status' => true,
            ],
            [
                'judul' => 'Data Analyst',
                'perusahaan' => 'PT Inovasi Data',
                'deskripsi' => 'Posisi data analyst untuk menganalisis dan menginterpretasikan data bisnis menggunakan Python dan SQL.',
                'lokasi' => 'Batam, Kepulauan Riau',
                'tipe' => 'full_time',
                'kategori' => 'teknologi',
                'gaji_min' => null,
                'gaji_max' => null,
                'gaji_negotiable' => true,
                'email_aplikasi' => 'jobs@inovasidata.com',
                'tanggal_berakhir' => now()->addDays(20)->toDateString(),
                'status' => true,
            ],
            [
                'judul' => 'Quality Control Staff',
                'perusahaan' => 'PT Manufaktur Jaya',
                'deskripsi' => 'Dibutuhkan QC staff untuk melakukan quality control di lantai produksi.',
                'lokasi' => 'Batam, Kepulauan Riau',
                'tipe' => 'full_time',
                'kategori' => 'manufaktur',
                'gaji_min' => '4000000',
                'gaji_max' => '5500000',
                'gaji_negotiable' => false,
                'email_aplikasi' => 'hrd@manufakturjaya.com',
                'tanggal_berakhir' => now()->addDays(25)->toDateString(),
                'status' => true,
            ],
            [
                'judul' => 'Customer Service Representative',
                'perusahaan' => 'PT Layanan Prima',
                'deskripsi' => 'Dicari customer service yang ramah dan komunikatif untuk melayani pelanggan.',
                'lokasi' => 'Batam, Kepulauan Riau',
                'tipe' => 'part_time',
                'kategori' => 'jasa',
                'gaji_min' => '3000000',
                'gaji_max' => '4000000',
                'gaji_negotiable' => false,
                'email_aplikasi' => 'cs@layananprima.com',
                'tanggal_berakhir' => now()->addDays(15)->toDateString(),
                'status' => true,
            ],
        ];

        foreach ($jobs as $job) {
            $job['created_by'] = $admin->id;
            $job['updated_by'] = $admin->id;
            LowonganKerja::create($job);
        }

        echo "✓ Sample job opportunities created\n";
    }

    private function createSamplePrograms($admin)
    {
        $programs = [
            [
                'judul' => 'Program MBKM Software Engineering',
                'perusahaan' => 'PT Teknologi Masa Depan',
                'deskripsi' => 'Program MBKM untuk mahasiswa yang ingin belajar software engineering secara langsung di industri.',
                'persyaratan' => "- Mahasiswa aktif semester 5 atau 6\n- IPK minimal 3.0\n- Menguasai dasar programming\n- Memiliki laptop pribadi",
                'benefit' => "- Sertifikat resmi\n- Uang saku bulanan\n- Bimbingan mentor profesional\n- Pengalaman kerja real project",
                'lokasi' => 'Batam, Kepulauan Riau',
                'tipe' => 'mbkm',
                'durasi' => 6,
                'tanggal_mulai' => now()->addDays(60)->toDateString(),
                'tanggal_berakhir' => now()->addDays(40)->toDateString(),
                'link_pendaftaran' => 'https://teknologimasadepan.com/mbkm',
                'status' => true,
                'kuota' => 10,
            ],
            [
                'judul' => 'Magang UI/UX Designer',
                'perusahaan' => 'PT Kreatif Digital',
                'deskripsi' => 'Program magang untuk belajar UI/UX design menggunakan Figma dan Adobe XD.',
                'persyaratan' => "- Mahasiswa aktif D3/D4/S1\n- Memiliki portfolio design\n- Menguasai Figma/Adobe XD\n- Kreatif dan detail oriented",
                'benefit' => "- Uang transport\n- Sertifikat magang\n- Portfolio real project\n- Networking dengan profesional",
                'lokasi' => 'Jakarta',
                'tipe' => 'magang_reguler',
                'durasi' => 3,
                'tanggal_mulai' => now()->addDays(30)->toDateString(),
                'tanggal_berakhir' => now()->addDays(20)->toDateString(),
                'link_pendaftaran' => 'https://kreatifdigital.com/internship',
                'status' => true,
                'kuota' => 5,
            ],
            [
                'judul' => 'Magang Network Administrator',
                'perusahaan' => 'PT Network Solutions',
                'deskripsi' => 'Program magang untuk belajar administrasi jaringan dan troubleshooting.',
                'persyaratan' => "- Mahasiswa jurusan TI/Informatika\n- Memahami dasar networking\n- Bersedia kerja shift\n- Jujur dan bertanggung jawab",
                'benefit' => "- Uang saku bulanan Rp 1.500.000\n- Sertifikat\n- Training cisco\n- Kesempatan diangkat karyawan",
                'lokasi' => 'Batam, Kepulauan Riau',
                'tipe' => 'magang_reguler',
                'durasi' => 4,
                'tanggal_mulai' => now()->addDays(45)->toDateString(),
                'tanggal_berakhir' => now()->addDays(30)->toDateString(),
                'link_pendaftaran' => 'https://networksolutions.com/apply',
                'status' => true,
                'kuota' => 8,
            ],
            [
                'judul' => 'Program Magang Independen Data Science',
                'perusahaan' => 'PT Analitik Cerdas',
                'deskripsi' => 'Magang independen untuk mahasiswa yang ingin mendalami data science dan machine learning.',
                'persyaratan' => "- IPK minimal 3.2\n- Menguasai Python\n- Memahami statistika\n- Pernah mengikuti kursus ML/Data Science",
                'benefit' => "- Uang saku kompetitif\n- Bimbingan data scientist senior\n- Akses ke tools dan dataset\n- Letter of recommendation",
                'lokasi' => 'Remote/Online',
                'tipe' => 'magang_independen',
                'durasi' => 5,
                'tanggal_mulai' => now()->addDays(50)->toDateString(),
                'tanggal_berakhir' => now()->addDays(35)->toDateString(),
                'link_pendaftaran' => 'https://analitikcerdas.com/magang',
                'status' => true,
                'kuota' => 15,
            ],
        ];

        foreach ($programs as $program) {
            $program['created_by'] = $admin->id;
            $program['updated_by'] = $admin->id;
            ProgramMagang::create($program);
        }

        echo "✓ Sample internship programs created\n";
    }

    private function createSampleNews($admin)
    {
        $news = [
            [
                'judul' => 'Career Fair Polibatam 2025: Ratusan Lowongan Kerja Menanti',
                'slug' => 'career-fair-polibatam-2025-ratusan-lowongan-kerja-menanti',
                'konten' => "<p>Politeknik Negeri Batam (Polibatam) akan menggelar Career Fair 2025 pada tanggal 15-17 Maret 2025 di Gedung Serba Guna Polibatam. Acara ini dihadiri oleh lebih dari 50 perusahaan dari berbagai industri yang siap menawarkan ratusan lowongan kerja untuk lulusan fresh graduate.</p>

<p>Direktur Polibatam, Prof. Dr. Ir. John Doe, M.Eng., menyampaikan bahwa Career Fair ini merupakan salah satu upaya institusi dalam memfasilitasi mahasiswa dan alumni untuk mendapatkan pekerjaan yang sesuai dengan kompetensi mereka.</p>

<p>\"Kami berharap melalui Career Fair ini, tingkat penyerapan lulusan Polibatam di dunia industri semakin meningkat,\" ujarnya.</p>

<p>Beberapa perusahaan besar yang akan berpartisipasi antara lain PT Astra International, PT Unilever Indonesia, PT Telkom Indonesia, dan masih banyak lagi. Peserta dapat langsung melakukan interview on the spot dengan recruiter dari perusahaan yang diminati.</p>

<p>Untuk informasi lebih lanjut dan registrasi, silakan kunjungi website CDC Polibatam atau hubungi panitia melalui email career@polibatam.ac.id</p>",
                'ringkasan' => 'Polibatam akan menggelar Career Fair 2025 dengan 50+ perusahaan dan ratusan lowongan kerja. Interview on the spot tersedia!',
                'kategori' => 'karir',
                'penulis' => 'CDC Polibatam',
                'tanggal_publikasi' => now()->subDays(5)->toDateString(),
                'status' => true,
                'is_featured' => true,
            ],
            [
                'judul' => 'Tips Sukses Mengikuti Program MBKM untuk Mahasiswa Polibatam',
                'slug' => 'tips-sukses-mengikuti-program-mbkm-untuk-mahasiswa-polibatam',
                'konten' => "<p>Program Merdeka Belajar Kampus Merdeka (MBKM) memberikan kesempatan emas bagi mahasiswa untuk mendapatkan pengalaman belajar di luar kampus. Namun, untuk sukses dalam program ini, ada beberapa tips yang perlu diperhatikan.</p>

<h3>1. Persiapkan Diri dengan Matang</h3>
<p>Sebelum mendaftar, pastikan Anda memahami program yang akan diikuti. Pelajari tentang perusahaan atau institusi yang menjadi mitra, dan sesuaikan dengan minat dan jurusan Anda.</p>

<h3>2. Tingkatkan Soft Skills</h3>
<p>Kemampuan komunikasi, kerja tim, dan problem solving sangat penting dalam program MBKM. Mulailah melatih kemampuan ini sejak dini.</p>

<h3>3. Jaga Komitmen dan Profesionalisme</h3>
<p>MBKM adalah kesempatan untuk menunjukkan profesionalisme Anda. Datang tepat waktu, menyelesaikan tugas dengan baik, dan menjaga sikap positif.</p>

<h3>4. Manfaatkan Networking</h3>
<p>Bangun relasi dengan mentor dan sesama peserta. Networking yang baik dapat membuka peluang karir di masa depan.</p>

<h3>5. Dokumentasikan Pengalaman</h3>
<p>Catat setiap pembelajaran dan pengalaman yang didapat. Ini akan berguna untuk portfolio dan CV Anda.</p>

<p>CDC Polibatam siap membantu mahasiswa dalam proses pendaftaran dan persiapan program MBKM. Jangan ragu untuk berkonsultasi!</p>",
                'ringkasan' => 'Lima tips penting untuk sukses mengikuti program MBKM: persiapan matang, soft skills, profesionalisme, networking, dan dokumentasi pengalaman.',
                'kategori' => 'mbkm',
                'penulis' => 'Tim CDC Polibatam',
                'tanggal_publikasi' => now()->subDays(3)->toDateString(),
                'status' => true,
                'is_featured' => true,
            ],
            [
                'judul' => 'Lokakarya Persiapan Interview: Tingkatkan Peluang Diterima Kerja',
                'slug' => 'lokakarya-persiapan-interview-tingkatkan-peluang-diterima-kerja',
                'konten' => "<p>CDC Polibatam menyelenggarakan lokakarya persiapan interview kerja pada tanggal 20 Februari 2025. Acara ini dibuka untuk semua mahasiswa semester akhir dan alumni Polibatam.</p>

<p>Dalam lokakarya ini, peserta akan belajar tentang:</p>
<ul>
<li>Cara menjawab pertanyaan interview dengan efektif</li>
<li>Bahasa tubuh dan penampilan yang profesional</li>
<li>Strategi mengatasi nervous saat interview</li>
<li>Mock interview dengan praktisi HR</li>
<li>Tips negosiasi gaji</li>
</ul>

<p>Narasumber yang dihadirkan adalah praktisi HR berpengalaman dari berbagai perusahaan multinasional. Peserta juga berkesempatan melakukan simulasi interview langsung dan mendapat feedback.</p>

<p>Pendaftaran dibuka hingga 18 Februari 2025 dengan kuota terbatas 100 peserta. Biaya pendaftaran gratis untuk mahasiswa Polibatam.</p>

<p>Daftar sekarang melalui: https://cdc.polibatam.ac.id/workshop-interview</p>",
                'ringkasan' => 'Lokakarya persiapan interview gratis untuk mahasiswa Polibatam. Dapatkan tips dari praktisi HR dan kesempatan mock interview!',
                'kategori' => 'karir',
                'penulis' => 'CDC Polibatam',
                'tanggal_publikasi' => now()->subDays(7)->toDateString(),
                'status' => true,
                'is_featured' => false,
            ],
            [
                'judul' => 'Pengumuman: Perpanjangan Masa Pendaftaran Magang di PT Teknologi Indonesia',
                'slug' => 'pengumuman-perpanjangan-masa-pendaftaran-magang-pt-teknologi-indonesia',
                'konten' => "<p>Dengan hormat,</p>

<p>Berdasarkan permintaan dari PT Teknologi Indonesia, kami informasikan bahwa masa pendaftaran program magang yang sebelumnya berakhir pada 10 Februari 2025 diperpanjang hingga 25 Februari 2025.</p>

<p>Program magang ini terbuka untuk mahasiswa semester 5 ke atas dengan jurusan:</p>
<ul>
<li>Teknik Informatika</li>
<li>Teknik Komputer</li>
<li>Multimedia dan Jaringan</li>
<li>Sistem Informasi</li>
</ul>

<p>Benefit yang diberikan:</p>
<ul>
<li>Uang saku bulanan Rp 2.000.000</li>
<li>Sertifikat magang</li>
<li>Pengalaman kerja di project nyata</li>
<li>Kemungkinan diangkat sebagai karyawan tetap</li>
</ul>

<p>Untuk informasi lebih lanjut dan pendaftaran, silakan hubungi CDC Polibatam atau kunjungi website resmi PT Teknologi Indonesia.</p>

<p>Terima kasih.</p>",
                'ringkasan' => 'Masa pendaftaran magang di PT Teknologi Indonesia diperpanjang hingga 25 Februari 2025. Kesempatan emas untuk mahasiswa TI!',
                'kategori' => 'magang',
                'penulis' => 'Admin CDC',
                'tanggal_publikasi' => now()->subDays(2)->toDateString(),
                'status' => true,
                'is_featured' => false,
            ],
            [
                'judul' => 'Kisah Sukses Alumni: Dari Mahasiswa Hingga Software Engineer di Startup Unicorn',
                'slug' => 'kisah-sukses-alumni-software-engineer-startup-unicorn',
                'konten' => "<p>Budi Santoso, alumni Teknik Informatika Polibatam angkatan 2019, kini bekerja sebagai Senior Software Engineer di salah satu startup unicorn Indonesia. Perjalanan karirnya dimulai dari aktif mengikuti program CDC Polibatam.</p>

<p>\"Saya sangat berterima kasih kepada CDC Polibatam yang memfasilitasi saya untuk mengikuti program magang di perusahaan teknologi. Dari situ, saya belajar banyak hal praktis yang tidak didapat di bangku kuliah,\" ujar Budi.</p>

<p>Budi memulai karirnya sebagai intern di perusahaan startup kecil pada tahun 2019. Berkat kerja keras dan skill yang terus diasah, ia kemudian pindah ke perusahaan yang lebih besar dan akhirnya bergabung dengan startup unicorn pada tahun 2022.</p>

<p>Pesan Budi untuk adik-adik juniornya:</p>
<blockquote>
<p>\"Jangan takut untuk mencoba hal baru. Manfaatkan setiap kesempatan magang dan workshop yang ada. Skill teknis memang penting, tapi soft skills seperti komunikasi dan teamwork sama pentingnya.\"</p>
</blockquote>

<p>CDC Polibatam terus berkomitmen untuk memfasilitasi mahasiswa dan alumni dalam mengembangkan karir mereka.</p>",
                'ringkasan' => 'Kisah inspiratif Budi Santoso, alumni Polibatam yang kini menjadi Senior Software Engineer di startup unicorn. Dimulai dari program magang CDC.',
                'kategori' => 'umum',
                'penulis' => 'Tim CDC Polibatam',
                'tanggal_publikasi' => now()->subDays(10)->toDateString(),
                'status' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($news as $item) {
            $item['created_by'] = $admin->id;
            $item['updated_by'] = $admin->id;
            Berita::create($item);
        }

        echo "✓ Sample news/newsletter created\n";
    }

    private function createAboutPage($admin)
    {
        Tentang::create([
            'sejarah' => "Career Development Center (CDC) Politeknik Negeri Batam didirikan pada tahun 2015 sebagai unit layanan yang berfokus pada pengembangan karir mahasiswa dan alumni. Sejak berdiri, CDC telah membantu ribuan lulusan Polibatam untuk mendapatkan pekerjaan yang sesuai dengan kompetensi mereka.\n\nDengan motto \"Connecting Talents with Opportunities\", CDC Polibatam terus berinovasi dalam memberikan layanan terbaik bagi mahasiswa dan alumni, serta membangun kemitraan yang kuat dengan dunia industri.",
            'visi' => "Menjadi pusat pengembangan karir terdepan yang menghubungkan talenta vokasi dengan peluang kerja berkualitas di tingkat nasional dan internasional.",
            'misi' => "1. Menyediakan informasi lowongan kerja dan program magang yang relevan dengan kompetensi mahasiswa.\n\n2. Menyelenggarakan pelatihan dan workshop pengembangan soft skills untuk meningkatkan daya saing lulusan.\n\n3. Membangun dan memperkuat kemitraan dengan industri untuk membuka peluang kerja bagi mahasiswa dan alumni.\n\n4. Memberikan layanan konseling karir dan bimbingan profesional kepada mahasiswa.\n\n5. Mengembangkan sistem informasi karir yang inovatif dan mudah diakses.",
            'tujuan' => "1. Meningkatkan tingkat penyerapan lulusan Polibatam di dunia kerja hingga mencapai 90% dalam 6 bulan setelah wisuda.\n\n2. Memfasilitasi minimal 500 mahasiswa per tahun untuk mengikuti program magang dan MBKM.\n\n3. Menjalin kerjasama dengan minimal 100 perusahaan mitra setiap tahunnya.\n\n4. Menyelenggarakan minimal 20 kegiatan pengembangan karir per tahun.\n\n5. Membangun database alumni yang terstruktur untuk tracer study dan networking.",
            'updated_by' => $admin->id,
        ]);

        echo "✓ About page created\n";
    }

    private function createContactPage($admin)
    {
        Kontak::create([
            'alamat' => 'Gedung CDC Polibatam, Jl. Ahmad Yani, Batam Centre, Batam 29461, Kepulauan Riau, Indonesia',
            'telepon' => '0778-469858',
            'email' => 'cdc@polibatam.ac.id',
            'whatsapp' => '081234567890',
            'google_maps_url' => 'https://goo.gl/maps/example',
            'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
            'jam_operasional' => "Senin - Jumat: 08:00 - 16:00 WIB\nSabtu: 08:00 - 12:00 WIB\nMinggu & Libur: Tutup",
            'facebook' => 'https://facebook.com/cdcpolibatam',
            'instagram' => 'https://instagram.com/cdcpolibatam',
            'linkedin' => 'https://linkedin.com/company/cdc-polibatam',
            'twitter' => 'https://twitter.com/cdcpolibatam',
            'updated_by' => $admin->id,
        ]);

        echo "✓ Contact page created\n";
    }

    private function createSampleSubscribers()
    {
        $subscribers = [
            ['email' => 'mahasiswa1@polibatam.ac.id', 'nama' => 'Ahmad Rizki'],
            ['email' => 'mahasiswa2@polibatam.ac.id', 'nama' => 'Siti Nurhaliza'],
            ['email' => 'alumni1@gmail.com', 'nama' => 'Budi Santoso'],
            ['email' => 'alumni2@yahoo.com', 'nama' => 'Rina Wijaya'],
            ['email' => 'user@example.com', 'nama' => null],
        ];

        foreach ($subscribers as $subscriber) {
            LanggananNewsletter::create([
                'email' => $subscriber['email'],
                'nama' => $subscriber['nama'],
                'status' => true,
                'verified_at' => now(),
            ]);
        }

        echo "✓ Sample newsletter subscribers created\n";
    }
}