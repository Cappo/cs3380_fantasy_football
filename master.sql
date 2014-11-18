DROP SCHEMA IF EXISTS master CASCADE;

CREATE SCHEMA master;

SET search_path TO master;

-- Table: master.user_info
-- Columns:
--    league          - The league for the account, supplied during registration.
--    registration_date - The date the user registered. Set automatically.
--    description       - A user-supplied description.
CREATE TABLE user_info (
	league 				VARCHAR(100) PRIMARY KEY,
	registration_date 	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	description 		VARCHAR(500)
);

-- Table: master.authentication
-- Columns:
--    league      - The league tied to the authentication info.
--    password_hash - The hash of the user's password + salt. Expected to be SHA1.
--    salt          - The salt to use. Expected to be a SHA1 hash of a random input.
CREATE TABLE authentication (
	league 			VARCHAR(100) PRIMARY KEY,
	password_hash 	CHAR(40) NOT NULL,
	salt 			CHAR(40) NOT NULL,
	FOREIGN KEY (league) REFERENCES user_info(league)
);

-- Table: master.teams
-- Columns:
--    name      	- Team name
--    points 		- How many points the team has gotten that week. Default is 0 to start.
--    num_players   - The number of players in the team
--    turn_order    - 
CREATE TABLE teams (
	name 		VARCHAR(100) NOT NULL,
	league		VARCHAR(100) NOT NULL,
	points 		INTEGER DEFAULT 0,
	num_players INTEGER DEFAULT 0,
	turn_order	INTEGER,
	PRIMARY KEY(name),
	FOREIGN KEY(league) REFERENCES user_info(league)
);

-- Table: master.draft
-- Columns:
--    player_id     - Player ID of player that was drafted. References master player list
--    team 			- Name of team the player was drafted for. References team list.
--    league   		- The name of the league the player was drafted in. References the user_info table.
CREATE TABLE draft (
	player_id	VARCHAR(25)		NOT NULL,
	team		VARCHAR(100)	NOT NULL,
	league		VARCHAR(100)	NOT NULL,
	PRIMARY KEY (player_id,team),
	FOREIGN KEY (team) REFERENCES teams(name),
	--FOREIGN KEY (player_id) REFERENCES ,
	FOREIGN KEY (league) REFERENCES user_info(league)
);

-- Table: master.league
-- Columns:
--    name    	- Name of the league we are keeping data for.
--    team 		- The game state. 0 = Team creation, 1 = Draft, 2 = Week-by-week play.
--    turn   	- Holds the turn number that references the turn order in the teams DB.
--    week   	- The week that the league is in.
CREATE TABLE league (
	name	VARCHAR(100)	NOT NULL,
	state	INTEGER	DEFAULT 0,
	turn	INTEGER	DEFAULT 1,
	week	INTEGER	DEFAULT 1,
	FOREIGN KEY (name) REFERENCES user_info(league)
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