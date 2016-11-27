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
   `basic_user`  VARCHAR(255) NOT NULL,
   `basic_key`   VARCHAR(255) NOT NULL,
   PRIMARY KEY(id)
);

CREATE TABLE api.calls
(
   id       INT(10) AUTO_INCREMENT NOT NULL,
   app_id   INT(10) NOT NULL,
   url      VARCHAR(255) NOT NULL,
   file     VARCHAR(255) NOT NULL,
   type     VARCHAR(255) NOT NULL,
   version  VARCHAR(255) NOT NULL
   PRIMARY KEY(id)
)