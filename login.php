<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['email' => ''];
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $old['email'] = $email;

    if (empty($email) || empty($password)) {
        $errors[] = 'Email dan password wajib diisi.';
    } else {
        $user = findUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email atau password salah.';
        } else {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nama']  = $user['nama'];

            if ($remember) {
                setRememberMeCookie($user['email']);
            }

            setFlash('success', 'Login berhasil! Selamat datang, ' . $user['nama'] . '.');
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login — Sistem Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-shell">
        <aside class="auth-panel">
            <div class="auth-panel-inner">
                <span class="brand-mark">Portal</span>
                <h1 class="panel-headline">Satu pintu masuk untuk semua yang kamu kerjakan di sini.</h1>
                <p class="panel-copy">Masuk sekali, akses dashboard dan profilmu tanpa ribet.</p>
            </div>
            <div class="panel-decor" aria-hidden="true"></div>
        </aside>

        <main class="auth-form-side">
            <div class="form-wrap">
                <h2 class="form-title">Masuk ke akunmu</h2>
                <p class="form-subtitle">Isi email dan password untuk melanjutkan.</p>

                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
                        <?= $flash['message'] ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= $old['email'] ?>" placeholder="contoh@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="form-group form-checkbox">
                        <label>
                            <input type="checkbox" name="remember" value="1">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn-primary">Masuk</button>
                </form>

                <p class="switch-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </main>
    </div>
</body>
</html>