<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP WhatsApp Style</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .logo-area {
            margin-bottom: 25px;
        }

        /* Ikon Pesan/WhatsApp Ringan */
        .icon-wa {
            width: 60px;
            height: 60px;
            background-color: #25D366;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 30px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
        }

        h2 {
            color: #111b21;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        p.instruction {
            color: #667781;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .otp-box {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 25px;
        }

        .otp-input {
            width: 45px;
            height: 50px;
            border: none;
            border-bottom: 3px solid #bac0c4;
            font-size: 24px;
            text-align: center;
            font-weight: 600;
            color: #111b21;
            outline: none;
            transition: border-color 0.2s ease;
        }

        /* Saat input diklik/aktif, garis bawah berubah menjadi hijau WhatsApp */
        .otp-input:focus {
            border-bottom-color: #00a884;
        }

        /* Menghilangkan tanda panah naik turun pada input type number */
        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .error-message {
            color: #ea0038;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: left;
        }

        .btn-verify {
            background-color: #00a884;
            color: #ffffff;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-verify:hover {
            background-color: #008f72;
        }

        .footer-text {
            margin-top: 25px;
            font-size: 13px;
            color: #667781;
        }

        .resend-link {
            color: #00a884;
            text-decoration: none;
            font-weight: 600;
        }

        .resend-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo-area">
        <!-- Menggunakan karakter amplop surat sebagai visual anchor pengganti logo -->
        <div class="icon-wa">✉</div> 
    </div>

    <h2>Verifikasi Nomor Anda</h2>
    <p class="instruction">
        Kami telah mengirimkan 6 digit kode OTP ke alamat email Anda. Silakan masukkan kode tersebut di bawah ini untuk mengaktifkan akun.
    </p>

    <!-- Notifikasi sukses kirim awal -->
    @if(session('success'))
        <div style="color: #00a884; font-size: 13px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('otp.verify.submit') }}" method="POST" id="otp-form">
        @csrf
        
        <!-- 6 Kotak Input Terpisah ala WhatsApp -->
        <div class="otp-box">
            <input type="number" maxlength="1" class="otp-input" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <input type="number" maxlength="1" class="otp-input" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <input type="number" maxlength="1" class="otp-input" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <input type="number" maxlength="1" class="otp-input" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <input type="number" maxlength="1" class="otp-input" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <input type="number" maxlength="1" class="otp-input" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
        </div>

        <!-- Hidden input untuk menampung gabungan teks asli sebelum di-submit ke backend -->
        <input type="hidden" name="otp" id="real-otp">

        <!-- Menampilkan pesan error dari Laravel jika OTP salah/kedaluwarsa -->
        @error('otp')
            <div class="error-message">⚠️ {{ $message }}</div>
        @enderror

        <button type="submit" class="btn-verify">Lanjutkan</button>
    </form>

    <div class="footer-text">
        Tidak menerima kode? <a href="#" class="resend-link">Kirim ulang kode</a>
    </div>
</div>

<script>
    const inputs = document.querySelectorAll('.otp-input');
    const hiddenInput = document.getElementById('real-otp');
    const form = document.getElementById('otp-form');

    // Fokus otomatis ke kotak pertama saat halaman dimuat
    window.addEventListener('load', () => inputs[0].focus());

    inputs.forEach((input, index) => {
        // Logika mengetik angka (pindah ke kanan)
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            combineOtp();
        });

        // Logika menghapus / Backspace (pindah ke kiri)
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Mencegah paste teks sembarangan, pastikan hanya angka yang masuk
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').trim();
            if (/^\d{6}$/.test(pasteData)) {
                inputs.forEach((numInput, i) => {
                    numInput.value = pasteData[i];
                });
                combineOtp();
                inputs[inputs.length - 1].focus();
            }
        });
    });

    // Menggabungkan nilai 6 kotak menjadi string utuh di hidden input
    function combineOtp() {
        let otpValue = "";
        inputs.forEach(input => otpValue += input.value);
        hiddenInput.value = otpValue;
    }
</script>

</body>
</html>
