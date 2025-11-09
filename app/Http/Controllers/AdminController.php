<?php
// app/Http/Controllers/AdminController.php
// ============================================================================
// PENJELASAN FILE INI:
// File ini adalah Controller untuk SEMUA halaman admin (backend/dashboard)
// Controller = tempat logika bisnis, seperti "otak" dari aplikasi
// Fungsinya: mengelola data lowongan kerja, program magang, berita, dll
// ============================================================================

namespace App\Http\Controllers;

// IMPORT CLASS YANG DIBUTUHKAN
// Import = mengambil/memanggil class dari file lain untuk digunakan di sini
use Illuminate\Http\Request;                    // Untuk menangani HTTP request (data dari form, URL, dll)
use Illuminate\Support\Facades\Validator;       // Untuk validasi input (cek apakah data valid)
use Illuminate\Support\Facades\Storage;         // Untuk mengelola file (upload, hapus gambar, dll)
use Illuminate\Support\Str;                     // Helper untuk manipulasi string (random, slug, dll)

// Import Model - Model = representasi tabel database
use App\Models\LowonganKerja;                   // Model untuk tabel lowongan_kerja
use App\Models\ProgramMagang;                   // Model untuk tabel program_magang
use App\Models\Berita;                          // Model untuk tabel berita
use App\Models\Tentang;                         // Model untuk tabel tentang
use App\Models\Kontak;                          // Model untuk tabel kontak
use App\Models\LanggananNewsletter;             // Model untuk tabel langganan_newsletter
use App\Models\ActivityLog;                     // Model untuk mencatat semua aktivitas admin

class AdminController extends Controller
{
    // ========================================================================
    // DASHBOARD - Halaman utama admin setelah login
    // ========================================================================
    
    /**
     * Display admin dashboard (KF-10)
     * Fungsi: Menampilkan halaman dashboard dengan statistik dan data terbaru
     * URL: GET /admin/dashboard
     * Return: View dashboard dengan data statistik
     */
    public function dashboard()
    {
        // STEP 1: HITUNG STATISTIK
        // Array = kumpulan data dengan key => value
        // count() = menghitung jumlah record/baris di database
        $stats = [
            // Total semua lowongan kerja (aktif + expired + nonaktif)
            'total_jobs' => LowonganKerja::count(),
            
            // Lowongan kerja yang aktif (status=true DAN belum expired)
            // active() = scope method di model (custom query)
            'active_jobs' => LowonganKerja::active()->count(),
            
            // Lowongan kerja yang sudah expired (tanggal_berakhir < hari ini)
            'expired_jobs' => LowonganKerja::expired()->count(),
            
            // Total program magang
            'total_programs' => ProgramMagang::count(),
            
            // Program magang aktif
            'active_programs' => ProgramMagang::active()->count(),
            
            // Program magang expired
            'expired_programs' => ProgramMagang::expired()->count(),
            
            // Total berita
            'total_news' => Berita::count(),
            
            // Berita yang sudah dipublikasikan (status=true)
            'published_news' => Berita::published()->count(),
            
            // Total subscriber newsletter yang aktif
            'total_subscribers' => LanggananNewsletter::active()->count(),
        ];

        // STEP 2: AMBIL AKTIVITAS TERBARU
        // with('user') = eager loading, ambil data user sekaligus (join)
        // latest() = urutkan dari yang terbaru (DESC by created_at)
        // limit(10) = ambil 10 data teratas
        // get() = execute query dan ambil hasilnya
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // STEP 3: AMBIL KONTEN POPULER (berdasarkan jumlah views)
        // popular(5) = scope method di model, ambil 5 yang paling banyak dilihat
        $popularJobs = LowonganKerja::active()->popular(5)->get();
        $popularPrograms = ProgramMagang::active()->popular(5)->get();
        $popularNews = Berita::published()->popular(5)->get();

        // STEP 4: AMBIL KONTEN TERBARU
        // latest() = urutkan dari yang paling baru dibuat
        $recentJobs = LowonganKerja::latest()->limit(5)->get();
        $recentPrograms = ProgramMagang::latest()->limit(5)->get();
        $recentNews = Berita::latest()->limit(5)->get();

        // STEP 5: KIRIM DATA KE VIEW
        // view() = memanggil file blade (template HTML)
        // compact() = membuat array dari variable dengan nama yang sama
        // Contoh: compact('stats') sama dengan ['stats' => $stats]
        return view('admin.dashboard', compact(
            'stats',                // Statistik angka
            'recentActivities',     // Log aktivitas terbaru
            'popularJobs',          // Lowongan paling populer
            'popularPrograms',      // Program paling populer
            'popularNews',          // Berita paling populer
            'recentJobs',           // Lowongan terbaru
            'recentPrograms',       // Program terbaru
            'recentNews'            // Berita terbaru
        ));
    }

    // ========================================================================
    // LOWONGAN KERJA MANAGEMENT (KF-11)
    // Bagian ini untuk mengelola semua data lowongan kerja
    // CRUD = Create, Read, Update, Delete
    // ========================================================================

    /**
     * Display list of job opportunities
     * Fungsi: Menampilkan daftar semua lowongan kerja dengan filter & search
     * URL: GET /admin/lowongan-kerja
     * Return: View daftar lowongan dengan pagination
     */
    public function lowonganKerjaIndex(Request $request)
    {
        // STEP 1: MULAI QUERY
        // Query Builder = cara membuat SQL query secara bertahap
        // with() = eager loading untuk relasi (join dengan tabel users)
        $query = LowonganKerja::with(['creator', 'updater']);

        // STEP 2: SEARCH (Pencarian)
        // filled() = cek apakah input ada dan tidak kosong
        if ($request->filled('search')) {
            // trim() = hapus spasi di awal/akhir
            $search = trim($request->search);
            
            // Cek lagi apakah setelah di-trim masih ada isinya
            if (!empty($search)) {
                // search() = scope method di model untuk pencarian
                // Akan mencari di kolom: judul, perusahaan, deskripsi
                $query->search($search);
            }
        }

        // STEP 3: FILTER BY STATUS
        // Filter = menyaring data berdasarkan kondisi tertentu
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                // active() = ambil yang aktif dan belum expired
                $query->active();
            } elseif ($request->status === 'expired') {
                // expired() = ambil yang sudah lewat tanggal berakhir
                $query->expired();
            } elseif ($request->status === 'inactive') {
                // where() = kondisi SQL WHERE
                // Ambil yang status=false (nonaktif manual)
                $query->where('status', false);
            }
        }

        // STEP 4: FILTER BY TYPE (Tipe Pekerjaan)
        // !== 'all' = bukan pilihan "Semua"
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            // byTipe() = scope method untuk filter berdasarkan tipe
            // Tipe: full_time, part_time, kontrak, magang
            $query->byTipe($request->tipe);
        }

        // STEP 5: FILTER BY CATEGORY (Kategori Industri)
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            // byKategori() = filter berdasarkan kategori industri
            // Kategori: teknologi, manufaktur, perdagangan, jasa, lainnya
            $query->byKategori($request->kategori);
        }

        // STEP 6: EXECUTE QUERY & PAGINATION
        // latest() = urutkan dari yang terbaru
        // paginate(15) = bagi hasil per halaman (15 item per halaman)
        // withQueryString() = pertahankan parameter URL saat pindah halaman
        $lowongan = $query->latest()->paginate(15)->withQueryString();

        // STEP 7: RETURN VIEW
        // Kirim data $lowongan ke view untuk ditampilkan
        return view('admin.lowongan-kerja.index', compact('lowongan'));
    }

    /**
     * Show form to create new job opportunity
     * Fungsi: Menampilkan form kosong untuk menambah lowongan baru
     * URL: GET /admin/lowongan-kerja/create
     */
    public function lowonganKerjaCreate()
    {
        // Hanya menampilkan form kosong, tidak ada logika kompleks
        return view('admin.lowongan-kerja.create');
    }

    /**
     * Store new job opportunity
     * Fungsi: Menyimpan data lowongan baru dari form ke database
     * URL: POST /admin/lowongan-kerja
     * Method: POST (karena mengirim/menyimpan data)
     */
    public function lowonganKerjaStore(Request $request)
    {
        // STEP 1: VALIDASI INPUT
        // Validasi = pengecekan apakah data yang dikirim valid
        // Validator::make() = membuat validator
        // Parameter 1: data yang akan divalidasi ($request->all())
        // Parameter 2: rules/aturan validasi
        // Parameter 3: custom error messages
        $validator = Validator::make($request->all(), [
            // RULES VALIDASI:
            // required = wajib diisi
            // string = harus berupa text
            // max:255 = maksimal 255 karakter
            'judul' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            
            // in:... = harus salah satu dari pilihan yang ada
            'tipe' => 'required|in:full_time,part_time,kontrak,magang',
            'kategori' => 'required|in:teknologi,manufaktur,perdagangan,jasa,lainnya',
            
            // nullable = boleh kosong
            // numeric = harus angka
            // min:0 = minimal 0
            'gaji_min' => 'nullable|numeric|min:0',
            
            // gte:gaji_min = harus >= (greater than or equal) dari gaji_min
            'gaji_max' => 'nullable|numeric|min:0|gte:gaji_min',
            
            // boolean = harus true/false (0/1)
            'gaji_negotiable' => 'boolean',
            
            // email = harus format email valid
            'email_aplikasi' => 'required|email',
            
            // date = harus format tanggal
            // after:today = harus setelah hari ini
            'tanggal_berakhir' => 'required|date|after:today',
            
            // image = harus file gambar
            // mimes:... = extensi yang dibolehkan
            // max:2048 = maksimal 2MB (dalam kilobytes)
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            'status' => 'boolean',
        ], [
            // CUSTOM ERROR MESSAGES (dalam Bahasa Indonesia)
            // Format: 'field.rule' => 'pesan error'
            'judul.required' => 'Judul wajib diisi.',
            'perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'tipe.required' => 'Tipe pekerjaan wajib dipilih.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'email_aplikasi.required' => 'Email aplikasi wajib diisi.',
            'email_aplikasi.email' => 'Format email tidak valid.',
            'tanggal_berakhir.required' => 'Tanggal berakhir wajib diisi.',
            'tanggal_berakhir.after' => 'Tanggal berakhir harus setelah hari ini.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // STEP 2: CEK HASIL VALIDASI
        // fails() = return true jika validasi gagal
        if ($validator->fails()) {
            // back() = kembali ke halaman sebelumnya
            // withErrors() = kirim pesan error ke view
            // withInput() = kirim input lama agar form tidak kosong
            return back()->withErrors($validator)->withInput();
        }

        // STEP 3: SIAPKAN DATA UNTUK DISIMPAN
        // except('gambar') = ambil semua input KECUALI gambar
        // Kenapa kecuali gambar? Karena gambar dihandle terpisah (upload file)
        $data = $request->except('gambar');
        
        // Tambahkan ID user yang sedang login sebagai pembuat
        // auth()->id() = ambil ID user yang sedang login
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // STEP 4: HANDLE IMAGE UPLOAD (Upload Gambar)
        // hasFile() = cek apakah ada file yang diupload
        if ($request->hasFile('gambar')) {
            // Ambil file gambar dari request
            $image = $request->file('gambar');
            
            // BUAT NAMA FILE UNIK
            // Format: lowongan_timestamp_randomstring.extension
            // time() = timestamp saat ini (mencegah nama duplikat)
            // Str::random(10) = string random 10 karakter
            // getClientOriginalExtension() = ambil ekstensi file asli (.jpg, .png, dll)
            $filename = 'lowongan_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // SIMPAN FILE KE STORAGE
            // storeAs() = simpan file dengan nama tertentu
            // Parameter 1: folder tujuan (storage/app/public/lowongan_kerja)
            // Parameter 2: nama file
            // Parameter 3: disk ('public' = storage/app/public/)
            $path = $image->storeAs('lowongan_kerja', $filename, 'public');
            
            // Simpan path ke array $data
            $data['gambar'] = $path;
        }

        // STEP 5: SIMPAN KE DATABASE
        // create() = insert data baru ke database
        // Return: object model yang baru dibuat
        $lowongan = LowonganKerja::create($data);

        // STEP 6: LOG ACTIVITY (Catat aktivitas admin)
        // log() = static method untuk mencatat aktivitas
        // Parameter 1: deskripsi aktivitas
        // Parameter 2: model yang terkait
        // Parameter 3: jenis event (create/update/delete)
        ActivityLog::log('Created job opportunity: ' . $lowongan->judul, $lowongan, 'create');

        // STEP 7: REDIRECT DENGAN PESAN SUKSES
        // redirect() = pindah ke halaman lain
        // route() = generate URL dari nama route
        // with() = flash message (pesan yang muncul sekali)
        return redirect()->route('admin.lowongan-kerja.index')
            ->with('success', 'Lowongan kerja berhasil ditambahkan.');
    }

    /**
     * Show form to edit job opportunity
     * Fungsi: Menampilkan form edit dengan data lowongan yang sudah ada
     * URL: GET /admin/lowongan-kerja/{id}/edit
     * Parameter: $id = ID lowongan yang akan diedit
     */
    public function lowonganKerjaEdit($id)
    {
        // findOrFail() = cari data berdasarkan ID
        // Jika tidak ditemukan, otomatis return 404 error
        $lowongan = LowonganKerja::findOrFail($id);
        
        // Kirim data lowongan ke view untuk ditampilkan di form
        return view('admin.lowongan-kerja.edit', compact('lowongan'));
    }

    /**
     * Update job opportunity
     * Fungsi: Memperbarui data lowongan yang sudah ada
     * URL: PUT/PATCH /admin/lowongan-kerja/{id}
     * Method: PUT (update data yang sudah ada)
     */
    public function lowonganKerjaUpdate(Request $request, $id)
    {
        // STEP 1: CARI DATA YANG AKAN DIUPDATE
        $lowongan = LowonganKerja::findOrFail($id);

        // STEP 2: VALIDASI INPUT
        // Rules sama dengan create, tapi tidak ada validasi 'after:today'
        // Karena lowongan lama boleh tetap aktif meski tanggalnya sudah lewat
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'tipe' => 'required|in:full_time,part_time,kontrak,magang',
            'kategori' => 'required|in:teknologi,manufaktur,perdagangan,jasa,lainnya',
            'gaji_min' => 'nullable|numeric|min:0',
            'gaji_max' => 'nullable|numeric|min:0|gte:gaji_min',
            'gaji_negotiable' => 'boolean',
            'email_aplikasi' => 'required|email',
            'tanggal_berakhir' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // STEP 3: SIAPKAN DATA
        $data = $request->except('gambar');
        $data['updated_by'] = auth()->id();

        // STEP 4: HANDLE IMAGE UPLOAD
        if ($request->hasFile('gambar')) {
            // HAPUS GAMBAR LAMA JIKA ADA
            // exists() = cek apakah file ada di storage
            if ($lowongan->gambar && Storage::disk('public')->exists($lowongan->gambar)) {
                // delete() = hapus file dari storage
                Storage::disk('public')->delete($lowongan->gambar);
            }

            // UPLOAD GAMBAR BARU
            $image = $request->file('gambar');
            $filename = 'lowongan_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('lowongan_kerja', $filename, 'public');
            $data['gambar'] = $path;
        }

        // STEP 5: UPDATE DATABASE
        // update() = update data yang sudah ada
        $lowongan->update($data);

        // STEP 6: LOG ACTIVITY
        ActivityLog::log('Updated job opportunity: ' . $lowongan->judul, $lowongan, 'update');

        // STEP 7: REDIRECT
        return redirect()->route('admin.lowongan-kerja.index')
            ->with('success', 'Lowongan kerja berhasil diperbarui.');
    }

    /**
     * Delete job opportunity
     * Fungsi: Menghapus lowongan kerja dari database
     * URL: DELETE /admin/lowongan-kerja/{id}
     * Method: DELETE (menghapus data)
     */
    public function lowonganKerjaDestroy($id)
    {
        // STEP 1: CARI DATA
        $lowongan = LowonganKerja::findOrFail($id);
        
        // Simpan judul untuk log (karena setelah delete, data hilang)
        $judul = $lowongan->judul;

        // STEP 2: HAPUS DATA
        // delete() = soft delete (data tidak benar-benar dihapus, hanya di-mark)
        // Bisa di-restore kembali jika perlu
        $lowongan->delete();

        // STEP 3: LOG ACTIVITY
        // Parameter 2 = null karena datanya sudah dihapus
        ActivityLog::log('Deleted job opportunity: ' . $judul, null, 'delete');

        // STEP 4: REDIRECT
        // back() = kembali ke halaman sebelumnya
        return back()->with('success', 'Lowongan kerja berhasil dihapus.');
    }

    // ========================================================================
    // PROGRAM MAGANG MANAGEMENT (KF-12)
    // Logikanya sama persis dengan Lowongan Kerja, hanya beda model dan field
    // ========================================================================

    /**
     * Display list of internship programs
     * Fungsi: Menampilkan daftar program magang
     * URL: GET /admin/program-magang
     */
    public function programMagangIndex(Request $request)
    {
        // Query dengan eager loading relasi creator dan updater
        $query = ProgramMagang::with(['creator', 'updater']);

        // Filter pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->search($search);
            }
        }

        // Filter status (active/expired/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'inactive') {
                $query->where('status', false);
            }
        }

        // Filter tipe (mbkm, magang_reguler, magang_independen)
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            $query->byTipe($request->tipe);
        }

        // Pagination 15 item per halaman
        $programs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.program-magang.index', compact('programs'));
    }

    /**
     * Show form to create new internship program
     * Fungsi: Tampilkan form tambah program magang
     */
    public function programMagangCreate()
    {
        return view('admin.program-magang.create');
    }

    /**
     * Store new internship program
     * Fungsi: Simpan program magang baru ke database
     * URL: POST /admin/program-magang
     */
    public function programMagangStore(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'persyaratan' => 'required|string',
            'benefit' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'tipe' => 'required|in:mbkm,magang_reguler,magang_independen',
            
            // integer = harus angka bulat
            // min:1 = minimal 1
            'durasi' => 'required|integer|min:1',
            
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'required|date|after:today',
            
            // url = harus format URL valid (http://... atau https://...)
            'link_pendaftaran' => 'required|url',
            
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean',
            'kuota' => 'nullable|integer|min:1',
        ], [
            'judul.required' => 'Judul wajib diisi.',
            'perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'persyaratan.required' => 'Persyaratan wajib diisi.',
            'benefit.required' => 'Benefit wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'tipe.required' => 'Tipe program wajib dipilih.',
            'durasi.required' => 'Durasi wajib diisi.',
            'tanggal_berakhir.required' => 'Tanggal berakhir wajib diisi.',
            'link_pendaftaran.required' => 'Link pendaftaran wajib diisi.',
            'link_pendaftaran.url' => 'Format URL tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Siapkan data
        $data = $request->except('gambar');
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $filename = 'program_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('program_magang', $filename, 'public');
            $data['gambar'] = $path;
        }

        // Simpan ke database
        $program = ProgramMagang::create($data);

        // Log aktivitas
        ActivityLog::log('Created internship program: ' . $program->judul, $program, 'create');

        // Redirect dengan pesan sukses
        return redirect()->route('admin.program-magang.index')
            ->with('success', 'Program magang berhasil ditambahkan.');
    }

    /**
     * Show form to edit internship program
     * Fungsi: Tampilkan form edit program magang
     */
    public function programMagangEdit($id)
    {
        $program = ProgramMagang::findOrFail($id);
        return view('admin.program-magang.edit', compact('program'));
    }

    /**
     * Update internship program
     * Fungsi: Update data program magang
     */
    public function programMagangUpdate(Request $request, $id)
    {
        $program = ProgramMagang::findOrFail($id);

        // Validasi (sama seperti store)
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'persyaratan' => 'required|string',
            'benefit' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'tipe' => 'required|in:mbkm,magang_reguler,magang_independen',
            'durasi' => 'required|integer|min:1',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'required|date',
            'link_pendaftaran' => 'required|url',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean',
            'kuota' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except('gambar');
        $data['updated_by'] = auth()->id();

        // Handle upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($program->gambar && Storage::disk('public')->exists($program->gambar)) {
                Storage::disk('public')->delete($program->gambar);
            }

            // Upload gambar baru
            $image = $request->file('gambar');
            $filename = 'program_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('program_magang', $filename, 'public');
            $data['gambar'] = $path;
        }

        // Update database
        $program->update($data);

        // Log
        ActivityLog::log('Updated internship program: ' . $program->judul, $program, 'update');

        return redirect()->route('admin.program-magang.index')
            ->with('success', 'Program magang berhasil diperbarui.');
    }

    /**
     * Delete internship program
     * Fungsi: Hapus program magang
     */
    public function programMagangDestroy($id)
    {
        $program = ProgramMagang::findOrFail($id);
        $judul = $program->judul;

        $program->delete();

        ActivityLog::log('Deleted internship program: ' . $judul, null, 'delete');

        return back()->with('success', 'Program magang berhasil dihapus.');
    }

    // ========================================================================
    // BERITA/NEWSLETTER MANAGEMENT (KF-13)
    // Bagian ini mengelola semua berita/artikel yang akan ditampilkan di website
    // Fitur: CRUD berita, filter, search, featured news, publikasi/draft
    // ========================================================================

    /**
     * Display list of news
     * Fungsi: Menampilkan daftar semua berita dengan fitur filter dan pencarian
     * URL: GET /admin/berita
     * Return: View dengan daftar berita (pagination)
     */
    public function beritaIndex(Request $request)
    {
        // STEP 1: MULAI QUERY DENGAN EAGER LOADING
        // with() = mengambil data relasi sekaligus (creator & updater)
        // Ini mencegah N+1 query problem (query berulang-ulang)
        // creator = user yang membuat berita
        // updater = user yang terakhir update berita
        $query = Berita::with(['creator', 'updater']);

        // STEP 2: FITUR PENCARIAN (SEARCH)
        // filled() = cek apakah input 'search' ada dan tidak kosong
        if ($request->filled('search')) {
            // trim() = hapus spasi di awal dan akhir text
            // Contoh: "  laravel  " menjadi "laravel"
            $search = trim($request->search);
            
            // Validasi tambahan: cek lagi setelah di-trim
            if (!empty($search)) {
                // search() = scope method di Model Berita
                // Akan mencari di kolom: judul, konten, ringkasan, penulis
                // Menggunakan LIKE query: WHERE judul LIKE '%keyword%'
                $query->search($search);
            }
        }

        // STEP 3: FILTER BERDASARKAN STATUS PUBLIKASI
        // Status ada 3: published (dipublikasikan), draft (belum dipublikasikan), all (semua)
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                // published() = scope method untuk ambil berita yang sudah dipublikasikan
                // WHERE status = true
                $query->published();
            } elseif ($request->status === 'draft') {
                // Draft = berita yang belum dipublikasikan
                // WHERE status = false
                $query->where('status', false);
            }
            // Jika tidak ada kondisi, berarti tampilkan semua (all)
        }

        // STEP 4: FILTER BERDASARKAN KATEGORI
        // Kategori berita: karir, mbkm, magang, umum
        // !== 'all' = pastikan bukan pilihan "Semua Kategori"
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            // byKategori() = scope method untuk filter kategori
            // WHERE kategori = $request->kategori
            $query->byKategori($request->kategori);
        }

        // STEP 5: FILTER BERITA UNGGULAN (FEATURED)
        // is_featured = berita yang ditampilkan di homepage
        // === '1' = pastikan nilainya string '1' (dari checkbox/select)
        if ($request->filled('featured') && $request->featured === '1') {
            // featured() = scope method untuk berita unggulan
            // WHERE is_featured = true
            $query->featured();
        }

        // STEP 6: EXECUTE QUERY & BUAT PAGINATION
        // latest() = urutkan dari yang terbaru (ORDER BY created_at DESC)
        // paginate(15) = bagi hasil menjadi halaman, 15 item per halaman
        // withQueryString() = pertahankan parameter URL (search, filter) saat pindah halaman
        // Contoh: /admin/berita?search=laravel&page=2 (parameter search tetap ada)
        $berita = $query->latest()->paginate(15)->withQueryString();

        // STEP 7: KIRIM DATA KE VIEW
        // compact('berita') = kirim variable $berita ke view
        // View akan ada di: resources/views/admin/berita/index.blade.php
        return view('admin.berita.index', compact('berita'));
    }

    /**
     * Show form to create new news
     * Fungsi: Menampilkan form kosong untuk menambah berita baru
     * URL: GET /admin/berita/create
     * Return: View form create berita
     */
    public function beritaCreate()
    {
        // Hanya menampilkan form kosong, tidak ada logika khusus
        // View akan ada di: resources/views/admin/berita/create.blade.php
        return view('admin.berita.create');
    }

    /**
     * Store new news
     * Fungsi: Menyimpan berita baru ke database dari form
     * URL: POST /admin/berita
     * Method: POST (karena menyimpan data baru)
     * Return: Redirect ke halaman daftar berita dengan pesan sukses
     */
    public function beritaStore(Request $request)
    {
        // STEP 1: VALIDASI INPUT DARI FORM
        // Validator::make() = membuat validator untuk validasi data
        // Parameter 1: data yang akan divalidasi ($request->all())
        // Parameter 2: rules/aturan validasi
        // Parameter 3: custom error messages (pesan error dalam Bahasa Indonesia)
        $validator = Validator::make($request->all(), [
            // ====== VALIDASI FIELD BERITA ======
            
            // JUDUL BERITA
            // required = wajib diisi, tidak boleh kosong
            // string = harus berupa text/string
            // max:255 = maksimal 255 karakter
            'judul' => 'required|string|max:255',
            
            // SLUG (URL-friendly version dari judul)
            // nullable = boleh kosong (akan auto-generate dari judul jika kosong)
            // unique:berita,slug = harus unik di tabel berita kolom slug
            // Slug digunakan untuk URL: /berita/ini-adalah-slug
            'slug' => 'nullable|string|max:255|unique:berita,slug',
            
            // KONTEN/ISI BERITA
            // required = wajib diisi
            // string = berupa text (bisa panjang, untuk konten artikel)
            'konten' => 'required|string',
            
            // RINGKASAN BERITA
            // required = wajib diisi
            // max:500 = maksimal 500 karakter (untuk preview/excerpt)
            'ringkasan' => 'required|string|max:500',
            
            // GAMBAR THUMBNAIL BERITA
            // nullable = boleh kosong (tidak wajib upload gambar)
            // image = harus berupa file gambar
            // mimes:jpeg,png,jpg = hanya boleh format jpeg, png, atau jpg
            // max:2048 = maksimal 2MB (2048 kilobytes)
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // KATEGORI BERITA
            // required = wajib dipilih
            // in:karir,mbkm,magang,umum = hanya boleh salah satu dari 4 pilihan ini
            'kategori' => 'required|in:karir,mbkm,magang,umum',
            
            // NAMA PENULIS
            // required = wajib diisi
            // max:255 = maksimal 255 karakter
            'penulis' => 'required|string|max:255',
            
            // TANGGAL PUBLIKASI
            // required = wajib diisi
            // date = harus format tanggal yang valid (YYYY-MM-DD)
            'tanggal_publikasi' => 'required|date',
            
            // STATUS PUBLIKASI
            // boolean = hanya boleh true (published) atau false (draft)
            // true = 1, false = 0
            'status' => 'boolean',
            
            // BERITA UNGGULAN
            // boolean = true (ditampilkan di featured) atau false (biasa)
            'is_featured' => 'boolean',
        ], [
            // ====== CUSTOM ERROR MESSAGES (BAHASA INDONESIA) ======
            // Format: 'field.rule' => 'pesan error'
            
            'judul.required' => 'Judul wajib diisi.',
            'konten.required' => 'Konten wajib diisi.',
            'ringkasan.required' => 'Ringkasan wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'penulis.required' => 'Penulis wajib diisi.',
            'tanggal_publikasi.required' => 'Tanggal publikasi wajib diisi.',
        ]);

        // STEP 2: CEK HASIL VALIDASI
        // fails() = return true jika ada validasi yang gagal
        if ($validator->fails()) {
            // back() = kembali ke halaman sebelumnya (form create)
            // withErrors() = kirim pesan error ke view untuk ditampilkan
            // withInput() = kirim input lama agar form tidak kosong (user tidak perlu ketik ulang)
            return back()->withErrors($validator)->withInput();
        }

        // STEP 3: SIAPKAN DATA UNTUK DISIMPAN
        // except() = ambil semua input KECUALI yang disebutkan
        // Kita except 'gambar' dan 'slug' karena akan dihandle terpisah
        $data = $request->except(['gambar', 'slug']);
        
        // Tambahkan ID user yang sedang login
        // auth()->id() = ID user yang sedang login (dari session)
        // created_by = user yang membuat berita ini
        // updated_by = user yang terakhir update (awalnya sama dengan created_by)
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // STEP 4: GENERATE SLUG UNIK
        // Slug = URL-friendly version dari judul
        // Contoh judul: "Cara Membuat Website Laravel"
        // Slug: "cara-membuat-website-laravel"
        if ($request->filled('slug')) {
            // Jika user sudah input slug manual, gunakan itu
            // generateUniqueSlug() = static method di Model Berita
            // Akan memastikan slug unik (jika ada duplikat, tambahkan angka)
            $data['slug'] = Berita::generateUniqueSlug($request->slug);
        } else {
            // Jika slug kosong, auto-generate dari judul
            $data['slug'] = Berita::generateUniqueSlug($request->judul);
        }

        // STEP 5: HANDLE IMAGE UPLOAD (UPLOAD GAMBAR)
        // hasFile() = cek apakah ada file gambar yang diupload
        if ($request->hasFile('gambar')) {
            // Ambil file gambar dari request
            $image = $request->file('gambar');
            
            // BUAT NAMA FILE UNIK
            // Format: berita_timestamp_randomstring.extension
            // time() = timestamp UNIX (contoh: 1699123456)
            // Str::random(10) = string random 10 karakter (contoh: aB3xZ9mK1p)
            // getClientOriginalExtension() = ambil ekstensi file asli (.jpg, .png, dll)
            // Hasil contoh: berita_1699123456_aB3xZ9mK1p.jpg
            $filename = 'berita_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // SIMPAN FILE KE STORAGE
            // storeAs() = simpan file dengan nama yang kita tentukan
            // Parameter 1: 'berita' = folder tujuan (storage/app/public/berita)
            // Parameter 2: $filename = nama file yang sudah kita buat
            // Parameter 3: 'public' = disk storage (storage/app/public/)
            // Return: path file (contoh: berita/berita_1699123456_aB3xZ9mK1p.jpg)
            $path = $image->storeAs('berita', $filename, 'public');
            
            // Simpan path ke array $data
            $data['gambar'] = $path;
        }

        // STEP 6: SIMPAN KE DATABASE
        // create() = insert data baru ke tabel berita
        // Return: object model Berita yang baru dibuat
        // Object ini berisi semua data berita + ID yang baru
        $berita = Berita::create($data);

        // STEP 7: LOG ACTIVITY (CATAT AKTIVITAS ADMIN)
        // ActivityLog::log() = static method untuk mencatat aktivitas
        // Parameter 1: deskripsi aktivitas (apa yang dilakukan)
        // Parameter 2: model yang terkait (object Berita yang baru dibuat)
        // Parameter 3: jenis event ('create' = membuat data baru)
        // Log ini berguna untuk audit trail (siapa, kapan, apa yang dilakukan)
        ActivityLog::log('Created news: ' . $berita->judul, $berita, 'create');

        // STEP 8: REDIRECT DENGAN PESAN SUKSES
        // redirect() = pindah ke halaman lain
        // route() = generate URL dari nama route
        // with() = flash message (pesan yang muncul sekali, lalu hilang)
        // Flash message akan muncul di halaman tujuan sebagai notifikasi
        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    /**
     * Show form to edit news
     * Fungsi: Menampilkan form edit dengan data berita yang sudah ada
     * URL: GET /admin/berita/{id}/edit
     * Parameter: $id = ID berita yang akan diedit
     * Return: View form edit dengan data berita
     */
    public function beritaEdit($id)
    {
        // findOrFail() = cari berita berdasarkan ID
        // Jika tidak ditemukan, otomatis return 404 error
        // Return: object model Berita
        $berita = Berita::findOrFail($id);
        
        // compact('berita') = kirim data berita ke view
        // View akan menampilkan form dengan data berita yang sudah diisi
        return view('admin.berita.edit', compact('berita'));
    }

    /**
     * Update news
     * Fungsi: Memperbarui data berita yang sudah ada di database
     * URL: PUT/PATCH /admin/berita/{id}
     * Method: PUT (update data yang sudah ada)
     * Parameter: $id = ID berita yang akan diupdate
     * Return: Redirect ke halaman daftar berita dengan pesan sukses
     */
    public function beritaUpdate(Request $request, $id)
    {
        // STEP 1: CARI DATA BERITA YANG AKAN DIUPDATE
        // findOrFail() = cari berdasarkan ID, jika tidak ada return 404
        $berita = Berita::findOrFail($id);

        // STEP 2: VALIDASI INPUT
        // Sama seperti store, tapi validasi slug berbeda
        // unique:berita,slug,' . $id = unik kecuali untuk berita ini sendiri
        // Contoh: berita dengan ID 5 boleh punya slug yang sama dengan slug lamanya
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:berita,slug,' . $id,
            'konten' => 'required|string',
            'ringkasan' => 'required|string|max:500',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori' => 'required|in:karir,mbkm,magang,umum',
            'penulis' => 'required|string|max:255',
            'tanggal_publikasi' => 'required|date',
            'status' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Cek validasi gagal
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // STEP 3: SIAPKAN DATA
        $data = $request->except(['gambar', 'slug']);
        
        // Update hanya updated_by (siapa yang terakhir update)
        // created_by tidak berubah (tetap user yang membuat pertama kali)
        $data['updated_by'] = auth()->id();

        // STEP 4: UPDATE SLUG JIKA BERUBAH
        // Cek apakah slug diisi manual DAN berbeda dengan slug lama
        if ($request->filled('slug') && $request->slug !== $berita->slug) {
            // Generate slug baru dengan parameter $id (untuk exclude dari pengecekan unik)
            $data['slug'] = Berita::generateUniqueSlug($request->slug, $id);
        } elseif ($request->judul !== $berita->judul && !$request->filled('slug')) {
            // Jika judul berubah tapi slug tidak diisi, auto-generate dari judul baru
            $data['slug'] = Berita::generateUniqueSlug($request->judul, $id);
        }
        // Jika tidak ada perubahan, slug tetap sama (tidak ada di $data)

        // STEP 5: HANDLE IMAGE UPLOAD
        if ($request->hasFile('gambar')) {
            // HAPUS GAMBAR LAMA JIKA ADA
            // $berita->gambar = path gambar lama
            // exists() = cek apakah file ada di storage
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                // delete() = hapus file dari storage
                // Ini penting agar tidak ada file sampah di server
                Storage::disk('public')->delete($berita->gambar);
            }

            // UPLOAD GAMBAR BARU
            // Proses sama seperti create
            $image = $request->file('gambar');
            $filename = 'berita_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('berita', $filename, 'public');
            $data['gambar'] = $path;
        }

        // STEP 6: UPDATE DATABASE
        // update() = update data yang sudah ada
        // Parameter: array data yang akan diupdate
        // Hanya kolom yang ada di $data yang akan diupdate
        $berita->update($data);

        // STEP 7: LOG ACTIVITY
        ActivityLog::log('Updated news: ' . $berita->judul, $berita, 'update');

        // STEP 8: REDIRECT
        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Delete news
     * Fungsi: Menghapus berita dari database
     * URL: DELETE /admin/berita/{id}
     * Method: DELETE (menghapus data)
     * Parameter: $id = ID berita yang akan dihapus
     * Return: Kembali ke halaman sebelumnya dengan pesan sukses
     */
    public function beritaDestroy($id)
    {
        // STEP 1: CARI DATA BERITA
        $berita = Berita::findOrFail($id);
        
        // Simpan judul untuk log (karena setelah delete, data hilang)
        $judul = $berita->judul;

        // STEP 2: HAPUS DATA
        // delete() = soft delete (data tidak benar-benar dihapus)
        // Data hanya di-mark sebagai deleted (deleted_at = timestamp)
        // Masih bisa di-restore jika perlu dengan restore()
        // Untuk hard delete (hapus permanen) gunakan forceDelete()
        $berita->delete();

        // STEP 3: LOG ACTIVITY
        // Parameter 2 = null karena data sudah dihapus
        ActivityLog::log('Deleted news: ' . $judul, null, 'delete');

        // STEP 4: REDIRECT
        // back() = kembali ke halaman sebelumnya
        return back()->with('success', 'Berita berhasil dihapus.');
    }

    // ==================== TENTANG MANAGEMENT (KF-14) ====================
    // ==================== SAMA SAJA KONSEPNYA DENGAN SEBELUMNYA CUKUP LIHAT SYNTAX OKE ====================

    /**
     * Show form to edit about page
     */
    public function tentangEdit()
    {
        $tentang = Tentang::first();
        
        if (!$tentang) {
            // Create default if not exists
            $tentang = Tentang::create([
                'sejarah' => '',
                'visi' => '',
                'misi' => '',
                'tujuan' => '',
                'updated_by' => auth()->id(),
            ]);
        }

        return view('admin.tentang.edit', compact('tentang'));
    }

    /**
     * Update about page
     */
    public function tentangUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sejarah' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'tujuan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'sejarah.required' => 'Sejarah wajib diisi.',
            'visi.required' => 'Visi wajib diisi.',
            'misi.required' => 'Misi wajib diisi.',
            'tujuan.required' => 'Tujuan wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tentang = Tentang::first();
        
        if (!$tentang) {
            $tentang = new Tentang();
        }

        $data = $request->except('gambar');
        $data['updated_by'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($tentang->gambar && Storage::disk('public')->exists($tentang->gambar)) {
                Storage::disk('public')->delete($tentang->gambar);
            }

            $image = $request->file('gambar');
            $filename = 'tentang_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('tentang', $filename, 'public');
            $data['gambar'] = $path;
        }

        if ($tentang->exists) {
            $tentang->update($data);
        } else {
            $tentang->fill($data);
            $tentang->save();
        }

        // Log activity
        ActivityLog::log('Updated about page', $tentang, 'update');

        return back()->with('success', 'Halaman tentang berhasil diperbarui.');
    }

    // ==================== KONTAK MANAGEMENT (KF-15) ====================
    // ==================== SAMA SAJA KONSEPNYA DENGAN SEBELUMNYA CUKUP LIHAT SYNTAX OKE ====================

    /**
     * Show form to edit contact page
     */
    public function kontakEdit()
    {
        $kontak = Kontak::first();
        
        if (!$kontak) {
            // Create default if not exists
            $kontak = Kontak::create([
                'alamat' => '',
                'telepon' => '',
                'email' => '',
                'jam_operasional' => '',
                'updated_by' => auth()->id(),
            ]);
        }

        return view('admin.kontak.edit', compact('kontak'));
    }

    /**
     * Update contact page
     */
    public function kontakUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email',
            'whatsapp' => 'nullable|string|max:20',
            'google_maps_url' => 'nullable|url',
            'google_maps_embed' => 'nullable|string',
            'jam_operasional' => 'required|string',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'twitter' => 'nullable|url',
        ], [
            'alamat.required' => 'Alamat wajib diisi.',
            'telepon.required' => 'Telepon wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'jam_operasional.required' => 'Jam operasional wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $kontak = Kontak::first();
        
        if (!$kontak) {
            $kontak = new Kontak();
        }

        $data = $request->all();
        $data['updated_by'] = auth()->id();

        if ($kontak->exists) {
            $kontak->update($data);
        } else {
            $kontak->fill($data);
            $kontak->save();
        }

        // Log activity
        ActivityLog::log('Updated contact page', $kontak, 'update');

        return back()->with('success', 'Halaman kontak berhasil diperbarui.');
    }

    // ========================================================================
    // NEWSLETTER SUBSCRIBERS MANAGEMENT
    // Bagian ini untuk mengelola daftar subscriber newsletter
    // Fitur: lihat daftar subscriber, filter, search, hapus, bulk delete
    // ========================================================================

    /**
     * Display list of newsletter subscribers
     * Fungsi: Menampilkan daftar semua subscriber newsletter dengan filter
     * URL: GET /admin/newsletter
     * Return: View daftar subscriber dengan statistik
     */
    public function newsletterIndex(Request $request)
    {
        // STEP 1: MULAI QUERY
        // query() = mulai query builder tanpa kondisi apapun
        $query = LanggananNewsletter::query();

        // STEP 2: FITUR PENCARIAN (SEARCH)
        // Cari berdasarkan email atau nama subscriber
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                // where(function()) = grouping kondisi OR
                // Gunakan closure untuk mengelompokkan kondisi OR
                // Tanpa ini, query bisa berantakan jika ada kondisi lain
                $query->where(function($q) use ($search) {
                    // Cari di kolom email
                    // LIKE = pencarian partial match
                    // "%{$search}%" = cocok jika mengandung kata tersebut
                    $q->where('email', 'like', "%{$search}%")
                      // orWhere = ATAU cari di kolom nama
                      ->orWhere('nama', 'like', "%{$search}%");
                });
                // Contoh: search "john" akan match:
                // - Email: john@email.com, john.doe@test.com
                // - Nama: John Doe, Johnny
            }
        }

        // STEP 3: FILTER BERDASARKAN STATUS
        // Status subscriber ada 3:
        // - active: sudah verified dan masih berlangganan
        // - unverified: belum verify email
        // - unsubscribed: sudah unsubscribe
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                // active() = scope method untuk subscriber aktif
                // WHERE status = true AND verified_at IS NOT NULL
                $query->active();
            } elseif ($request->status === 'unverified') {
                // unverified() = scope method untuk subscriber belum verify
                // WHERE verified_at IS NULL
                $query->unverified();
            } elseif ($request->status === 'unsubscribed') {
                // unsubscribed() = scope method untuk yang sudah unsubscribe
                // WHERE status = false
                $query->unsubscribed();
            }
        }

        // STEP 4: EXECUTE QUERY & PAGINATION
        // latest() = urutkan dari yang terbaru subscribe
        // paginate(20) = 20 subscriber per halaman (lebih banyak dari yang lain)
        // withQueryString() = pertahankan parameter URL
        $subscribers = $query->latest()->paginate(20)->withQueryString();

        // STEP 5: HITUNG STATISTIK SUBSCRIBER
        // Array berisi jumlah subscriber per kategori
        $stats = [
            // Total semua subscriber (aktif + unverified + unsubscribed)
            'total' => LanggananNewsletter::count(),
            
            // Subscriber yang aktif dan sudah verify
            'active' => LanggananNewsletter::active()->count(),
            
            // Subscriber yang belum verify email
            'unverified' => LanggananNewsletter::unverified()->count(),
            
            // Subscriber yang sudah unsubscribe
            'unsubscribed' => LanggananNewsletter::unsubscribed()->count(),
        ];

        // STEP 6: KIRIM DATA KE VIEW
        // compact() = kirim 2 variable: $subscribers dan $stats
        return view('admin.newsletter.index', compact('subscribers', 'stats'));
    }

    /**
     * Delete newsletter subscriber
     * Fungsi: Menghapus satu subscriber dari database
     * URL: DELETE /admin/newsletter/{id}
     * Parameter: $id = ID subscriber yang akan dihapus
     * Return: Kembali ke halaman sebelumnya dengan pesan sukses
     */
    public function newsletterDestroy($id)
    {
        // STEP 1: CARI SUBSCRIBER
        $subscriber = LanggananNewsletter::findOrFail($id);
        
        // Simpan email untuk log (karena setelah delete, data hilang)
        $email = $subscriber->email;

        // STEP 2: HAPUS DATA
        // delete() = soft delete (jika ada SoftDeletes trait)
        // atau permanent delete (jika tidak ada SoftDeletes)
        $subscriber->delete();

        // STEP 3: LOG ACTIVITY
        // Catat siapa yang menghapus subscriber ini
        ActivityLog::log('Deleted newsletter subscriber: ' . $email, null, 'delete');

        // STEP 4: REDIRECT DENGAN PESAN SUKSES
        // back() = kembali ke halaman daftar subscriber
        return back()->with('success', 'Subscriber berhasil dihapus.');
    }

    /**
     * Bulk delete newsletter subscribers
     * Fungsi: Menghapus banyak subscriber sekaligus
     * URL: POST /admin/newsletter/bulk-delete
     * Method: POST (karena mengirim array IDs)
     * Return: Kembali dengan pesan jumlah subscriber yang dihapus
     */
    public function newsletterBulkDelete(Request $request)
    {
        // STEP 1: VALIDASI INPUT
        // Pastikan ada IDs yang dikirim dan valid
        $validator = Validator::make($request->all(), [
            // IDS
            // required = wajib ada
            // array = harus berupa array (list of IDs)
            'ids' => 'required|array',
            
            // IDS.* (setiap item dalam array)
            // exists:langganan_newsletter,id = setiap ID harus ada di tabel
            // Mencegah hapus ID yang tidak ada
            'ids.*' => 'exists:langganan_newsletter,id',
        ]);

        // Cek validasi gagal
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // STEP 2: HITUNG JUMLAH YANG AKAN DIHAPUS
        // whereIn() = WHERE id IN (1, 2, 3, 4, 5)
        // count() = hitung jumlahnya
        $count = LanggananNewsletter::whereIn('id', $request->ids)->count();
        
        // STEP 3: HAPUS DATA
        // whereIn() = cari semua record dengan ID yang ada di array
        // delete() = hapus semua record yang ditemukan
        LanggananNewsletter::whereIn('id', $request->ids)->delete();

        // STEP 4: LOG ACTIVITY
        // Catat berapa banyak subscriber yang dihapus
        ActivityLog::log('Bulk deleted ' . $count . ' newsletter subscribers', null, 'delete');

        // STEP 5: REDIRECT DENGAN PESAN
        // Tampilkan jumlah subscriber yang berhasil dihapus
        return back()->with('success', $count . ' subscriber berhasil dihapus.');
    }

    // ========================================================================
    // ACTIVITY LOGS
    // Bagian ini untuk melihat log aktivitas admin
    // Setiap aksi admin (create, update, delete) dicatat di sini
    // Berguna untuk audit trail dan tracking perubahan
    // ========================================================================

    /**
     * Display activity logs
     * Fungsi: Menampilkan log aktivitas admin dengan berbagai filter
     * URL: GET /admin/activity-logs
     * Return: View daftar log dengan pagination
     */
    public function activityLogs(Request $request)
    {
        // STEP 1: MULAI QUERY DENGAN RELASI
        // with('user') = eager loading relasi user
        // Agar bisa tampilkan nama user yang melakukan aktivitas
        $query = ActivityLog::with('user');

        // STEP 2: FILTER BERDASARKAN USER
        // Filter untuk melihat aktivitas dari user tertentu saja
        if ($request->filled('user_id')) {
            // byUser() = scope method untuk filter berdasarkan user
            // WHERE user_id = $request->user_id
            $query->byUser($request->user_id);
        }

        // STEP 3: FILTER BERDASARKAN EVENT (JENIS AKTIVITAS)
        // Event: create, update, delete
        // !== 'all' = pastikan bukan pilihan "Semua Event"
        if ($request->filled('event') && $request->event !== 'all') {
            // byEvent() = scope method untuk filter berdasarkan event
            // WHERE event = $request->event
            $query->byEvent($request->event);
        }

        // STEP 4: FILTER BERDASARKAN TANGGAL (DATE RANGE)
        // Filter untuk melihat log dalam rentang tanggal tertentu
        
        // Tanggal mulai (FROM)
        if ($request->filled('date_from')) {
            // whereDate() = hanya compare tanggalnya (ignore waktu)
            // >= = greater than or equal (dari tanggal ini atau sesudahnya)
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Tanggal akhir (TO)
        if ($request->filled('date_to')) {
            // <= = less than or equal (sampai tanggal ini atau sebelumnya)
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // STEP 5: PENCARIAN (SEARCH)
        // Cari berdasarkan deskripsi aktivitas
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                // Cari di kolom description
                // Contoh: cari "lowongan kerja" untuk melihat log terkait lowongan
                $query->where('description', 'like', "%{$search}%");
            }
        }

        // STEP 6: EXECUTE QUERY & PAGINATION
        // latest() = urutkan dari yang terbaru
        // paginate(30) = 30 log per halaman
        $logs = $query->latest()->paginate(30)->withQueryString();

        // STEP 7: AMBIL DAFTAR USER UNTUK FILTER DROPDOWN
        // \App\Models\User:: = full namespace (karena tidak di-import di atas)
        // admins() = scope method untuk ambil user dengan role admin
        // get() = ambil semua user admin
        $users = \App\Models\User::admins()->get();

        // STEP 8: KIRIM DATA KE VIEW
        return view('admin.activity-logs.index', compact('logs', 'users'));
    }

    /**
     * Clear old activity logs
     * Fungsi: Menghapus log aktivitas yang sudah lama (pembersihan database)
     * URL: POST /admin/activity-logs/clear
     * Method: POST (karena mengubah/menghapus data)
     * Return: Kembali dengan pesan jumlah log yang dihapus
     */
    public function activityLogsClear(Request $request)
    {
        // STEP 1: VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            // DAYS = berapa hari ke belakang yang akan dihapus
            // required = wajib diisi
            // integer = harus angka bulat
            // min:1 = minimal 1 hari
            // max:365 = maksimal 365 hari (1 tahun)
            'days' => 'required|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // STEP 2: HITUNG TANGGAL BATAS
        // now() = timestamp sekarang
        // subDays() = kurangi dengan jumlah hari yang diinput
        // Contoh: sekarang 2024-01-20, input 30 hari
        // Result: 2023-12-21 (log sebelum tanggal ini akan dihapus)
        $date = now()->subDays($request->days);
        
        // STEP 3: HITUNG JUMLAH LOG YANG AKAN DIHAPUS
        // where('created_at', '<', $date) = log yang lebih lama dari tanggal batas
        // count() = hitung jumlahnya
        $count = ActivityLog::where('created_at', '<', $date)->count();
        
        // STEP 4: HAPUS LOG
        // delete() = hapus semua log yang lebih lama dari tanggal batas
        ActivityLog::where('created_at', '<', $date)->delete();

        // STEP 5: LOG ACTIVITY PEMBERSIHAN
        // Catat aktivitas pembersihan log ini
        // Termasuk berapa hari dan berapa record yang dihapus
        ActivityLog::log('Cleared activity logs older than ' . $request->days . ' days (' . $count . ' records)', null, 'delete');

        // STEP 6: REDIRECT DENGAN PESAN
        return back()->with('success', $count . ' log aktivitas berhasil dihapus.');
    }

    // ========================================================================
    // BULK ACTIONS (AKSI MASSAL)
    // Bagian ini untuk melakukan aksi ke banyak data sekaligus
    // Fitur: bulk status update, bulk delete
    // Tujuan: efisiensi - tidak perlu satu-satu
    // ========================================================================

    // ========================================================================
    // LOWONGAN KERJA BULK ACTIONS
    // ========================================================================

    /**
     * Bulk update status for job opportunities
     * Fungsi: Aktifkan/nonaktifkan banyak lowongan sekaligus
     * URL: POST /admin/lowongan-kerja/bulk-status
     * Return: Kembali dengan pesan jumlah lowongan yang diupdate
     */
    public function lowonganKerjaBulkStatus(Request $request)
    {
        // STEP 1: VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            // IDS = array ID lowongan yang akan diupdate
            'ids' => 'required|array',
            'ids.*' => 'exists:lowongan_kerja,id',
            
            // STATUS = true (aktifkan) atau false (nonaktifkan)
            // boolean = hanya boleh true/false (1/0)
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // STEP 2: UPDATE STATUS BANYAK LOWONGAN SEKALIGUS
        // whereIn() = WHERE id IN (1, 2, 3, 4, 5)
        // update() = update kolom yang disebutkan
        // Return: jumlah row yang diupdate
        $count = LowonganKerja::whereIn('id', $request->ids)->update([
            'status' => $request->status,        // Status baru (true/false)
            'updated_by' => auth()->id(),        // User yang melakukan update
        ]);

        // STEP 3: SIAPKAN TEXT UNTUK PESAN
        // Ternary operator: kondisi ? jika_true : jika_false
        // Jika status true = "diaktifkan", jika false = "dinonaktifkan"
        $statusText = $request->status ? 'diaktifkan' : 'dinonaktifkan';

        // STEP 4: LOG ACTIVITY
        ActivityLog::log('Bulk updated status for ' . $count . ' job opportunities to ' . $statusText, null, 'update');

        // STEP 5: REDIRECT DENGAN PESAN
        // Pesan: "5 lowongan kerja berhasil diaktifkan."
        return back()->with('success', $count . ' lowongan kerja berhasil ' . $statusText . '.');
    }

    /**
     * Bulk delete job opportunities
     * Fungsi: Hapus banyak lowongan sekaligus
     * URL: POST /admin/lowongan-kerja/bulk-delete
     * Return: Kembali dengan pesan jumlah lowongan yang dihapus
     */
    public function lowonganKerjaBulkDelete(Request $request)
    {
        // STEP 1: VALIDASI
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:lowongan_kerja,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // STEP 2: HITUNG & HAPUS
        $count = LowonganKerja::whereIn('id', $request->ids)->count();
        LowonganKerja::whereIn('id', $request->ids)->delete();

        // STEP 3: LOG & REDIRECT
        ActivityLog::log('Bulk deleted ' . $count . ' job opportunities', null, 'delete');
        return back()->with('success', $count . ' lowongan kerja berhasil dihapus.');
    }

    // ========================================================================
    // PROGRAM MAGANG BULK ACTIONS
    // ========================================================================

    /**
     * Bulk update status for internship programs
     * Fungsi: Aktifkan/nonaktifkan banyak program sekaligus
     * Logika sama persis dengan lowonganKerjaBulkStatus
     */
    public function programMagangBulkStatus(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:program_magang,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Update status banyak program sekaligus
        $count = ProgramMagang::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        // Text untuk pesan
        $statusText = $request->status ? 'diaktifkan' : 'dinonaktifkan';

        // Log & redirect
        ActivityLog::log('Bulk updated status for ' . $count . ' internship programs to ' . $statusText, null, 'update');
        return back()->with('success', $count . ' program magang berhasil ' . $statusText . '.');
    }

    /**
     * Bulk delete internship programs
     * Fungsi: Hapus banyak program sekaligus
     */
    public function programMagangBulkDelete(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:program_magang,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Hitung & hapus
        $count = ProgramMagang::whereIn('id', $request->ids)->count();
        ProgramMagang::whereIn('id', $request->ids)->delete();

        // Log & redirect
        ActivityLog::log('Bulk deleted ' . $count . ' internship programs', null, 'delete');
        return back()->with('success', $count . ' program magang berhasil dihapus.');
    }

    // ========================================================================
    // BERITA BULK ACTIONS
    // ========================================================================

    /**
     * Bulk update status for news
     * Fungsi: Publish/draft banyak berita sekaligus
     */
    public function beritaBulkStatus(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:berita,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Update status
        $count = Berita::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        // Text untuk pesan
        // Untuk berita: true = "dipublikasikan", false = "dijadikan draft"
        $statusText = $request->status ? 'dipublikasikan' : 'dijadikan draft';

        // Log & redirect
        ActivityLog::log('Bulk updated status for ' . $count . ' news to ' . $statusText, null, 'update');
        return back()->with('success', $count . ' berita berhasil ' . $statusText . '.');
    }

    /**
     * Bulk delete news
     * Fungsi: Hapus banyak berita sekaligus
     */
    public function beritaBulkDelete(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:berita,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Hitung & hapus
        $count = Berita::whereIn('id', $request->ids)->count();
        Berita::whereIn('id', $request->ids)->delete();

        // Log & redirect
        ActivityLog::log('Bulk deleted ' . $count . ' news', null, 'delete');
        return back()->with('success', $count . ' berita berhasil dihapus.');
    }

    /**
     * Bulk toggle featured status for news
     * Fungsi: Set/unset featured banyak berita sekaligus
     * Featured = berita unggulan yang ditampilkan di homepage
     */
    public function beritaBulkFeatured(Request $request)
    {
        // STEP 1: VALIDASI
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:berita,id',
            
            // IS_FEATURED = true (jadikan featured) atau false (hilangkan dari featured)
            'is_featured' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // STEP 2: UPDATE IS_FEATURED
        // Update kolom is_featured untuk banyak berita sekaligus
        $count = Berita::whereIn('id', $request->ids)->update([
            'is_featured' => $request->is_featured,
            'updated_by' => auth()->id(),
        ]);

        // STEP 3: TEXT UNTUK PESAN
        // true = "ditampilkan", false = "dihilangkan dari tampilan"
        $featuredText = $request->is_featured ? 'ditampilkan' : 'dihilangkan dari tampilan';

        // STEP 4: LOG & REDIRECT
        ActivityLog::log('Bulk updated featured status for ' . $count . ' news', null, 'update');
        
        // Pesan: "5 berita berhasil ditampilkan unggulan."
        // atau: "3 berita berhasil dihilangkan dari tampilan unggulan."
        return back()->with('success', $count . ' berita berhasil ' . $featuredText . ' unggulan.');
    }

// ============================================================================
// END OF AdminController
// ============================================================================
// TOTAL METHODS DALAM AdminController:
// 
// Dashboard: 1 method
// Lowongan Kerja: 7 methods (index, create, store, edit, update, destroy, bulk actions)
// Program Magang: 7 methods (sama seperti lowongan)
// Berita: 8 methods (+ bulk featured)
// Tentang: 2 methods (edit, update)
// Kontak: 2 methods (edit, update)
// Newsletter: 3 methods (index, destroy, bulk delete)
// Activity Logs: 2 methods (index, clear)
// Bulk Actions: 8 methods total
//
// TOTAL: 40 methods dalam 1 controller
// ============================================================================
}