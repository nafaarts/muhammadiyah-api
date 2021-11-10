<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Gumlet\ImageResize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

class GalleryController extends Controller
{
    private function resizeImage($filename, $size, $output)
    {
        $image = new ImageResize($filename);
        $image->resizeToWidth($size, true);
        $image->save($output, IMAGETYPE_JPEG);
    }

    public function index()
    {
        $gallery = Gallery::latest()->get();
        $data = collect($gallery)->map(function ($item) {
            return collect($item)->merge(['gambar' => [
                'original' => env('BASE_URL') . 'img/gallery/' . $item->gambar,
                'medium' => env('BASE_URL') . 'img/gallery/medium/' . $item->gambar,
                'thumbnail' => env('BASE_URL') . 'img/gallery/thumbnail/' . $item->gambar
            ]]);
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
                $path = 'img/gallery/';
                $request->file('gambar')->move($path, $name);
                $this->resizeImage($path . $name, 150, $path . 'thumbnail/' . $name);
                $this->resizeImage($path . $name, 400, $path . 'medium/' . $name);
            } catch (\Throwable $err) {
                return $err;
            }
        }

        $gallery = Gallery::create([
            'deskripsi' => $request->deskripsi,
            'gambar' => $name,
        ]);

        $data = collect($gallery)->merge(['gambar' => [
            'original' => env('BASE_URL') . 'img/gallery/' . $gallery->gambar,
            'medium' => env('BASE_URL') . 'img/gallery/medium/' . $gallery->gambar,
            'thumbnail' => env('BASE_URL') . 'img/gallery/thumbnail/' . $gallery->gambar
        ]]);

        return response([
            'success' => true,
            'message' => 'Gallery has been added!',
            'data'   => $data
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

        $data = collect($gallery)->merge(['gambar' => [
            'original' => env('BASE_URL') . 'img/gallery/' . $gallery->gambar,
            'medium' => env('BASE_URL') . 'img/gallery/medium/' . $gallery->gambar,
            'thumbnail' => env('BASE_URL') . 'img/gallery/thumbnail/' . $gallery->gambar
        ]]);

        return response([
            'success' => true,
            'message' => 'Gallery has been updated!',
            'data'   => $data
        ], 200);
    }

    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        File::delete('img/gallery/' . $gallery->gambar);
        File::delete('img/gallery/medium/' . $gallery->gambar);
        File::delete('img/gallery/thumbnail/' . $gallery->gambar);
        $gallery->delete();
        return response([
            'success' => true,
            'message' => 'Gallery has been deleted!',
            'data'   => $gallery
        ], 200);
    }
}
