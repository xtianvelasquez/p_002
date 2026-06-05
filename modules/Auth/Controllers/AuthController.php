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
            $emailAddress = filter_var(trim($_POST["emailAddress"]), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST["password"]);

            $user = $this->userModel->getUserByEmailAndPassword($emailAddress, $password);
            if ($user) {
                $_SESSION["profilePicture"] = $user["profile_picture"];
                $_SESSION["searchName"] = $user["search_name"];
                $_SESSION["emailAddress"] = $user["email_address"];
                $_SESSION["userId"] = $user["user_id"];
                $this->redirect('/received');
            } else {
                $error = urlencode("The email or password you entered is incorrect. Please try again.");
                $this->redirect('/zone?loginerror=' . $error);
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
            $userId = $this->generateUserId();
            $firstName = trim($_POST["firstName"]);
            $middleName = trim($_POST["middleName"]);
            $lastName = trim($_POST["lastName"]);
            $searchName = strtoupper($firstName . " " . $lastName);
            $contactNumber = trim($_POST["contactNumber"]);
            $emailAddress = filter_var(trim($_POST["emailAddress"]), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST["password"]);

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $error = urlencode("Invalid email address.");
                $this->redirect('/zone?signuperror=' . $error);
            }

            if (isset($_FILES["profilePicture"])) {
                $profilePictureError = $_FILES["profilePicture"]["error"];
                if ($profilePictureError !== UPLOAD_ERR_OK) {
                    $error = urlencode("Error uploading files. Please try again later.");
                    $this->redirect('/zone?signuperror=' . $error);
                }

                $allowedExtensions = array("png", "jpg", "jpeg");
                $profilePictureName = basename($_FILES["profilePicture"]["name"]);
                $profilePictureExtension = strtolower(pathinfo($profilePictureName, PATHINFO_EXTENSION));
                if (!in_array($profilePictureExtension, $allowedExtensions)) {
                    $error = urlencode("Invalid file type. Only PNG, JPG, and JPEG files are allowed.");
                    $this->redirect('/zone?signuperror=' . $error);
                }

                $profilePictureSize = $_FILES["profilePicture"]["size"];
                if ($profilePictureSize >= 10000000) {
                    $error = urlencode("File size too large. Maximum size allowed is 10MB.");
                    $this->redirect('/zone?signuperror=' . $error);
                }

                $existingUser = $this->userModel->getUserByEmailOrPhone($emailAddress, $contactNumber);
                if (!$existingUser) {
                    $targetDirectory = 'uploads/';
                    if (!is_dir($targetDirectory)) {
                        mkdir($targetDirectory, 0755, true);
                    }

                    $profilePicturePath = $targetDirectory . uniqid() . '.' . $profilePictureExtension;
                    if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $profilePicturePath)) {
                        $error = urlencode("Error moving uploaded files. Please try again later.");
                        $this->redirect('/zone?signuperror=' . $error);
                    }

                    $signupData = [
                        'user_id' => $userId,
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'search_name' => $searchName,
                        'contact_number' => $contactNumber,
                        'email_address' => $emailAddress,
                        'user_password' => $password,
                        'profile_picture' => $profilePicturePath
                    ];

                    if ($this->userModel->createUser($signupData)) {
                        $success = urlencode("Signup successful!");
                        $this->redirect('/zone?signupsuccess=' . $success);
                    } else {
                        $error = urlencode("We're currently experiencing some technical difficulties. Please try again later.");
                        $this->redirect('/zone?signuperror=' . $error);
                    }
                } else {
                    $error = urlencode("Email or contact number already exists.");
                    $this->redirect('/zone?signuperror=' . $error);
                }
            }
        } else {
            $this->redirect('/signup');
        }
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        $this->redirect('/');
    }

    public function zone() {
        $this->renderView('Auth/Views/zone');
    }
}
