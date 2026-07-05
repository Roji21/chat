<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
/* ---------------- CSS SISI KIRI ---------------- */
.side-panel { background-color: #ffffff; display: flex; flex-direction: column; border-right: 1px solid #111111; }
.side-header { background-color: #f0f2f5; height: 60px; padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; }
.profile-pic { font-size: 24px; cursor: pointer; }

/* Kode Anda: ID Saya & Dropdown dipindah ke sisi kiri */
.side-header .id-saya { background: #008069; color: white; padding: 6px 10px; border-radius: 5px; font-size: 12px; cursor: pointer; user-select: none; }
.menu-user-container { position: relative; }
.dropdown-menu { display: none; position: absolute; right: 0; top: 32px; background-color: white; min-width: 140px; box-shadow: 0px 4px 12px rgba(0,0,0,0.15); border-radius: 6px; z-index: 999; overflow: hidden; }
.dropdown-menu a { color: #333; padding: 10px 15px; text-decoration: none; display: block; font-size: 13px; }
.dropdown-menu a:hover { background-color: #ffebe9; color: #cf222e; }

/* Bagian Search Box */
.search-box-container { padding: 8px 12px; background-color: #fff; border-bottom: 1px solid #f0f2f5; }
.search-input-wrapper { background-color: #f0f2f5; border-radius: 8px; padding: 6px 12px; display: flex; align-items: center; gap: 8px; }
.search-icon { color: #667781; font-size: 14px; }
.search-input-wrapper input { background: transparent; border: none; outline: none; width: 100%; font-size: 14px; color: #111b21; }

/* Daftar List Obrolan */
.setting-list { flex: 1; overflow-y: auto; background-color: #fff; overflow: hidden;}
.setting-item { display: flex; padding: 12px; gap: 12px; cursor: pointer; align-items: center; border-bottom: 1px solid #f0f2f5; transition: background 0.2s; }
.setting-item:hover { background-color: #f0f2f5; }
.setting-item.active { background-color: #eae6df; }
.avatar { width: 45px; height: 45px; background-color: #dfe5e7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.icon { width: 30px; height: 30px;  border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.setting-info { flex: 1; display: flex; flex-direction: column; gap: 4px; overflow: hidden; }
.setting-name-row { display: flex; justify-content: space-between; align-items: center; }
.setting-name { font-weight: 500; color: #111b21; font-size: 15px; }

/* ---------------- CSS SISI KANAN ---------------- */
.main-chat-area { display: flex; flex-direction: column; background: #efeae2; overflow: hidden; }
.chat-header { background: #008069; color: white; height: 60px; padding: 10px 10px; display: flex;  justify-content: left; align-items: flex-start; }
.chat-header .nama-lawan { font-weight: bold; font-size: 26px; padding-left: 10px; padding-top: 4px; }
.chat-messages { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
.message-row { display: flex; width: 100%; }
.message-row.bukan-saya { justify-content: flex-start; }
.message-row.saya { justify-content: flex-end; }
.bubble { max-width: 65%; padding: 8px 12px; border-radius: 8px; font-size: 14px; position: relative; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
.message-row.bukan-saya .bubble { background: #ffffff; border-top-left-radius: 0; }
.message-row.saya .bubble { background: #d9fdd3; border-top-right-radius: 0; }
.time { font-size: 10px; color: #667781; text-align: right; margin-top: 4px; display: block; }
.chat-input-area { background: #f0f2f5; padding: 10px; display: flex; gap: 10px; }
.chat-input-area input { flex: 1; padding: 10px; border: none; border-radius: 8px; outline: none; }
.chat-input-area button { background: #008069; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
    
    <!-- ================= SISI KIRI ================= -->
    <div class="side-panel">
        <!-- Bagian Atas Sisi Kiri: Info Profil Saya -->
        <div class="side-header">
            <div class="avatar" id="ava_id"><img src="{{ asset('storage/img/' . $foto) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"></div>
            <div class="menu-user-container">
                <div class="id-saya" onclick="toggleMenu(event)">ID: {{ $userSekarang }} ▾</div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="#" onclick="konfirmasiLogout(event)">Logout Account</a>
                </div>
            </div>
        </div>

        <div class="setting-list" id="list" >
            <div class="setting-item"  page="profile">
                <div class="icon"><svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="style=fill">
                    <g id="profile">
                    <path id="vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M6.75 6.5C6.75 3.6005 9.1005 1.25 12 1.25C14.8995 1.25 17.25 3.6005 17.25 6.5C17.25 9.3995 14.8995 11.75 12 11.75C9.1005 11.75 6.75 9.3995 6.75 6.5Z" fill="#000000"/>
                    <path id="rec (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M4.25 18.5714C4.25 15.6325 6.63249 13.25 9.57143 13.25H14.4286C17.3675 13.25 19.75 15.6325 19.75 18.5714C19.75 20.8792 17.8792 22.75 15.5714 22.75H8.42857C6.12081 22.75 4.25 20.8792 4.25 18.5714Z" fill="#000000"/>
                    </g>
                    </g>
                    </svg></div>
                <div class="setting-info">
                    <div class="setting-name-row">
                        <div class="setting-name">Profile</div>
                    </div>
                </div>
            </div>
            <div class="setting-item"  page="notification">
                <div class="icon"><svg fill="#000000" width="800px" height="800px" viewBox="0 0 24 24" id="notification-alert" xmlns="http://www.w3.org/2000/svg" class="icon line"><path id="primary" d="M19,15v3H5V10a7,7,0,0,1,7-7,5.47,5.47,0,0,1,1,.08" style="fill: none; stroke: rgb(0, 0, 0); stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5;"></path><path id="primary-2" data-name="primary" d="M3,18H21M9,18a3,3,0,0,0,6,0ZM17,3V6" style="fill: none; stroke: rgb(0, 0, 0); stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5;"></path><line id="primary-upstroke" x1="16.95" y1="10.5" x2="17.05" y2="10.5" style="fill: none; stroke: rgb(0, 0, 0); stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.95;"></line></svg></div>
                <div class="setting-info">
                    <div class="setting-name-row">
                        <div class="setting-name">Notificaion</div>
                    </div>
                </div>
            </div>
            <div class="setting-item" page="account">
                <div class="icon"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                    	 viewBox="0 0 32 32" enable-background="new 0 0 32 32" xml:space="preserve">
                    <path fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" d="M20,4c-4.4,0-8,3.6-8,8c0,0.6,0.1,1.2,0.2,1.8L4,22
                    	v6h5v-3h3v-3h3v-3h1.1c1.1,0.6,2.5,1,3.9,1c4.4,0,8-3.6,8-8S24.4,4,20,4z"/>
                    <circle cx="22" cy="10" r="2"/>
                    </svg></div>
                <div class="setting-info">
                    <div class="setting-name-row">
                        <div class="setting-name">Acount</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= SISI KANAN ================= -->
    <div class="main-sett-area" id="dynamic-content-setting">
        <!-- Konten dinamis masuk di sini -->
        <div style = "padding: 20px; font-size: 18px; color: #667781;">Pilih menu di sebelah kiri untuk melihat pengaturan.</div>
    </div>
<form id="logout-form" action="/logout" method="POST" style="display: none;">
    @csrf
</form>
</div>
<script>

</script>
</body>

