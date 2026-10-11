<?php
require_once 'config.php';
requireLogin();

$user = findUserByEmail($_SESSION['user_email']);
$errors = [];
$old = ['nama' => $user['nama']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = clean($_POST['nama'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_new_password'] ?? '';

    $old['nama'] = $nama;

    if (empty($nama)) {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    $updateData = ['nama' => $nama];

    if (!empty($newPassword) || !empty($confirmPassword)) {
        if (strlen($newPassword) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Konfirmasi password baru tidak cocok.';
        } else {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
    }

    if (empty($errors)) {
        updateUser($user['email'], $updateData);
        $_SESSION['user_nama'] = $nama;

        setFlash('success', 'Profil berhasil diperbarui.');
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile — Sistem Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="app-sidebar">
            <div class="sidebar-brand">Portal</div>
            <nav class="sidebar-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="edit_profile.php" class="active">Edit Profile</a>
            </nav>
            <a href="logout.php" class="sidebar-logout">Logout</a>
        </aside>

        <main class="app-main">
            <header class="app-header">
                <h1>Edit Profile</h1>
            </header>

            <div class="app-content">
                <div class="panel-block">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="edit_profile.php">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($old['nama']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            <small>Email tidak dapat diubah.</small>
                        </div>

                        <hr>
                        <p class="form-subtitle">Kosongkan jika tidak ingin mengganti password.</p>

                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" placeholder="Minimal 6 karakter">
                        </div>

                        <div class="form-group">
                            <label for="confirm_new_password">Konfirmasi Password Baru</label>
                            <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Ulangi password baru">
                        </div>

                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>