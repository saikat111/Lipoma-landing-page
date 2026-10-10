<?php
require_once __DIR__ . '/auth.php';
require_admin_auth();

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($order_id <= 0) {
    die('ভুল অর্ডার আইডি।');
}

$db = get_db();
$stmt = $db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch();

if (!$order) {
    die('অর্ডার খুঁজে পাওয়া যায়নি।');
}

$site_title   = get_setting('site_title', 'সাউথ মুন বাংলাদেশ');
$product_name = get_setting('product_name', 'South Moon Lipoma Removal Spray');
$hotline      = get_setting('hotline', '01872307272');
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ইনভয়েস - <?= htmlspecialchars($order['order_number']) ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=Inter:wght@400;600;700&display=swap">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Hind Siliguri', 'Inter', sans-serif; }
        body { background: #f1f5f9; padding: 30px 15px; color: #1e293b; }
        .invoice-card {
            max-width: 750px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .invoice-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .company-info h2 { font-size: 1.5rem; color: #0284c7; font-weight: 800; }
        .company-info p { font-size: 0.9rem; color: #64748b; margin-top: 4px; }
        .invoice-meta { text-align: right; }
        .invoice-meta h3 { font-size: 1.3rem; color: #0f172a; font-weight: 700; }
        .invoice-meta p { font-size: 0.9rem; color: #64748b; margin-top: 3px; font-family: monospace; }
        
        .customer-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 25px;
        }
        .customer-section h4 { font-size: 1rem; color: #0f172a; margin-bottom: 6px; }
        .customer-section p { font-size: 0.95rem; color: #475569; margin: 2px 0; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background: #f8fafc;
            padding: 10px 14px;
            text-align: left;
            font-size: 0.88rem;
            color: #475569;
            border-bottom: 1.5px solid #cbd5e1;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.95rem;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .totals-table {
            width: 320px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 12px;
            font-size: 1rem;
        }
        .totals-table td.label { color: #64748b; }
        .totals-table td.val { text-align: right; font-weight: 700; color: #0f172a; }
        .totals-table tr.total-due {
            border-top: 2px solid #0f172a;
            font-size: 1.25rem;
        }
        .totals-table tr.total-due td { color: #e11d48; font-weight: 800; padding-top: 10px; }

        .print-btn-bar {
            text-align: center;
            margin-bottom: 25px;
        }
        .btn-print {
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover { background: #0369a1; }

        @media print {
            body { background: #ffffff; padding: 0; }
            .invoice-card { box-shadow: none; border: none; padding: 0; }
            .print-btn-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <button class="btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> ইনভয়েস প্রিন্ট করুন
        </button>
    </div>

    <div class="invoice-card">
        <div class="invoice-top">
            <div class="company-info">
                <img src="../assets/images/logo.svg" alt="South Moon Logo" style="height: 48px; margin-bottom: 8px;">
                <p>হটলাইন: <?= htmlspecialchars($hotline) ?></p>
                <p>ক্যাশ অন ডেলিভারি হোম ডেলিভারি পার্সেল</p>
            </div>
            <div class="invoice-meta">
                <h3>কাস্টমার ইনভয়েস</h3>
                <p>ইনভয়েস #: <?= htmlspecialchars($order['order_number']) ?></p>
                <p>তারিখ: <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                <span style="display:inline-block; margin-top:6px; background:#dcfce7; color:#15803d; padding:2px 10px; border-radius:12px; font-size:0.8rem; font-weight:700;">
                    ক্যাশ অন ডেলিভারি (COD)
                </span>
            </div>
        </div>

        <div class="customer-section">
            <h4>প্রাপকের বিবরণ:</h4>
            <p><strong>নাম:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><strong>মোবাইল:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            <p><strong>ঠিকানা:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
            <?php if (!empty($order['note'])): ?>
                <p><strong>নোট:</strong> <?= htmlspecialchars($order['note']) ?></p>
            <?php endif; ?>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>বিবরণ</th>
                    <th style="text-align: center;">পরিমাণ</th>
                    <th style="text-align: right;">একক মূল্য</th>
                    <th style="text-align: right;">মোট</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $qty = (int)($order['quantity'] ?? 1);
                $subtotal = (float)$order['package_price'] * $qty;
                ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($order['package_name']) ?></strong>
                        <div style="font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($product_name) ?></div>
                    </td>
                    <td style="text-align: center; font-weight: 700;"><?= $qty ?></td>
                    <td style="text-align: right;">৳<?= number_format($order['package_price']) ?></td>
                    <td style="text-align: right; font-weight: 700;">৳<?= number_format($subtotal) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">প্রোডাক্ট সাবটোটাল:</td>
                    <td class="val">৳<?= number_format($subtotal) ?></td>
                </tr>
                <tr>
                    <td class="label">ডেলিভারি চার্জ (<?= htmlspecialchars($order['shipping_area']) ?>):</td>
                    <td class="val">৳<?= number_format($order['shipping_cost']) ?></td>
                </tr>
                <tr class="total-due">
                    <td class="label">সংগ্রহযোগ্য টাকা (COD):</td>
                    <td class="val">৳<?= number_format($order['total_amount']) ?></td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px; font-size: 0.9rem; color: #94a3b8;">
            আমাদের সাথে থাকার জন্য আপনাকে ধন্যবাদ! পণ্য সংক্রান্ত যেকোনো তথ্যে হটলাইনে যোগাযোগ করুন।
        </div>
    </div>

</body>
</html>
