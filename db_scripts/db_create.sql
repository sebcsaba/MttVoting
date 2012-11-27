CREATE TABLE voting (
    id INT PRIMARY KEY NOT NULL auto_increment,
    creator_user_id INT NOT NULL,
    title VARCHAR NOT NULL,
    description VARCHAR NOT NULL,
    start_date DATE NOT NULL,
    stop_date DATE,
    private BOOL NOT NULL
);

CREATE TABLE answer (
    id INT PRIMARY KEY NOT NULL auto_increment,
    fk_voting INT NOT NULL REFERENCES voting(id),
    title VARCHAR NOT NULL
);

CREATE TABLE participant (
    id INT PRIMARY KEY NOT NULL auto_increment,
    fk_voting INT NOT NULL REFERENCES voting(id),
    user_id INT NOT NULL,
    voted BOOL NOT NULL
);

CREATE TABLE vote (
    id INT PRIMARY KEY NOT NULL auto_increment,
    fk_voting INT NOT NULL REFERENCES voting(id),
    fk_answer INT NOT NULL REFERENCES answer(id),
    fk_participant INT REFERENCES participant(id)
);
/**
 * One given vote. There are some contraint that MUST be satisfied:
 * - If the referenced voting is private, the fk_participant MUST be null.
 * - The voting of the referenced answer, the voting of the referenced participant and the referenced voting MUST be the same.
 */
