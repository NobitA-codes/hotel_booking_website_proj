<?php
// Database connection
function connectDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "userbookings";

    try {
        $conn = new mysqli($servername, $username, $password, $database);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        // Set charset //
        $conn->set_charset("utf8");
        
        return $conn;
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        die("Database connection failed. Please try again later.");
    }
}

// Validate form data//
function validateFormData($data) {
    $errors = [];   
    // Required fields
    $required_fields = ['firstName', 'lastName', 'email', 'phone', 'checkin', 'checkout', 'roomType', 'rooms'];
    
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst($field) . " is required.";
        }
    }
   
    // Date validation
    if (!empty($data['checkin']) && !empty($data['checkout'])) {
        $checkin = new DateTime($data['checkin']);
        $checkout = new DateTime($data['checkout']);
        $today = new DateTime();
        $today->setTime(0, 0, 0); // beginning of day
        
        if ($checkin < $today) {
            $errors[] = "Check-in date cannot be in the past.";
        }
        
        if ($checkout <= $checkin) {
            $errors[] = "Check-out date must be after check-in date.";
        }
    }   
    return $errors;
}

//Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $errors = validateFormData($_POST);
        
        if (!empty($errors)) {
            $errorMessage = implode("\\n", $errors);
            echo "<script>alert('Please fix the following errors:\\n" . $errorMessage . "'); window.history.back();</script>";
            exit();
        }
        
        // Connect to database
        $conn = connectDatabase();
        
        // Get form data
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];
        $roomType = $_POST['roomType'];
        $rooms = (int)$_POST['rooms'];
        
        //Nights and Total amount//
        $checkInDate = new DateTime($checkin);
        $checkOutDate = new DateTime($checkout);
        $nights = $checkInDate->diff($checkOutDate)->days;
        
        // Room prices
        $roomPrices = [
            'standard' => 999,
            'deluxe' => 1999,
            'executive' => 2999,
            'presidential' => 1299
        ];
        
        $roomPrice = $roomPrices[$roomType] ?? 0;
        $totalAmount = $nights * $roomPrice * $rooms;
        
        if ($totalAmount <= 0) {
            echo "<script>alert('Invalid room type or calculation error.'); window.history.back();</script>";
            exit();
        }
        
        // Data Insertion//
        $sql = "INSERT INTO `bookings` (`first_name`, `last_name`, `email`, `phone`, `check_in_date`, `check_out_date`, `room_type`, `number_of_rooms`, `nights`, `total_amount`, `booking_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Bind parameters 
        $stmt->bind_param("sssssssiid", 
            $firstName,
            $lastName, 
            $email,
            $phone,
            $checkin,
            $checkout,
            $roomType,
            $rooms,
            $nights,
            $totalAmount
        );
        
        // Execute the statement
        if ($stmt->execute()) {
            $bookingId = $conn->insert_id;
            echo '<script>
                alert("Booking confirmed successfully!\\n\\nBooking ID: ' . $bookingId . '\\nTotal Amount: ₹' . number_format($totalAmount) . ' for ' . $nights . ' nights\\n\\nThank you   for choosing Grand Vista Hotel!");
                window.location.href = "booking.php";
            </script>';
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        error_log("Booking error: " . $e->getMessage());
        echo '<script>alert("Booking failed due to a system error. Please try again later or contact support."); window.history.back();</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Rooms - Grand Vista Hotel</title>
    <!-- GOOGLE FONTS AND ICONS -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="GV logo.ico" type="image/x-icon" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--border-gray) 100%);
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            box-shadow: var(--shadow-sm);
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        /* Hero Section */
        .hero-section {
            height: 79vh;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(99, 191, 255, 0.1)),
                url('https://images.unsplash.com/photo-1689729830269-0ea04c62ecce?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            margin-bottom: var(--spacing-md);
            opacity: 0;
            animation: fadeInUp 1s ease 0.5s forwards;
            color: var(--white);
        }

        .hero-content p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            margin-bottom: var(--spacing-lg);
            opacity: 0;
            animation: fadeInUp 1s ease 0.7s forwards;
            color: var(--light-gray);
        }

        /* Booking Section */
        .booking-section {
            padding: clamp(2rem, 5vw, 5rem) 5%;
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--border-gray) 100%);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: var(--spacing-xl);
            max-width: 1400px;
            margin: 0 auto;
        }

        .booking-info {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .info-card {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: all var(--transition-normal);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .info-card h3 {
            color: var(--primary-color);
            margin-bottom: var(--spacing-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            font-size: clamp(1rem, 2vw, 1.2rem);
        }

        .info-card h3 i {
            color: var(--secondary-color);
            font-size: 1.5rem;
        }

        .info-card p {
            font-size: clamp(0.9rem, 1.5vw, 1rem);
            line-height: 1.6;
        }

        .contact-info {
            background: var(--primary-color);
            color: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            text-align: center;
        }

        .contact-info h3 {
            color: var(--white);
            margin-bottom: var(--spacing-md);
        }

        .contact-info p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: var(--spacing-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
        }

        .contact-info i {
            color: var(--white);
        }

        .contact-info a {
            color: var(--white);
            text-decoration: none;
        }

        /* Booking Form */
        .booking-form {
            background: white;
            padding: clamp(2rem, 4vw, 3rem);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 1s ease 0.5s forwards;
        }

        .form-header {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }

        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3vw, 2.2rem);
            color: var(--neutral-dark);
            margin-bottom: var(--spacing-sm);
        }

        .form-header p {
            color: #666;
            font-size: clamp(0.9rem, 1.5vw, 1rem);
        }

        .form-group {
            margin-bottom: 1.7rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-md);
            font-weight: 600;
            color: var(--neutral-dark);
            font-size: clamp(0.8rem, 1.2vw, 0.9rem);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: var(--spacing-md);
            border: 2px solid var(--border-gray);
            border-radius: var(--radius-md);
            font-size: clamp(0.9rem, 1.5vw, 1rem);
            transition: all var(--transition-normal);
            background: var(--light-gray);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-size: clamp(1rem, 1.5vw, 1.1rem);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            margin-top: var(--spacing-md);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }

        /* Room Selection */
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--spacing-md);
            margin-top: var(--spacing-md);
        }

        .room-option {
            border: 2px solid var(--border-gray);
            border-radius: var(--radius-md);
            padding: var(--spacing-md);
            cursor: pointer;
            transition: all var(--transition-normal);
            background: var(--light-gray);
            text-align: center;
        }

        .room-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .room-option.selected {
            border-color: var(--primary-color);
            background: rgba(52, 152, 219, 0.1);
        }

        .room-option h4 {
            color: var(--neutral-dark);
            margin-bottom: var(--spacing-sm);
            font-size: clamp(1rem, 1.5vw, 1.1rem);
            text-align: center;
        }

        .room-price {
            color: var(--secondary-color);
            font-weight: 600;
            font-size: clamp(1rem, 1.5vw, 1.1rem);
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .booking-section {
                grid-template-columns: 1fr;
                gap: var(--spacing-lg);
            }

            .hero-section {
                height: 60vh;
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav-links {
                display: none;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .room-grid {
                grid-template-columns: 1fr;
            }

            .hero-section {
                height: 50vh;
                margin-top: 60px;
            }

            .booking-section {
                padding: var(--spacing-lg) 5%;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 1rem 3%;
            }

            .booking-section {
                padding: var(--spacing-md) 3%;
            }

            .booking-form {
                padding: var(--spacing-lg);
            }

            .room-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-sm);
            }
        }
    </style>
</head>

<body>
    <!-- Main Navbar -->
    <header class="header">
        <nav class="navbar">
            <div class="logo"><a href="index.html">Grand Vista Hotel</a></div>
            <ul class="nav-links" style="font-family:var(--font-third);">
                <li><a href="index.html"></div>Home</a></li>
                <li id="rooms-href"><a href="index.html#rooms-btn">Rooms</a></li>
                <li><a href="booking.php" class="active">Booking</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="hamburger"><span></span><span></span><span></span></div>
        </nav>
    </header>

    <section class="hero-section">
        <div class="hero-content">
            <h1>Book Your Perfect Stay</h1>
            <p>Experience luxury and comfort at Grand Vista Hotel</p>
        </div>
    </section>

    <!-- Booking Section -->
    <section class="booking-section">
        <div class="booking-info">
            <div class="info-card">
                <h3><i class="fas fa-shield-alt"></i>Secure Booking</h3>
                <p>Don't worry! Your personal information and privacy is well protected with industry-standard encryption.</p>
            </div>
            <div class="info-card">
                <h3><i class="fas fa-calendar-times"></i>Free Cancellation</h3>
                <p>Cancel up to 24 hours before your stay for a full refund, without any extra payments.</p>
            </div>
            <div class="info-card">
                <h3><i class="fas fa-headset"></i>24/7 Customer Support</h3>
                <p>Our customer service team is available round the clock to assist you.</p>
            </div>
            <div class="contact-info">
                <h3>Need Help?</h3>
                <p><i class="fas fa-clock"></i>Available 24/7</p>
                <a href="contact.php">
                    <p><i class="fas fa-phone"></i>Contact Us Here</p>
                </a>
                <p><i class="fas fa-envelope"></i>reservations@grandvistahotel.com</p>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="booking-form">
            <div class="form-header">
                <h2>Make a Reservation</h2>
                <p>Fill out the form carefully below to book your perfect stay with us!</p>
            </div>
            <form id="bookingForm" action="booking.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="checkin">Check-in Date *</label>
                        <input type="date" id="checkin" name="checkin" required>
                    </div>
                    <div class="form-group">
                        <label for="checkout">Check-out Date *</label>
                        <input type="date" id="checkout" name="checkout" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="guests">Number of Guests *</label>
                        <select id="guests" name="guests" required>
                            <option value="">Select guests</option>
                            <option value="1">1 Guest</option>
                            <option value="2">2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5+ Guests</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rooms">Number of Rooms *</label>
                        <select id="rooms" name="rooms" required>
                            <option value="">Select rooms</option>
                            <option value="1">1 Room</option>
                            <option value="2">2 Rooms</option>
                            <option value="3">3 Rooms</option>
                            <option value="4">4+ Rooms</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Room Type *</label>
                    <div class="room-grid">
                        <div class="room-option" data-room="standard" data-price="999">
                            <h4>Standard Room</h4>
                            <p class="room-price">₹999/night</p>
                        </div>
                        <div class="room-option" data-room="deluxe" data-price="1999">
                            <h4>Deluxe Suite</h4>
                            <p class="room-price">₹1999/night</p>
                        </div>
                        <div class="room-option" data-room="executive" data-price="2999">
                            <h4>Executive Room</h4>
                            <p class="room-price">₹2999/night</p>
                        </div>
                        <div class="room-option" data-room="presidential" data-price="1299">
                            <h4>Presidential Suite</h4>
                            <p class="room-price">₹1299/night</p>
                        </div>
                    </div>
                    <input type="hidden" id="roomType" name="roomType" required>
                </div>
                <button type="submit" class="submit-btn">Book Now</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Grand Vista Hotel</h3>
                <p>Experience the pinnacle of luxury and comfort in the heart of the city.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Contact Info</h3>
                <p><i class="fas fa-map-marker-alt"></i><a href="contact.php#mapG_heading">Grand Vista Hotel, City Center</a></p>
                <p><i class="fas fa-phone"></i>+91 9898989898</p>
                <p><i class="fas fa-envelope"></i>info@grandvistahotel.com</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.html#rooms-btn">Our Rooms</a></li>
                    <li><a href="booking.php">Book Now</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="#">Gallery</a></li>
                    <li><a href="contact.php">Reviews</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Services</h3>
                <ul>
                    <li><a href="#">Room Service</a></li>
                    <li><a href="#">Spa & Wellness</a></li>
                    <li><a href="#">Business Center</a></li>
                    <li><a href="#">Event Spaces</a></li>
                    <li><a href="#">Airport Transfer</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024-25 Grand Vista Hotel. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Room selection functionality
        const roomOptions = document.querySelectorAll('.room-option');
        const roomTypeInput = document.getElementById('roomType');

        roomOptions.forEach(option => {
            option.addEventListener('click', function() {
                roomOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                roomTypeInput.value = this.dataset.room;
            });
        });
    </script>
</body>

</html>