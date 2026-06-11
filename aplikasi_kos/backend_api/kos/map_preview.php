<?php
$url = $_GET['url'] ?? '';

// VALIDASI URL (WAJIB BIAR AMAN)
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo "URL tidak valid";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { margin: 0; padding: 0; }
        iframe { width: 100%; height: 100vh; border: 0; }
    </style>
</head>
<body>
    <iframe src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
</body>
</html>