<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $gallery = Gallery::latest()->get();
        $data = collect($gallery)->map(function ($item) {
            return collect($item)->merge(['gambar' => env('BASE_URL') . 'img/gallery/' . $item->gambar]);
        });

        return response([
            'success' => true,
            'message' => 'Gallery list',
            'count' => $gallery->count(),
            'data'   => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'deskripsi' => 'required',
            'gambar' => 'required|max:10240|mimes:jpg,png,jpeg|image'
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation error!',
                'data'   => $validation->errors()
            ], 422);
        }

        if ($request->hasFile('gambar')) {
            try {
                $file = $request->file('gambar')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $name = Str::slug($filename) . '-' . time() . '.' . $extension;
                $request->file('gambar')->move('img/gallery/', $name);
            } catch (\Throwable $err) {
                return $err;
            }
        }

        $gallery = Gallery::create([
            'deskripsi' => $request->deskripsi,
            'gambar' => $name,
        ]);

        return response([
            'success' => true,
            'message' => 'Gallery has been added!',
            'data'   => $gallery
        ], 200);
    }

    public function show($id)
    {
        $gallery = Gallery::findOrFail($id);
        return response([
            'success' => true,
            'message' => 'Show Gallery',
            'data'   => $gallery
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'deskripsi' => 'required',
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
            File::delete('img/gallery/' . $gallery->gambar);

            $file = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $name = Str::slug($filename) . '-' . time() . '.' . $extension;
            $request->file('gambar')->move('img/gallery/', $name);
        } else {
            $name = $gallery->gambar;
        }

        $gallery->update([
            'deskripsi' => $request->deskripsi,
            'gambar' => $name,
        ]);

        return response([
            'success' => true,
            'message' => 'Gallery has been updated!',
            'data'   => $gallery
        ], 200);
    }

    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->delete();
        return response([
            'success' => true,
            'message' => 'Gallery has been deleted!',
            'data'   => $gallery
        ], 200);
    }
}
