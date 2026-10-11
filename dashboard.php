<?php
require_once 'config.php';
requireLogin();

$flash = getFlash();
$user = findUserByEmail($_SESSION['user_email']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Sistem Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="app-sidebar">
            <div class="sidebar-brand">Portal</div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="edit_profile.php">Edit Profile</a>
            </nav>
            <a href="logout.php" class="sidebar-logout">Logout</a>
        </aside>

        <main class="app-main">
            <header class="app-header">
                <h1>Dashboard</h1>
            </header>

            <div class="app-content">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
                        <?= $flash['message'] ?>
                    </div>
                <?php endif; ?>

                <div class="panel-block">
                    <h2>Selamat datang, <?= htmlspecialchars($user['nama']) ?></h2>
                    <p>Ini halaman yang cuma bisa diakses kalau kamu sudah login.</p>

                    <dl class="profile-list">
                        <div class="profile-row">
                            <dt>Nama</dt>
                            <dd><?= htmlspecialchars($user['nama']) ?></dd>
                        </div>
                        <div class="profile-row">
                            <dt>Email</dt>
                            <dd><?= htmlspecialchars($user['email']) ?></dd>
                        </div>
                        <div class="profile-row">
                            <dt>Bergabung sejak</dt>
                            <dd><?= htmlspecialchars($user['created_at'] ?? '-') ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </main>
    </div>
</body>
</html>