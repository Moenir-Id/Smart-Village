<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function index()
    {
        $jenisSurat = JenisSurat::orderBy('urutan')->get();
        return view('admin.jenis-surat.index', compact('jenisSurat'));
    }

    public function create()
    {
        return view('admin.jenis-surat.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'         => ['required', 'string', 'max:255', 'unique:jenis_surat,nama'],
            'persyaratan'  => ['nullable', 'string'],
            'aktif'        => ['boolean'],
            'urutan'       => ['integer', 'min:0'],
        ]);

        JenisSurat::create($data);
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function edit(JenisSurat $jenisSurat)
    {
        return view('admin.jenis-surat.form', ['jenisSurat' => $jenisSurat]);
    }

    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $data = $request->validate([
            'nama'        => ['required', 'string', 'max:255', 'unique:jenis_surat,nama,' . $jenisSurat->id],
            'persyaratan' => ['nullable', 'string'],
            'aktif'       => ['boolean'],
            'urutan'      => ['integer', 'min:0'],
        ]);

        $jenisSurat->update($data);
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat)
    {
        if ($jenisSurat->permohonan()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus jenis surat yang sudah memiliki permohonan.');
        }
        $jenisSurat->delete();
        return back()->with('success', 'Jenis surat berhasil dihapus.');
    }
}
