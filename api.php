<?php
declare(strict_types=1);

/**
 * Hotel King Choice — Read-Only Pre-Launch API for React Frontend
 * Connects to king-choice-private/core.php & MySQL (kingozfc_hkc_db)
 * Online reservations & payments remain strictly disabled during construction.
 */

$privateDir = dirname(__DIR__) . '/king-choice-private';
if (!is_dir($privateDir)) {
    $privateDir = __DIR__ . '/king-choice-private';
}
require_once $privateDir . '/core.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? 'status';

// Block any POST attempt to book or pay while under construction
if ($method !== 'GET') {
    http_response_code(503);
    echo json_encode([
        'ok'      => false,
        'status'  => 'UNDER_CONSTRUCTION_DISABLED',
        'message' => 'Hotel King Choice is currently under construction. Online availability checks, reservations, and payments are disabled. Please call +91 9699066190 for future-stay enquiries.'
    ]);
    exit;
}

try {
    $pdo = hkc_get_pdo();

    // 1. Fetch hotel system settings from MySQL
    $settingsStmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
    $settings = [];
    foreach ($settingsStmt->fetchAll() as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    // 2. Fetch planned room categories from MySQL
    $roomsStmt = $pdo->query("
        SELECT id, slug, name, short_summary, planned_quantity,
               verified_operational_quantity, is_inventory_verified,
               max_adults, max_children, base_tariff_inr, is_active
        FROM room_types
        ORDER BY id ASC
    ");
    $rooms = $roomsStmt->fetchAll();

    echo json_encode([
        'ok'             => true,
        'db_connected'   => true,
        'hotel_status'   => $settings['hotel_status'] ?? 'UNDER_CONSTRUCTION',
        'booking_live'   => false,
        'room_categories'=> $rooms,
        'timestamp_ist'  => date('Y-m-d H:i:s')
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'           => false,
        'db_connected' => false,
        'message'      => 'Pre-launch mode active.'
    ]);
}