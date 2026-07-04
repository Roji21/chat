<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - {{ auth()->user()->name }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; width: 100vw; height: 100vh; overflow: hidden; font-family: sans-serif; background-color: #111b21; }
        .chat-container { width: 100vw; height: 100vh; display: grid; grid-template-columns: 40px 1fr; overflow: hidden; }
        .menu-container { background: #ffffff; display: flex; flex-direction: column; align-items: center; padding: 20px 0; gap: 25px; border-right: 1px solid #2f3b43; height: 100%; }
        .menu-item { font-size: 24px; cursor: pointer; position: relative; }
        .main-content-area { display: grid; width: 100%; height: 100%; overflow: hidden; grid-template-columns: 1fr; }
        .menu-item:hover { background-color: #898d91; color: #111b21; border-radius: 10px; padding: 6px; }
        .menu-item:hover::after { opacity: 1; transform: translateX(-50%) translateY(0); } */
    </style>
</head>
<body>

    <div class="chat-container">
        <div class="menu-container">
            <div class="menu-item" title="Home" onclick="renderHalaman('chat')">🏠</div>
            <div class="menu-item" title="New Chat" onclick="renderHalaman('newchat')">
                <div class="avatar" style="background-color: transparent; font-size: 24px; height: 28px; width: 28px; padding: 0;">
                    <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-labelledby="chatAddIconTitle" stroke="#000000" stroke-width="1" stroke-linecap="square" stroke-linejoin="miter" fill="none" color="#000000"> <title id="chatAddIconTitle">New chat</title> <path d="M21 4V17H13L7 21V17H3V4H21Z"/> <path d="M15 10H9"/> <path d="M12 7V13"/> </svg>
                </div>
            </div>
            <div class="menu-item" title="Settings" onclick="renderHalaman('setting')">⚙️</div>
            <div class="menu-item" title="Log out" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="cursor: pointer;">
    ⚙️
</div>

<!-- Form Tersembunyi (Wajib Menggunakan POST dan @csrf untuk Keamanan) -->
<form id="logout-form" action="/logout" method="POST" style="display: none;">
    @csrf
</form>
        </div>

        <div class="main-content-area" id="dynamic-content">
            <!-- Konten dinamis masuk di sini -->
        </div>
    </div>

    <script>
        const LAYOUT_CONFIG = {
            chat: "300px 1fr",
            setting: "250px 1fr ",
            newchat: "300px 1fr"
        };
        window.chatConfig = {
        iduser: "{{ auth()->user()->number }}",
        userSekarang: "{{ auth()->user()->name }}",
        lawanBicara: "",
        lawanfoto: "{{ auth()->user()->photo }}",
        routeList: "{{ route('chat.list') }}"
        };

        async function renderHalaman(halaman) {
            
            // 1. Bersihkan aset CSS dan JS lama dari memori browser
            const dynamicContent = document.getElementById('dynamic-content');
            bersihkanAsetLama();

            if (LAYOUT_CONFIG[halaman]) {
                dynamicContent.style.gridTemplateColumns = LAYOUT_CONFIG[halaman];
            }
            

            dynamicContent.innerHTML = `<div style="color:white; padding:20px;">Memuat...</div>`;

            try {
                const response = await fetch(`/${halaman}`);
                if (!response.ok) throw new Error("Gagal memuat halaman.");
                const htmlResult = await response.text();
                if (window.chatInterval) {
                    clearInterval(window.chatInterval);
                    window.chatInterval = null; 
                }
                if (typeof window.hentikanIntervalList === "function") {
                    window.hentikanIntervalList();
                }

                dynamicContent.innerHTML = htmlResult;
                muatAsetDinamis(halaman);
                if(halaman == 'chat' && typeof window.mulaiIntervalList === "function") {
                    window.hentikanIntervalList();
                    clearInterval(window.chatInterval);
                    window.chatInterval = null;
                    window.mulaiIntervalList();
                    window.chatInterval = setInterval(function() {
                        muatChat(false);
                    }, 5000);
                } 

            } catch (error) {
                dynamicContent.innerHTML = `<div style="color:red; padding:20px;">${error.message}</div>`;
            }
        }

        function muatAsetDinamis(halaman) {
            // Suntik CSS Baru
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = `/css/${halaman}.css`;
            link.id = `dynamic-css-${halaman}`;
            document.head.appendChild(link);

            // Suntik JS Baru (Gunakan type=module agar variabel antar file tidak bentrok)
            const script = document.createElement('script');
            script.src = `/js/${halaman}.js`;
            // script.type = 'module';
            script.id = `dynamic-js-${halaman}`;
            document.body.appendChild(script);
        }

        function bersihkanAsetLama() {
            // Hapus elemen link CSS dan tag script dinamis lama dari DOM HTML
            document.querySelectorAll('[id^="dynamic-css-"]').forEach(el => el.remove());
            document.querySelectorAll('[id^="dynamic-js-"]').forEach(el => el.remove());
        }
        document.addEventListener("DOMContentLoaded", () => {
            renderHalaman('chat'); 
        });
    </script>
</body>
</html>
