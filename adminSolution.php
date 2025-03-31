<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); // Updated to match earlier convention
    exit();
}

include 'db_connect.php'; // Adjust path as needed

// Handle Add Solution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_solution'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $icon = filter_input(INPUT_POST, 'icon', FILTER_SANITIZE_STRING);
    $features = filter_input(INPUT_POST, 'features', FILTER_SANITIZE_STRING);

    if (empty($title) || empty($description)) {
        $message = "Title and description are required.";
        $message_type = "danger";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO solutions (title, description, icon, features) VALUES (:title, :description, :icon, :features)");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':icon' => $icon,
                ':features' => $features
            ]);
            $message = "Solution added successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error adding solution: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

// Handle Edit Solution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_solution'])) {
    $solution_id = filter_input(INPUT_POST, 'solution_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $icon = filter_input(INPUT_POST, 'icon', FILTER_SANITIZE_STRING);
    $features = filter_input(INPUT_POST, 'features', FILTER_SANITIZE_STRING);

    if (empty($title) || empty($description)) {
        $message = "Title and description are required.";
        $message_type = "danger";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE solutions SET title = :title, description = :description, icon = :icon, features = :features WHERE solution_id = :solution_id");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':icon' => $icon,
                ':features' => $features,
                ':solution_id' => $solution_id
            ]);
            $message = "Solution updated successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error updating solution: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

// Handle Delete Solution
if (isset($_GET['delete_id'])) {
    $delete_id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $pdo->prepare("DELETE FROM solutions WHERE solution_id = :solution_id");
        $stmt->execute([':solution_id' => $delete_id]);
        $message = "Solution deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error deleting solution: " . $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solutions - AI-Solutions Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content-area {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .solution-item {
            transition: transform 0.3s ease;
        }
        .solution-item:hover {
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
                    <!-- Solutions Content -->
                    <div class="container-fluid px-4 py-4">
                        <!-- Display Messages -->
                        <?php if (isset($message)): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Solutions Management</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSolutionModal">
                                    <i class="bi bi-plus"></i> Add Solution
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM solutions ORDER BY updated_at DESC");
                                        $stmt->execute();
                                        $solutions = $stmt->fetchAll();

                                        if (count($solutions) > 0) {
                                            foreach ($solutions as $solution) {
                                    ?>
                                                <div class="list-group-item solution-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($solution['title']); ?></h6>
                                                        <small class="text-muted">Last updated: <?php echo htmlspecialchars($solution['updated_at']); ?></small>
                                                    </div>
                                                    <div>
                                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editSolutionModal" 
                                                                onclick="populateEditModal(<?php echo $solution['solution_id']; ?>, '<?php echo htmlspecialchars($solution['title']); ?>', '<?php echo htmlspecialchars($solution['description']); ?>', '<?php echo htmlspecialchars($solution['icon']); ?>', '<?php echo htmlspecialchars($solution['features']); ?>')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="adminSolution.php?delete_id=<?php echo $solution['solution_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this solution?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        } else {
                                            echo '<p class="text-muted">No solutions found.</p>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<p class="text-danger">Error fetching solutions: ' . $e->getMessage() . '</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Solution Modal -->
    <div class="modal fade" id="addSolutionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Solution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="solutionForm">
                        <input type="hidden" name="add_solution" value="1">
                        <div class="mb-3">
                            <label class="form-label">Solution Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            <select class="form-select" name="icon">
                                <option value="robot">Robot</option>
                                <option value="gear">Gear</option>
                                <option value="graph">Graph</option>
                                <option value="people">People</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Features (comma-separated)</label>
                            <input type="text" class="form-control" name="features" placeholder="Feature 1, Feature 2, Feature 3">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Solution</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Solution Modal -->
    <div class="modal fade" id="editSolutionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Solution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editSolutionForm">
                        <input type="hidden" name="edit_solution" value="1">
                        <input type="hidden" name="solution_id" id="edit_solution_id">
                        <div class="mb-3">
                            <label class="form-label">Solution Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            <select class="form-select" name="icon" id="edit_icon">
                                <option value="robot">Robot</option>
                                <option value="gear">Gear</option>
                                <option value="graph">Graph</option>
                                <option value="people">People</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Features (comma-separated)</label>
                            <input type="text" class="form-control" name="features" id="edit_features" placeholder="Feature 1, Feature 2, Feature 3">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Solution</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate Edit Modal
        function populateEditModal(id, title, description, icon, features) {
            document.getElementById('edit_solution_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_icon').value = icon;
            document.getElementById('edit_features').value = features;
        }
    </script>
</body>
</html>