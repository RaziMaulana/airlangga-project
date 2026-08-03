<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\siswa as Siswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->input('mode', 'single');

        if ($mode === 'range' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            if ($startDate->gt($endDate)) {
                $temp = $startDate;
                $startDate = $endDate;
                $endDate = $temp;
            }
        } else {
            $mode = 'single';
            $tanggal = $request->filled('tanggal')
                ? Carbon::parse($request->tanggal)
                : Carbon::today();
            $startDate = $tanggal;
            $endDate = $tanggal;
        }

        $startDateString = $startDate->toDateString();
        $endDateString = $endDate->toDateString();

        $totalSiswa = Siswa::count();

        $presensisPaginator = Presensi::query()
            ->select('id', 'uid_tag', 'id_kelas', 'tanggal', 'waktu', 'status')
            ->with([
                'siswa:uid_tag,nama_siswa',
                'kelas:id,nama_kelas',
            ])
            ->when($mode === 'single', function ($q) use ($startDateString) {
                return $q->where('tanggal', $startDateString);
            }, function ($q) use ($startDateString, $endDateString) {
                return $q->whereBetween('tanggal', [$startDateString, $endDateString]);
            })
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $stats = $this->calculateStats($mode, $startDate, $endDate, $totalSiswa);
        $weeklyTrend = $this->calculateWeeklyTrend($endDate->copy(), $totalSiswa);

        if ($request->ajax()) {
            return response()->json([
                'presensis' => collect($presensisPaginator->items())->map(fn($p) => [
                    'nama' => $p->siswa->nama_siswa ?? $p->uid_tag,
                    'kelas' => $p->kelas->nama_kelas ?? '-',
                    'tanggal' => Carbon::parse($p->tanggal)->format('d-m-Y'),
                    'waktu' => $p->waktu,
                    'status' => $p->status,
                ]),
                'pagination' => [
                    'current_page' => $presensisPaginator->currentPage(),
                    'last_page' => $presensisPaginator->lastPage(),
                    'per_page' => $presensisPaginator->perPage(),
                    'total' => $presensisPaginator->total(),
                    'from' => $presensisPaginator->firstItem(),
                    'to' => $presensisPaginator->lastItem(),
                ],
                'count' => $presensisPaginator->total(),
                'stats' => $stats,
                'weeklyTrend' => $weeklyTrend,
            ]);
        }

        $presensis = $presensisPaginator;
        $tanggal = $startDate;

        return view('admin.dashboard', compact('presensis', 'tanggal', 'stats', 'weeklyTrend'));
    }

    private function calculateStats(string $mode, Carbon $startDate, Carbon $endDate, int $totalSiswa): array
    {
        $startStr = $startDate->toDateString();
        $endStr = $endDate->toDateString();

        if ($mode === 'single') {
            $hadirCount = Presensi::where('tanggal', $startStr)
                ->where('status', 'IN')
                ->distinct('uid_tag')
                ->count('uid_tag');

            $terlambatCount = Presensi::where('tanggal', $startStr)
                ->where('status', 'OUT')
                ->distinct('uid_tag')
                ->count('uid_tag');

            $prevStartStr = $startDate->copy()->subDay()->toDateString();
            $prevEndStr = $prevStartStr;
        } else {
            $daysDiff = $startDate->diffInDays($endDate) + 1;

            $hadirCount = Presensi::whereBetween('tanggal', [$startStr, $endStr])
                ->where('status', 'IN')
                ->distinct('uid_tag')
                ->count('uid_tag');

            $terlambatCount = Presensi::whereBetween('tanggal', [$startStr, $endStr])
                ->where('status', 'OUT')
                ->distinct('uid_tag')
                ->count('uid_tag');

            $prevEndStr = $startDate->copy()->subDay()->toDateString();
            $prevStartStr = $startDate->copy()->subDays($daysDiff)->toDateString();
        }

        $hadirKemarin = Presensi::whereBetween('tanggal', [$prevStartStr, $prevEndStr])
            ->where('status', 'IN')
            ->distinct('uid_tag')
            ->count('uid_tag');

        $terlambatKemarin = Presensi::whereBetween('tanggal', [$prevStartStr, $prevEndStr])
            ->where('status', 'OUT')
            ->distinct('uid_tag')
            ->count('uid_tag');

        $persentaseKehadiran = $totalSiswa > 0 ? round(($hadirCount / $totalSiswa) * 100) : 0;
        $persentaseKeterlambatan = $totalSiswa > 0 ? round(($terlambatCount / $totalSiswa) * 100) : 0;
        $persentaseKehadiranKemarin = $totalSiswa > 0 ? round(($hadirKemarin / $totalSiswa) * 100) : 0;
        $persentaseKeterlambatanKemarin = $totalSiswa > 0 ? round(($terlambatKemarin / $totalSiswa) * 100) : 0;

        return [
            'kehadiran' => $persentaseKehadiran,
            'keterlambatan' => $persentaseKeterlambatan,
            'hadir_count' => $hadirCount,
            'terlambat_count' => $terlambatCount,
            'total_siswa' => $totalSiswa,
            'trend_kehadiran' => $this->resolveTrend($persentaseKehadiran, $persentaseKehadiranKemarin, true),
            'trend_keterlambatan' => $this->resolveTrend($persentaseKeterlambatan, $persentaseKeterlambatanKemarin, false),
        ];
    }

    private function resolveTrend(int $sekarang, int $kemarin, bool $naikBaik): array
    {
        if ($sekarang === $kemarin) {
            return ['arrow' => '→', 'label' => 'stabil'];
        }

        $naik = $sekarang > $kemarin;

        if ($naikBaik) {
            return $naik
                ? ['arrow' => '↑', 'label' => 'meningkat']
                : ['arrow' => '↓', 'label' => 'menurun'];
        }

        return $naik
            ? ['arrow' => '↑', 'label' => 'perlu diawasi']
            : ['arrow' => '↓', 'label' => 'membaik'];
    }

    private function calculateWeeklyTrend(Carbon $tanggal, int $totalSiswa): array
    {
        $labels = [];
        $data = [];

        $start = $tanggal->copy()->subDays(6);

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $dateString = $date->toDateString();

            $hadirCount = Presensi::where('tanggal', $dateString)
                ->where('status', 'IN')
                ->distinct('uid_tag')
                ->count('uid_tag');

            $persentase = $totalSiswa > 0 ? round(($hadirCount / $totalSiswa) * 100) : 0;

            $labels[] = $date->translatedFormat('D');
            $data[] = $persentase;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
