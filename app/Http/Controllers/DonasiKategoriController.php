<?php

namespace App\Http\Controllers;

use App\Models\DonasiKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonasiKategoriController extends Controller
{
    public function index()
    {
        $kategori = DonasiKategori::latest()->get();
        return response([
            'success' => true,
            'message' => 'Donation Category list',
            'count' => $kategori->count(),
            'data'   => $kategori
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'kategori' => 'required|unique:donasi_kategori',
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation error!',
                'data'   => $validation->errors()
            ], 422);
        }

        $kategori = DonasiKategori::create([
            'kategori' => $request->kategori
        ]);

        return response([
            'success' => true,
            'message' => 'Donation Category has been added!',
            'data'   => $kategori
        ], 200);
    }

    public function show($id)
    {
        $kategori = DonasiKategori::findOrFail($id);
        return response([
            'success' => true,
            'message' => 'Show Donation Category',
            'data'   => $kategori
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $kategori = DonasiKategori::findOrFail($id);
        $validation = Validator::make($request->all(), [
            'kategori' => 'required|unique:donasi_kategori',
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation error!',
                'data'   => $validation->errors()
            ], 422);
        }

        $kategori->update([
            'kategori' => $request->kategori
        ]);

        return response([
            'success' => true,
            'message' => 'Donation category has been updated!',
            'data'   => $kategori
        ], 200);
    }

    public function destroy($id)
    {
        $kategori = DonasiKategori::findOrFail($id);
        $kategori->delete();
        return response([
            'success' => true,
            'message' => 'Donation Category has been deleted!',
            'data'   => $kategori
        ], 200);
    }
}
