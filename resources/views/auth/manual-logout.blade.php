<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
</head>
<body>
    <h1>Anda akan logout.</h1>
    <p>Klik tombol di bawah ini untuk melanjutkan.</p>

    <!-- Formulir ini akan mengirim request POST ke route logout -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            Logout Sekarang
        </button>
    </form>
</body>
</html>
