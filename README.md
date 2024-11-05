# P_002: One Calenday Appointment System

## Description

One Calenday is an appointment system that enables users to schedule appointments on their preferred dates and times. Users can provide the event name and location during the booking process. A login system is implemented for the user panel, allowing users to view all individuals who have booked their time, along with the event name and location. The system also includes a "Sent" panel that displays all the appointments the user has sent to others.

The system incorporates a registration and authentication process to enable users to access their appointment details. Upon logging into their account, users can view their scheduled appointments, including the name of the user who booked the appointment, the date, event name, and location. Users can utilize the search bar to find the account of the person with whom they wish to book an appointment. If the account is found, users can then schedule an appointment by specifying the desired date and time of appointment.

**Features:**
- **User Interface:** Built with HTML, CSS, and Bootstrap v5.3 for a responsive, minimalist, and interactive design.
- **Backend:** Powered by PHP for handling booking logic and data management.
- **Development Environment:** XAMPP and Visual Studio Code for PHP development.
- **Database Management:** HeidiSQL for database management and administration.

## Table of Contents

1. [Installation](#installation)
2. [Functionalities](#functionalities)
3. [Limitation](#limitation)
4. [Project Structure](#project-structure)
5. [Author](#author)
6. [Acknowledgements](#acknowledgements)

## Installation

To set up the One Calenday system on your local machine, follow these steps:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/xtianvelasquez/p_002.git
   ```

2. **Navigate to the Project Directory:**
   ```bash
   cd p_002
   ```

3. **Set Up XAMPP:**
   - Ensure that XAMPP is installed and running on your local machine.
   - Copy the project files into the `htdocs` directory of your XAMPP installation.

4. **Import the Database:**
   - Open HeidiSQL and create a new database for the project.
   - Import the `.sql` file included in the project directory to set up the database schema.

5. **Configure the Database Connection:**
   - Update the database connection settings in the `config.php` file to match your local database credentials.

6. **Start XAMPP:**
   - Ensure Apache and MySQL services are running in XAMPP.

7. **Access the Application:**
   - Open a web browser and navigate to `http://localhost/index.php` to view the application.

## Functionalities:

1. **Signup and Login**
- Users must create an account to access the system's features. Once registered, users can log in to their panel to view and manage their appointments.

2. **Appointment Booking**
- Users can search for another user's account and schedule an appointment by selecting a preferred date and specifying the time of appointment in the search bar in the navigation.
- The received bar in the navigation bar displays all booked appointments, including details such as the name of the user who booked the appointment, the date, event name or description, the location, and many more that is relevant to the person who made the appointment.
- Users can view the appointments they have requested with other users in the "Sent" panel, with the option to delete those appointments.

3 **Appointment Management**
- The system includes an action button within the appointment details, allowing users to view detailed information about the person booking the appointment, edit the appointment status (approved, denied, canceled), or delete the appointment.

## Limitation:

- The system only supports one-day schedules for specific times, which is why it's called "One Calenday." The functionality allows users to determine whether a date and time are already booked, but other users can still book the same day. Users have the freedom to cancel or deny appointments based on their schedules.

## Project Structure

- **`/bootstrap`**: Downloaded Bootstrap v5.3 files.
- **`/images`**: Includes the system's logo.
- **`/uploads`**: The path for the user's profile picture.
- **`/sql`**: Contains the `.sql` file for database setup.

## Author

- [@xtianvelasquez](https://github.com/xtianvelasquez)

## Acknowledgements

- **Bootstrap v5.3**: For styling and responsive design.
- **PHP**: For backend development.
- **XAMPP**: For local server setup.
- **HeidiSQL**: For database management.
- **Visual Studio Code**: For development.
