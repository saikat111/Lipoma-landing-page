<?php
require_once __DIR__ . '/config.php';

// Fetch settings
$site_title           = get_setting('site_title', 'সাউথ মুন লাইপোমা রিমুভাল স্প্রে');
$product_name         = get_setting('product_name', 'South Moon Lipoma Removal Spray');
$hotline              = get_setting('hotline', '01961116565');
$whatsapp             = get_setting('whatsapp', '01872307272');

$pkg1_title           = get_setting('package_1_title', 'Buy 2 Get 1 Free (৩টি স্প্রে)');
$pkg1_price           = (float) get_setting('package_1_price', 1580);
$pkg1_regular         = (float) get_setting('package_1_regular', 2370);
$pkg1_badge           = get_setting('package_1_badge', 'সেরা অফার (Buy 2 Get 1)');

$pkg2_title           = get_setting('package_2_title', '১টি স্প্রে');
$pkg2_price           = (float) get_setting('package_2_price', 790);
$pkg2_regular         = (float) get_setting('package_2_regular', 990);
$pkg2_badge           = get_setting('package_2_badge', 'স্ট্যান্ডার্ড প্যাক');

$delivery_dhaka       = (float) get_setting('delivery_dhaka', 70);
$delivery_outside     = (float) get_setting('delivery_outside', 130);

$meta_pixel_id        = get_setting('meta_pixel_id', '');
$meta_pixel_active    = (int) get_setting('meta_pixel_active', 0) === 1;

$custom_header_script = get_setting('custom_header_script', '');
$custom_footer_script = get_setting('custom_footer_script', '');

// =========================================================================
// Facebook Conversions API (CAPI) & Pixel Configuration
// =========================================================================
$fb_access_token = 'EAAMu0Kdh40ABStn4G53rxR5atbAN2zJ77K6LBPur5XYP1r62wBg14nIgo7fLPmnnbLZAZAIpcbV9bggdeIbw3tgNv1jpw7PMOH9lMdWZAvG6nZC5shfweZCFYsTwFSp741zAT3NWkdKxeJGw4zOp1qzAS5msCHp7ZAjeRF6mPOTwMwlsAQhrynbxmiMQDb1pTbegZDZD';
$fb_test_code    = 'TEST88584'; // Leave empty string '' in live production when done testing

// Target Pixel / Dataset IDs (Auto-uses database pixel if set, or defaults to datasets assigned to this token)
$fb_pixel_id_setting = get_setting('meta_pixel_id', '');
if (!empty($fb_pixel_id_setting)) {
    $fb_pixel_ids = [trim($fb_pixel_id_setting)];
} else {
    $fb_pixel_ids = ['830823070028776', '892119713796815', '1066781429691948', '1302647544977792', '28599535763018740'];
}

/**
 * Send Event to Meta Facebook Conversions API (CAPI)
 */
function send_fb_capi_event(string $event_name, string $event_id, array $custom_data = [], array $user_data = []): array {
    global $fb_access_token, $fb_test_code, $fb_pixel_ids;

    if (empty($fb_access_token) || empty($fb_pixel_ids)) {
        return ['success' => false, 'error' => 'Missing access token or pixel ID'];
    }

    // Capture User IP Address
    $client_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] 
        ?? $_SERVER['HTTP_X_REAL_IP'] 
        ?? (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]) : null) 
        ?? $_SERVER['REMOTE_ADDR'] 
        ?? '';

    // Capture User Agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Capture / Generate _fbp cookie (Facebook Browser ID)
    $fbp = $_COOKIE['_fbp'] ?? null;
    if (!$fbp) {
        $fbp = 'fb.1.' . time() . '.' . mt_rand(100000000, 999999999);
        if (!headers_sent()) {
            @setcookie('_fbp', $fbp, time() + (90 * 86400), '/');
        }
    }

    // Capture / Generate _fbc cookie (Facebook Click ID)
    $fbc = $_COOKIE['_fbc'] ?? null;
    if (!$fbc && !empty($_GET['fbclid'])) {
        $fbc = 'fb.1.' . time() . '.' . preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['fbclid']);
        if (!headers_sent()) {
            @setcookie('_fbc', $fbc, time() + (90 * 86400), '/');
        }
    }

    // Event Source URL
    $is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $source_url = ($is_https ? "https://" : "http://") . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');

    $event_payload = [
        'event_name'       => $event_name,
        'event_time'       => time(),
        'event_id'         => $event_id,
        'event_source_url' => $source_url,
        'action_source'    => 'website',
        'user_data'        => array_merge([
            'client_ip_address' => $client_ip,
            'client_user_agent' => $user_agent,
            'fbp'               => $fbp,
        ], array_filter(['fbc' => $fbc]), $user_data)
    ];

    if (!empty($custom_data)) {
        $event_payload['custom_data'] = $custom_data;
    }

    $post_data = ['data' => [$event_payload]];
    if (!empty($fb_test_code)) {
        $post_data['test_event_code'] = $fb_test_code;
    }

    $json_payload = json_encode($post_data);
    $mh = curl_multi_init();
    $handles = [];

    foreach ($fb_pixel_ids as $pid) {
        $ch = curl_init("https://graph.facebook.com/v19.0/" . $pid . "/events?access_token=" . urlencode($fb_access_token));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_POST              => true,
            CURLOPT_POSTFIELDS        => $json_payload,
            CURLOPT_HTTPHEADER        => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT_MS        => 3000,
            CURLOPT_CONNECTTIMEOUT_MS => 1500,
            CURLOPT_SSL_VERIFYPEER    => false,
            CURLOPT_SSL_VERIFYHOST    => false
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$pid] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    $responses = [];
    foreach ($handles as $pid => $ch) {
        $responses[$pid] = curl_multi_getcontent($ch);
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);

    return ['success' => true, 'event_id' => $event_id, 'responses' => $responses];
}

// Generate Unique Event IDs for PageView and ViewContent (for Browser + CAPI Deduplication)
$pv_event_id = 'pv_' . time() . '_' . bin2hex(random_bytes(4));
$vc_event_id = 'vc_' . time() . '_' . bin2hex(random_bytes(4));

// Handle AJAX CAPI Event Requests (e.g., InitiateCheckout from JS)
if (isset($_GET['fb_capi_event']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=UTF-8');
    $raw_input = file_get_contents('php://input');
    $req = json_decode($raw_input, true) ?: $_POST;
    $e_name = trim($req['event_name'] ?? 'PageView');
    $e_id   = trim($req['event_id'] ?? ('ajax_' . time() . '_' . bin2hex(random_bytes(4))));
    $c_data = $req['custom_data'] ?? [];
    $u_data = $req['user_data'] ?? [];
    $res = send_fb_capi_event($e_name, $e_id, $c_data, $u_data);
    echo json_encode($res);
    exit;
}

// Fire Server-Side PageView & ViewContent via CAPI on page load
$capi_pv_res = send_fb_capi_event('PageView', $pv_event_id);
$capi_vc_res = send_fb_capi_event('ViewContent', $vc_event_id, [
    'content_name' => $product_name,
    'content_type' => 'product',
    'currency'     => 'BDT',
    'value'        => $pkg1_price
]);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_title) ?></title>
    <meta name="description" content="South Moon Lipoma Removal Spray - অপারেশন ছাড়াই লাইপোমা ও চর্বির দলা দূর করার 100% নিরাপদ প্রাকৃতিক হার্বাল সমাধান। সারা দেশে ক্যাশ অন ডেলিভারি।">
    
    <!-- Open Graph for Facebook Ads & Sharing -->
    <meta property="og:title" content="<?= htmlspecialchars($site_title) ?>">
    <meta property="og:description" content="অপারেশন ছাড়াই কি লাইপোমা দূর করা সম্ভব? হ্যাঁ, সম্ভব! টানা একমাস ব্যবহারে পান লাইপোমা থেকে মুক্তি।">
    <meta property="og:image" content="assets/images/1.PNG">
    <meta property="og:type" content="product">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/l.png">
    <link rel="shortcut icon" type="image/png" href="assets/images/l.png">
    <link rel="apple-touch-icon" href="assets/images/l.png">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Medical CSS with Auto Cache Busting -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">

    <!-- Meta / Facebook Pixel Tracking with Conversions API (CAPI) Deduplication -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    t.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');

    <?php foreach ($fb_pixel_ids as $pid): ?>
    fbq('init', '<?= htmlspecialchars($pid) ?>');
    <?php endforeach; ?>

    // Deduplicated PageView (Matches Server CAPI event_id)
    fbq('track', 'PageView', {}, {eventID: '<?= $pv_event_id ?>'});

    // Deduplicated ViewContent (Matches Server CAPI event_id)
    fbq('track', 'ViewContent', {
        content_name: '<?= htmlspecialchars($product_name) ?>',
        content_type: 'product',
        currency: 'BDT',
        value: <?= $pkg1_price ?>
    }, {eventID: '<?= $vc_event_id ?>'});
    </script>
    <noscript>
    <?php foreach ($fb_pixel_ids as $pid): ?>
    <img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?= htmlspecialchars($pid) ?>&ev=PageView&noscript=1"/>
    <?php endforeach; ?>
    </noscript>
    <!-- End Meta Pixel Code -->

    <?php if (isset($_GET['test_capi'])): ?>
    <!-- CAPI Diagnostic Output:
    <?= htmlspecialchars(json_encode(['pv' => $capi_pv_res, 'vc' => $capi_vc_res], JSON_PRETTY_PRINT)) ?>
    -->
    <?php endif; ?>

    <!-- Custom Header Scripts -->
    <?php if (!empty($custom_header_script)): ?>
        <?= $custom_header_script ?>
    <?php endif; ?>
</head>
<body>


    <!-- <header class="top-notice-bar">
        <div class="container">
            <div class="notice-inner">
                <div class="pulse-badge">
                    <span class="pulse-dot"></span>
                    🔥 সীমিত সময়ের জন্য বিশেষ ছাড় চলছে!
                </div>
                <div class="top-notice-text">
                    সারা দেশে হোম ডেলিভারি ও পণ্য হাতে পেয়ে টাকা পরিশোধ (ক্যাশ অন ডেলিভারি)
                </div>
                <div class="top-contact-links">
                    <a href="tel:<?= htmlspecialchars($hotline) ?>" class="top-btn">
                        <i class="fa-solid fa-phone-volume"></i> কল: <?= htmlspecialchars($hotline) ?>
                    </a>
                </div>
            </div>
        </div>
    </header>  -->

    <!-- Main Navigation / Brand Bar -->
    <div class="site-header">
        <div class="container">
            <div class="header-inner">
                <div class="logo-wrap">
                    <a href="index.php" style="display: inline-flex; align-items: center; text-decoration: none;">
                        <img src="assets/images/logo.svg" alt="South Moon Lipoma Care Logo" class="site-logo">
                    </a>
                </div>

                <!-- 100% Original & Effectiveness Trust Badge -->
                <div class="header-trust-badge">
                    <div class="trust-badge-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div class="trust-badge-content">
                        <span class="trust-badge-title">★ 100% অরিজিনাল ও কার্যকরী ★</span>
                        <span class="trust-badge-subtitle">ন্যাচারাল ভেষজ ফর্মুলা গ্যারান্টি</span>
                    </div>
                </div>

                <a href="#order-section" class="header-cta-btn scroll-to-order btn-animated">
                    <i class="fa-solid fa-cart-shopping"></i> অর্ডার করতে চাই
                </a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-headline-wrap">
                <!-- <span class="hero-tag"><i class="fa-solid fa-shield-halved"></i> হার্বাল ও প্রাকৃতিক লাইপোমা কেয়ার</span> -->
                <h1 class="hero-title">
                    <span class="hero-title-main">অপারেশন ছাড়াই কি লাইপোমা দূর করা সম্ভব?</span>
                    <span class="hero-title-badge-wrap">
                        <span class="highlight-red">হ্যাঁ, শতভাগ সম্ভব!</span>
                    </span>
                </h1>
                <p class="hero-subtitle">
                    টানা একমাস ব্যবহারে খুব সহজেই পাবেন লাইপোমা ও চর্বির দলা থেকে <strong>শতভাগ মুক্তি ইনশাআল্লাহ</strong>, <span class="highlight-100">100% কার্যকরী গ্যারান্টি</span>।
                </p>
            </div>

            <div class="hero-grid">
                <!-- Hero Image Showcase -->
                <div class="hero-media-card">
                    <img src="assets/images/1.PNG" alt="<?= htmlspecialchars($product_name) ?> Poster" loading="eager">
                </div>

                <!-- Core Benefits & Real Bottle -->
                <div class="hero-features">
                    <div class="feature-pill">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-shield-virus"></i>
                        </div>
                        <div>
                            <h4>অপারেশন বা সার্জারির কোনো প্রয়োজন নেই</h4>
                            <p>কোনো প্রকার কাটা-ছেঁড়া ছাড়া ঘরে বসেই ব্যথাহীনভাবে চর্বির দলা নির্মূল করে।</p>
                        </div>
                    </div>

                    <div class="feature-pill">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-leaf"></i>
                        </div>
                        <div>
                            <h4>100% প্রাকৃতিক ভেষজ উপাদানে প্রস্তুত</h4>
                            <p>জাফরান, অ্যাস্ট্রাগালাস ও জিনসেং সমৃদ্ধ নিরাপদ চীনা হারবাল ফর্মুলা।</p>
                        </div>
                    </div>

                    <div class="feature-pill">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-droplet"></i>
                        </div>
                        <div>
                            <h4>চিটচিটে ভাব নেই ও দ্রুত শোষিত হয়</h4>
                            <p>স্প্রে ব্যবহারের পর ত্বকে চিটচিটে ভাব থাকে না এবং দ্রুত গভীরে প্রবেশ করে কাজ শুরু করে।</p>
                        </div>
                    </div>

                    <div class="feature-pill">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-venus-mars"></i>
                        </div>
                        <div>
                            <h4>নারী ও পুরুষ উভয়ের জন্য সমান কার্যকর</h4>
                            <p>ঘাড়, পিঠ, হাত, পা বা শরীরের যেকোনো স্থানের লাইপোমায় নিরাপদ ব্যবহারযোগ্য।</p>
                        </div>
                    </div>

                    <!-- Real In-Hand Product Photo for High Trust -->
                    <div style="background: #ffffff; border-radius: var(--radius-md); padding: 12px; border: 1.5px dashed #0284c7; display: flex; align-items: center; gap: 15px;">
                        <img src="assets/images/2.jpeg" alt="Original Real Product" style="width: 85px; height: 85px; object-fit: cover; border-radius: 8px;">
                        <div>
                            <span style="background: #e0f2fe; color: #0369a1; font-size: 0.8rem; font-weight: 700; padding: 2px 8px; border-radius: 10px;">আসল প্রোডাক্টের বাস্তব ছবি</span>
                            <h5 style="font-size: 0.98rem; font-weight: 700; color: #0f172a; margin-top: 4px;">অরিজিনাল সাউথ মুন লাইপোমা স্প্রে (৫০ মিলি)</h5>
                            <p style="font-size: 0.85rem; color: #64748b; margin: 0;">প্রোডাক্ট হাতে পেয়ে চেক করে সম্পূর্ণ মূল্য পরিশোধ করুন।</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Big Urgency CTA Banner -->
            <div class="hero-cta-banner">
                <div class="cta-banner-text">
                    <h3>⚡ বিশেষ ডিসকাউন্ট অফার চলছে!</h3>
                    <p>স্টক সীমিত — আজই অর্ডার করুন এবং দ্রুত লাইপোমার অস্বস্তি থেকে মুক্তি পান</p>
                </div>
                <a href="#order-section" class="btn-order-now scroll-to-order btn-animated">
                    <i class="fa-solid fa-hand-point-right"></i> অর্ডার করতে চাই
                </a>
            </div>
        </div>
    </section>

    <!-- Section 1: Pain Points & Symptoms (Emotional Hook) -->
    <section class="section-padding symptoms-section">
        <div class="container">
            <div class="section-title-wrap">
                <span class="section-tag tag-red">আপনার সমস্যাটি চিহ্নিত করুন</span>
                <h2 class="section-title">এই লক্ষণগুলো কি আপনার শরীরেও দেখা দিয়েছে?</h2>
                <p class="section-desc">শরীরের বিভিন্ন স্থানে ছোট বা বড় চর্বির দলা (Lipoma) সময়ের সাথে সাথে অস্বস্তি ও বিব্রতকর পরিস্থিতির সৃষ্টি করে।</p>
            </div>

            <div class="symptoms-grid" style="display: flex; justify-content: center; align-items: center;">
                <div style="max-width: 650px; width: 100%; text-align: center;">
                    <img src="assets/images/5.webp" alt="Types of Lipoma Tumors" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--border-color); margin: 0 auto; display: block; max-width: 100%;">
                </div>
                <!-- <div class="symptoms-list">
                    <div class="symptom-item">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>শরীরের কোথাও ত্বকের নিচে ছোট বা বড় চর্বিযুক্ত ফোলা রয়েছে?</span>
                    </div>
                    <div class="symptom-item">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>লাইপোমা (Lipoma)-এর কারণে অস্বস্তি বা মৃদু ব্যথা অনুভব করেন?</span>
                    </div>
                    <div class="symptom-item">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>ত্বকের ফোলাভাব (Skin Lump) নিয়ে জনসমক্ষে বিব্রত বোধ করছেন?</span>
                    </div>
                    <div class="symptom-item">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>অপারেশন বা সার্জারির ভয় পাচ্ছেন এবং প্রাকৃতিক সমাধান খুঁজছেন?</span>
                    </div>
                    <div class="symptom-item">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>বাজারে নানা প্রসাধনী ব্যবহার করেও কাঙ্ক্ষিত ফলাফল পাননি?</span>
                    </div>

                    <div class="solution-box">
                        <i class="fa-solid fa-circle-check"></i> যদি আপনার উত্তর <strong>"হ্যাঁ"</strong> হয়, তবে অপারেশন ছাড়াই সাউথ মুন লাইপোমা স্প্রে আপনার জন্য সবচেয়ে নিরাপদ ও স্থায়ী ঘরোয়া সমাধান।
                    </div>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Section 2: What is Lipoma & How it Works -->
    <section class="section-padding" style="background-color: #f8fafc;">
        <div class="container">
            <div class="section-title-wrap">
                <span class="section-tag tag-blue">চিকিৎসা বিজ্ঞান ও কার্যপদ্ধতি</span>
                <h2 class="section-title">লাইপোমা বা চর্বির দলা মূলত কী?</h2>
                <p class="section-desc">ত্বক ও মাংসপেশীর মাঝে জমে থাকা অস্বাভাবিক অতিরিক্ত চর্বির কোষগুচ্ছই হলো লাইপোমা।</p>
            </div>

            <div class="ingredients-card" style="margin-bottom: 35px;">
                <div>
                    <img src="assets/images/6.webp" alt="What is a fatty lump" style="border-radius: var(--radius-md);">
                </div>
                <div>
                    <h3 style="font-size: 1.5rem; color: var(--primary-deep); font-weight: 800; margin-bottom: 12px;">কীভাবে সাউথ মুন স্প্রে কাজ করে?</h3>
                    <p style="font-size: 1.05rem; color: #475569; margin-bottom: 14px;">
                        সাউথ মুন স্প্রে-এর সক্রিয় ভেষজ মলিকিউলগুলো সরাসরি ত্বকের এপিডার্মিস স্তর ভেদ করে চর্বির স্তরে প্রবেশ করে। এটি চর্বির শক্ত বন্ধন ভেঙে লাইপোলাইসিস (Lipolysis) প্রক্রিয়া সক্রিয় করে এবং ধীরে ধীরে চর্বির দলাকে গলিয়ে স্বাভাবিক করে তোলে।
                    </p>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                        <li style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #0f172a;">
                            <i class="fa-solid fa-check" style="color: var(--accent-dark);"></i> ফোলাভাব ও প্রদাহ দ্রুত হ্রাস করে
                        </li>
                        <li style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #0f172a;">
                            <i class="fa-solid fa-check" style="color: var(--accent-dark);"></i> ত্বক নরম ও স্বাভাবিক টানটান রাখে
                        </li>
                        <li style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #0f172a;">
                            <i class="fa-solid fa-check" style="color: var(--accent-dark);"></i> নতুন করে চর্বি জমতে বাধা প্রদান করে
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Natural Herbal Ingredients (4.webp) -->
            <div class="ingredients-card" style="display: flex; justify-content: center; align-items: center; max-width: 750px; margin: 0 auto;">
                <!-- <div>
                    <span class="section-tag tag-green">100% ভেষজ উপাদান</span>
                    <h3 style="font-size: 1.45rem; color: var(--primary-deep); font-weight: 800; margin-bottom: 12px;">প্রাকৃতিক চীনা হার্বাল উপাদানের সংমিশ্রণ</h3>
                    <p style="font-size: 1.05rem; color: #475569;">
                        রাসায়নিক স্টেরয়েড বা ক্ষতিকর উপাদানের পরিবর্তে এতে রয়েছে প্রাচীন চিকিৎসায় ব্যবহৃত দুর্লভ প্রাকৃতিক হার্বস:
                    </p>
                    <div class="ing-badge-list">
                        <span class="ing-badge"><i class="fa-solid fa-seedling" style="color: var(--accent-dark);"></i> জাফরান (Saffron)</span>
                        <span class="ing-badge"><i class="fa-solid fa-seedling" style="color: var(--accent-dark);"></i> অ্যাস্ট্রাগালাস (Astragalus)</span>
                        <span class="ing-badge"><i class="fa-solid fa-seedling" style="color: var(--accent-dark);"></i> একোনাইট (Aconite)</span>
                        <span class="ing-badge"><i class="fa-solid fa-seedling" style="color: var(--accent-dark);"></i> সালভিয়া (Salvia)</span>
                        <span class="ing-badge"><i class="fa-solid fa-seedling" style="color: var(--accent-dark);"></i> সিউডো-জিনসেং (Ginseng)</span>
                        <span class="ing-badge"><i class="fa-solid fa-seedling" style="color: var(--accent-dark);"></i> জেরানিয়াম (Geranium)</span>
                    </div>
                </div> -->
                <div style="max-width: 650px; width: 100%; text-align: center;">
                    <img src="assets/images/4.webp" alt="South Moon Herbal Ingredients" style="border-radius: var(--radius-md); margin: 0 auto; display: block; max-width: 100%;">
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Usage Instructions (3.jpg.jpeg) -->
    <section class="section-padding" style="background-color: #ffffff;">
        <div class="container">
            <div class="section-title-wrap">
                <span class="section-tag tag-blue">ব্যবহারের সহজ নিয়ম</span>
                <h2 class="section-title">সাউথ মুন লাইপোমা স্প্রে ব্যবহারের সঠিক নিয়মাবলী</h2>
                <p class="section-desc">নিয়মিত সঠিক নিয়মে ব্যবহারে দ্রুত ও স্থায়ী কাঙ্ক্ষিত ফল পাওয়া যায়।</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">১</div>
                    <h4>আক্রান্ত স্থান পরিষ্কার করুন</h4>
                    <p>স্প্রে ব্যবহারের আগে স্থানটি সাবান ও হালকা কুসুম গরম পানি দিয়ে ভালো করে ধুয়ে শুকিয়ে নিন।</p>
                </div>

                <div class="step-card">
                    <div class="step-number">২</div>
                    <h4>পরিমাপমতো স্প্রে করুন</h4>
                    <p>লাইপোমার উপর স্প্রে বোতলটি ৩-৪ ইঞ্চি দূরে ধরে সরাসরি পর্যাপ্ত পরিমাণে স্প্রে করুন।</p>
                </div>

                <div class="step-card">
                    <div class="step-number">৩</div>
                    <h4>আলতো হাতে ম্যাসাজ করুন</h4>
                    <p>স্প্রে করার পর হালকা হাতে ২-৩ মিনিট চক্রাকারে ম্যাসাজ করুন যাতে ওষুধটি ত্বকে পুরোপুরি শোষিত হয়।</p>
                </div>

                <div class="step-card">
                    <div class="step-number">৪</div>
                    <h4>দিনে ২ বার নিয়মিত ব্যবহার</h4>
                    <p>সর্বোচ্চ ফলাফলের জন্য প্রতিদিন সকালে ১ বার ও রাতে ঘুমানোর পূর্বে ১ বার ব্যবহার করুন।</p>
                </div>
            </div>

            <div class="usage-warning-box">
                <i class="fa-solid fa-triangle-exclamation"></i> <strong>সতর্কতা:</strong> শুধুমাত্র বাহ্যিক ব্যবহারের জন্য। শিশুদের নাগালের বাইরে রাখুন এবং চোখের সংস্পর্শ সম্পূর্ণ এড়িয়ে চলুন।
            </div>
        </div>
    </section>

    <!-- Section 4: Customer Reviews (Real Social Proof) -->
    <section class="section-padding reviews-section">
        <div class="container">
            <div class="section-title-wrap">
                <span class="section-tag tag-green"><i class="fa-solid fa-star"></i> গ্রাহকদের বাস্তব অভিজ্ঞতা ও ফলাফল</span>
                <h2 class="section-title">আমাদের সন্তুষ্ট কাস্টমারদের রিভিউ ও মেসেঞ্জার প্রমাণ</h2>
                <p class="section-desc">হাজারো সন্তুষ্ট গ্রাহক সাউথ মুন লাইপোমা স্প্রে ব্যবহার করে ইতিবাচক ফলাফল পেয়েছেন। আসল চ্যাট স্ক্রিনশটগুলো দেখে নিন।</p>
                <div class="reviews-rating-bar">
                    <div class="rating-stars">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <span class="rating-text"><strong>4.9 / 5</strong> রেটিং • ৩,৫০০+ সন্তুষ্ট কাস্টমার • <strong>100% আসল প্রমাণ</strong></span>
                </div>
            </div>

            <!-- Premium Auto-Sliding Reviews Carousel -->
        <div class="reviews-carousel-container" id="reviews-carousel">
            <button type="button" class="carousel-arrow carousel-prev" id="reviews-prev-btn" aria-label="Previous Review">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="reviews-carousel-viewport" id="reviews-viewport">
                <div class="reviews-carousel-track" id="reviews-track">
                    <!-- Review 1 -->
                    <div class="review-slide" data-index="0">
                        <div class="review-card-box">
                            <div class="review-card-header">
                                <div class="reviewer-profile">
                                    <div class="reviewer-avatar"><i class="fa-solid fa-user-check"></i></div>
                                    <div class="reviewer-info">
                                        <span class="reviewer-name">ইমরান হোসেন আইয়ুব</span>
                                        <span class="reviewer-status"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (মেসেঞ্জার)</span>
                                    </div>
                                </div>
                                <div class="card-stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>

                            <div class="review-screenshot-box review-zoom-btn" title="ক্লিক করে বড় করে দেখুন">
                                <img src="assets/reviews/1.jpg.jpeg" alt="গ্রাহক রিভিউ স্ক্রিনশট ১" loading="lazy">
                                <div class="zoom-badge">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> সম্পূর্ণ ছবি দেখুন
                                </div>
                            </div>

                            <div class="review-quote">
                                <i class="fa-solid fa-quote-left"></i>
                                <p>"১ মাস ১৫ দিন ব্যবহার করতেছি, আগের চেয়ে অনেক ছোট হয়ে আসছে আলহামদুলিল্লাহ।"</p>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2 -->
                    <div class="review-slide" data-index="1">
                        <div class="review-card-box">
                            <div class="review-card-header">
                                <div class="reviewer-profile">
                                    <div class="reviewer-avatar"><i class="fa-solid fa-user-check"></i></div>
                                    <div class="reviewer-info">
                                        <span class="reviewer-name">আশাদুজ্জামান বাবু</span>
                                        <span class="reviewer-status"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (ফেসবুক)</span>
                                    </div>
                                </div>
                                <div class="card-stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>

                            <div class="review-screenshot-box review-zoom-btn" title="ক্লিক করে বড় করে দেখুন">
                                <img src="assets/reviews/2.jpg.jpeg" alt="গ্রাহক রিভিউ স্ক্রিনশট ২" loading="lazy">
                                <div class="zoom-badge">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> সম্পূর্ণ ছবি দেখুন
                                </div>
                            </div>

                            <div class="review-quote">
                                <i class="fa-solid fa-quote-left"></i>
                                <p>"আলহামদুলিল্লাহ, আমি খুবই উপকৃত হয়েছি, আপনারা ভালো থাকবেন।"</p>
                            </div>
                        </div>
                    </div>

                    <!-- Review 3 -->
                    <div class="review-slide" data-index="2">
                        <div class="review-card-box">
                            <div class="review-card-header">
                                <div class="reviewer-profile">
                                    <div class="reviewer-avatar"><i class="fa-solid fa-user-check"></i></div>
                                    <div class="reviewer-info">
                                        <span class="reviewer-name">মো: জিয়াউর রহমান</span>
                                        <span class="reviewer-status"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (ফেসবুক)</span>
                                    </div>
                                </div>
                                <div class="card-stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>

                            <div class="review-screenshot-box review-zoom-btn" title="ক্লিক করে বড় করে দেখুন">
                                <img src="assets/reviews/4.jpg.jpeg" alt="গ্রাহক রিভিউ স্ক্রিনশট ৩" loading="lazy">
                                <div class="zoom-badge">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> সম্পূর্ণ ছবি দেখুন
                                </div>
                            </div>

                            <div class="review-quote">
                                <i class="fa-solid fa-quote-left"></i>
                                <p>"আলহামদুলিল্লাহ আল্লাহর রহমতে উপকার পেয়েছি।"</p>
                            </div>
                        </div>
                    </div>

                    <!-- Review 4 -->
                    <div class="review-slide" data-index="3">
                        <div class="review-card-box">
                            <div class="review-card-header">
                                <div class="reviewer-profile">
                                    <div class="reviewer-avatar"><i class="fa-solid fa-user-check"></i></div>
                                    <div class="reviewer-info">
                                        <span class="reviewer-name">নুরশেদ আহমেদ</span>
                                        <span class="reviewer-status"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (ফেসবুক)</span>
                                    </div>
                                </div>
                                <div class="card-stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>

                            <div class="review-screenshot-box review-zoom-btn" title="ক্লিক করে বড় করে দেখুন">
                                <img src="assets/reviews/5.jpg.jpeg" alt="গ্রাহক রিভিউ স্ক্রিনশট ৪" loading="lazy">
                                <div class="zoom-badge">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> সম্পূর্ণ ছবি দেখুন
                                </div>
                            </div>

                            <div class="review-quote">
                                <i class="fa-solid fa-quote-left"></i>
                                <p>"আমি ২টা নিয়েছিলাম, আমি এখন সম্পূর্ণ সুস্থ ধন্যবাদ।"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="carousel-arrow carousel-next" id="reviews-next-btn" aria-label="Next Review">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <!-- Pagination Dots & Status -->
        <div class="carousel-dots-wrap" id="reviews-dots-wrap"></div>
        <div class="carousel-hint-text">
            <i class="fa-solid fa-circle-play"></i> অটো-স্লাইড হচ্ছে • যে কোনো রিভিউতে ক্লিক করে বড় করে দেখুন
        </div>

        <!-- Trust Bar -->
            <!-- <div class="trust-bar">
                <div class="trust-item">
                    <div class="trust-item-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <h4>দ্রুত হোম ডেলিভারি</h4>
                    <p>সারা দেশে ২-৩ দিনে ঘরে বসে হোম ডেলিভারি পাবেন</p>
                </div>
                <div class="trust-item">
                    <div class="trust-item-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <h4>ক্যাশ অন ডেলিভারি</h4>
                    <p>আগে কোনো অগ্রিম টাকা দিতে হবে না, পণ্য পেয়ে টাকা দিন</p>
                </div>
                <div class="trust-item">
                    <div class="trust-item-icon"><i class="fa-solid fa-award"></i></div>
                    <h4>100% অরিজিনাল গ্যারান্টি</h4>
                    <p>সরাসরি আমদানিকৃত অথেনটিক সাউথ মুন প্রোডাক্ট</p>
                </div>
            </div> -->
        </div>
    </section>

    <!-- Section 5: Checkout & Order Form (Core Conversion Machine) -->
    <section class="checkout-section" id="order-section">
        <div class="container container-sm">
            <div class="order-box-wrapper">
                <div class="order-box-header">
                    <h2>অর্ডার করতে নিচের ফর্মটি সঠিক তথ্য দিয়ে পূরণ করুন</h2>
                    <p>কোনো অগ্রিম পেমেন্ট ছাড়াই সারা দেশে ক্যাশ অন ডেলিভারিতে অর্ডার করুন</p>
                </div>

                <div class="order-form-body">
                    <!-- Error Alert -->
                    <div id="order-error-alert" class="alert-error"></div>

                    <form id="checkout-form" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="selected_package" id="selected-package" value="pkg1">
                        <input type="hidden" name="shipping_area" id="selected-delivery" value="dhaka">

                        <!-- Step 1: Package Selection -->
                        <div class="form-step-title">
                            <span class="step-circle">১</span>
                            <span>প্যাকেজ নির্বাচন করুন:</span>
                        </div>

                        <div class="packages-grid">
                            <!-- Package 1: Buy 2 Get 1 Free (Default Best Offer) -->
                            <div class="package-card selected" 
                                 data-package="pkg1" 
                                 data-title="<?= htmlspecialchars($pkg1_title) ?>" 
                                 data-price="<?= $pkg1_price ?>">
                                <?php if (!empty($pkg1_badge)): ?>
                                    <span class="best-offer-ribbon"><?= htmlspecialchars($pkg1_badge) ?></span>
                                <?php endif; ?>
                                <div class="pkg-radio-wrap">
                                    <div class="custom-radio"></div>
                                    <div class="pkg-info">
                                        <h4><?= htmlspecialchars($pkg1_title) ?></h4>
                                        <div class="pkg-price-wrap">
                                            <span class="pkg-price-current">৳<?= number_format($pkg1_price) ?></span>
                                            <span class="pkg-price-regular">৳<?= number_format($pkg1_regular) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Package 2: 1 Pcs -->
                            <div class="package-card" 
                                 data-package="pkg2" 
                                 data-title="<?= htmlspecialchars($pkg2_title) ?>" 
                                 data-price="<?= $pkg2_price ?>">
                                <?php if (!empty($pkg2_badge)): ?>
                                    <span class="best-offer-ribbon" style="background: #475569;"><?= htmlspecialchars($pkg2_badge) ?></span>
                                <?php endif; ?>
                                <div class="pkg-radio-wrap">
                                    <div class="custom-radio"></div>
                                    <div class="pkg-info">
                                        <h4><?= htmlspecialchars($pkg2_title) ?></h4>
                                        <div class="pkg-price-wrap">
                                            <span class="pkg-price-current">৳<?= number_format($pkg2_price) ?></span>
                                            <span class="pkg-price-regular">৳<?= number_format($pkg2_regular) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Stepper Control -->
                        <div class="quantity-control-wrap">
                            <div class="quantity-label">
                                <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i>
                                <span>প্রয়োজনীয় পরিমাণ (Quantity) নির্বাচন করুন:</span>
                            </div>
                            <div class="quantity-stepper">
                                <button type="button" class="qty-btn" id="qty-minus" aria-label="Decrease quantity">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" id="order-quantity" name="quantity" value="1" min="1" max="25" class="qty-input">
                                <button type="button" class="qty-btn" id="qty-plus" aria-label="Increase quantity">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Customer Delivery Details -->
                        <div class="form-step-title">
                            <span class="step-circle">২</span>
                            <span>আপনার ডেলিভারির ঠিকানা লিখুন:</span>
                        </div>

                        <div class="input-row">
                            <label for="customer_name" class="input-label">আপনার পুরো নাম <span class="req">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="যেমন: মোঃ রফিকুল ইসলাম" required>
                        </div>

                        <div class="input-row">
                            <label for="phone" class="input-label">মোবাইল নাম্বার <span class="req">*</span></label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="যেমন: 01712345678" maxlength="14" required>
                            <small style="color: #64748b; font-size: 0.85rem; margin-top: 4px; display: block;">
                                💡 ডেলিভারি ম্যানের যোগাযোগের জন্য সচল নাম্বার দিন।
                            </small>
                        </div>

                        <div class="input-row">
                            <label for="address" class="input-label">সম্পূর্ণ ঠিকানা (বাসা নং, রোড, থানা, জেলা) <span class="req">*</span></label>
                            <textarea id="address" name="address" class="form-control" placeholder="যেমন: বাসা নং ১২, রোড নং ৫, ধানমন্ডি, ঢাকা" required></textarea>
                        </div>

                        <!-- Step 3: Delivery Area Selector -->
                        <div class="form-step-title">
                            <span class="step-circle">৩</span>
                            <span>ডেলিভারি এলাকা নির্বাচন করুন:</span>
                        </div>

                        <div class="delivery-options-grid">
                            <!-- Inside Dhaka -->
                            <div class="delivery-card selected" data-area="dhaka" data-title="ঢাকার ভিতরে" data-price="<?= $delivery_dhaka ?>">
                                <div class="delivery-card-title">
                                    <i class="fa-solid fa-city" style="color: var(--primary);"></i>
                                    <span>ঢাকার ভিতরে</span>
                                </div>
                                <div class="delivery-card-price">৳<?= number_format($delivery_dhaka) ?></div>
                            </div>

                            <!-- Outside Dhaka -->
                            <div class="delivery-card" data-area="outside" data-title="ঢাকার বাহিরে" data-price="<?= $delivery_outside ?>">
                                <div class="delivery-card-title">
                                    <i class="fa-solid fa-map-location-dot" style="color: var(--accent-dark);"></i>
                                    <span>ঢাকার বাহিরে</span>
                                </div>
                                <div class="delivery-card-price">৳<?= number_format($delivery_outside) ?></div>
                            </div>
                        </div>

                        <!-- Real-time Order Summary -->
                        <div class="order-summary-card">
                            <div class="summary-row">
                                <span id="summary-package-title"><?= htmlspecialchars($pkg1_title) ?></span>
                                <span id="summary-package-price" style="font-weight: 700;">৳<?= number_format($pkg1_price) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>ডেলিভারি চার্জ (<span id="summary-delivery-title">ঢাকার ভিতরে</span>)</span>
                                <span id="summary-delivery-price" style="font-weight: 700;">৳<?= number_format($delivery_dhaka) ?></span>
                            </div>
                            <div class="summary-row total-row">
                                <span>সর্বমোট প্রদেয় টাকা:</span>
                                <span class="total-amount" id="summary-total-amount">৳<?= number_format($pkg1_price + $delivery_dhaka) ?></span>
                            </div>
                        </div>

                        <!-- Submit Order Button -->
                        <button type="submit" id="btn-submit-order" class="btn-submit-order btn-animated">
                            <span class="btn-spinner"></span>
                            <span class="btn-text">
                                <i class="fa-solid fa-circle-check"></i> অর্ডার কনফার্ম করুন (<span id="btn-submit-amount">৳<?= number_format($pkg1_price + $delivery_dhaka) ?></span>)
                            </span>
                        </button>

                        <div class="checkout-guarantee-note">
                            <i class="fa-solid fa-lock" style="color: var(--accent-dark);"></i> 
                            100% নিরাপদ চেকআউট | পণ্য হাতে পেয়ে টাকা পরিশোধ (ক্যাশ অন ডেলিভারি)
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding faq-section">
        <div class="container container-sm">
            <div class="section-title-wrap">
                <span class="section-tag tag-blue">সাধারণ জিজ্ঞাসা</span>
                <h2 class="section-title">সচরাচর জিজ্ঞাসিত প্রশ্নাবলী (FAQ)</h2>
            </div>

            <div class="faq-list">
                <div class="faq-card">
                    <div class="faq-question">
                        <i class="fa-solid fa-circle-question"></i>
                        <span>লাইপোমা দূর হতে কতদিন সময় লাগতে পারে?</span>
                    </div>
                    <div class="faq-answer">
                        সাধারণত নিয়মিত ব্যবহারে ২ থেকে ৪ সপ্তাহের মধ্যে চর্বির দলা নরম হতে শুরু করে এবং ছোট হয়ে যায়। লাইপোমার আকার ও বয়সের উপর নির্ভর করে পূর্ণাঙ্গ ফলাফলের জন্য টানা ১ মাস ব্যবহারের পরামর্শ দেওয়া হয়।
                    </div>
                </div>

                <div class="faq-card">
                    <div class="faq-question">
                        <i class="fa-solid fa-circle-question"></i>
                        <span>প্রোডাক্টটির কি কোনো পার্শ্বপ্রতিক্রিয়া আছে?</span>
                    </div>
                    <div class="faq-answer">
                        না, এটি সম্পূর্ণ প্রাকৃতিক চীনা হার্বাল এক্সট্রাক্ট দিয়ে তৈরি। এতে কোনো ক্ষতিকর রাসায়নিক স্টেরয়েড নেই, তাই যেকোনো বয়সের নারী ও পুরুষ নিরাপদে ব্যবহার করতে পারেন।
                    </div>
                </div>

                <div class="faq-card">
                    <div class="faq-question">
                        <i class="fa-solid fa-circle-question"></i>
                        <span>ডেলিভারি পেতে কতদিন সময় লাগবে?</span>
                    </div>
                    <div class="faq-answer">
                        ঢাকার ভেতরে ২৪ থেকে ৪৮ ঘণ্টার মধ্যে এবং ঢাকার বাইরে ২ থেকে ৩ কার্যদিবসের মধ্যে আপনার দেওয়া ঠিকানায় হোম ডেলিভারি পৌঁছে দেওয়া হবে।
                    </div>
                </div>

                <div class="faq-card">
                    <div class="faq-question">
                        <i class="fa-solid fa-circle-question"></i>
                        <span>আমি কীভাবে পণ্যটি রিসিভ করবো এবং পেমেন্ট করবো?</span>
                    </div>
                    <div class="faq-answer">
                        ডেলিভারি ম্যান আপনার ঠিকানায় পার্সেল নিয়ে গেলে আপনি প্যাকেট দেখে বুঝে নিয়ে ডেলিভারি ম্যানের কাছে ক্যাশ টাকা পরিশোধ করবেন।
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-hotline">
                প্রয়োজনে যেকোনো তথ্যের জন্য কল বা WhatsApp করুন: <br>
                <a href="tel:<?= htmlspecialchars($hotline) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($hotline) ?></a>
            </div>
            <p style="color: #64748b; max-width: 650px; margin: 10px auto;">
                সাউথ মুন বাংলাদেশ — 100% আসল কোয়ালিটি নিশ্চয়তা সহ সরাসরি আমদানিকৃত পণ্য পৌঁছে দিচ্ছি আপনার দোরগোড়ায়।
            </p>
            <div class="footer-copyright">
                <div>&copy; <?= date('Y') ?> South Moon Lipoma Removal. All Rights Reserved.</div>
              <!--   <div class="footer-dev-credit">
                    Developed by <a href="https://www.cyberdeveloperbd.com/" target="_blank" rel="noopener noreferrer">Cyber Developer BD</a>
                </div> -->
            </div>
        </div>
    </footer>

    <!-- Sticky Mobile Navigation Bar -->
    <div class="sticky-mobile-bar">
        <div class="sticky-bar-inner">
            <a href="tel:<?= htmlspecialchars($hotline) ?>" class="sticky-btn-call">
                <i class="fa-solid fa-phone"></i> কল করুন
            </a>
            <a href="https://wa.me/88<?= preg_replace('/[^0-9]/', '', $whatsapp) ?>" target="_blank" class="sticky-btn-whatsapp">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
            <a href="#order-section" class="sticky-btn-order scroll-to-order btn-animated">
                <i class="fa-solid fa-cart-shopping"></i> অর্ডার করুন
            </a>
        </div>
    </div>

    <!-- Floating WhatsApp Button (Direct Chat Link) -->
    <a href="https://wa.me/88<?= preg_replace('/[^0-9]/', '', $whatsapp) ?>?text=<?= urlencode('সাউথ মুন লাইপোমা স্প্রে সম্পর্কে বিস্তারিত জানতে চাই') ?>" target="_blank" class="floating-whatsapp-btn" title="সরাসরি WhatsApp করুন">
        <i class="fa-brands fa-whatsapp"></i>
        <span class="whatsapp-tooltip">সরাসরি WhatsApp করুন</span>
    </a>

    <!-- Review Image Lightbox Modal -->
    <div id="lightbox-modal" class="lightbox-modal">
        <button id="lightbox-close" class="lightbox-close">&times;</button>
        <div class="lightbox-content">
            <img id="lightbox-img" src="" alt="Enlarged Customer Review">
        </div>
    </div>

    <!-- Scripts with Auto Cache Busting -->
    <script src="assets/js/main.js?v=<?= filemtime(__DIR__ . '/assets/js/main.js') ?>"></script>

    <!-- Custom Footer Scripts -->
    <?php if (!empty($custom_footer_script)): ?>
        <?= $custom_footer_script ?>
    <?php endif; ?>
</body>
</html>
