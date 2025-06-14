<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/lib/assets/Config.php';
require_once __DIR__ . '/lib/assets/DB.php';

class RegistrationTest {
    private $db;
    private $testCases = [];
    private $results = [];
    private $testEmails = [];

    public function __construct() {
        try {
            $this->db = \app\assets\DB::getInstance();
            $this->setupTestCases();
            $this->cleanupTestData();
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private function cleanupTestData() {
        // Clean up any test data from previous runs
        foreach ($this->testEmails as $email) {
            try {
                $this->db->prepare("DELETE FROM users WHERE email = ?", 's', $email);
            } catch (Exception $e) {
                error_log("Cleanup error for email $email: " . $e->getMessage());
            }
        }
    }

    private function setupTestCases() {
        // Test Case 1: Valid registration
        $this->testCases[] = [
            'name' => 'John Doe',
            'phone' => '1234567890',
            'email' => 'john@example.com',
            'user_type' => 'customer',
            'password' => 'password123',
            'confirm_password' => 'password123',
            'description' => 'Valid registration data'
        ];
        $this->testEmails[] = 'john@example.com';

        // Test Case 2: Duplicate email
        $this->testCases[] = [
            'name' => 'Jane Doe',
            'phone' => '0987654321',
            'email' => 'john@example.com', // Same email as test case 1
            'user_type' => 'vendor',
            'password' => 'password456',
            'confirm_password' => 'password456',
            'description' => 'Duplicate email registration'
        ];

        // Test Case 3: Invalid email format
        $this->testCases[] = [
            'name' => 'Invalid User',
            'phone' => '5555555555',
            'email' => 'invalid-email',
            'user_type' => 'customer',
            'password' => 'password789',
            'confirm_password' => 'password789',
            'description' => 'Invalid email format'
        ];

        // Test Case 4: Password mismatch
        $this->testCases[] = [
            'name' => 'Mismatch User',
            'phone' => '1111111111',
            'email' => 'mismatch@example.com',
            'user_type' => 'vendor',
            'password' => 'password123',
            'confirm_password' => 'different123',
            'description' => 'Password mismatch'
        ];
        $this->testEmails[] = 'mismatch@example.com';

        // Test Case 5: SQL Injection attempt
        $this->testCases[] = [
            'name' => "SQL' Injection",
            'phone' => "123' OR '1'='1",
            'email' => "test@example.com' OR '1'='1",
            'user_type' => "vendor' OR '1'='1",
            'password' => "password123' OR '1'='1",
            'confirm_password' => "password123' OR '1'='1",
            'description' => 'SQL Injection attempt'
        ];
        $this->testEmails[] = "test@example.com' OR '1'='1";

        // Test Case 6: Empty fields
        $this->testCases[] = [
            'name' => '',
            'phone' => '',
            'email' => '',
            'user_type' => '',
            'password' => '',
            'confirm_password' => '',
            'description' => 'Empty fields validation'
        ];

        // Test Case 7: Special characters in name
        $this->testCases[] = [
            'name' => 'John Doe!@#$%^&*()',
            'phone' => '1234567890',
            'email' => 'special@example.com',
            'user_type' => 'customer',
            'password' => 'password123',
            'confirm_password' => 'password123',
            'description' => 'Special characters in name'
        ];
        $this->testEmails[] = 'special@example.com';
    }

    public function runTests() {
        echo "<h1>Registration Test Results</h1>";
        echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;'>";
        
        $totalTests = count($this->testCases);
        $passedTests = 0;
        
        foreach ($this->testCases as $index => $testCase) {
            echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
            echo "<h2 style='color: #333;'>Test Case " . ($index + 1) . ": " . htmlspecialchars($testCase['description']) . "</h2>";
            
            try {
                // Validate inputs
                $errors = $this->validateRegistrationData($testCase);
                
                if (!empty($errors)) {
                    echo "<div style='color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 4px;'>";
                    echo "<strong>Validation Errors:</strong><br>";
                    foreach ($errors as $error) {
                        echo "- " . htmlspecialchars($error) . "<br>";
                    }
                    echo "</div>";
                    continue;
                }

                // Check for duplicate email using prepared statement
                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?", 's', $testCase['email']);
                if ($stmt && $stmt->num_rows > 0) {
                    echo "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px;'>";
                    echo "Email already registered";
                    echo "</div>";
                    continue;
                }

                // Hash password
                $hashedPassword = password_hash($testCase['password'], PASSWORD_DEFAULT);

                // Insert user with prepared statement
                $sql = "INSERT INTO users (name, phone, email, user_type, password) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql, 'sssss', 
                    $testCase['name'],
                    $testCase['phone'],
                    $testCase['email'],
                    $testCase['user_type'],
                    $hashedPassword
                );

                if ($stmt) {
                    echo "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 4px;'>";
                    echo "Registration successful!";
                    echo "</div>";
                    $passedTests++;
                } else {
                    echo "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px;'>";
                    echo "Registration failed";
                    echo "</div>";
                }

            } catch (Exception $e) {
                echo "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px;'>";
                echo "Error: " . htmlspecialchars($e->getMessage());
                echo "</div>";
            }
            
            echo "</div>";
        }

        // Display test summary
        echo "<div style='margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;'>";
        echo "<h3>Test Summary</h3>";
        echo "<p>Total Tests: $totalTests</p>";
        echo "<p>Passed Tests: $passedTests</p>";
        echo "<p>Failed Tests: " . ($totalTests - $passedTests) . "</p>";
        echo "</div>";

        echo "</div>";
    }

    private function validateRegistrationData($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = "Name is required";
        }
        if (empty($data['phone'])) {
            $errors[] = "Phone number is required";
        }
        if (empty($data['email'])) {
            $errors[] = "Email is required";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        if (empty($data['user_type'])) {
            $errors[] = "User type is required";
        }
        if (empty($data['password'])) {
            $errors[] = "Password is required";
        }
        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = "Passwords do not match";
        }

        return $errors;
    }

    public function __destruct() {
        // Clean up test data after all tests are complete
        $this->cleanupTestData();
    }
}

// Run the tests
$test = new RegistrationTest();
$test->runTests();
?>
