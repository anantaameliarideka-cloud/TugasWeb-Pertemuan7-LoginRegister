<?php
/**
 * config.php
 * Berisi konfigurasi dasar + fungsi-fungsi helper
 * untuk baca/tulis data user ke file JSON.
 */

session_start();

define('USERS_FILE', __DIR__ . '/data/users.json');

/**
 * Ambil semua data user dari file JSON.
 * Kalau file belum ada / rusak, kembalikan array kosong.
 */
function getUsers(): array {
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, json_encode([]));
    }
    $json = file_get_contents(USERS_FILE);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

/**
 * Simpan array user ke file JSON.
 */
function saveUsers(array $users): void {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

/**
 * Cari satu user berdasarkan email. Return null kalau tidak ketemu.
 */
function findUserByEmail(string $email): ?array {
    $users = getUsers();
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            return $user;
        }
    }
    return null;
}

/**
 * Cek apakah user sedang login (via session).
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_email']);
}

/**
 * Proteksi halaman: kalau belum login, redirect ke login.php
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Helper untuk menampilkan pesan flash (sukses/error) sekali pakai.
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Sanitasi input dasar untuk mencegah XSS.
 */
function clean(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * ----- BONUS: REMEMBER ME -----
 * Update field user tertentu (misal saat menyimpan/menghapus token).
 */
function updateUser(string $email, array $newData): void {
    $users = getUsers();
    foreach ($users as &$user) {
        if (strtolower($user['email']) === strtolower($email)) {
            $user = array_merge($user, $newData);
        }
    }
    unset($user);
    saveUsers($users);
}

/**
 * Buat cookie "Remember Me" berisi email + token acak.
 * Token disimpan (hash) di data user supaya bisa diverifikasi.
 */
function setRememberMeCookie(string $email): void {
    $token = bin2hex(random_bytes(32));
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);

    updateUser($email, ['remember_token' => $hashedToken]);

    // cookie berlaku 30 hari, isi: email:token (dipisah titik dua)
    $cookieValue = base64_encode($email . ':' . $token);
    setcookie('remember_me', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

/**
 * Hapus cookie & token remember me (dipanggil saat logout).
 */
function clearRememberMeCookie(): void {
    if (isset($_COOKIE['remember_me'])) {
        $decoded = base64_decode($_COOKIE['remember_me']);
        $parts = explode(':', $decoded, 2);
        if (count($parts) === 2) {
            updateUser($parts[0], ['remember_token' => null]);
        }
        setcookie('remember_me', '', time() - 3600, '/');
    }
}

/**
 * Coba login otomatis pakai cookie remember_me jika session belum ada.
 * Dipanggil sekali di awal (setelah session_start).
 */
function attemptAutoLogin(): void {
    if (isLoggedIn() || !isset($_COOKIE['remember_me'])) {
        return;
    }

    $decoded = base64_decode($_COOKIE['remember_me']);
    $parts = explode(':', $decoded, 2);
    if (count($parts) !== 2) {
        return;
    }

    [$email, $token] = $parts;
    $user = findUserByEmail($email);

    if ($user && !empty($user['remember_token']) && password_verify($token, $user['remember_token'])) {
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nama']  = $user['nama'];
    }
}

attemptAutoLogin();