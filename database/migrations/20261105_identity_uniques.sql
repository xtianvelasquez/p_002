-- Apply once to existing PostgreSQL databases. The transaction preserves the
-- existing unique indexes if a replacement cannot be built.
BEGIN;

DO $$
BEGIN
    IF EXISTS (SELECT 1 FROM user_account GROUP BY LOWER(email_address) HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Cannot add case-insensitive email uniqueness: duplicate email addresses exist.';
    END IF;
    IF EXISTS (
        SELECT 1 FROM user_account
        WHERE contact_number IS NOT NULL AND contact_number <> '0000000000'
        GROUP BY contact_number HAVING COUNT(*) > 1
    ) THEN
        RAISE EXCEPTION 'Cannot add contact uniqueness: duplicate contact numbers exist.';
    END IF;
    IF EXISTS (SELECT 1 FROM user_account WHERE google_id IS NOT NULL GROUP BY google_id HAVING COUNT(*) > 1) THEN
        RAISE EXCEPTION 'Cannot add Google ID uniqueness: duplicate Google IDs exist.';
    END IF;
END $$;

CREATE UNIQUE INDEX idx_user_account_email_v2 ON user_account (LOWER(email_address));
DROP INDEX IF EXISTS idx_user_account_email;
ALTER INDEX idx_user_account_email_v2 RENAME TO idx_user_account_email;

CREATE UNIQUE INDEX idx_user_account_contact_number_v2 ON user_account (contact_number)
    WHERE contact_number IS NOT NULL AND contact_number <> '0000000000';
DROP INDEX IF EXISTS idx_user_account_contact_number;
ALTER INDEX idx_user_account_contact_number_v2 RENAME TO idx_user_account_contact_number;

CREATE UNIQUE INDEX idx_user_account_google_id_v2 ON user_account (google_id)
    WHERE google_id IS NOT NULL;
DROP INDEX IF EXISTS idx_user_account_google_id;
ALTER INDEX idx_user_account_google_id_v2 RENAME TO idx_user_account_google_id;

CREATE INDEX IF NOT EXISTS idx_appointment_receiver_date_status
    ON appointment_details (receiver_id, appointment_date, appointment_status);
CREATE INDEX IF NOT EXISTS idx_appointment_sender_date_time
    ON appointment_details (sender_id, appointment_date, appointment_time);

DROP INDEX IF EXISTS idx_appointment_details_receiver_id;
DROP INDEX IF EXISTS idx_appointment_details_sender_id;
DROP INDEX IF EXISTS idx_user_profile_company;
DROP INDEX IF EXISTS idx_user_profile_job_title;

COMMIT;
