<?php
require_once __DIR__ . '/../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'ইউজারনেম এবং পাসওয়ার্ড উভয়ই পূরণ করতে হবে।';
    } else {
        try {
            $db = get_db();
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'ভুল ইউজারনেম বা পাসওয়ার্ড দিয়েছেন।';
            }
        } catch (Exception $e) {
            $error = 'লগইন করতে সমস্যা হয়েছে: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন লগইন | সাউথ মুন লাইপোমা শপ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=Inter:wght@400;600;700&display=swap">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Hind Siliguri', 'Inter', sans-serif; }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            padding: 35px 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }
        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .login-icon {
            width: 60px;
            height: 60px;
            background: #e0f2fe;
            color: #0284c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 12px;
        }
        .login-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
        }
        .login-subtitle {
            font-size: 0.92rem;
            color: #64748b;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
        .btn-login {
            width: 100%;
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 13px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 6px;
        }
        .btn-login:hover {
            background: #0369a1;
        }
        .error-msg {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.92rem;
            margin-bottom: 18px;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="login-title">অ্যাডমিন ড্যাশবোর্ড</h2>
            <p class="login-subtitle">লগইন করতে আপনার তথ্য প্রদান করুন</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">ইউজারনেম</label>
                <input type="text" name="username" class="form-input" placeholder="ইউজারনেম" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">পাসওয়ার্ড</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> লগইন করুন
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center;">
            <a href="../" style="color: #64748b; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> ওয়েবসাইটে ফিরে যান
            </a>
        </div>
    </div>

</body>
</html>
