<?php
/**
 * Shared API helpers: CORS, response, auth
 */

// CORS headers for React frontend
header('Content-Type: application/json; charset=utf-8');
$allowed_origin = "https://round68-react-mv-ecommerce.netlify.app";

if ($_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
}

// header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function respond_error(string $message, int $code = 400): void {
    respond(['success' => false, 'message' => $message], $code);
}

function respond_success($data = [], string $message = 'OK'): void {
    respond(['success' => true, 'message' => $message, 'data' => $data]);
}

/**
 * Decode JSON body (for POST with application/json content-type)
 */
function get_json_body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Get input from POST, JSON body, or GET
 */
function input(string $key, $default = null) {
    if (isset($_POST[$key])) return $_POST[$key];
    $body = get_json_body();
    if (isset($body[$key])) return $body[$key];
    if (isset($_GET[$key])) return $_GET[$key];
    return $default;
}

/**
 * Simple JWT-like token using base64 (stateless, signed with secret)
 * Not production-grade — swap with a real JWT library for production.
 */
define('TOKEN_SECRET', 'marketplace_secret_key_2026');
define('TOKEN_EXPIRY', 86400 * 7); // 7 days

function generate_token(array $payload): string {
    $payload['exp'] = time() + TOKEN_EXPIRY;
    $encoded = base64_encode(json_encode($payload));
    $sig = hash_hmac('sha256', $encoded, TOKEN_SECRET);
    return $encoded . '.' . $sig;
}

function verify_token(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 2) return null;
    [$encoded, $sig] = $parts;
    $expected = hash_hmac('sha256', $encoded, TOKEN_SECRET);
    if (!hash_equals($expected, $sig)) return null;
    $payload = json_decode(base64_decode($encoded), true);
    if (!$payload || $payload['exp'] < time()) return null;
    return $payload;
}

/**
 * Require a valid token; returns the payload or exits with 401
 */
function require_auth(): array {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.+)/i', $header, $m)) {
        $payload = verify_token($m[1]);
        if ($payload) return $payload;
    }
    respond_error('Unauthorized', 401);
    exit(); // satisfy static analysis
}
