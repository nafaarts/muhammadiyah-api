<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Models\Donatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonaturController extends Controller
{
    public function index()
    {
        $donatur = Donatur::latest()->get();
        $data = collect($donatur)->map(function ($item, $key) {
            return collect($item)->merge(['donasi' => Donasi::findOrFail($item->donasi)]);
        });
        return response([
            'success' => true,
            'message' => 'Donation list',
            'count' => $donatur->count(),
            'data'   => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email',
            'jumlah' => 'required|integer',
            'private' => 'required|boolean',
            'donasi' => 'required|integer|exists:donasi,id',
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation error!',
                'data'   => $validation->errors()
            ], 422);
        }

        $donatur = Donatur::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'jumlah' => $request->jumlah,
            'private' => $request->private,
            'donasi' => $request->donasi
        ]);

        $donasi = Donasi::findOrFail($request->donasi);
        $donasi->update([
            'jumlah' => ($donasi->jumlah + $request->jumlah)
        ]);

        $data = collect($donatur)->merge(['donasi' => $donasi]);

        return response([
            'success' => true,
            'message' => 'Donation Category has been added!',
            'data'   => $data
        ], 200);
    }

    public function show(Donatur $donatur)
    {
        return response('undefined', 404);
    }

    public function update(Request $request, Donatur $donatur)
    {
        return response('undefined', 404);
    }

    public function destroy(Donatur $donatur)
    {
        return response('undefined', 404);
    }
}
