<?php
namespace App\Modules\Appointment\Controllers;

use App\Core\BaseController;
use App\Modules\Appointment\Models\AppointmentModel;
use DateTime;

class AppointmentController extends BaseController {
    /**
     * @var AppointmentModel
     */
    private $appointmentModel;

    public function __construct() {
        $this->appointmentModel = new AppointmentModel();
    }

    private function requireAuth() {
        if (!isset($_SESSION["userId"])) {
            $this->redirect('/login');
        }
    }

    private function generateAppointmentId($receiverId, $senderId, $length = 20) {
        $characters = "00112233445566778899aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzzAABBCCDDEEFFGGHHIIJJKKLLMMNNOOPPQQRRSSTTUUVVWWXXYYZZ";
        $generateAppointmentId = "";
        for ($i = 0; $i < $length; $i++) {
            $generateAppointmentId .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $generatedId = trim($receiverId . $generateAppointmentId . $senderId);
        $generatedAppointmentId = "";
        for ($i = 0; $i < 60; $i++) {
            $generatedAppointmentId .= $generatedId[random_int(0, strlen($generatedId) - 1)];
        }

        return $generatedAppointmentId;
    }

    private function processAppointments($rawAppointments, $sort, $statusFilter, $searchQuery) {
        $enriched = [];
        foreach ($rawAppointments as $appt) {
            $fullName = strtoupper(trim(($appt["first_name"] ?? '') . " " . ($appt["last_name"] ?? '')));
            if (empty($fullName)) {
                $fullName = "UNKNOWN";
            }
            $enriched[] = [
                'appointment_id' => $appt['appointment_id'],
                'fullName' => $fullName,
                'first_name' => $appt['first_name'] ?? '',
                'last_name' => $appt['last_name'] ?? '',
                'profile_picture' => $appt['profile_picture'] ?? 'images/logo.png',
                'appointment_date' => $appt['appointment_date'],
                'appointment_time' => $appt['appointment_time'],
                'event_name' => $appt['event_name'],
                'location' => $appt['location'],
                'event_type' => $appt['event_type'] ?? 'consultation',
                'meeting_mode' => $appt['meeting_mode'] ?? 'google_meet',
                'agenda' => $appt['agenda'] ?? '',
                'appointment_status' => $appt['appointment_status']
            ];
        }

        $totalCount = count($enriched);
        $pendingCount = 0;
        $approvedCount = 0;
        $cancelledCount = 0;
        foreach ($enriched as $a) {
            $st = strtolower(trim($a['appointment_status'] ?? 'pending'));
            if ($st === 'approved') $approvedCount++;
            elseif ($st === 'cancel' || $st === 'denied') $cancelledCount++;
            else $pendingCount++;
        }

        $filtered = $enriched;
        if ($statusFilter !== 'all' && !empty($statusFilter)) {
            $filtered = array_values(array_filter($filtered, function($a) use ($statusFilter) {
                $st = strtolower(trim($a['appointment_status'] ?? 'pending'));
                if ($statusFilter === 'pending') return in_array($st, ['pending', '']);
                if ($statusFilter === 'approved') return $st === 'approved';
                if ($statusFilter === 'cancel') return in_array($st, ['cancel', 'denied']);
                return true;
            }));
        }

        if (!empty($searchQuery)) {
            $toLower = function($str) {
                $val = (string)($str ?? '');
                return function_exists('mb_strtolower') ? mb_strtolower($val) : strtolower($val);
            };
            $qLower = $toLower($searchQuery);
            $filtered = array_values(array_filter($filtered, function($a) use ($qLower, $toLower) {
                return str_contains($toLower($a['fullName'] ?? ''), $qLower)
                    || str_contains($toLower($a['event_name'] ?? ''), $qLower)
                    || str_contains($toLower($a['location'] ?? ''), $qLower)
                    || str_contains($toLower($a['agenda'] ?? ''), $qLower);
            }));
        }

        usort($filtered, function($a, $b) use ($sort) {
            return match ($sort) {
                'date_desc' => strcmp($b['appointment_date'], $a['appointment_date']) ?: strcmp($b['appointment_time'], $a['appointment_time']),
                'name_asc' => strcasecmp($a['fullName'], $b['fullName']),
                'name_desc' => strcasecmp($b['fullName'], $a['fullName']),
                'status_pending' => self::statusWeight($a['appointment_status'], 'pending') <=> self::statusWeight($b['appointment_status'], 'pending') ?: strcmp($a['appointment_date'], $b['appointment_date']),
                'status_approved' => self::statusWeight($a['appointment_status'], 'approved') <=> self::statusWeight($b['appointment_status'], 'approved') ?: strcmp($a['appointment_date'], $b['appointment_date']),
                default => strcmp($a['appointment_date'], $b['appointment_date']) ?: strcmp($a['appointment_time'], $b['appointment_time']),
            };
        });

        return [
            'filtered' => $filtered,
            'all' => $enriched,
            'total' => $totalCount,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'cancelled' => $cancelledCount
        ];
    }

    public function panel() {
        $this->requireAuth();
        $userId = $_SESSION["userId"];
        
        $currentUser = $this->appointmentModel->getUserDetails($userId) ?: [];
        $activeTab = strtolower(trim(scalarString($_GET['tab'] ?? 'received')));
        if (!in_array($activeTab, ['received', 'sent'], true)) {
            $activeTab = 'received';
        }

        $allowedSorts = ['date_asc', 'date_desc', 'name_asc', 'name_desc', 'status_pending', 'status_approved'];
        $sort = scalarString($_GET['sort'] ?? 'date_asc');
        if (!in_array($sort, $allowedSorts, true)) $sort = 'date_asc';
        $statusFilter = strtolower(trim(scalarString($_GET['status'] ?? 'all')));
        if (!in_array($statusFilter, ['all', 'pending', 'approved', 'cancel'], true)) $statusFilter = 'all';
        $searchQuery = trim(scalarString($_GET['q'] ?? ''));
        $searchQuery = function_exists('mb_substr') ? mb_substr($searchQuery, 0, 100) : substr($searchQuery, 0, 100);

        $rawReceived = $this->appointmentModel->getReceivedAppointments($userId);
        $rawSent = $this->appointmentModel->getSentAppointments($userId);

        $processedReceived = $this->processAppointments($rawReceived, $sort, $statusFilter, $searchQuery);
        $processedSent = $this->processAppointments($rawSent, $sort, $statusFilter, $searchQuery);

        $this->renderView('Appointment/Views/panel', [
            'currentUser' => $currentUser,
            'activeTab' => $activeTab,

            // Received Appointments
            'appointments' => $processedReceived['filtered'],
            'allAppointments' => $processedReceived['all'],
            'totalCount' => $processedReceived['total'],
            'pendingCount' => $processedReceived['pending'],
            'approvedCount' => $processedReceived['approved'],
            'cancelledCount' => $processedReceived['cancelled'],

            // Sent Appointments
            'sentAppointments' => $processedSent['filtered'],
            'allSentAppointments' => $processedSent['all'],
            'sentTotalCount' => $processedSent['total'],
            'sentPendingCount' => $processedSent['pending'],
            'sentApprovedCount' => $processedSent['approved'],
            'sentCancelledCount' => $processedSent['cancelled'],

            // Filter/Sort State
            'currentSort' => $sort,
            'currentStatus' => $statusFilter,
            'currentQuery' => $searchQuery
        ]);
    }

    public function sent() {
        $_GET['tab'] = 'sent';
        return $this->panel();
    }

    public function markAllNotificationsRead() {
        $this->requireAuth();
        $this->validateCsrf();
        if (isset($_SESSION['userId'])) {
            $notifs = $this->appointmentModel->getUserNotifications($_SESSION['userId']);
            $_SESSION['read_notifications'] = array_map(fn($n) => $n['id'], $notifs);
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
        exit;
    }

    public function markNotificationRead() {
        $this->requireAuth();
        $this->validateCsrf();
        $id = scalarString($_POST['id'] ?? '');
        if ($id && isset($_SESSION['userId'])) {
            if ($this->appointmentModel->getAppointmentForUser($id, $_SESSION['userId'])) {
                if (!isset($_SESSION['read_notifications']) || !is_array($_SESSION['read_notifications'])) {
                    $_SESSION['read_notifications'] = [];
                }
                if (!in_array($id, $_SESSION['read_notifications'], true)) {
                    $_SESSION['read_notifications'][] = $id;
                }
                $_SESSION['read_notifications'] = array_slice($_SESSION['read_notifications'], -500);
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
        exit;
    }

    private static function statusWeight($status, $priority = 'pending') {
        $st = strtolower(trim((string)$status));
        if ($priority === 'pending') {
            return match ($st) {
                'pending', '' => 1,
                'approved' => 2,
                'denied' => 3,
                'cancel' => 4,
                default => 5,
            };
        }
        return match ($st) {
            'approved' => 1,
            'pending', '' => 2,
            'denied' => 3,
            'cancel' => 4,
            default => 5,
        };
    }

    public function bookForm($params = []) {
        $id = scalarString($params['account'] ?? $params['id'] ?? $_GET['account'] ?? '');

        if (empty($id)) {
            $this->setFlash('danger', 'Missing account identifier.');
            $this->redirect(isset($_SESSION['userId']) ? '/search' : '/login');
        }

        $isOwner = (isset($_SESSION["userId"]) && $id === $_SESSION["userId"]);

        $account = $this->appointmentModel->getUserDetails($id);
        if (!$account) {
            $this->setFlash('danger', 'Account not found.');
            $this->redirect(isset($_SESSION['userId']) ? '/search' : '/login');
        }

        $initialDate = date('Y-m-d');
        $initialSlots = $this->appointmentModel->generateAvailableSlots($id, $initialDate, 30);

        $this->renderView('Appointment/Views/book', [
            'id' => $id,
            'firstName' => $account['first_name'],
            'middleName' => $account['middle_name'],
            'lastName' => $account['last_name'],
            'contactNumber' => '',
            'emailAddress' => '',
            'profilePicture' => $account['profile_picture'],
            'workStartTime' => $account['work_start_time'] ?? '09:00',
            'workEndTime' => $account['work_end_time'] ?? '17:00',
            'slotDuration' => $account['slot_duration_minutes'] ?? 30,
            'headline' => $account['headline'] ?? '',
            'bio' => $account['bio'] ?? '',
            'jobTitle' => $account['job_title'] ?? '',
            'company' => $account['company'] ?? '',
            'locationCity' => $account['location_city'] ?? '',
            'skills' => $account['skills'] ?? '',
            'consultationTopics' => $account['consultation_topics'] ?? '',
            'linkedinUrl' => $account['linkedin_url'] ?? '',
            'githubUrl' => $account['github_url'] ?? '',
            'websiteUrl' => $account['website_url'] ?? '',
            'initialDate' => $initialDate,
            'initialSlots' => $initialSlots,
            'isAuthenticated' => isset($_SESSION['userId']),
            'isOwner' => $isOwner
        ]);
    }

    public function apiSlots($params = []) {
        $receiverId = trim(scalarString($_GET['receiverId'] ?? $params['receiverId'] ?? ''));
        $date = trim(scalarString($_GET['date'] ?? $params['date'] ?? date('Y-m-d')));

        if (empty($receiverId)) {
            return $this->jsonResponse(['error' => 'Receiver ID is required'], 400);
        }

        // Validate date format YYYY-MM-DD
        $parsedDate = \DateTime::createFromFormat('!Y-m-d', $date);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
            return $this->jsonResponse(['error' => 'Invalid date format (expected YYYY-MM-DD)'], 400);
        }
        $maxDate = (new \DateTimeImmutable('today'))->modify('+1 year')->format('Y-m-d');
        if ($date < date('Y-m-d') || $date > $maxDate) {
            return $this->jsonResponse(['error' => 'Date must be within the next year.'], 400);
        }

        $durationInput = $_GET['duration'] ?? null;
        if ($durationInput !== null && !is_scalar($durationInput)) {
            return $this->jsonResponse(['error' => 'Unsupported appointment duration'], 400);
        }
        $duration = $durationInput !== null ? filter_var((string)$durationInput, FILTER_VALIDATE_INT) : null;
        if ($duration !== null && !in_array($duration, [15, 30, 60], true)) {
            return $this->jsonResponse(['error' => 'Unsupported appointment duration'], 400);
        }
        $slots = $this->appointmentModel->generateAvailableSlots($receiverId, $date, $duration);
        return $this->jsonResponse([
            'receiverId' => $receiverId,
            'date' => $date,
            'duration' => $duration,
            'slots' => $slots
        ]);
    }

    public function book() {
        $this->requireAuth();
        
        if (isset($_POST["submit"])) {
            $this->validateCsrf();
            $receiverId = trim(scalarString($_POST['receiverId'] ?? ''));
            $senderId = $_SESSION["userId"] ?? "";
            $appointmentDate = trim(scalarString($_POST['appointmentDate'] ?? ''));
            $appointmentTime = trim(scalarString($_POST['appointmentTime'] ?? ''));
            $eventName = trim(scalarString($_POST['eventName'] ?? ''));
            $location = trim(scalarString($_POST['location'] ?? ''));
            $eventType = trim(scalarString($_POST['eventType'] ?? 'consultation'));
            $meetingMode = trim(scalarString($_POST['meetingMode'] ?? 'google_meet'));
            $agenda = trim(scalarString($_POST['agenda'] ?? ''));

            $allowedEventTypes = ["quick_sync", "consultation", "deep_dive"];
            if (!in_array($eventType, $allowedEventTypes, true)) {
                $eventType = "consultation";
            }

            $allowedMeetingModes = ["google_meet", "zoom", "in_person", "phone"];
            if (!in_array($meetingMode, $allowedMeetingModes, true)) {
                $meetingMode = "google_meet";
            }

            if (empty($receiverId) || empty($senderId) || empty($appointmentDate) || empty($appointmentTime) || empty($eventName) || empty($location)) {
                $this->setFlash('danger', 'All fields and an open time slot are required to schedule an appointment.');
                $this->redirect('/appointment/book?account=' . rawurlencode($receiverId));
            }

            $parsedDate = DateTime::createFromFormat('!Y-m-d', $appointmentDate);
            $dateErrors = DateTime::getLastErrors();
            $validTime = preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $appointmentTime) === 1;
            if (!$parsedDate || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
                || $parsedDate->format('Y-m-d') !== $appointmentDate || !$validTime
                || strlen($eventName) > 100 || strlen($location) > 100 || strlen($agenda) > 5000) {
                $this->setFlash('danger', 'Please check the appointment date, time, and field lengths.');
                $this->redirect('/appointment/book?account=' . rawurlencode($receiverId));
            }

            if ($receiverId === $senderId) {
                $this->setFlash('warning', 'You cannot book an appointment with yourself.');
                $this->redirect('/search');
            }

            if ($appointmentDate < date('Y-m-d')) {
                $this->setFlash('danger', 'Cannot schedule appointments in the past.');
                $this->redirect('/appointment/book?account=' . rawurlencode($receiverId));
            }
            if ($appointmentDate > (new \DateTimeImmutable('today'))->modify('+1 year')->format('Y-m-d')) {
                $this->setFlash('danger', 'Appointments can only be scheduled within the next year.');
                $this->redirect('/appointment/book?account=' . rawurlencode($receiverId));
            }

            // Conflict detection
            $requestedSlot = null;
            foreach ($this->appointmentModel->generateAvailableSlots($receiverId, $appointmentDate, match ($eventType) {
                'quick_sync' => 15,
                'deep_dive' => 60,
                default => 30,
            }) as $slot) {
                if ($slot['time'] === $appointmentTime && $slot['available']) {
                    $requestedSlot = $slot;
                    break;
                }
            }
            if ($requestedSlot === null || $this->appointmentModel->hasConflict($receiverId, $appointmentDate, $appointmentTime, $eventType)) {
                $this->setFlash('danger', 'The selected time slot (' . $appointmentTime . ') is already booked. Please choose an open slot.');
                $this->redirect('/appointment/book?account=' . rawurlencode($receiverId));
            }

            $appointmentId = $this->generateAppointmentId($receiverId, $senderId);
            $appointmentStatus = "pending";

            $data = [
                'appointment_id' => $appointmentId,
                'receiver_id' => $receiverId,
                'sender_id' => $senderId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'event_name' => $eventName,
                'location' => $location,
                'event_type' => $eventType,
                'meeting_mode' => $meetingMode,
                'agenda' => $agenda,
                'appointment_status' => $appointmentStatus
            ];

            if ($this->appointmentModel->createAppointment($data)) {
                $this->setFlash('success', 'Appointment scheduled successfully! You can track it here.');
                $this->redirect('/sent');
            } else {
                $this->setFlash('danger', 'An error occurred while creating the appointment. Please try again.');
                $this->redirect('/appointment/book?account=' . rawurlencode($receiverId));
            }
        } else {
            $this->redirect('/search');
        }
    }

    public function view($params = []) {
        $this->requireAuth();
        $id = scalarString($params['id'] ?? $_GET['id'] ?? '');
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/received');
        }

        // Auto-mark notification for this appointment as read
        if (!isset($_SESSION['read_notifications']) || !is_array($_SESSION['read_notifications'])) {
            $_SESSION['read_notifications'] = [];
        }
        if (!in_array($id, $_SESSION['read_notifications'], true)) {
            $_SESSION['read_notifications'][] = $id;
        }
        $_SESSION['read_notifications'] = array_slice($_SESSION['read_notifications'], -500);

        $isSender = ($appt['sender_id'] === $userId);
        $displayName = $isSender 
            ? strtoupper(($appt['receiver_first_name'] ?? '') . ' ' . ($appt['receiver_last_name'] ?? ''))
            : strtoupper(($appt['sender_first_name'] ?? '') . ' ' . ($appt['sender_last_name'] ?? ''));
        $displayContact = $isSender ? ($appt['receiver_contact'] ?? '') : ($appt['sender_contact'] ?? '');
        $displayEmail = $isSender ? ($appt['receiver_email'] ?? '') : ($appt['sender_email'] ?? '');

        // Determine meeting duration
        $durationMinutes = 30;
        if (($appt['event_type'] ?? '') === 'quick_sync') $durationMinutes = 15;
        elseif (($appt['event_type'] ?? '') === 'deep_dive') $durationMinutes = 60;

        // Build Google Calendar TEMPLATE URL
        $partnerName = $displayName;
        $title = $appt['event_name'] ?: 'Appointment with ' . $partnerName;
        $desc = "More Than Blue Appointment with " . $partnerName;
        if (!empty($appt['agenda'])) {
            $desc .= "\n\nAgenda:\n" . $appt['agenda'];
        }
        $loc = $appt['location'] ?: 'Online';

        $startTimeStr = $appt['appointment_time'] ?: '09:00';
        $startDT = new \DateTime($appt['appointment_date'] . ' ' . $startTimeStr);
        $endDT = clone $startDT;
        $endDT->modify("+{$durationMinutes} minutes");

        $datesParam = $startDT->format('Ymd\THis') . '/' . $endDT->format('Ymd\THis');
        $googleCalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE"
            . "&text=" . urlencode($title)
            . "&dates=" . $datesParam
            . "&ctz=" . urlencode(date_default_timezone_get())
            . "&details=" . urlencode($desc)
            . "&location=" . urlencode($loc);

        $this->renderView('Appointment/Views/view', [
            'appointmentId' => $appt['appointment_id'],
            'fullName' => $displayName,
            'contactNumber' => !empty($displayContact) ? "+63" . $displayContact : "",
            'emailAddress' => $displayEmail,
            'appointment_date' => $appt['appointment_date'],
            'appointment_time' => $appt['appointment_time'],
            'event_name' => $appt['event_name'],
            'location' => $appt['location'],
            'event_type' => $appt['event_type'] ?? 'consultation',
            'meeting_mode' => $appt['meeting_mode'] ?? 'google_meet',
            'agenda' => $appt['agenda'] ?? '',
            'duration_minutes' => $durationMinutes,
            'googleCalUrl' => $googleCalUrl,
            'appointment_status' => $appt['appointment_status']
        ]);
    }

    public function exportIcs($params = []) {
        $this->requireAuth();
        $id = scalarString($params['id'] ?? $_GET['id'] ?? '');
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/received');
        }

        // Auto-mark notification for this appointment as read
        if (!isset($_SESSION['read_notifications']) || !is_array($_SESSION['read_notifications'])) {
            $_SESSION['read_notifications'] = [];
        }
        if (!in_array($id, $_SESSION['read_notifications'], true)) {
            $_SESSION['read_notifications'][] = $id;
        }
        $_SESSION['read_notifications'] = array_slice($_SESSION['read_notifications'], -500);

        $isSender = ($appt['sender_id'] === $userId);
        $partnerName = $isSender 
            ? trim(($appt['receiver_first_name'] ?? '') . ' ' . ($appt['receiver_last_name'] ?? ''))
            : trim(($appt['sender_first_name'] ?? '') . ' ' . ($appt['sender_last_name'] ?? ''));

        $title = $appt['event_name'] ?: 'Appointment with ' . $partnerName;
        $description = "More Than Blue Appointment with " . $partnerName;
        if (!empty($appt['agenda'])) {
            $description .= "\n\nAgenda: " . $appt['agenda'];
        }
        $location = $appt['location'] ?: 'Online';

        $durationMinutes = 30;
        if (($appt['event_type'] ?? '') === 'quick_sync') $durationMinutes = 15;
        elseif (($appt['event_type'] ?? '') === 'deep_dive') $durationMinutes = 60;

        $startTimeStr = $appt['appointment_time'] ?: '09:00';
        $startDT = new \DateTime($appt['appointment_date'] . ' ' . $startTimeStr);
        $endDT = clone $startDT;
        $endDT->modify("+{$durationMinutes} minutes");

        $utc = new \DateTimeZone('UTC');
        $dtStart = (clone $startDT)->setTimezone($utc)->format('Ymd\THis\Z');
        $dtEnd = (clone $endDT)->setTimezone($utc)->format('Ymd\THis\Z');
        $dtStamp = gmdate('Ymd\THis\Z');
        $uid = $appt['appointment_id'] . '@morethanblue.local';
        $escapeIcsText = static function ($value) {
            return str_replace(
                ["\\", "\r\n", "\r", "\n", ",", ";"],
                ["\\\\", "\\n", "\\n", "\\n", "\\,", "\\;"],
                (string)$value
            );
        };

        $foldIcsLine = static function (string $line): string {
            $parts = [];
            while (strlen($line) > 75) {
                $cut = 75;
                while ($cut > 0 && (ord($line[$cut]) & 0xC0) === 0x80) $cut--;
                $parts[] = substr($line, 0, $cut);
                $line = ' ' . substr($line, $cut);
            }
            $parts[] = $line;
            return implode("\r\n", $parts);
        };
        $status = match (strtolower(trim($appt['appointment_status'] ?? 'pending'))) {
            'approved' => 'CONFIRMED',
            'pending' => 'TENTATIVE',
            default => 'CANCELLED',
        };
        $icsLines = [
            'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//More Than Blue//Appointment Scheduler//EN',
            'CALSCALE:GREGORIAN', 'METHOD:PUBLISH', 'BEGIN:VEVENT',
            'UID:' . $uid, 'DTSTAMP:' . $dtStamp, 'DTSTART:' . $dtStart, 'DTEND:' . $dtEnd,
            'SUMMARY:' . $escapeIcsText($title), 'DESCRIPTION:' . $escapeIcsText($description),
            'LOCATION:' . $escapeIcsText($location), 'STATUS:' . $status,
            'END:VEVENT', 'END:VCALENDAR'
        ];
        $ics = implode("\r\n", array_map($foldIcsLine, $icsLines)) . "\r\n";

        header('Content-Type: text/calendar; charset=utf-8');
        $downloadId = preg_replace('/[^A-Za-z0-9_-]/', '', substr((string)$appt['appointment_id'], 0, 8));
        header('Content-Disposition: attachment; filename="appointment-' . $downloadId . '.ics"');
        header('Content-Length: ' . strlen($ics));
        echo $ics;
        exit;
    }

    public function edit($params = []) {
        $this->requireAuth();
        $id = scalarString($params['id'] ?? '');
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/received');
        }

        // Auto-mark notification for this appointment as read
        if (!isset($_SESSION['read_notifications']) || !is_array($_SESSION['read_notifications'])) {
            $_SESSION['read_notifications'] = [];
        }
        if (!in_array($id, $_SESSION['read_notifications'], true)) {
            $_SESSION['read_notifications'][] = $id;
        }
        $_SESSION['read_notifications'] = array_slice($_SESSION['read_notifications'], -500);

        $this->renderView('Appointment/Views/edit', [
            'appointmentId' => $id,
            'appointment' => $appt,
            'isReceiver' => ($appt['receiver_id'] === $userId)
        ]);
    }

    public function updateStatus() {
        $this->requireAuth();
        $this->validateCsrf();
        $userId = $_SESSION["userId"];

        $id = trim(scalarString($_POST['id'] ?? ''));
        $status = trim(scalarString($_POST['status'] ?? ''));

        $allowedStatuses = ["approved", "denied", "cancel"];
        if (empty($id) || !in_array($status, $allowedStatuses, true)) {
            $this->redirect('/received');
        }

        if ($this->appointmentModel->updateAppointmentStatusForUser($id, $status, $userId)) {
            $this->setFlash('success', 'Appointment status updated to ' . ucfirst($status) . '.');
            $this->redirect('/received');
        } else {
            $this->setFlash('danger', 'Unable to update appointment status.');
            $this->redirect('/received');
        }
    }

    public function deleteForm($params = []) {
        $this->requireAuth();
        $id = scalarString($params['id'] ?? '');
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt || $appt['receiver_id'] !== $userId) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/received');
        }

        $this->renderView('Appointment/Views/delete', ['appointmentId' => $id]);
    }

    public function deleteConfirm() {
        $this->requireAuth();
        $this->validateCsrf();
        $userId = $_SESSION["userId"];

        $id = trim(scalarString($_POST['id'] ?? ''));

        if (empty($id)) {
            $this->redirect('/received');
        }

        if ($this->appointmentModel->deleteReceivedAppointment($id, $userId)) {
            $this->setFlash('success', 'Received appointment was successfully deleted.');
            $this->redirect('/received');
        } else {
            $this->setFlash('danger', 'Unable to delete appointment.');
            $this->redirect('/received');
        }
    }

    public function deleteSentForm($params = []) {
        $this->requireAuth();
        $id = scalarString($params['id'] ?? '');
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/sent');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt || $appt['sender_id'] !== $userId) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/sent');
        }

        $this->renderView('Appointment/Views/delete-sent', ['appointmentId' => $id]);
    }

    public function deleteSentConfirm() {
        $this->requireAuth();
        $this->validateCsrf();
        $userId = $_SESSION["userId"];

        $id = trim(scalarString($_POST['id'] ?? ''));

        if (empty($id)) {
            $this->redirect('/sent');
        }

        if ($this->appointmentModel->deleteSentAppointment($id, $userId)) {
            $this->setFlash('success', 'Sent appointment was successfully deleted.');
            $this->redirect('/sent');
        } else {
            $this->setFlash('danger', 'Unable to delete appointment.');
            $this->redirect('/sent');
        }
    }
}
