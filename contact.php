<?php
// Database connection//
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'contacthere'
]; 

$response = ['success' => false, 'message' => '', 'errors' => []];
$form_data = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

//Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $form_data['name'] = trim($_POST['name'] ?? '');
    $form_data['email'] = trim($_POST['email'] ?? '');
    $form_data['phone'] = trim($_POST['phone'] ?? '');
    $form_data['subject'] = trim($_POST['subject'] ?? '');
    $form_data['message'] = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($form_data['name'])) {
        $response['errors'][] = 'Name is required';
    } elseif (strlen($form_data['name']) < 2) {
        $response['errors'][] = 'Name must be at least 2 characters';
    }
    
    if (empty($form_data['email'])) {
        $response['errors'][] = 'Email is required';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = 'Please enter a valid email address';
    }
    
    if (!empty($form_data['phone']) && !preg_match('/^[\d\s\-\+\(\)]{10,15}$/', $form_data['phone'])) {
        $response['errors'][] = 'Please enter a valid phone number';
    }
    
    if (empty($form_data['subject'])) {
        $response['errors'][] = 'Subject is required';
    }
    
    if (empty($form_data['message'])) {
        $response['errors'][] = 'Message is required';
    } elseif (strlen($form_data['message']) < 10) {
        $response['errors'][] = 'Message must be at least 10 characters';
    }
    
    if (empty($response['errors'])) {
        try {
            $pdo = new PDO(
                "mysql:host={$db_config['host']};dbname={$db_config['database']};charset=utf8mb4",
                $db_config['username'],
                $db_config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $stmt = $pdo->prepare("
                INSERT INTO contactdb (name, email, phone, subject, message, created_at, ip_address) 
                VALUES (?, ?, ?, ?, ?, NOW(), ?)
            ");
            
            $stmt->execute([
                $form_data['name'],
                $form_data['email'],
                $form_data['phone'],
                $form_data['subject'],
                $form_data['message'],
                $_SERVER['REMOTE_ADDR']
            ]);
            
            $response['success'] = true;
            $response['message'] = 'Thank you for contacting us! We will get back to you within 24 hours.';
            
            // Clear form data//
            $form_data = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];
            
        } catch (PDOException $e) {
            $response['errors'][] = 'Sorry, there was an error sending your message. Please try again.';
            error_log("Contact form error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Grand Vista Hotel</title>

    <!-- Fonts & Icons -->
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="shortcut icon" href="GV logo.ico" type="image/x-icon" />
    <link rel="stylesheet" href="style.css" />
    <style>
          body {
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--border-gray) 100%);
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color:var(--)
       }
      /* Main Content */
      .page-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
      }

      .page-title {
        text-align: center;
        font-family: "Playfair Display", serif;
        font-size: 3rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 2rem 0;
      }

      .page-subtitle {
        text-align: center;
        font-size: 1.1rem;
        color: var(--text-light);
        margin-bottom: 3rem;
      }

      /* Contact Section */
      .contact-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        margin-bottom: 4rem;
      }

      .contact-info {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      }

      .contact-info h3 {
        font-family: "Playfair Display", serif;
        font-size: 1.8rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
      }

      .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
      }

      .contact-item i {
        font-size: 1.2rem;
        color: var(--secondary-color);
        margin-right: 1rem;
        width: 20px;
      }

      .contact-item span {
        font-size: 1rem;
        color: var(--text-dark);
      }
      
      .contact-form {
        background: white;
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
      }
      
           .btn-submit {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(184, 134, 11, 0.3);
        }

      /* Alert Styles */
      .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 1px solid transparent;
      }

      .alert-success {
        background-color: #d1edff;
        border-color: #bee5eb;
        color: #0c5460;
      }

      .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
      }

      /* Responsive Design */
      @media (max-width: 768px) {
        .navbar {
          flex-direction: column;
          gap: 1rem;
        }

        .nav-links {
          gap: 1rem;
        }

        .page-title {
          font-size: 2rem;
        }

        .contact-container {
          grid-template-columns: 1fr;
          gap: 2rem;
        }

        .page-content {
          padding: 1rem;
        }
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <header class="header">
      <nav class="navbar">
        <div class="logo">
          <a href="index.html">Grand Vista Hotel</a>
        </div>
        <ul class="nav-links">
          <li><a href="index.html">Home</a></li>
          <li>
            <a href="index.html#rooms-btn">Rooms</a>
          </li>
          <li><a href="booking.php">Booking</a></li>
          <li><a href="contact.php" class="active">Contact</a></li>
        </ul>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="page-content">
      <h1 class="page-title">Get In Touch</h1>
      <p class="page-subtitle">
        We'd love to hear from you. Send us a message and we'll respond as soon
        as possible.
      </p>

      <!-- Contact Section -->
      <div class="contact-container">
        <!-- Contact Information -->
        <div class="contact-info">
          <h3>Contact Information</h3>

          <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <span
              >Grand Vista Hotel, City Center<br />Kolkata, West Bengal,
              India</span
            >
          </div>

          <div class="contact-item">
            <i class="fas fa-phone"></i>
            <span>+91 9898989898</span>
          </div>

          <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <span>info@grandvistahotel.com</span>
          </div>

          <div class="contact-item">
            <i class="fas fa-clock"></i>
            <span>24/7 Customer Support</span>
          </div>

          <div class="contact-item">
            <i class="fas fa-wifi"></i>
            <span>Free WiFi Available</span>
          </div>

          <div class="contact-item">
            <i class="fas fa-car"></i>
            <span>Free Parking Available</span>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form">
          <h3>Send us a Message</h3>

          <!-- Display Messages -->
          <?php if ($response['success']): ?>
          <div class="alert alert-success">
            <?php echo htmlspecialchars($response['message']); ?>
          </div>
          <?php endif; ?>

          <?php if (!empty($response['errors'])): ?>
          <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1rem">
              <?php foreach ($response['errors'] as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input
                type="text" id="name" name="name"class="form-control"
                value="<?php echo htmlspecialchars($form_data['name']); ?>"
                required
              />
            </div>

            <div class="form-group">
              <label for="email">Email Address *</label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="<?php echo htmlspecialchars($form_data['email']); ?>"
                required
              />
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input
                type="tel" id="phone" name="phone" class="form-control"
                value="<?php echo htmlspecialchars($form_data['phone']); ?>"
              />
            </div>

            <div class="form-group">
              <label for="subject">Subject *</label>
              <input
                type="text" id="subject" name="subject" class="form-control"
                value="<?php echo htmlspecialchars($form_data['subject']); ?>"
                required
              />
            </div>

            <div class="form-group">
              <label for="message">Message *</label>
              <textarea
                id="message" name="message" class="form-control" rows="5" required
                placeholder="Please tell us how we can help you..."
              >
<?php echo htmlspecialchars($form_data['message']); ?></textarea
              >
            </div>

            <button type="submit" class="btn-submit">
              <i class="fas fa-paper-plane"></i> Send Message
            </button>
          </form>
        </div>
      </div>

      <!-- Map Section -->
      <div class="map-section">
        <h3>Our Location</h3>
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed/v1/place?q=kolkata&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8"
            width="100%"
            height="400"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Grand Vista Hotel Location"
          ></iframe>
        </div>
        <p style="margin-top: 1rem; color: var(--text-light)">
          Located in the heart the city with easy access to major attractions
        </p>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="footer-section">
          <h3>Grand Vista Hotel</h3>
          <p>
            Experience luxury and comfort in the heart of the city. Your perfect
            stay awaits.
          </p>
          <div class="social-icons">
            <a href="#" aria-label="Facebook"
              ><i class="fab fa-facebook-f"></i
            ></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="Instagram"
              ><i class="fab fa-instagram"></i
            ></a>
            <a href="#" aria-label="LinkedIn"
              ><i class="fab fa-linkedin-in"></i
            ></a>
          </div>
        </div>

        <div class="footer-section">
          <h3>Contact Info</h3>
         <p><i class="fas fa-map-marker-alt"></i><a href="contact.php#mapG_heading">Grand Vista Hotel, City Center</a></p>
          <p><i class="fas fa-phone"></i> +91 9898989898</p>
          <p><i class="fas fa-envelope"></i> info@grandvistahotel.com</p>
        </div>

        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li>
              <a href="index.html#rooms-btn"
                >Our Rooms</a
              >
            </li>
            <li><a href="booking.php">Book Now</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="gallery.html">Gallery</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h3>Services</h3>
          <ul>
            <li><a href="#">Room Service</a></li>
            <li><a href="#">Spa & Wellness</a></li>
            <li><a href="#">Business Center</a></li>
            <li><a href="#">Event Spaces</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2024-25 Grand Vista Hotel. All rights reserved.</p>
      </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
