<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Som Restaurant - Authentic Cuisine</title>
    <link rel="stylesheet" href="assets/css/landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">
            <img src="assets/website images/freepik-cool-shiny-catering-logo-20251227115416r8zy.png" alt="Som Restaurant" style="height: 60px;">
        </a>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#menu">Menu</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
            <a href="login.php" class="btn-login">Login</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Taste the Spirit of <br><span>Somali Cuisine</span></h1>
            <p>Experience the rich flavors and traditions of authentic Somali food, prepared with passion and served with love.</p>
            <a href="login.php" class="cta-btn">Order Now <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="hero-image">
            <img src="assets/Homepage images/1.png" alt="Delicious Somali Plate">
        </div>
    </section>

    <!-- Popular Dishes Section -->
    <section class="featured" id="menu">
        <h2 class="section-title"><span>Popular Dishes</span></h2>
        
        <div class="food-grid">
            <!-- Item 1: Baasto -->
            <div class="food-card">
                <div class="food-img-container">
                    <img src="assets/Homepage images/freepik-somali-baasto-20251227141250aCae.png" alt="Somali Baasto">
                </div>
                <div class="food-info">
                    <h3>Classic Baasto</h3>
                    <p>Delicious Somali spaghetti served with seasoned meat sauce and fresh vegetables.</p>
                </div>
            </div>

            <!-- Item 2: Bariis -->
            <div class="food-card">
                <div class="food-img-container">
                    <img src="assets/Homepage images/freepik-somali-bariis-20251227140805aE0K.png" alt="Somali Bariis">
                </div>
                <div class="food-info">
                    <h3>Aromatic Bariis</h3>
                    <p>Fragrant spiced rice with tender meat, a staple of every Somali feast.</p>
                </div>
            </div>

            <!-- Item 3: Soor -->
            <div class="food-card">
                <div class="food-img-container">
                    <img src="assets/Homepage images/freepik-somali-soor-20251227141041V6M9.png" alt="Somali Soor">
                </div>
                <div class="food-info">
                    <h3>Traditional Soor</h3>
                    <p>Authentic soft cornmeal served with fresh milk or spinach stew.</p>
                </div>
            </div>

             <!-- Item 4: Special Baasto -->
             <div class="food-card">
                <div class="food-img-container">
                    <img src="assets/Homepage images/freepik-somali-baasto-20251227142405VFDY.png" alt="Special Baasto">
                </div>
                <div class="food-info">
                    <h3>Chef's Special Baasto</h3>
                    <p>Our signature pasta dish with a secret blend of spices and premium toppings.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About/Showcase Section -->
    <section class="about" id="about">
        <div class="about-img">
            <img src="assets/Homepage images/0.png" alt="Restaurant Ambiance">
        </div>
        <div class="about-text">
            <h2 class="section-title" style="text-align: left; margin-bottom: 20px;"><span>Our Story</span></h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #4A5568;">
                Welcome to <strong>Som Restaurant</strong>, where tradition meets taste. Since our opening, we have been dedicated to bringing you the most authentic Somali culinary experience.
            </p>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #4A5568;">
                Our chefs use only the freshest ingredients and traditional recipes passed down through generations to ensure every bite takes you home.
            </p>
            <br>
            <a href="login.php" class="cta-btn">Visit Us Today</a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <h2>Som Restaurant</h2>
        <p>123 Mogadishu Ave, Somalia | +252 61 123 4567</p>
        <p>&copy; 2025 Som Restaurant. All rights reserved.</p>
    </footer>

</body>
</html>
