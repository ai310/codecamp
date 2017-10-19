CREATE TABLE post(
  id int(11) NOT NULL AUTO_INCREMENT,
  user_name varchar(100) NOT NULL COLLATE utf8_general_ci,
  user_comment varchar(100) NOT NULL COLLATE utf8_general_ci,
  create_datetime datetime,
  primary key(id)
)
DELETE from post;
INSERT INTO post(id, user_name, user_comment, create_datetime) VALUES
(1, 'きむら', 'こんにちは', '2016-01-11 15:00:01'),(2, 'なかい', 'こんちには！', '2016-01-12 15:00:01'),(3,' もり',' こんばんは！！', '2016-01-12 15:00:01')