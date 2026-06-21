    <!-- ================= SISI KIRI: DAFTAR CHAT & SEARCH ================= -->
    <div class="side-panel">
        <!-- Bagian Atas Sisi Kiri: Info Profil Saya -->
        <div class="side-header">
            <div class="avatar" id="ava_id"><img src="{{ asset('storage/img/'.$foto) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
            <div class="menu-user-container">
                <div class="id-saya" onclick="toggleMenu(event)">ID: {{ auth()->user()->name }} ▾</div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="#" onclick="konfirmasiLogout(event)">Logout Account</a>
                </div>
            </div>
        </div>

        <!-- Kolom Pencarian / Search Bar -->
        <div class="search-box-container">
            <div class="search-input-wrapper">
                <span class="search-icon">🔍</span>
                <!-- Tambahkan atribut oninput -->
                <input type="text" id="search_user" oninput="filterUser()" placeholder="Cari user...">
             </div>
        </div>

        <!-- Daftar Pengguna Lain yang Di-chat -->
        <div class="chat-list" id="list" >
                <!-- List akan dimuat di sini melalui JavaScript -->
        </div>
    </div>

    <!-- ================= SISI KANAN: RUANG OBROLAN UTAMA ================= -->
    <div class="main-chat-area">
        <div class="chat-header">
            <div class="avatar" id="ava-id"><img src="{{ asset('storage/img/'.$fotolawan) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
            <div id="nama-lawan-bicara" class="nama-lawan">💬 {{ $lawanBicara }}</div>
        </div>

        <div class="chat-messages" id="box_pesan">
            <!-- Pesan akan dimuat di sini melalui JavaScript -->
        </div>

        <div class="chat-input-area">
            <input type="text" id="isi_pesan" placeholder="Ketik pesan..." autocomplete="off">
            <button id="btn_kirim">Kirim</button>
        </div>
    </div>
<form id="logout-form" action="/logout" method="POST" style="display: none;">
    @csrf
</form>
</div>

