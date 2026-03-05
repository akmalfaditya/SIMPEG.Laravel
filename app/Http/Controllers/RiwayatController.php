<?php

namespace App\Http\Controllers;

use App\Enums\GolonganRuang;
use App\Enums\TingkatHukuman;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PenilaianKinerja;
use App\Models\RiwayatHukumanDisiplin;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatKgb;
use App\Models\RiwayatLatihanJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    // --- PANGKAT ---
    public function createPangkat(int $pegawaiId)
    {
        return view('riwayat.create-pangkat', [
            'pegawaiId' => $pegawaiId,
            'golonganOptions' => GolonganRuang::cases(),
        ]);
    }

    public function storePangkat(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'golongan_ruang' => 'required|integer',
            'nomor_sk' => 'nullable|string',
            'tmt_pangkat' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        RiwayatPangkat::create($validated);
        return redirect()->route('pegawai.show', $validated['pegawai_id'])->with('success', 'Riwayat Pangkat berhasil ditambahkan.');
    }

    public function editPangkat(RiwayatPangkat $riwayatPangkat)
    {
        return view('riwayat.edit-pangkat', [
            'riwayat' => $riwayatPangkat,
            'golonganOptions' => GolonganRuang::cases(),
        ]);
    }

    public function updatePangkat(Request $request, RiwayatPangkat $riwayatPangkat)
    {
        $validated = $request->validate([
            'golongan_ruang' => 'required|integer',
            'nomor_sk' => 'nullable|string',
            'tmt_pangkat' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        $riwayatPangkat->update($validated);
        return redirect()->route('pegawai.show', $riwayatPangkat->pegawai_id)->with('success', 'Riwayat Pangkat berhasil diperbarui.');
    }

    public function destroyPangkat(RiwayatPangkat $riwayatPangkat)
    {
        $pegawaiId = $riwayatPangkat->pegawai_id;
        $riwayatPangkat->delete();
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- JABATAN ---
    public function createJabatan(int $pegawaiId)
    {
        return view('riwayat.create-jabatan', [
            'pegawaiId' => $pegawaiId,
            'jabatanOptions' => Jabatan::orderBy('nama_jabatan')->get(),
        ]);
    }

    public function storeJabatan(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'nomor_sk' => 'nullable|string',
            'tmt_jabatan' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        RiwayatJabatan::create($validated);
        return redirect()->route('pegawai.show', $validated['pegawai_id'])->with('success', 'Riwayat Jabatan berhasil ditambahkan.');
    }

    public function editJabatan(RiwayatJabatan $riwayatJabatan)
    {
        return view('riwayat.edit-jabatan', [
            'riwayat' => $riwayatJabatan,
            'jabatanOptions' => Jabatan::orderBy('nama_jabatan')->get(),
        ]);
    }

    public function updateJabatan(Request $request, RiwayatJabatan $riwayatJabatan)
    {
        $validated = $request->validate([
            'jabatan_id' => 'required|exists:jabatans,id',
            'nomor_sk' => 'nullable|string',
            'tmt_jabatan' => 'required|date',
            'tanggal_sk' => 'required|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        $riwayatJabatan->update($validated);
        return redirect()->route('pegawai.show', $riwayatJabatan->pegawai_id)->with('success', 'Riwayat Jabatan berhasil diperbarui.');
    }

    public function destroyJabatan(RiwayatJabatan $riwayatJabatan)
    {
        $pegawaiId = $riwayatJabatan->pegawai_id;
        $riwayatJabatan->delete();
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- KGB ---
    public function createKGB(int $pegawaiId)
    {
        $peg = Pegawai::findOrFail($pegawaiId);
        return view('riwayat.create-kgb', [
            'pegawaiId' => $pegawaiId,
            'gajiPokok' => $peg->gaji_pokok,
        ]);
    }

    public function storeKGB(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'nomor_sk' => 'nullable|string',
            'tmt_kgb' => 'required|date',
            'gaji_lama' => 'required|numeric',
            'gaji_baru' => 'required|numeric',
            'masa_kerja_golongan_tahun' => 'required|integer',
            'masa_kerja_golongan_bulan' => 'required|integer',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        RiwayatKgb::create($validated);
        // Update gaji pokok
        Pegawai::where('id', $validated['pegawai_id'])->update(['gaji_pokok' => $validated['gaji_baru']]);
        return redirect()->route('pegawai.show', $validated['pegawai_id'])->with('success', 'Riwayat KGB berhasil ditambahkan.');
    }

    public function editKGB(RiwayatKgb $riwayatKgb)
    {
        return view('riwayat.edit-kgb', ['riwayat' => $riwayatKgb]);
    }

    public function updateKGB(Request $request, RiwayatKgb $riwayatKgb)
    {
        $validated = $request->validate([
            'nomor_sk' => 'nullable|string',
            'tmt_kgb' => 'required|date',
            'gaji_lama' => 'required|numeric',
            'gaji_baru' => 'required|numeric',
            'masa_kerja_golongan_tahun' => 'required|integer',
            'masa_kerja_golongan_bulan' => 'required|integer',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        $riwayatKgb->update($validated);
        return redirect()->route('pegawai.show', $riwayatKgb->pegawai_id)->with('success', 'Riwayat KGB berhasil diperbarui.');
    }

    public function destroyKGB(RiwayatKgb $riwayatKgb)
    {
        $pegawaiId = $riwayatKgb->pegawai_id;
        $riwayatKgb->delete();
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- HUKUMAN DISIPLIN ---
    public function createHukuman(int $pegawaiId)
    {
        return view('riwayat.create-hukuman', [
            'pegawaiId' => $pegawaiId,
            'tingkatOptions' => TingkatHukuman::cases(),
        ]);
    }

    public function storeHukuman(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tingkat_hukuman' => 'required|integer',
            'jenis_hukuman' => 'required|string',
            'nomor_sk' => 'nullable|string',
            'tanggal_sk' => 'nullable|date',
            'tmt_hukuman' => 'required|date',
            'tmt_selesai_hukuman' => 'nullable|date',
            'deskripsi' => 'nullable|string',
        ]);
        RiwayatHukumanDisiplin::create($validated);
        return redirect()->route('pegawai.show', $validated['pegawai_id'])->with('success', 'Riwayat Hukuman berhasil ditambahkan.');
    }

    public function editHukuman(RiwayatHukumanDisiplin $riwayatHukuman)
    {
        return view('riwayat.edit-hukuman', [
            'riwayat' => $riwayatHukuman,
            'tingkatOptions' => TingkatHukuman::cases(),
        ]);
    }

    public function updateHukuman(Request $request, RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $validated = $request->validate([
            'tingkat_hukuman' => 'required|integer',
            'jenis_hukuman' => 'required|string',
            'nomor_sk' => 'nullable|string',
            'tanggal_sk' => 'nullable|date',
            'tmt_hukuman' => 'required|date',
            'tmt_selesai_hukuman' => 'nullable|date',
            'deskripsi' => 'nullable|string',
        ]);
        $riwayatHukuman->update($validated);
        return redirect()->route('pegawai.show', $riwayatHukuman->pegawai_id)->with('success', 'Riwayat Hukuman berhasil diperbarui.');
    }

    public function destroyHukuman(RiwayatHukumanDisiplin $riwayatHukuman)
    {
        $pegawaiId = $riwayatHukuman->pegawai_id;
        $riwayatHukuman->delete();
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- PENDIDIKAN ---
    public function createPendidikan(int $pegawaiId)
    {
        return view('riwayat.create-pendidikan', ['pegawaiId' => $pegawaiId]);
    }

    public function storePendidikan(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tingkat_pendidikan' => 'required|string',
            'institusi' => 'required|string',
            'jurusan' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'no_ijazah' => 'nullable|string',
            'tanggal_ijazah' => 'nullable|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        RiwayatPendidikan::create($validated);
        return redirect()->route('pegawai.show', $validated['pegawai_id'])->with('success', 'Riwayat Pendidikan berhasil ditambahkan.');
    }

    public function editPendidikan(RiwayatPendidikan $riwayatPendidikan)
    {
        return view('riwayat.edit-pendidikan', ['riwayat' => $riwayatPendidikan]);
    }

    public function updatePendidikan(Request $request, RiwayatPendidikan $riwayatPendidikan)
    {
        $validated = $request->validate([
            'tingkat_pendidikan' => 'required|string',
            'institusi' => 'required|string',
            'jurusan' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'no_ijazah' => 'nullable|string',
            'tanggal_ijazah' => 'nullable|date',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        $riwayatPendidikan->update($validated);
        return redirect()->route('pegawai.show', $riwayatPendidikan->pegawai_id)->with('success', 'Riwayat Pendidikan berhasil diperbarui.');
    }

    public function destroyPendidikan(RiwayatPendidikan $riwayatPendidikan)
    {
        $pegawaiId = $riwayatPendidikan->pegawai_id;
        $riwayatPendidikan->delete();
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- LATIHAN JABATAN ---
    public function createLatihan(int $pegawaiId)
    {
        return view('riwayat.create-latihan', ['pegawaiId' => $pegawaiId]);
    }

    public function storeLatihan(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'nama_latihan' => 'required|string',
            'tahun_pelaksanaan' => 'required|integer',
            'jumlah_jam' => 'required|integer|min:0',
            'penyelenggara' => 'nullable|string',
            'tempat_pelaksanaan' => 'nullable|string',
            'no_sertifikat' => 'nullable|string',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        RiwayatLatihanJabatan::create($validated);
        return redirect()->route('pegawai.show', $validated['pegawai_id'])->with('success', 'Riwayat Latihan berhasil ditambahkan.');
    }

    public function editLatihan(RiwayatLatihanJabatan $riwayatLatihan)
    {
        return view('riwayat.edit-latihan', ['riwayat' => $riwayatLatihan]);
    }

    public function updateLatihan(Request $request, RiwayatLatihanJabatan $riwayatLatihan)
    {
        $validated = $request->validate([
            'nama_latihan' => 'required|string',
            'tahun_pelaksanaan' => 'required|integer',
            'jumlah_jam' => 'required|integer|min:0',
            'penyelenggara' => 'nullable|string',
            'tempat_pelaksanaan' => 'nullable|string',
            'no_sertifikat' => 'nullable|string',
            'file_pdf_path' => 'nullable|string',
            'google_drive_link' => 'nullable|string',
        ]);
        $riwayatLatihan->update($validated);
        return redirect()->route('pegawai.show', $riwayatLatihan->pegawai_id)->with('success', 'Riwayat Latihan berhasil diperbarui.');
    }

    public function destroyLatihan(RiwayatLatihanJabatan $riwayatLatihan)
    {
        $pegawaiId = $riwayatLatihan->pegawai_id;
        $riwayatLatihan->delete();
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }

    // --- PENILAIAN KINERJA (SKP) ---
    public function createSKP(int $pegawaiId)
    {
        return view('riwayat.create-skp', ['pegawaiId' => $pegawaiId]);
    }

    public function storeSKP(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tahun' => 'required|integer',
            'nilai_skp' => 'required|string',
        ]);
        PenilaianKinerja::create($validated);
        return redirect()->route('pegawai.show', $validated['pegawai_id'])->with('success', 'Penilaian Kinerja berhasil ditambahkan.');
    }

    public function editSKP(PenilaianKinerja $penilaianKinerja)
    {
        return view('riwayat.edit-skp', ['riwayat' => $penilaianKinerja]);
    }

    public function updateSKP(Request $request, PenilaianKinerja $penilaianKinerja)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer',
            'nilai_skp' => 'required|string',
        ]);
        $penilaianKinerja->update($validated);
        return redirect()->route('pegawai.show', $penilaianKinerja->pegawai_id)->with('success', 'Penilaian Kinerja berhasil diperbarui.');
    }

    public function destroySKP(PenilaianKinerja $penilaianKinerja)
    {
        $pegawaiId = $penilaianKinerja->pegawai_id;
        $penilaianKinerja->delete();
        return redirect()->route('pegawai.show', $pegawaiId)->with('success', 'Berhasil dihapus.');
    }
}
