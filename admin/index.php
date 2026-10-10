<?php
require_once __DIR__ . '/auth.php';
require_admin_auth();

$db = get_db();
$message = '';
$error = '';

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $order_id   = (int) ($_POST['order_id'] ?? 0);
        $new_status = trim($_POST['status'] ?? '');
        $allowed = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if ($order_id > 0 && in_array($new_status, $allowed, true)) {
            $stmt = $db->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $new_status, ':id' => $order_id]);
            $message = "অর্ডার #$order_id এর স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।";
        }
    } elseif ($_POST['action'] === 'delete_order') {
        $order_id = (int) ($_POST['order_id'] ?? 0);
        if ($order_id > 0) {
            $stmt = $db->prepare("DELETE FROM orders WHERE id = :id");
            $stmt->execute([':id' => $order_id]);
            $message = "অর্ডার #$order_id মুছে ফেলা হয়েছে।";
        }
    }
}

// Filter and Search Parameters
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

// Fetch Overview Stats
$total_orders = (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending_orders = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$today_orders = (int) $db->query("SELECT COUNT(*) FROM orders WHERE date(created_at) = date('now', 'localtime')")->fetchColumn();
$total_sales = (float) $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();

// Fetch Orders with pagination
$page = max(1, (int) ($_GET['page'] ?? 1));
$per_page = 25;
$offset = ($page - 1) * $per_page;

$count_stmt = $db->prepare("SELECT COUNT(*) FROM orders $where_sql");
$count_stmt->execute($params);
$total_filtered = (int) $count_stmt->fetchColumn();
$total_pages = ceil($total_filtered / $per_page);

$orders_stmt = $db->prepare("SELECT * FROM orders $where_sql ORDER BY id DESC LIMIT $per_page OFFSET $offset");
$orders_stmt->execute($params);
$orders = $orders_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অর্ডার ড্যাশবোর্ড | সাউথ মুন লাইপোমা শপ</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap">

    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --sidebar-bg: #0f172a;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Hind Siliguri', 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; }

        /* Top Navbar */
        .admin-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .nav-link-btn {
            color: #334155;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 14px;
            border-radius: 6px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .nav-link-btn.active, .nav-link-btn:hover {
            background: #f1f5f9;
            color: var(--primary);
        }
        .btn-logout {
            background: #fee2e2;
            color: #be123c;
        }
        .btn-logout:hover {
            background: #fecaca;
        }

        /* Container */
        .admin-container {
            max-width: 1300px;
            margin: 25px auto;
            padding: 0 20px;
        }

        /* Alert message */
        .alert-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Metric Stats Cards */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }
        .metric-card {
            background: var(--card);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .metric-info h5 {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }
        .metric-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }
        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        .icon-blue { background: #e0f2fe; color: #0284c7; }
        .icon-yellow { background: #fef3c7; color: #d97706; }
        .icon-green { background: #dcfce7; color: #16a34a; }
        .icon-purple { background: #f3e8ff; color: #9333ea; }

        /* Filter & Action Bar */
        .controls-card {
            background: var(--card);
            border-radius: 12px;
            padding: 18px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .search-form {
            display: flex;
            gap: 10px;
            flex: 1;
            max-width: 450px;
        }
        .search-input {
            flex: 1;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
        }
        .search-input:focus { border-color: var(--primary); }
        .btn-search {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .status-filter-select {
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            background: #ffffff;
            outline: none;
        }
        .btn-export {
            background: #10b981;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        .btn-export:hover { background: #059669; }

        /* Table */
        .table-card {
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .orders-table th {
            background: #f8fafc;
            padding: 14px 16px;
            font-size: 0.88rem;
            font-weight: 700;
            color: #475569;
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
        }
        .orders-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
            vertical-align: middle;
        }
        .orders-table tr:hover { background: #f8fafc; }
        
        .customer-title {
            font-weight: 700;
            color: #0f172a;
        }
        .customer-phone {
            font-size: 0.88rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            margin-top: 2px;
        }
        .customer-address {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 4px;
            max-width: 250px;
            line-height: 1.3;
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .badge-pending    { background: #fef3c7; color: #b45309; }
        .badge-confirmed  { background: #e0f2fe; color: #0369a1; }
        .badge-processing { background: #ede9fe; color: #6d28d9; }
        .badge-shipped    { background: #ffedd5; color: #c2410c; }
        .badge-delivered  { background: #dcfce7; color: #15803d; }
        .badge-cancelled  { background: #fee2e2; color: #b91c1c; }

        /* Action Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-action:hover {
            background: #f1f5f9;
            color: var(--primary);
        }
        .btn-delete:hover {
            background: #fee2e2;
            color: #e11d48;
            border-color: #fca5a5;
        }

        /* Quick Status Select in Row */
        .status-update-select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--border);
            font-size: 0.85rem;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        /* Pagination */
        .pagination-wrap {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
        }
        .page-link {
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            color: var(--text);
            font-size: 0.9rem;
            margin: 0 2px;
        }
        .page-link.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal.active { display: flex; }
        .modal-card {
            background: #ffffff;
            border-radius: 14px;
            max-width: 550px;
            width: 100%;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
        }
        .modal-close {
            background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8;
        }
        .modal-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
        }

        @media (max-width: 900px) {
            .metrics-grid { grid-template-columns: repeat(2, 1fr); }
            .orders-table th:nth-child(4), .orders-table td:nth-child(4) { display: none; }
        }
        @media (max-width: 600px) {
            .metrics-grid { grid-template-columns: 1fr; }
            .admin-nav { flex-direction: column; gap: 12px; }
            .controls-card { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

    <!-- Top Admin Bar -->
    <nav class="admin-nav">
        <div class="nav-brand">
            <i class="fa-solid fa-pills" style="color: var(--primary);"></i>
            <span>সাউথ মুন শপ অ্যাডমিন</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link-btn active"><i class="fa-solid fa-list-check"></i> অর্ডার তালিকা</a>
            <a href="settings.php" class="nav-link-btn"><i class="fa-solid fa-gear"></i> ওয়েবসাইট সেটিংস</a>
            <a href="../index.php" target="_blank" class="nav-link-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i> ল্যান্ডিং পেজ দেখুন</a>
            <a href="logout.php" class="nav-link-btn btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> লগআউট</a>
        </div>
    </nav>

    <div class="admin-container">

        <?php if (!empty($message)): ?>
            <div class="alert-box">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Key Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-info">
                    <h5>মোট অর্ডার</h5>
                    <h3><?= number_format($total_orders) ?></h3>
                </div>
                <div class="metric-icon icon-blue"><i class="fa-solid fa-cart-shopping"></i></div>
            </div>

            <div class="metric-card">
                <div class="metric-info">
                    <h5>পেন্ডিং অর্ডার</h5>
                    <h3><?= number_format($pending_orders) ?></h3>
                </div>
                <div class="metric-icon icon-yellow"><i class="fa-solid fa-clock"></i></div>
            </div>

            <div class="metric-card">
                <div class="metric-info">
                    <h5>আজকের অর্ডার</h5>
                    <h3><?= number_format($today_orders) ?></h3>
                </div>
                <div class="metric-icon icon-purple"><i class="fa-solid fa-calendar-day"></i></div>
            </div>

            <div class="metric-card">
                <div class="metric-info">
                    <h5>মোট সেলস (ক্যাশ)</h5>
                    <h3>৳<?= number_format($total_sales) ?></h3>
                </div>
                <div class="metric-icon icon-green"><i class="fa-solid fa-bangladeshi-taka-sign"></i></div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="controls-card">
            <form class="search-form" method="GET" action="index.php">
                <?php if (!empty($status_filter)): ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                <?php endif; ?>
                <input type="text" name="q" class="search-input" placeholder="ফোন নাম্বার, নাম বা অর্ডার আইডি দিয়ে খুঁজুন..." value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> সার্চ</button>
            </form>

            <div class="action-buttons">
                <form method="GET" action="index.php" style="display: inline;">
                    <?php if (!empty($search_query)): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($search_query) ?>">
                    <?php endif; ?>
                    <select name="status" class="status-filter-select" onchange="this.form.submit()">
                        <option value="">সব স্ট্যাটাস (All)</option>
                        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>পেন্ডিং (Pending)</option>
                        <option value="confirmed" <?= $status_filter === 'confirmed' ? 'selected' : '' ?>>কনফার্মড (Confirmed)</option>
                        <option value="processing" <?= $status_filter === 'processing' ? 'selected' : '' ?>>প্রসেসিং (Processing)</option>
                        <option value="shipped" <?= $status_filter === 'shipped' ? 'selected' : '' ?>>কুরিয়ারে পাঠানো (Shipped)</option>
                        <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>ডেলিভার্ড (Delivered)</option>
                        <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>বাতিল (Cancelled)</option>
                    </select>
                </form>

                <a href="export.php?status=<?= urlencode($status_filter) ?>&q=<?= urlencode($search_query) ?>" class="btn-export">
                    <i class="fa-solid fa-file-excel"></i> কুরিয়ার এক্সপোর্ট (CSV)
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="table-card">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>আইডি / তারিখ</th>
                        <th>কাস্টমার তথ্য</th>
                        <th>প্যাকেজ / এলাকা</th>
                        <th>মোট টাকা</th>
                        <th>স্ট্যাটাস পরিবর্তন</th>
                        <th style="text-align: right;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                কোনো অর্ডার পাওয়া যায়নি।
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td>
                                    <strong style="color: #0284c7; font-family: monospace;"><?= htmlspecialchars($o['order_number']) ?></strong>
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 3px;">
                                        <?= date('d M Y, h:i A', strtotime($o['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="customer-title"><?= htmlspecialchars($o['customer_name']) ?></div>
                                    <a href="tel:<?= htmlspecialchars($o['phone']) ?>" class="customer-phone">
                                        <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($o['phone']) ?>
                                    </a>
                                    <div class="customer-address" title="<?= htmlspecialchars($o['address']) ?>">
                                        <?= htmlspecialchars(mb_strimwidth($o['address'], 0, 50, '...')) ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($o['package_name']) ?></strong>
                                    <div style="font-size: 0.85rem; color: #64748b;">
                                        <?= htmlspecialchars($o['shipping_area']) ?> (৳<?= number_format($o['shipping_cost']) ?>)
                                    </div>
                                </td>
                                <td>
                                    <strong style="font-size: 1.15rem; color: #e11d48;">৳<?= number_format($o['total_amount']) ?></strong>
                                    <div style="font-size: 0.8rem; color: #16a34a; font-weight: 600;">ক্যাশ অন ডেলিভারি</div>
                                </td>
                                <td>
                                    <form method="POST" action="index.php" style="display: inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="status" class="status-update-select" onchange="this.form.submit()">
                                            <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>পেন্ডিং</option>
                                            <option value="confirmed" <?= $o['status'] === 'confirmed' ? 'selected' : '' ?>>কনফার্মড</option>
                                            <option value="processing" <?= $o['status'] === 'processing' ? 'selected' : '' ?>>প্রসেসিং</option>
                                            <option value="shipped" <?= $o['status'] === 'shipped' ? 'selected' : '' ?>>কুরিয়ারে পাঠানো</option>
                                            <option value="delivered" <?= $o['status'] === 'delivered' ? 'selected' : '' ?>>ডেলিভার্ড</option>
                                            <option value="cancelled" <?= $o['status'] === 'cancelled' ? 'selected' : '' ?>>বাতিল</option>
                                        </select>
                                    </form>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <!-- View Details Modal Trigger -->
                                    <button type="button" class="btn-action" title="বিস্তারিত দেখুন" onclick='showDetailsModal(<?= json_encode($o) ?>)'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <!-- Print Invoice -->
                                    <a href="invoice.php?id=<?= $o['id'] ?>" target="_blank" class="btn-action" title="ইনভয়েস প্রিন্ট">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <!-- Delete Order -->
                                    <form method="POST" action="index.php" style="display: inline;" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই অর্ডারটি মুছে ফেলতে চান?');">
                                        <input type="hidden" name="action" value="delete_order">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <button type="submit" class="btn-action btn-delete" title="মুছে ফেলুন">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-wrap">
                    <span style="font-size: 0.9rem; color: #64748b;">
                        মোট <?= $total_filtered ?> টি অর্ডারের মধ্যে <?= $offset + 1 ?>-<?= min($offset + $per_page, $total_filtered) ?> দেখানো হচ্ছে
                    </span>
                    <div>
                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                            <a href="?page=<?= $p ?>&status=<?= urlencode($status_filter) ?>&q=<?= urlencode($search_query) ?>" 
                               class="page-link <?= $page === $p ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="details-modal" class="modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">অর্ডার বিবরণী</h3>
                <button type="button" class="modal-close" onclick="closeDetailsModal()">&times;</button>
            </div>
            <div id="modal-details-content">
                <!-- Dynamically populated -->
            </div>
        </div>
    </div>

    <script>
        function showDetailsModal(order) {
            const container = document.getElementById('modal-details-content');
            container.innerHTML = `
                <div class="modal-row"><strong>অর্ডার নম্বর:</strong> <span style="color:#0284c7; font-family:monospace; font-weight:700;">${order.order_number}</span></div>
                <div class="modal-row"><strong>তারিখ ও সময়:</strong> <span>${order.created_at}</span></div>
                <div class="modal-row"><strong>কাস্টমারের নাম:</strong> <span>${order.customer_name}</span></div>
                <div class="modal-row"><strong>ফোন নাম্বার:</strong> <a href="tel:${order.phone}" style="color:#0284c7; font-weight:700;">${order.phone}</a></div>
                <div class="modal-row" style="flex-direction:column; gap:4px;">
                    <strong>সম্পূর্ণ ডেলিভারি ঠিকানা:</strong>
                    <div style="background:#f8fafc; padding:8px 12px; border-radius:6px; border:1px solid #e2e8f0; line-height:1.4;">${order.address}</div>
                </div>
                <div class="modal-row"><strong>প্যাকেজ:</strong> <span>${order.package_name} (৳${order.package_price})</span></div>
                <div class="modal-row"><strong>ডেলিভারি এলাকা:</strong> <span>${order.shipping_area} (৳${order.shipping_cost})</span></div>
                <div class="modal-row" style="font-size:1.15rem; font-weight:800; color:#e11d48;">
                    <strong>সর্বমোট প্রদেয় টাকা:</strong> <span>৳${order.total_amount}</span>
                </div>
                <div class="modal-row"><strong>আইপি অ্যাড্রেস:</strong> <code>${order.ip_address || 'N/A'}</code></div>
                <div class="modal-row"><strong>বর্তমান স্ট্যাটাস:</strong> <span class="badge badge-${order.status}">${order.status.toUpperCase()}</span></div>
                <div style="margin-top: 18px; text-align: right;">
                    <a href="invoice.php?id=${order.id}" target="_blank" style="background:#0284c7; color:#fff; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem;">
                        <i class="fa-solid fa-print"></i> ইনভয়েস প্রিন্ট
                    </a>
                </div>
            `;
            document.getElementById('details-modal').classList.add('active');
        }

        function closeDetailsModal() {
            document.getElementById('details-modal').classList.remove('active');
        }

        document.getElementById('details-modal').addEventListener('click', function(e) {
            if (e.target === this) closeDetailsModal();
        });
    </script>
</body>
</html>
