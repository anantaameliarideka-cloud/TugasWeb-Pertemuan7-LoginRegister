<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = clean($_POST['nama'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $old['nama']  = $nama;
    $old['email'] = $email;

    if (empty($nama)) {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    if (empty($email)) {
        $errors[] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    } elseif (findUserByEmail($email)) {
        $errors[] = 'Email sudah terdaftar. Silakan gunakan email lain atau login.';
    }

    if (empty($password)) {
        $errors[] = 'Password tidak boleh kosong.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (empty($errors)) {
        $users = getUsers();

        $newUser = [
            'id'       => uniqid('user_'),
            'nama'     => $nama,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $users[] = $newUser;
        saveUsers($users);

        setFlash('success', 'Registrasi berhasil! Silakan login.');
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar — Sistem Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-shell">
        <aside class="auth-panel">
            <div class="auth-panel-inner">
                <span class="brand-mark">Portal</span>
                <h1 class="panel-headline">Buat akun baru, mulai dalam hitungan detik.</h1>
                <p class="panel-copy">Data kamu tersimpan aman dan cuma bisa diakses lewat akunmu sendiri.</p>
            </div>
            <div class="panel-decor" aria-hidden="true"></div>
        </aside>

        <main class="auth-form-side">
            <div class="form-wrap">
                <h2 class="form-title">Daftar akun</h2>
                <p class="form-subtitle">Lengkapi data di bawah untuk membuat akun.</p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?= $old['nama'] ?>" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= $old['email'] ?>" placeholder="contoh@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" class="btn-primary">Daftar</button>
                </form>

                <p class="switch-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
            </div>
        </main>
    </div>
</body>
</html>