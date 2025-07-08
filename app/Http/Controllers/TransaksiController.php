<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
           return Inertia::render('Transaksi', [
        'transaksis' => Transaksi::where('user_id', auth()->id())->get()
    ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:255'],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:Pemasukan,Pengeluaran'],
            'nominal' => ['required', 'numeric'],
            'deskripsi' => ['required', 'max:1000'],
        ]);

        $data['user_id'] = auth()->id();

        Transaksi::create($data);

        return to_route('transaksi');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($request->validate([
             'name' => ['required', 'max:255'],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:Pemasukan,Pengeluaran'],
            'nominal' => ['required', 'numeric'],
            'deskripsi' => ['required', 'max:1000'],
        ]));

        return to_route('transaksi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();
        return to_route('transaksi');
    }
}
