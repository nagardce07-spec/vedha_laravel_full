<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — Vedha Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Poppins', sans-serif; background: #EEF2FF;
            min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px;
        }
        .card {
            background: #fff; border-radius: 24px; overflow: hidden; display: flex;
            width: 900px; max-width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.08);
        }
        .illustration {
            flex: 1; background: linear-gradient(160deg, #A855F7, #4C1D95);
            min-height: 560px;
        }
        .form-side { flex: 1; padding: 48px 56px; display:flex; flex-direction: column; justify-content: center; }
        .logo-box { margin-bottom: 32px; }
        .logo-box img { width: 130px; }
        h1 { font-size: 28px; margin: 0 0 4px; }
        .subtitle { color: #6B7280; margin-bottom: 24px; font-size: 14px; }
        hr { border: none; border-top: 1px solid #E5E7EB; margin: 20px 0; }
        label { font-size: 14px; font-weight: 500; display:block; margin-bottom: 8px; }
        .field { position: relative; margin-bottom: 18px; }
        input[type=email], input[type=password], input[type=text] {
            width: 100%; padding: 13px 16px; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 14px;
        }
        .toggle-eye { position: absolute; right: 14px; top: 13px; cursor: pointer; color: #9CA3AF; }
        .btn-login {
            width: 100%; padding: 14px; margin-top: 8px; background: #7C3AED; color: #fff; border: none;
            border-radius: 10px; font-weight: 600; font-size: 15px; letter-spacing: .03em; cursor: pointer;
        }
        .btn-login:hover { background: #6D28D9; }
        .error { color: #DC2626; font-size: 13px; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="illustration"></div>
        <div class="form-side">
            <div class="logo-box">
                @if(optional($settings)->logo_light_url)
                    <img src="{{ $settings->logo_light_url }}" alt="{{ $settings->title }}">
                @else
                    <h2 style="color:#7C3AED;">{{ optional($settings)->title ?? 'Vedha' }}</h2>
                @endif
            </div>

            <h1>Welcome Back</h1>
            <p class="subtitle">Login to your Dashboard</p>
            <hr>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label>Email</label>
                <div class="field">
                    <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required>
                </div>
                @error('email') <div class="error">{{ $message }}</div> @enderror

                <label>Password</label>
                <div class="field">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="toggle-eye" onclick="
                        const p = document.getElementById('password');
                        p.type = p.type === 'password' ? 'text' : 'password';
                    ">👁</span>
                </div>

                <button type="submit" class="btn-login">LOG IN</button>
            </form>
        </div>
    </div>
</body>
</html>
