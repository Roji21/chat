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
        return view('index');
    }
    public function chat(Request $request)
    {
        return view('chat');
    }
    public function newchat(Request $request)
    {
        return view('newchat');
    }

    // Menyimpan pesan ke database lewat AJAX POST
    public function kirim(Request $request)
    {
        DB::table('messages')->insert([
            'sender' => auth()->user()->number,
            'message_text' => $request->isi_pesan,
            'recipient' => $request->untuk_siapa,
            'sent_time' => now()->setTimezone('Asia/Jakarta')
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
            ->where(function ($query) use ($userAktif, $lawan) {
                $query->where('sender', $userAktif)
                    ->where('recipient', $lawan);
            })
            ->orWhere(function ($query) use ($userAktif, $lawan) {
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

            if ($pesan->sender == $userAktif) {
                // dd(pesan->id);
                $html .= '<div class="message-row saya">
                            <div class="bubble">' . e($pesan->message_text) . '<span class="time">' . $waktu . '</span></div>
                          </div>';
            } else if ($pesan->sender == $lawan) {
                // dd(pesan->id);
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
        // 1. Buat subquery untuk mencari ID pesan terakhir dari setiap lawan bicara
        $subQuery = DB::table('messages')
            ->select(DB::raw('MAX(id) as max_id'))
            ->where(function ($query) use ($userAktif) {
                $query->where('sender', '?')
                    ->orWhere('recipient', '?');
            })
            ->groupBy(DB::raw("IF(recipient = ?, sender, recipient)"))
            ->setBindings([$userAktif, $userAktif, $userAktif]); // Isi binding untuk 'sender', 'recipient', dan 'IF'

        // 2. Main query untuk mengambil data lengkap berdasarkan ID dari subquery di atas
        $chatList = DB::table('messages as m')
            ->joinSub($subQuery, 'dm', function ($join) {
                $join->on('m.id', '=', 'dm.max_id');
            })
            ->orderBy('m.sent_time', 'desc')
            ->get();
        $html = '';
        // dd(auth()->user()->number,$chatList);
        foreach ($chatList as $pesan) {
            $list = DB::table('messages')->where('id', $pesan->id)->get();
            // dd($list[0]->recipient, $userAktif);
            if ($userAktif != $list[0]->recipient) {
                $namelawan = DB::table('custom_users')->where('number', $list[0]->recipient)->first();
                // dd($namelawan);
                $html .= '<div class="chat-item active chat-click"  data-pengirim="' . e($list[0]->recipient) . '"  data-nama="' . e($namelawan->name) . '" data-foto="' . e($namelawan->photo) . '">
                <div class="avatar">';
                if ($namelawan->photo) {
                    $html .= '<img src="' . asset('storage/img/' . $namelawan->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                } else {
                    $html .= '👥';
                }
                $html .= '</div>';
                $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">' . e($namelawan->name) . '</span>
                        <span class="chat-time">' . e(
                    \Carbon\Carbon::parse($list[0]->sent_time)->isToday()
                        ? \Carbon\Carbon::parse($list[0]->sent_time)->format('H:i')
                        : (\Carbon\Carbon::parse($list[0]->sent_time)->isYesterday()
                            ? 'Yesterday'
                            : \Carbon\Carbon::parse($list[0]->sent_time)->format('d/m/Y'))
                ) .
                    '</span>
                    </div>
                    <div class="chat-preview">' . e($list[0]->message_text) . '</div>
                </div>
            </div>';
            } else {
                $namelawan = DB::table('custom_users')->where('number', $list[0]->sender)->first();
                // dd($namelawan);
                $html .= '<div class="chat-item active chat-click"  data-pengirim="' . e($list[0]->sender) . '"  data-nama="' . e($namelawan->name) . '" data-foto="' . e($namelawan->photo) . '">
                <div class="avatar">';
                if ($namelawan->photo) {
                    $html .= '<img src="' . asset('storage/img/' . $namelawan->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                } else {
                    $html .= '👥';
                }
                // dd($namelawan);
                $html .= '</div>';
                $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">' . e($namelawan->name) . '</span>
                        <span class="chat-time">' . e(
                    \Carbon\Carbon::parse($list[0]->sent_time)->isToday()
                        ? \Carbon\Carbon::parse($list[0]->sent_time)->format('H:i')
                        : (\Carbon\Carbon::parse($list[0]->sent_time)->isYesterday()
                            ? 'Yesterday'
                            : \Carbon\Carbon::parse($list[0]->sent_time)->format('d/m/Y'))
                ) .
                    '</span>
                    </div>
                    <div class="chat-preview">' . e($list[0]->message_text) . '</div>
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
        $html = '';
        $index = 0;
        $lanjut = 0;
        if ($filter) {
            $cari = DB::table('custom_users')->select(DB::raw('number'))->where('name', 'LIKE', '%' . $filter . '%')->get();
            foreach ($cari as $cari1) {
                $lanjut = 0;
                try {
                    $subQueryMaxId = DB::table('messages')
                        ->select(DB::raw('MAX(id) as id'))
                        ->where(function ($query) use ($userAktif, $cari1) {
                            $query->where('sender', $cari1->number)
                                ->where('recipient', $userAktif);
                        })
                        ->orWhere(function ($query) use ($userAktif, $cari1) {
                            $query->where('sender', $userAktif)
                                ->where('recipient', $cari1->number);
                        })->get();
                    $lanjut = 1;
                } catch (\Throwable $e) { // Menangkap segala jenis eror PHP / fatal crash lainnya
                    $lanjut = 0;
                }
                $hasilId = $subQueryMaxId[0]->id ?? null;
                if (is_null($hasilId) && $lanjut = 1) {
                    continue;
                } else {
                    $list = DB::table('messages')->where('id', $subQueryMaxId[0]->id)->get();
                    if ($cari1->number == $userAktif) {
                        continue; // Skip jika user yang dicari adalah user aktif
                    } else if ($userAktif != $list[0]->recipient) {
                        $namelawan = DB::table('custom_users')->where('number', $list[0]->recipient)->first();
                        $html .= '<div class="chat-item active chat-click"  data-pengirim="' . e($list[0]->recipient) . '"  data-nama="' . e($namelawan->name) . '" data-foto="' . e($namelawan->photo) . '">
                <div class="avatar">';
                        $html .= '<img src="' . asset('storage/img/' . $namelawan->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                        $html .= '</div>';
                        $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">' . e($namelawan->name) . '</span>
                        <span class="chat-time">' . e(
                            \Carbon\Carbon::parse($list[0]->sent_time)->isToday()
                                ? \Carbon\Carbon::parse($list[0]->sent_time)->format('H:i')
                                : (\Carbon\Carbon::parse($list[0]->sent_time)->isYesterday()
                                    ? 'Yesterday'
                                    : \Carbon\Carbon::parse($list[0]->sent_time)->format('d/m/Y'))
                        ) .
                            '</span>
                    </div>
                    <div class="chat-preview">' . e($list[0]->message_text) . '</div>
                </div>
            </div>';
                    } else {
                        $namelawan = DB::table('custom_users')->where('number', $list[0]->sender)->first();
                        $html .= '<div class="chat-item active chat-click"  data-pengirim="' . e($list[0]->sender) . '"  data-nama="' . e($namelawan->name) . '" data-foto="' . e($namelawan->photo) . '">
                <div class="avatar">';
                        $html .= '<img src="' . asset('storage/img/' . $namelawan->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                        $html .= '</div>';
                        $html .= '<div class="chat-info">
                    <div class="chat-name-row">
                        <span class="contact-name">' . e($namelawan->name) . '</span>
                        <span class="chat-time">' . e(
                            \Carbon\Carbon::parse($list[0]->sent_time)->isToday()
                                ? \Carbon\Carbon::parse($list[0]->sent_time)->format('H:i')
                                : (\Carbon\Carbon::parse($list[0]->sent_time)->isYesterday()
                                    ? 'Yesterday'
                                    : \Carbon\Carbon::parse($list[0]->sent_time)->format('d/m/Y'))
                        ) .
                            '</span>
                    </div>
                    <div class="chat-preview">' . e($list[0]->message_text) . '</div>
                </div>
            </div>';
                    }
                }
            }
            return response($html);
        }
    }
    public function searchnew(Request $request) {
        $filter = $request->query('filter');
        $userAktif = auth()->user()->number;
        $subQuery = DB::table('contacts')
            ->where(function ($query) use ($userAktif, $filter) {
                $query->where('user_number', $userAktif)
                    ->where('custom_name','LIKE', '%' . $filter . '%');
            })
            ->orWhere(function ($query) use ($userAktif, $filter) {
                $query->where('user_number', $userAktif)
                    ->where('contact_number','LIKE', '%' . $filter . '%');
            })->get();
        $html = '';
        $hurufAwal = '';
        foreach ($subQuery as $contact) {
            $data = DB::table('custom_users')->where('number', $contact->contact_number)->first();
            // dd($data);
            $html .= '<div class="contact-item chat-click" <div class="chat-item active chat-click"  data-lawan="' . e($data->number) . '"  data-nama="' . e($contact->custom_name) . '" data-foto="' . e($data->photo) . '" page="notification">
                        <div class="icon"><img src="' . asset('storage/img/' . $data->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
                        <div class="contact-info">
                            <div class="contact-name-row">
                                <div class="contact-name">' . e($contact->custom_name) . '</div>
                            </div>
                        </div>
                    </div>';
        }
        return response($html);
    }

    public function listcontact(Request $request)
    {
        $userAktif = auth()->user()->number;
        $subQuery = DB::table('contacts')->where('user_number', $userAktif)->get();
        // dd($subQuery, $userAktif);
        $html = '';
        $hurufAwal = '';
        $html .= '<div class="contact-item"  page="profile">
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
            // dd($contact->custom_name,$huruf_pertama);
            if ($huruf_pertama !== $hurufAwal) {
                $html .= '<div class="contact-header">
                            <div class="contact-info">
                                <div class="contact-header-name">' . e($huruf_pertama) . '</div>
                            </div>
                        </div>';
                $hurufAwal = $huruf_pertama;
            }
            $data = DB::table('custom_users')->where('number', $contact->contact_number)->first();
            // dd($data);
            $html .= '<div class="contact-item chat-click" <div class="chat-item active chat-click"  data-lawan="' . e($data->number) . '"  data-nama="' . e($contact->custom_name) . '" data-foto="' . e($data->photo) . '" page="notification">
                        <div class="icon"><img src="' . asset('storage/img/' . $data->photo) . '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
                        <div class="contact-info">
                            <div class="contact-name-row">
                                <div class="contact-name">' . e($contact->custom_name) . '</div>
                            </div>
                        </div>
                    </div>';
        }
        return response($html);
    }
}
