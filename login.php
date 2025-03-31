<?php
session_start();
include 'db_connect.php'; // Adjust path if needed

// Handle POST request and return JSON only
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Set response type to JSON
    ob_clean(); // Clear any prior output

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Console logs injected into JSON response for debugging
    $logs = [];
    $logs[] = "PHP: Form submitted with username: $username";

    if (empty($username) || empty($password)) {
        $logs[] = "PHP: Validation failed - empty fields";
        echo json_encode(['success' => false, 'error' => 'Please enter both username and password.', 'logs' => $logs]);
        exit();
    }

    try {
        $logs[] = "PHP: Querying database for admin username: $username";
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin'");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $logs[] = "PHP: Login successful for $username";
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            echo json_encode(['success' => true, 'redirect' => 'admin.php', 'logs' => $logs]);
        } else {
            $logs[] = "PHP: Login failed - invalid credentials";
            echo json_encode(['success' => false, 'error' => 'Invalid username or password.', 'logs' => $logs]);
        }
    } catch (PDOException $e) {
        $logs[] = "PHP: Database error - " . $e->getMessage();
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage(), 'logs' => $logs]);
    }
    exit(); // Stop execution after JSON response
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AI-Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-4 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <i class="bi bi-shield-lock text-primary display-4"></i>
                                <h2 class="mt-3">Admin Login</h2>
                                <p class="text-muted">Enter your credentials to access the dashboard</p>
                            </div>
                            <div id="errorMessage" class="alert alert-danger text-center" role="alert" style="display: none;"></div>
                            <form method="POST" id="loginForm">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePassword.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });

        // Handle form submission with AJAX and console logs
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            console.log('JS: Login form submission started');
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            console.log(`JS: Captured credentials - Username: ${username}, Password length: ${password.length}`);

            if (!username || !password) {
                console.log('JS: Validation failed - Empty username or password');
                document.getElementById('errorMessage').innerText = 'Please enter both username and password.';
                document.getElementById('errorMessage').style.display = 'block';
                return;
            }

            console.log('JS: Sending login request to server');
            const formData = new FormData(this);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('JS: Received response from server');
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json(); // Expect JSON response
            })
            .then(data => {
                // Log PHP console messages from the response
                if (data.logs) {
                    data.logs.forEach(log => console.log(log));
                }

                if (data.success) {
                    console.log(`JS: Login successful, redirecting to ${data.redirect}`);
                    window.location.href = data.redirect; // Client-side redirect
                } else {
                    console.log(`JS: Login failed - ${data.error}`);
                    document.getElementById('errorMessage').innerText = data.error;
                    document.getElementById('errorMessage').style.display = 'block';
                }
            })
            .catch(error => {
                console.log(`JS: Fetch error - ${error.message}`);
                document.getElementById('errorMessage').innerText = 'An error occurred. Please try again.';
                document.getElementById('errorMessage').style.display = 'block';
            });
        });
    </script>
</body>
</html>