<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AI-Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .invalid-feedback { display: none; }
        .was-validated .form-control:invalid ~ .invalid-feedback { display: block; }
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
                        <a class="nav-link active" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contact Header -->
    <section class="bg-primary text-white py-5 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold">Contact Us</h1>
                    <p class="lead">Get in touch to discuss how we can help transform your business</p>
                </div>
            </div>
        </div>
    </section>

    <?php session_start(); ?>

    <!-- Contact Form -->
    <section class="py-5">
        <div class="container">
            <!-- Display Success/Error Message -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <script>
                    console.log('JS: <?php echo $_SESSION['message_type'] === 'success' ? 'Inquiry submission successful' : 'Inquiry submission failed'; ?> - <?php echo addslashes($_SESSION['message']); ?>');
                </script>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-body p-5">
                            <form id="contactForm" class="needs-validation" action="logic/submit_inquiry.php" method="POST" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                        <div class="invalid-feedback">
                                            Please provide your name.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid email.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                        <div class="invalid-feedback">
                                            Please provide your phone number.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company" class="form-label">Company</label>
                                        <input type="text" class="form-control" id="company" name="company" required>
                                        <div class="invalid-feedback">
                                            Please provide your company name.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country" required>
                                        <div class="invalid-feedback">
                                            Please provide your country.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jobTitle" class="form-label">Job Title</label>
                                        <input type="text" class="form-control" id="jobTitle" name="jobTitle" required>
                                        <div class="invalid-feedback">
                                            Please provide your job title.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="jobDetails" class="form-label">Job Details</label>
                                        <textarea class="form-control" id="jobDetails" name="jobDetails" rows="4" required></textarea>
                                        <div class="invalid-feedback">
                                            Please provide job details.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            Submit Inquiry
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="bi bi-geo-alt text-primary display-4 mb-3"></i>
                        <h4>Visit Us</h4>
                        <p class="text-muted mb-0">Sunderland, UK</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="bi bi-telephone text-primary display-4 mb-3"></i>
                        <h4>Call Us</h4>
                        <p class="text-muted mb-0">+44 (0) 123 456 7890</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="bi bi-envelope text-primary display-4 mb-3"></i>
                        <h4>Email Us</h4>
                        <p class="text-muted mb-0">info@ai-solutions.com</p>
                    </div>
                </div>
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
        // Bootstrap form validation
        (function () {
            'use strict';
            const form = document.getElementById('contactForm');
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
</body>
</html>