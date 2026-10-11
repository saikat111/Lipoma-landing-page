<?php
require_once __DIR__ . '/auth.php';
require_admin_auth();

$db = get_db();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Save Website & Marketing Settings
    $settings_to_update = [
        'site_title'           => trim($_POST['site_title'] ?? ''),
        'product_name'         => trim($_POST['product_name'] ?? ''),
        'hotline'              => trim($_POST['hotline'] ?? ''),
        'whatsapp'             => trim($_POST['whatsapp'] ?? ''),
        'package_1_title'      => trim($_POST['package_1_title'] ?? ''),
        'package_1_price'      => trim($_POST['package_1_price'] ?? '1580'),
        'package_1_regular'    => trim($_POST['package_1_regular'] ?? '2370'),
        'package_1_badge'      => trim($_POST['package_1_badge'] ?? ''),
        'package_2_title'      => trim($_POST['package_2_title'] ?? ''),
        'package_2_price'      => trim($_POST['package_2_price'] ?? '790'),
        'package_2_regular'    => trim($_POST['package_2_regular'] ?? '990'),
        'package_2_badge'      => trim($_POST['package_2_badge'] ?? ''),
        'delivery_dhaka'       => trim($_POST['delivery_dhaka'] ?? '70'),
        'delivery_outside'     => trim($_POST['delivery_outside'] ?? '130'),
        'meta_pixel_id'        => trim($_POST['meta_pixel_id'] ?? ''),
        'meta_pixel_active'    => isset($_POST['meta_pixel_active']) ? '1' : '0',
        'custom_header_script'        => trim($_POST['custom_header_script'] ?? ''),
        'custom_footer_script'        => trim($_POST['custom_footer_script'] ?? ''),
        'fake_order_protection_days'  => trim($_POST['fake_order_protection_days'] ?? '7')
    ];

    foreach ($settings_to_update as $k => $v) {
        set_setting($k, $v);
    }

    // 2. Optional Password Change
    $new_password = trim($_POST['new_password'] ?? '');
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
        } else {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE admins SET password_hash = :hash WHERE id = :id");
            $stmt->execute([':hash' => $hash, ':id' => $_SESSION['admin_id']]);
            $message = 'সেটিংস এবং অ্যাডমিন পাসওয়ার্ড সফলভাবে আপডেট করা হয়েছে!';
        }
    } else {
        $message = 'ওয়েবসাইট সেটিংস সফলভাবে সংরক্ষিত হয়েছে!';
    }
}

// Read current settings
$site_title           = get_setting('site_title', 'সাউথ মুন লাইপোমা রিমুভাল স্প্রে | 100% অরিজিনাল');
$product_name         = get_setting('product_name', 'South Moon Lipoma Removal Spray');
$hotline              = get_setting('hotline', '01872307272');
$whatsapp             = get_setting('whatsapp', '01872307272');
$pkg1_title           = get_setting('package_1_title', 'Buy 2 Get 1 Free (৩টি স্প্রে)');
$pkg1_price           = get_setting('package_1_price', '1580');
$pkg1_regular         = get_setting('package_1_regular', '2370');
$pkg1_badge           = get_setting('package_1_badge', 'সেরা অফার (Buy 2 Get 1)');
$pkg2_title           = get_setting('package_2_title', '১টি স্প্রে');
$pkg2_price           = get_setting('package_2_price', '790');
$pkg2_regular         = get_setting('package_2_regular', '990');
$pkg2_badge           = get_setting('package_2_badge', 'স্ট্যান্ডার্ড প্যাক');
$delivery_dhaka       = get_setting('delivery_dhaka', '70');
$delivery_outside     = get_setting('delivery_outside', '130');
$meta_pixel_id        = get_setting('meta_pixel_id', '');
$meta_pixel_active    = get_setting('meta_pixel_active', '0') === '1';
$custom_header_script        = get_setting('custom_header_script', '');
$custom_footer_script        = get_setting('custom_footer_script', '');
$fake_order_protection_days  = (int) get_setting('fake_order_protection_days', 7);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ওয়েবসাইট ও পিক্সেল সেটিংস | অ্যাডমিন প্যানেল</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/l.png">
    <link rel="shortcut icon" type="image/png" href="../assets/images/l.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap">

    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Hind Siliguri', 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; padding-bottom: 60px; }

        .admin-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-brand { font-size: 1.25rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
        .nav-links { display: flex; align-items: center; gap: 15px; }
        .nav-link-btn {
            color: #334155; font-weight: 600; font-size: 0.95rem; padding: 8px 14px; border-radius: 6px;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .nav-link-btn.active, .nav-link-btn:hover { background: #f1f5f9; color: var(--primary); }
        .btn-logout { background: #fee2e2; color: #be123c; }

        .admin-container { max-width: 900px; margin: 30px auto; padding: 0 20px; }

        .settings-card {
            background: #ffffff; border-radius: 14px; border: 1px solid var(--border);
            padding: 28px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); margin-bottom: 25px;
        }
        .card-header-title {
            font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 18px;
            padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; display: flex; align-items: center; gap: 10px;
        }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 0.95rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 11px 14px; border: 1.5px solid var(--border);
            border-radius: 8px; font-size: 0.98rem; outline: none; transition: border-color 0.2s;
        }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12); }
        textarea.form-input { font-family: monospace; font-size: 0.9rem; min-height: 90px; }

        .checkbox-label {
            display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 1rem; font-weight: 600; color: #0f172a;
        }
        .checkbox-label input { width: 18px; height: 18px; cursor: pointer; }

        .btn-save {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff; border: none; padding: 14px 28px; border-radius: 8px; font-size: 1.1rem;
            font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            display: inline-flex; align-items: center; gap: 8px; transition: transform 0.2s;
        }
        .btn-save:hover { transform: translateY(-2px); }

        .alert-box {
            background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;
            padding: 12px 16px; border-radius: 8px; margin-bottom: 25px; font-weight: 600;
        }
        .alert-error {
            background: #fee2e2; border: 1px solid #fecaca; color: #991b1b;
            padding: 12px 16px; border-radius: 8px; margin-bottom: 25px; font-weight: 600;
        }
        @media (max-width: 700px) {
            .form-grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <nav class="admin-nav">
        <div class="nav-brand">
            <img src="../assets/images/logo.svg" alt="South Moon Logo" style="height: 38px;">
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link-btn"><i class="fa-solid fa-list-check"></i> অর্ডার তালিকা</a>
            <a href="settings.php" class="nav-link-btn active"><i class="fa-solid fa-gear"></i> ওয়েবসাইট সেটিংস</a>
            <a href="../index.php" target="_blank" class="nav-link-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i> ল্যান্ডিং পেজ</a>
            <a href="logout.php" class="nav-link-btn btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> লগআউট</a>
        </div>
    </nav>

    <div class="admin-container">

        <?php if (!empty($message)): ?>
            <div class="alert-box"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="settings.php">

            <!-- Marketing & Meta Pixel Section -->
            <div class="settings-card" style="border: 2px solid #38bdf8;">
                <div class="card-header-title">
                    <i class="fa-brands fa-facebook" style="color: #1877f2;"></i>
                    <span>মেটা পিক্সেল ও ট্র্যাকিং সেটিংস (Meta Pixel)</span>
                </div>
                <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 16px;">
                    ফেসবুক অ্যাড চালানোর জন্য আপনার মেটা পিক্সেল আইডি এখানে দিন। সিস্টেম স্বয়ংক্রিয়ভাবে PageView, ViewContent, InitiateCheckout এবং Thank You পেজে Purchase ইভেন্ট ফায়ার করবে।
                </p>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">মেটা পিক্সেল আইডি (Meta Pixel ID)</label>
                        <input type="text" name="meta_pixel_id" class="form-input" placeholder="যেমন: 123456789012345" value="<?= htmlspecialchars($meta_pixel_id) ?>">
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; margin-top: 25px;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="meta_pixel_active" value="1" <?= $meta_pixel_active ? 'checked' : '' ?>>
                            <span>মেটা পিক্সেল ট্র্যাকিং সক্রিয় করুন (Enable Pixel)</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">কাস্টম হেডার স্ক্রিপ্ট (&lt;head&gt; - GTM / TikTok Pixel ইত্যাদি)</label>
                    <textarea name="custom_header_script" class="form-input" placeholder="<!-- অতিরিক্ত কোনো হেডার স্ক্রিপ্ট থাকলে এখানে পেস্ট করুন -->"><?= htmlspecialchars($custom_header_script) ?></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">কাস্টম ফুটার স্ক্রিপ্ট (&lt;/body&gt; এর পূর্বে)</label>
                    <textarea name="custom_footer_script" class="form-input" placeholder="<!-- ফুটার স্ক্রিপ্ট -->"><?= htmlspecialchars($custom_footer_script) ?></textarea>
                </div>
            </div>

            <!-- Pricing & Packages Section -->
            <div class="settings-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-tags" style="color: #0284c7;"></i>
                    <span>প্রোডাক্ট প্যাকেজ ও মূল্য নির্ধারণ</span>
                </div>

                <!-- Package 1 (Combo / Buy 2 Get 1) -->
                <div style="background: #f8fafc; padding: 18px; border-radius: 10px; margin-bottom: 18px; border: 1px solid var(--border);">
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: #0284c7; margin-bottom: 12px;">প্যাকেজ ১ (কম্বো / বেস্ট ভ্যালু অফার)</h4>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">প্যাকেজের শিরোনাম</label>
                            <input type="text" name="package_1_title" class="form-input" value="<?= htmlspecialchars($pkg1_title) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ব্যাজ টেক্সট</label>
                            <input type="text" name="package_1_badge" class="form-input" value="<?= htmlspecialchars($pkg1_badge) ?>">
                        </div>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">অফার মূল্য (৳)</label>
                            <input type="number" step="any" name="package_1_price" class="form-input" value="<?= htmlspecialchars($pkg1_price) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">রেগুলার মূল্য (কাটা দাগের জন্য) (৳)</label>
                            <input type="number" step="any" name="package_1_regular" class="form-input" value="<?= htmlspecialchars($pkg1_regular) ?>">
                        </div>
                    </div>
                </div>

                <!-- Package 2 (Single) -->
                <div style="background: #f8fafc; padding: 18px; border-radius: 10px; border: 1px solid var(--border);">
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: #0284c7; margin-bottom: 12px;">প্যাকেজ ২ (একক প্যাক)</h4>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">প্যাকেজের শিরোনাম</label>
                            <input type="text" name="package_2_title" class="form-input" value="<?= htmlspecialchars($pkg2_title) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ব্যাজ টেক্সট</label>
                            <input type="text" name="package_2_badge" class="form-input" value="<?= htmlspecialchars($pkg2_badge) ?>">
                        </div>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">অফার মূল্য (৳)</label>
                            <input type="number" step="any" name="package_2_price" class="form-input" value="<?= htmlspecialchars($pkg2_price) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">রেগুলার মূল্য (কাটা দাগের জন্য) (৳)</label>
                            <input type="number" step="any" name="package_2_regular" class="form-input" value="<?= htmlspecialchars($pkg2_regular) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Charges -->
            <div class="settings-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-truck" style="color: #10b981;"></i>
                    <span>ডেলিভারি চার্জ কনফিগারেশন</span>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">ঢাকার ভিতরে ডেলিভারি চার্জ (৳)</label>
                        <input type="number" step="any" name="delivery_dhaka" class="form-input" value="<?= htmlspecialchars($delivery_dhaka) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ঢাকার বাহিরে ডেলিভারি চার্জ (৳)</label>
                        <input type="number" step="any" name="delivery_outside" class="form-input" value="<?= htmlspecialchars($delivery_outside) ?>">
                    </div>
                </div>
            </div>

            <!-- Fake Order Protection Settings -->
            <div class="settings-card" style="border: 2px solid #10b981;">
                <div class="card-header-title">
                    <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i>
                    <span>ভুয়া অর্ডার প্রতিরোধ সেটিংস (Fake Order Protection)</span>
                </div>
                <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 16px;">
                    একই মোবাইল নাম্বার দিয়ে নির্দিষ্ট দিনের মধ্যে পুনরায় নতুন অর্ডার করা স্বয়ংক্রিয়ভাবে ব্লক করে ভুয়া অর্ডার ও ডেলিভারি লস প্রতিরোধ করুন।
                </p>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">কত দিনের মধ্যে একই নাম্বারে নতুন অর্ডার ব্লক থাকবে? (দিন)</label>
                        <input type="number" name="fake_order_protection_days" class="form-input" min="0" max="365" value="<?= htmlspecialchars($fake_order_protection_days) ?>">
                        <small style="color: #64748b; display: block; margin-top: 4px;">ডিফল্ট: ৭ দিন। (নিষ্ক্রিয় করতে 0 দিন দিন)</small>
                    </div>
                </div>
            </div>

            <!-- General Website & Contact Settings -->
            <div class="settings-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-sliders" style="color: #6366f1;"></i>
                    <span>সাধারণ তথ্য ও যোগাযোগ</span>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">সাইট টাইটেল</label>
                        <input type="text" name="site_title" class="form-input" value="<?= htmlspecialchars($site_title) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">প্রোডাক্টের নাম</label>
                        <input type="text" name="product_name" class="form-input" value="<?= htmlspecialchars($product_name) ?>">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">হটলাইন কল নাম্বার</label>
                        <input type="text" name="hotline" class="form-input" value="<?= htmlspecialchars($hotline) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp মেসেজ নাম্বার</label>
                        <input type="text" name="whatsapp" class="form-input" value="<?= htmlspecialchars($whatsapp) ?>">
                    </div>
                </div>
            </div>

            <!-- Security / Change Password -->
            <div class="settings-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-key" style="color: #e11d48;"></i>
                    <span>অ্যাডমিন পাসওয়ার্ড পরিবর্তন</span>
                </div>
                <div class="form-group" style="max-width: 400px; margin-bottom: 0;">
                    <label class="form-label">নতুন পাসওয়ার্ড দিন (পরিবর্তন না করতে চাইলে ফাঁকা রাখুন)</label>
                    <input type="password" name="new_password" class="form-input" placeholder="কমপক্ষে ৬ অক্ষর">
                </div>
            </div>

            <button type="submit" class="btn-save">
                <i class="fa-solid fa-floppy-disk"></i> সেটিংস সংরক্ষণ করুন
            </button>
        </form>

    </div>

</body>
</html>
