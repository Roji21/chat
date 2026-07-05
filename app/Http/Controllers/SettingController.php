<?php

namespace App\Http\Controllers;

use App\Events\PesanTerkirim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // Menampilkan halaman utama
    public function index(Request $request)
    {
        return view('setting');
    }
    public function upfoto(Request $request)
    {
    $request->validate([
        'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
    ]);
    $userId = auth()->user()->number;
    $nama = db::table('custom_users')->where('number', $userId)->first();
    if ($request->hasFile('foto_profil')) {
        $file = $request->file('foto_profil');

        $namaFile = 'user_' . $nama->name . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        $tujuanFolder = public_path('storage/img');
        
        $file->move($tujuanFolder, $namaFile);
        DB::table('custom_users')
            ->where('number', $userId)
            ->update(['photo' => $namaFile]);
        
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diperbarui.',
                'path' => asset('img/' . $namaFile)
            ]);
        }

    return response()->json([
        'success' => false,
        'message' => 'Tidak ada file yang diunggah.'
    ], 400);
    }
    public function update(Request $request)
    {
        $request->validate([
            'tipe_input' => 'required|string|in:input-name,input-about',
            'nilai_teks' => 'required|string|max:255',
        ]);

        $userId = auth()->user()->number;
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }

        // 3. Tentukan kolom database berdasarkan ID input dari JavaScript
        if ($request->tipe_input === 'input-name') {
            db::table('custom_users')->where('number', $userId)->update(['name' => $request->nilai_teks]);    
        } elseif ($request->tipe_input === 'input-about') {
            db::table('custom_user')->where('number', $userId)->update(['about' => $request->nilai_teks]);
        } else {
            return response()->json(['success' => false, 'message' => 'Tipe input tidak valid.'], 400);    
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui di database.'
        ]);
    }
    public function profile(Request $request)
    {
        return view('profile');
    }
    public function notification(Request $request)
    {
        
    }
    public function account(Request $request)
    {
        
    }

}
