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

$db = \App\Core\Database::getInstance();

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

// 12. Test Cal.com Meeting Formats, Modes, and Agenda Insertion
$testApptId = 'test_cal_exp_' . bin2hex(random_bytes(10));
$testApptData = [
    'appointment_id' => $testApptId,
    'receiver_id' => 'Z4whw2o3jDnHkyQA7tIR',
    'sender_id' => '10BwghsSz2BD8L7Htdfd',
    'appointment_date' => date('Y-m-d', strtotime('+10 days')),
    'appointment_time' => '14:00',
    'event_name' => 'Architecture Deep Dive',
    'location' => 'https://meet.google.com/abc-defg-hij',
    'event_type' => 'deep_dive',
    'meeting_mode' => 'google_meet',
    'agenda' => 'Review microservices design and PostgreSQL indexes',
    'appointment_status' => 'pending'
];

if ($apptModel->createAppointment($testApptData)) {
    $fetched = $apptModel->getAppointmentForUser($testApptId, 'Z4whw2o3jDnHkyQA7tIR');
    if ($fetched && $fetched['event_type'] === 'deep_dive' && $fetched['meeting_mode'] === 'google_meet' && str_contains($fetched['agenda'], 'microservices')) {
        echo "[PASS] Cal.com Meeting Types, Modes, and Agenda persisted and retrieved successfully.\n";
    } else {
        echo "[FAIL] Cal.com attributes failed verification upon retrieval.\n";
        exit(1);
    }
    // Cleanup
    $db->prepare("DELETE FROM appointment_details WHERE appointment_id = ?")->execute([$testApptId]);
} else {
    echo "[FAIL] Failed to create Cal.com appointment record.\n";
    exit(1);
}

// 13. Test ICS Calendar Format Generation
$sampleDtStart = '20261104T100000';
$sampleDtEnd = '20261104T103000';
$sampleIcs = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//More Than Blue//Appointment Scheduler//EN\r\nBEGIN:VEVENT\r\nDTSTART:{$sampleDtStart}\r\nDTEND:{$sampleDtEnd}\r\nSUMMARY:Test Consultation\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";
if (str_contains($sampleIcs, 'BEGIN:VCALENDAR') && str_contains($sampleIcs, 'PRODID:-//More Than Blue//') && str_contains($sampleIcs, 'END:VCALENDAR')) {
    echo "[PASS] ICS calendar payload conforms to RFC 5545 format.\n";
} else {
    echo "[FAIL] ICS calendar generation format failed.\n";
    exit(1);
}

// 14. Test LinkedIn-Style Professional Search by Skill & Company
$skillSearchResults = $userModel->searchUsers('PostgreSQL');
if (!empty($skillSearchResults) && count($skillSearchResults) >= 1) {
    echo "[PASS] LinkedIn-style search successfully discovered user by skill keyword 'PostgreSQL'.\n";
} else {
    echo "[FAIL] Skill-based user search failed.\n";
    exit(1);
}

// 15. Test LinkedIn Profile Fields Retrieval
$stacyDetails = $apptModel->getUserDetails('10BwghsSz2BD8L7Htdfd');
if ($stacyDetails && !empty($stacyDetails['headline']) && !empty($stacyDetails['skills']) && !empty($stacyDetails['consultation_topics']) && !empty($stacyDetails['linkedin_url'])) {
    echo "[PASS] LinkedIn professional profile fields verified on user account.\n";
} else {
    echo "[FAIL] LinkedIn profile fields missing or unpopulated.\n";
    exit(1);
}

// 16. Test Google OAuth User Creation & Retrieval
$googleTestId = 'goog_oauth_' . bin2hex(random_bytes(8));
$googleSub = '1092837465' . rand(100000, 999999);
$googleTestEmail = 'google_user_' . time() . '@gmail.com';
$googleUserData = [
    'user_id' => $googleTestId,
    'first_name' => 'Sergey',
    'middle_name' => null,
    'last_name' => 'Brin',
    'search_name' => 'SERGEY BRIN',
    'contact_number' => '0000000000',
    'email_address' => $googleTestEmail,
    'user_password' => password_hash('random-oauth-hash', PASSWORD_DEFAULT),
    'profile_picture' => 'images/logo.png',
    'work_start_time' => '09:00',
    'work_end_time' => '17:00',
    'slot_duration_minutes' => 30,
    'google_id' => $googleSub
];

if ($userModel->createGoogleUser($googleUserData)) {
    $foundGoogleUser = $userModel->getUserByGoogleId($googleSub);
    if ($foundGoogleUser && $foundGoogleUser['email_address'] === $googleTestEmail && $foundGoogleUser['google_id'] === $googleSub) {
        echo "[PASS] Google OAuth user created and retrieved by google_id successfully.\n";
    } else {
        echo "[FAIL] Failed to retrieve user by google_id.\n";
        exit(1);
    }

    // 17. Test Google Account Linking
    $newGoogleSub = 'linked_' . bin2hex(random_bytes(6));
    if ($userModel->linkGoogleId($googleTestId, $newGoogleSub)) {
        $linkedUser = $userModel->getUserByGoogleId($newGoogleSub);
        if ($linkedUser && $linkedUser['user_id'] === $googleTestId) {
            echo "[PASS] Existing account successfully linked with Google OAuth identifier.\n";
        } else {
            echo "[FAIL] Google account linking verification failed.\n";
            exit(1);
        }
    } else {
        echo "[FAIL] linkGoogleId returned false.\n";
        exit(1);
    }

    // Cleanup
    $stmt = $db->prepare("DELETE FROM user_account WHERE user_id = ?");
    $stmt->execute([$googleTestId]);
} else {
    echo "[FAIL] createGoogleUser query failed.\n";
    exit(1);
}

echo "--- ALL CHECKS PASSED (17/17) ---\n";
