    <!-- ================= SISI KIRI: DAFTAR CHAT & SEARCH ================= -->
    <div class="side-panel">
        <!-- Bagian Atas Sisi Kiri: Info Profil Saya -->
        <div class="side-header">
            <div class="avatar" id="ava_id"><img src="{{ auth()->user()->photo ? asset('img/' . auth()->user()->photo) : asset('storage/img/default.jpeg') }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
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
        <div class="chat-list" id="list">
            <!-- List akan dimuat di sini melalui JavaScript -->
        </div>
    </div>

    <!-- ================= SISI KANAN: RUANG OBROLAN UTAMA ================= -->
    <div class="main-chat-area">
        <div class="chat-header" id="chat-header">
            <div class="avatar" id="ava-id"><img src="{{ asset('storage/img/default.jpeg') }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
            <div id="nama-lawan-bicara" class="nama-lawan">💬</div>
        </div>

        <div class="chat-messages" id="box_pesan">
            <!-- Pesan akan dimuat di sini melalui JavaScript -->
        </div>

        <div class="chat-input-area">
            <input type="text" id="isi_pesan" placeholder="Ketik pesan..." autocomplete="off">
            <button id="btn_kirim">Kirim</button>
        </div>
    </div>
    <div class="profile-area" id="profile-area">
        <div class="profile-header">
            <h2>Profil Saya</h2>
            <button id="close-profile">✖</button>
        </div>
        <div class="profile-content">
            <div class="profile-avatar">
                <img src="{{ auth()->user()->photo ? asset('storage/img/' . auth()->user()->photo) : asset('storage/img/default.jpeg') }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            </div>
            <div class="profile-info">
                <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                <p><strong>Username:</strong> {{ auth()->user()->username }}</p>
                <!-- Tambahkan informasi lain yang relevan -->
            </div>
        </div>
    </div>
    <form id="logout-form" action="/logout" method="POST" style="display: none;">
        @csrf
    </form>