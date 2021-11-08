<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        $informasi = Informasi::latest()->get();
        if ($request->get('page')) {
            $informasi = Informasi::latest()->paginate($request->get('limit'));
        }
        return response([
            'success' => true,
            'message' => 'Information list',
            'count' => $informasi->count(),
            'data'   => $informasi
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'judul' => 'required|unique:informasi',
            'deskripsi' => 'required',
            'isi' => 'required',
            'kategori' => 'required',
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
            $request->file('gambar')->move('img/', $name);
        }

        $informasi = Informasi::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul),
            'deskripsi' => $request->deskripsi,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'gambar' => $name,
            'views' => 0,
        ]);

        return response([
            'success' => true,
            'message' => 'Data has been added!',
            'data'   => $informasi
        ], 200);
    }

    public function show($slug)
    {
        $informasi = Informasi::where('slug', $slug)->get()->first();
        return response([
            'success' => true,
            'message' => 'Show Informasi',
            'data'   => $informasi
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $informasi = Informasi::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'judul' => 'required',
            'deskripsi' => 'required',
            'isi' => 'required',
            'kategori' => 'required',
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
            File::delete('img/' . $informasi->gambar);

            $file = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $name = Str::slug($filename) . '-' . time() . '.' . $extension;

            $request->file('gambar')->move('img/', $name);
        } else {
            $name = $informasi->gambar;
        }

        $informasi->update([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul),
            'deskripsi' => $request->deskripsi,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'gambar' => $name,
        ]);

        return response([
            'success' => true,
            'message' => 'Data has been updated!',
            'data'   => $informasi
        ], 200);
    }

    public function destroy($id)
    {
        $informasi = Informasi::findOrFail($id);
        File::delete('img/' . $informasi->gambar);

        $informasi->delete();
        return response([
            'success' => true,
            'message' => 'Data has been deleted!',
            'data'   => $informasi
        ], 200);
    }
}
