<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'অননুমোদিত রিকোয়েস্ট মেথড।']);
    exit;
}

// 1. Verify CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf($token)) {
    echo json_encode(['success' => false, 'message' => 'সিকিউরিটি টোকেন মিসিং বা মেয়াদোত্তীর্ণ। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।']);
    exit;
}

// 2. Validate Customer Details
$customer_name = sanitize($_POST['customer_name'] ?? '');
$raw_phone     = trim($_POST['phone'] ?? '');
$address       = sanitize($_POST['address'] ?? '');
$note          = sanitize($_POST['note'] ?? '');
$selected_pkg  = trim($_POST['selected_package'] ?? 'pkg1');
$shipping_area = trim($_POST['shipping_area'] ?? 'dhaka');

if (empty($customer_name) || mb_strlen($customer_name) < 2) {
    echo json_encode(['success' => false, 'message' => 'অনুগ্রহ করে আপনার পুরো নাম সঠিকভাবে লিখুন।']);
    exit;
}

// Clean phone number (strip whitespace, hyphens, country code)
$phone = preg_replace('/[^0-9]/', '', $raw_phone);
if (str_starts_with($phone, '8801')) {
    $phone = substr($phone, 2);
}

if (!preg_match('/^01[3-9]\d{8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'সঠিক ১১ ডিজিটের মোবাইল নাম্বার দিন (যেমন: 017xxxxxxxx বা 018xxxxxxxx)।']);
    exit;
}

if (empty($address) || mb_strlen($address) < 5) {
    echo json_encode(['success' => false, 'message' => 'অনুগ্রহ করে আপনার সম্পূর্ণ ডেলিভারি ঠিকানা বিস্তারিত লিখুন।']);
    exit;
}

// 3. Determine Package and Pricing from Server Settings
if ($selected_pkg === 'pkg2') {
    $package_name  = get_setting('package_2_title', '১টি স্প্রে');
    $package_price = (float) get_setting('package_2_price', 790);
} else {
    $package_name  = get_setting('package_1_title', 'Buy 2 Get 1 Free (৩টি স্প্রে)');
    $package_price = (float) get_setting('package_1_price', 1580);
}

// 4. Determine Shipping Cost
if ($shipping_area === 'outside') {
    $shipping_label = 'ঢাকার বাহিরে';
    $shipping_cost  = (float) get_setting('delivery_outside', 130);
} else {
    $shipping_label = 'ঢাকার ভিতরে';
    $shipping_cost  = (float) get_setting('delivery_dhaka', 70);
}

$total_amount = $package_price + $shipping_cost;
$ip_address   = $_SERVER['REMOTE_ADDR'] ?? '';

// Generate Human-Readable Order Number
$order_number = 'SM-' . date('ymd') . '-' . mt_rand(1000, 9999);

try {
    $db = get_db();
    $sql = "INSERT INTO orders (
        order_number, customer_name, phone, address, 
        package_name, package_price, quantity, shipping_area, 
        shipping_cost, total_amount, note, status, ip_address, created_at
    ) VALUES (
        :order_number, :customer_name, :phone, :address,
        :package_name, :package_price, 1, :shipping_area,
        :shipping_cost, :total_amount, :note, 'pending', :ip_address, CURRENT_TIMESTAMP
    )";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':order_number'  => $order_number,
        ':customer_name' => $customer_name,
        ':phone'         => $phone,
        ':address'       => $address,
        ':package_name'  => $package_name,
        ':package_price' => $package_price,
        ':shipping_area' => $shipping_label,
        ':shipping_cost' => $shipping_cost,
        ':total_amount'  => $total_amount,
        ':note'          => $note,
        ':ip_address'    => $ip_address
    ]);

    $order_id = (int) $db->lastInsertId();

    echo json_encode([
        'success'      => true,
        'order_id'     => $order_id,
        'order_number' => $order_number,
        'redirect'     => 'thank-you.php?id=' . $order_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'ডাটাবেসে অর্ডার সেভ করতে সমস্যা হয়েছে: ' . $e->getMessage()
    ]);
}
