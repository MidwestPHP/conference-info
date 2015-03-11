CREATE DATABASE IF NOT EXISTS giveaways;

Create Table giveaways.prizes (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR (200),
  available INT(1) DEFAULT 1
);

CREATE TABLE giveaways.phonenumbers (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  number VARCHAR (200),
  prizeId INT(6),
  available INT(1) DEFAULT 1
);