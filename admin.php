<?php
session_start();
include 'db_connect.php'; // Adjust path if needed

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php'); // Redirect to login if not authenticated
    exit();
}

// Log successful login to console
$admin_username = $_SESSION['admin_username'] ?? 'Unknown';
echo "<script>console.log('PHP: Admin login successful for username: $admin_username');</script>";

// Fetch stats from database
try {
    $inquiries_count = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    $solutions_count = $pdo->query("SELECT COUNT(*) FROM solutions")->fetchColumn();
    $events_count = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
    $testimonials_count = $pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();

    // Fetch recent inquiries
    $stmt = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 2");
    $recent_inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent solutions
    $stmt = $pdo->query("SELECT title, updated_at FROM solutions ORDER BY updated_at DESC LIMIT 2");
    $recent_solutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch upcoming events
    $stmt = $pdo->query("SELECT title, event_date FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 2");
    $recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch gallery items
    $stmt = $pdo->query("SELECT image_url FROM gallery ORDER BY updated_at DESC LIMIT 2");
    $recent_gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AI-Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content-area {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .stat-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            width: 250px;
        }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar from navbar.php -->
            <?php include 'navbar.php'; ?>

            <!-- Main Content Area -->
            <div class="col main-content">
                <div class="content-area">
                    <!-- Top Navbar is already included via navbar.php -->

                    <!-- Dashboard Content -->
                    <div class="container-fluid px-4 py-4">
                        <!-- Success Message -->
                        <div id="successMessage" class="alert alert-success text-center" role="alert" style="display: none;">
                            Welcome, <?php echo htmlspecialchars($admin_username); ?>! You have successfully logged in.
                        </div>

                        <!-- Stats Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <a href="inquiries.php" class="text-decoration-none">
                                    <div class="card stat-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-subtitle mb-2 text-muted">Total Inquiries</h6>
                                                    <h2 class="card-title mb-0"><?php echo $inquiries_count; ?></h2>
                                                </div>
                                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-envelope text-primary fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="adminSolution.php" class="text-decoration-none">
                                    <div class="card stat-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-subtitle mb-2 text-muted">Active Solutions</h6>
                                                    <h2 class="card-title mb-0"><?php echo $solutions_count; ?></h2>
                                                </div>
                                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-gear text-success fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="adminEvent.php" class="text-decoration-none">
                                    <div class="card stat-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-subtitle mb-2 text-muted">Upcoming Events</h6>
                                                    <h2 class="card-title mb-0"><?php echo $events_count; ?></h2>
                                                </div>
                                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-calendar-event text-warning fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="adminTestimonials.php" class="text-decoration-none">
                                    <div class="card stat-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="card-subtitle mb-2 text-muted">Testimonials</h6>
                                                    <h2 class="card-title mb-0"><?php echo $testimonials_count; ?></h2>
                                                </div>
                                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                                    <i class="bi bi-chat-quote text-info fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Recent Inquiries -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Inquiries</h5>
                                <a href="inquiries.php" class="btn btn-primary btn-sm">View More</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Company</th>
                                                <th>Email</th>
                                                <th>Country</th>
                                                <th>Job Title</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_inquiries as $inquiry): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($inquiry['company']); ?></td>
                                                    <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($inquiry['country']); ?></td>
                                                    <td><?php echo htmlspecialchars($inquiry['job_title']); ?></td>
                                                    <td><?php echo date('Y-m-d', strtotime($inquiry['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($recent_inquiries)): ?>
                                                <tr><td colspan="6" class="text-muted">No recent inquiries.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Content Management -->
                        <div class="row g-4">
                            <!-- Solutions Management -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Solutions Management</h5>
                                        <a href="adminSolution.php" class="btn btn-primary btn-sm">View More</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group">
                                            <?php foreach ($recent_solutions as $solution): ?>
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($solution['title']); ?></h6>
                                                        <small class="text-muted">Last updated: <?php echo date('Y-m-d', strtotime($solution['updated_at'])); ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if (empty($recent_solutions)): ?>
                                                <p class="text-muted">No recent solutions.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Events Management -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Events Management</h5>
                                        <a href="adminEvent.php" class="btn btn-primary btn-sm">View More</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group">
                                            <?php foreach ($recent_events as $event): ?>
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                                        <small class="text-muted">Date: <?php echo date('Y-m-d', strtotime($event['event_date'])); ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if (empty($recent_events)): ?>
                                                <p class="text-muted">No upcoming events.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Management -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Gallery Management</h5>
                                        <a href="adminGallery.php" class="btn btn-primary btn-sm">View More</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <?php foreach ($recent_gallery as $gallery): ?>
                                                <div class="col-md-3">
                                                    <img src="<?php echo htmlspecialchars($gallery['image_url']); ?>" 
                                                         class="img-fluid rounded" 
                                                         alt="Gallery Image">
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if (empty($recent_gallery)): ?>
                                                <p class="text-muted">No gallery items.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show success message and log to console on page load
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('successMessage');
            console.log('JS: Admin dashboard loaded successfully');
            successMessage.style.display = 'block';

            // Optional: Hide the success message after 5 seconds
            setTimeout(() => {
                successMessage.style.display = 'none';
                console.log('JS: Success message hidden after 5 seconds');
            }, 5000);
        });
    </script>
</body>
</html>