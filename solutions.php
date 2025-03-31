<?php
include 'db_connect.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Solutions - AI-Solutions</title>
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
                        <a class="nav-link active" href="solutions.php">Solutions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gallery.php">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Solutions Header -->
    <section class="bg-primary text-white py-5 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold">Our Solutions</h1>
                    <p class="lead">Innovative AI-powered solutions for the modern workplace</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Grid -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <?php
                try {
                    // Fetch solutions from the database
                    $stmt = $pdo->prepare("SELECT * FROM solutions ORDER BY created_at DESC");
                    $stmt->execute();
                    $solutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($solutions) > 0) {
                        foreach ($solutions as $solution) {
                            // Convert icon to Bootstrap Icons class (prepend "bi-" if not already present)
                            $icon_class = !empty($solution['icon']) ? (strpos($solution['icon'], 'bi-') === 0 ? $solution['icon'] : 'bi-' . $solution['icon']) : 'bi-robot';
                            
                            // Parse comma-separated features into an array
                            $features = !empty($solution['features']) ? array_map('trim', explode(',', $solution['features'])) : [];
                            ?>
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-4">
                                            <i class="bi <?php echo htmlspecialchars($icon_class); ?> text-primary display-4 me-3"></i>
                                            <h3><?php echo htmlspecialchars($solution['title']); ?></h3>
                                        </div>
                                        <p class="text-muted"><?php echo htmlspecialchars($solution['description']); ?></p>
                                        <?php if (!empty($features)) : ?>
                                            <ul class="list-unstyled">
                                                <?php foreach ($features as $feature) : ?>
                                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><?php echo htmlspecialchars($feature); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p class="text-center text-muted">No solutions available at this time.</p>';
                    }
                } catch (PDOException $e) {
                    echo '<p class="text-center text-danger">Error fetching solutions: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Case Studies -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Success Stories</h2>
            <div class="row g-4">
                <?php
                try {
                    // Fetch success stories from the database, limited to 3 as per original design
                    $stmt = $pdo->prepare("SELECT * FROM success_stories ORDER BY updated_at DESC LIMIT 3");
                    $stmt->execute();
                    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($stories) > 0) {
                        foreach ($stories as $story) {
                            // Use placeholder image if no image is uploaded
                            $image_url = !empty($story['image_path']) ? htmlspecialchars($story['image_path']) : 'https://via.placeholder.com/800x400';
                            ?>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <img src="<?php echo $image_url; ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($story['title']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($story['title']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($story['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        // Fallback to static content if no stories are found, to maintain design
                        ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                     class="card-img-top" 
                                     alt="TechCorp Case Study">
                                <div class="card-body">
                                    <h5 class="card-title">TechCorp Transformation</h5>
                                    <p class="card-text">40% increase in productivity after implementing our AI Virtual Assistant solution.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                     class="card-img-top" 
                                     alt="Future Labs Case Study">
                                <div class="card-body">
                                    <h5 class="card-title">Future Labs Innovation</h5>
                                    <p class="card-text">Revolutionized customer service with 24/7 AI support system.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                     class="card-img-top" 
                                     alt="InnovateX Case Study">
                                <div class="card-body">
                                    <h5 class="card-title">InnovateX Success</h5>
                                    <p class="card-text">Achieved 60% faster project completion with workflow automation.</p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } catch (PDOException $e) {
                    echo '<p class="text-center text-danger">Error fetching success stories: ' . htmlspecialchars($e->getMessage()) . '</p>';
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