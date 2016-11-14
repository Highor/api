CREATE DATABASE api;

CREATE TABLE api.patches
(
   id     INT(10) AUTO_INCREMENT NOT NULL,
   name   VARCHAR(255) NOT NULL,
   PRIMARY KEY(id)
);

INSERT INTO api.patches (name) VALUES ('patches/sql/v1__createDatabase.sql');

CREATE TABLE api.login
(
   id         INT(10) AUTO_INCREMENT NOT NULL,
   username   VARCHAR(255) NOT NULL,
   password   VARCHAR(255) NOT NULL,
   hash       VARCHAR(255),
   PRIMARY KEY(id)
);

INSERT INTO api.login (username, password) VALUES ('{username}', '{password}');

CREATE TABLE api.apps
(
   id      INT(10) AUTO_INCREMENT NOT NULL,
   name    VARCHAR(255) NOT NULL,
   `key`   VARCHAR(255) NOT NULL,
   PRIMARY KEY(id)
);

