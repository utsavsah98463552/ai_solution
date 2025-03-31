<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); // Updated to match earlier convention
    exit();
}

include 'db_connect.php'; // Adjust path as needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries - AI-Solutions Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content-area {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .inquiry-card {
            transition: transform 0.3s ease;
        }
        .inquiry-card:hover {
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
            <!-- Sidebar and Top Navbar from navbar.php -->
            <?php include 'navbar.php'; ?>

            <!-- Main Content Area -->
            <div class="col main-content">
                <div class="content-area">
                    <!-- Inquiries Content -->
                    <div class="container-fluid px-4 py-4">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">All Inquiries</h5>
                                <button class="btn btn-primary btn-sm" onclick="window.location.reload();">Refresh</button>
                            </div>
                            <div class="card-body">
                                <?php
                                try {
                                    // Fetch all inquiries
                                    $stmt = $pdo->prepare("SELECT * FROM inquiries ORDER BY inquiry_date DESC");
                                    $stmt->execute();
                                    $inquiries = $stmt->fetchAll();

                                    if (count($inquiries) > 0) {
                                        foreach ($inquiries as $inquiry) {
                                ?>
                                            <div class="card inquiry-card mb-3">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="card-title">Name: <?php echo htmlspecialchars($inquiry['name']); ?></h6>
                                                            <p class="card-text"><strong>Company:</strong> <?php echo htmlspecialchars($inquiry['company'] ?? 'N/A'); ?></p>
                                                            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($inquiry['email']); ?></p>
                                                            <p class="card-text"><strong>Phone:</strong> <?php echo htmlspecialchars($inquiry['phone'] ?? 'N/A'); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="card-text"><strong>Country:</strong> <?php echo htmlspecialchars($inquiry['country'] ?? 'N/A'); ?></p>
                                                            <p class="card-text"><strong>Job Title:</strong> <?php echo htmlspecialchars($inquiry['job_title'] ?? 'N/A'); ?></p>
                                                            <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($inquiry['inquiry_date']); ?></p>
                                                        </div>
                                                        <div class="col-12 mt-3">
                                                            <p class="card-text"><strong>Job Details:</strong> <?php echo nl2br(htmlspecialchars($inquiry['job_details'] ?? 'N/A')); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                <?php
                                        }
                                    } else {
                                        echo '<p class="text-muted">No inquiries found.</p>';
                                    }
                                } catch (PDOException $e) {
                                    echo '<p class="text-danger">Error fetching inquiries: ' . $e->getMessage() . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>