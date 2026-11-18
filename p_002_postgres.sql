-- PostgreSQL schema for More Than Blue (3NF normalized; intentionally no sample rows)

DROP TABLE IF EXISTS appointment_details CASCADE;
DROP TABLE IF EXISTS login_attempt_bucket CASCADE;
DROP TABLE IF EXISTS user_profile CASCADE;
DROP TABLE IF EXISTS user_availability CASCADE;
DROP TABLE IF EXISTS user_account CASCADE;

--
-- Table structure for table user_account (Core Auth & Identity)
--
CREATE TABLE user_account (
  user_id varchar(50) NOT NULL,
  first_name varchar(100) NOT NULL,
  middle_name varchar(100) DEFAULT NULL,
  last_name varchar(100) NOT NULL,
  search_name varchar(200) NOT NULL,
  contact_number varchar(25) DEFAULT NULL,
  email_address varchar(100) NOT NULL,
  user_password varchar(200) NOT NULL,
  profile_picture text NOT NULL,
  google_id varchar(100) DEFAULT NULL,
  last_update date DEFAULT NULL,
  PRIMARY KEY (user_id)
);

CREATE TABLE login_attempt_bucket (
  bucket_key varchar(64) NOT NULL PRIMARY KEY,
  attempt_count smallint NOT NULL DEFAULT 0 CHECK (attempt_count >= 0),
  window_started_at timestamptz NOT NULL DEFAULT NOW(),
  blocked_until timestamptz DEFAULT NULL
);
CREATE INDEX idx_login_attempt_window ON login_attempt_bucket (window_started_at);

--
-- Table structure for table user_profile (LinkedIn Dossier & Professional Details)
--
CREATE TABLE user_profile (
  user_id varchar(50) NOT NULL,
  headline varchar(200) DEFAULT NULL,
  bio text DEFAULT NULL,
  job_title varchar(150) DEFAULT NULL,
  company varchar(150) DEFAULT NULL,
  location_city varchar(100) DEFAULT NULL,
  skills text DEFAULT NULL,
  consultation_topics text DEFAULT NULL,
  linkedin_url varchar(255) DEFAULT NULL,
  github_url varchar(255) DEFAULT NULL,
  website_url varchar(255) DEFAULT NULL,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_user_profile FOREIGN KEY (user_id) REFERENCES user_account (user_id) ON DELETE CASCADE
);

--
-- Table structure for table user_availability (Working Hours & Slot Intervals)
--
CREATE TABLE user_availability (
  user_id varchar(50) NOT NULL,
  work_start_time varchar(5) NOT NULL DEFAULT '09:00' CHECK (work_start_time ~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'),
  work_end_time varchar(5) NOT NULL DEFAULT '17:00' CHECK (work_end_time ~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'),
  slot_duration_minutes int NOT NULL DEFAULT 30 CHECK (slot_duration_minutes IN (15, 30, 45, 60)),
  PRIMARY KEY (user_id),
  CONSTRAINT user_availability_hours_valid CHECK (work_end_time > work_start_time),
  CONSTRAINT fk_user_availability FOREIGN KEY (user_id) REFERENCES user_account (user_id) ON DELETE CASCADE
);

--
-- Table structure for table appointment_details (Bookings & Meetings)
--
CREATE TABLE appointment_details (
  appointment_id varchar(60) NOT NULL,
  receiver_id varchar(50) NOT NULL,
  sender_id varchar(50) NOT NULL,
  appointment_date date NOT NULL,
  appointment_time varchar(5) NOT NULL CHECK (appointment_time ~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'),
  event_name varchar(100) NOT NULL,
  location varchar(100) NOT NULL,
  event_type varchar(50) NOT NULL DEFAULT 'consultation' CHECK (event_type IN ('quick_sync', 'consultation', 'deep_dive')),
  meeting_mode varchar(50) NOT NULL DEFAULT 'google_meet' CHECK (meeting_mode IN ('google_meet', 'zoom', 'in_person', 'phone')),
  agenda text DEFAULT NULL,
  appointment_status varchar(20) NOT NULL DEFAULT 'pending' CHECK (appointment_status IN ('pending', 'approved', 'denied', 'cancel')),
  PRIMARY KEY (appointment_id),
  CONSTRAINT fk_receiver FOREIGN KEY (receiver_id) REFERENCES user_account (user_id) ON DELETE CASCADE,
  CONSTRAINT fk_sender FOREIGN KEY (sender_id) REFERENCES user_account (user_id) ON DELETE CASCADE
);

--
-- Indexes for performance
--
CREATE INDEX idx_appointment_receiver_date_status ON appointment_details (receiver_id, appointment_date, appointment_status);
CREATE INDEX idx_appointment_sender_date_time ON appointment_details (sender_id, appointment_date, appointment_time);
CREATE UNIQUE INDEX idx_user_account_email ON user_account (LOWER(email_address));
CREATE UNIQUE INDEX idx_user_account_contact_number ON user_account (contact_number)
    WHERE contact_number <> '0000000000';
CREATE UNIQUE INDEX idx_user_account_google_id ON user_account (google_id) WHERE google_id IS NOT NULL;
