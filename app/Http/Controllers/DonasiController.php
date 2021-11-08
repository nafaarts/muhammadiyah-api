<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Models\DonasiKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DonasiController extends Controller
{
    public function index()
    {
        $donasi = Donasi::latest()->get();
        $data = collect($donasi)->map(function ($item, $key) {
            return collect($item)->merge(['kategori' => DonasiKategori::findOrFail($item->kategori)]);
        });
        return response([
            'success' => true,
            'message' => 'Donation list',
            'count' => $donasi->count(),
            'data'   => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'judul' => 'required|unique:informasi',
            'target' => 'required|integer',
            'kategori' => 'required|exists:donasi_kategori,id|integer',
            'gambar' => 'required|max:5048|mimes:jpg,png,jpeg|image'
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation error!',
                'data'   => $validation->errors()
            ], 422);
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $name = Str::slug($filename) . '-' . time() . '.' . $extension;
            $request->file('gambar')->move('img/donasi/', $name);
        }

        $donasi = Donasi::create([
            'judul' => Str::of($request->judul)->trim(),
            'target' => $request->target,
            'jumlah' => 0,
            'kategori' => $request->kategori,
            'gambar' => $name,
            'slug' => Str::slug($request->judul)
        ]);

        return response([
            'success' => true,
            'message' => 'Donasi has been added!',
            'data'   => $donasi
        ], 200);
    }

    public function show($id)
    {
        $donasi = Donasi::findOrFail($id);
        $data = collect($donasi)->merge(['kategori' => DonasiKategori::findOrFail($donasi->kategori)]);
        return response([
            'success' => true,
            'message' => 'Show Donation',
            'data'   => $data->all()
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $donasi = Donasi::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'judul' => 'required|unique:informasi',
            'target' => 'required|integer',
            'jumlah' => 'integer',
            'kategori' => 'required|exists:donasi_kategori,id|integer',
            'gambar' => 'max:5048|mimes:jpg,png,jpeg|image'
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation error!',
                'data'   => $validation->errors()
            ], 422);
        }

        if ($request->hasFile('gambar')) {
            File::delete('img/donasi/' . $donasi->gambar);

            $file = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $name = Str::slug($filename) . '-' . time() . '.' . $extension;

            $request->file('gambar')->move('img/donasi/', $name);
        } else {
            $name = $donasi->gambar;
        }

        $donasi->update([
            'judul' => Str::of($request->judul)->trim(),
            'target' => $request->target,
            'jumlah' => ($request->jumlah) ? $request->jumlah : $donasi->jumlah,
            'kategori' => $request->kategori,
            'gambar' => $name,
            'slug' => Str::slug($request->judul)
        ]);

        $data = collect($donasi)->merge(['kategori' => DonasiKategori::findOrFail($donasi->kategori)]);

        return response([
            'success' => true,
            'message' => 'Data has been updated!',
            'data'   => $data->all()
        ], 200);
    }

    public function destroy($id)
    {
        $donasi = Donasi::findOrFail($id);
        File::delete('img/donasi/' . $donasi->gambar);

        $donasi->delete();
        return response([
            'success' => true,
            'message' => 'Donasi has been deleted!',
            'data'   => $donasi
        ], 200);
    }
}
