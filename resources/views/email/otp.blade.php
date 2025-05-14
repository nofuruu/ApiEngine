<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Kode OTP Anda</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto;">
        <h2 style="color: #333333;">Verifikasi Login</h2>
        <p>Halo <strong>{{ $name }}</strong></p>
        <p>Gunakan kode OTP berikut untuk memverifikasi login Anda:</p>
        <h1 style="text-align: center; color: #2c3e50;">{{ $otp }}</h1>
        <p>Kode ini hanya berlaku selama beberapa menit. Jangan berikan kode ini kepada siapa pun.</p>
        <p>Terima kasih,<br>Tim Keamanan</p>
    </div>
</body>

</html>