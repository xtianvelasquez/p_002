<?php
namespace App\Modules\Appointment\Controllers;

use App\Core\BaseController;
use App\Modules\Appointment\Models\AppointmentModel;

class AppointmentController extends BaseController {
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

        $slots = $this->appointmentModel->generateAvailableSlots($receiverId, $date);
        return $this->jsonResponse([
            'receiverId' => $receiverId,
            'date' => $date,
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

    public function view($params) {
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

        $isSender = ($appt['sender_id'] === $userId);
        $displayName = $isSender 
            ? strtoupper(($appt['receiver_first_name'] ?? '') . ' ' . ($appt['receiver_last_name'] ?? ''))
            : strtoupper(($appt['sender_first_name'] ?? '') . ' ' . ($appt['sender_last_name'] ?? ''));
        $displayContact = $isSender ? ($appt['receiver_contact'] ?? '') : ($appt['sender_contact'] ?? '');
        $displayEmail = $isSender ? ($appt['receiver_email'] ?? '') : ($appt['sender_email'] ?? '');

        $this->renderView('Appointment/Views/view', [
            'appointmentId' => $appt['appointment_id'],
            'fullName' => $displayName,
            'contactNumber' => !empty($displayContact) ? "+63" . $displayContact : "",
            'emailAddress' => $displayEmail,
            'appointment_date' => $appt['appointment_date'],
            'appointment_time' => $appt['appointment_time'],
            'event_name' => $appt['event_name'],
            'location' => $appt['location'],
            'appointment_status' => $appt['appointment_status']
        ]);
    }

    public function edit($params) {
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

    public function deleteForm($params) {
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

    public function deleteSentForm($params) {
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
