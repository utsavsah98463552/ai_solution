<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); // Updated to match earlier convention
    exit();
}

include 'db_connect.php'; // Adjust path as needed

// Ensure upload directory exists
$upload_dir = 'uploads/testimonials/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle Add Testimonial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_testimonial'])) {
    $author_name = filter_input(INPUT_POST, 'author_name', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
    $image_path = null;

    if (empty($author_name) || empty($content)) {
        $message = "Author name and content are required.";
        $message_type = "danger";
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $image_name = uniqid() . '-' . basename($image['name']);
            $image_path = $upload_dir . $image_name;

            if (!move_uploaded_file($image['tmp_name'], $image_path)) {
                $message = "Error uploading image.";
                $message_type = "danger";
            }
        }

        if (!isset($message)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO testimonials (author_name, content, company, image_path) VALUES (:author_name, :content, :company, :image_path)");
                $stmt->execute([
                    ':author_name' => $author_name,
                    ':content' => $content,
                    ':company' => $company,
                    ':image_path' => $image_path
                ]);
                $message = "Testimonial added successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Error adding testimonial: " . $e->getMessage();
                $message_type = "danger";
                if ($image_path && file_exists($image_path)) unlink($image_path); // Clean up on error
            }
        }
    }
}

// Handle Edit Testimonial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_testimonial'])) {
    $testimonial_id = filter_input(INPUT_POST, 'testimonial_id', FILTER_SANITIZE_NUMBER_INT);
    $author_name = filter_input(INPUT_POST, 'author_name', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
    $existing_image = filter_input(INPUT_POST, 'existing_image', FILTER_SANITIZE_STRING);
    $image_path = $existing_image;

    if (empty($author_name) || empty($content)) {
        $message = "Author name and content are required.";
        $message_type = "danger";
    } else {
        // Handle image upload (optional for edit)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $image_name = uniqid() . '-' . basename($image['name']);
            $image_path = $upload_dir . $image_name;

            if (move_uploaded_file($image['tmp_name'], $image_path)) {
                if ($existing_image && file_exists($existing_image)) {
                    unlink($existing_image); // Delete old image
                }
            } else {
                $message = "Error uploading image.";
                $message_type = "danger";
            }
        }

        if (!isset($message)) {
            try {
                $stmt = $pdo->prepare("UPDATE testimonials SET author_name = :author_name, content = :content, company = :company, image_path = :image_path WHERE testimonial_id = :testimonial_id");
                $stmt->execute([
                    ':author_name' => $author_name,
                    ':content' => $content,
                    ':company' => $company,
                    ':image_path' => $image_path,
                    ':testimonial_id' => $testimonial_id
                ]);
                $message = "Testimonial updated successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Error updating testimonial: " . $e->getMessage();
                $message_type = "danger";
            }
        }
    }
}

// Handle Delete Testimonial
if (isset($_GET['delete_id'])) {
    $delete_id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM testimonials WHERE testimonial_id = :testimonial_id");
        $stmt->execute([':testimonial_id' => $delete_id]);
        $testimonial = $stmt->fetch();
        if ($testimonial['image_path'] && file_exists($testimonial['image_path'])) {
            unlink($testimonial['image_path']); // Delete image file
        }

        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE testimonial_id = :testimonial_id");
        $stmt->execute([':testimonial_id' => $delete_id]);
        $message = "Testimonial deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error deleting testimonial: " . $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - AI-Solutions Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content-area {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .testimonial-item {
            transition: transform 0.3s ease;
        }
        .testimonial-item:hover {
            transform: translateY(-5px);
        }
        .testimonial-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
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
                    <!-- Testimonials Content -->
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
                                <h5 class="mb-0">Testimonials Management</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                                    <i class="bi bi-plus"></i> Add Testimonial
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM testimonials ORDER BY updated_at DESC");
                                        $stmt->execute();
                                        $testimonials = $stmt->fetchAll();

                                        if (count($testimonials) > 0) {
                                            foreach ($testimonials as $testimonial) {
                                    ?>
                                                <div class="list-group-item testimonial-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($testimonial['image_path']): ?>
                                                            <img src="<?php echo htmlspecialchars($testimonial['image_path']); ?>" alt="Author Image" class="testimonial-img me-3">
                                                        <?php endif; ?>
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($testimonial['author_name']); ?></h6>
                                                            <small class="text-muted">Company: <?php echo htmlspecialchars($testimonial['company'] ?? 'N/A'); ?> | Last updated: <?php echo htmlspecialchars($testimonial['updated_at']); ?></small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editTestimonialModal" 
                                                                onclick="populateEditModal(<?php echo $testimonial['testimonial_id']; ?>, '<?php echo htmlspecialchars($testimonial['author_name']); ?>', '<?php echo htmlspecialchars($testimonial['content']); ?>', '<?php echo htmlspecialchars($testimonial['company'] ?? ''); ?>', '<?php echo htmlspecialchars($testimonial['image_path'] ?? ''); ?>')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="adminTestimonials.php?delete_id=<?php echo $testimonial['testimonial_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this testimonial?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        } else {
                                            echo '<p class="text-muted">No testimonials found.</p>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<p class="text-danger">Error fetching testimonials: ' . $e->getMessage() . '</p>';
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

    <!-- Add Testimonial Modal -->
    <div class="modal fade" id="addTestimonialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="testimonialForm" enctype="multipart/form-data">
                        <input type="hidden" name="add_testimonial" value="1">
                        <div class="mb-3">
                            <label class="form-label">Author Name</label>
                            <input type="text" class="form-control" name="author_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company (Optional)</label>
                            <input type="text" class="form-control" name="company">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Author Image (Optional)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Testimonial</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Testimonial Modal -->
    <div class="modal fade" id="editTestimonialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editTestimonialForm" enctype="multipart/form-data">
                        <input type="hidden" name="edit_testimonial" value="1">
                        <input type="hidden" name="testimonial_id" id="edit_testimonial_id">
                        <input type="hidden" name="existing_image" id="edit_existing_image">
                        <div class="mb-3">
                            <label class="form-label">Author Name</label>
                            <input type="text" class="form-control" name="author_name" id="edit_author_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" id="edit_content" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company (Optional)</label>
                            <input type="text" class="form-control" name="company" id="edit_company">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div id="current_image_preview"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Image (Optional)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Testimonial</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate Edit Modal
        function populateEditModal(id, author_name, content, company, image_path) {
            document.getElementById('edit_testimonial_id').value = id;
            document.getElementById('edit_author_name').value = author_name;
            document.getElementById('edit_content').value = content;
            document.getElementById('edit_company').value = company;
            document.getElementById('edit_existing_image').value = image_path;
            const preview = document.getElementById('current_image_preview');
            if (image_path) {
                preview.innerHTML = `<img src="${image_path}" alt="Current Image" class="testimonial-img">`;
            } else {
                preview.innerHTML = 'No image uploaded.';
            }
        }
    </script>
</body>
</html>