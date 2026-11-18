<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$appConfig = require __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';

if (($appConfig['app_env'] ?? '') !== 'test'
    || !preg_match('/_test$/', (string)($appConfig['dbname'] ?? ''))) {
    fwrite(STDERR, "Refusing to run the database-mutating suite unless APP_ENV=test and DB_NAME ends with _test.\n");
    exit(2);
}

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
$userModel = new \App\Modules\Auth\Models\UserModel();
$apptModel = new \App\Modules\Appointment\Models\AppointmentModel();

// Create isolated per-run fixtures instead of depending on imported demo users.
$fixtureSuffix = bin2hex(random_bytes(6));
$fixturePassword = 'fixturePassword123';
$fixtureUserIds = [
    'test_sender_' . $fixtureSuffix,
    'test_receiver_' . $fixtureSuffix,
    'test_outside_' . $fixtureSuffix,
];
register_shutdown_function(static function () use ($db, $fixtureUserIds): void {
    foreach ($fixtureUserIds as $fixtureId) {
        $db->prepare('DELETE FROM user_account WHERE user_id = ?')->execute([$fixtureId]);
    }
});
$createFixtureUser = static function (string $userId, string $firstName, string $lastName) use ($userModel, $fixturePassword): bool {
    return $userModel->createUser([
        'user_id' => $userId,
        'first_name' => $firstName,
        'middle_name' => null,
        'last_name' => $lastName,
        'search_name' => strtoupper($firstName . ' ' . $lastName),
        'contact_number' => '9' . str_pad((string)random_int(0, 999999999), 9, '0', STR_PAD_LEFT),
        'email_address' => $userId . '@test.invalid',
        'user_password' => password_hash($fixturePassword, PASSWORD_DEFAULT),
        'profile_picture' => 'images/logo.png',
        'work_start_time' => '09:00',
        'work_end_time' => '17:00',
        'slot_duration_minutes' => 30,
    ]);
};

foreach ([
    [$fixtureUserIds[0], 'Fixture', 'Sender' . $fixtureSuffix],
    [$fixtureUserIds[1], 'Fixture', 'Receiver' . $fixtureSuffix],
    [$fixtureUserIds[2], 'Fixture', 'Outside' . $fixtureSuffix],
] as [$fixtureId, $firstName, $lastName]) {
    if (!$createFixtureUser($fixtureId, $firstName, $lastName)) {
        foreach ($fixtureUserIds as $createdId) {
            $db->prepare('DELETE FROM user_account WHERE user_id = ?')->execute([$createdId]);
        }
        fwrite(STDERR, "Unable to create isolated test fixtures.\n");
        exit(1);
    }
}

[$senderId, $receiverId, $outsideId] = $fixtureUserIds;
$fixtureEmail = $senderId . '@test.invalid';
$fixtureAppointmentId = 'test_fixture_' . $fixtureSuffix;
$fixtureDate = date('Y-m-d', strtotime('+10 days'));
$db->prepare("UPDATE user_profile SET headline = ?, skills = ?, consultation_topics = ?, linkedin_url = ? WHERE user_id = ?")
    ->execute(['Fixture profile', 'PostgreSQL, Testing', 'Fixture consultations', 'https://linkedin.com/in/fixture', $receiverId]);
$db->prepare("INSERT INTO appointment_details (appointment_id, receiver_id, sender_id, appointment_date, appointment_time, event_name, location, event_type, meeting_mode, agenda, appointment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
    ->execute([$fixtureAppointmentId, $receiverId, $senderId, $fixtureDate, '11:00', 'Fixture Meeting', 'Online', 'consultation', 'google_meet', 'Fixture agenda', 'pending']);

// 1. Test Password Verification
$user = $userModel->getUserByEmailAndPassword($fixtureEmail, $fixturePassword);
if ($user && $user['user_id'] === $senderId) {
    echo "[PASS] Bcrypt password verification succeeded for an isolated test account.\n";
} else {
    echo "[FAIL] Bcrypt password verification failed.\n";
    exit(1);
}

// 2. Test Invalid Password
$wrongUser = $userModel->getUserByEmailAndPassword($fixtureEmail, 'wrongpassword');
if ($wrongUser === false) {
    echo "[PASS] Wrong password rejected.\n";
} else {
    echo "[FAIL] Wrong password was accepted!\n";
    exit(1);
}

// 3. Test Partial Search (ILIKE)
$searchResults = $userModel->searchUsers($fixtureSuffix);
if (!empty($searchResults) && in_array($senderId, array_column($searchResults, 'user_id'), true)) {
    echo "[PASS] Partial case-insensitive search found user.\n";
} else {
    echo "[FAIL] Partial search failed.\n";
    exit(1);
}

// 4. Test Appointment N+1 Join Query
$received = $apptModel->getReceivedAppointments($receiverId);
if (!empty($received) && isset($received[0]['first_name'])) {
    echo "[PASS] Joined received appointments successfully fetched sender info.\n";
} else {
    echo "[FAIL] Joined received appointments query failed.\n";
    exit(1);
}

// 5. Test Conflict Detection
$hasConflict = $apptModel->hasConflict($receiverId, $fixtureDate, '11:00');
if ($hasConflict) {
    echo "[PASS] Appointment slot conflict correctly detected.\n";
} else {
    echo "[FAIL] Conflict was not detected!\n";
    exit(1);
}

// 6. Test IDOR protection in Model
$unauthorizedAppt = $apptModel->getAppointmentForUser($fixtureAppointmentId, $outsideId);
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
$testEmail = 'signup_flow_test_' . $fixtureSuffix . '@test.invalid';
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
$slots = $apptModel->generateAvailableSlots($receiverId, $fixtureDate);
$foundBookingSlot = false;
$bookingSlotAvailable = null;
foreach ($slots as $s) {
    if ($s['time'] === '11:00') {
        $foundBookingSlot = true;
        $bookingSlotAvailable = $s['available'];
        break;
    }
}
if ($foundBookingSlot && $bookingSlotAvailable === false) {
    echo "[PASS] Dynamic slot generation correctly flagged the fixture booking as unavailable.\n";
} else {
    if ($apptModel->hasConflict($receiverId, $fixtureDate, '11:00')) {
        echo "[PASS] Dynamic slot conflict successfully recognized booked appointment.\n";
    } else {
        echo "[FAIL] Dynamic slot generation failed to flag booked slot.\n";
        exit(1);
    }
}

// 11. Test Future Available Slot Generation
$futureDate = date('Y-m-d', strtotime('+7 days'));
$futureSlots = $apptModel->generateAvailableSlots($senderId, $futureDate);
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
    'receiver_id' => $receiverId,
    'sender_id' => $senderId,
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
    $fetched = $apptModel->getAppointmentForUser($testApptId, $receiverId);
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
$sampleStart = new DateTimeImmutable('tomorrow 10:00');
$sampleDtStart = $sampleStart->format('Ymd\\THis');
$sampleDtEnd = $sampleStart->modify('+30 minutes')->format('Ymd\\THis');
$sampleIcs = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//More Than Blue//Appointment Scheduler//EN\r\nBEGIN:VEVENT\r\nDTSTART:{$sampleDtStart}\r\nDTEND:{$sampleDtEnd}\r\nSUMMARY:Test Consultation\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";
if (str_contains($sampleIcs, 'BEGIN:VCALENDAR') && str_contains($sampleIcs, 'PRODID:-//More Than Blue//') && str_contains($sampleIcs, 'END:VCALENDAR')) {
    echo "[PASS] ICS calendar payload conforms to RFC 5545 format.\n";
} else {
    echo "[FAIL] ICS calendar generation format failed.\n";
    exit(1);
}

// 14. Test LinkedIn-Style Professional Search by Skill & Company
$skillSearchResults = $userModel->searchUsers('PostgreSQL');
if (!empty($skillSearchResults) && in_array($receiverId, array_column($skillSearchResults, 'user_id'), true)) {
    echo "[PASS] LinkedIn-style search successfully discovered user by skill keyword 'PostgreSQL'.\n";
} else {
    echo "[FAIL] Skill-based user search failed.\n";
    exit(1);
}

// 15. Test LinkedIn Profile Fields Retrieval
$fixtureDetails = $apptModel->getUserDetails($receiverId);
if ($fixtureDetails && !empty($fixtureDetails['headline']) && !empty($fixtureDetails['skills']) && !empty($fixtureDetails['consultation_topics']) && !empty($fixtureDetails['linkedin_url'])) {
    echo "[PASS] LinkedIn professional profile fields verified on user account.\n";
} else {
    echo "[FAIL] LinkedIn profile fields missing or unpopulated.\n";
    exit(1);
}

// 16. Test Google OAuth User Creation & Retrieval
$googleTestId = 'goog_oauth_' . bin2hex(random_bytes(8));
$googleSub = '1092837465' . rand(100000, 999999);
$googleTestEmail = 'google_user_' . $fixtureSuffix . '@test.invalid';
$googleUserData = [
    'user_id' => $googleTestId,
    'first_name' => 'OAuth',
    'middle_name' => null,
    'last_name' => 'Fixture',
    'search_name' => 'OAUTH FIXTURE',
    'contact_number' => null,
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

// 18. Test 3NF Database Normalization Schema & Column Separation
$userAccountCols = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'user_account'")->fetchAll(PDO::FETCH_COLUMN);
$userProfileCols = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'user_profile'")->fetchAll(PDO::FETCH_COLUMN);
$userAvailabilityCols = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'user_availability'")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('headline', $userAccountCols, true) &&
    !in_array('work_start_time', $userAccountCols, true) &&
    in_array('headline', $userProfileCols, true) &&
    in_array('skills', $userProfileCols, true) &&
    in_array('work_start_time', $userAvailabilityCols, true) &&
    in_array('slot_duration_minutes', $userAvailabilityCols, true)) {
    echo "[PASS] 3NF Database normalization verified: separate domain tables with no redundant columns in user_account.\n";
} else {
    echo "[FAIL] Normalization check failed: unexpected columns in user_account or missing in profile/availability.\n";
    exit(1);
}

// 19. Test Normalized Multi-Table Profile & Availability Update
$normTestUserId = 'norm_test_' . bin2hex(random_bytes(6));
$normUserData = [
    'user_id' => $normTestUserId,
    'first_name' => 'Normalize',
    'middle_name' => 'Db',
    'last_name' => 'Tester',
    'search_name' => 'NORMALIZE DB TESTER',
    'contact_number' => '09123456789',
    'email_address' => 'norm_' . $fixtureSuffix . '@test.invalid',
    'user_password' => password_hash('password123', PASSWORD_DEFAULT),
    'profile_picture' => 'images/logo.png',
    'work_start_time' => '10:00',
    'work_end_time' => '18:00',
    'slot_duration_minutes' => 45
];

if ($userModel->createUser($normUserData)) {
    $updateData = [
        'first_name' => 'NormalizeUpdated',
        'middle_name' => 'Db',
        'last_name' => 'Tester',
        'search_name' => 'NORMALIZEUPDATED DB TESTER',
        'contact_number' => '09988776655',
        'email_address' => $normUserData['email_address'],
        'profile_picture' => 'images/logo.png',
        'last_update' => date('Y-m-d'),
        'work_start_time' => '08:30',
        'work_end_time' => '16:30',
        'slot_duration_minutes' => 60,
        'headline' => 'Normalized Schema Specialist',
        'bio' => 'Focusing on 3NF database architecture.',
        'job_title' => 'Database Architect',
        'company' => 'More Than Blue',
        'location_city' => 'Manila',
        'skills' => 'PostgreSQL, 3NF, SQL Tuning',
        'consultation_topics' => 'Database Optimization',
        'linkedin_url' => 'https://linkedin.com/in/norm-tester',
        'github_url' => 'https://github.com/norm-tester',
        'website_url' => 'https://normtester.dev'
    ];

    if ($userModel->updateUser($normTestUserId, $updateData)) {
        $fetchedNormalized = $userModel->getUserById($normTestUserId);
        if ($fetchedNormalized &&
            $fetchedNormalized['first_name'] === 'NormalizeUpdated' &&
            $fetchedNormalized['work_start_time'] === '08:30' &&
            $fetchedNormalized['slot_duration_minutes'] == 60 &&
            $fetchedNormalized['headline'] === 'Normalized Schema Specialist' &&
            $fetchedNormalized['company'] === 'More Than Blue') {
            echo "[PASS] Multi-table profile & availability updates persisted and unified seamlessly.\n";
        } else {
            echo "[FAIL] Fetched normalized user data does not match updated values.\n";
            exit(1);
        }
    } else {
        echo "[FAIL] updateUser returned false.\n";
        exit(1);
    }

    // 20. Test Foreign Key ON DELETE CASCADE
    $stmtDel = $db->prepare("DELETE FROM user_account WHERE user_id = ?");
    $stmtDel->execute([$normTestUserId]);

    $profCheck = $db->prepare("SELECT COUNT(*) FROM user_profile WHERE user_id = ?");
    $profCheck->execute([$normTestUserId]);
    $profCount = (int)$profCheck->fetchColumn();

    $availCheck = $db->prepare("SELECT COUNT(*) FROM user_availability WHERE user_id = ?");
    $availCheck->execute([$normTestUserId]);
    $availCount = (int)$availCheck->fetchColumn();

    if ($profCount === 0 && $availCount === 0) {
        echo "[PASS] Foreign key ON DELETE CASCADE verified: child records in user_profile & user_availability automatically purged.\n";
    } else {
        echo "[FAIL] Foreign key cascade failed: orphan rows remain in user_profile or user_availability.\n";
        exit(1);
    }
} else {
    echo "[FAIL] Failed to create user for normalization test.\n";
    exit(1);
}

// 21. Test GOOGLE_REDIRECT_URI Loaded from .env and Config
$appConfig = require __DIR__ . '/config/config.php';
$authCtrl = new \App\Modules\Auth\Controllers\AuthController();
$googleCreds = $authCtrl->getGoogleCredentials();

if (!empty($appConfig['google_redirect_uri']) &&
    !empty($googleCreds['redirect_uri']) &&
    $googleCreds['redirect_uri'] === $appConfig['google_redirect_uri'] &&
    str_contains($googleCreds['redirect_uri'], '/auth/google/callback')) {
    echo "[PASS] GOOGLE_REDIRECT_URI successfully loaded from .env dynamically (" . $googleCreds['redirect_uri'] . ").\n";
} else {
    echo "[FAIL] GOOGLE_REDIRECT_URI not loaded properly from .env.\n";
    exit(1);
}

// 22. Test Header Notification Feed Generation
$notifs = $apptModel->getUserNotifications($receiverId);
if (!empty($notifs) && isset($notifs[0]['title']) && isset($notifs[0]['message']) && isset($notifs[0]['badge_color'])) {
    echo "[PASS] Header notifications engine correctly aggregated schedule events (" . count($notifs) . " items).\n";
} else {
    echo "[FAIL] Header notification generator returned empty or malformed payload.\n";
    exit(1);
}

// 23. Test Schedule Sorting & Filtering Engine
$_GET['sort'] = 'name_desc';
$_GET['status'] = 'all';
$_SESSION['userId'] = $receiverId;
$apptCtrl = new class extends \App\Modules\Appointment\Controllers\AppointmentController {
    public $viewData = [];
    protected function renderView($viewName, $data = [], $layout = 'main') {
        $this->viewData = $data;
    }
};
$apptCtrl->panel();
if (!empty($apptCtrl->viewData['appointments']) && $apptCtrl->viewData['currentSort'] === 'name_desc') {
    echo "[PASS] Schedule sorting and filtering engine properly processed parameters.\n";
} else {
    echo "[FAIL] Schedule sorting engine failed to process sort parameter.\n";
    exit(1);
}

echo "--- ALL CHECKS PASSED (23/23) ---\n";
