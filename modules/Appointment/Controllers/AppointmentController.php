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

    public function panel() {
        $this->requireAuth();
        $userId = $_SESSION["userId"];
        
        $appointments = $this->appointmentModel->getReceivedAppointments($userId);
        
        $enrichedAppointments = [];
        foreach ($appointments as $appt) {
            $fullName = strtoupper(trim(($appt["first_name"] ?? '') . " " . ($appt["last_name"] ?? '')));
            if (empty($fullName)) {
                $fullName = "UNKNOWN";
            }
            
            $enrichedAppointments[] = [
                'appointment_id' => $appt['appointment_id'],
                'fullName' => $fullName,
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

        $this->renderView('Appointment/Views/panel', ['appointments' => $enrichedAppointments]);
    }

    public function sent() {
        $this->requireAuth();
        $userId = $_SESSION["userId"];
        
        $appointments = $this->appointmentModel->getSentAppointments($userId);
        
        $enrichedAppointments = [];
        foreach ($appointments as $appt) {
            $fullName = strtoupper(trim(($appt["first_name"] ?? '') . " " . ($appt["last_name"] ?? '')));
            if (empty($fullName)) {
                $fullName = "UNKNOWN";
            }
            
            $enrichedAppointments[] = [
                'appointment_id' => $appt['appointment_id'],
                'fullName' => $fullName,
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

        $this->renderView('Appointment/Views/sent', ['appointments' => $enrichedAppointments]);
    }

    public function bookForm($params = []) {
        $id = $params["account"] ?? $params["id"] ?? $_GET["account"] ?? "";

        if (empty($id)) {
            $this->setFlash('danger', 'Missing account identifier.');
            $this->redirect(isset($_SESSION['userId']) ? '/search' : '/login');
        }

        if (isset($_SESSION["userId"]) && $id === $_SESSION["userId"]) {
            $this->setFlash('warning', 'You cannot book an appointment with yourself.');
            $this->redirect('/search');
        }

        $account = $this->appointmentModel->getUserDetails($id);
        if (!$account) {
            $this->setFlash('danger', 'Account not found.');
            $this->redirect(isset($_SESSION['userId']) ? '/search' : '/login');
        }

        $initialDate = date('Y-m-d');
        $initialSlots = $this->appointmentModel->generateAvailableSlots($id, $initialDate);

        $this->renderView('Appointment/Views/book', [
            'id' => $id,
            'firstName' => $account['first_name'],
            'middleName' => $account['middle_name'],
            'lastName' => $account['last_name'],
            'contactNumber' => $account['contact_number'],
            'emailAddress' => $account['email_address'],
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
            'isAuthenticated' => isset($_SESSION['userId'])
        ]);
    }

    public function apiSlots($params = []) {
        $receiverId = trim($_GET["receiverId"] ?? $params["receiverId"] ?? "");
        $date = trim($_GET["date"] ?? $params["date"] ?? date('Y-m-d'));

        if (empty($receiverId)) {
            return $this->jsonResponse(['error' => 'Receiver ID is required'], 400);
        }

        // Validate date format YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->jsonResponse(['error' => 'Invalid date format (expected YYYY-MM-DD)'], 400);
        }

        $duration = isset($_GET["duration"]) && (int)$_GET["duration"] > 0 ? (int)$_GET["duration"] : null;
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
            $receiverId = trim($_POST["receiverId"] ?? "");
            $senderId = $_SESSION["userId"] ?? "";
            $appointmentDate = trim($_POST["appointmentDate"] ?? "");
            $appointmentTime = trim($_POST["appointmentTime"] ?? "");
            $eventName = trim($_POST["eventName"] ?? "");
            $location = trim($_POST["location"] ?? "");
            $eventType = trim($_POST["eventType"] ?? "consultation");
            $meetingMode = trim($_POST["meetingMode"] ?? "google_meet");
            $agenda = trim($_POST["agenda"] ?? "");

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
                $this->redirect('/appointment/book?account=' . $receiverId);
            }

            if ($receiverId === $senderId) {
                $this->setFlash('warning', 'You cannot book an appointment with yourself.');
                $this->redirect('/search');
            }

            if ($appointmentDate < date('Y-m-d')) {
                $this->setFlash('danger', 'Cannot schedule appointments in the past.');
                $this->redirect('/appointment/book?account=' . $receiverId);
            }

            // Conflict detection
            if ($this->appointmentModel->hasConflict($receiverId, $appointmentDate, $appointmentTime)) {
                $this->setFlash('danger', 'The selected time slot (' . $appointmentTime . ') is already booked. Please choose an open slot.');
                $this->redirect('/appointment/book?account=' . $receiverId);
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
                $this->redirect('/appointment/book?account=' . $receiverId);
            }
        } else {
            $this->redirect('/search');
        }
    }

    public function view($params = []) {
        $this->requireAuth();
        $id = $params["id"] ?? $_GET["id"] ?? "";
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/received');
        }

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
        $id = $params["id"] ?? $_GET["id"] ?? "";
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/received');
        }

        $isSender = ($appt['sender_id'] === $userId);
        $partnerName = $isSender 
            ? trim(($appt['receiver_first_name'] ?? '') . ' ' . ($appt['receiver_last_name'] ?? ''))
            : trim(($appt['sender_first_name'] ?? '') . ' ' . ($appt['sender_last_name'] ?? ''));

        $title = $appt['event_name'] ?: 'Appointment with ' . $partnerName;
        $description = "More Than Blue Appointment with " . $partnerName;
        if (!empty($appt['agenda'])) {
            $description .= "\\n\\nAgenda: " . str_replace(["\r\n", "\n"], "\\n", $appt['agenda']);
        }
        $location = $appt['location'] ?: 'Online';

        $durationMinutes = 30;
        if (($appt['event_type'] ?? '') === 'quick_sync') $durationMinutes = 15;
        elseif (($appt['event_type'] ?? '') === 'deep_dive') $durationMinutes = 60;

        $startTimeStr = $appt['appointment_time'] ?: '09:00';
        $startDT = new \DateTime($appt['appointment_date'] . ' ' . $startTimeStr);
        $endDT = clone $startDT;
        $endDT->modify("+{$durationMinutes} minutes");

        $dtStart = $startDT->format('Ymd\THis');
        $dtEnd = $endDT->format('Ymd\THis');
        $dtStamp = gmdate('Ymd\THis\Z');
        $uid = $appt['appointment_id'] . '@morethanblue.local';

        $ics = "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//More Than Blue//Appointment Scheduler//EN\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:PUBLISH\r\n"
            . "BEGIN:VEVENT\r\n"
            . "UID:" . $uid . "\r\n"
            . "DTSTAMP:" . $dtStamp . "\r\n"
            . "DTSTART:" . $dtStart . "\r\n"
            . "DTEND:" . $dtEnd . "\r\n"
            . "SUMMARY:" . addcslashes($title, ",;") . "\r\n"
            . "DESCRIPTION:" . addcslashes($description, ",;") . "\r\n"
            . "LOCATION:" . addcslashes($location, ",;") . "\r\n"
            . "STATUS:CONFIRMED\r\n"
            . "END:VEVENT\r\n"
            . "END:VCALENDAR\r\n";

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="appointment-' . substr($appt['appointment_id'], 0, 8) . '.ics"');
        header('Content-Length: ' . strlen($ics));
        echo $ics;
        exit;
    }

    public function edit($params = []) {
        $this->requireAuth();
        $id = $params["id"] ?? "";
        $userId = $_SESSION["userId"];

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentForUser($id, $userId);
        if (!$appt) {
            $this->setFlash('danger', 'Appointment not found or unauthorized access.');
            $this->redirect('/received');
        }

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

        $id = trim($_POST["id"] ?? "");
        $status = trim($_POST["status"] ?? "");

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
        $id = $params["id"] ?? "";
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

        $id = trim($_POST["id"] ?? "");

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
        $id = $params["id"] ?? "";
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

        $id = trim($_POST["id"] ?? "");

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
