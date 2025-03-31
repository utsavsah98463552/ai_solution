<?php
// Ensure session is started in the including page before this
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Database connection (assuming $pdo is available from the including page)
try {
    $inquiries_count = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    $stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE username = :username AND role = 'admin'");
    $stmt->execute([':username' => $_SESSION['admin_username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle password change
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
        $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
        $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

        if ($new_password !== $confirm_password) {
            $password_error = "Passwords do not match.";
        } elseif (strlen($new_password) < 8) {
            $password_error = "Password must be at least 8 characters long.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username AND role = 'admin'");
            $stmt->execute([
                ':password' => $hashed_password,
                ':username' => $_SESSION['admin_username']
            ]);
            $password_success = "Password updated successfully!";
        }
    }
} catch (PDOException $e) {
    echo "Database error in navbar: " . $e->getMessage();
    exit();
}
?>

<!-- Sidebar -->
<div class="col-auto px-0 sidebar">
    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 text-white min-vh-100">
        <a href="admin.php" class="d-flex align-items-center pb-3 mb-md-1 text-white text-decoration-none">
            <i class="bi bi-brain fs-3 me-2"></i>
            <span class="fs-5 d-none d-sm-inline">AI-Solutions</span>
        </a>
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100" id="menu">
            <li class="nav-item w-100">
                <a href="admin.php" class="nav-link px-3 py-2 mb-2 <?php echo basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a href="inquiries.php" class="nav-link px-3 py-2 mb-2 <?php echo basename($_SERVER['PHP_SELF']) === 'inquiries.php' ? 'active' : ''; ?>">
                    <i class="bi bi-envelope me-2"></i>
                    <span class="d-none d-sm-inline">Inquiries</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a href="adminSolution.php" class="nav-link px-3 py-2 mb-2 <?php echo basename($_SERVER['PHP_SELF']) === 'adminSolution.php' ? 'active' : ''; ?>">
                    <i class="bi bi-gear me-2"></i>
                    <span class="d-none d-sm-inline">Solutions</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a href="adminEvent.php" class="nav-link px-3 py-2 mb-2 <?php echo basename($_SERVER['PHP_SELF']) === 'adminEvent.php' ? 'active' : ''; ?>">
                    <i class="bi bi-calendar-event me-2"></i>
                    <span class="d-none d-sm-inline">Events</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a href="adminTestimonials.php" class="nav-link px-3 py-2 mb-2 <?php echo basename($_SERVER['PHP_SELF']) === 'adminTestimonials.php' ? 'active' : ''; ?>">
                    <i class="bi bi-chat-quote me-2"></i>
                    <span class="d-none d-sm-inline">Testimonials</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a href="adminGallery.php" class="nav-link px-3 py-2 mb-2 <?php echo basename($_SERVER['PHP_SELF']) === 'adminGallery.php' ? 'active' : ''; ?>">
                    <i class="bi bi-images me-2"></i>
                    <span class="d-none d-sm-inline">Gallery</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a href="sucessStories.php" class="nav-link px-3 py-2 mb-2 <?php echo basename($_SERVER['PHP_SELF']) === 'adminGallery.php' ? 'active' : ''; ?>">
                    <i class="bi bi-images me-2"></i>
                    <span class="d-none d-sm-inline">Sucess Stories</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid">
        <button class="btn btn-link text-dark">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="d-flex align-items-center">
            <div class="position-relative me-3">
                <a href="inquiries.php" class="btn btn-link text-dark">
                    <i class="bi bi-bell fs-4"></i>
                    <span class="notification-badge bg-danger"><?php echo $inquiries_count; ?></span>
                </a>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-dark dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" 
                         alt="Admin" 
                         class="rounded-circle me-2" 
                         width="32" 
                         height="32">
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="bi bi-key me-2"></i>Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" 
                         alt="Admin" 
                         class="rounded-circle" 
                         width="100" 
                         height="100">
                </div>
                <dl class="row">
                    <dt class="col-sm-4">Username:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($user['username']); ?></dd>
                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($user['email']); ?></dd>
                    <dt class="col-sm-4">Joined:</dt>
                    <dd class="col-sm-8"><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($password_error)): ?>
                    <div class="alert alert-danger"><?php echo $password_error; ?></div>
                <?php elseif (isset($password_success)): ?>
                    <div class="alert alert-success"><?php echo $password_success; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Navbar-specific Styles -->
<style>
    .sidebar {
        min-height: 100vh;
        background: #1a1f2d;
    }
    .nav-link {
        color: #ffffff80;
        transition: all 0.3s;
    }
    .nav-link:hover, .nav-link.active {
        color: #fff;
        background: rgba(255,255,255,0.1);
    }
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        padding: 0.25rem 0.5rem;
        border-radius: 50%;
        font-size: 0.75rem;
    }
</style>

<!-- Navbar-specific JavaScript -->
<script>
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        fetch('logout.php', { method: 'POST' })
            .then(() => window.location.href = 'login.php');
    });
</script>