<?php
namespace App\Modules\Auth\Controllers;

use App\Core\BaseController;
use App\Modules\Auth\Models\UserModel;

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

        if (isset($_POST["login"])) {
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

        if (isset($_POST["signup"])) {
            $this->validateCsrf();
            $userId = $this->generateUserId();
            $firstName = trim($_POST["firstName"] ?? '');
            $middleName = trim($_POST["middleName"] ?? '');
            $lastName = trim($_POST["lastName"] ?? '');
            $searchName = strtoupper($firstName . " " . $lastName);
            $contactNumber = trim($_POST["contactNumber"] ?? '');
            $emailAddress = filter_var(trim($_POST["emailAddress"] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST["password"] ?? '');

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email address provided.');
                $this->redirect('/signup');
            }

            if (strlen($password) < 6) {
                $this->setFlash('danger', 'Password must be at least 6 characters.');
                $this->redirect('/signup');
            }

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

                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES["profilePicture"]["tmp_name"]);
                $allowedMimes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                ];

                if (!array_key_exists($mimeType, $allowedMimes)) {
                    $this->setFlash('danger', 'Invalid file type. Only PNG, JPG, and JPEG images are allowed.');
                    $this->redirect('/signup');
                }

                $existingUser = $this->userModel->getUserByEmailOrPhone($emailAddress, $contactNumber);
                if (!$existingUser) {
                    $publicUploadsDir = dirname(__DIR__, 3) . '/public/uploads';
                    if (!is_dir($publicUploadsDir)) {
                        mkdir($publicUploadsDir, 0755, true);
                    }

                    $ext = $allowedMimes[$mimeType];
                    $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
                    $targetPath = $publicUploadsDir . '/' . $fileName;

                    if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetPath)) {
                        $this->setFlash('danger', 'Failed to store uploaded profile picture.');
                        $this->redirect('/signup');
                    }

                    $profilePictureRelativePath = 'uploads/' . $fileName;

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

                    if ($this->userModel->createUser($signupData)) {
                        $this->setFlash('success', 'Signup successful! Please sign in with your credentials.');
                        $this->redirect('/login');
                    } else {
                        $this->setFlash('danger', 'An error occurred during registration. Please try again.');
                        $this->redirect('/signup');
                    }
                } else {
                    $this->setFlash('danger', 'The contact number or email address is already registered.');
                    $this->redirect('/signup');
                }
            } else {
                $this->setFlash('danger', 'Please upload a profile picture.');
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
