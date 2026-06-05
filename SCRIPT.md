# AI Agent Guide: Hybrid Architecture (Modular, MVC, RESTful API)

This guide outlines the target architecture for this project. As an AI agent working on this codebase, you are expected to follow these architectural principles, migrating the existing procedural PHP code towards a hybrid structure combining Modular Programming, Model-View-Controller (MVC) patterns, and RESTful APIs.

## 1. Architectural Overview
The system utilizes a hybrid approach:
- **Modular Programming**: Code is separated into independent, interchangeable modules based on business domains (Auth, User, Appointment).
- **MVC (Model-View-Controller)**: Each module implements MVC to separate data handling (Model), presentation/UI (View), and business logic/routing (Controller).
- **RESTful API**: Communication between the frontend (Views/JS) and backend (Controllers/Models) happens via RESTful JSON APIs or standard routed HTTP requests, enabling a decoupled design.

## 2. Directory Structure
```text
/
├── core/                # Core system files (Router, Database, Base Controller/Model)
├── config/              # Configuration files (Database settings)
├── modules/             # Business logic modules
│   ├── Auth/            # Sign up, Login, Logout modules
│   │   ├── Controllers/
│   │   ├── Models/
│   │   └── Views/
│   ├── User/            # User profile management & Search modules
│   │   ├── Controllers/
│   │   ├── Models/
│   │   └── Views/
│   └── Appointment/     # Received, Sent, Booking, Status & Deletion modules
│       ├── Controllers/
│       ├── Models/
│       └── Views/
└── public/              # Entry point (index.php), CSS, JS, images, uploads
    ├── index.php
    ├── bootstrap/
    ├── images/
    └── uploads/
```

## 3. Modular Programming Principles
- **Separation of Concerns**: Keep related functionality grouped within a specific module folder.
- **Independence**: A module should ideally be functional even if other modules are removed (or gracefully handle their absence).
- **Namespacing**: Use namespaces to avoid naming collisions (e.g., `namespace App\Modules\Auth\Controllers;`).

## 4. MVC Implementation
- **Model**: Handles database interactions (CRUD) via PDO. Extends a base Model class. Returns data to the Controller. Never contains HTML or `echo` statements directly unless formatted for JSON.
- **View**: Contains HTML/UI code. Receives data from the Controller. Minimal logic (only iteration/conditional display).
- **Controller**: Processes incoming HTTP requests, invokes Models to process data, and returns a response (either rendering a View or a JSON response).

## 5. RESTful API & CRUD Operations
When building APIs, follow RESTful conventions. Controllers should return JSON responses and use appropriate HTTP status codes.

### CRUD Example: `Appointment` Module
| Operation | HTTP Method | Endpoint (Route) | Controller Method | Description |
|---|---|---|---|---|
| **Create** | `POST` | `/appointment/book` | `book()` | Inserts a new appointment. |
| **Read (List Received)**| `GET` | `/received` | `panel()` | Retrieves received appointments list. |
| **Read (List Sent)** | `GET` | `/sent` | `sent()` | Retrieves sent appointments list. |
| **Read (Single)** | `GET` | `/appointment/view` | `view()` | Retrieves a specific appointment. |
| **Update Status** | `POST` | `/appointment/edit` | `edit()` | Updates an existing appointment status. |
| **Delete** | `POST` | `/appointment/delete` | `delete()` | Deletes/Cancels an appointment. |

### Sample Controller Implementation (PHP)
```php
<?php
namespace App\Modules\Appointment\Controllers;

use App\Core\BaseController;
use App\Modules\Appointment\Models\AppointmentModel;

class AppointmentController extends BaseController {
    private $model;

    public function __construct() {
        $this->model = new AppointmentModel();
    }

    // CREATE (POST /appointment/book)
    public function book() {
        $data = [
            'receiver_id' => $_POST['receiverId'] ?? '',
            'sender_id' => $_SESSION['userId'] ?? '',
            'appointment_date' => $_POST['appointmentDate'] ?? '',
            'appointment_time' => $_POST['appointmentTime'] ?? '',
            'event_name' => $_POST['eventName'] ?? '',
            'location' => $_POST['location'] ?? ''
        ];
        
        $id = $this->model->createAppointment($data);
        $this->redirect('/search');
    }
}
?>
```

## 6. Migration Strategy for AI Agents
When refactoring legacy files:
1. **Identify the Domain**: Determine which module the script belongs to (`Auth`, `User`, or `Appointment`).
2. **Extract Database Logic**: Move SQL queries into the respective Module's Model class. Use PDO prepared statements to prevent SQL injection.
3. **Extract Business Logic**: Move validation and processing into the Module's Controller.
4. **Transform Output**: Keep styling using the Bootstrap system in `public/bootstrap`. Route views through `public/index.php`.
5. **Update Routes**: Map the path in the Router inside `public/index.php` to target the correct Controller and action.
