<?php
class Security {
    // Sanitize input
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    // Validate email
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Rate limiting
    public static function checkRateLimit($key, $max_requests = 5, $time_window = 300) {
        if (!isset($_SESSION['rate_limits'][$key])) {
            $_SESSION['rate_limits'][$key] = [
                'count' => 0,
                'first_request' => time()
            ];
        }

        $limit = &$_SESSION['rate_limits'][$key];
        
        if (time() - $limit['first_request'] > $time_window) {
            $limit['count'] = 1;
            $limit['first_request'] = time();
            return true;
        }

        if ($limit['count'] >= $max_requests) {
            return false;
        }

        $limit['count']++;
        return true;
    }

    // Password strength validation
    public static function validatePassword($password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($pattern, $password);
    }

    // Generate secure random token
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    // Validate file upload
    public static function validateFileUpload($file, $allowed_types = ['jpg', 'jpeg', 'png'], $max_size = 5242880) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return false;
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Check file type
        if (!in_array($file_extension, $allowed_types)) {
            return false;
        }

        // Check file size (default 5MB)
        if ($file['size'] > $max_size) {
            return false;
        }

        // Verify MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        $allowed_mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];

        if (!in_array($mime_type, $allowed_mimes)) {
            return false;
        }

        return true;
    }

    // Prevent XSS attacks
    public static function preventXSS() {
        foreach ($_GET as $key => $value) {
            $_GET[$key] = self::sanitize($value);
        }
        foreach ($_POST as $key => $value) {
            $_POST[$key] = self::sanitize($value);
        }
    }

    // Log security events
    public static function logSecurityEvent($event_type, $description, $ip_address = null) {
        if ($ip_address === null) {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        $log_file = __DIR__ . '/../logs/security.log';
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = sprintf(
            "[%s] %s: %s (IP: %s)\n",
            $timestamp,
            $event_type,
            $description,
            $ip_address
        );

        if (!is_dir(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }

        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }

    // Validate and sanitize URL
    public static function sanitizeURL($url) {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
    }

    // Check if request is AJAX
    public static function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
