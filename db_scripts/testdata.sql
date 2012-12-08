INSERT INTO privatevoting_voting (creator_user_id,title,description,start_date,private)
VALUES (1,'foo bar','lorem ipsum dolor sit amet',NOW(),false);

INSERT INTO privatevoting_answer (fk_voting,title)
VALUES (1,'igen'),(1,'nem');

INSERT INTO privatevoting_participant (fk_voting,user_id,voted)
VALUES (1,1,false),(1,2,false),(1,3,false);
