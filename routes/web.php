<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DUKController;
use App\Http\Controllers\KenaikanPangkatController;
use App\Http\Controllers\KGBController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PensiunController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\SatyalencanaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GolonganController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\TabelGajiController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export-pdf');

    // Pegawai
    Route::resource('pegawai', PegawaiController::class);
    Route::get('/pegawai-data', [PegawaiController::class, 'getPaginated'])->name('pegawai.data');
    Route::patch('/pegawai/{pegawai}/reactivate', [PegawaiController::class, 'reactivate'])->name('pegawai.reactivate')->withTrashed();
    Route::patch('/pegawai/{pegawai}/cancel-pensiun', [PegawaiController::class, 'cancelPensiun'])->name('pegawai.cancel-pensiun')->withTrashed();

    // Riwayat Pangkat
    Route::get('/riwayat/pangkat/create/{pegawaiId}', [RiwayatController::class, 'createPangkat'])->name('riwayat.pangkat.create');
    Route::post('/riwayat/pangkat', [RiwayatController::class, 'storePangkat'])->name('riwayat.pangkat.store');
    Route::get('/riwayat/pangkat/{riwayatPangkat}/edit', [RiwayatController::class, 'editPangkat'])->name('riwayat.pangkat.edit');
    Route::put('/riwayat/pangkat/{riwayatPangkat}', [RiwayatController::class, 'updatePangkat'])->name('riwayat.pangkat.update');
    Route::delete('/riwayat/pangkat/{riwayatPangkat}', [RiwayatController::class, 'destroyPangkat'])->name('riwayat.pangkat.destroy');

    // Riwayat Jabatan
    Route::get('/riwayat/jabatan/create/{pegawaiId}', [RiwayatController::class, 'createJabatan'])->name('riwayat.jabatan.create');
    Route::post('/riwayat/jabatan', [RiwayatController::class, 'storeJabatan'])->name('riwayat.jabatan.store');
    Route::get('/riwayat/jabatan/{riwayatJabatan}/edit', [RiwayatController::class, 'editJabatan'])->name('riwayat.jabatan.edit');
    Route::put('/riwayat/jabatan/{riwayatJabatan}', [RiwayatController::class, 'updateJabatan'])->name('riwayat.jabatan.update');
    Route::delete('/riwayat/jabatan/{riwayatJabatan}', [RiwayatController::class, 'destroyJabatan'])->name('riwayat.jabatan.destroy');

    // Riwayat KGB
    Route::get('/riwayat/kgb/create/{pegawaiId}', [RiwayatController::class, 'createKGB'])->name('riwayat.kgb.create');
    Route::post('/riwayat/kgb', [RiwayatController::class, 'storeKGB'])->name('riwayat.kgb.store');
    Route::get('/riwayat/kgb/{riwayatKgb}/edit', [RiwayatController::class, 'editKGB'])->name('riwayat.kgb.edit');
    Route::put('/riwayat/kgb/{riwayatKgb}', [RiwayatController::class, 'updateKGB'])->name('riwayat.kgb.update');
    Route::delete('/riwayat/kgb/{riwayatKgb}', [RiwayatController::class, 'destroyKGB'])->name('riwayat.kgb.destroy');

    // Riwayat Hukuman Disiplin
    Route::get('/riwayat/hukuman/create/{pegawaiId}', [RiwayatController::class, 'createHukuman'])->name('riwayat.hukuman.create');
    Route::post('/riwayat/hukuman', [RiwayatController::class, 'storeHukuman'])->name('riwayat.hukuman.store');
    Route::get('/riwayat/hukuman/{riwayatHukuman}/edit', [RiwayatController::class, 'editHukuman'])->name('riwayat.hukuman.edit');
    Route::put('/riwayat/hukuman/{riwayatHukuman}', [RiwayatController::class, 'updateHukuman'])->name('riwayat.hukuman.update');
    Route::delete('/riwayat/hukuman/{riwayatHukuman}', [RiwayatController::class, 'destroyHukuman'])->name('riwayat.hukuman.destroy');
    Route::post('/riwayat/hukuman/{riwayatHukuman}/pulihkan', [RiwayatController::class, 'pulihkanHukuman'])->name('riwayat.hukuman.pulihkan');

    // Riwayat Pendidikan
    Route::get('/riwayat/pendidikan/create/{pegawaiId}', [RiwayatController::class, 'createPendidikan'])->name('riwayat.pendidikan.create');
    Route::post('/riwayat/pendidikan', [RiwayatController::class, 'storePendidikan'])->name('riwayat.pendidikan.store');
    Route::get('/riwayat/pendidikan/{riwayatPendidikan}/edit', [RiwayatController::class, 'editPendidikan'])->name('riwayat.pendidikan.edit');
    Route::put('/riwayat/pendidikan/{riwayatPendidikan}', [RiwayatController::class, 'updatePendidikan'])->name('riwayat.pendidikan.update');
    Route::delete('/riwayat/pendidikan/{riwayatPendidikan}', [RiwayatController::class, 'destroyPendidikan'])->name('riwayat.pendidikan.destroy');

    // Riwayat Latihan
    Route::get('/riwayat/latihan/create/{pegawaiId}', [RiwayatController::class, 'createLatihan'])->name('riwayat.latihan.create');
    Route::post('/riwayat/latihan', [RiwayatController::class, 'storeLatihan'])->name('riwayat.latihan.store');
    Route::get('/riwayat/latihan/{riwayatLatihan}/edit', [RiwayatController::class, 'editLatihan'])->name('riwayat.latihan.edit');
    Route::put('/riwayat/latihan/{riwayatLatihan}', [RiwayatController::class, 'updateLatihan'])->name('riwayat.latihan.update');
    Route::delete('/riwayat/latihan/{riwayatLatihan}', [RiwayatController::class, 'destroyLatihan'])->name('riwayat.latihan.destroy');

    // Penilaian Kinerja (SKP)
    Route::get('/riwayat/skp/create/{pegawaiId}', [RiwayatController::class, 'createSKP'])->name('riwayat.skp.create');
    Route::post('/riwayat/skp', [RiwayatController::class, 'storeSKP'])->name('riwayat.skp.store');
    Route::get('/riwayat/skp/{penilaianKinerja}/edit', [RiwayatController::class, 'editSKP'])->name('riwayat.skp.edit');
    Route::put('/riwayat/skp/{penilaianKinerja}', [RiwayatController::class, 'updateSKP'])->name('riwayat.skp.update');
    Route::delete('/riwayat/skp/{penilaianKinerja}', [RiwayatController::class, 'destroySKP'])->name('riwayat.skp.destroy');

    // Reports
    Route::get('/kgb', [KGBController::class, 'index'])->name('kgb.index');
    Route::get('/kgb/upcoming', [KGBController::class, 'upcoming'])->name('kgb.upcoming');
    Route::get('/kgb/eligible', [KGBController::class, 'eligible'])->name('kgb.eligible');
    Route::get('/kgb/ditunda', [KGBController::class, 'ditunda'])->name('kgb.ditunda');
    Route::get('/kgb/process/{pegawai}', [KGBController::class, 'showProcessForm'])->name('kgb.process.form');
    Route::post('/kgb/process', [KGBController::class, 'process'])->name('kgb.process');

    Route::get('/kenaikan-pangkat', [KenaikanPangkatController::class, 'index'])->name('kenaikan-pangkat.index');
    Route::get('/kenaikan-pangkat/eligible', [KenaikanPangkatController::class, 'eligible'])->name('kenaikan-pangkat.eligible');
    Route::get('/kenaikan-pangkat/ditunda', [KenaikanPangkatController::class, 'ditunda'])->name('kenaikan-pangkat.ditunda');
    Route::get('/kenaikan-pangkat/process/{pegawai}', [KenaikanPangkatController::class, 'showProcessForm'])->name('kenaikan-pangkat.process.form');
    Route::post('/kenaikan-pangkat/process', [KenaikanPangkatController::class, 'process'])->name('kenaikan-pangkat.process');

    Route::get('/pensiun', [PensiunController::class, 'index'])->name('pensiun.index');
    Route::get('/pensiun/process/{pegawai}', [PensiunController::class, 'showProcessForm'])->name('pensiun.process.form');
    Route::post('/pensiun/process', [PensiunController::class, 'process'])->name('pensiun.process');
    Route::get('/duk', [DUKController::class, 'index'])->name('duk.index');
    Route::get('/satyalencana', [SatyalencanaController::class, 'index'])->name('satyalencana.index');
    Route::post('/satyalencana/award', [SatyalencanaController::class, 'award'])->name('satyalencana.award');

    // Export
    Route::get('/export/{type}/{format}', [ExportController::class, 'export'])->name('export');

    // Document Download
    Route::get('/dokumen/{type}/{id}', [DocumentController::class, 'download'])
        ->where('type', 'pangkat|jabatan|kgb|hukuman|pendidikan|latihan|skp|pensiun')
        ->where('id', '[0-9]+')
        ->name('dokumen.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Admin Setting (SuperAdmin only)
    Route::prefix('admin')->middleware('superadmin')->group(function () {
        Route::get('/tabel-gaji', [TabelGajiController::class, 'index'])->name('admin.tabel-gaji.index');
        Route::post('/tabel-gaji', [TabelGajiController::class, 'store'])->name('admin.tabel-gaji.store');
        Route::get('/tabel-gaji/{golongan}', [TabelGajiController::class, 'show'])->where('golongan', '[0-9]+')->name('admin.tabel-gaji.show');
        Route::put('/tabel-gaji/{tabelGaji}', [TabelGajiController::class, 'update'])->name('admin.tabel-gaji.update');
        Route::delete('/tabel-gaji/{tabelGaji}', [TabelGajiController::class, 'destroy'])->name('admin.tabel-gaji.destroy');

        Route::get('/golongan', [GolonganController::class, 'index'])->name('admin.golongan.index');
        Route::get('/golongan/create', [GolonganController::class, 'create'])->name('admin.golongan.create');
        Route::post('/golongan', [GolonganController::class, 'store'])->name('admin.golongan.store');
        Route::get('/golongan/{golonganPangkat}/edit', [GolonganController::class, 'edit'])->name('admin.golongan.edit');
        Route::put('/golongan/{golonganPangkat}', [GolonganController::class, 'update'])->name('admin.golongan.update');
        Route::patch('/golongan/{golonganPangkat}/toggle-active', [GolonganController::class, 'toggleActive'])->name('admin.golongan.toggle-active');
        Route::delete('/golongan/{golonganPangkat}', [GolonganController::class, 'destroy'])->name('admin.golongan.destroy');

        Route::resource('jabatan', JabatanController::class)->names('admin.jabatan')->except(['show']);
        Route::patch('/jabatan/{jabatan}/toggle-active', [JabatanController::class, 'toggleActive'])->name('admin.jabatan.toggle-active');

        // Generic Master Data CRUD (8 normalized tables)
        Route::get('/master-data/{entity}', [MasterDataController::class, 'index'])->name('admin.master-data.index');
        Route::get('/master-data/{entity}/create', [MasterDataController::class, 'create'])->name('admin.master-data.create');
        Route::post('/master-data/{entity}', [MasterDataController::class, 'store'])->name('admin.master-data.store');
        Route::get('/master-data/{entity}/{id}/edit', [MasterDataController::class, 'edit'])->name('admin.master-data.edit');
        Route::put('/master-data/{entity}/{id}', [MasterDataController::class, 'update'])->name('admin.master-data.update');
        Route::delete('/master-data/{entity}/{id}', [MasterDataController::class, 'destroy'])->name('admin.master-data.destroy');
    });
});
