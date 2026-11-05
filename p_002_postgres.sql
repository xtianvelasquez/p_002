-- PostgreSQL Dump of More Than Blue Database (3NF Normalized)

DROP TABLE IF EXISTS appointment_details CASCADE;
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
  contact_number varchar(25) NOT NULL,
  email_address varchar(100) NOT NULL,
  user_password varchar(200) NOT NULL,
  profile_picture text NOT NULL,
  google_id varchar(100) DEFAULT NULL,
  last_update date DEFAULT NULL,
  PRIMARY KEY (user_id)
);

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
  work_start_time varchar(10) NOT NULL DEFAULT '09:00',
  work_end_time varchar(10) NOT NULL DEFAULT '17:00',
  slot_duration_minutes int NOT NULL DEFAULT 30,
  PRIMARY KEY (user_id),
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
  appointment_time varchar(50) NOT NULL DEFAULT '',
  event_name varchar(100) NOT NULL,
  location varchar(100) NOT NULL,
  event_type varchar(50) DEFAULT 'consultation',
  meeting_mode varchar(50) DEFAULT 'google_meet',
  agenda text DEFAULT NULL,
  appointment_status char(50) DEFAULT NULL,
  PRIMARY KEY (appointment_id),
  CONSTRAINT fk_receiver FOREIGN KEY (receiver_id) REFERENCES user_account (user_id) ON DELETE CASCADE,
  CONSTRAINT fk_sender FOREIGN KEY (sender_id) REFERENCES user_account (user_id) ON DELETE CASCADE
);

--
-- Dumping data for table user_account
--
INSERT INTO user_account (user_id, first_name, middle_name, last_name, search_name, contact_number, email_address, user_password, profile_picture, last_update) VALUES
('10BwghsSz2BD8L7Htdfd', 'Stacy', 'Smith', 'Baluyot', 'STACY BALUYOT', '9647023773', 'stacybaluyot@example.com', '$2y$12$Zw9g9FXk8gHWLq61b8KEUeAAc3dxsid323dFVy7Pt7CXB7eYYvXVa', 'uploads/66d1455b0f4c7.png', NULL),
('aVpQWBEhr40pNnGFPjCK', 'Rome', 'Manlangit', 'Montecillo', 'ROME MONTECILLO', '9848282983', 'romentecillo.mantlangit@example.com', '$2y$12$q5A4KuZ/bpXKhrdPc6gznu1uGd5Oj81DJ43i.h9rv8xPVYIwyZdkG', 'uploads/66d146189067b.png', NULL),
('iwpmNfR1NzAxZ030or59', 'Mabeth', 'Rosalia', 'Ramos', 'MABETH RAMOS', '9731676318', 'rosaliaramos@example.com', '$2y$12$cGdySWLzidiDJYuKvYo/2uYCTei2ZasQwtT5Dp0gHCcwE0GiMSSvK', 'uploads/66d144e10f9dc.png', NULL),
('m3MQ8peXehSvz6Ad4nrS', 'Joe', 'Maiden', 'Mateo', 'JOE MATEO', '9738383882', 'joemateo@example.com', '$2y$12$BlzUdSMoXmP5GNECKvq7a.31AfeNwwrBmIkbYGNjYcaJ506NpnLb6', 'uploads/66d1459a3bf2a.png', NULL),
('XoW6v4698ZkoVomfMYhO', 'Amber', 'Mobito', 'Cruz', 'AMBER CRUZ', '9646367228', 'ambermobito.cruz@example.com', '$2y$12$Q3rIp98ZLSiXVszQ9mQ66uePFkAUwl.96y1oud3RngVJPwe1hnrwG', 'uploads/66d1451c352eb.png', NULL),
('Z4whw2o3jDnHkyQA7tIR', 'Luicito', 'Santos', 'Materan', 'LUICITO MATERAN', '9377337338', 'luisito_materan.santos@example.com', '$2y$12$972b9KVDBiUTobyv3m63n.NOb4vpAntLeVMfY4pYzM/Iql/yfJKsu', 'uploads/66d1456d8eccd8.png', NULL);

--
-- Dumping data for table user_profile
--
INSERT INTO user_profile (user_id, headline, bio, job_title, company, location_city, skills, consultation_topics, linkedin_url, github_url, website_url) VALUES
('10BwghsSz2BD8L7Htdfd', 'Senior Full-Stack Architect | PostgreSQL & PHP Specialist | Tech Speaker', 'Passionate software architect with 8+ years building resilient web platforms and distributed scheduling engines. I love mentoring junior developers, conducting architecture design reviews, and optimizing relational databases for high-concurrency workloads.', 'Lead Software Engineer', 'More Than Blue Labs', 'Manila, Philippines', 'PHP 8, PostgreSQL, Architecture Design, REST APIs, Docker, System Optimization, Security, Mentorship', 'System Architecture Design, PostgreSQL Query Tuning, Senior Engineering Career Coaching, Code Review & Refactoring', 'https://linkedin.com/in/stacy-baluyot', 'https://github.com/stacy-baluyot', 'https://stacybaluyot.dev'),
('aVpQWBEhr40pNnGFPjCK', 'Product Design Lead & Design Systems Enthusiast', 'Crafting user-centered interfaces with attention to ergonomics, accessibility, and delightful micro-interactions.', 'Product Designer', 'Blue Studios', 'Cebu, Philippines', 'UI/UX Design, Figma, Design Systems, User Research, Prototyping', 'Design Systems, Portfolio Reviews, Usability Audits', 'https://linkedin.com/in/rome-montecillo', 'https://github.com/rome-montecillo', 'https://romemontecillo.design'),
('iwpmNfR1NzAxZ030or59', 'DevOps & Cloud Infrastructure Engineer | Kubernetes & Terraform', 'Specializing in CI/CD pipeline automation, automated failover, and cloud cost governance.', 'Cloud Engineer', 'CloudSphere', 'Makati, Philippines', 'AWS, Docker, Kubernetes, Terraform, Linux, CI/CD, Monitoring', 'Cloud Migration Strategy, Container Orchestration, CI/CD Best Practices', 'https://linkedin.com/in/mabeth-ramos', 'https://github.com/mabeth-ramos', 'https://mabethramos.io'),
('m3MQ8peXehSvz6Ad4nrS', 'Cybersecurity Analyst & Penetration Tester', 'Helping tech organizations identify vulnerabilities, prevent data leaks, and achieve security compliance.', 'Security Consultant', 'SecurePoint InfoSec', 'Taguig, Philippines', 'Application Security, OWASP, Penetration Testing, Threat Modeling, ISO 27001', 'Security Auditing, OWASP Top 10 Remediation, Threat Modeling', 'https://linkedin.com/in/joe-mateo', 'https://github.com/joe-mateo', 'https://joemateo.sec'),
('XoW6v4698ZkoVomfMYhO', 'Engineering Manager & Agile Coach', 'Guiding high-velocity engineering squads to deliver robust digital products with joyful developer experience.', 'Engineering Manager', 'Apex Innovations', 'Quezon City, Philippines', 'Agile Leadership, Scrum, OKRs, Team Mentorship, Hiring, Tech Strategy', 'Engineering Leadership, 1-on-1 Coaching, Agile Transformation', 'https://linkedin.com/in/amber-cruz', 'https://github.com/amber-cruz', 'https://ambercruz.tech'),
('Z4whw2o3jDnHkyQA7tIR', 'Data Engineering Specialist & Real-Time Analytics Architect', 'Architecting high-throughput data pipelines and OLAP databases for real-time analytics.', 'Principal Data Architect', 'Quantum Data', 'Pasig, Philippines', 'PostgreSQL, Apache Kafka, Python, ETL Pipelines, Spark, ClickHouse', 'Data Pipeline Architecture, Analytics Strategy, Big Data Scaling', 'https://linkedin.com/in/luicito-materan', 'https://github.com/luicito-materan', 'https://luicitomateran.data');

--
-- Dumping data for table user_availability
--
INSERT INTO user_availability (user_id, work_start_time, work_end_time, slot_duration_minutes) VALUES
('10BwghsSz2BD8L7Htdfd', '09:00', '17:00', 30),
('aVpQWBEhr40pNnGFPjCK', '09:00', '17:00', 30),
('iwpmNfR1NzAxZ030or59', '09:00', '17:00', 30),
('m3MQ8peXehSvz6Ad4nrS', '09:00', '17:00', 30),
('XoW6v4698ZkoVomfMYhO', '09:00', '17:00', 30),
('Z4whw2o3jDnHkyQA7tIR', '09:00', '17:00', 30);

--
-- Dumping data for table appointment_details
--
INSERT INTO appointment_details (appointment_id, receiver_id, sender_id, appointment_date, appointment_time, event_name, location, appointment_status) VALUES
('5vCkv43OD5osWdZv7BCPHYRYWIVsjWPwPHoYvdwwjyoowQyDIdgH7doHPxdW', 'Z4whw2o3jDnHkyQA7tIR', 'XoW6v4698ZkoVomfMYhO', '2024-10-05', '11:20', 'general meeting', 'ms teams', 'approved'),
('jYV4hCsLt6LhmLkSgE7hovQfXogSQ9LofvVd8fghhBC46LS0ohqYof5W9W6E', '10BwghsSz2BD8L7Htdfd', 'XoW6v4698ZkoVomfMYhO', '2024-10-05', '11:20', 'general meeting', 'ms teams', 'cancel');

--
-- Indexes for performance
--
CREATE INDEX idx_appointment_receiver_date_status ON appointment_details (receiver_id, appointment_date, appointment_status);
CREATE INDEX idx_appointment_sender_date_time ON appointment_details (sender_id, appointment_date, appointment_time);
CREATE UNIQUE INDEX idx_user_account_email ON user_account (LOWER(email_address));
CREATE UNIQUE INDEX idx_user_account_contact_number ON user_account (contact_number)
    WHERE contact_number <> '0000000000';
CREATE UNIQUE INDEX idx_user_account_google_id ON user_account (google_id) WHERE google_id IS NOT NULL;
