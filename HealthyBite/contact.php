<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'includes/db.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $success_msg = "Thank you for contacting us! We will get back to you soon.";
        
        $to = $email;
        $mail_subject = "Nutrivo - Thank you for contacting us";
        $mail_message = "Hi $name,\n\nWe have received your message regarding '$subject'.\n\nOur team will review it and get back to you shortly.\n\nBest Regards,\nNutrivo Team";
        $headers = "From: noreply@nutrivo.example.com\r\n";
        $headers .= "Reply-To: healthybitesupport@gmail.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        @mail($to, $mail_subject, $mail_message, $headers);
    } else {
        $error_msg = "Something went wrong. Please try again later.";
    }
    $stmt->close();
}
?>

<?php include 'includes/header.php'; ?>

<div class="hero-bg-light reveal">
    <div class="container">
        <h1 class="hero-heading">Get in Touch</h1>
        <p class="hero-subtext">Have a question about our menu, delivery, or a recent order? We're here to help.</p>
    </div>
</div>

<section class="contact-section">
    <div class="contact-card reveal">
        
        <div class="contact-info">
            <h3>Contact Information</h3>
            <p>Fill out the form and our team will get back to you within 24 hours.</p>
            
            <div class="info-item">
                <div class="info-item-icon">📍</div>
                <div>Delhi Sector 52</div>
            </div>
            <div class="info-item">
                <div class="info-item-icon">📞</div>
                <div>+91 9546676989</div>
            </div>
            <div class="info-item">
                <div class="info-item-icon">✉️</div>
                <div>healthybitesupport@gmail.com</div>
            </div>
        </div>

        <div class="contact-form-wrapper">
            <h2>Send us a message</h2>
            <p class="contact-subtitle">We would love to hear from you.</p>
            
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger">
                    <strong>Error!</strong> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required rows="4"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-submit">Send Message</button>
            </form>
        </div>
        
    </div>
</section>

<?php include 'includes/footer.php'; ?>
