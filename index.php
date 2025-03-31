<?php
include 'db_connect.php'; // Adjust path as needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Solutions - Empowering Innovation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .hero-section {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: none; /* Remove any unwanted background */
        }

        .hero-section .carousel {
            width: 100%;
        }

        .hero-section .carousel-item {
            height: 75vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            width: 100%;
            position: relative;
        }

        .hero-section .carousel-caption {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 20px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.3); /* Optional: slight overlay for better text readability */
        }

        .hero-section .btn {
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            width: auto; /* Prevent full-width */
            display: inline-block; /* Ensure proper button sizing */
        }

        .btn {
            padding: 8px 16px;
            font-size: 0.9rem;
            width: auto; /* Prevent full-width */
        }

        .btn-lg {
            padding: 12px 24px;
            font-size: 1.1rem;
            width: auto; /* Prevent full-width */
        }

        .btn:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .testimonials-carousel .carousel-inner {
            display: flex;
            flex-wrap: nowrap;
        }

        .testimonials-carousel .carousel-item {
            flex: 0 0 33.3333%;
            max-width: 33.3333%;
        }
    </style>
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

    <!-- Hero Section with Carousel -->
    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" style="background-image: url('img/carsoul-a.jpg');">
                    <div class="carousel-caption">
                        <h1 class="display-4 fw-bold mb-4">Empowering Innovation Through AI</h1>
                        <p class="lead mb-4">Revolutionizing the digital employee experience with AI-powered solutions</p>
                        <a href="contact.php" class="btn btn-light">Get Started <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('img/carsoul-b.jpg');">
                    <div class="carousel-caption">
                        <h1 class="display-4 fw-bold mb-4">Advanced AI Technology</h1>
                        <p class="lead mb-4">Leveraging cutting-edge AI to drive your business forward</p>
                        <a href="contact.php" class="btn btn-light">Get Started <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
                    <div class="carousel-caption">
                        <h1 class="display-4 fw-bold mb-4">Smart Solutions</h1>
                        <p class="lead mb-4">Intelligent automation for a smarter workplace</p>
                        <a href="contact.php" class="btn btn-light">Get Started <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose AI-Solutions?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <i class="bi bi-lightning-charge text-primary display-4 mb-3"></i>
                        <h3 class="h4">Rapid Solutions</h3>
                        <p class="text-muted">Quick and efficient AI-powered solutions for your business needs</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <i class="bi bi-award text-primary display-4 mb-3"></i>
                        <h3 class="h4">Industry Excellence</h3>
                        <p class="text-muted">Award-winning solutions trusted by leading companies</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <i class="bi bi-people text-primary display-4 mb-3"></i>
                        <h3 class="h4">Customer Focus</h3>
                        <p class="text-muted">Dedicated support and personalized solutions for your success</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">What Our Clients Say</h2>
            <?php
            try {
                $stmt = $pdo->prepare("SELECT * FROM testimonials ORDER BY updated_at DESC");
                $stmt->execute();
                $testimonials = $stmt->fetchAll();
                $testimonial_count = count($testimonials);

                if ($testimonial_count > 0) {
                    if ($testimonial_count <= 3) {
                        echo '<div class="row g-4">';
                        foreach ($testimonials as $testimonial) {
            ?>
                            <div class="col-lg-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="<?php echo htmlspecialchars($testimonial['image_path'] ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'); ?>" 
                                                 alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>" 
                                                 class="rounded-circle me-3" 
                                                 width="48">
                                            <div>
                                                <h5 class="mb-0"><?php echo htmlspecialchars($testimonial['author_name']); ?></h5>
                                                <p class="text-muted mb-0"><?php echo htmlspecialchars($testimonial['company'] ?? 'N/A'); ?></p>
                                            </div>
                                        </div>
                                        <p class="text-muted">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                                    </div>
                                </div>
                            </div>
            <?php
                        }
                        echo '</div>';
                    } else {
                        echo '<div id="testimonialsCarousel" class="carousel slide testimonials-carousel" data-bs-ride="carousel" data-bs-interval="3000">';
                        echo '<div class="carousel-inner">';
                        foreach ($testimonials as $index => $testimonial) {
                            $active_class = ($index === 0) ? ' active' : '';
            ?>
                            <div class="carousel-item<?php echo $active_class; ?>">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="<?php echo htmlspecialchars($testimonial['image_path'] ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'); ?>" 
                                                 alt="<?php echo htmlspecialchars($testimonial['author_name']); ?>" 
                                                 class="rounded-circle me-3" 
                                                 width="48">
                                            <div>
                                                <h5 class="mb-0"><?php echo htmlspecialchars($testimonial['author_name']); ?></h5>
                                                <p class="text-muted mb-0"><?php echo htmlspecialchars($testimonial['company'] ?? 'N/A'); ?></p>
                                            </div>
                                        </div>
                                        <p class="text-muted">"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                                    </div>
                                </div>
                            </div>
            <?php
                        }
                        echo '</div>';
                        echo '<button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">';
                        echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                        echo '<span class="visually-hidden">Previous</span>';
                        echo '</button>';
                        echo '<button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">';
                        echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                        echo '<span class="visually-hidden">Next</span>';
                        echo '</button>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="text-center text-muted">No testimonials available at this time.</p>';
                }
            } catch (PDOException $e) {
                echo '<p class="text-center text-danger">Error fetching testimonials: ' . $e->getMessage() . '</p>';
            }
            ?>
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