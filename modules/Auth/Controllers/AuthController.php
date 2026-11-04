<?php
namespace App\Modules\Auth\Controllers;

use App\Core\BaseController;
use App\Modules\Auth\Models\UserModel;
use Exception;

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    private function generateUserId($length = 20) {
        $characters = '00112233445566778899aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzzAABBCCDDEEFFGGHHIIJJKKLLMMNNOOPPQQRRSSTTUUVVWWXXYYZZ';
        $generatedUserId = '';
        for ($i = 0; $i < $length; $i++) {
            $generatedUserId .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $generatedUserId;
    }

    public function index() {
        if (isset($_SESSION["userId"])) {
            $this->redirect('/received');
        }
        $this->renderView('Auth/Views/index');
    }

    public function loginForm() {
        if (isset($_SESSION["userId"])) {
            $this->redirect('/received');
        }
        $this->renderView('Auth/Views/login');
    }

    public function login() {
        if (isset($_SESSION["userId"])) {
            $this->redirect('/received');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $emailAddress = filter_var(trim($_POST["emailAddress"] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST["password"] ?? '');

            $user = $this->userModel->getUserByEmailAndPassword($emailAddress, $password);
            if ($user) {
                $_SESSION["profilePicture"] = $user["profile_picture"];
                $_SESSION["searchName"] = $user["search_name"];
                $_SESSION["emailAddress"] = $user["email_address"];
                $_SESSION["userId"] = $user["user_id"];
                $this->setFlash('success', 'Welcome back, ' . $user['first_name'] . '!');
                $this->redirect('/received');
            } else {
                $this->setFlash('danger', 'The email or password you entered is incorrect. Please try again.');
                $this->redirect('/login');
            }
        } else {
            $this->redirect('/login');
        }
    }

    public function signupForm() {
        if (isset($_SESSION["userId"])) {
            $this->redirect('/received');
        }
        $this->renderView('Auth/Views/signup');
    }

    public function signup() {
        if (isset($_SESSION["userId"])) {
            $this->redirect('/received');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $userId = $this->generateUserId();
            $firstName = trim($_POST["firstName"] ?? '');
            $middleName = trim($_POST["middleName"] ?? '');
            $lastName = trim($_POST["lastName"] ?? '');
            $searchName = strtoupper(trim($firstName . " " . $lastName));
            $rawContact = trim($_POST["contactNumber"] ?? '');
            $emailAddress = filter_var(trim($_POST["emailAddress"] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST["password"] ?? '');

            if (empty($firstName) || empty($lastName)) {
                $this->setFlash('danger', 'First name and last name are required.');
                $this->redirect('/signup');
            }

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email address provided.');
                $this->redirect('/signup');
            }

            if (strlen($password) < 6) {
                $this->setFlash('danger', 'Password must be at least 6 characters.');
                $this->redirect('/signup');
            }

            // Normalize contact number: extract digits, strip leading 0 if 11 digits
            $contactNumber = preg_replace('/\D/', '', $rawContact);
            if (strlen($contactNumber) === 11 && str_starts_with($contactNumber, '0')) {
                $contactNumber = substr($contactNumber, 1);
            }
            if (strlen($contactNumber) < 7 || strlen($contactNumber) > 15) {
                $this->setFlash('danger', 'Please provide a valid contact number (10 digits).');
                $this->redirect('/signup');
            }

            // Check if user already exists
            $existingUser = $this->userModel->getUserByEmailOrPhone($emailAddress, $contactNumber);
            if ($existingUser) {
                $this->setFlash('danger', 'The contact number or email address is already registered.');
                $this->redirect('/signup');
            }

            // Handle Profile Picture (optional with default avatar)
            $profilePictureRelativePath = 'images/logo.png';

            if (isset($_FILES["profilePicture"]) && $_FILES["profilePicture"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $profilePictureError = $_FILES["profilePicture"]["error"];
                if ($profilePictureError !== UPLOAD_ERR_OK) {
                    $this->setFlash('danger', 'Error uploading profile image. Please try again.');
                    $this->redirect('/signup');
                }

                $profilePictureSize = $_FILES["profilePicture"]["size"];
                if ($profilePictureSize >= 10000000) {
                    $this->setFlash('danger', 'File size exceeds 10MB limit.');
                    $this->redirect('/signup');
                }

                $ext = validateUploadedImage($_FILES["profilePicture"]["tmp_name"], $_FILES["profilePicture"]["name"]);
                if (!$ext) {
                    $this->setFlash('danger', 'Invalid file type. Only PNG, JPG, and JPEG images are allowed.');
                    $this->redirect('/signup');
                }

                $publicUploadsDir = dirname(__DIR__, 3) . '/public/uploads';
                if (!is_dir($publicUploadsDir)) {
                    mkdir($publicUploadsDir, 0755, true);
                }

                $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
                $targetPath = $publicUploadsDir . '/' . $fileName;

                if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetPath)) {
                    $this->setFlash('danger', 'Failed to store uploaded profile picture.');
                    $this->redirect('/signup');
                }

                $profilePictureRelativePath = 'uploads/' . $fileName;
            }

            $signupData = [
                'user_id' => $userId,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'search_name' => $searchName,
                'contact_number' => $contactNumber,
                'email_address' => $emailAddress,
                'user_password' => password_hash($password, PASSWORD_DEFAULT),
                'profile_picture' => $profilePictureRelativePath
            ];

            try {
                if ($this->userModel->createUser($signupData)) {
                    $this->setFlash('success', 'Signup successful! Please sign in with your credentials.');
                    $this->redirect('/login');
                } else {
                    $this->setFlash('danger', 'An error occurred during registration. Please try again.');
                    $this->redirect('/signup');
                }
            } catch (Exception $e) {
                $this->setFlash('danger', 'Database registration error: ' . $e->getMessage());
                $this->redirect('/signup');
            }
        } else {
            $this->redirect('/signup');
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        $this->redirect('/login');
    }

    public function zone() {
        $this->renderView('Auth/Views/zone');
    }
}
