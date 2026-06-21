<?php
$conn = oci_connect('system', '123', 'localhost/XE');
if (!$conn) { $e = oci_error(); die("Connect failed: " . $e['message']); }

// Check rows in USERS
$stmt = oci_parse($conn, "SELECT user_id, name, email, role FROM USERS");
oci_execute($stmt);
echo "Users in DB:\n";
while ($row = oci_fetch_assoc($stmt)) {
    echo "  [{$row['USER_ID']}] {$row['NAME']} | {$row['EMAIL']} | {$row['ROLE']}\n";
}

// Test password_hash retrieval
$stmt2 = oci_parse($conn, "SELECT password_hash FROM USERS WHERE email = 'admin@uni.edu'");
oci_execute($stmt2);
$r = oci_fetch_assoc($stmt2);
if ($r) {
    $hash = $r['PASSWORD_HASH'];
    echo "\nAdmin hash: $hash\n";
    echo "Verify 'password': " . (password_verify('password', $hash) ? 'OK' : 'FAIL') . "\n";
} else {
    echo "Admin user not found!\n";
}

oci_close($conn);
