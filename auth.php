<?php
require 'db.php';
$error = '';
$success = '';

// REGISTRATION LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $stage_name = trim($_POST['stage_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dance_style = trim($_POST['dance_style']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        $error = "Email hii tayari imesajiliwa kwenye mfumo!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, stage_name, email, phone, dance_style, password) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$full_name, $stage_name, $email, $phone, $dance_style, $password])) {
            $success = "Usajili umekamilika! Subiri Admin akubali akaunti yako ili uweze ku-login.";
        } else {
            $error = "Kuna tatizo limetokea, tafadhali jaribu tena.";
        }
    }
}

// LOGIN LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
        // Angalia kama ni user na status yake ni pending
        if ($user['status'] == 'pending' && $user['role'] == 'user') {
            $error = "TAARIFA ZAKO ZINACHUNGUZWA WASILIANA NA ADMINISTRATOR";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['stage_name'] = $user['stage_name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }
    } else {
        $error = "Email au Password si sahihi!";
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register | Babati Maits Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body { background: #09090b; color: white; font-family: 'Segoe UI', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.8); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.1); }
        .gradient-text { background: linear-gradient(90deg, #f43f5e, #a855f7, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-rose-900/20 via-zinc-950 to-zinc-950">

    <div class="glass w-full max-w-md p-8 rounded-3xl shadow-2xl animate__animated animate__zoomIn">
        <div class="text-center mb-6">
            <a href="[Public Website](http://localhost/babati_maits/index.php)" class="text-2xl font-black gradient-text tracking-wider">BABATI MAITS</a>
            <p class="text-zinc-400 text-xs mt-1 uppercase tracking-widest font-semibold">Member & Admin Portal</p>
        </div>

        <!-- Fomu Switch Tabs -->
        <div class="flex bg-zinc-900/90 p-1 rounded-xl mb-6 border border-zinc-800">
            <button onclick="switchForm('login')" id="btn-login" class="w-1/2 py-2.5 rounded-lg font-semibold bg-rose-600 text-white text-sm transition shadow">Ingia (Login)</button>
            <button onclick="switchForm('register')" id="btn-register" class="w-1/2 py-2.5 rounded-lg font-semibold text-zinc-400 hover:text-white text-sm transition">Jisajili (Join)</button>
        </div>

        <?php if($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 p-3 rounded-xl mb-4 text-xs font-bold text-center flex items-center justify-center gap-2 animate__animated animate__shakeX">
                <i class="fas fa-exclamation-triangle text-sm"></i> <span><?= $error ?></span>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 p-3 rounded-xl mb-4 text-xs flex items-center gap-2 animate__animated animate__fadeIn">
                <i class="fas fa-check-circle text-base"></i> <span><?= $success ?></span>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form id="login-form" method="POST" class="space-y-4 animate__animated animate__fadeIn">
            <div>
                <label class="text-xs text-zinc-400 font-medium">Email Address</label>
                <input type="email" name="email" placeholder="e.g. admin@babatimaits.com" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 mt-1 text-sm focus:border-rose-500 focus:outline-none transition">
            </div>
            <div>
                <label class="text-xs text-zinc-400 font-medium">Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 mt-1 text-sm focus:border-rose-500 focus:outline-none transition">
            </div>
            <button type="submit" name="login" class="w-full bg-gradient-to-r from-rose-500 to-purple-600 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-rose-500/20 hover:opacity-90 transition">Log In</button>
        </form>

        <!-- REGISTER FORM -->
        <form id="register-form" method="POST" class="space-y-3.5 hidden animate__animated animate__fadeIn">
            <div>
                <input type="text" name="full_name" placeholder="Jina Kamili" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-rose-500 focus:outline-none transition">
            </div>
            <div>
                <input type="text" name="stage_name" placeholder="Jina la Kisanii (Stage Name)" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-rose-500 focus:outline-none transition">
            </div>
            <div>
                <input type="email" name="email" placeholder="Email Address" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-rose-500 focus:outline-none transition">
            </div>
            <div>
                <input type="text" name="phone" placeholder="Namba ya Simu (mf. 0712345678)" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-rose-500 focus:outline-none transition">
            </div>
            <div>
                <select name="dance_style" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-rose-500 focus:outline-none transition text-zinc-300">
                    <option value="Afro Dance">Afro Dance</option>
                    <option value="Hip Hop / Breaking">Hip Hop / Breaking</option>
                    <option value="Amapiano">Amapiano</option>
                    <option value="Traditional / Asili">Traditional / Asili</option>
                    <option value="All Styles">All Styles</option>
                </select>
            </div>
            <div>
                <input type="password" name="password" placeholder="Tengeneza Password Imara" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-rose-500 focus:outline-none transition">
            </div>
            <button type="submit" name="register" class="w-full bg-gradient-to-r from-purple-600 to-rose-500 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-purple-500/20 hover:opacity-90 transition">Kamilisha Usajili</button>
        </form>

        <div class="text-center mt-6 pt-4 border-t border-zinc-900">
            <a href="[Public Website](http://localhost/babati_maits/index.php)" class="text-xs text-zinc-500 hover:text-zinc-300 transition flex items-center justify-center gap-1.5"><i class="fas fa-arrow-left"></i> Rudi Ukurasa wa Mwanzo</a>
        </div>
    </div>

    <script>
        function switchForm(type) {
            const loginForm = document.getElementById('login-form');
            const regForm = [document.getElementById('register-form')][0];
            const btnLogin = document.getElementById('btn-login');
            const btnReg = document.getElementById('btn-register');

            if (type === 'login') {
                loginForm.classList.remove('hidden');
                regForm.classList.add('hidden');
                btnLogin.classList.add('bg-rose-600', 'text-white', 'shadow');
                btnLogin.classList.remove('text-zinc-400');
                btnReg.classList.remove('bg-rose-600', 'text-white', 'shadow');
                btnReg.classList.add('text-zinc-400');
            } else {
                regForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
                btnReg.classList.add('bg-rose-600', 'text-white', 'shadow');
                btnReg.classList.remove('text-zinc-400');
                btnLogin.classList.remove('bg-rose-600', 'text-white', 'shadow');
                btnLogin.classList.add('text-zinc-400');
            }
        }
    </script>
</body>
</html>