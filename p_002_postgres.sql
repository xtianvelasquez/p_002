-- PostgreSQL Dump of One Calenday Database

DROP TABLE IF EXISTS appointment_details CASCADE;
DROP TABLE IF EXISTS user_account CASCADE;

--
-- Table structure for table user_account
--
CREATE TABLE user_account (
  user_id varchar(20) NOT NULL,
  first_name varchar(100) NOT NULL,
  middle_name varchar(100) DEFAULT NULL,
  last_name varchar(100) NOT NULL,
  search_name varchar(200) NOT NULL,
  contact_number varchar(10) NOT NULL,
  email_address varchar(100) NOT NULL,
  user_password varchar(200) NOT NULL,
  profile_picture text NOT NULL,
  last_update date DEFAULT NULL,
  PRIMARY KEY (user_id)
);

--
-- Dumping data for table user_account
--
INSERT INTO user_account (user_id, first_name, middle_name, last_name, search_name, contact_number, email_address, user_password, profile_picture, last_update) VALUES
('10BwghsSz2BD8L7Htdfd', 'Stacy', 'Smith', 'Baluyot', 'STACY BALUYOT', '9647023773', 'stacybaluyot@example.com', 'stacybaluyot', 'uploads/66d1455b0f4c7.png', NULL),
('aVpQWBEhr40pNnGFPjCK', 'Rome', 'Manlangit', 'Montecillo', 'ROME MONTECILLO', '9848282983', 'romentecillo.mantlangit@example.com', 'romeanlangit', 'uploads/66d146189067b.png', NULL),
('iwpmNfR1NzAxZ030or59', 'Mabeth', 'Rosalia', 'Ramos', 'MABETH RAMOS', '9731676318', 'rosaliaramos@example.com', 'rosaliaramos', 'uploads/66d144e10f9dc.png', NULL),
('m3MQ8peXehSvz6Ad4nrS', 'Joe', 'Maiden', 'Mateo', 'JOE MATEO', '9738383882', 'joemateo@example.com', 'joemateo', 'uploads/66d1459a3bf2a.png', NULL),
('XoW6v4698ZkoVomfMYhO', 'Amber', 'Mobito', 'Cruz', 'AMBER CRUZ', '9646367228', 'ambermobito.cruz@example.com', 'ambermobito', 'uploads/66d1451c352eb.png', NULL),
('Z4whw2o3jDnHkyQA7tIR', 'Luicito', 'Santos', 'Materan', 'LUICITO MATERAN', '9377337338', 'luisito_materan.santos@example.com', 'materanluisito', 'uploads/66d1456d8eccd8.png', NULL);

--
-- Table structure for table appointment_details
--
CREATE TABLE appointment_details (
  appointment_id varchar(60) NOT NULL,
  receiver_id varchar(20) NOT NULL,
  sender_id varchar(20) NOT NULL,
  appointment_date date NOT NULL,
  appointment_time varchar(50) NOT NULL DEFAULT '',
  event_name varchar(100) NOT NULL,
  location varchar(100) NOT NULL,
  appointment_status char(50) DEFAULT NULL,
  PRIMARY KEY (appointment_id),
  CONSTRAINT fk_receiver FOREIGN KEY (receiver_id) REFERENCES user_account (user_id) ON DELETE CASCADE,
  CONSTRAINT fk_sender FOREIGN KEY (sender_id) REFERENCES user_account (user_id) ON DELETE CASCADE
);

--
-- Dumping data for table appointment_details
--
INSERT INTO appointment_details (appointment_id, receiver_id, sender_id, appointment_date, appointment_time, event_name, location, appointment_status) VALUES
('5vCkv43OD5osWdZv7BCPHYRYWIVsjWPwPHoYvdwwjyoowQyDIdgH7doHPxdW', 'Z4whw2o3jDnHkyQA7tIR', 'XoW6v4698ZkoVomfMYhO', '2024-10-05', '11:20', 'general meeting', 'ms teams', 'approved'),
('jYV4hCsLt6LhmLkSgE7hovQfXogSQ9LofvVd8fghhBC46LS0ohqYof5W9W6E', '10BwghsSz2BD8L7Htdfd', 'XoW6v4698ZkoVomfMYhO', '2024-10-05', '11:20', 'general meeting', 'ms teams', 'cancel');

--
-- Indexes for performance
--
CREATE INDEX idx_appointment_details_receiver_id ON appointment_details (receiver_id);
CREATE INDEX idx_appointment_details_sender_id ON appointment_details (sender_id);
