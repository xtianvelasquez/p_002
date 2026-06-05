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
            $sender = $this->appointmentModel->getUserDetails($appt["sender_id"]);
            $fullName = $sender ? strtoupper($sender["first_name"] . " " . $sender["last_name"]) : "UNKNOWN";
            
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
            $receiver = $this->appointmentModel->getUserDetails($appt["receiver_id"]);
            $fullName = $receiver ? strtoupper($receiver["first_name"] . " " . $receiver["last_name"]) : "UNKNOWN";
            
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

    public function bookForm($params) {
        $this->requireAuth();
        $id = $params["account"] ?? "";

        if (empty($id)) {
            $error = urlencode("Missing account ID.");
            $this->redirect('/feedbacks?accounterror=' . $error);
        }

        $account = $this->appointmentModel->getUserDetails($id);
        if (!$account) {
            $error = urlencode("Account not found.");
            $this->redirect('/feedbacks?accounterror=' . $error);
        }

        $this->renderView('Appointment/Views/book', [
            'id' => $id,
            'firstName' => $account['first_name'],
            'middleName' => $account['middle_name'],
            'lastName' => $account['last_name'],
            'contactNumber' => $account['contact_number'],
            'emailAddress' => $account['email_address'],
            'profilePicture' => $account['profile_picture']
        ]);
    }

    public function book() {
        $this->requireAuth();
        
        if (isset($_POST["submit"])) {
            $receiverId = $_POST["receiverId"] ?? "";
            $senderId = $_SESSION["userId"] ?? "";
            $appointmentDate = $_POST["appointmentDate"] ?? "";
            $appointmentTime = $_POST["appointmentTime"] ?? "";
            $eventName = $_POST["eventName"] ?? "";
            $location = $_POST["location"] ?? "";

            if (empty($receiverId) || empty($senderId) || empty($appointmentDate) || empty($appointmentTime) || empty($eventName) || empty($location)) {
                $error = urlencode("All fields are required.");
                $this->redirect('/feedbacks?appointmenterror=' . $error);
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
                $this->redirect('/search');
            } else {
                $error = urlencode("We're currently experiencing some technical difficulties. Please try again later.");
                $this->redirect('/feedbacks?appointmenterror=' . $error);
            }
        } else {
            $this->redirect('/search');
        }
    }

    public function view($params) {
        $this->requireAuth();
        $id = $params["id"] ?? "";

        if (empty($id)) {
            $this->redirect('/received');
        }

        $appt = $this->appointmentModel->getAppointmentById($id);
        if (!$appt) {
            $error = urlencode("No further informations found.");
            $this->redirect('/feedbacks?actionerror=' . $error);
        }

        $sender = $this->appointmentModel->getUserDetails($appt["sender_id"]);
        $fullName = $sender ? strtoupper($sender["first_name"] . " " . $sender["last_name"]) : "UNKNOWN";
        $contactNumber = $sender ? "+63" . $sender["contact_number"] : "";
        $emailAddress = $sender ? $sender["email_address"] : "";

        $this->renderView('Appointment/Views/view', [
            'appointmentId' => $appt['appointment_id'],
            'fullName' => $fullName,
            'contactNumber' => $contactNumber,
            'emailAddress' => $emailAddress,
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

        if (empty($id)) {
            $this->redirect('/received');
        }

        $this->renderView('Appointment/Views/edit', ['appointmentId' => $id]);
    }

    public function updateStatus($params) {
        $this->requireAuth();
        
        $id = "";
        $status = "";
        
        if (isset($params["approved"])) {
            $id = $params["approved"];
            $status = "approved";
        } elseif (isset($params["denied"])) {
            $id = $params["denied"];
            $status = "denied";
        } elseif (isset($params["cancel"])) {
            $id = $params["cancel"];
            $status = "cancel";
        }

        if (empty($id) || empty($status)) {
            $this->redirect('/received');
        }

        if ($this->appointmentModel->updateAppointmentStatus($id, $status)) {
            $this->redirect('/received');
        } else {
            $error = urlencode("We're currently experiencing some technical difficulties. Please try again later.");
            $this->redirect('/feedbacks?actionerror=' . $error);
        }
    }

    public function deleteForm($params) {
        $this->requireAuth();
        $id = $params["id"] ?? "";

        if (empty($id)) {
            $this->redirect('/received');
        }

        $this->renderView('Appointment/Views/delete', ['appointmentId' => $id]);
    }

    public function deleteConfirm($params) {
        $this->requireAuth();
        $id = $params["yes"] ?? "";

        if (empty($id)) {
            $this->redirect('/received');
        }

        if ($this->appointmentModel->deleteAppointment($id)) {
            $this->redirect('/received');
        } else {
            $error = urlencode("We're currently experiencing some technical difficulties. Please try again later.");
            $this->redirect('/feedbacks?actionerror=' . $error);
        }
    }

    public function deleteSentForm($params) {
        $this->requireAuth();
        $id = $params["id"] ?? "";

        if (empty($id)) {
            $this->redirect('/sent');
        }

        $this->renderView('Appointment/Views/delete-sent', ['appointmentId' => $id]);
    }

    public function deleteSentConfirm($params) {
        $this->requireAuth();
        $id = $params["yes"] ?? "";

        if (empty($id)) {
            $this->redirect('/sent');
        }

        if ($this->appointmentModel->deleteAppointment($id)) {
            $this->redirect('/sent');
        } else {
            $error = urlencode("We're currently experiencing some technical difficulties. Please try again later.");
            $this->redirect('/feedbacks?actionerror=' . $error);
        }
    }
}
