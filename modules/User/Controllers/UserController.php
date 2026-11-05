<?php
namespace App\Modules\User\Controllers;

use App\Core\BaseController;
use App\Modules\Auth\Models\UserModel;

class UserController extends BaseController {
    /**
     * @var UserModel
     */
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
            $firstName = trim(scalarString($_POST['firstName'] ?? ''));
            $middleName = trim(scalarString($_POST['middleName'] ?? ''));
            $lastName = trim(scalarString($_POST['lastName'] ?? ''));
            $searchName = strtoupper(trim($firstName . " " . $lastName));
            $contactNumber = trim(scalarString($_POST['contactNumber'] ?? ''));
            $emailAddress = strtolower(trim(scalarString($_POST['emailAddress'] ?? '')));
            $workStartTime = trim(scalarString($_POST['workStartTime'] ?? '09:00'));
            $workEndTime = trim(scalarString($_POST['workEndTime'] ?? '17:00'));
            $slotDuration = (int)scalarString($_POST['slotDuration'] ?? '30');
            $headline = trim(scalarString($_POST['headline'] ?? ''));
            $jobTitle = trim(scalarString($_POST['jobTitle'] ?? ''));
            $company = trim(scalarString($_POST['company'] ?? ''));
            $locationCity = trim(scalarString($_POST['locationCity'] ?? ''));
            $bio = trim(scalarString($_POST['bio'] ?? ''));
            $skills = trim(scalarString($_POST['skills'] ?? ''));
            $consultationTopics = trim(scalarString($_POST['consultationTopics'] ?? ''));
            $linkedinUrl = trim(scalarString($_POST['linkedinUrl'] ?? ''));
            $githubUrl = trim(scalarString($_POST['githubUrl'] ?? ''));
            $websiteUrl = trim(scalarString($_POST['websiteUrl'] ?? ''));
            $dateUpdate = date("Y-m-d");

            foreach (['LinkedIn' => $linkedinUrl, 'GitHub' => $githubUrl, 'Website' => $websiteUrl] as $label => $url) {
                if ($url === '') continue;
                $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
                if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
                    $this->setFlash('danger', $label . ' link must be a valid HTTP or HTTPS URL.');
                    $this->redirect('/profile');
                }
            }

            $contactNumber = preg_replace('/\D/', '', $contactNumber);
            if (strlen($contactNumber) === 11 && str_starts_with($contactNumber, '0')) {
                $contactNumber = substr($contactNumber, 1);
            }
            if (strlen($contactNumber) < 7 || strlen($contactNumber) > 15) {
                $this->setFlash('danger', 'Please provide a valid contact number.');
                $this->redirect('/profile');
            }

            $startTime = \DateTime::createFromFormat('!H:i', $workStartTime);
            $endTime = \DateTime::createFromFormat('!H:i', $workEndTime);
            $timeErrors = \DateTime::getLastErrors();
            if (!$startTime || !$endTime
                || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $workStartTime)
                || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $workEndTime)
                || $endTime <= $startTime || !in_array($slotDuration, [15, 30, 45, 60], true)
                || ($timeErrors !== false && ($timeErrors['warning_count'] > 0 || $timeErrors['error_count'] > 0))) {
                $this->setFlash('danger', 'Please choose valid working hours and a supported slot duration.');
                $this->redirect('/profile');
            }

            if (empty($firstName) || empty($lastName)
                || strlen($firstName) > 100 || strlen($middleName) > 100 || strlen($lastName) > 100
                || strlen($headline) > 200 || strlen($jobTitle) > 150 || strlen($company) > 150
                || strlen($locationCity) > 100 || strlen($linkedinUrl) > 255
                || strlen($githubUrl) > 255 || strlen($websiteUrl) > 255) {
                $this->setFlash('danger', 'Please check the required fields and their maximum lengths.');
                $this->redirect('/profile');
            }

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email address provided.');
                $this->redirect('/profile');
            }
            if (strlen($emailAddress) > 100) {
                $this->setFlash('danger', 'Email address is too long.');
                $this->redirect('/profile');
            }

            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                $this->redirect('/login');
            }

            if ($this->userModel->getOtherUserByEmailOrPhone($emailAddress, $contactNumber, $userId)) {
                $this->setFlash('danger', 'That contact number or email address is already in use.');
                $this->redirect('/profile');
            }

            $profilePicturePath = $user['profile_picture'];
            $newUploadPath = null;

            // Check if a new file is uploaded
            if (isset($_FILES["profilePicture"]) && $_FILES["profilePicture"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $profilePictureError = $_FILES["profilePicture"]["error"];
                if ($profilePictureError !== UPLOAD_ERR_OK) {
                    $this->setFlash('danger', 'Error uploading profile image. Please try again.');
                    $this->redirect('/profile');
                }

                $profilePictureSize = $_FILES["profilePicture"]["size"];
                if ($profilePictureSize > 10000000) {
                    $this->setFlash('danger', 'File size exceeds 10MB limit.');
                    $this->redirect('/profile');
                }

                $ext = validateUploadedImage($_FILES["profilePicture"]["tmp_name"]);
                if (!$ext) {
                    $this->setFlash('danger', 'Invalid file type. Only PNG, JPG, and JPEG images are allowed.');
                    $this->redirect('/profile');
                }

                $publicDir = dirname(__DIR__, 3) . '/public';
                $uploadsDir = $publicDir . '/uploads';
                if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true) && !is_dir($uploadsDir)) {
                    $this->setFlash('danger', 'Image uploads are temporarily unavailable.');
                    $this->redirect('/profile');
                }

                $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;
                $targetPath = $uploadsDir . '/' . $newFileName;

                if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetPath)) {
                    $this->setFlash('danger', 'Failed to store uploaded file.');
                    $this->redirect('/profile');
                }

                $profilePicturePath = 'uploads/' . $newFileName;
                $newUploadPath = $targetPath;
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
                if ($newUploadPath !== null && !empty($user['profile_picture'])) {
                    $realUploadsDir = realpath(dirname($newUploadPath));
                    $oldFilePath = realpath(dirname(__DIR__, 3) . '/public/' . ltrim($user['profile_picture'], '/\\'));
                    if ($oldFilePath && $realUploadsDir && str_starts_with($oldFilePath, $realUploadsDir . DIRECTORY_SEPARATOR) && is_file($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $_SESSION['profilePicture'] = $profilePicturePath;
                $_SESSION['searchName'] = $searchName;
                $this->setFlash('success', 'Profile and availability settings updated successfully!');
                $this->redirect('/profile');
            } else {
                if ($newUploadPath !== null && is_file($newUploadPath)) unlink($newUploadPath);
                $this->setFlash('danger', 'An error occurred while updating your profile.');
                $this->redirect('/profile');
            }
        } else {
            $this->redirect('/profile');
        }
    }

    public function search($params = []) {
        $this->requireAuth();
        
        $name = trim(scalarString($params['name'] ?? $_GET['name'] ?? $_POST['name'] ?? ''));
        $name = substr($name, 0, 100);
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

}
