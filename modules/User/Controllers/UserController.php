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
            $this->validateCsrf();
            $userId = $_SESSION["userId"];
            $firstName = trim($_POST["firstName"] ?? '');
            $middleName = trim($_POST["middleName"] ?? '');
            $lastName = trim($_POST["lastName"] ?? '');
            $searchName = strtoupper($firstName . " " . $lastName);
            $contactNumber = trim($_POST["contactNumber"] ?? '');
            $emailAddress = filter_var(trim($_POST["emailAddress"] ?? ''), FILTER_SANITIZE_EMAIL);
            $dateUpdate = date("Y-m-d");

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email address provided.');
                $this->redirect('/profile');
            }

            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                $this->redirect('/login');
            }

            $profilePicturePath = $user['profile_picture'];

            // Check if a new file is uploaded
            if (isset($_FILES["profilePicture"]) && $_FILES["profilePicture"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $profilePictureError = $_FILES["profilePicture"]["error"];
                if ($profilePictureError !== UPLOAD_ERR_OK) {
                    $this->setFlash('danger', 'Error uploading profile image. Please try again.');
                    $this->redirect('/profile');
                }

                $profilePictureSize = $_FILES["profilePicture"]["size"];
                if ($profilePictureSize >= 10000000) {
                    $this->setFlash('danger', 'File size exceeds 10MB limit.');
                    $this->redirect('/profile');
                }

                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES["profilePicture"]["tmp_name"]);
                $allowedMimes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                ];

                if (!array_key_exists($mimeType, $allowedMimes)) {
                    $this->setFlash('danger', 'Invalid file type. Only PNG, JPG, and JPEG images are allowed.');
                    $this->redirect('/profile');
                }

                $publicDir = dirname(__DIR__, 3) . '/public';
                $uploadsDir = $publicDir . '/uploads';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }

                $ext = $allowedMimes[$mimeType];
                $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;
                $targetPath = $uploadsDir . '/' . $newFileName;

                if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetPath)) {
                    $this->setFlash('danger', 'Failed to store uploaded file.');
                    $this->redirect('/profile');
                }

                // Safely delete old picture from disk if it was an uploaded file
                $realUploadsDir = realpath($uploadsDir);
                if (!empty($user['profile_picture'])) {
                    $oldFilePath = realpath($publicDir . '/' . ltrim($user['profile_picture'], '/'));
                    if ($oldFilePath && $realUploadsDir && str_starts_with($oldFilePath, $realUploadsDir) && file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }

                $profilePicturePath = 'uploads/' . $newFileName;
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
                $this->setFlash('success', 'Profile updated successfully!');
                $this->redirect('/profile');
            } else {
                $this->setFlash('danger', 'An error occurred while updating your profile.');
                $this->redirect('/profile');
            }
        } else {
            $this->redirect('/profile');
        }
    }

    public function search($params = []) {
        $this->requireAuth();
        
        $name = trim($params["name"] ?? $_GET["name"] ?? $_POST["name"] ?? "");
        $searchResults = null;
        $searched = false;

        if ($name !== '') {
            $searchResults = $this->userModel->searchUsers($name);
            $searched = true;
        }

        $this->renderView('User/Views/search', [
            'searchTerm' => $name,
            'searchResults' => $searchResults,
            'searched' => $searched
        ]);
    }

    public function searchSubmit($params = []) {
        $this->search($params);
    }

    public function feedbacks() {
        $this->requireAuth();
        $this->renderView('User/Views/feedbacks');
    }
}
