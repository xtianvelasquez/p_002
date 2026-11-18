-- Apply once to existing PostgreSQL databases after reviewing legacy rows.
-- Invalid appointment/availability values abort the migration with a clear error.
BEGIN;

DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM appointment_details
        WHERE appointment_time IS NULL OR appointment_time !~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'
    ) THEN
        RAISE EXCEPTION 'Cannot migrate: appointment_details contains invalid appointment_time values (expected HH:MM).';
    END IF;
    IF EXISTS (
        SELECT 1 FROM appointment_details
        WHERE appointment_status IS NOT NULL AND btrim(appointment_status) <> ''
          AND btrim(appointment_status) NOT IN ('pending', 'approved', 'denied', 'cancel')
    ) THEN
        RAISE EXCEPTION 'Cannot migrate: appointment_details contains unsupported appointment_status values.';
    END IF;
    IF EXISTS (
        SELECT 1 FROM appointment_details
        WHERE event_type IS NOT NULL AND event_type NOT IN ('quick_sync', 'consultation', 'deep_dive')
    ) OR EXISTS (
        SELECT 1 FROM appointment_details
        WHERE meeting_mode IS NOT NULL AND meeting_mode NOT IN ('google_meet', 'zoom', 'in_person', 'phone')
    ) THEN
        RAISE EXCEPTION 'Cannot migrate: appointment_details contains unsupported event_type or meeting_mode values.';
    END IF;
    IF EXISTS (
        SELECT 1 FROM user_availability
        WHERE work_start_time !~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'
           OR work_end_time !~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'
           OR work_end_time <= work_start_time
           OR slot_duration_minutes NOT IN (15, 30, 45, 60)
    ) THEN
        RAISE EXCEPTION 'Cannot migrate: user_availability contains invalid hours or slot durations.';
    END IF;
END $$;

UPDATE appointment_details
SET appointment_status = COALESCE(NULLIF(btrim(appointment_status), ''), 'pending'),
    event_type = COALESCE(event_type, 'consultation'),
    meeting_mode = COALESCE(meeting_mode, 'google_meet');

ALTER TABLE user_account ALTER COLUMN contact_number DROP NOT NULL;
ALTER TABLE user_availability
    ALTER COLUMN work_start_time TYPE varchar(5),
    ALTER COLUMN work_end_time TYPE varchar(5),
    ADD CONSTRAINT user_availability_start_time_valid CHECK (work_start_time ~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'),
    ADD CONSTRAINT user_availability_end_time_valid CHECK (work_end_time ~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'),
    ADD CONSTRAINT user_availability_hours_valid CHECK (work_end_time > work_start_time),
    ADD CONSTRAINT user_availability_duration_valid CHECK (slot_duration_minutes IN (15, 30, 45, 60));

ALTER TABLE appointment_details
    ALTER COLUMN appointment_time TYPE varchar(5),
    ALTER COLUMN appointment_time DROP DEFAULT,
    ALTER COLUMN appointment_status TYPE varchar(20),
    ALTER COLUMN appointment_status SET DEFAULT 'pending',
    ALTER COLUMN appointment_status SET NOT NULL,
    ALTER COLUMN event_type SET DEFAULT 'consultation',
    ALTER COLUMN event_type SET NOT NULL,
    ALTER COLUMN meeting_mode SET DEFAULT 'google_meet',
    ALTER COLUMN meeting_mode SET NOT NULL,
    ADD CONSTRAINT appointment_time_valid CHECK (appointment_time ~ '^(?:[01][0-9]|2[0-3]):[0-5][0-9]$'),
    ADD CONSTRAINT appointment_status_valid CHECK (appointment_status IN ('pending', 'approved', 'denied', 'cancel')),
    ADD CONSTRAINT appointment_event_type_valid CHECK (event_type IN ('quick_sync', 'consultation', 'deep_dive')),
    ADD CONSTRAINT appointment_meeting_mode_valid CHECK (meeting_mode IN ('google_meet', 'zoom', 'in_person', 'phone'));

COMMIT;
