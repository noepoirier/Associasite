DROP DATABASE if exists AssociaSite;
CREATE DATABASE AssociaSite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;;
USE AssociaSite;

DROP TABLE if exists Associations;
CREATE TABLE Associations(
  association_id INT AUTO_INCREMENT NOT NULL,
  association_name VARCHAR (50) NOT NULL,
  mail_address VARCHAR (50) NOT NULL,
  hash_password VARCHAR (255) NOT NULL,
  logo_url VARCHAR(255) DEFAULT NULL,
  slogan VARCHAR(255) DEFAULT NULL,
  page_color VARCHAR (50) DEFAULT NULL,
  background_color VARCHAR (50) DEFAULT NULL,
  PRIMARY KEY (association_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE if exists Pages;
CREATE TABLE Pages(
  page_id INT AUTO_INCREMENT NOT NULL,
  page_name VARCHAR (50) NOT NULL,
  page_order INT NOT NULL,
  association_id INT NOT NULL,
  PRIMARY KEY (page_id),
  FOREIGN KEY (association_id) REFERENCES Associations(association_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE if exists Content;
CREATE TABLE Content(
  element_id INT AUTO_INCREMENT NOT NULL,
  element_content VARCHAR (255) NOT NULL,
  element_center VARCHAR (10) DEFAULT NULL,
  element_bold VARCHAR (10) DEFAULT NULL,
  element_italic VARCHAR (10) DEFAULT NULL,
  page_id INT NOT NULL,
  PRIMARY KEY (element_id),
  FOREIGN KEY (page_id) REFERENCES Pages(page_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP USER if exists 'user'@'%';
CREATE USER 'user'@'%' IDENTIFIED BY 'user_as';
GRANT SELECT ON AssociaSite.* TO 'user'@'%';
GRANT UPDATE ON AssociaSite.* TO 'user'@'%';
GRANT EXECUTE ON AssociaSite.* TO 'user'@'%';

FLUSH PRIVILEGES;