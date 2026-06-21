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
        $user = session('user_id'); 
        $iduser = DB::table('user')->where('id_user', $user)->first();
        $id = $iduser->id_user;
        $userSekarang = $iduser->nama;
        $riwayatPesan = DB::table('pesan')->where('pengirim',$user)->get();
        $lawanBicara = '';
        $foto= $iduser->foto ;
        $fotolawan = 'default.jpeg';
        return view('setting', compact('id','userSekarang', 'lawanBicara','riwayatPesan', 'foto', 'fotolawan'));
    }
    public function upfoto(Request $request)
    {
    $request->validate([
        'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
    ]);
    $userId = session('user_id');
    $nama = db::table('user')->where('id_user', $userId)->first();
    if ($request->hasFile('foto_profil')) {
        $file = $request->file('foto_profil');

        $namaFile = 'user_' . $nama->nama . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        $tujuanFolder = public_path('storage/img');
        // if ($nama && $nama->foto) {
        //         // Gabungkan jalur folder dengan nama file lama yang ada di database
        //         $jalurFotoLama = $tujuanFolder . '/' . $nama->foto;

        //         // Cek apakah file lama tersebut benar-benar ada secara fisik di folder public
        //         if (File::exists($jalurFotoLama)) {
        //             File::delete($jalurFotoLama); // Hapus file lama dari penyimpanan
        //         }
        // }
        $file->move($tujuanFolder, $namaFile);

        $userId = session('user_id');
        DB::table('user')
            ->where('id_user', $userId)
            ->update(['foto' => $namaFile]);
        
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diperbarui.',
                'path' => asset('storage/img/' . $namaFile)
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

        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }

        // 3. Tentukan kolom database berdasarkan ID input dari JavaScript
        if ($request->tipe_input === 'input-name') {
            db::table('user')->where('id_user', $userId)->update(['nama' => $request->nilai_teks]);    
        } elseif ($request->tipe_input === 'input-about') {
            db::table('user')->where('id_user', $userId)->update(['about' => $request->nilai_teks]);
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
        $user = session('user_id'); 
        $iduser = DB::table('user')->where('id_user', $user)->first();
        $id = $iduser->id_user;
        $userSekarang = $iduser->nama;
        $foto= $iduser->foto ;
        $about = $iduser->about;
        return view('profile', compact('id','userSekarang','foto','about'));
    }
    public function notification(Request $request)
    {
        
    }
    public function account(Request $request)
    {
        
    }

}
