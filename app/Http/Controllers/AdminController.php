<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\LowonganKerja;
use App\Models\ProgramMagang;
use App\Models\Berita;
use App\Models\Tentang;
use App\Models\Kontak;
use App\Models\LanggananNewsletter;
use App\Models\ActivityLog;

class AdminController extends Controller
{
    /**
     * Display admin dashboard (KF-10)
     */
    public function dashboard()
    {
        // Statistics
        $stats = [
            'total_jobs' => LowonganKerja::count(),
            'active_jobs' => LowonganKerja::active()->count(),
            'expired_jobs' => LowonganKerja::expired()->count(),
            'total_programs' => ProgramMagang::count(),
            'active_programs' => ProgramMagang::active()->count(),
            'expired_programs' => ProgramMagang::expired()->count(),
            'total_news' => Berita::count(),
            'published_news' => Berita::published()->count(),
            'total_subscribers' => LanggananNewsletter::active()->count(),
        ];

        // Recent activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Popular content
        $popularJobs = LowonganKerja::active()->popular(5)->get();
        $popularPrograms = ProgramMagang::active()->popular(5)->get();
        $popularNews = Berita::published()->popular(5)->get();

        // Recent content
        $recentJobs = LowonganKerja::latest()->limit(5)->get();
        $recentPrograms = ProgramMagang::latest()->limit(5)->get();
        $recentNews = Berita::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'popularJobs',
            'popularPrograms',
            'popularNews',
            'recentJobs',
            'recentPrograms',
            'recentNews'
        ));
    }

    // ==================== LOWONGAN KERJA MANAGEMENT (KF-11) ====================

    /**
     * Display list of job opportunities
     */
    public function lowonganKerjaIndex(Request $request)
    {
        $query = LowonganKerja::with(['creator', 'updater']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->search($search);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'inactive') {
                $query->where('status', false);
            }
        }

        // Filter by type
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            $query->byTipe($request->tipe);
        }

        // Filter by category
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->byKategori($request->kategori);
        }

        $lowongan = $query->latest()->paginate(15)->withQueryString();

        return view('admin.lowongan-kerja.index', compact('lowongan'));
    }

    /**
     * Show form to create new job opportunity
     */
    public function lowonganKerjaCreate()
    {
        return view('admin.lowongan-kerja.create');
    }

    /**
     * Store new job opportunity
     */
    public function lowonganKerjaStore(Request $request)
    {
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
            'tanggal_berakhir' => 'required|date|after:today',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'boolean',
        ], [
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

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except('gambar');
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $filename = 'lowongan_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('lowongan_kerja', $filename, 'public');
            $data['gambar'] = $path;
        }

        $lowongan = LowonganKerja::create($data);

        // Log activity
        ActivityLog::log('Created job opportunity: ' . $lowongan->judul, $lowongan, 'create');

        return redirect()->route('admin.lowongan-kerja.index')
            ->with('success', 'Lowongan kerja berhasil ditambahkan.');
    }

    /**
     * Show form to edit job opportunity
     */
    public function lowonganKerjaEdit($id)
    {
        $lowongan = LowonganKerja::findOrFail($id);
        return view('admin.lowongan-kerja.edit', compact('lowongan'));
    }

    /**
     * Update job opportunity
     */
    public function lowonganKerjaUpdate(Request $request, $id)
    {
        $lowongan = LowonganKerja::findOrFail($id);

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

        $data = $request->except('gambar');
        $data['updated_by'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($lowongan->gambar && Storage::disk('public')->exists($lowongan->gambar)) {
                Storage::disk('public')->delete($lowongan->gambar);
            }

            $image = $request->file('gambar');
            $filename = 'lowongan_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('lowongan_kerja', $filename, 'public');
            $data['gambar'] = $path;
        }

        $lowongan->update($data);

        // Log activity
        ActivityLog::log('Updated job opportunity: ' . $lowongan->judul, $lowongan, 'update');

        return redirect()->route('admin.lowongan-kerja.index')
            ->with('success', 'Lowongan kerja berhasil diperbarui.');
    }

    /**
     * Delete job opportunity
     */
    public function lowonganKerjaDestroy($id)
    {
        $lowongan = LowonganKerja::findOrFail($id);
        $judul = $lowongan->judul;

        $lowongan->delete();

        // Log activity
        ActivityLog::log('Deleted job opportunity: ' . $judul, null, 'delete');

        return back()->with('success', 'Lowongan kerja berhasil dihapus.');
    }

    // ==================== PROGRAM MAGANG MANAGEMENT (KF-12) ====================

    /**
     * Display list of internship programs
     */
    public function programMagangIndex(Request $request)
    {
        $query = ProgramMagang::with(['creator', 'updater']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->search($search);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'inactive') {
                $query->where('status', false);
            }
        }

        // Filter by type
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            $query->byTipe($request->tipe);
        }

        $programs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.program-magang.index', compact('programs'));
    }

    /**
     * Show form to create new internship program
     */
    public function programMagangCreate()
    {
        return view('admin.program-magang.create');
    }

    /**
     * Store new internship program
     */
    public function programMagangStore(Request $request)
    {
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
            'tanggal_berakhir' => 'required|date|after:today',
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

        $data = $request->except('gambar');
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $filename = 'program_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('program_magang', $filename, 'public');
            $data['gambar'] = $path;
        }

        $program = ProgramMagang::create($data);

        // Log activity
        ActivityLog::log('Created internship program: ' . $program->judul, $program, 'create');

        return redirect()->route('admin.program-magang.index')
            ->with('success', 'Program magang berhasil ditambahkan.');
    }

    /**
     * Show form to edit internship program
     */
    public function programMagangEdit($id)
    {
        $program = ProgramMagang::findOrFail($id);
        return view('admin.program-magang.edit', compact('program'));
    }

    /**
     * Update internship program
     */
    public function programMagangUpdate(Request $request, $id)
    {
        $program = ProgramMagang::findOrFail($id);

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

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($program->gambar && Storage::disk('public')->exists($program->gambar)) {
                Storage::disk('public')->delete($program->gambar);
            }

            $image = $request->file('gambar');
            $filename = 'program_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('program_magang', $filename, 'public');
            $data['gambar'] = $path;
        }

        $program->update($data);

        // Log activity
        ActivityLog::log('Updated internship program: ' . $program->judul, $program, 'update');

        return redirect()->route('admin.program-magang.index')
            ->with('success', 'Program magang berhasil diperbarui.');
    }

    /**
     * Delete internship program
     */
    public function programMagangDestroy($id)
    {
        $program = ProgramMagang::findOrFail($id);
        $judul = $program->judul;

        $program->delete();

        // Log activity
        ActivityLog::log('Deleted internship program: ' . $judul, null, 'delete');

        return back()->with('success', 'Program magang berhasil dihapus.');
    }

    // ==================== BERITA/NEWSLETTER MANAGEMENT (KF-13) ====================

    /**
     * Display list of news
     */
    public function beritaIndex(Request $request)
    {
        $query = Berita::with(['creator', 'updater']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->search($search);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('status', false);
            }
        }

        // Filter by category
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->byKategori($request->kategori);
        }

        // Filter by featured
        if ($request->filled('featured') && $request->featured === '1') {
            $query->featured();
        }

        $berita = $query->latest()->paginate(15)->withQueryString();

        return view('admin.berita.index', compact('berita'));
    }

    /**
     * Show form to create new news
     */
    public function beritaCreate()
    {
        return view('admin.berita.create');
    }

    /**
     * Store new news
     */
    public function beritaStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:berita,slug',
            'konten' => 'required|string',
            'ringkasan' => 'required|string|max:500',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori' => 'required|in:karir,mbkm,magang,umum',
            'penulis' => 'required|string|max:255',
            'tanggal_publikasi' => 'required|date',
            'status' => 'boolean',
            'is_featured' => 'boolean',
        ], [
            'judul.required' => 'Judul wajib diisi.',
            'konten.required' => 'Konten wajib diisi.',
            'ringkasan.required' => 'Ringkasan wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'penulis.required' => 'Penulis wajib diisi.',
            'tanggal_publikasi.required' => 'Tanggal publikasi wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['gambar', 'slug']);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // Generate unique slug
        if ($request->filled('slug')) {
            $data['slug'] = Berita::generateUniqueSlug($request->slug);
        } else {
            $data['slug'] = Berita::generateUniqueSlug($request->judul);
        }

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $filename = 'berita_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('berita', $filename, 'public');
            $data['gambar'] = $path;
        }

        $berita = Berita::create($data);

        // Log activity
        ActivityLog::log('Created news: ' . $berita->judul, $berita, 'create');

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    /**
     * Show form to edit news
     */
    public function beritaEdit($id)
    {
        $berita = Berita::findOrFail($id);
        return view('admin.berita.edit', compact('berita'));
    }

    /**
     * Update news
     */
    public function beritaUpdate(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

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

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['gambar', 'slug']);
        $data['updated_by'] = auth()->id();

        // Update slug if changed
        if ($request->filled('slug') && $request->slug !== $berita->slug) {
            $data['slug'] = Berita::generateUniqueSlug($request->slug, $id);
        } elseif ($request->judul !== $berita->judul && !$request->filled('slug')) {
            $data['slug'] = Berita::generateUniqueSlug($request->judul, $id);
        }

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }

            $image = $request->file('gambar');
            $filename = 'berita_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('berita', $filename, 'public');
            $data['gambar'] = $path;
        }

        $berita->update($data);

        // Log activity
        ActivityLog::log('Updated news: ' . $berita->judul, $berita, 'update');

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Delete news
     */
    public function beritaDestroy($id)
    {
        $berita = Berita::findOrFail($id);
        $judul = $berita->judul;

        $berita->delete();

        // Log activity
        ActivityLog::log('Deleted news: ' . $judul, null, 'delete');

        return back()->with('success', 'Berita berhasil dihapus.');
    }

    // ==================== TENTANG MANAGEMENT (KF-14) ====================

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

    // ==================== NEWSLETTER SUBSCRIBERS ====================

    /**
     * Display list of newsletter subscribers
     */
    public function newsletterIndex(Request $request)
    {
        $query = LanggananNewsletter::query();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                });
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'unverified') {
                $query->unverified();
            } elseif ($request->status === 'unsubscribed') {
                $query->unsubscribed();
            }
        }

        $subscribers = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => LanggananNewsletter::count(),
            'active' => LanggananNewsletter::active()->count(),
            'unverified' => LanggananNewsletter::unverified()->count(),
            'unsubscribed' => LanggananNewsletter::unsubscribed()->count(),
        ];

        return view('admin.newsletter.index', compact('subscribers', 'stats'));
    }

    /**
     * Delete newsletter subscriber
     */
    public function newsletterDestroy($id)
    {
        $subscriber = LanggananNewsletter::findOrFail($id);
        $email = $subscriber->email;

        $subscriber->delete();

        // Log activity
        ActivityLog::log('Deleted newsletter subscriber: ' . $email, null, 'delete');

        return back()->with('success', 'Subscriber berhasil dihapus.');
    }

    /**
     * Bulk delete newsletter subscribers
     */
    public function newsletterBulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:langganan_newsletter,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = LanggananNewsletter::whereIn('id', $request->ids)->count();
        LanggananNewsletter::whereIn('id', $request->ids)->delete();

        // Log activity
        ActivityLog::log('Bulk deleted ' . $count . ' newsletter subscribers', null, 'delete');

        return back()->with('success', $count . ' subscriber berhasil dihapus.');
    }

    // ==================== ACTIVITY LOGS ====================

    /**
     * Display activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by event
        if ($request->filled('event') && $request->event !== 'all') {
            $query->byEvent($request->event);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->where('description', 'like', "%{$search}%");
            }
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        // Get all users for filter
        $users = \App\Models\User::admins()->get();

        return view('admin.activity-logs.index', compact('logs', 'users'));
    }

    /**
     * Clear old activity logs
     */
    public function activityLogsClear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'required|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $date = now()->subDays($request->days);
        $count = ActivityLog::where('created_at', '<', $date)->count();
        ActivityLog::where('created_at', '<', $date)->delete();

        // Log activity
        ActivityLog::log('Cleared activity logs older than ' . $request->days . ' days (' . $count . ' records)', null, 'delete');

        return back()->with('success', $count . ' log aktivitas berhasil dihapus.');
    }

    // ==================== BULK ACTIONS ====================

    /**
     * Bulk update status for job opportunities
     */
    public function lowonganKerjaBulkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:lowongan_kerja,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = LowonganKerja::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        $statusText = $request->status ? 'diaktifkan' : 'dinonaktifkan';

        // Log activity
        ActivityLog::log('Bulk updated status for ' . $count . ' job opportunities to ' . $statusText, null, 'update');

        return back()->with('success', $count . ' lowongan kerja berhasil ' . $statusText . '.');
    }

    /**
     * Bulk delete job opportunities
     */
    public function lowonganKerjaBulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:lowongan_kerja,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = LowonganKerja::whereIn('id', $request->ids)->count();
        LowonganKerja::whereIn('id', $request->ids)->delete();

        // Log activity
        ActivityLog::log('Bulk deleted ' . $count . ' job opportunities', null, 'delete');

        return back()->with('success', $count . ' lowongan kerja berhasil dihapus.');
    }

    /**
     * Bulk update status for internship programs
     */
    public function programMagangBulkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:program_magang,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = ProgramMagang::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        $statusText = $request->status ? 'diaktifkan' : 'dinonaktifkan';

        // Log activity
        ActivityLog::log('Bulk updated status for ' . $count . ' internship programs to ' . $statusText, null, 'update');

        return back()->with('success', $count . ' program magang berhasil ' . $statusText . '.');
    }

    /**
     * Bulk delete internship programs
     */
    public function programMagangBulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:program_magang,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = ProgramMagang::whereIn('id', $request->ids)->count();
        ProgramMagang::whereIn('id', $request->ids)->delete();

        // Log activity
        ActivityLog::log('Bulk deleted ' . $count . ' internship programs', null, 'delete');

        return back()->with('success', $count . ' program magang berhasil dihapus.');
    }

    /**
     * Bulk update status for news
     */
    public function beritaBulkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:berita,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = Berita::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        $statusText = $request->status ? 'dipublikasikan' : 'dijadikan draft';

        // Log activity
        ActivityLog::log('Bulk updated status for ' . $count . ' news to ' . $statusText, null, 'update');

        return back()->with('success', $count . ' berita berhasil ' . $statusText . '.');
    }

    /**
     * Bulk delete news
     */
    public function beritaBulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:berita,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = Berita::whereIn('id', $request->ids)->count();
        Berita::whereIn('id', $request->ids)->delete();

        // Log activity
        ActivityLog::log('Bulk deleted ' . $count . ' news', null, 'delete');

        return back()->with('success', $count . ' berita berhasil dihapus.');
    }

    /**
     * Bulk toggle featured status for news
     */
    public function beritaBulkFeatured(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:berita,id',
            'is_featured' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $count = Berita::whereIn('id', $request->ids)->update([
            'is_featured' => $request->is_featured,
            'updated_by' => auth()->id(),
        ]);

        $featuredText = $request->is_featured ? 'ditampilkan' : 'dihilangkan dari tampilan';

        // Log activity
        ActivityLog::log('Bulk updated featured status for ' . $count . ' news', null, 'update');

        return back()->with('success', $count . ' berita berhasil ' . $featuredText . ' unggulan.');
    }
}