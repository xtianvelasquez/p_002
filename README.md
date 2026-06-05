# One Calenday Appointment System

## Description

One Calenday is an appointment system that enables users to schedule appointments on their preferred dates and times. Users can provide the event name and location during the booking process. A login system is implemented for the user panel, allowing users to view all individuals who have booked their time, along with the event name and location. The system also includes a "Sent" panel that displays all the appointments the user has sent to others.

The system incorporates a registration and authentication process to enable users to access their appointment details. Upon logging into their account, users can view their scheduled appointments, including the name of the user who booked the appointment, the date, event name, and location. Users can utilize the search bar to find the account of the person with whom they wish to book an appointment. If the account is found, users can then schedule an appointment by specifying the desired date and time of appointment.

**Features:**
- **User Interface:** Built with HTML, CSS, and Bootstrap v5.3 for a responsive, minimalist, and interactive design.
- **Backend:** Decoupled Modular MVC framework written in PHP with a custom URL Router.
- **Database:** PostgreSQL for robust and secure data handling using PDO prepared statements.

---

## Installation & Setup

To set up the One Calenday system on your local machine, follow these steps:

### 1. Clone the Repository:
```bash
git clone https://github.com/xtianvelasquez/p_002.git
cd p_002
```

### 2. Configure the Database Connection:
Ensure PostgreSQL is running on port `5433` (or update it in `config/config.php` to your local port). The database credentials are configured in [config/config.php](file:///c:/p_002/config/config.php):
```php
return [
    'host' => 'localhost',
    'port' => '5433', // PostgreSQL port
    'dbname' => 'calenday',
    'user' => 'postgres',
    'password' => 'admin123'
];
```

### 3. Create and Import the PostgreSQL Database:
1. Create a database named `calenday` in your PostgreSQL instance.
2. Import the [p_002_postgres.sql](file:///c:/p_002/p_002_postgres.sql) schema and data dump file.
   - Using command line:
     ```bash
     psql -h localhost -p 5433 -U postgres -d calenday -f p_002_postgres.sql
     ```
   - Alternatively, copy-paste the SQL contents from `p_002_postgres.sql` and run them inside pgAdmin, DBeaver, or your database management utility.

### 4. Start the Local Server:
Navigate to the `public` directory and start a local PHP development server:
```powershell
cd public
php -S localhost:8000
```

### 5. Access the Application:
Open a web browser and navigate to `http://localhost:8000` to view the application.

---

## Functionalities

1. **Signup and Login**
   - Users must create an account to access the system's features. Once registered, users can log in to their panel to view and manage their appointments.

2. **Appointment Booking**
   - Users can search for another user's account and schedule an appointment by selecting a preferred date and specifying the time of appointment in the search bar.
   - The received bar displays all booked appointments, including details such as the name of the user who booked the appointment, the date, event name/description, and location.
   - Users can view the appointments they have requested with other users in the "Sent" panel, with the option to delete those appointments.

3. **Appointment Management**
   - An action button within the appointment details allows users to view detailed info, edit the appointment status (approved, denied, canceled), or delete the appointment.

---

## Limitations

- The system only supports one-day schedules for specific times. The functionality allows users to determine whether a date and time are already booked, but other users can still book the same day. Users can cancel or deny appointments based on their schedules.

---

## Project Structure

- **`/core`**: Router, Database interface, Base Controller, and Model.
- **`/config`**: Configuration settings (e.g. database credentials).
- **`/modules`**: Separated domain modules (**Auth**, **User**, **Appointment**).
- **`/public`**: Application entry point (`index.php`), Bootstrap resources, images, and user profile picture uploads.
