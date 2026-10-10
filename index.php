<?php
require_once __DIR__ . '/config.php';

// Fetch settings
$site_title           = get_setting('site_title', 'সাউথ মুন লাইপোমা রিমুভাল স্প্রে | ১০০% অরিজিনাল');
$product_name         = get_setting('product_name', 'South Moon Lipoma Removal Spray');
$hotline              = get_setting('hotline', '01872307272');
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
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_title) ?></title>
    <meta name="description" content="South Moon Lipoma Removal Spray - অপারেশন ছাড়াই লাইপোমা ও চর্বির দলা দূর করার ১০০% নিরাপদ প্রাকৃতিক হার্বাল সমাধান। সারা দেশে ক্যাশ অন ডেলিভারি।">
    
    <!-- Open Graph for Facebook Ads & Sharing -->
    <meta property="og:title" content="<?= htmlspecialchars($site_title) ?>">
    <meta property="og:description" content="অপারেশন ছাড়াই কি লাইপোমা দূর করা সম্ভব? হ্যাঁ, সম্ভব! টানা একমাস ব্যবহারে পান লাইপোমা থেকে মুক্তি।">
    <meta property="og:image" content="assets/images/1.PNG">
    <meta property="og:type" content="product">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Medical CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Meta / Facebook Pixel Tracking -->
    <?php if ($meta_pixel_active && !empty($meta_pixel_id)): ?>
    <!-- Meta Pixel Code -->
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
    fbq('track', 'ViewContent', {
        content_name: '<?= htmlspecialchars($product_name) ?>',
        content_type: 'product',
        currency: 'BDT',
        value: <?= $pkg1_price ?>
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?= htmlspecialchars($meta_pixel_id) ?>&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    <?php endif; ?>

    <!-- Custom Header Scripts -->
    <?php if (!empty($custom_header_script)): ?>
        <?= $custom_header_script ?>
    <?php endif; ?>
</head>
<body>

    <!-- Top Urgency Notice Bar -->
    <header class="top-notice-bar">
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
    </header>

    <!-- Main Navigation / Brand Bar -->
    <div class="site-header">
        <div class="container">
            <div class="header-inner">
                <div class="logo-wrap">
                    <div class="logo-badge">South Moon</div>
                    <div class="logo-text">
                        <h1>সাউথ মুন বাংলাদেশ</h1>
                        <span>১০০% অরিজিনাল প্রোডাক্ট নিশ্চয়তা</span>
                    </div>
                </div>
                <a href="#order-section" class="header-cta-btn scroll-to-order">
                    <i class="fa-solid fa-cart-shopping"></i> অর্ডার করতে চাই
                </a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-headline-wrap">
                <span class="hero-tag">হার্বাল ও প্রাকৃতিক লাইপোমা কেয়ার</span>
                <h1 class="hero-title">
                    অপারেশন ছাড়াই কি লাইপোমা দূর করা সম্ভব? <br>
                    <span class="highlight-red">হ্যাঁ, শতভাগ সম্ভব!</span>
                </h1>
                <p class="hero-subtitle">
                    টানা একমাস ব্যবহারে খুব সহজেই পাবেন লাইপোমা ও চর্বির দলা থেকে <strong>শতভাগ মুক্তি ইনশাআল্লাহ</strong>, ১০০% কার্যকরী গ্যারান্টি।
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
                            <h4>১০০% প্রাকৃতিক ভেষজ উপাদানে প্রস্তুত</h4>
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
                <a href="#order-section" class="btn-order-now scroll-to-order">
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

            <div class="symptoms-grid">
                <div>
                    <img src="assets/images/5.webp" alt="Types of Lipoma Tumors" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); border: 1px solid var(--border-color);">
                </div>
                <div class="symptoms-list">
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
                </div>
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
            <div class="ingredients-card">
                <div>
                    <span class="section-tag tag-green">১০০% ভেষজ উপাদান</span>
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
                </div>
                <div>
                    <img src="assets/images/4.webp" alt="South Moon Herbal Ingredients" style="border-radius: var(--radius-md);">
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
                <span class="section-tag tag-green">গ্রাহকদের অভিজ্ঞতা</span>
                <h2 class="section-title">আমাদের সন্তুষ্ট কাস্টমারদের রিভিউ ও মতামত</h2>
                <p class="section-desc">হাজারো সন্তুষ্ট গ্রাহক আমাদের প্রোডাক্ট ব্যবহার করে লাইপোমা থেকে মুক্ত হয়েছেন। স্ক্রিনশটগুলোতে ক্লিক করে বড় করে দেখুন।</p>
            </div>

            <div class="reviews-grid">
                <!-- Review 1 -->
                <div class="review-card">
                    <div class="review-img-wrap review-zoom-btn">
                        <img src="assets/reviews/1.jpg.jpeg" alt="Messenger Review Screenshot 1" loading="lazy">
                    </div>
                    <div class="review-body">
                        <div class="review-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <div class="review-author">ইমরান হোসেন আইয়ুব</div>
                        <div class="review-verified"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (মেসেঞ্জার)</div>
                        <p style="font-size: 0.88rem; color: #475569; margin-top: 6px;">"১ মাস ১৫ দিন ব্যবহার করতেছি, আগের চেয়ে অনেক ছোট হয়ে আসছে আলহামদুলিল্লাহ।"</p>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="review-card">
                    <div class="review-img-wrap review-zoom-btn">
                        <img src="assets/reviews/2.jpg.jpeg" alt="Facebook Review Screenshot 2" loading="lazy">
                    </div>
                    <div class="review-body">
                        <div class="review-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <div class="review-author">আশাদুজ্জামান বাবু</div>
                        <div class="review-verified"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (ফেসবুক)</div>
                        <p style="font-size: 0.88rem; color: #475569; margin-top: 6px;">"আলহামদুলিল্লাহ, আমি খুবই উপকৃত হয়েছি, আপনারা ভালো থাকবেন।"</p>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="review-card">
                    <div class="review-img-wrap review-zoom-btn">
                        <img src="assets/reviews/4.jpg.jpeg" alt="Facebook Review Screenshot 3" loading="lazy">
                    </div>
                    <div class="review-body">
                        <div class="review-stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <div class="review-author">মো: জিয়াউর রহমান</div>
                        <div class="review-verified"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (ফেসবুক)</div>
                        <p style="font-size: 0.88rem; color: #475569; margin-top: 6px;">"আলহামদুলিল্লাহ আল্লাহর রহমতে উপকার পেয়েছি।"</p>
                    </div>
                </div>

                <!-- Review 4 -->
                <div class="review-card">
                    <div class="review-body" style="padding: 0;">
                        <div class="review-img-wrap review-zoom-btn">
                            <img src="assets/reviews/5.jpg.jpeg" alt="Facebook Review Screenshot 4" loading="lazy">
                        </div>
                        <div style="padding: 14px;">
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <div class="review-author">নুরশেদ আহমেদ</div>
                            <div class="review-verified"><i class="fa-solid fa-circle-check"></i> ভেরিফাইড বায়ার (ফেসবুক)</div>
                            <p style="font-size: 0.88rem; color: #475569; margin-top: 6px;">"আমি ২টা নিয়েছিলাম, আমি এখন সম্পূর্ণ সুস্থ ধন্যবাদ।"</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trust Bar -->
            <div class="trust-bar">
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
                    <h4>১০০% অরিজিনাল গ্যারান্টি</h4>
                    <p>সরাসরি আমদানিকৃত অথেনটিক সাউথ মুন প্রোডাক্ট</p>
                </div>
            </div>
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
                        <button type="submit" id="btn-submit-order" class="btn-submit-order">
                            <span class="btn-spinner"></span>
                            <span class="btn-text">
                                <i class="fa-solid fa-circle-check"></i> অর্ডার কনফার্ম করুন (<span id="btn-submit-amount">৳<?= number_format($pkg1_price + $delivery_dhaka) ?></span>)
                            </span>
                        </button>

                        <div class="checkout-guarantee-note">
                            <i class="fa-solid fa-lock" style="color: var(--accent-dark);"></i> 
                            ১০০% নিরাপদ চেকআউট | পণ্য হাতে পেয়ে টাকা পরিশোধ (ক্যাশ অন ডেলিভারি)
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
                সাউথ মুন বাংলাদেশ — ১০০% আসল কোয়ালিটি নিশ্চয়তা সহ সরাসরি আমদানিকৃত পণ্য পৌঁছে দিচ্ছি আপনার দোরগোড়ায়।
            </p>
            <div class="footer-copyright">
                &copy; <?= date('Y') ?> South Moon Lipoma Removal. All Rights Reserved.
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
            <a href="#order-section" class="sticky-btn-order scroll-to-order">
                <i class="fa-solid fa-cart-shopping"></i> অর্ডার করুন
            </a>
        </div>
    </div>

    <!-- Review Image Lightbox Modal -->
    <div id="lightbox-modal" class="lightbox-modal">
        <button id="lightbox-close" class="lightbox-close">&times;</button>
        <div class="lightbox-content">
            <img id="lightbox-img" src="" alt="Enlarged Customer Review">
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>

    <!-- Custom Footer Scripts -->
    <?php if (!empty($custom_footer_script)): ?>
        <?= $custom_footer_script ?>
    <?php endif; ?>
</body>
</html>
