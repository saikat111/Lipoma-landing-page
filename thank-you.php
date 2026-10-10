<?php
require_once __DIR__ . '/config.php';

$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order = null;

if ($order_id > 0) {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch();
    } catch (Exception $e) {
        $order = null;
    }
}

$site_title        = get_setting('site_title', 'সাউথ মুন লাইপোমা রিমুভাল স্প্রে');
$hotline           = get_setting('hotline', '01872307272');
$whatsapp          = get_setting('whatsapp', '01872307272');
$meta_pixel_id     = get_setting('meta_pixel_id', '');
$meta_pixel_active = (int) get_setting('meta_pixel_active', 0) === 1;
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার কনফার্মেশন | <?= htmlspecialchars($site_title) ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Meta / Facebook Pixel Tracking -->
    <?php if ($meta_pixel_active && !empty($meta_pixel_id)): ?>
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?= htmlspecialchars($meta_pixel_id) ?>');
    fbq('track', 'PageView');

    <?php if ($order): ?>
    // Fire Purchase Conversion Event
    fbq('track', 'Purchase', {
        content_name: '<?= htmlspecialchars($order['package_name']) ?>',
        content_type: 'product',
        value: <?= (float) $order['total_amount'] ?>,
        currency: 'BDT',
        order_id: '<?= htmlspecialchars($order['order_number']) ?>'
    });
    <?php endif; ?>
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?= htmlspecialchars($meta_pixel_id) ?>&ev=PageView&noscript=1"
    /></noscript>
    <?php endif; ?>

    <style>
        .thank-you-wrapper {
            max-width: 680px;
            margin: 40px auto 60px;
            background: #ffffff;
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .thank-you-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            padding: 35px 20px;
            text-align: center;
        }
        .success-checkmark {
            width: 70px;
            height: 70px;
            background: #ffffff;
            color: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            animation: bounceIn 0.8s ease;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        .thank-you-body {
            padding: 30px;
        }
        .order-meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .order-meta-table tr {
            border-bottom: 1px solid #f1f5f9;
        }
        .order-meta-table td {
            padding: 12px 6px;
            font-size: 1rem;
        }
        .order-meta-table td.label {
            color: #64748b;
            font-weight: 500;
            width: 40%;
        }
        .order-meta-table td.val {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }
        .instruction-box {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 25px;
            color: #0369a1;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .btn-back-home {
            display: block;
            text-align: center;
            background: #0f172a;
            color: #ffffff;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.05rem;
            transition: background 0.2s;
        }
        .btn-back-home:hover {
            background: #1e293b;
        }
    </style>
</head>
<body style="background-color: #f1f5f9;">

    <div class="container">
        <div class="thank-you-wrapper">
            <div style="background:#ffffff; padding:18px 20px 10px; text-align:center; border-bottom:1px solid #e2e8f0;">
                <img src="assets/images/logo.svg" alt="South Moon Lipoma Care Logo" style="height: 50px; margin: 0 auto; display: block;">
            </div>
            <div class="thank-you-header">
                <div class="success-checkmark">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 6px;">আলহামদুলিল্লাহ! আপনার অর্ডারটি গৃহীত হয়েছে</h1>
                <p style="font-size: 1.05rem; opacity: 0.95;">আমাদের টিম খুব শীঘ্রই আপনার সাথে যোগাযোগ করবে।</p>
            </div>

            <div class="thank-you-body">
                <?php if ($order): ?>
                <table class="order-meta-table">
                    <tr>
                        <td class="label">অর্ডার নম্বর:</td>
                        <td class="val" style="color: #0284c7; font-family: monospace; font-size: 1.1rem;"><?= htmlspecialchars($order['order_number']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">কাস্টমারের নাম:</td>
                        <td class="val"><?= htmlspecialchars($order['customer_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">মোবাইল নাম্বার:</td>
                        <td class="val"><?= htmlspecialchars($order['phone']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">ডেলিভারি ঠিকানা:</td>
                        <td class="val" style="word-break: break-word;"><?= nl2br(htmlspecialchars($order['address'])) ?></td>
                    </tr>
                    <tr>
                        <td class="label">প্যাকেজ ও পরিমাণ:</td>
                        <td class="val">
                            <?= htmlspecialchars($order['package_name']) ?>
                            <span style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:10px; font-size:0.85rem; margin-left:4px;">
                                <?= (int)$order['quantity'] ?> টি
                            </span>
                            <div style="font-size:0.85rem; color:#64748b; font-weight:normal;">
                                (একক মূল্য ৳<?= number_format($order['package_price']) ?> × <?= (int)$order['quantity'] ?> = ৳<?= number_format($order['package_price'] * (int)$order['quantity']) ?>)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">ডেলিভারি এলাকা:</td>
                        <td class="val"><?= htmlspecialchars($order['shipping_area']) ?> (৳<?= number_format($order['shipping_cost']) ?>)</td>
                    </tr>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <td class="label" style="font-size: 1.15rem; color: #0f172a; font-weight: 800;">সর্বমোট প্রদেয় টাকা:</td>
                        <td class="val" style="font-size: 1.35rem; color: var(--danger); font-weight: 800;">৳<?= number_format($order['total_amount']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">পেমেন্ট মেথড:</td>
                        <td class="val"><span style="background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 12px; font-size: 0.85rem;">ক্যাশ অন ডেলিভারি</span></td>
                    </tr>
                </table>

                <div class="instruction-box">
                    <strong><i class="fa-solid fa-circle-info"></i> পরবর্তী করণীয়:</strong><br>
                    • আমাদের একজন কাস্টমার কেয়ার প্রতিনিধি আপনার ফোন নাম্বারে কল করে অর্ডারটি কনফার্ম করবেন।<br>
                    • ঢাকার ভেতরে ২৪ থেকে ৪৮ ঘণ্টার মধ্যে এবং ঢাকার বাহিরে ২ থেকে ৩ কার্যদিবসের মধ্যে পার্সেল পৌঁছাবে।<br>
                    • পার্সেলটি গ্রহণ করার সময় ডেলিভারি ম্যানের কাছে সর্বমোট <strong>৳<?= number_format($order['total_amount']) ?></strong> পরিশোধ করবেন।
                </div>

                <div style="text-align: center; margin-bottom: 20px; font-size: 0.95rem; color: #64748b;">
                    যেকোনো প্রয়োজনে কল করুন: <a href="tel:<?= htmlspecialchars($hotline) ?>" style="color: #0284c7; font-weight: 700;"><?= htmlspecialchars($hotline) ?></a>
                </div>

                <?php else: ?>
                <div style="text-align: center; padding: 30px 10px;">
                    <p style="font-size: 1.1rem; color: #64748b;">অর্ডার তথ্য খুঁজে পাওয়া যায়নি।</p>
                </div>
                <?php endif; ?>

                <a href="index.php" class="btn-back-home">
                    <i class="fa-solid fa-house"></i> হোম পেজে ফিরে যান
                </a>
            </div>
        </div>
    </div>

</body>
</html>
