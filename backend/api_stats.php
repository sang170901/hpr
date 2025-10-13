<?php
// Real-time statistics API endpoint
require_once __DIR__ . '/inc/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $pdo = getPDO();
    
    $todayStart = date('Y-m-d 00:00:00');
    $yesterdayStart = date('Y-m-d 00:00:00', strtotime('-1 day'));
    $yesterdayEnd = date('Y-m-d 23:59:59', strtotime('-1 day'));
    
    // Get current statistics
    $totalVisitsToday = $pdo->query("SELECT COUNT(*) FROM access_logs WHERE access_time >= '$todayStart'")->fetchColumn();
    $totalVisitsYesterday = $pdo->query("SELECT COUNT(*) FROM access_logs WHERE access_time BETWEEN '$yesterdayStart' AND '$yesterdayEnd'")->fetchColumn();
    
    $uniqueVisitorsToday = $pdo->query("SELECT COUNT(DISTINCT user_ip) FROM access_logs WHERE access_time >= '$todayStart'")->fetchColumn();
    $uniqueVisitorsYesterday = $pdo->query("SELECT COUNT(DISTINCT user_ip) FROM access_logs WHERE access_time BETWEEN '$yesterdayStart' AND '$yesterdayEnd'")->fetchColumn();
    
    // Calculate changes
    $visitChange = $totalVisitsYesterday > 0 ? round((($totalVisitsToday - $totalVisitsYesterday) / $totalVisitsYesterday) * 100) : 0;
    $uniqueChange = $uniqueVisitorsYesterday > 0 ? round((($uniqueVisitorsToday - $uniqueVisitorsYesterday) / $uniqueVisitorsYesterday) * 100) : 0;
    
    // Page statistics
    $pageStats = $pdo->query("
        SELECT 
            page_url,
            COUNT(*) as visits,
            COUNT(DISTINCT user_ip) as unique_visitors
        FROM access_logs 
        WHERE access_time >= '$todayStart'
        GROUP BY page_url 
        ORDER BY visits DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'data' => [
            'totalVisitsToday' => $totalVisitsToday,
            'uniqueVisitorsToday' => $uniqueVisitorsToday,
            'visitChange' => $visitChange,
            'uniqueChange' => $uniqueChange,
            'currentTime' => date('H:i'),
            'pageStats' => $pageStats,
            'lastUpdate' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>