<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::latest()->get();
        return response([
            'success' => true,
            'message' => 'Information Category list',
            'count' => $kategori->count(),
            'data'   => $kategori
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'kategori' => 'required|unique:kategori',
        ]);

        if ($validation->fails()) {
            return response([
                'success' => false,
                'message' => 'Validation error!',
                'data'   => $validation->errors()
            ], 422);
        }

        $kategori = Kategori::create([
            'kategori' => $request->kategori
        ]);

        return response([
            'success' => true,
            'message' => 'Category has been added!',
            'data'   => $kategori
        ], 200);
    }

    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);
        return response([
            'success' => true,
            'message' => 'Show Kategori',
            'data'   => $kategori
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $validation = Validator::make($request->all(), [
            'kategori' => 'required|unique:kategori',
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
            'message' => 'Category has been updated!',
            'data'   => $kategori
        ], 200);
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        return response([
            'success' => true,
            'message' => 'Category has been deleted!',
            'data'   => $kategori
        ], 200);
    }
}
