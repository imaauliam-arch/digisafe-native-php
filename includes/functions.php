<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function verify_csrf(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function log_activity(PDO $pdo, string $type, string $description, ?string $targetType = null, ?int $targetId = null): void {
    try {
        $adminId = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
        $stmt = $pdo->prepare('INSERT INTO ds_activity_logs (admin_id, activity_type, description, target_type, target_id, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$adminId, $type, mb_substr($description, 0, 255), $targetType, $targetId, $ip, $agent]);
    } catch (Throwable $ex) {
        error_log('DIGISAFE activity log error: ' . $ex->getMessage());
    }
}

function require_admin(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function check_comment_warning(string $comment): ?string
{
    $text = mb_strtolower(trim($comment));

    $blockedWords = [
        'anjing',
        'bangsat',
        'kontol',
        'memek',
        'ngentot',
        'brengsek',
        'tolol',
        'goblok',
        'bajingan'
    ];

    foreach ($blockedWords as $word) {
        $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($word, '/') . '(?![\p{L}\p{N}])/u';

        if (preg_match($pattern, $text)) {
            return 'Mengandung kata kasar atau umpatan.';
        }
    }

    preg_match_all('/https?:\/\/|www\./i', $text, $links);

    if (count($links[0]) >= 3) {
        return 'Terdeteksi terlalu banyak tautan atau kemungkinan spam.';
    }

    if (preg_match('/(.)\1{9,}/u', $text)) {
        return 'Terdeteksi pengulangan karakter yang berlebihan.';
    }

    return null;
}
