<?php
namespace App\Modules\User\Controllers;

use App\Core\BaseController;
use App\Modules\Auth\Models\UserModel;

class UserController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    private function requireAuth() {
        if (!isset($_SESSION["userId"])) {
            $this->redirect('/login');
        }
    }

    public function profile() {
        $this->requireAuth();
        
        $userId = $_SESSION["userId"];
        $user = $this->userModel->getUserById($userId);
        
        if ($user) {
            $data = [
                'firstName' => $user["first_name"],
                'middleName' => $user["middle_name"],
                'lastName' => $user["last_name"],
                'contactNumber' => $user["contact_number"],
                'emailAddress' => $user["email_address"],
                'currentProfilePicture' => $user["profile_picture"]
            ];
            $this->renderView('User/Views/profile', $data);
        } else {
            $this->redirect('/login');
        }
    }

    public function profileUpdate() {
        $this->requireAuth();
        
        if (isset($_POST["update"])) {
            $userId = $_SESSION["userId"];
            $firstName = trim($_POST["firstName"]);
            $middleName = trim($_POST["middleName"]);
            $lastName = trim($_POST["lastName"]);
            $searchName = strtoupper($firstName . " " . $lastName);
            $contactNumber = trim($_POST["contactNumber"]);
            $emailAddress = filter_var(trim($_POST["emailAddress"]), FILTER_SANITIZE_EMAIL);
            $currentProfilePicture = $_POST["currentProfilePicture"];
            $dateUpdate = date("Y-m-d");

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $error = urlencode("Invalid email address.");
                $this->redirect('/feedbacks?profileerror=' . $error);
            }

            $user = $this->userModel->getUserById($userId);
            if ($user) {
                $lastDateUpdate = $user["last_update"];
                if ($lastDateUpdate === $dateUpdate) {
                    $error = urlencode("Updating is done once a day only.");
                    $this->redirect('/feedbacks?profileerror=' . $error);
                }
            }

            if (isset($_FILES["profilePicture"])) {
                $profilePictureError = $_FILES["profilePicture"]["error"];
                if ($profilePictureError !== UPLOAD_ERR_OK) {
                    $error = urlencode("Error uploading files. Please try again later.");
                    $this->redirect('/feedbacks?profileerror=' . $error);
                }

                $allowedExtensions = array("png", "jpg", "jpeg");
                $profilePictureName = basename($_FILES["profilePicture"]["name"]);
                $profilePictureExtension = strtolower(pathinfo($profilePictureName, PATHINFO_EXTENSION));
                if (!in_array($profilePictureExtension, $allowedExtensions)) {
                    $error = urlencode("Invalid file type. Only PNG, JPG, and JPEG files are allowed.");
                    $this->redirect('/feedbacks?profileerror=' . $error);
                }

                $profilePictureSize = $_FILES["profilePicture"]["size"];
                if ($profilePictureSize >= 10000000) {
                    $error = urlencode("File size too large. Maximum size allowed is 10MB.");
                    $this->redirect('/feedbacks?profileerror=' . $error);
                }

                $targetDirectory = "uploads/";
                if (!is_dir($targetDirectory)) {
                    mkdir($targetDirectory, 0755, true);
                }

                $profilePicturePath = $targetDirectory . uniqid() . '.' . $profilePictureExtension;
                if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $profilePicturePath)) {
                    $error = urlencode("Failed to move uploaded files to the target directory. Please try again later.");
                    $this->redirect('/feedbacks?profileerror=' . $error);
                }

                // Delete old picture
                if (!empty($currentProfilePicture) && file_exists($currentProfilePicture)) {
                    unlink($currentProfilePicture);
                }

                $updateData = [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'search_name' => $searchName,
                    'contact_number' => $contactNumber,
                    'email_address' => $emailAddress,
                    'profile_picture' => $profilePicturePath,
                    'last_update' => $dateUpdate
                ];

                if ($this->userModel->updateUser($userId, $updateData)) {
                    $_SESSION['profilePicture'] = $profilePicturePath;
                    $_SESSION['searchName'] = $searchName;
                    $this->redirect('/profile');
                } else {
                    $error = urlencode("We're currently experiencing some technical difficulties. Please try again later.");
                    $this->redirect('/feedbacks?profileerror=' . $error);
                }
            }
        } else {
            $this->redirect('/profile');
        }
    }

    public function search() {
        $this->requireAuth();
        $this->renderView('User/Views/search', ['searchResults' => null, 'searched' => false]);
    }

    public function searchSubmit() {
        $this->requireAuth();
        
        $searchResults = [];
        $searched = false;
        if (isset($_POST["search"])) {
            $name = strtoupper($_POST["name"]);
            $searchResults = $this->userModel->searchUsers($name);
            $searched = true;
        }
        
        $this->renderView('User/Views/search', [
            'searchResults' => $searchResults,
            'searched' => $searched
        ]);
    }

    public function feedbacks() {
        $this->requireAuth();
        $this->renderView('User/Views/feedbacks');
    }
}
