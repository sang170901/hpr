<?php
// Track visitor script - automatically logs each page visit
require_once __DIR__ . '/db.php';

function trackVisit($pageUrl = null) {
    try {
        $pdo = getPDO();
        
        // Create access_logs table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS access_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page_url TEXT,
            user_ip TEXT,
            user_agent TEXT,
            access_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            session_id TEXT,
            referrer TEXT
        )");
        
        // Get visitor information
        $pageUrl = $pageUrl ?: $_SERVER['REQUEST_URI'] ?? '/unknown';
        $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $sessionId = session_id() ?: 'no-session';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        // Clean up old logs (keep only last 30 days)
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        $pdo->exec("DELETE FROM access_logs WHERE access_time < '$thirtyDaysAgo'");
        
        // Insert new visit
        $stmt = $pdo->prepare("INSERT INTO access_logs (page_url, user_ip, user_agent, session_id, referrer) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$pageUrl, $userIp, $userAgent, $sessionId, $referrer]);
        
        return true;
    } catch (Exception $e) {
        error_log("Track visit error: " . $e->getMessage());
        return false;
    }
}

// Auto-track if called directly
if (!function_exists('debug_backtrace') || empty(debug_backtrace())) {
    trackVisit();
}
?>