<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar</title>
    <style>
        /* Notifikasi Pesan Eror atau Sukses */
        .alert { padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; line-height: 1.4; }
        .alert-danger { background-color: #ffebe9; color: #cf222e; border: 1px solid rgba(207, 34, 46, 0.15); }
        .alert-success { background-color: #dafbe3; color: #1f883d; border: 1px solid rgba(31, 136, 61, 0.15); }

        /* Pengaturan Dasar & Skema Warna WhatsApp */
        :root {
            --wa-teal: #008069;
            --wa-teal-light: #00a884;
            --wa-teal-hover: #006653;
            --wa-bg: #f0f2f5;
            --wa-text-dark: #111b21;
            --wa-text-gray: #667781;
            --wa-border: #e9edef;
            --wa-icon-gray: #8696a0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--wa-bg);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container Kartu Form */
        .signup-container {
            background-color: #ffffff;
            width: 100%;
            max-width: 450px;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(11, 20, 26, 0.08);
            box-sizing: border-box;
            border-top: 10px solid var(--wa-teal);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: var(--wa-teal);
            font-size: 24px;
            margin: 0 0 8px 0;
            font-weight: 700;
        }

        .header p {
            color: var(--wa-text-gray);
            font-size: 14px;
            margin: 0;
        }

        /* Preview Unggah Gambar Profil */
        .avatar-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
        }

        .avatar-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #f0f2f5;
            overflow: hidden;
            border: 2px dashed var(--wa-icon-gray);
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.2s ease;
        }

        .avatar-wrapper:hover {
            border-color: var(--wa-teal-light);
        }

        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            text-align: center;
            color: var(--wa-text-gray);
            font-size: 12px;
            padding: 10px;
        }

        .avatar-placeholder svg {
            width: 28px;
            height: 28px;
            color: var(--wa-icon-gray);
            margin-bottom: 4px;
        }

        .hidden {
            display: none !important;
        }

        /* Struktur Elemen Formulir */
        .form-group { 
            margin-bottom: 20px; 
        }

        .form-group label { 
            display: block; 
            font-size: 13px; 
            color: var(--wa-teal); 
            font-weight: 600; 
            margin-bottom: 6px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }

        .form-group input { 
            width: 100%; 
            padding: 12px 40px 12px 12px; 
            border: 1px solid var(--wa-border); 
            background-color: #fff; 
            border-radius: 6px; 
            font-size: 15px; 
            color: var(--wa-text-dark); 
            outline: none; 
            transition: all 0.2s ease; 
            box-sizing: border-box; 
        }

        .form-group input:focus { 
            border-color: var(--wa-teal-light); 
            box-shadow: 0 0 0 3px rgba(0, 168, 132, 0.15); 
        }

        /* Pembungkus Input Kata Sandi */
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--wa-icon-gray);
            display: flex;
            align-items: center;
            user-select: none;
        }

        .toggle-password:hover {
            color: var(--wa-text-dark);
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        /* Tombol Aksi */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: var(--wa-teal);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: var(--wa-teal-hover);
        }

        .btn-submit:disabled {
            background-color: var(--wa-icon-gray);
            cursor: not-allowed;
        }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--wa-text-gray);
        }

        .footer-link a {
            color: var(--wa-teal-light);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }

        .feedback-msg {
        font-size: 13px;
        margin-top: 5px;
        font-weight: 500;
    }
    .text-success { color: #00a884; } /* Hijau WhatsApp */
    .text-danger { color: #ea0038; }  /* Merah Peringatan */

    .suggestion-box {
        margin-top: 8px;
        background-color: #f8f9fa;
        border: 1px solid #e9edef;
        border-radius: 6px;
        padding: 10px;
    }
    .suggestion-box p {
        margin: 0 0 6px 0;
        font-size: 12px;
        color: var(--wa-text-gray);
    }
    .suggest-badge {
        display: inline-block;
        background-color: #e8f5e9;
        color: var(--wa-teal);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 13px;
        cursor: pointer;
        margin-right: 6px;
        margin-bottom: 6px;
        border: 1px solid rgba(0, 128, 105, 0.2);
        font-weight: 500;
    }
    .suggest-badge:hover {
        background-color: var(--wa-teal);
        color: #fff;
    }
    </style>
</head>
<body>

    <div class="signup-container">
        <div class="header">
            <h1>Daftar Akun Baru</h1>
            <p>Mulai berkirim pesan dengan mudah dan aman</p>
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>

        <form action="/registerup" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="name" placeholder="Contoh: Budi Santoso" required>
            </div>

            <!-- Input Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Minimal 5-11 karakter" maxlength="11" required>
                <!-- Pesan status ketersediaan -->
                <div id="username_feedback" class="feedback-msg"></div>
                <!-- Container untuk memunculkan rekomendasi nama -->
                <div id="suggestion_container" class="suggestion-box hidden">
                    <p>Saran username:</p>
                    <div id="suggestion_list"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" placeholder="Contoh: budi@gmail.com" required>
                <!-- Tempat menampilkan pesan kesalahan/sukses -->
                <div id="email_feedback" class="feedback-msg"></div>
            </div>

            <!-- Nomor Telepon -->
            <!-- <div class="form-group">
                <label for="number">Nomor Telepon</label>
                <input type="tel" id="number" name="number" placeholder="Contoh: 08123456789" maxlength="13" required>
            </div> -->

            <!-- Kata Sandi -->
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi aman" required>
                    <span class="toggle-password" id="togglePasswordregister">
                        <svg id="eyeIcon" xmlns="http://w3.org" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" y1="2" x2="22" y2="22"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi kata sandi" required>
                    <span class="toggle-password" id="togglePasswordConfirmation">
                        <svg id="eyeIcon2" xmlns="http://w3.org" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" y1="2" x2="22" y2="22"/>
                        </svg>
                    </span>
                </div>
            </div>

            <button type="submit" id="btn_kirim" class="btn-submit" disabled>Daftar Sekarang</button>
        </form>

        <div class="footer-link">
            Sudah punya akun? <a href="/">Masuk ke sini</a>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const togglePassword = document.getElementById('togglePasswordregister');
        const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeIcon1 = document.getElementById('eyeIcon2');
        const usernameInput = document.getElementById("username");
        const usernameFeedback = document.getElementById("username_feedback");
        const suggestionContainer = document.getElementById("suggestion_container");
        const suggestionList = document.getElementById("suggestion_list");
        const emailInput = document.getElementById("email");
        const emailFeedback = document.getElementById("email_feedback");
        const tombolSubmit = document.getElementById("btn_kirim");
        let emailTimer;
        let typingTimer;
        let isEmailValid = false;
        let isUsernameValid = false;

        emailInput.addEventListener("input", function () {
            clearTimeout(emailTimer);

            const emailValue = this.value.trim();

            // 1. Jika inputan masih kosong, bersihkan pesan error
            if (emailValue.length === 0) {
                emailFeedback.innerText = "";
                return;
            }

            // Beri jeda 400ms setelah user berhenti mengetik (debounce) sebelum divalidasi
            emailTimer = setTimeout(() => {
        
                /* 
                   Regex Standar Industri untuk Validasi Email:
                   - Memastikan ada karakter sebelum @
                   - Memastikan ada simbol @
                   - Memastikan ada domain setelah @ (misal: gmail)
                   - Memastikan ada ekstensi domain minimal 2 karakter (misal: .com, .id, .co.id)
                */
                const regexEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                if (regexEmail.test(emailValue)) {
                    // Jika format BENAR
                    emailFeedback.innerText = "✓ Format email valid.";
                    emailFeedback.className = "feedback-msg text-success";
                    isEmailValid = true;
                    periksaSemuaValidasi();
                } else {
                    // Jika format SALAH
                    emailFeedback.innerText = "✗ Format email tidak sah (contoh: nama@domain.com).";
                    emailFeedback.className = "feedback-msg text-danger";
                    isEmailValid = false;
                    periksaSemuaValidasi();
                }
                periksaSemuaValidasi(); 

            }, 400);
        });

        // Deteksi ketikan di input username
        usernameInput.addEventListener("input", function () {
            clearTimeout(typingTimer);

            let username = this.value.trim();

            // Saring input agar hanya menerima huruf dan angka saja (Alfanumerik)
            username = username.replace(/[^a-zA-Z0-9]/g, "");
            this.value = username; 

            // Sembunyikan elemen jika inputan kosong
            if (username.length === 0) {
                usernameFeedback.innerText = "";
                suggestionContainer.classList.add("hidden");
                isUsernameValid = false;
                periksaSemuaValidasi();
                return;
            }

            // Validasi panjang karakter (5 - 11) sebelum dikirim ke database
            if (username.length < 5 || username.length > 11) {
                usernameFeedback.innerText = "Username wajib terdiri dari 5 sampai 11 karakter angka/huruf.";
                usernameFeedback.className = "feedback-msg text-danger";
                suggestionContainer.classList.add("hidden");
                isUsernameValid = false;
                periksaSemuaValidasi();
                return;
            }else {
                usernameFeedback.innerText = "Memeriksa ketersediaan username...";
                usernameFeedback.className = "feedback-msg text-success";
                isUsernameValid = false;
                periksaSemuaValidasi();
            }
            periksaSemuaValidasi(); 

            // Beri jeda 500ms setelah user berhenti mengetik, baru cek ke server Laravel
            typingTimer = setTimeout(() => {
                checkUsernameOnServer(username);
            }, 500);
        });

        // Fungsi untuk mengirim data username ke Laravel secara async
        function checkUsernameOnServer(username) {
            fetch('/check-username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Jika Anda menggunakan Laravel CSRF, pastikan sertakan token ini:
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ username: username })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'available') {
                    usernameFeedback.innerText = "✓ " + data.message;
                    usernameFeedback.className = "feedback-msg text-success";
                    suggestionContainer.classList.add("hidden");
                    isUsernameValid = true;
                    periksaSemuaValidasi();
                } 
                else if (data.status === 'taken') {
                    usernameFeedback.innerText = "✗ " + data.message;
                    usernameFeedback.className = "feedback-msg text-danger";
                    isUsernameValid = false;
                    periksaSemuaValidasi();

                    // Tampilkan rekomendasi nama alternatif
                    suggestionList.innerHTML = "";
                    data.suggestions.forEach(name => {
                        const badge = document.createElement("span");
                        badge.className = "suggest-badge";
                        badge.innerText = name;

                        // Jika lencana saran diklik, otomatis masukkan ke kolom input
                        badge.addEventListener("click", function() {
                            usernameInput.value = name;
                            usernameFeedback.innerText = "✓ Username diperbarui dari saran!";
                            usernameFeedback.className = "feedback-msg text-success";
                            suggestionContainer.classList.add("hidden");
                        });

                        suggestionList.appendChild(badge);
                    });
                    suggestionContainer.classList.remove("hidden");
                } else {
                    usernameFeedback.innerText = data.message;
                    usernameFeedback.className = "feedback-msg text-danger";
                    isUsernameValid = false;
                }
                periksaSemuaValidasi(); 
            })
            .catch(error => {
                console.error("Gagal memeriksa username:", error);
            });
        }

        function periksaSemuaValidasi() {
            // Tombol aktif HANYA JIKA email DAN username bernilai true
            if (isEmailValid && isUsernameValid) {
                console.log("Semua validasi berhasil. Tombol submit diaktifkan.");
                tombolSubmit.disabled = false;
            } else {
                tombolSubmit.disabled = true;
            }
        }

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
        function showPassword2(e) {
            e.preventDefault(); // Mencegah glitch seleksi teks di browser
            passwordConfirmationInput.setAttribute('type', 'text');
            eyeIcon1.innerHTML = eyeOpenSvg; // Ikon berubah jadi mata terbuka saat diintip
        }
    
        // Fungsi untuk menyembunyikan kembali password (Hide)
        function hidePassword(e) {
            e.preventDefault();
            passwordInput.setAttribute('type', 'password');
            eyeIcon.innerHTML = eyeClosedSvg; // Kembali jadi mata dicoret saat dilepas
        }
        function hidePassword2(e) {
            e.preventDefault();
            passwordConfirmationInput.setAttribute('type', 'password');
            eyeIcon1.innerHTML = eyeClosedSvg; // Kembali jadi mata dicoret saat dilepas
        }
    
        // --- EVENT UNTUK PENGGUNA LAPTOP / DESKTOP (MOUSE) ---
        // Ketika klik kiri mouse ditekan dan ditahan
        togglePassword.addEventListener('mousedown', showPassword);
        togglePasswordConfirmation.addEventListener('mousedown', showPassword2);
        // Ketika klik kiri mouse dilepas
        togglePassword.addEventListener('mouseup', hidePassword);
        togglePasswordConfirmation.addEventListener('mouseup', hidePassword2);
        // Ketika kursor mouse tidak sengaja geser keluar dari area ikon saat menahan
        togglePassword.addEventListener('mouseleave', hidePassword);
        togglePasswordConfirmation.addEventListener('mouseleave', hidePassword2);
    
        // --- EVENT UNTUK PENGGUNA HP / TABLET (TOUCH LAYAR) ---
        // Ketika layar mulai disentuh/ditekan jari
        togglePassword.addEventListener('touchstart', showPassword, { passive: false });
        togglePasswordConfirmation.addEventListener('touchstart', showPassword2, { passive: false });
        // Ketika jari dilepas dari layar
        togglePassword.addEventListener('touchend', hidePassword, { passive: false });
        togglePasswordConfirmation.addEventListener('touchend', hidePassword2, { passive: false });
        // Ketika sentuhan terganggu sistem (misal muncul notifikasi hp)
        togglePassword.addEventListener('touchcancel', hidePassword);
        togglePasswordConfirmation.addEventListener('touchcancel', hidePassword2);
    </script>
</body>
</html>