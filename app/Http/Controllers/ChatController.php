<?php

namespace App\Http\Controllers;

use App\Events\PesanTerkirim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
//sudo systemctl stop mysql
//sudo killall -9 mysqld
//sudo /opt/lampp/lampp restart

class ChatController extends Controller
{
    // Menampilkan halaman utama
    public function index(Request $request)
    {
        $user = auth()->user()->number; 
        $iduser = DB::table('custom_users')->where('number', $user)->first();
        $id = $iduser->number;
        $userSekarang = $iduser->name;
        $riwayatPesan = DB::table('messages')->where('recipient',$user)->get();
        $lawanBicara = '';
        $foto= $iduser->photo ;
        $fotolawan = 'default.jpeg';
        // dd($foto,$riwayatPesan);
        return view('index', compact('id','userSekarang', 'lawanBicara','riwayatPesan', 'foto', 'fotolawan'));
    
    }
    public function chat(Request $request)
    {
        $user = auth()->user()->number; 
        $iduser = DB::table('custom_users')->where('number', $user)->first();
        $id = $iduser->number;
        $userSekarang = $iduser->name;
        $riwayatPesan = DB::table('messages')->where('recipient',$user)->get();
        $lawanBicara = '';
        $foto= $iduser->photo ;
        $fotolawan = 'default.jpeg';
        // dd($iduser,$riwayatPesan);
        return view('chat', compact('id','userSekarang', 'lawanBicara','riwayatPesan', 'foto', 'fotolawan'));
    }
    public function newchat(Request $request)
    {
        $user = session('user_id'); 
        $iduser = DB::table('user')->where('id_user', $user)->first();
        $id = $iduser->id_user;
        $userSekarang = $iduser->nama;
        $riwayatPesan = DB::table('pesan')->where('pengirim',$user)->get();
        $lawanBicara = '';
        $foto= $iduser->foto ;
        $fotolawan = 'default.jpeg';
        return view('newchat', compact('id','userSekarang', 'lawanBicara','riwayatPesan', 'foto', 'fotolawan'));
    }

    // Menyimpan pesan ke database lewat AJAX POST
    public function kirim(Request $request)
    {
        DB::table('messages')->insert([
            'sender' => $request->dari_siapa,
            'message_text' => $request->isi_pesan,
            'recipient' => $request->untuk_siapa,
            'sent_time' => now(),
        ]);

        return response()->json(['status' => 'Sukses']);
    }

    // Mengambil komponen HTML chat lewat AJAX GET
    public function ambilData(Request $request)
    {
        $userAktif = auth()->user()->number;
        $lawan = $request->query('lawan_bicara');
        $page = $request->query('page', 1); // Default halaman 1
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $html = '';

        // Jika parameter lawan bicara kosong, gagalkan proses
        if (!$lawan) {
            $html = "<p style='color:gray; text-align:center; font-size:12px;'>Lawan bicara tidak ditentukan.</p>";
            return response($html);
        }

        $allPesan = DB::table('messages')
            ->where(function($query) use ($userAktif, $lawan) {
                $query->where('sender', $userAktif)
                    ->where('recipient', $lawan);
            })
            ->orWhere(function($query) use ($userAktif, $lawan) {
                $query->where('sender', $lawan)
                    ->where('recipient', $userAktif);
            })
            ->orderBy('id', 'desc') // Ambil chat paling baru dulu
            ->skip($offset)
            ->take($limit)
            ->get()
            ->reverse(); // Balikkan agar chat lama di atas, chat baru di bawah
        // dd( $allPesan);
        foreach ($allPesan as $pesan) {
            $waktu = date('H:i', strtotime($pesan->sent_time));

            if ($pesan->recipient == $userAktif) {
                $html .= '<div class="message-row saya">
                            <div class="bubble">' . e($pesan->message_text) . '<span class="time">' . $waktu . '</span></div>
                        </div>';
            } else if($pesan->recipient == $lawan) {
                $html .= '<div class="message-row bukan-saya">
                            <div class="bubble">' . e($pesan->message_text) . '<span class="time">' . $waktu . '</span></div>
                        </div>';
            }
        }

        // Jika halaman 1 kosong, tampilkan pesan belum ada obrolan
        if ($allPesan->isEmpty() && $page == 1) {
            $html = "<p style='color:gray; text-align:center; font-size:12px;'>Belum ada obrolan.</p>";
        }
        return response($html);
    }

    public function listpesan(Request $request)
    {
        $userAktif = auth()->user()->number;
        $subQueryMaxId = DB::table('messages')
            ->select(DB::raw('MAX(id) as id'))
            ->where('recipient', $userAktif)
            ->orWhere('sender', $userAktif)
            ->groupBy(DB::raw("IF(recipient = '$userAktif', sender, recipient)"))->get();
        $html = '';
        // dd(auth()->user());
        foreach ($subQueryMaxId as $pesan) {
            $list = DB::table('messages')->where('id',$pesan->id)->get();
            // dd($list[0]->recipient, $userAktif);
            if($userAktif!=$list[0]->recipient){
                $namelawan = DB::table('custom_users')->where('number', $list[0]->recipient)->first();
                // dd($namelawan);
                $html .= '<div class="chat-item active chat-click"  data-pengirim="'.e($list[0]->recipient).'"  data-nama="'.e($namelawan->name).'" data-foto="'.e($namelawan->photo).'">
                <div class="avatar">';
                if ($namelawan->photo) {
                    $html .= '<img src="' . asset('storage/img/'. $namelawan->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                } else {
                    $html .= '👥'; 
                }
                $html .= '</div>';
                $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">'.e($namelawan->name).'</span>
                        <span class="chat-time">'.e($list[0]->sent_time).'</span>
                    </div>
                    <div class="chat-preview">'.e($list[0]->message_text).'</div>
                </div>
            </div>';
            }else {
                $namelawan = DB::table('custom_users')->where('number', $list[0]->sender)->first();
                // dd($namelawan);
                $html .= '<div class="chat-item active chat-click"  data-pengirim="'.e($list[0]->sender).'"  data-nama="'.e($namelawan->name).'" data-foto="'.e($namelawan->photo).'">
                <div class="avatar">';
                if ($namelawan->photo) {
                    $html .= '<img src="' . asset('storage/img/'. $namelawan->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                } else {
                    $html .= '👥'; 
                }
                // dd($namelawan);
                $html .= '</div>';
                $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">'.e($namelawan->name).'</span>
                        <span class="chat-time">'.e($list[0]->sent_time).'</span>
                    </div>
                    <div class="chat-preview">'.e($list[0]->message_text).'</div>
                </div>
            </div>';
            }
        }
        return response($html);
    }
    public function listfilter(Request $request)
    {
        $userAktif = $request->query('user_aktif');
        $filter = $request->query('filter');
        $subQueryMaxId;
        if(!$filter){
            $subQueryMaxId = DB::table('pesan')
                ->select(DB::raw('MAX(id) as id'))
                ->where('pengirim', $userAktif)
                ->orWhere('penerima', $userAktif)
                ->groupBy(DB::raw("IF(pengirim = '$userAktif', penerima, pengirim)"))->get();
        } else {
            $cari = DB::table('user')->where('nama', 'LIKE', '%' . $filter . '%')->get();
            // dd($cari, $filter);
            $html = '';
        foreach($cari as $cari1){
            $subQueryMaxId = DB::table('pesan')
                ->select(DB::raw('MAX(id) as id'))
                ->where(function($query) use ($userAktif) {
                    $query->where('pengirim', $userAktif)
                        ->orWhere('penerima', $userAktif);
                })
                ->where('pengirim', 'LIKE', '%' . $cari1->id_user . '%')
                ->orWhere('penerima', 'LIKE', '%' . $cari1->id_user . '%')
                ->groupBy(DB::raw("IF(pengirim = '$userAktif', penerima, pengirim)"))
                ->get();
            // dd($subQueryMaxId);
            $list = DB::table('pesan')->where('id',$subQueryMaxId[0]->id)->get();
            // dd($list);
            if ($cari1->id_user == $userAktif) {
                continue; // Skip jika user yang dicari adalah user aktif
            }else if($userAktif!=$list[0]->penerima){
                $namelawan = DB::table('user')->where('id_user', $list[0]->penerima)->first();
                $html .= '<div class="chat-item active chat-click"  data-pengirim="'.e($list[0]->penerima).'"  data-nama="'.e($namelawan->nama).'" data-foto="'.e($namelawan->foto).'">
                <div class="avatar">';
                if ($namelawan->foto) {
                    $html .= '<img src="' . asset('storage/img/'. $namelawan->foto) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                } else {
                    $html .= '👥'; 
                }
                $html .= '</div>';
                $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">'.e($namelawan->nama).'</span>
                        <span class="chat-time">'.e($list[0]->waktu_kirim).'</span>
                    </div>
                    <div class="chat-preview">'.e($list[0]->isi_pesan).'</div>
                </div>
            </div>';
            }else {
                $namelawan = DB::table('user')->where('id_user', $list[0]->pengirim)->first();
                $html .= '<div class="chat-item active chat-click"  data-pengirim="'.e($list[0]->pengirim).'"  data-nama="'.e($namelawan->nama).'" data-foto="'.e($namelawan->foto).'">
                <div class="avatar">';
                if ($namelawan->foto) {
                    $html .= '<img src="' . asset('storage/img/'. $namelawan->foto) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                } else {
                    $html .= '👥'; 
                }
                $html .= '</div>';
                $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">'.e($namelawan->nama).'</span>
                        <span class="chat-time">'.e($list[0]->waktu_kirim).'</span>
                    </div>
                    <div class="chat-preview">'.e($list[0]->isi_pesan).'</div>
                </div>
            </div>';
            }
            }
        }
        return response($html);
    }
    public function searchnew(Request $request)
    {
        
    }

    public function listcontact(Request $request)
    {
        $userAktif = session('user_id');
        $subQuery = DB::table('contacts')->where('uid', $userAktif)->get();
            // dd($subQuery, $userAktif);
        $html = '';
        $hurufAwal = '';
        $html .='<div class="contact-item"  page="profile">
                    <div class="icon"><svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.5 19.5H14.5" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16.5 21.5V17.5" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.16 10.87C12.06 10.86 11.94 10.86 11.83 10.87C9.44997 10.79 7.55997 8.84 7.55997 6.44C7.54997 3.99 9.53997 2 11.99 2C14.44 2 16.43 3.99 16.43 6.44C16.43 8.84 14.53 10.79 12.16 10.87Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11.99 21.8101C10.17 21.8101 8.36004 21.3501 6.98004 20.4301C4.56004 18.8101 4.56004 16.1701 6.98004 14.5601C9.73004 12.7201 14.24 12.7201 16.99 14.5601" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg></div>
                    <div class="contact-info">
                        <div class="contact-name-row">
                            <div class="contact-name">New Contact</div>
                        </div>
                    </div>
                </div>
                <div class="contact-item"  page="notification">
                    <div class="icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="9" cy="9" r="3" stroke="#33363F" stroke-width="2" stroke-linecap="round"></circle> <path d="M12.2679 9C12.5332 8.54063 12.97 8.20543 13.4824 8.06815C13.9947 7.93086 14.5406 8.00273 15 8.26795C15.4594 8.53317 15.7946 8.97 15.9319 9.48236C16.0691 9.99472 15.9973 10.5406 15.7321 11C15.4668 11.4594 15.03 11.7946 14.5176 11.9319C14.0053 12.0691 13.4594 11.9973 13 11.7321C12.5406 11.4668 12.2054 11.03 12.0681 10.5176C11.9309 10.0053 12.0027 9.45937 12.2679 9L12.2679 9Z" stroke="#33363F" stroke-width="2"></path> <path d="M13.8816 19L12.9013 19.1974L13.0629 20H13.8816V19ZM17.7202 17.9042L18.6627 17.5699L17.7202 17.9042ZM11.7808 15.7105L11.176 14.9142L10.0194 15.7927L11.2527 16.5597L11.7808 15.7105ZM16.8672 18H13.8816V20H16.8672V18ZM16.7777 18.2384C16.7707 18.2186 16.7642 18.181 16.7725 18.1354C16.7804 18.0921 16.7982 18.0593 16.8151 18.0383C16.8474 17.9982 16.874 18 16.8672 18V20C18.0132 20 19.1414 18.9194 18.6627 17.5699L16.7777 18.2384ZM14 16C15.6416 16 16.4027 17.1811 16.7777 18.2384L18.6627 17.5699C18.1976 16.2588 16.9485 14 14 14V16ZM12.3857 16.5069C12.7702 16.2148 13.282 16 14 16V14C12.8381 14 11.9028 14.3622 11.176 14.9142L12.3857 16.5069ZM11.2527 16.5597C12.2918 17.206 12.7271 18.3324 12.9013 19.1974L14.8619 18.8026C14.644 17.7204 14.0374 15.9364 12.309 14.8614L11.2527 16.5597Z" fill="#33363F"></path> <path d="M9 15C12.5715 15 13.5919 17.5512 13.8834 19.0089C13.9917 19.5504 13.5523 20 13 20H5C4.44772 20 4.00829 19.5504 4.11659 19.0089C4.4081 17.5512 5.42846 15 9 15Z" stroke="#33363F" stroke-width="2" stroke-linecap="round"></path> <path d="M19 3V7" stroke="#33363F" stroke-width="2" stroke-linecap="round"></path> <path d="M21 5L17 5" stroke="#33363F" stroke-width="2" stroke-linecap="round"></path> </g></svg></div>
                    <div class="contact-info">
                        <div class="contact-name-row">
                            <div class="contact-name">New Group</div>
                        </div>
                    </div>
                </div>';
        foreach ($subQuery as $contact) {
            $huruf_pertama = strtoupper(substr($contact->custom_name, 0, 1));
            if ($huruf_pertama !== $hurufAwal) {
                $html .= '<div class="contact-header">
                            <div class="contact-info">
                                <div class="contact-header-name">'. e($huruf_pertama) . '</div>
                            </div>
                        </div>';
                $hurufAwal = $huruf_pertama;
            }
            $data = DB::table('user')->where('id_user', $contact->contact_uid)->first();
            $html .= '<div class="contact-item"  page="notification">
                        <div class="icon"><img src="' . asset('storage/img/'. $data->foto) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
                        <div class="contact-info">
                            <div class="contact-name-row">
                                <div class="contact-name">'.e($data->nama).'</div>
                            </div>
                        </div>
                    </div>';
        }
        return response($html);
    }
}
