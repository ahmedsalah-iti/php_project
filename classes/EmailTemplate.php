<?php

class EmailTemplate {
    private $receiverName;
    private $subject;
    private $mainMessage;
    private $ctaText;
    private $ctaUrl;

    public function __construct($receiverName, $subject, $mainMessage, $ctaText, $ctaUrl) {
        $this->receiverName = $receiverName;
        $this->subject = $subject;
        $this->mainMessage = $mainMessage;
        $this->ctaText = $ctaText;
        $this->ctaUrl = $ctaUrl;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function render() {
        return "
        <div style='background-color: #f4f4f4; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
                <div style='background-color: #A5D6A7; padding: 20px; text-align: center;'>
                    <img src='https://cafeteria.iti.cam/logo.png' alt='Cafeteria Logo' style='max-width: 200px;'>
                </div>
                <div style='padding: 20px;'>
                    <h2 style='color: #2E7D32; margin-top: 0;'>Hello {$this->receiverName}, Welcome to Cafeteria!</h2>
                    {$this->mainMessage}
                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='{$this->ctaUrl}' style='background-color: #4CAF50; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>{$this->ctaText}</a>
                    </div>
                </div>
                <div style='background-color: #f4f4f4; padding: 10px; text-align: center; font-size: 12px; color: #666666;'>
                    <p>Questions? Contact us at <a href='mailto:support@cafeteria.com' style='color: #4CAF50;'>support@cafeteria.com</a></p>
                    <p><a href='https://cafeteria.iti.cam/unsubscribe' style='color: #4CAF50;'>Unsubscribe</a></p>
                </div>
            </div>
        </div>";
    }

    public static function welcome($receiverName) {
        return new self(
            $receiverName,
            "Welcome to Cafeteria - Your Fast Food & Drinks Hub",
            "<p>Hi {$receiverName},</p><p>Thank you for joining Cafeteria! We're excited to bring you fast, delicious drinks and food right at your fingertips. Explore our menu and let us know if you need any help.</p>",
            "Check Out Our Menu",
            "https://cafeteria.iti.cam/"
        );
    }

    public static function purchaseThanks($receiverName) {
        return new self(
            $receiverName,
            "Thank You for Your Order at Cafeteria",
            "<p>Hi {$receiverName},</p><p>Your order has been confirmed! We'll get your food and drinks ready soon. Check your order status below.</p>",
            "Track Your Order",
            "https://cafeteria.iti.cam/track-order"
        );
    }

    public static function passwordReset($receiverName, $resetToken = null) {
        $resetToken = $resetToken ?? bin2hex(random_bytes(8));
        return new self(
            $receiverName,
            "Reset Your Cafeteria Password",
            "<p>Hi {$receiverName},</p><p>We received a request to reset your password. Use the link below to set a new one.</p>",
            "Reset Your Password",
            "https://cafeteria.iti.cam/reset-password?token=$resetToken"
        );
    }

    public static function orderConfirmation($receiverName) {
        return new self(
            $receiverName,
            "Your Cafeteria Order Confirmation",
            "<p>Hi {$receiverName},</p><p>Your order is confirmed! Here are the details: [Insert details here]. We'll notify you when it's on the way.</p>",
            "View Order Details",
            "https://cafeteria.iti.cam/order-details"
        );
    }
}