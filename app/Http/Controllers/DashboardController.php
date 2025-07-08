<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalPemasukan = Transaksi::where('user_id', $userId)
        ->where('jenis', 'Pemasukan')
        ->sum('nominal');

        $totalPengeluaran = Transaksi::where('user_id', $userId)
        ->where('jenis', 'Pengeluaran')
        ->sum('nominal');

        $saldo = $totalPemasukan - $totalPengeluaran;

      

        $pemasukanBulanan = DB::table('transaksis')->select(DB::raw('EXTRACT(MONTH FROM tanggal) as bulan'), DB::raw('SUM(nominal) as total'))->where('jenis', 'Pemasukan')->where('user_id', $userId)->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal)'))->pluck('total', 'bulan')->toArray();
        $pemasukanFinal = array_fill(0, 12, 0);

        foreach ($pemasukanBulanan as $bulan => $total) {
            $pemasukanFinal[$bulan - 1] = $total;
        }

        $pengeluaranBulanan = DB::table('transaksis')->select(DB::raw('EXTRACT(MONTH FROM tanggal) as bulan'), DB::raw('SUM(nominal) as total'))->where('jenis', 'Pengeluaran')->where('user_id', $userId)->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal)'))->pluck('total', 'bulan')->toArray();

        $pengeluaranFinal = array_fill(0, 12, 0);

        foreach ($pengeluaranBulanan as $bulan => $total) {
            $pengeluaranFinal[$bulan - 1] = $total;
        }
        return Inertia('Dashboard', [
            'pemasukans' => $totalPemasukan,
            'pengeluarans' => $totalPengeluaran,
            'saldos' => $saldo,
            'pengeluaranFinals' => $pengeluaranFinal,
            'pemasukanFinals' => $pemasukanFinal,
        ]);
    }
}
