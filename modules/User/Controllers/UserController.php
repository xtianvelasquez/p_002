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
                'userId' => $user["user_id"],
                'firstName' => $user["first_name"],
                'middleName' => $user["middle_name"],
                'lastName' => $user["last_name"],
                'contactNumber' => $user["contact_number"],
                'emailAddress' => $user["email_address"],
                'currentProfilePicture' => $user["profile_picture"],
                'workStartTime' => $user["work_start_time"] ?? '09:00',
                'workEndTime' => $user["work_end_time"] ?? '17:00',
                'slotDuration' => $user["slot_duration_minutes"] ?? 30,
                'headline' => $user["headline"] ?? '',
                'bio' => $user["bio"] ?? '',
                'jobTitle' => $user["job_title"] ?? '',
                'company' => $user["company"] ?? '',
                'locationCity' => $user["location_city"] ?? '',
                'skills' => $user["skills"] ?? '',
                'consultationTopics' => $user["consultation_topics"] ?? '',
                'linkedinUrl' => $user["linkedin_url"] ?? '',
                'githubUrl' => $user["github_url"] ?? '',
                'websiteUrl' => $user["website_url"] ?? '',
                'shareableLink' => routeUrl('/u/' . $user["user_id"])
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
            $searchName = strtoupper(trim($firstName . " " . $lastName));
            $contactNumber = trim($_POST["contactNumber"] ?? '');
            $emailAddress = filter_var(trim($_POST["emailAddress"] ?? ''), FILTER_SANITIZE_EMAIL);
            $workStartTime = trim($_POST["workStartTime"] ?? '09:00');
            $workEndTime = trim($_POST["workEndTime"] ?? '17:00');
            $slotDuration = max(15, min(120, (int)($_POST["slotDuration"] ?? 30)));
            $headline = trim($_POST["headline"] ?? '');
            $jobTitle = trim($_POST["jobTitle"] ?? '');
            $company = trim($_POST["company"] ?? '');
            $locationCity = trim($_POST["locationCity"] ?? '');
            $bio = trim($_POST["bio"] ?? '');
            $skills = trim($_POST["skills"] ?? '');
            $consultationTopics = trim($_POST["consultationTopics"] ?? '');
            $linkedinUrl = trim($_POST["linkedinUrl"] ?? '');
            $githubUrl = trim($_POST["githubUrl"] ?? '');
            $websiteUrl = trim($_POST["websiteUrl"] ?? '');
            $dateUpdate = date("Y-m-d");

            if (empty($firstName) || empty($lastName)) {
                $this->setFlash('danger', 'First name and last name are required.');
                $this->redirect('/profile');
            }

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

                $ext = validateUploadedImage($_FILES["profilePicture"]["tmp_name"], $_FILES["profilePicture"]["name"]);
                if (!$ext) {
                    $this->setFlash('danger', 'Invalid file type. Only PNG, JPG, and JPEG images are allowed.');
                    $this->redirect('/profile');
                }

                $publicDir = dirname(__DIR__, 3) . '/public';
                $uploadsDir = $publicDir . '/uploads';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }

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
                'last_update' => $dateUpdate,
                'work_start_time' => $workStartTime,
                'work_end_time' => $workEndTime,
                'slot_duration_minutes' => $slotDuration,
                'headline' => $headline,
                'bio' => $bio,
                'job_title' => $jobTitle,
                'company' => $company,
                'location_city' => $locationCity,
                'skills' => $skills,
                'consultation_topics' => $consultationTopics,
                'linkedin_url' => $linkedinUrl,
                'github_url' => $githubUrl,
                'website_url' => $websiteUrl
            ];

            if ($this->userModel->updateUser($userId, $updateData)) {
                $_SESSION['profilePicture'] = $profilePicturePath;
                $_SESSION['searchName'] = $searchName;
                $this->setFlash('success', 'Profile and availability settings updated successfully!');
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
