<?php
namespace App\Modules\Auth\Controllers;

use App\Core\BaseController;
use App\Modules\Auth\Models\UserModel;
use Exception;

class AuthController extends BaseController {
    /**
     * @var UserModel
     */
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
            $emailAddress = strtolower(trim(scalarString($_POST['emailAddress'] ?? '')));
            $password = scalarString($_POST['password'] ?? '');

            if (strlen($password) > 72) {
                $this->setFlash('danger', 'The email or password you entered is incorrect. Please try again.');
                $this->redirect('/login');
            }

            $user = $this->userModel->getUserByEmailAndPassword($emailAddress, $password);
            if ($user) {
                session_regenerate_id(true);
                unset($_SESSION['_csrf']);
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
            $firstName = trim(scalarString($_POST['firstName'] ?? ''));
            $middleName = trim(scalarString($_POST['middleName'] ?? ''));
            $lastName = trim(scalarString($_POST['lastName'] ?? ''));
            $searchName = strtoupper(trim($firstName . " " . $lastName));
            $rawContact = trim(scalarString($_POST['contactNumber'] ?? ''));
            $emailAddress = strtolower(trim(scalarString($_POST['emailAddress'] ?? '')));
            $password = scalarString($_POST['password'] ?? '');

            if (empty($firstName) || empty($lastName) || strlen($firstName) > 100 || strlen($lastName) > 100 || strlen($middleName) > 100) {
                $this->setFlash('danger', 'First name and last name are required.');
                $this->redirect('/signup');
            }

            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('danger', 'Invalid email address provided.');
                $this->redirect('/signup');
            }
            if (strlen($emailAddress) > 100) {
                $this->setFlash('danger', 'Email address is too long.');
                $this->redirect('/signup');
            }

            if (strlen($password) < 12 || strlen($password) > 72) {
                $this->setFlash('danger', 'Password must be between 12 and 72 bytes.');
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
            $uploadedProfilePath = null;

            if (isset($_FILES["profilePicture"]) && $_FILES["profilePicture"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $profilePictureError = $_FILES["profilePicture"]["error"];
                if ($profilePictureError !== UPLOAD_ERR_OK) {
                    $this->setFlash('danger', 'Error uploading profile image. Please try again.');
                    $this->redirect('/signup');
                }

                $profilePictureSize = $_FILES["profilePicture"]["size"];
                if ($profilePictureSize > 10000000) {
                    $this->setFlash('danger', 'File size exceeds 10MB limit.');
                    $this->redirect('/signup');
                }

                $ext = validateUploadedImage($_FILES["profilePicture"]["tmp_name"]);
                if (!$ext) {
                    $this->setFlash('danger', 'Invalid file type. Only PNG, JPG, and JPEG images are allowed.');
                    $this->redirect('/signup');
                }

                $publicUploadsDir = dirname(__DIR__, 3) . '/public/uploads';
                if (!is_dir($publicUploadsDir) && !mkdir($publicUploadsDir, 0755, true) && !is_dir($publicUploadsDir)) {
                    $this->setFlash('danger', 'Image uploads are temporarily unavailable.');
                    $this->redirect('/signup');
                }

                $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
                $targetPath = $publicUploadsDir . '/' . $fileName;

                if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetPath)) {
                    $this->setFlash('danger', 'Failed to store uploaded profile picture.');
                    $this->redirect('/signup');
                }

                $profilePictureRelativePath = 'uploads/' . $fileName;
                $uploadedProfilePath = $targetPath;
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
                    if ($uploadedProfilePath && is_file($uploadedProfilePath)) unlink($uploadedProfilePath);
                    $this->setFlash('danger', 'An error occurred during registration. Please try again.');
                    $this->redirect('/signup');
                }
            } catch (Exception $e) {
                if ($uploadedProfilePath && is_file($uploadedProfilePath)) unlink($uploadedProfilePath);
                error_log((string)$e);
                $this->setFlash('danger', 'Registration could not be completed. Please try again.');
                $this->redirect('/signup');
            }
        } else {
            $this->redirect('/signup');
        }
    }

    public function logout() {
        $this->validateCsrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $cookie = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $cookie['path'],
                'domain' => $cookie['domain'],
                'secure' => $cookie['secure'],
                'httponly' => $cookie['httponly'],
                'samesite' => $cookie['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
        $this->redirect('/login');
    }

    public function getGoogleCredentials() {
        $config = require dirname(__DIR__, 3) . '/config/config.php';
        return [
            'client_id' => $config['google_client_id'] ?? getenv('GOOGLE_CLIENT_ID') ?: ($_ENV['GOOGLE_CLIENT_ID'] ?? ''),
            'client_secret' => $config['google_client_secret'] ?? getenv('GOOGLE_CLIENT_SECRET') ?: ($_ENV['GOOGLE_CLIENT_SECRET'] ?? ''),
            'redirect_uri' => $this->getGoogleRedirectUri()
        ];
    }

    public function getGoogleRedirectUri() {
        $config = require dirname(__DIR__, 3) . '/config/config.php';
        if (!empty($config['google_redirect_uri'])) {
            return trim($config['google_redirect_uri']);
        }
        $envUri = getenv('GOOGLE_REDIRECT_URI') ?: ($_ENV['GOOGLE_REDIRECT_URI'] ?? ($_SERVER['GOOGLE_REDIRECT_URI'] ?? ''));
        if (!empty($envUri)) {
            return trim($envUri);
        }
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $port = (int)($_SERVER['SERVER_PORT'] ?? ($scheme === 'https' ? 443 : 80));
        if (!in_array($port, [80, 443], true)) $host .= ':' . $port;
        return $scheme . '://' . $host . routeUrl('/auth/google/callback');
    }

    public function googleRedirect() {
        if (isset($_SESSION["userId"])) {
            $this->redirect('/received');
        }

        $creds = $this->getGoogleCredentials();
        if (empty($creds['client_id'])) {
            $this->setFlash('danger', 'Google OAuth client ID is not configured.');
            $this->redirect('/login');
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = [
            'client_id' => $creds['client_id'],
            'redirect_uri' => $creds['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
            'access_type' => 'online'
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    public function googleCallback() {
        if (isset($_SESSION["userId"])) {
            $this->redirect('/received');
        }

        $code = trim(scalarString($_GET['code'] ?? ''));
        $state = trim(scalarString($_GET['state'] ?? ''));
        $savedState = $_SESSION['oauth_state'] ?? '';
        unset($_SESSION['oauth_state']);

        if (empty($code)) {
            $this->setFlash('danger', 'Google sign-in was cancelled or could not be completed.');
            $this->redirect('/login');
        }

        if (empty($state) || empty($savedState) || !hash_equals($savedState, $state)) {
            $this->setFlash('danger', 'Invalid OAuth state verification. Please try again.');
            $this->redirect('/login');
        }

        $creds = $this->getGoogleCredentials();
        $redirectUri = $creds['redirect_uri'];

        // 1. Exchange authorization code for access token
        $tokenEndpoint = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => $creds['client_id'],
            'client_secret' => $creds['client_secret'],
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $tokenResponse = $this->httpRequest($tokenEndpoint, 'POST', $postData);
        if (!$tokenResponse || empty($tokenResponse['access_token'])) {
            $errorMsg = $tokenResponse['error_description'] ?? $tokenResponse['error'] ?? 'Could not retrieve access token from Google.';
            if (isset($tokenResponse['error']) && $tokenResponse['error'] === 'redirect_uri_mismatch') {
                $errorMsg = 'Redirect URI mismatch. Google received: ' . $redirectUri . '. Please ensure this exact URI is added to your Authorized Redirect URIs in the Google Cloud Console.';
            }
            error_log('Google OAuth token exchange failed: ' . $errorMsg);
            $this->setFlash('danger', 'Google sign-in could not be completed. Please try again.');
            $this->redirect('/login');
        }

        $accessToken = $tokenResponse['access_token'];

        // 2. Fetch User Profile
        $userInfoEndpoint = 'https://www.googleapis.com/oauth2/v3/userinfo';
        $userInfo = $this->httpRequest($userInfoEndpoint, 'GET', [], [
            'Authorization: Bearer ' . $accessToken
        ]);

        if (!$userInfo || empty($userInfo['email'])) {
            $infoError = $userInfo['error_description'] ?? $userInfo['error'] ?? 'Could not retrieve user profile from Google.';
            error_log('Google OAuth profile request failed: ' . $infoError);
            $this->setFlash('danger', 'Google sign-in could not be completed. Please try again.');
            $this->redirect('/login');
        }

        if (empty($userInfo['email_verified']) || empty($userInfo['sub'])) {
            $this->setFlash('danger', 'Google did not return a verified account. Please try another sign-in method.');
            $this->redirect('/login');
        }

        $googleId = (string)($userInfo['sub'] ?? '');
        $email = strtolower(trim(scalarString($userInfo['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
            $this->setFlash('danger', 'Google did not return a valid email address.');
            $this->redirect('/login');
        }
        $firstName = trim($userInfo['given_name'] ?? '');
        $lastName = trim($userInfo['family_name'] ?? '');
        $fullName = trim($userInfo['name'] ?? '');
        $picture = $userInfo['picture'] ?? 'images/logo.png';

        if (empty($firstName)) {
            if (!empty($fullName)) {
                $parts = explode(' ', $fullName, 2);
                $firstName = $parts[0];
                $lastName = $parts[1] ?? 'User';
            } else {
                $firstName = 'Google';
                $lastName = 'User';
            }
        }
        if (empty($lastName)) {
            $lastName = 'User';
        }

        // 3. Check existing user by google_id
        $user = $this->userModel->getUserByGoogleId($googleId);

        // 4. If not found by google_id, check by email to link account
        if (!$user) {
            $user = $this->userModel->getUserByEmail($email);
            if ($user) {
                $this->userModel->linkGoogleId($user['user_id'], $googleId);
            }
        }

        // 5. If user still doesn't exist, create new account
        if (!$user) {
            $userId = $this->generateUserId();
            $searchName = strtoupper(trim($firstName . ' ' . $lastName));
            
            $newUserData = [
                'user_id' => $userId,
                'first_name' => $firstName,
                'middle_name' => null,
                'last_name' => $lastName,
                'search_name' => $searchName,
                'contact_number' => '999' . str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
                'email_address' => $email,
                'user_password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                'profile_picture' => $picture,
                'work_start_time' => '09:00',
                'work_end_time' => '17:00',
                'slot_duration_minutes' => 30,
                'google_id' => $googleId
            ];

            try {
                if ($this->userModel->createGoogleUser($newUserData)) {
                    $user = $this->userModel->getUserById($userId);
                } else {
                    $this->setFlash('danger', 'Unable to create user account from Google profile.');
                    $this->redirect('/login');
                }
            } catch (Exception $e) {
                error_log((string)$e);
                $this->setFlash('danger', 'Google sign-in could not be completed. Please try again.');
                $this->redirect('/login');
            }
        }

        // 6. Sign in user
        session_regenerate_id(true);
        unset($_SESSION['_csrf']);
        $_SESSION["profilePicture"] = $user["profile_picture"];
        $_SESSION["searchName"] = $user["search_name"];
        $_SESSION["emailAddress"] = $user["email_address"];
        $_SESSION["userId"] = $user["user_id"];

        $this->setFlash('success', 'Successfully signed in with Google! Welcome, ' . htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8') . '.');
        $this->redirect('/received');
    }

    private function httpRequest($url, $method = 'GET', $data = [], $headers = []) {
        if (extension_loaded('curl')) {
            $ch = curl_init();
            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            } else {
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
                curl_setopt($ch, CURLOPT_URL, $url);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            $statusCode = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);
            if ($response === false) return ['error' => 'request_failed', 'error_description' => $curlError];
            $decoded = json_decode($response, true);
            return is_array($decoded) && $statusCode >= 200 && $statusCode < 300
                ? $decoded
                : ['error' => 'request_failed'];
        }

        $opts = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'timeout' => 15,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ];
        if ($method === 'POST') {
            $opts['http']['header'] .= "\r\nContent-Type: application/x-www-form-urlencoded";
            $opts['http']['content'] = http_build_query($data);
        }
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);
        return json_decode((string)$response, true) ?: ['error' => 'stream_error', 'error_description' => (string)$response];
    }
}
