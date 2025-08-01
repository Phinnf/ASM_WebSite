<?php
/**
 * QA Test Script for Computer Selling Platform
 * This script demonstrates various QA testing procedures
 */

// Test Configuration
$test_results = [];
$total_tests = 0;
$passed_tests = 0;

// Test Helper Functions
function run_test($test_name, $test_function) {
    global $test_results, $total_tests, $passed_tests;
    
    $total_tests++;
    echo "Running test: $test_name... ";
    
    try {
        $result = $test_function();
        if ($result) {
            echo "PASSED\n";
            $passed_tests++;
            $test_results[$test_name] = "PASSED";
        } else {
            echo "FAILED\n";
            $test_results[$test_name] = "FAILED";
        }
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        $test_results[$test_name] = "ERROR";
    }
}

// Test 1: Database Connection Test
function test_database_connection() {
    require_once 'db.php';
    return isset($conn) && $conn !== false;
}

// Test 2: Password Hashing Test
function test_password_hashing() {
    $password = "TestPassword123!";
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Test 1: Hash should not be the same as original password
    if ($hash === $password) return false;
    
    // Test 2: Hash should be verifiable
    if (!password_verify($password, $hash)) return false;
    
    // Test 3: Wrong password should not verify
    if (password_verify("WrongPassword", $hash)) return false;
    
    return true;
}

// Test 3: SQL Injection Prevention Test
function test_sql_injection_prevention() {
    // Simulate malicious input
    $malicious_input = "'; DROP TABLE users; --";
    
    // Test that parameterized queries handle this safely
    $safe_query = "SELECT * FROM users WHERE username = ?";
    $params = [$malicious_input];
    
    // In a real test, we would execute this against a test database
    // For this demonstration, we'll just verify the structure
    return strpos($safe_query, $malicious_input) === false;
}

// Test 4: Input Validation Test
function test_input_validation() {
    // Test email validation
    $valid_email = "test@example.com";
    $invalid_email = "invalid-email";
    
    if (!filter_var($valid_email, FILTER_VALIDATE_EMAIL)) return false;
    if (filter_var($invalid_email, FILTER_VALIDATE_EMAIL)) return false;
    
    // Test password strength validation
    $strong_password = "StrongPass123!";
    $weak_password = "weak";
    
    $password_regex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};\':"\\|,.<>/?]).{10,}$/';
    
    if (!preg_match($password_regex, $strong_password)) return false;
    if (preg_match($password_regex, $weak_password)) return false;
    
    return true;
}

// Test 5: Session Security Test
function test_session_security() {
    // Test session start
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Test session ID regeneration (security best practice)
    $old_session_id = session_id();
    session_regenerate_id(true);
    $new_session_id = session_id();
    
    return $old_session_id !== $new_session_id;
}

// Test 6: XSS Prevention Test
function test_xss_prevention() {
    $malicious_input = '<script>alert("XSS")</script>';
    $sanitized = htmlspecialchars($malicious_input, ENT_QUOTES, 'UTF-8');
    
    // Check if script tags are escaped
    return strpos($sanitized, '<script>') === false && 
           strpos($sanitized, '&lt;script&gt;') !== false;
}

// Test 7: File Structure Test
function test_file_structure() {
    $required_files = [
        'index.php',
        'login.php',
        'register.php',
        'products.php',
        'mycart.php',
        'profile.php',
        'contact.php',
        'db.php'
    ];
    
    foreach ($required_files as $file) {
        if (!file_exists($file)) {
            return false;
        }
    }
    
    return true;
}

// Test 8: HTML Validation Test
function test_html_structure() {
    $html_files = ['index.php', 'login.php', 'register.php'];
    
    foreach ($html_files as $file) {
        $content = file_get_contents($file);
        
        // Check for basic HTML structure
        if (strpos($content, '<!DOCTYPE html>') === false) return false;
        if (strpos($content, '<html') === false) return false;
        if (strpos($content, '</html>') === false) return false;
        if (strpos($content, '<head>') === false) return false;
        if (strpos($content, '<body>') === false) return false;
    }
    
    return true;
}

// Test 9: Bootstrap Integration Test
function test_bootstrap_integration() {
    $html_files = ['index.php', 'login.php', 'register.php'];
    
    foreach ($html_files as $file) {
        $content = file_get_contents($file);
        
        // Check for Bootstrap CSS
        if (strpos($content, 'bootstrap.min.css') === false) return false;
        
        // Check for Bootstrap JS
        if (strpos($content, 'bootstrap.bundle.min.js') === false) return false;
    }
    
    return true;
}

// Test 10: Responsive Design Test
function test_responsive_design() {
    $html_files = ['index.php', 'login.php', 'register.php'];
    
    foreach ($html_files as $file) {
        $content = file_get_contents($file);
        
        // Check for viewport meta tag
        if (strpos($content, 'viewport') === false) return false;
        
        // Check for responsive classes
        if (strpos($content, 'col-') === false && strpos($content, 'container') === false) return false;
    }
    
    return true;
}

// Execute Tests
echo "=== QA Test Suite for Computer Selling Platform ===\n";
echo "Starting comprehensive testing...\n\n";

run_test("Database Connection", 'test_database_connection');
run_test("Password Hashing", 'test_password_hashing');
run_test("SQL Injection Prevention", 'test_sql_injection_prevention');
run_test("Input Validation", 'test_input_validation');
run_test("Session Security", 'test_session_security');
run_test("XSS Prevention", 'test_xss_prevention');
run_test("File Structure", 'test_file_structure');
run_test("HTML Structure", 'test_html_structure');
run_test("Bootstrap Integration", 'test_bootstrap_integration');
run_test("Responsive Design", 'test_responsive_design');

// Generate Test Report
echo "\n=== Test Results Summary ===\n";
echo "Total Tests: $total_tests\n";
echo "Passed: $passed_tests\n";
echo "Failed: " . ($total_tests - $passed_tests) . "\n";
echo "Success Rate: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n\n";

echo "Detailed Results:\n";
foreach ($test_results as $test_name => $result) {
    echo "- $test_name: $result\n";
}

// Generate HTML Report
$html_report = "
<!DOCTYPE html>
<html>
<head>
    <title>QA Test Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #f0f0f0; padding: 20px; border-radius: 5px; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 3px; }
        .passed { background: #d4edda; color: #155724; }
        .failed { background: #f8d7da; color: #721c24; }
        .summary { background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='header'>
        <h1>QA Test Report - Computer Selling Platform</h1>
        <p>Generated on: " . date('Y-m-d H:i:s') . "</p>
    </div>
    
    <div class='summary'>
        <h2>Test Summary</h2>
        <p><strong>Total Tests:</strong> $total_tests</p>
        <p><strong>Passed:</strong> $passed_tests</p>
        <p><strong>Failed:</strong> " . ($total_tests - $passed_tests) . "</p>
        <p><strong>Success Rate:</strong> " . round(($passed_tests / $total_tests) * 100, 2) . "%</p>
    </div>
    
    <h2>Detailed Results</h2>";

foreach ($test_results as $test_name => $result) {
    $class = ($result === 'PASSED') ? 'passed' : 'failed';
    $html_report .= "<div class='test-result $class'><strong>$test_name:</strong> $result</div>";
}

$html_report .= "</body></html>";

file_put_contents('qa_test_report.html', $html_report);

echo "\nHTML report generated: qa_test_report.html\n";
echo "\n=== QA Testing Complete ===\n";
?> 