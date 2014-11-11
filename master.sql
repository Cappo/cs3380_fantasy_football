DROP TABLE IF EXISTS master.authentication;
DROP TABLE IF EXISTS master.user_info;

DROP SCHEMA IF EXISTS master;

CREATE SCHEMA master;

-- Table: master.user_info
-- Columns:
--    league          - The league for the account, supplied during registration.
--    registration_date - The date the user registered. Set automatically.
--    description       - A user-supplied description.
CREATE TABLE master.user_info (
	league 		VARCHAR(30) PRIMARY KEY,
	registration_date 	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	description 		VARCHAR(500)
);

-- Table: master.authentication
-- Columns:
--    league      - The league tied to the authentication info.
--    password_hash - The hash of the user's password + salt. Expected to be SHA1.
--    salt          - The salt to use. Expected to be a SHA1 hash of a random input.
CREATE TABLE master.authentication (
	league 	VARCHAR(30) PRIMARY KEY,
	password_hash 	CHAR(40) NOT NULL,
	salt 		CHAR(40) NOT NULL,
	FOREIGN KEY (league) REFERENCES master.user_info(league)
);

-- Table: master.log
-- Columns:
--    log_id     - A unique ID for the log entry. Set by a sequence.
--    league   - The user whose action generated this log entry.
--    ip_address - The IP address of the user at the time the log was entered.
--    log_date   - The date of the log entry. Set automatically by a default value.
--    action     - What the user did to generate a log entry (i.e., "logged in").
--CREATE TABLE master.log (
--	log_id  	SERIAL PRIMARY KEY,
--	league 	VARCHAR(30) NOT NULL REFERENCES master.user_info,
--	ip_address 	VARCHAR(15) NOT NULL,
--	log_date 	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--	action 		VARCHAR(50) NOT NULL
--);