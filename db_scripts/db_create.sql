CREATE TABLE privatevoting_voting (
	id INT PRIMARY KEY NOT NULL auto_increment,
	creator_user_id INT NOT NULL,
	title VARCHAR(8000) NOT NULL,
	description TEXT NOT NULL,
	start_date DATETIME NOT NULL,
	stop_date DATETIME,
	private BOOL NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE privatevoting_answer (
	id INT PRIMARY KEY NOT NULL auto_increment,
	fk_voting INT NOT NULL REFERENCES voting(id),
	title VARCHAR(8000) NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE privatevoting_participant (
	id INT PRIMARY KEY NOT NULL auto_increment,
	fk_voting INT NOT NULL REFERENCES voting(id),
	user_id INT NOT NULL,
	voted BOOL NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE privatevoting_vote (
	id INT PRIMARY KEY NOT NULL auto_increment,
	fk_voting INT NOT NULL REFERENCES voting(id),
	fk_answer INT NOT NULL REFERENCES answer(id),
	fk_participant INT REFERENCES participant(id)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- One given vote. There are some contraint that MUST be satisfied:
-- - If the referenced voting is private, the fk_participant MUST be null.
-- - The voting of the referenced answer, the voting of the referenced participant and the referenced voting MUST be the same.
