<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); // Updated to match earlier convention
    exit();
}

include 'db_connect.php'; // Adjust path as needed

// Ensure upload directory exists
$upload_dir = 'uploads/gallery/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle Add Gallery Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_gallery'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if (empty($title) || !isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $message = "Title and image are required.";
        $message_type = "danger";
    } else {
        $image = $_FILES['image'];
        $image_name = uniqid() . '-' . basename($image['name']);
        $image_path = $upload_dir . $image_name;

        if ($image['error'] === UPLOAD_ERR_OK && move_uploaded_file($image['tmp_name'], $image_path)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, image_url, description) VALUES (:title, :image_url, :description)");
                $stmt->execute([
                    ':title' => $title,
                    ':image_url' => $image_path,
                    ':description' => $description
                ]);
                $message = "Gallery item added successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Error adding gallery item: " . $e->getMessage();
                $message_type = "danger";
                if (file_exists($image_path)) unlink($image_path); // Clean up on error
            }
        } else {
            $message = "Error uploading image.";
            $message_type = "danger";
        }
    }
}

// Handle Edit Gallery Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_gallery'])) {
    $gallery_id = filter_input(INPUT_POST, 'gallery_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $existing_image = filter_input(INPUT_POST, 'existing_image', FILTER_SANITIZE_STRING);
    $image_path = $existing_image;

    if (empty($title)) {
        $message = "Title is required.";
        $message_type = "danger";
    } else {
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
                $stmt = $pdo->prepare("UPDATE gallery SET title = :title, image_url = :image_url, description = :description WHERE gallery_id = :gallery_id");
                $stmt->execute([
                    ':title' => $title,
                    ':image_url' => $image_path,
                    ':description' => $description,
                    ':gallery_id' => $gallery_id
                ]);
                $message = "Gallery item updated successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Error updating gallery item: " . $e->getMessage();
                $message_type = "danger";
            }
        }
    }
}

// Handle Delete Gallery Item
if (isset($_GET['delete_id'])) {
    $delete_id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $pdo->prepare("SELECT image_url FROM gallery WHERE gallery_id = :gallery_id");
        $stmt->execute([':gallery_id' => $delete_id]);
        $gallery = $stmt->fetch();
        if ($gallery['image_url'] && file_exists($gallery['image_url'])) {
            unlink($gallery['image_url']); // Delete image file
        }

        $stmt = $pdo->prepare("DELETE FROM gallery WHERE gallery_id = :gallery_id");
        $stmt->execute([':gallery_id' => $delete_id]);
        $message = "Gallery item deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error deleting gallery item: " . $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - AI-Solutions Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content-area {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .gallery-item {
            transition: transform 0.3s ease;
        }
        .gallery-item:hover {
            transform: scale(1.05);
        }
        .gallery-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
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
                    <!-- Gallery Content -->
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
                                <h5 class="mb-0">Gallery Management</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                                    <i class="bi bi-plus"></i> Add Gallery Item
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM gallery ORDER BY updated_at DESC");
                                        $stmt->execute();
                                        $gallery_items = $stmt->fetchAll();

                                        if (count($gallery_items) > 0) {
                                            foreach ($gallery_items as $item) {
                                    ?>
                                                <div class="col-md-3">
                                                    <div class="card gallery-item">
                                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="gallery-img card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                                        <div class="card-body">
                                                            <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                            <p class="card-text text-muted"><?php echo htmlspecialchars($item['description'] ?? 'No description'); ?></p>
                                                            <div>
                                                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editGalleryModal" 
                                                                        onclick="populateEditModal(<?php echo $item['gallery_id']; ?>, '<?php echo htmlspecialchars($item['title']); ?>', '<?php echo htmlspecialchars($item['description'] ?? ''); ?>', '<?php echo htmlspecialchars($item['image_url']); ?>')">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <a href="adminGallery.php?delete_id=<?php echo $item['gallery_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this gallery item?');">
                                                                    <i class="bi bi-trash"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        } else {
                                            echo '<p class="text-muted">No gallery items found.</p>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<p class="text-danger">Error fetching gallery items: ' . $e->getMessage() . '</p>';
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

    <!-- Add Gallery Modal -->
    <div class="modal fade" id="addGalleryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Gallery Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="galleryForm" enctype="multipart/form-data">
                        <input type="hidden" name="add_gallery" value="1">
                        <div class="mb-3">
                            <label class="form-label">Image Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Gallery Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Gallery Modal -->
    <div class="modal fade" id="editGalleryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Gallery Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editGalleryForm" enctype="multipart/form-data">
                        <input type="hidden" name="edit_gallery" value="1">
                        <input type="hidden" name="gallery_id" id="edit_gallery_id">
                        <input type="hidden" name="existing_image" id="edit_existing_image">
                        <div class="mb-3">
                            <label class="form-label">Image Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div id="current_image_preview"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Image (Optional)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Gallery Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate Edit Modal
        function populateEditModal(id, title, description, image_url) {
            document.getElementById('edit_gallery_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_existing_image').value = image_url;
            const preview = document.getElementById('current_image_preview');
            if (image_url) {
                preview.innerHTML = `<img src="${image_url}" alt="Current Image" class="gallery-img">`;
            } else {
                preview.innerHTML = 'No image uploaded.';
            }
        }
    </script>
</body>
</html>