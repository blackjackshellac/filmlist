CREATE DATABASE if not exists filmlist;
USE filmlist;
CREATE TABLE if not exists list (
	id		INT PRIMARY KEY AUTO_INCREMENT UNIQUE,
	userid	INT NOT NULL,
	url		TEXT NOT NULL,
	title	TEXT,
	actors	TEXT,
	year	TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
