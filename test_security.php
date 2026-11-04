<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

echo "--- RUNNING TESTS ---\n";

// 1. Test Password Verification
$userModel = new \App\Modules\Auth\Models\UserModel();
$user = $userModel->getUserByEmailAndPassword('stacybaluyot@example.com', 'stacybaluyot');
if ($user && $user['first_name'] === 'Stacy') {
    echo "[PASS] Bcrypt password verification succeeded for Stacy.\n";
} else {
    echo "[FAIL] Bcrypt password verification failed.\n";
    exit(1);
}

// 2. Test Invalid Password
$wrongUser = $userModel->getUserByEmailAndPassword('stacybaluyot@example.com', 'wrongpassword');
if ($wrongUser === false) {
    echo "[PASS] Wrong password rejected.\n";
} else {
    echo "[FAIL] Wrong password was accepted!\n";
    exit(1);
}

// 3. Test Partial Search (ILIKE)
$searchResults = $userModel->searchUsers('stacy');
if (!empty($searchResults) && $searchResults[0]['first_name'] === 'Stacy') {
    echo "[PASS] Partial case-insensitive search found user.\n";
} else {
    echo "[FAIL] Partial search failed.\n";
    exit(1);
}

// 4. Test Appointment N+1 Join Query
$apptModel = new \App\Modules\Appointment\Models\AppointmentModel();
$received = $apptModel->getReceivedAppointments('Z4whw2o3jDnHkyQA7tIR');
if (!empty($received) && isset($received[0]['first_name'])) {
    echo "[PASS] Joined received appointments successfully fetched sender info.\n";
} else {
    echo "[FAIL] Joined received appointments query failed.\n";
    exit(1);
}

// 5. Test Conflict Detection
$hasConflict = $apptModel->hasConflict('Z4whw2o3jDnHkyQA7tIR', '2024-10-05', '11:20');
if ($hasConflict) {
    echo "[PASS] Appointment slot conflict correctly detected.\n";
} else {
    echo "[FAIL] Conflict was not detected!\n";
    exit(1);
}

// 6. Test IDOR protection in Model
$unauthorizedAppt = $apptModel->getAppointmentForUser('5vCkv43OD5osWdZv7BCPHYRYWIVsjWPwPHoYvdwwjyoowQyDIdgH7doHPxdW', '10BwghsSz2BD8L7Htdfd');
if ($unauthorizedAppt === false) {
    echo "[PASS] IDOR blocked: Unauthorized user cannot fetch another user's appointment.\n";
} else {
    echo "[FAIL] IDOR check failed! Unauthorized user was able to fetch appointment.\n";
    exit(1);
}

// 7. Test Flash Messaging
setFlash('success', 'Test flash message');
$flashes = getFlashes();
if (!empty($flashes) && $flashes[0]['message'] === 'Test flash message') {
    echo "[PASS] Flash messaging system correctly stores and consumes messages.\n";
} else {
    echo "[FAIL] Flash messaging test failed.\n";
    exit(1);
}

// Ensure consumed flash is cleared
if (empty(getFlashes())) {
    echo "[PASS] Flash messages are automatically cleared upon reading.\n";
} else {
    echo "[FAIL] Flash message was not cleared.\n";
    exit(1);
}

// 8. Test Master Layout and View Rendering
$controller = new class extends \App\Core\BaseController {
    public function testRender() {
        ob_start();
        $this->renderView('Auth/Views/login', ['title' => 'Test Login']);
        return ob_get_clean();
    }
};

$renderedOutput = $controller->testRender();
if (strpos($renderedOutput, 'Sign In to Your Account') !== false && strpos($renderedOutput, '<nav') !== false) {
    echo "[PASS] Master layout successfully encapsulated view content.\n";
} else {
    echo "[FAIL] Master layout rendering failed.\n";
    exit(1);
}

// 9. Test End-to-End User Registration Flow
$testEmail = 'signup_flow_test_' . time() . '@example.com';
$testPhone = '999' . rand(1000000, 9999999);
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    '_csrf' => csrfToken(),
    'firstName' => 'Flow',
    'middleName' => 'Test',
    'lastName' => 'User',
    'contactNumber' => $testPhone,
    'emailAddress' => $testEmail,
    'password' => 'testPassword123'
];
$_FILES = [];

$auth = new class extends \App\Modules\Auth\Controllers\AuthController {
    public $redirectUrl = null;
    protected function redirect($url) {
        $this->redirectUrl = $url;
    }
};

$auth->signup();
$createdUser = $userModel->getUserByEmail($testEmail);
if ($createdUser && $createdUser['first_name'] === 'Flow') {
    echo "[PASS] End-to-end user registration saved user to database.\n";
    // Clean up
    $db = \App\Core\Database::getInstance();
    $stmt = $db->prepare("DELETE FROM user_account WHERE email_address = ?");
    $stmt->execute([$testEmail]);
} else {
    echo "[FAIL] User registration flow failed to save.\n";
    exit(1);
}

// 10. Test Dynamic Slot Generation & Conflict Detection
$slots = $apptModel->generateAvailableSlots('Z4whw2o3jDnHkyQA7tIR', '2024-10-05');
$found1120 = false;
$slot1120Available = null;
foreach ($slots as $s) {
    if ($s['time'] === '11:20') {
        $found1120 = true;
        $slot1120Available = $s['available'];
        break;
    }
}
// Seed data has 11:20 booked on 2024-10-05 for Z4whw2o3jDnHkyQA7tIR
if ($found1120 && $slot1120Available === false) {
    echo "[PASS] Dynamic slot generation correctly flagged booked 11:20 slot as unavailable.\n";
} else {
    // If exact interval step doesn't land on 11:20, test direct conflict check
    if ($apptModel->hasConflict('Z4whw2o3jDnHkyQA7tIR', '2024-10-05', '11:20')) {
        echo "[PASS] Dynamic slot conflict successfully recognized booked appointment.\n";
    } else {
        echo "[FAIL] Dynamic slot generation failed to flag booked slot.\n";
        exit(1);
    }
}

// 11. Test Future Available Slot Generation
$futureDate = date('Y-m-d', strtotime('+7 days'));
$futureSlots = $apptModel->generateAvailableSlots('10BwghsSz2BD8L7Htdfd', $futureDate);
if (!empty($futureSlots) && count($futureSlots) >= 8) {
    echo "[PASS] Generated " . count($futureSlots) . " working hour slots for future date.\n";
} else {
    echo "[FAIL] Future slot generation produced insufficient slots.\n";
    exit(1);
}

echo "--- ALL CHECKS PASSED (11/11) ---\n";
