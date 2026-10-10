<?php
require_once __DIR__ . '/auth.php';
require_admin_auth();

$db = get_db();

$status_filter = trim($_GET['status'] ?? '');
$search_query  = trim($_GET['q'] ?? '');

$where_clauses = [];
$params = [];

if (!empty($status_filter)) {
    $where_clauses[] = "status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($search_query)) {
    $where_clauses[] = "(order_number LIKE :q OR customer_name LIKE :q OR phone LIKE :q OR address LIKE :q)";
    $params[':q'] = "%$search_query%";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

$stmt = $db->prepare("SELECT * FROM orders $where_sql ORDER BY id DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

$filename = 'courier_orders_' . date('Y-m-d_His') . '.csv';

// Output CSV headers
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Write UTF-8 BOM so Excel opens Bangla characters without corrupted encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Header row (Compatible with Steadfast, Pathao, RedX courier import)
fputcsv($output, [
    'Invoice / Order ID',
    'Customer Name',
    'Phone Number',
    'Full Delivery Address',
    'COD Amount (Tk)',
    'Package / Item Description',
    'Delivery Area',
    'Delivery Charge',
    'Order Status',
    'Order Date & Time'
]);

foreach ($orders as $o) {
    fputcsv($output, [
        $o['order_number'],
        $o['customer_name'],
        $o['phone'],
        $o['address'],
        $o['total_amount'],
        $o['package_name'],
        $o['shipping_area'],
        $o['shipping_cost'],
        strtoupper($o['status']),
        $o['created_at']
    ]);
}

fclose($output);
exit;
