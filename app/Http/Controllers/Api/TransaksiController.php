<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaksi;


class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        $transaksi = Transaksi::paginate(20);
        return response()->json([
            "status" => true,
            "message" => "Berhasil",
            "data" => $transaksi,
        ],200);
    }

   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'jenis' => 'required|in:Pemasukan,Pengeluaran',
        'nominal' => 'required|numeric',
        'deskripsi' => 'required|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    try {
        $transaksi = Transaksi::create($request->all());
        $response = [
            'status' => true,
            'message' => "Success",
            'data' => $transaksi
        ];
        return response()->json($response, 201);
    } catch (QueryException $e) {
        return response()->json([
            "message" => "Failed" . $e->errorInfo
        ]);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return response()->json([
            "status" => true,
            "message" => "Berhasil",
            "data" => $transaksi
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'jenis' => 'required|in:Pemasukan,Pengeluaran',
        'nominal' => 'required|numeric',
        'deskripsi' => 'required|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    try {
        $transaksi->update($request->all());
        $response = [
            'status' => true,
            'message' => "Success",
            'data' => $transaksi
        ];
        return response()->json($response, 200);
    } catch (QueryExepcetion $e) {
        return response()->json([
            "message" => "Failed" . $e->errorInfo
        ]);
    }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();
        return response()->json([
            "status" => true,
            "message" => "Berhasil",
        ],200);
    }
}
