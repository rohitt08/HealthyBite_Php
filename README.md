# HealthyBite

HealthyBite is a dynamic food delivery and restaurant web application built with PHP and MySQL. It offers a seamless experience for users to browse a healthy food menu, add items to their cart, securely log in or register, and place orders. 

## Features

- **Dynamic Menu:** Browse various food categories and items. Menu data is stored in the database and displayed dynamically.
- **User Authentication:** Secure user registration and login system.
- **Shopping Cart:** Add, update, or remove items from the cart. Cart state is managed through the database using session IDs.
- **Order Management:** Place orders and view order history with calculated totals and delivery fees.
- **Contact Form:** Allow users to send inquiries that are saved directly to the database.
- **Automated Setup:** The application handles its own database and table creation upon the first run!

## Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL

## Prerequisites

To run this project locally, you will need a local server environment capable of running PHP and MySQL. Some popular options include:
- **XAMPP** (Windows, macOS, Linux)
- **WAMP** (Windows)
- **MAMP** (macOS, Windows)

## Installation and Setup

1. **Clone the Repository**
   Clone this project into your local server's document root directory (e.g., `C:\xampp\htdocs` for XAMPP or `C:\wamp\www` for WAMP).
   ```bash
   git clone https://github.com/rohitt08/HealthyBite_Php.git
   ```

2. **Start the Server**
   Open your XAMPP/WAMP/MAMP control panel and start both the **Apache** and **MySQL** services.

3. **Run the Application**
   Open your web browser and navigate to the project directory:
   ```text
   http://localhost/HealthyBite_Php/HealthyBite
   ```
   *(Adjust the URL based on the exact folder name you cloned the repository into).*

4. **Automated Database Setup**
   You **do not** need to manually import any `.sql` files! 
   Upon accessing the website for the first time, the `includes/db.php` script will automatically:
   - Create a new MySQL database named `healthybite`.
   - Create all necessary tables (`users`, `menu_items`, `cart`, `orders`, `order_items`, `contacts`).
   - Seed the `menu_items` table with initial data from `includes/data.php`.

## Project Structure

- `HealthyBite/` - Main application directory
  - `assets/` & `css/` & `js/` - Frontend assets (images, stylesheets, scripts)
  - `includes/` - Backend utility scripts like `db.php` (database connection & setup), `header.php`, and `data.php`
  - `index.php` - Homepage
  - `menu.php` - Food menu page
  - `cart.php` & `cart_action.php` - Cart display and logic
  - `login.php` & `logout.php` - User authentication
  - `place_order.php` & `orders.php` - Order processing and history
  - `contact.php` - Contact form page
