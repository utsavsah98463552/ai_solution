<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include 'db_connect.php'; // Adjust path as needed

// Ensure upload directory exists
$upload_dir = 'uploads/success_stories/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle Add Success Story
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_story'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $image_path = null;

    if (empty($title) || empty($description)) {
        $message = "Title and description are required.";
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
                $stmt = $pdo->prepare("INSERT INTO success_stories (title, description, image_path) VALUES (:title, :description, :image_path)");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':image_path' => $image_path
                ]);
                $message = "Success story added successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Error adding success story: " . $e->getMessage();
                $message_type = "danger";
                if ($image_path && file_exists($image_path)) unlink($image_path); // Clean up on error
            }
        }
    }
}

// Handle Edit Success Story
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_story'])) {
    $story_id = filter_input(INPUT_POST, 'story_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $existing_image = filter_input(INPUT_POST, 'existing_image', FILTER_SANITIZE_STRING);
    $image_path = $existing_image;

    if (empty($title) || empty($description)) {
        $message = "Title and description are required.";
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
                $stmt = $pdo->prepare("UPDATE success_stories SET title = :title, description = :description, image_path = :image_path WHERE story_id = :story_id");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':image_path' => $image_path,
                    ':story_id' => $story_id
                ]);
                $message = "Success story updated successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Error updating success story: " . $e->getMessage();
                $message_type = "danger";
            }
        }
    }
}

// Handle Delete Success Story
if (isset($_GET['delete_id'])) {
    $delete_id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM success_stories WHERE story_id = :story_id");
        $stmt->execute([':story_id' => $delete_id]);
        $story = $stmt->fetch();
        if ($story['image_path'] && file_exists($story['image_path'])) {
            unlink($story['image_path']); // Delete image file
        }

        $stmt = $pdo->prepare("DELETE FROM success_stories WHERE story_id = :story_id");
        $stmt->execute([':story_id' => $delete_id]);
        $message = "Success story deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error deleting success story: " . $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Stories - AI-Solutions Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content-area {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .story-item {
            transition: transform 0.3s ease;
        }
        .story-item:hover {
            transform: translateY(-5px);
        }
        .story-img {
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
                    <!-- Success Stories Content -->
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
                                <h5 class="mb-0">Success Stories Management</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStoryModal">
                                    <i class="bi bi-plus"></i> Add Success Story
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM success_stories ORDER BY updated_at DESC");
                                        $stmt->execute();
                                        $stories = $stmt->fetchAll();

                                        if (count($stories) > 0) {
                                            foreach ($stories as $story) {
                                    ?>
                                                <div class="list-group-item story-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($story['image_path']): ?>
                                                            <img src="<?php echo htmlspecialchars($story['image_path']); ?>" alt="Story Image" class="story-img me-3">
                                                        <?php endif; ?>
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($story['title']); ?></h6>
                                                            <small class="text-muted">Last updated: <?php echo htmlspecialchars($story['updated_at']); ?></small>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editStoryModal" 
                                                                onclick="populateEditModal(<?php echo $story['story_id']; ?>, '<?php echo htmlspecialchars($story['title']); ?>', '<?php echo htmlspecialchars($story['description']); ?>', '<?php echo htmlspecialchars($story['image_path'] ?? ''); ?>')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="adminSuccessStories.php?delete_id=<?php echo $story['story_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this success story?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        } else {
                                            echo '<p class="text-muted">No success stories found.</p>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<p class="text-danger">Error fetching success stories: ' . $e->getMessage() . '</p>';
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

    <!-- Add Success Story Modal -->
    <div class="modal fade" id="addStoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Success Story</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="storyForm" enctype="multipart/form-data">
                        <input type="hidden" name="add_story" value="1">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image (Optional)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Success Story</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Success Story Modal -->
    <div class="modal fade" id="editStoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Success Story</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editStoryForm" enctype="multipart/form-data">
                        <input type="hidden" name="edit_story" value="1">
                        <input type="hidden" name="story_id" id="edit_story_id">
                        <input type="hidden" name="existing_image" id="edit_existing_image">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div id="current_image_preview"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Image (Optional)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Success Story</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate Edit Modal
        function populateEditModal(id, title, description, image_path) {
            document.getElementById('edit_story_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_existing_image').value = image_path;
            const preview = document.getElementById('current_image_preview');
            if (image_path) {
                preview.innerHTML = `<img src="${image_path}" alt="Current Image" class="story-img">`;
            } else {
                preview.innerHTML = 'No image uploaded.';
            }
        }
    </script>
</body>
</html>