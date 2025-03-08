function checkAdmin() {
    if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
        header("HTTP/1.1 403 Forbidden");
        exit("Admin access required");
    }
}

function validateCsrfToken() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        die("CSRF token validation failed");
    }
}
