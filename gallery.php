<?php
include 'db_connect.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - AI-Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-brain text-primary fs-3 me-2"></i>
                <span class="fw-bold">AI-Solutions</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="solutions.php">Solutions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gallery.php">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Gallery Header -->
    <section class="bg-primary text-white py-5 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold">Our Gallery</h1>
                    <p class="lead">Showcasing our events and achievements</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Gallery (Recent Events) -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Recent Events</h2>
            <div class="row g-4">
                <?php
                try {
                    // Fetch recent events from the gallery table, limited to 3 as per original design
                    $stmt = $pdo->prepare("SELECT * FROM gallery ORDER BY updated_at DESC LIMIT 3");
                    $stmt->execute();
                    $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($gallery_items) > 0) {
                        foreach ($gallery_items as $item) {
                            ?>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($item['description'] ?? 'No description available.'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        // Fallback to static content if no gallery items are found
                        ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                     class="card-img-top" 
                                     alt="Tech Conference 2025">
                                <div class="card-body">
                                    <h5 class="card-title">Tech Conference 2025</h5>
                                    <p class="card-text">Showcasing our latest AI innovations at the annual tech conference.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                     class="card-img-top" 
                                     alt="Innovation Workshop">
                                <div class="card-body">
                                    <h5 class="card-title">Innovation Workshop</h5>
                                    <p class="card-text">Hands-on workshop with industry leaders exploring AI solutions.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                     class="card-img-top" 
                                     alt="Team Collaboration">
                                <div class="card-body">
                                    <h5 class="card-title">Team Collaboration</h5>
                                    <p class="card-text">Our team working together to deliver innovative solutions.</p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } catch (PDOException $e) {
                    echo '<p class="text-center text-danger">Error fetching recent events: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Upcoming Events</h2>
            <div class="row g-4">
                <?php
                try {
                    // Fetch upcoming events from the events table, limited to 3 as per original design
                    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3");
                    $stmt->execute();
                    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($events) > 0) {
                        foreach ($events as $event) {
                            // Format the event date
                            $event_date = date('F j, Y', strtotime($event['event_date']));
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-calendar-event text-primary fs-4 me-3"></i>
                                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($event['title']); ?></h5>
                                        </div>
                                        <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($event['location']); ?><br>
                                            <i class="bi bi-clock me-2"></i><?php echo $event_date; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        // Fallback to static content if no upcoming events are found
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar-event text-primary fs-4 me-3"></i>
                                        <h5 class="card-title mb-0">AI Summit 2025</h5>
                                    </div>
                                    <p class="card-text">Join us at the biggest AI event of the year. We'll be showcasing our latest innovations.</p>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-geo-alt me-2"></i>London, UK<br>
                                        <i class="bi bi-clock me-2"></i>September 15-17, 2025
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar-event text-primary fs-4 me-3"></i>
                                        <h5 class="card-title mb-0">Tech Meetup</h5>
                                    </div>
                                    <p class="card-text">Monthly meetup discussing the latest trends in AI and machine learning.</p>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-geo-alt me-2"></i>Sunderland, UK<br>
                                        <i class="bi bi-clock me-2"></i>Last Thursday of every month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar-event text-primary fs-4 me-3"></i>
                                        <h5 class="card-title mb-0">Innovation Workshop</h5>
                                    </div>
                                    <p class="card-text">Hands-on workshop exploring practical applications of AI in business.</p>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-geo-alt me-2"></i>Manchester, UK<br>
                                        <i class="bi bi-clock me-2"></i>October 5, 2025
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } catch (PDOException $e) {
                    echo '<p class="text-center text-danger">Error fetching upcoming events: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h4>Contact Us</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i> Sunderland, UK
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2"></i> +44 (0) 123 456 7890
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2"></i> info@ai-solutions.com
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h4>Quick Links</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="solutions.php" class="text-white text-decoration-none">Our Solutions</a></li>
                        <li class="mb-2"><a href="gallery.php" class="text-white text-decoration-none">Gallery</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h4>About AI-Solutions</h4>
                    <p class="text-muted">Innovating, promoting, and delivering the future of digital employee experience with AI-powered solutions.</p>
                </div>
            </div>
            <div class="text-center mt-4 pt-4 border-top border-secondary">
                <p class="mb-0">© <span id="year"></span> AI-Solutions. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>