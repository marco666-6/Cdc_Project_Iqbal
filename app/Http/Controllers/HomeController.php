<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LowonganKerja;
use App\Models\ProgramMagang;
use App\Models\Berita;
use App\Models\Tentang;
use App\Models\Kontak;
use App\Models\LanggananNewsletter;

class HomeController extends Controller
{
    /**
     * Display homepage (KF-01)
     */
    public function index()
    {
        // Get featured content for homepage
        $featuredJobs = LowonganKerja::active()
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $featuredPrograms = ProgramMagang::active()
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $featuredNews = Berita::published()
            ->featured()
            ->latest()
            ->limit(3)
            ->get();

        // Statistics
        $stats = [
            'total_jobs' => LowonganKerja::active()->count(),
            'total_programs' => ProgramMagang::active()->count(),
            'total_news' => Berita::published()->count(),
            'total_companies' => LowonganKerja::active()
                ->distinct('perusahaan')
                ->count('perusahaan'),
        ];

        return view('home.index', compact(
            'featuredJobs',
            'featuredPrograms',
            'featuredNews',
            'stats'
        ));
    }

    /**
     * Display career opportunities page (KF-02)
     */
    public function lowonganKerja(Request $request)
    {
        $query = LowonganKerja::active();

        // Search functionality
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->search($search);
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

        // Filter by location
        if ($request->filled('lokasi')) {
            $lokasi = trim($request->lokasi);
            if (!empty($lokasi)) {
                $query->byLokasi($lokasi);
            }
        }

        // Sorting
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

        $lowongan = $query->paginate(12)->withQueryString();

        // Get filter options
        $tipeOptions = LowonganKerja::getTipeOptions();
        $kategoriOptions = LowonganKerja::getKategoriOptions();

        return view('home.lowongan-kerja', compact(
            'lowongan',
            'tipeOptions',
            'kategoriOptions'
        ));
    }

    /**
     * Display single job detail
     */
    public function lowonganKerjaDetail($id)
    {
        $lowongan = LowonganKerja::active()->findOrFail($id);

        // Increment views
        $lowongan->incrementViews();

        // Get related jobs
        $relatedJobs = LowonganKerja::active()
            ->where('id', '!=', $lowongan->id)
            ->where(function($query) use ($lowongan) {
                $query->where('kategori', $lowongan->kategori)
                      ->orWhere('perusahaan', $lowongan->perusahaan);
            })
            ->limit(3)
            ->get();

        return view('home.lowongan-kerja-detail', compact('lowongan', 'relatedJobs'));
    }

    /**
     * Display internship and MBKM programs page (KF-03)
     */
    public function programMagang(Request $request)
    {
        $query = ProgramMagang::active();

        // Search functionality
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->search($search);
            }
        }

        // Filter by type
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            $query->byTipe($request->tipe);
        }

        // Filter by location
        if ($request->filled('lokasi')) {
            $lokasi = trim($request->lokasi);
            if (!empty($lokasi)) {
                $query->byLokasi($lokasi);
            }
        }

        // Sorting
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

        $programs = $query->paginate(12)->withQueryString();

        // Get filter options
        $tipeOptions = ProgramMagang::getTipeOptions();

        return view('home.program-magang', compact('programs', 'tipeOptions'));
    }

    /**
     * Display single program detail
     */
    public function programMagangDetail($id)
    {
        $program = ProgramMagang::active()->findOrFail($id);

        // Increment views
        $program->incrementViews();

        // Get related programs
        $relatedPrograms = ProgramMagang::active()
            ->where('id', '!=', $program->id)
            ->where(function($query) use ($program) {
                $query->where('tipe', $program->tipe)
                      ->orWhere('perusahaan', $program->perusahaan);
            })
            ->limit(3)
            ->get();

        return view('home.program-magang-detail', compact('program', 'relatedPrograms'));
    }

    /**
     * Display news/newsletter page (KF-04)
     */
    public function berita(Request $request)
    {
        $query = Berita::published();

        // Search functionality
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search)) {
                $query->search($search);
            }
        }

        // Filter by category
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->byKategori($request->kategori);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('tanggal_publikasi', 'asc');
                break;
            default:
                $query->latest();
        }

        $berita = $query->paginate(9)->withQueryString();

        // Get featured news
        $featuredNews = Berita::published()
            ->featured()
            ->latest()
            ->limit(3)
            ->get();

        // Get category options
        $kategoriOptions = Berita::getKategoriOptions();

        return view('home.berita', compact('berita', 'featuredNews', 'kategoriOptions'));
    }

    /**
     * Display single news detail
     */
    public function beritaDetail($slug)
    {
        $berita = Berita::published()->where('slug', $slug)->firstOrFail();

        // Increment views
        $berita->incrementViews();

        // Get related news
        $relatedNews = Berita::published()
            ->where('id', '!=', $berita->id)
            ->where('kategori', $berita->kategori)
            ->latest()
            ->limit(3)
            ->get();

        return view('home.berita-detail', compact('berita', 'relatedNews'));
    }

    /**
     * Display about page (KF-05)
     */
    public function tentang()
    {
        $tentang = Tentang::first();

        if (!$tentang) {
            abort(404, 'Halaman tentang belum tersedia.');
        }

        return view('home.tentang', compact('tentang'));
    }

    /**
     * Display contact page (KF-06)
     */
    public function kontak()
    {
        $kontak = Kontak::first();

        if (!$kontak) {
            abort(404, 'Halaman kontak belum tersedia.');
        }

        return view('home.kontak', compact('kontak'));
    }

    /**
     * Handle newsletter subscription (KF-08)
     */
    public function subscribeNewsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:langganan_newsletter,email',
            'nama' => 'nullable|string|max:255',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('newsletter_error', true);
        }

        // Sanitize input
        $email = trim($request->email);
        $nama = $request->nama ? trim($request->nama) : null;

        // Validate not empty
        if (empty($email)) {
            return back()
                ->withErrors(['email' => 'Email tidak boleh kosong.'])
                ->withInput()
                ->with('newsletter_error', true);
        }

        // Create subscription
        LanggananNewsletter::create([
            'email' => $email,
            'nama' => $nama,
            'status' => true,
            'verified_at' => now(), // Auto-verify for simplicity
        ]);

        return back()->with('success', 'Terima kasih! Anda telah berlangganan newsletter kami.');
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribeNewsletter($email)
    {
        $subscriber = LanggananNewsletter::where('email', $email)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Email tidak ditemukan.');
        }

        $subscriber->unsubscribe();

        return redirect()->route('home')->with('success', 'Anda telah berhenti berlangganan newsletter.');
    }
}