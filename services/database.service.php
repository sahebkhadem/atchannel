<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/logging.service.php");

define("SERVERNAME", "localhost");
define("USRENAME", "root");
define("PASSWORD", "");
define("DATABASE", "atchan");

class DatabaseService
{
	private $log;

	public function Connect()
	{
		try {
			$conn = new mysqli(SERVERNAME, USRENAME, PASSWORD);

			if ($conn->connect_error) {
				throw new Exception("Error: " . $conn->connect_error, 0);
				die();
			}

			return $conn;
		} catch (Exception $e) {
			$this->log = new LoggingService();
			$this->log->LogToFile($e->getMessage());

			header("Location: /@chan/error.php?e=500");
		}
	}

	public function Init()
	{
		try {
			$conn = $this->Connect();

			$sql = "
			CREATE DATABASE IF NOT EXISTS atchan;
			USE atchan;
			CREATE TABLE IF NOT EXISTS users(
				id INT(16) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
				username VARCHAR(256) NOT NULL,
				password TEXT NOT NULL,
				pfp TEXT NOT NULL,
				default_pfp INT(1) NOT NULL DEFAULT 1,
				accesslevel INT(1) UNSIGNED NOT NULL DEFAULT 1,
				registered DATE NOT NULL DEFAULT CURRENT_TIMESTAMP
			);
			CREATE TABLE IF NOT EXISTS channels(
				id INT(16) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
				name VARCHAR(32) NOT NULL,
				description VARCHAR(1024) NOT NULL,
				banner TEXT NULL,
				threads INT(16) UNSIGNED NOT NULL DEFAULT 0,
				comments INT(16) UNSIGNED NOT NULL DEFAULT 0,
				creator INT(16) UNSIGNED NOT NULL,
				created DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
				FOREIGN KEY(creator) REFERENCES users(id)
			);
			CREATE TABLE IF NOT EXISTS threads(
				id VARCHAR(512) NOT NULL PRIMARY KEY,
				title VARCHAR(512) NOT NULL,
				content VARCHAR(1024) NULL,
				comments INT(16) UNSIGNED NOT NULL DEFAULT 0,
				creator INT(16) UNSIGNED NOT NULL,
				channel INT(16) UNSIGNED,
				created DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
				FOREIGN KEY(creator) REFERENCES users(id),
				FOREIGN KEY(channel) REFERENCES channels(id)
			);
			CREATE TABLE IF NOT EXISTS comments(
				id INT(16) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
				comment TEXT NOT NULL,
				channel INT(16) UNSIGNED NOT NULL,
				thread VARCHAR(512) NOT NULL,
				user INT(16) UNSIGNED NOT NULL,
				written DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
				FOREIGN KEY(channel) REFERENCES channels(id),
				FOREIGN KEY(thread) REFERENCES threads(id),
				FOREIGN KEY(user) REFERENCES users(id)
			);
			";

			if ($conn->multi_query($sql) === false) {
				throw new Exception("Error: " . $conn->error, 1);
				$conn->close();
			}
		} catch (Exception $e) {
			$this->log = new LoggingService();
			$this->log->LogToFile($e->getMessage());

			header("Location: /@chan/error.php?e=500");
		}
	}
}
