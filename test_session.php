<?php
// Test Laravel's file session path
$sessionPath = __DIR__ . '/storage/framework/sessions';
$testFile = $sessionPath . '/laravel_test_' . time() . '.sess';

// Write
file_put_contents($testFile, serialize(['token' => 'abc123', 'user' => 'test']));

// Read back
$data = unserialize(file_get_contents($testFile));

echo "Write+Read: " . ($data['token'] === 'abc123' ? 'PASSED' : 'FAILED') . PHP_EOL;
echo "Session path writable: YES" . PHP_EOL;

// Clean up
unlink($testFile);

// Check php.ini session settings
echo "PHP session.save_handler: " . ini_get('session.save_handler') . PHP_EOL;
echo "PHP session.save_path: " . ini_get('session.save_path') . PHP_EOL;
echo "PHP loaded ini: " . php_ini_loaded_file() . PHP_EOL;
