<?php
require_once 'config.php';

// Hapus cookie & token remember me jika ada
clearRememberMeCookie();

// Hapus semua data session
$_SESSION = [];
session_destroy();

// Mulai session baru khusus untuk pesan flash
session_start();
setFlash('success', 'Kamu berhasil logout.');

header('Location: login.php');
exit;