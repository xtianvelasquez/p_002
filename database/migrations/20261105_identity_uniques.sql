-- Apply once to existing databases after checking for duplicate email,
-- contact_number, or non-null google_id values.
DROP INDEX IF EXISTS idx_user_account_email;
CREATE UNIQUE INDEX idx_user_account_email ON user_account (LOWER(email_address));

CREATE UNIQUE INDEX IF NOT EXISTS idx_user_account_contact_number
    ON user_account (contact_number)
    WHERE contact_number <> '0000000000';

DROP INDEX IF EXISTS idx_user_account_google_id;
CREATE UNIQUE INDEX idx_user_account_google_id
    ON user_account (google_id)
    WHERE google_id IS NOT NULL;

CREATE INDEX IF NOT EXISTS idx_appointment_receiver_date_status
    ON appointment_details (receiver_id, appointment_date, appointment_status);
CREATE INDEX IF NOT EXISTS idx_appointment_sender_date_time
    ON appointment_details (sender_id, appointment_date, appointment_time);

DROP INDEX IF EXISTS idx_appointment_details_receiver_id;
DROP INDEX IF EXISTS idx_appointment_details_sender_id;
DROP INDEX IF EXISTS idx_user_profile_company;
DROP INDEX IF EXISTS idx_user_profile_job_title;
