<?php
session_start();
include 'db_connect.php'; // Adjust path as needed

// Handle Add Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $event_date = filter_input(INPUT_POST, 'event_date', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if (empty($title) || empty($event_date) || empty($location) || empty($description)) {
        $message = "All fields are required.";
        $message_type = "danger";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (title, event_date, location, description) VALUES (:title, :event_date, :location, :description)");
            $stmt->execute([
                ':title' => $title,
                ':event_date' => $event_date,
                ':location' => $location,
                ':description' => $description
            ]);
            $message = "Event added successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error adding event: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

// Handle Edit Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_event'])) {
    $event_id = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $event_date = filter_input(INPUT_POST, 'event_date', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if (empty($title) || empty($event_date) || empty($location) || empty($description)) {
        $message = "All fields are required.";
        $message_type = "danger";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE events SET title = :title, event_date = :event_date, location = :location, description = :description WHERE event_id = :event_id");
            $stmt->execute([
                ':title' => $title,
                ':event_date' => $event_date,
                ':location' => $location,
                ':description' => $description,
                ':event_id' => $event_id
            ]);
            $message = "Event updated successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error updating event: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

// Handle Delete Event
if (isset($_GET['delete_id'])) {
    $delete_id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = :event_id");
        $stmt->execute([':event_id' => $delete_id]);
        $message = "Event deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error deleting event: " . $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - AI-Solutions Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .content-area {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .event-item {
            transition: transform 0.3s ease;
        }
        .event-item:hover {
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
                    <!-- Events Content -->
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
                                <h5 class="mb-0">Events Management</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                    <i class="bi bi-plus"></i> Add Event
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM events ORDER BY event_date DESC");
                                        $stmt->execute();
                                        $events = $stmt->fetchAll();

                                        if (count($events) > 0) {
                                            foreach ($events as $event) {
                                    ?>
                                                <div class="list-group-item event-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                                        <small class="text-muted">Date: <?php echo htmlspecialchars($event['event_date']); ?></small>
                                                    </div>
                                                    <div>
                                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editEventModal" 
                                                                onclick="populateEditModal(<?php echo $event['event_id']; ?>, '<?php echo htmlspecialchars($event['title']); ?>', '<?php echo htmlspecialchars($event['event_date']); ?>', '<?php echo htmlspecialchars($event['location']); ?>', '<?php echo htmlspecialchars($event['description']); ?>')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="adminEvent.php?delete_id=<?php echo $event['event_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this event?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        } else {
                                            echo '<p class="text-muted">No events found.</p>';
                                        }
                                    } catch (PDOException $e) {
                                        echo '<p class="text-danger">Error fetching events: ' . $e->getMessage() . '</p>';
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

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="eventForm">
                        <input type="hidden" name="add_event" value="1">
                        <div class="mb-3">
                            <label class="form-label">Event Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="event_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editEventForm">
                        <input type="hidden" name="edit_event" value="1">
                        <input type="hidden" name="event_id" id="edit_event_id">
                        <div class="mb-3">
                            <label class="form-label">Event Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="event_date" id="edit_event_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" id="edit_location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate Edit Modal
        function populateEditModal(id, title, event_date, location, description) {
            document.getElementById('edit_event_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_event_date').value = event_date;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_description').value = description;
        }
    </script>
</body>
</html>