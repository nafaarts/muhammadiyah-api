<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use Gumlet\ImageResize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InformasiController extends Controller
{
    private function resizeImage($filename, $size, $output)
    {
        $image = new ImageResize($filename);
        $image->resizeToWidth($size, true);
        $image->save($output, IMAGETYPE_JPEG);
    }

    public function index(Request $request)
    {
        $informasi = Informasi::latest()->get();
        if ($request->get('page')) {
            $informasi = Informasi::latest()->paginate($request->get('limit'));
        }
        $data = collect($informasi)->map(function ($item) {
            return collect($item)->merge(['gambar' => env('BASE_URL') . 'img/informasi/' . $item->gambar]);
        });
        return response([
            'success' => true,
            'message' => 'Information list',
            'count' => $informasi->count(),
            'data'   => $data
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
            $path = 'img/informasi/';
            $request->file('gambar')->move($path, $name);
            $this->resizeImage($path . $name, 150, $path . 'thumbnail/' . $name);
            $this->resizeImage($path . $name, 400, $path . 'medium/' . $name);
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
        $data = collect($informasi)->merge(['gambar' => env('BASE_URL') . '/img/informasi/' . $informasi->gambar]);
        return response([
            'success' => true,
            'message' => 'Show Informasi',
            'data'   => $data
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
            $path = 'img/informasi/';
            $request->file('gambar')->move($path, $name);
            $this->resizeImage($path . $name, 150, $path . 'thumbnail/' . $name);
            $this->resizeImage($path . $name, 400, $path . 'medium/' . $name);
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
        File::delete('img/thumbnail/' . $informasi->gambar);
        File::delete('img/medium/' . $informasi->gambar);

        $informasi->delete();
        return response([
            'success' => true,
            'message' => 'Data has been deleted!',
            'data'   => $informasi
        ], 200);
    }
}
