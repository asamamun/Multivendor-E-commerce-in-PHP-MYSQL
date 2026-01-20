
# System Settings Database Setup

To enable dynamic system settings, execute the following SQL to create the table and insert the initial configuration records.

## 1. Create Table Structure (if not exists)

```sql
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 2. Insert Default Records

```sql
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('company_name', 'MarketPlace', 'The name of the website/company'),
('site_logo', 'assets/uploads/settings/logo.png', 'Path to the main logo image'),
('site_favicon', 'assets/uploads/settings/favicon.ico', 'Path to the favicon'),
('company_address', '123 E-commerce St, Digital City, Dhaka-1200', 'Physical address of the company'),
('company_email', 'support@marketplace.com', 'Contact email address'),
('company_phone', '+880 1700 000000', 'Contact phone number'),
('vat_rate', '5.00', 'VAT percentage added to orders'),
('commission_rate', '10.00', 'Commission percentage taken from vendor sales'),
('currency_symbol', '৳', 'Currency symbol to display'),
('footer_text', '&copy; 2026 MarketPlace. All rights reserved.', 'Text to display in the footer'),
('social_facebook', 'https://facebook.com', 'Facebook page URL'),
('social_twitter', 'https://twitter.com', 'Twitter profile URL'),
('social_instagram', 'https://instagram.com', 'Instagram profile URL');
```

## 3. Usage in PHP

To use these settings in your application, you can fetch them into an associative array:

```php
// Fetch all settings
$settings_result = $conn->query("SELECT * FROM system_settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Usage examples:
// echo $settings['company_name'];
// echo $settings['vat_rate'];
```
