<?php
// app/Http/Controllers/HomeController.php
// ============================================================================
// PENJELASAN FILE INI:
// File ini adalah Controller untuk SEMUA halaman public (frontend)
// Controller = tempat logika untuk menampilkan halaman yang bisa diakses semua orang
// Fungsinya: menampilkan homepage, lowongan kerja, program magang, berita, tentang, kontak
// ============================================================================

namespace App\Http\Controllers;

// IMPORT CLASS YANG DIBUTUHKAN
use Illuminate\Http\Request;                    // Untuk menangani HTTP request (dari URL, form, dll)
use Illuminate\Support\Facades\Validator;       // Untuk validasi input (cek data valid/tidak)
use App\Models\LowonganKerja;                   // Model untuk tabel lowongan_kerja
use App\Models\ProgramMagang;                   // Model untuk tabel program_magang
use App\Models\Berita;                          // Model untuk tabel berita
use App\Models\Tentang;                         // Model untuk tabel tentang
use App\Models\Kontak;                          // Model untuk tabel kontak
use App\Models\LanggananNewsletter;             // Model untuk tabel langganan_newsletter

class HomeController extends Controller
{
    // ========================================================================
    // HOMEPAGE (KF-01)
    // Halaman utama website, yang pertama kali dilihat pengunjung
    // ========================================================================

    /**
     * Display homepage (KF-01)
     * Fungsi: Menampilkan halaman utama dengan konten featured & statistik
     * URL: GET / (root URL)
     * Return: View homepage dengan data featured content
     */
    public function index()
    {
        // STEP 1: AMBIL LOWONGAN KERJA FEATURED
        // active() = hanya yang aktif dan belum expired
        // orderBy('created_at', 'desc') = urutkan dari yang terbaru
        // limit(6) = ambil 6 data teratas
        // get() = execute query dan ambil hasilnya sebagai collection
        $featuredJobs = LowonganKerja::active()
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // STEP 2: AMBIL PROGRAM MAGANG FEATURED
        // Sama seperti lowongan, ambil 6 program magang terbaru yang aktif
        $featuredPrograms = ProgramMagang::active()
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // STEP 3: AMBIL BERITA FEATURED
        // published() = hanya berita yang sudah dipublikasikan
        // featured() = berita yang ditandai sebagai unggulan (is_featured = true)
        // latest() = urutkan dari yang terbaru
        // limit(3) = ambil 3 berita featured
        $featuredNews = Berita::published()
            ->featured()
            ->latest()
            ->limit(3)
            ->get();

        // STEP 4: HITUNG STATISTIK UNTUK DITAMPILKAN
        // Array = kumpulan data dengan key => value
        $stats = [
            // Total lowongan kerja yang aktif
            'total_jobs' => LowonganKerja::active()->count(),
            
            // Total program magang yang aktif
            'total_programs' => ProgramMagang::active()->count(),
            
            // Total berita yang sudah dipublikasikan
            'total_news' => Berita::published()->count(),
            
            // Total perusahaan yang posting lowongan
            // distinct('perusahaan') = hitung perusahaan yang unik
            // count('perusahaan') = hitung jumlahnya
            // Contoh: PT A post 5 lowongan, PT B post 3 lowongan = 2 perusahaan
            'total_companies' => LowonganKerja::active()
                ->distinct('perusahaan')
                ->count('perusahaan'),
        ];

        // STEP 5: KIRIM DATA KE VIEW
        // compact() = buat array dari variable dengan nama yang sama
        // Contoh: compact('stats') = ['stats' => $stats]
        // View akan ada di: resources/views/home/index.blade.php
        return view('home.index', compact(
            'featuredJobs',      // 6 lowongan terbaru
            'featuredPrograms',  // 6 program magang terbaru
            'featuredNews',      // 3 berita featured
            'stats'              // Statistik angka
        ));
    }

    // ========================================================================
    // LOWONGAN KERJA (KF-02)
    // Halaman daftar semua lowongan kerja dengan filter & search
    // ========================================================================

    /**
     * Display career opportunities page (KF-02)
     * Fungsi: Menampilkan halaman daftar lowongan kerja dengan filter
     * URL: GET /lowongan-kerja
     * Return: View daftar lowongan dengan pagination
     */
    public function lowonganKerja(Request $request)
    {
        // STEP 1: MULAI QUERY
        // active() = hanya ambil lowongan yang aktif dan belum expired
        $query = LowonganKerja::active();

        // STEP 2: FITUR PENCARIAN (SEARCH)
        // filled() = cek apakah parameter 'search' ada dan tidak kosong
        if ($request->filled('search')) {
            // trim() = hapus spasi di awal dan akhir
            $search = trim($request->search);
            
            // Validasi: pastikan setelah trim masih ada isinya
            if (!empty($search)) {
                // search() = scope method di Model LowonganKerja
                // Akan mencari di kolom: judul, perusahaan, deskripsi, lokasi
                $query->search($search);
            }
        }

        // STEP 3: FILTER BERDASARKAN TIPE PEKERJAAN
        // Tipe: full_time, part_time, kontrak, magang
        // !== 'all' = pastikan bukan pilihan "Semua Tipe"
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            // byTipe() = scope method untuk filter tipe
            // WHERE tipe = $request->tipe
            $query->byTipe($request->tipe);
        }

        // STEP 4: FILTER BERDASARKAN KATEGORI INDUSTRI
        // Kategori: teknologi, manufaktur, perdagangan, jasa, lainnya
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            // byKategori() = scope method untuk filter kategori
            // WHERE kategori = $request->kategori
            $query->byKategori($request->kategori);
        }

        // STEP 5: FILTER BERDASARKAN LOKASI
        // Lokasi = text input, bisa partial match
        if ($request->filled('lokasi')) {
            $lokasi = trim($request->lokasi);
            if (!empty($lokasi)) {
                // byLokasi() = scope method untuk filter lokasi
                // WHERE lokasi LIKE '%$lokasi%'
                // Contoh: input "jakarta" akan match "Jakarta Selatan", "Jakarta Barat", dll
                $query->byLokasi($lokasi);
            }
        }

        // STEP 6: SORTING (PENGURUTAN DATA)
        // get() = ambil parameter 'sort' dari URL, default 'latest' jika tidak ada
        $sort = $request->get('sort', 'latest');
        
        // switch-case = pilih action berdasarkan nilai $sort
        switch ($sort) {
            case 'popular':
                // Urutkan berdasarkan jumlah views (paling banyak dilihat)
                // views_count = kolom di database yang menyimpan jumlah views
                // desc = descending (dari besar ke kecil)
                $query->orderBy('views_count', 'desc');
                break;
                
            case 'ending_soon':
                // Urutkan berdasarkan tanggal berakhir (yang paling dekat expired duluan)
                // asc = ascending (dari kecil ke besar / dari dekat ke jauh)
                $query->orderBy('tanggal_berakhir', 'asc');
                break;
                
            default:
                // Default: urutkan dari yang terbaru dibuat
                // created_at = timestamp kapan lowongan dibuat
                // desc = yang terbaru duluan
                $query->orderBy('created_at', 'desc');
        }

        // STEP 7: EXECUTE QUERY & PAGINATION
        // paginate(12) = bagi hasil menjadi halaman, 12 item per halaman
        // withQueryString() = pertahankan parameter URL saat pindah halaman
        // Contoh: /lowongan-kerja?search=IT&tipe=full_time&page=2
        $lowongan = $query->paginate(12)->withQueryString();

        // STEP 8: AMBIL OPSI UNTUK FILTER DROPDOWN
        // getTipeOptions() = static method di Model untuk ambil list tipe
        // Return: array ['full_time' => 'Full Time', 'part_time' => 'Part Time', ...]
        $tipeOptions = LowonganKerja::getTipeOptions();
        
        // getKategoriOptions() = static method untuk ambil list kategori
        $kategoriOptions = LowonganKerja::getKategoriOptions();

        // STEP 9: KIRIM DATA KE VIEW
        return view('home.lowongan-kerja', compact(
            'lowongan',           // Data lowongan (dengan pagination)
            'tipeOptions',        // Opsi untuk dropdown filter tipe
            'kategoriOptions'     // Opsi untuk dropdown filter kategori
        ));
    }

    /**
     * Display single job detail
     * Fungsi: Menampilkan detail lengkap satu lowongan kerja
     * URL: GET /lowongan-kerja/{id}
     * Parameter: $id = ID lowongan yang akan ditampilkan
     * Return: View detail lowongan
     */
    public function lowonganKerjaDetail($id)
    {
        // STEP 1: AMBIL DATA LOWONGAN
        // active() = hanya yang aktif
        // findOrFail() = cari berdasarkan ID, jika tidak ada return 404
        $lowongan = LowonganKerja::active()->findOrFail($id);

        // STEP 2: INCREMENT VIEWS COUNTER
        // incrementViews() = method di model untuk menambah jumlah views
        // Setiap kali halaman ini dibuka, views_count bertambah 1
        // Ini untuk statistik lowongan yang paling banyak dilihat
        $lowongan->incrementViews();

        // STEP 3: AMBIL LOWONGAN TERKAIT (RELATED JOBS)
        // Tujuan: rekomendasi lowongan lain yang mungkin menarik
        $relatedJobs = LowonganKerja::active()
            // where('id', '!=', ...) = kecuali lowongan yang sedang dibuka
            ->where('id', '!=', $lowongan->id)
            // Cari yang kategorinya sama ATAU perusahaannya sama
            ->where(function($query) use ($lowongan) {
                $query->where('kategori', $lowongan->kategori)      // Kategori sama
                      ->orWhere('perusahaan', $lowongan->perusahaan); // ATAU perusahaan sama
            })
            ->limit(3)  // Ambil 3 lowongan terkait
            ->get();

        // STEP 4: KIRIM DATA KE VIEW
        return view('home.lowongan-kerja-detail', compact('lowongan', 'relatedJobs'));
    }

    // ========================================================================
    // PROGRAM MAGANG (KF-03)
    // Halaman daftar semua program magang & MBKM dengan filter
    // ========================================================================

    /**
     * Display internship and MBKM programs page (KF-03)
     * Fungsi: Menampilkan halaman daftar program magang dengan filter
     * URL: GET /program-magang
     * Return: View daftar program dengan pagination
     */
    public function programMagang(Request $request)
    {
        // STEP 1: MULAI QUERY
        $query = ProgramMagang::active();

        // STEP 2: FITUR PENCARIAN
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                // search() akan mencari di: judul, perusahaan, deskripsi
                $query->search($search);
            }
        }

        // STEP 3: FILTER BERDASARKAN TIPE PROGRAM
        // Tipe: mbkm, magang_reguler, magang_independen
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            $query->byTipe($request->tipe);
        }

        // STEP 4: FILTER BERDASARKAN LOKASI
        if ($request->filled('lokasi')) {
            $lokasi = trim($request->lokasi);
            if (!empty($lokasi)) {
                $query->byLokasi($lokasi);
            }
        }

        // STEP 5: SORTING
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'ending_soon':
                $query->orderBy('tanggal_berakhir', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // STEP 6: EXECUTE & PAGINATION
        $programs = $query->paginate(12)->withQueryString();

        // STEP 7: AMBIL OPSI FILTER
        // getTipeOptions() = array tipe program
        $tipeOptions = ProgramMagang::getTipeOptions();

        // STEP 8: KIRIM DATA KE VIEW
        return view('home.program-magang', compact('programs', 'tipeOptions'));
    }

    /**
     * Display single program detail
     * Fungsi: Menampilkan detail lengkap satu program magang
     * URL: GET /program-magang/{id}
     * Parameter: $id = ID program yang akan ditampilkan
     * Return: View detail program
     */
    public function programMagangDetail($id)
    {
        // STEP 1: AMBIL DATA PROGRAM
        $program = ProgramMagang::active()->findOrFail($id);

        // STEP 2: INCREMENT VIEWS
        $program->incrementViews();

        // STEP 3: AMBIL PROGRAM TERKAIT
        // Cari berdasarkan tipe sama atau perusahaan sama
        $relatedPrograms = ProgramMagang::active()
            ->where('id', '!=', $program->id)
            ->where(function($query) use ($program) {
                $query->where('tipe', $program->tipe)
                      ->orWhere('perusahaan', $program->perusahaan);
            })
            ->limit(3)
            ->get();

        // STEP 4: KIRIM DATA KE VIEW
        return view('home.program-magang-detail', compact('program', 'relatedPrograms'));
    }

    // ========================================================================
    // BERITA/NEWSLETTER (KF-04)
    // Halaman daftar semua berita dengan filter & featured news
    // ========================================================================

    /**
     * Display news/newsletter page (KF-04)
     * Fungsi: Menampilkan halaman daftar berita dengan filter
     * URL: GET /berita
     * Return: View daftar berita dengan pagination
     */
    public function berita(Request $request)
    {
        // STEP 1: MULAI QUERY
        // published() = hanya berita yang sudah dipublikasikan
        $query = Berita::published();

        // STEP 2: FITUR PENCARIAN
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                // search() akan mencari di: judul, konten, ringkasan, penulis
                $query->search($search);
            }
        }

        // STEP 3: FILTER BERDASARKAN KATEGORI
        // Kategori: karir, mbkm, magang, umum
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->byKategori($request->kategori);
        }

        // STEP 4: SORTING
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                // Urutkan berdasarkan jumlah views
                $query->orderBy('views_count', 'desc');
                break;
                
            case 'oldest':
                // Urutkan dari yang paling lama
                // tanggal_publikasi = tanggal berita dipublikasikan
                // asc = ascending (dari lama ke baru)
                $query->orderBy('tanggal_publikasi', 'asc');
                break;
                
            default:
                // Default: dari yang terbaru
                // latest() = shortcut untuk orderBy('created_at', 'desc')
                $query->latest();
        }

        // STEP 5: EXECUTE & PAGINATION
        // 9 item per halaman (3x3 grid layout biasanya)
        $berita = $query->paginate(9)->withQueryString();

        // STEP 6: AMBIL BERITA FEATURED
        // featured() = berita unggulan (is_featured = true)
        // Ditampilkan terpisah di bagian atas halaman
        $featuredNews = Berita::published()
            ->featured()
            ->latest()
            ->limit(3)
            ->get();

        // STEP 7: AMBIL OPSI KATEGORI
        $kategoriOptions = Berita::getKategoriOptions();

        // STEP 8: KIRIM DATA KE VIEW
        return view('home.berita', compact('berita', 'featuredNews', 'kategoriOptions'));
    }

    /**
     * Display single news detail
     * Fungsi: Menampilkan detail lengkap satu berita
     * URL: GET /berita/{slug}
     * Parameter: $slug = slug berita (bukan ID, lebih SEO friendly)
     * Return: View detail berita
     */
    public function beritaDetail($slug)
    {
        // STEP 1: AMBIL DATA BERITA BERDASARKAN SLUG
        // where('slug', $slug) = cari berdasarkan slug
        // firstOrFail() = ambil yang pertama, jika tidak ada return 404
        // Kenapa slug? Untuk URL yang lebih bagus dan SEO friendly
        // Contoh: /berita/cara-membuat-website (slug) vs /berita/123 (ID)
        $berita = Berita::published()->where('slug', $slug)->firstOrFail();

        // STEP 2: INCREMENT VIEWS
        $berita->incrementViews();

        // STEP 3: AMBIL BERITA TERKAIT
        // Cari berita lain dengan kategori yang sama
        $relatedNews = Berita::published()
            ->where('id', '!=', $berita->id)        // Kecuali berita ini
            ->where('kategori', $berita->kategori)  // Kategori sama
            ->latest()
            ->limit(3)
            ->get();

        // STEP 4: KIRIM DATA KE VIEW
        return view('home.berita-detail', compact('berita', 'relatedNews'));
    }

    // ========================================================================
    // TENTANG (KF-05)
    // Halaman tentang organisasi/perusahaan
    // ========================================================================

    /**
     * Display about page (KF-05)
     * Fungsi: Menampilkan halaman tentang (sejarah, visi, misi, tujuan)
     * URL: GET /tentang
     * Return: View halaman tentang
     */
    public function tentang()
    {
        // STEP 1: AMBIL DATA TENTANG
        // first() = ambil record pertama (seharusnya cuma ada 1 record)
        // Tabel tentang biasanya hanya punya 1 record untuk menyimpan informasi organisasi
        $tentang = Tentang::first();

        // STEP 2: CEK APAKAH DATA ADA
        // Jika data belum dibuat oleh admin, tampilkan 404
        if (!$tentang) {
            // abort(404) = stop execution dan tampilkan halaman 404
            // Parameter 2: pesan error
            abort(404, 'Halaman tentang belum tersedia.');
        }

        // STEP 3: KIRIM DATA KE VIEW
        return view('home.tentang', compact('tentang'));
    }

    // ========================================================================
    // KONTAK (KF-06)
    // Halaman kontak dengan informasi alamat, telepon, email, dll
    // ========================================================================

    /**
     * Display contact page (KF-06)
     * Fungsi: Menampilkan halaman kontak
     * URL: GET /kontak
     * Return: View halaman kontak
     */
    public function kontak()
    {
        // STEP 1: AMBIL DATA KONTAK
        $kontak = Kontak::first();

        // STEP 2: CEK APAKAH DATA ADA
        if (!$kontak) {
            abort(404, 'Halaman kontak belum tersedia.');
        }

        // STEP 3: KIRIM DATA KE VIEW
        return view('home.kontak', compact('kontak'));
    }

    // ========================================================================
    // NEWSLETTER SUBSCRIPTION (KF-08)
    // Fitur berlangganan newsletter via email
    // ========================================================================

    /**
     * Handle newsletter subscription (KF-08)
     * Fungsi: Memproses langganan newsletter dari form
     * URL: POST /newsletter/subscribe
     * Method: POST (mengirim data baru)
     * Return: Redirect kembali dengan pesan sukses/error
     */
    public function subscribeNewsletter(Request $request)
    {
        // STEP 1: VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            // EMAIL
            // required = wajib diisi
            // email = harus format email valid
            // unique:langganan_newsletter,email = email harus unik (tidak boleh duplikat)
            'email' => 'required|email|unique:langganan_newsletter,email',
            
            // NAMA (OPSIONAL)
            // nullable = boleh kosong
            // string = harus text
            // max:255 = maksimal 255 karakter
            'nama' => 'nullable|string|max:255',
        ], [
            // Custom error messages dalam Bahasa Indonesia
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        // STEP 2: CEK HASIL VALIDASI
        if ($validator->fails()) {
            // back() = kembali ke halaman sebelumnya
            // withErrors() = kirim pesan error
            // withInput() = kirim input lama (email tetap terisi)
            // with('newsletter_error', true) = flag khusus untuk form newsletter
            // Ini agar error message muncul di form newsletter, bukan form lain
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('newsletter_error', true);
        }

        // STEP 3: SANITIZE INPUT (BERSIHKAN DATA)
        // trim() = hapus spasi di awal dan akhir
        // Contoh: "  email@test.com  " menjadi "email@test.com"
        $email = trim($request->email);
        
        // Jika nama diisi, trim juga. Jika tidak, set null
        // ? : = ternary operator (if-else singkat)
        $nama = $request->nama ? trim($request->nama) : null;

        // STEP 4: VALIDASI TAMBAHAN (CEK TIDAK KOSONG)
        // Setelah di-trim, pastikan email tidak jadi string kosong
        if (empty($email)) {
            return back()
                ->withErrors(['email' => 'Email tidak boleh kosong.'])
                ->withInput()
                ->with('newsletter_error', true);
        }

        // STEP 5: SIMPAN SUBSCRIPTION KE DATABASE
        // create() = insert data baru ke tabel langganan_newsletter
        LanggananNewsletter::create([
            'email' => $email,
            'nama' => $nama,
            'status' => true,           // Status aktif
            'verified_at' => now(),     // Auto-verify (untuk simplicity)
            // now() = timestamp sekarang
            // Dalam production, biasanya perlu verifikasi via email dulu
        ]);

        // STEP 6: REDIRECT DENGAN PESAN SUKSES
        // back() = kembali ke halaman sebelumnya
        // with('success', ...) = flash message sukses
        return back()->with('success', 'Terima kasih! Anda telah berlangganan newsletter kami.');
    }

    /**
     * Unsubscribe from newsletter
     * Fungsi: Berhenti berlangganan newsletter
     * URL: GET /newsletter/unsubscribe/{email}
     * Parameter: $email = email yang ingin unsubscribe
     * Return: Redirect ke homepage dengan pesan
     */
    public function unsubscribeNewsletter($email)
    {
        // STEP 1: CARI SUBSCRIBER BERDASARKAN EMAIL
        // where('email', $email) = cari berdasarkan email
        // first() = ambil yang pertama (return null jika tidak ada)
        $subscriber = LanggananNewsletter::where('email', $email)->first();

        // STEP 2: CEK APAKAH SUBSCRIBER ADA
        if (!$subscriber) {
            // Jika tidak ada, redirect ke home dengan pesan error
            return redirect()->route('home')->with('error', 'Email tidak ditemukan.');
        }

        // STEP 3: UNSUBSCRIBE
        // unsubscribe() = method di model untuk set status jadi false
        // Subscriber tidak dihapus, hanya di-nonaktifkan
        $subscriber->unsubscribe();

        // STEP 4: REDIRECT KE HOME
        return redirect()->route('home')->with('success', 'Anda telah berhenti berlangganan newsletter.');
    }

    /**
     * Display Rekap Magang page (NEW)
     */
    public function rekapMagang()
    {
        // External links data
        $links = [
            [
                'title' => 'Borang Pendaftaran Magang',
                'description' => 'Formulir pendaftaran untuk program magang mahasiswa',
                'url' => 'https://intranet.polibatam.ac.id/SISTEM%20PENJAMINAN%20%20MUTU%20INTERNAL/PROSES%20BISNIS/PB%2008%20-%20Pelaksanaan%20Pembelajaran/BO/No.BO.8.4.1.1-V0%20Borang%20Pendaftaran%20Magang.doc',
                'icon' => 'bi-file-earmark-text'
            ],
            [
                'title' => 'Form Pengajuan Surat Pengantar/Permohonan Magang',
                'description' => 'Form online untuk pengajuan surat pengantar magang',
                'url' => 'https://form.jotform.com/250021251341437',
                'icon' => 'bi-file-earmark-check'
            ],
            [
                'title' => 'Balasan Surat Magang',
                'description' => 'Arsip balasan surat magang dari perusahaan',
                'url' => 'https://drive.google.com/drive/folders/1y3rXelv2hfwzxJ_x6UJdkHomaTCOK51N?usp=sharing',
                'icon' => 'bi-folder2-open'
            ],
            [
                'title' => 'Konfirmasi Penerimaan Magang',
                'description' => 'Form konfirmasi penerimaan mahasiswa magang',
                'url' => 'https://form.jotform.com/250199363422457',
                'icon' => 'bi-check2-circle'
            ],
            [
                'title' => 'Surat Magang Scan',
                'description' => 'Dokumen scan surat magang mahasiswa',
                'url' => 'https://drive.google.com/drive/folders/1IisNPkSAJhMba4tX8FAKD8WiKVYrlRO5?usp=sharing',
                'icon' => 'bi-file-earmark-pdf'
            ],
            [
                'title' => 'Form Pendataan Magang Luar Negeri',
                'description' => 'Formulir khusus untuk magang di luar negeri',
                'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScQIC27zsesmlUsN6k65UeYy9UNt4nqUjmnFOWcDXdA88Q_Vw/viewform',
                'icon' => 'bi-globe'
            ],
            [
                'title' => 'Kartu BPJS TK Mahasiswa Magang 1',
                'description' => 'Dokumen BPJS Ketenagakerjaan mahasiswa magang batch 1',
                'url' => 'https://drive.google.com/drive/folders/1pJAuTXMO5JPzsvvajYkIEbq8hMHyhL7b',
                'icon' => 'bi-card-list'
            ],
            [
                'title' => 'Kartu BPJS TK Mahasiswa Magang 2',
                'description' => 'Dokumen BPJS Ketenagakerjaan mahasiswa magang batch 2',
                'url' => 'https://drive.google.com/drive/folders/1KO7-63Gqvb_FJv2VR5z87edYE5yC3u_K',
                'icon' => 'bi-card-list'
            ],
            [
                'title' => 'Rekap Permintaan Magang Dari Perusahaan',
                'description' => 'Database permintaan magang dari berbagai perusahaan',
                'url' => 'https://docs.google.com/spreadsheets/d/1KVwzG3-pyXM2oojYo3kgCjWQVqhIZa_opzurKhfqkvI/edit?gid=0#gid=0',
                'icon' => 'bi-table'
            ],
            [
                'title' => 'Rekap Data Luar Negeri',
                'description' => 'Rekapitulasi data mahasiswa magang luar negeri',
                'url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQ77OX0zTsXIKpHDs4jS4MnZZjjnFQS6Y2hGgL64Ej6XWFd88MvbCvzJObsFXj_cozQD0WF_Vb5ylg5/pubhtml?gid=666477961&single=true',
                'icon' => 'bi-graph-up'
            ],
            [
                'title' => 'MyInternship',
                'description' => 'Platform manajemen magang mahasiswa',
                'url' => 'https://myinternship.id/',
                'icon' => 'bi-laptop'
            ],
            [
                'title' => 'Talenthub Polibatam',
                'description' => 'Portal talent dan karir Polibatam',
                'url' => 'https://talenthub.polibatam.ac.id/',
                'icon' => 'bi-people'
            ],
        ];

        return view('home.rekap-magang', compact('links'));
    }

    /**
     * Display Rekap MBKM page (NEW)
     */
    public function rekapMbkm()
    {
        // External links data
        $links = [
            [
                'title' => 'Awardee IISMAVO 2023',
                'description' => 'Daftar penerima IISMAVO tahun 2023',
                'url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSxIv1UI3VGAHqGb9jaPH42ZGTcUa6jJSrE_sahHrmVvYZFZ-C_pdR_HabijdQtogN_ifJaEeBkQBCj/pubhtml?gid=0&single=true',
                'icon' => 'bi-award'
            ],
            [
                'title' => 'Awardee IISMAVO 2022',
                'description' => 'Daftar penerima IISMAVO tahun 2022',
                'url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vR4ZBkLmxEPW1QQLr1gqaNY6tSTJLbuzMBoZrcoYNkyRtOBXYp5bInpdIOjoUvXobtViTIW12lixMxq/pubhtml?gid=0&single=true',
                'icon' => 'bi-award'
            ],
            [
                'title' => 'Rekap MSIB Batch 6 (Januari/Juli 2024)',
                'description' => 'Rekapitulasi peserta MSIB Batch 6',
                'url' => 'https://docs.google.com/spreadsheets/d/1yiNskxT3_JOqgoYi8bkiu3d-tSFdJv0QhhpgqfCl5EM/edit?gid=0#gid=0',
                'icon' => 'bi-journal-text'
            ],
            [
                'title' => 'Rekap MSIB Batch 5 (Agustus/Desember 2023)',
                'description' => 'Rekapitulasi peserta MSIB Batch 5',
                'url' => 'https://docs.google.com/spreadsheets/d/1yiNskxT3_JOqgoYi8bkiu3d-tSFdJv0QhhpgqfCl5EM/edit?gid=0#gid=0',
                'icon' => 'bi-journal-text'
            ],
            [
                'title' => 'Rekap MSIB Batch 3 (Agustus/Desember 2022)',
                'description' => 'Rekapitulasi peserta MSIB Batch 3',
                'url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSPMfH9WyRTL96chi9sgBXxKhqfyIn7KIMCT-MAPEZIYC_2R_mQ1HxVrTQwr5eZKg/pubhtml',
                'icon' => 'bi-journal-text'
            ],
            [
                'title' => 'Rekap MSIB Batch 2 (Januari/Juli 2022)',
                'description' => 'Rekapitulasi peserta MSIB Batch 2',
                'url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vS7x1o_Y1lnZV12Vi5ZGNnKj4YRREUxNszwI082N0r__jqk1ctaIMaDKyStbJYTX-wXR3R-8NcKf75G/pubhtml?gid=1723333423&single=true',
                'icon' => 'bi-journal-text'
            ],
            [
                'title' => 'Data Mahasiswa Poltek Keluar PMM4 Genap 23/24',
                'description' => 'Data mahasiswa yang mengikuti PMM4 semester genap',
                'url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSrAmjVifinlXEMgAwckah-TURMvvUusREJ2Y1QzlHrU6DAhjry3zoqd3nO86YRwg/pubhtml?gid=1702176397&single=true',
                'icon' => 'bi-people'
            ],
            [
                'title' => 'Data Mahasiswa Poltek Keluar PMM3 Ganjil 23/24',
                'description' => 'Data mahasiswa yang mengikuti PMM3 semester ganjil',
                'url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vRPmLyIHrKQkjW3GGEYNIWjtOU1n8lkkzDczA3qufVoklmnGgKUBcsb5myYqBkyEw/pubhtml?gid=988681260&single=true',
                'icon' => 'bi-people'
            ],
        ];

        return view('home.rekap-mbkm', compact('links'));
    }

    /**
     * Display Tracer Study page (NEW)
     */
    public function tracerStudy()
    {
        // External links data
        $links = [
            [
                'title' => 'Tracer Study Polibatam',
                'description' => 'Sistem pelacakan alumni dan karir lulusan Polibatam',
                'url' => 'https://tracer.polibatam.ac.id/',
                'icon' => 'bi-graph-up-arrow',
                'featured' => true
            ],
            [
                'title' => 'Tracer Study Portal 2',
                'description' => 'Portal alternatif untuk mengakses sistem tracer study',
                'url' => 'https://login.microsoftonline.com/cd084a28-5844-4df5-a775-0144b2c4ea6b/oauth2/authorize?client%5Fid=00000003%2D0000%2D0ff1%2Dce00%2D000000000000&response%5Fmode=form%5Fpost&response%5Ftype=code%20id%5Ftoken&resource=00000003%2D0000%2D0ff1%2Dce00%2D000000000000&scope=openid&nonce=68632DCD82067A585726EBD15AA854CA6F40AACFD10219AC%2DA243EA96983DCE3BB7B67D6B5B09F25BF42045FD0AABD1392E34C33670411633&redirect%5Furi=https%3A%2F%2Fpolbat%2Dmy%2Esharepoint%2Ecom%2F%5Fforms%2Fdefault%2Easpx&state=OD0w&claims=%7B%22id%5Ftoken%22%3A%7B%22xms%5Fcc%22%3A%7B%22values%22%3A%5B%22CP1%22%5D%7D%7D%7D&wsucxt=1&cobrandid=11bd8083%2D87e0%2D41b5%2Dbb78%2D0bc43c8a8e8a&client%2Drequest%2Did=1389d7a1%2D20c8%2D6000%2D2dbc%2D7ac9b8d60ecc&sso_reload=true',
                'icon' => 'bi-door-open',
                'featured' => false
            ],
        ];

        return view('home.tracer-study', compact('links'));
    }
}