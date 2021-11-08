<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::latest()->get();
        return response([
            'success' => true,
            'message' => 'Staff list',
            'count' => $staff->count(),
            'data'   => $staff
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nama' => 'required',
            'jabatan' => 'required',
            'phone' => 'required',
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
            $request->file('gambar')->move('img/staff/', $name);
        }

        $staff = Staff::create([
            'name' => $request->nama,
            'jabatan' => $request->jabatan,
            'phone' => $request->phone,
            'gambar' => $name,
        ]);

        return response([
            'success' => true,
            'message' => 'Staff has been added!',
            'data'   => $staff
        ], 200);
    }

    public function show($id)
    {
        $staff = Staff::findOrFail($id);
        return response([
            'success' => true,
            'message' => 'Show Staff',
            'data'   => $staff
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'nama' => 'required',
            'jabatan' => 'required',
            'phone' => 'required',
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
            File::delete('img/staff/' . $staff->gambar);

            $file = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $name = Str::slug($filename) . '-' . time() . '.' . $extension;

            $request->file('gambar')->move('img/staff/', $name);
        } else {
            $name = $staff->gambar;
        }

        $staff->update([
            'name' => $request->nama,
            'jabatan' => $request->jabatan,
            'phone' => $request->phone,
            'gambar' => $name,
        ]);

        return response([
            'success' => true,
            'message' => 'Staff has been updated!',
            'data'   => $staff
        ], 200);
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        File::delete('img/staff/' . $staff->gambar);

        $staff->delete();
        return response([
            'success' => true,
            'message' => 'Data has been deleted!',
            'data'   => $staff
        ], 200);
    }
}
