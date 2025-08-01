<?php
session_start();
require_once 'db.php';

// Get total products
$product_count = 0;
$res = pg_query($conn, 'SELECT COUNT(*) FROM products');
if ($res) {
    $row = pg_fetch_row($res);
    $product_count = $row[0];
}

// Get total sales
$sales_count = 0;
$res = pg_query($conn, 'SELECT COUNT(*) FROM sales');
if ($res) {
    $row = pg_fetch_row($res);
    $sales_count = $row[0];
}

// Get cart count for current user (from session)
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cecus - Your Trusted Computer & Technology Partner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #f59e0b;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f8fafc;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .logo-icon {
            color: var(--accent-color);
            margin-right: 8px;
        }
        
        .navbar-brand img {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05);
        }
        
        .footer-title img {
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }
        
        .footer-title:hover img {
            transform: scale(1.05);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .btn-hero {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .section-padding {
            padding: 80px 0;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: var(--text-dark);
        }
        
        .section-subtitle {
            text-align: center;
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 4rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        
        .feature-text {
            color: var(--text-light);
            line-height: 1.6;
        }
        
        .stats-section {
            background: var(--bg-light);
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
            color: var(--text-light);
            font-weight: 500;
        }
        
        .about-section {
            background: white;
        }
        
        .about-content {
            display: flex;
            align-items: center;
            gap: 3rem;
        }
        
        .about-text {
            flex: 1;
        }
        
        .about-image {
            flex: 1;
            text-align: center;
        }
        
        .about-image i {
            font-size: 8rem;
            color: var(--primary-color);
            opacity: 0.8;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .testimonial-text {
            font-style: italic;
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--accent-color) 0%, #d97706 100%);
            color: white;
            text-align: center;
        }
        
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 3rem 0 1rem;
        }
        
        .footer-section {
            margin-bottom: 2rem;
        }
        
        .footer-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .footer-link {
            color: #9ca3af;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: color 0.3s ease;
        }
        
        .social-links a:hover {
            color: var(--accent-color);
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .about-content {
                flex-direction: column;
                text-align: center;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="IMG/image.png" alt="TechStore Logo" height="40" class="me-2">
                TechStore
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="mycart.php">My Cart</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="sales.php">Sales</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Your Trusted Technology Partner</h1>
                    <p class="hero-subtitle">Discover premium computers, laptops, and tech accessories. Quality products, competitive prices, and exceptional service.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="products.php" class="btn btn-light btn-hero">
                            <i class="fas fa-shopping-cart me-2"></i>Shop Now
                        </a>
                        <a href="#about" class="btn btn-outline-light btn-hero">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-laptop-code" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $product_count; ?>+</div>
                        <div class="stat-label">Products Available</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $sales_count; ?>+</div>
                        <div class="stat-label">Successful Sales</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Customer Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="about-section section-padding">
        <div class="container">
            <h2 class="section-title">About Cecus</h2>
            <p class="section-subtitle">Your premier destination for all things technology</p>
            
            <div class="about-content">
                <div class="about-text">
                    <h3 class="mb-4">Why Choose Cecus?</h3>
                    <p class="mb-4">Founded with a passion for technology and customer satisfaction, Cecus has been serving tech enthusiasts and businesses for over a decade. We understand that technology is not just about products—it's about solutions that enhance your life and work.</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Premium Quality Products</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Expert Technical Support</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Competitive Pricing</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Fast & Secure Delivery</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section-padding" style="background: var(--bg-light);">
        <div class="container">
            <h2 class="section-title">Why Choose Us</h2>
            <p class="section-subtitle">Discover what makes TechStore the preferred choice for technology solutions</p>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="feature-title">Quality Assurance</h4>
                        <p class="feature-text">All our products undergo rigorous quality testing to ensure you receive only the best technology solutions.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4 class="feature-title">24/7 Support</h4>
                        <p class="feature-text">Our dedicated support team is available round the clock to assist you with any questions or technical issues.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4 class="feature-title">Fast Delivery</h4>
                        <p class="feature-text">Quick and secure delivery to your doorstep with real-time tracking and insurance coverage.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <h4 class="feature-title">Easy Returns</h4>
                        <p class="feature-text">Hassle-free return policy with 30-day money-back guarantee on all products.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h4 class="feature-title">Best Prices</h4>
                        <p class="feature-text">Competitive pricing with regular discounts and special offers for our valued customers.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h4 class="feature-title">Technical Expertise</h4>
                        <p class="feature-text">Professional technical support and maintenance services for all your technology needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section-padding" style="background: white;">
        <div class="container">
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-subtitle">Real feedback from satisfied customers</p>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "TechStore has been my go-to place for all computer needs. Their products are top-quality and the customer service is exceptional!"
                        </div>
                        <div class="testimonial-author">- Sarah Johnson, Business Owner</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Fast delivery and competitive prices. The technical support team helped me choose the perfect laptop for my needs."
                        </div>
                        <div class="testimonial-author">- Michael Chen, Software Developer</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Excellent quality products and outstanding after-sales support. Highly recommended for anyone looking for reliable tech solutions."
                        </div>
                        <div class="testimonial-author">- Emily Rodriguez, IT Manager</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section section-padding">
        <div class="container">
            <h2 class="mb-4">Ready to Get Started?</h2>
            <p class="mb-4">Join thousands of satisfied customers who trust TechStore for their technology needs.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="products.php" class="btn btn-light btn-hero">
                    <i class="fas fa-shopping-cart me-2"></i>Shop Now
                </a>
                <a href="contact.php" class="btn btn-outline-light btn-hero">
                    <i class="fas fa-phone me-2"></i>Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 footer-section">
                    <h5 class="footer-title">
                        <img src="IMG/image.png" alt="TechStore Logo" height="30" class="me-2">
                        TechStore
                    </h5>
                    <p>Your trusted partner for all technology solutions. Quality products, exceptional service, and competitive prices.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 footer-section">
                    <h6 class="footer-title">Quick Links</h6>
                    <a href="#home" class="footer-link">Home</a>
                    <a href="#about" class="footer-link">About Us</a>
                    <a href="#features" class="footer-link">Features</a>
                    <a href="products.php" class="footer-link">Products</a>
                </div>
                <div class="col-lg-2 footer-section">
                    <h6 class="footer-title">Services</h6>
                    <a href="products.php" class="footer-link">Computer Sales</a>
                    <a href="sales.php" class="footer-link">Sales Tracking</a>
                    <a href="contact.php" class="footer-link">Technical Support</a>
                    <a href="contact.php" class="footer-link">Consultation</a>
                </div>
                <div class="col-lg-2 footer-section">
                    <h6 class="footer-title">Support</h6>
                    <a href="contact.php" class="footer-link">Contact Us</a>
                    <a href="#" class="footer-link">Help Center</a>
                    <a href="#" class="footer-link">Warranty</a>
                    <a href="#" class="footer-link">Returns</a>
                </div>
                <div class="col-lg-2 footer-section">
                    <h6 class="footer-title">Account</h6>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="footer-link">My Profile</a>
                        <a href="mycart.php" class="footer-link">My Cart</a>
                        <a href="logout.php" class="footer-link">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="footer-link">Login</a>
                        <a href="register.php" class="footer-link">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <hr class="my-4" style="border-color: #374151;">
            <div class="text-center">
                <small>&copy; <?php echo date('Y'); ?> TechStore. All rights reserved. | Privacy Policy | Terms of Service</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.backgroundColor = '#1e40af';
            } else {
                navbar.style.backgroundColor = '#2563eb';
            }
        });
    </script>
</body>
</html> 