<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Akun</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px;">
        <h2 style="color: #059669; text-align: center;">Verifikasi Akun Anda</h2>
        <p>Halo,</p>
        <p>Terima kasih telah mendaftar. Gunakan kode OTP di bawah ini untuk menyelesaikan proses pendaftaran Anda:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #111827; background: #f3f4f6; padding: 10px 20px; border-radius: 5px; border: 1px solid #e5e7eb;">
                {{ $otp }}
            </span>
        </div>
        
        <p style="color: #6b7280; font-size: 14px;">Kode ini hanya berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.</p>
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">
        <p style="font-size: 12px; color: #9ca3af; text-align: center;">Email ini dikirim secara otomatis oleh sistem.</p>
    </div>
</body>
</html>
