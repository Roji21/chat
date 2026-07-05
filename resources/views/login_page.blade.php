<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <!-- Menggunakan Font Google Inter agar teks terlihat lebih modern -->
    <link href="https://googleapis.com" rel="stylesheet">
    <script src="https://unpkg.com"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        
        body { background-color: #eae6df; height: 100vh; display: flex; flex-direction: column; align-items: center; }

        /* Aksen Bar Hijau khas WhatsApp di bagian atas latar belakang */
        .top-bar { background-color: #00a884; width: 100%; height: 222px; position: absolute; top: 0; left: 0; z-index: 1; }

        /* Wadah Konten Utama */
        .main-container { position: relative; z-index: 2; width: 1000px; max-width: 95%; margin-top: 80px; background-color: #ffffff; border-radius: 4px; box-shadow: 0 17px 50px 0 rgba(11,20,26,.19), 0 12px 15px 0 rgba(11,20,26,.24); min-height: 500px; display: flex; padding: 60px; gap: 50px; }

        /* Sisi Kiri: Petunjuk Penggunaan */
        .left-side { flex: 1.2; display: flex; flex-direction: column; justify-content: center; }
        .left-side h1 { color: #41525d; font-size: 28px; font-weight: 300; margin-bottom: 40px; line-height: 1.3; }
        .instruction-list { list-style: none; }
        .instruction-list li { display: flex; align-items: flex-start; margin-bottom: 24px; font-size: 16px; color: #3b4a54; line-height: 1.5; }
        .instruction-list .number { background-color: #f0f2f5; color: #667781; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 500; margin-right: 15px; flex-shrink: 0; }
        .instruction-list strong { color: #111b21; font-weight: 500; }

        /* Sisi Kanan: Kotak Formulir Login */
        .right-side { flex: 0.8; display: flex; flex-direction: column; justify-content: center; align-items: center; border-left: 1px solid #f0f2f5; padding-left: 50px; }
        .login-box { width: 100%; max-width: 320px; }
        .login-box h2 { font-size: 20px; color: #111b21; margin-bottom: 8px; font-weight: 600; text-align: center; }
        .login-box p { font-size: 14px; color: #667781; text-align: center; margin-bottom: 25px; }

        /* Elemen Formulir */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; color: #008069; font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input { width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #e9edef; background-color: #fff; border-radius: 6px; font-size: 15px; color: #111b21; outline: none; transition: all 0.2s ease; }
        .form-group input:focus { border-color: #00a884; box-shadow: 0 0 0 3px rgba(0, 168, 132, 0.15); }
        
        .password-wrapper {position: relative;width: 100%;}
        .toggle-password {position: absolute;right: 14px;top: 50%;transform: translateY(-50%);cursor: pointer;color: #8696a0; /* Menyesuaikan palet warna WhatsApp/Teal Anda */display: flex;align-items: center;user-select: none;}
        .toggle-password:hover { color: #111b21;}
        .toggle-password svg { width: 20px;height: 20px;}

        /* Tombol Utama */
        .btn-submit { width: 100%; background-color: #00a884; color: #ffffff; border: none; padding: 14px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; margin-top: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn-submit:hover { background-color: #008f70; }

        /* Teks Tautan di Bagian Bawah */
        .form-footer { margin-top: 25px; text-align: center; font-size: 14px; color: #667781; }
        .form-footer a { color: #00a884; text-decoration: none; font-weight: 600; }
        .form-footer a:hover { text-decoration: underline; }

        /* Notifikasi Pesan Eror atau Sukses */
        .alert { padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; line-height: 1.4; }
        .alert-danger { background-color: #ffebe9; color: #cf222e; border: 1px solid rgba(207, 34, 46, 0.15); }
        .alert-success { background-color: #dafbe3; color: #1f883d; border: 1px solid rgba(31, 136, 61, 0.15); }

        /* Responsif untuk Layar Handphone */
        @media (max-width: 850px) {
            .main-container { flex-direction: column; padding: 30px; margin-top: 20px; gap: 30px; min-height: auto; }
            .right-side { border-left: none; padding-left: 0; padding-top: 30px; border-top: 1px solid #f0f2f5; }
            .top-bar { height: 120px; }
        }
    </style>
</head>
<body>

    <div class="top-bar"></div>

    <div class="main-container">
        
        <!-- SISI KIRI: Panduan Penggunaan Aplikasi -->
        <div class="left-side">
            <h1>Chat</h1>
            <ul class="instruction-list">
                <li>
                    <span class="number">1</span>
                    <div>Silakan buat akun terlebih dahulu di menu <strong>Daftar Akun</strong> jika belum memilikinya.</div>
                </li>
                <li>
                    <span class="number">2</span>
                    <div>Masukkan <strong>Nama Pengguna (Username)</strong> terdaftar pada kolom di sebelah kanan.</div>
                </li>
                <li>
                    <span class="number">3</span>
                    <div>Gunakan <strong>Kata Sandi</strong> yang sesuai untuk mulai membuka ruang obrolan obrolan langsung.</div>
                </li>
            </ul>
        </div>

        <!-- SISI KANAN: Formulir Masuk -->
        <div class="right-side">
            <div class="login-box">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda </p>

                <!-- Menampilkan Pesan Sukses Setelah Daftar Akun -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Menampilkan Notifikasi Ketika Terjadi Eror Salah Password -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Form Login -->
                <form action="/login/proses" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Username atau Email</label>
                        <input type="text" id="login_input" name="login_input" placeholder="Username atau Email" required autofocus autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                            <span class="toggle-password" id="togglePassword">
                                <svg id="eyeIcon" xmlns="http://w3.org" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" y1="2" x2="22" y2="22"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Masuk Sekarang</button>
                </form>

                <div class="form-footer">
                    Pengguna baru? <a href="/register">Daftar Akun Baru</a>
                </div>
            </div>
        </div>

    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        // Menyimpan string struktur SVG mata terbuka dan tertutup secara lokal
        const eyeOpenSvg = `
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
            <circle cx="12" cy="12" r="3"/>
        `;
        
        const eyeClosedSvg = `
            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
            <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
            <line x1="2" y1="2" x2="22" y2="22"/>
        `;

        // Fungsi untuk memunculkan password (Unhide)
    function showPassword(e) {
        e.preventDefault(); // Mencegah glitch seleksi teks di browser
        passwordInput.setAttribute('type', 'text');
        eyeIcon.innerHTML = eyeOpenSvg; // Ikon berubah jadi mata terbuka saat diintip
    }

    // Fungsi untuk menyembunyikan kembali password (Hide)
    function hidePassword(e) {
        e.preventDefault();
        passwordInput.setAttribute('type', 'password');
        eyeIcon.innerHTML = eyeClosedSvg; // Kembali jadi mata dicoret saat dilepas
    }

    // --- EVENT UNTUK PENGGUNA LAPTOP / DESKTOP (MOUSE) ---
    // Ketika klik kiri mouse ditekan dan ditahan
    togglePassword.addEventListener('mousedown', showPassword);
    // Ketika klik kiri mouse dilepas
    togglePassword.addEventListener('mouseup', hidePassword);
    // Ketika kursor mouse tidak sengaja geser keluar dari area ikon saat menahan
    togglePassword.addEventListener('mouseleave', hidePassword);

    // --- EVENT UNTUK PENGGUNA HP / TABLET (TOUCH LAYAR) ---
    // Ketika layar mulai disentuh/ditekan jari
    togglePassword.addEventListener('touchstart', showPassword, { passive: false });
    // Ketika jari dilepas dari layar
    togglePassword.addEventListener('touchend', hidePassword, { passive: false });
    // Ketika sentuhan terganggu sistem (misal muncul notifikasi hp)
    togglePassword.addEventListener('touchcancel', hidePassword);
    </script>
</body>
</html>
