BEGIN;

CREATE TABLE IF NOT EXISTS login_attempt_bucket (
    bucket_key varchar(64) PRIMARY KEY,
    attempt_count smallint NOT NULL DEFAULT 0 CHECK (attempt_count >= 0),
    window_started_at timestamptz NOT NULL DEFAULT NOW(),
    blocked_until timestamptz DEFAULT NULL
);
CREATE INDEX IF NOT EXISTS idx_login_attempt_window
    ON login_attempt_bucket (window_started_at);

COMMIT;
