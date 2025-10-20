<?php
require_once __DIR__ . '/../../inc/timeout.php';
require_once __DIR__ . '/../../inc/auth.php';
require_login();
require_once __DIR__ . '/../../inc/db.php';

$user = current_user();
$status = null;
$error = null;

// Ensure notifications table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `manager_notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(100) NOT NULL,
  `sender_id` INT UNSIGNED NULL,
  `payload` TEXT NOT NULL,
  `meta` JSON NULL,
  `priority` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (`is_read`),
  INDEX (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Admin</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">    <link rel="stylesheet" href="../../assets/css/style.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
.contact-form {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  margin-top: 1.5rem;
}

.contact-form label {
  font-weight: 600;
  color: #333;
  align-self: flex-start;
  font-size: 15px;
}

.contact-form textarea {
  width: 100%;
  padding: 14px;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  background-color: #fafafa;
  font-size: 15px;
  resize: none;
  transition: all 0.3s ease;
}

.contact-form textarea:focus {
  border-color: #2575fc;
  box-shadow: 0 0 0 4px rgba(37, 117, 252, 0.15);
  background-color: #fff;
  outline: none;
}

.contact-form button {
  background: linear-gradient(90deg, #2575fc, #6a11cb);
  color: white;
  border: none;
  padding: 12px 40px;
  border-radius: 30px;
  cursor: pointer;
  font-weight: 600;
  font-size: 16px;
  letter-spacing: 0.5px;
  transition: 0.3s ease;
  box-shadow: 0 4px 15px rgba(37, 117, 252, 0.3);
}

.contact-form button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(106, 17, 203, 0.35);
}

  </style>
</head>
<body>

<?php
$status = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if (empty($message)) {
        $error = "Please enter your message.";
    } else {
        // Example: send or save message logic here
        // mail($admin_email, "Message from User", $message);
        $status = "Message sent successfully!";
    }
}
?>
             <header>
        <div class="menu-toggle" id="menuToggle">☰</div>

        <nav id="sidebarNav">
            <h1>Admin <br> POS Dashboard</h1>
            <ul>
                <li><a href="../cashier_dashboard.php" >Dashboard</a></li>
                <li><a href="../contacts/contact_admin.php">contact admin</a> </li>
                <li><a href="../contacts/contact_manager.php">Contact Manager</a></li>
                <li><a href="/pos/pos.php">POS</a></li>
                <li><a href="/pos/auth/out.php">Logout</a></li>
            </ul>
        </nav>
    </header>
<main>
  <h2>Contact Admin</h2>

<form method="post" class="contact-form">
  <label for="message">Message:</label>
  <textarea id="message" name="message" rows="4" placeholder="Type your message here..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
  <button type="submit">Send</button>
</form>


<?php if ($status): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '<?= htmlspecialchars($status) ?>',
    showConfirmButton: false,
    timer: 2000
  });
</script>
<?php endif; ?>

<?php if ($error): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Oops!',
    text: '<?= htmlspecialchars($error) ?>',
    confirmButtonColor: '#2575fc'
  });
</script>
<?php endif; ?>

</body>
</html>
