<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Kelas;
use App\Models\siswa as Siswa;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ReportController extends Controller
{
    public function index()
    {
        $kelases = Kelas::orderBy('nama_kelas')->get();
        return view('admin.report', compact('kelases'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:harian,bulanan',
            'id_kelas' => 'required|exists:kelas,id',
            'format' => 'required|in:pdf,word',
            'tanggal' => 'required_if:type,harian',
            'bulan' => 'required_if:type,bulanan',
        ]);

        $type = $request->input('type');
        $format = $request->input('format');
        $kelas = Kelas::findOrFail($request->id_kelas);

        if ($type === 'harian') {
            $tanggalObj = Carbon::parse($request->tanggal);
            $presensis = Presensi::with(['siswa', 'kelas'])
                ->where('id_kelas', $kelas->id)
                ->where('tanggal', $tanggalObj->toDateString())
                ->orderBy('waktu', 'asc')
                ->get();

            $title = "Laporan Absensi Harian - Kelas {$kelas->nama_kelas}";
            $subtitle = "Tanggal: " . $tanggalObj->translatedFormat('d F Y');

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('admin.reports.pdf_harian', compact('presensis', 'kelas', 'title', 'subtitle'));
                return $pdf->download("Laporan_Absensi_Harian_{$kelas->nama_kelas}_{$tanggalObj->format('Ymd')}.pdf");
            } else {
                return $this->exportWordHarian($presensis, $kelas, $title, $subtitle, $tanggalObj);
            }
        } else {
            $bulanCarbon = Carbon::parse($request->bulan);
            $siswas = Siswa::where('id_kelas', $kelas->id)->orderBy('nama_siswa')->get();
            $daysInMonth = $bulanCarbon->daysInMonth;

            $presensisRaw = Presensi::where('id_kelas', $kelas->id)
                ->whereYear('tanggal', $bulanCarbon->year)
                ->whereMonth('tanggal', $bulanCarbon->month)
                ->get();

            $rekapSiswa = [];
            foreach ($siswas as $siswa) {
                $rekapSiswa[$siswa->uid_tag] = [
                    'nama' => $siswa->nama_siswa,
                    'presensi' => [],
                    'total_in' => 0,
                    'total_out' => 0,
                ];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $tglStr = $bulanCarbon->copy()->day($d)->toDateString();
                    $records = $presensisRaw->where('uid_tag', $siswa->uid_tag)->where('tanggal', $tglStr);

                    $hasIn = $records->where('status', 'IN')->isNotEmpty();
                    $hasOut = $records->where('status', 'OUT')->isNotEmpty();

                    if ($hasIn)
                        $rekapSiswa[$siswa->uid_tag]['total_in']++;
                    if ($hasOut)
                        $rekapSiswa[$siswa->uid_tag]['total_out']++;

                    $rekapSiswa[$siswa->uid_tag]['presensi'][$d] = [
                        'in' => $hasIn,
                        'out' => $hasOut
                    ];
                }
            }

            $title = "Laporan Absensi Bulanan - Kelas {$kelas->nama_kelas}";
            $subtitle = "Bulan: " . $bulanCarbon->translatedFormat('F Y');

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('admin.reports.pdf_bulanan', compact('rekapSiswa', 'kelas', 'daysInMonth', 'bulanCarbon', 'title', 'subtitle'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download("Laporan_Absensi_Bulanan_{$kelas->nama_kelas}_{$bulanCarbon->format('Y_m')}.pdf");
            } else {
                return $this->exportWordBulanan($rekapSiswa, $kelas, $daysInMonth, $title, $subtitle, $bulanCarbon);
            }
        }
    }

    public function exportWordHarian($presensis, $kelas, $title, $subtitle, $tanggalObj)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText($title, ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addText($subtitle, ['size' => 12], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];
        $phpWord->addTableStyle('AttendanceTable', $tableStyle);
        $table = $section->addTable('AttendanceTable');

        $table->addRow();
        $table->addCell(1000)->addText('No', ['bold' => true]);
        $table->addCell(3000)->addText('Nama Siswa', ['bold' => true]);
        $table->addCell(2000)->addText('Waktu', ['bold' => true]);
        $table->addCell(2000)->addText('Status', ['bold' => true]);

        foreach ($presensis as $index => $p) {
            $table->addRow();
            $table->addCell(1000)->addText($index + 1);
            $table->addCell(3000)->addText($p->siswa->nama_siswa ?? $p->uid_tag);
            $table->addCell(2000)->addText($p->waktu);
            $table->addCell(2000)->addText($p->status);
        }

        $fileName = "Laporan_Absensi_Harian_{$kelas->nama_kelas}_{$tanggalObj->format('Ymd')}.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportWordBulanan($rekapSiswa, $kelas, $daysInMonth, $title, $subtitle, $bulanCarbon)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection(['orientation' => 'landscape']);

        $section->addText($title, ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addText($subtitle, ['size' => 12], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $tableStyle = ['borderSize' => 4, 'borderColor' => 'CCCCCC', 'cellMargin' => 40];
        $phpWord->addTableStyle('MonthlyTable', $tableStyle);
        $table = $section->addTable('MonthlyTable');

        $table->addRow();
        $table->addCell(1500)->addText('Nama', ['bold' => true, 'size' => 9]);
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $table->addCell(300)->addText($d, ['bold' => true, 'size' => 8]);
        }
        $table->addCell(600)->addText('Hadir', ['bold' => true, 'size' => 8]);

        foreach ($rekapSiswa as $data) {
            $table->addRow();
            $table->addCell(1500)->addText($data['nama'], ['size' => 8]);
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $status = $data['presensi'][$d]['in'] ? 'H' : '-';
                $table->addCell(300)->addText($status, ['size' => 8]);
            }
            $table->addCell(600)->addText($data['total_in'], ['size' => 8]);
        }

        $fileName = "Laporan_Absensi_Bulanan_{$kelas->nama_kelas}_{$bulanCarbon->format('Y_m')}.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
