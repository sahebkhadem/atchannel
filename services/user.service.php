<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/database.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/logging.service.php");

class UserService
{
	private $log;

	public function Register($username, $password, $pfp_path)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$hashed_password = password_hash($password, PASSWORD_DEFAULT);

		$default_pfp = 0;
		if ($pfp_path === "") {
			$default_pfp = 1;
			$default_pfps = array(
				"default-pfps/chad.png",
				"default-pfps/gigachad_0.jpg",
				"default-pfps/gigachad_1.jpg",
				"default-pfps/mike.jpg",
				"default-pfps/soyjak.png"
			);
			$random_index = rand(0, 4);
			$pfp_path = $default_pfps[$random_index];
		}

		$sql = "SELECT * FROM atchan.users WHERE username = '" . $username . "';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows === 0) {
				$sql = "INSERT INTO atchan.users (username, password, pfp, default_pfp, accesslevel) VALUES ('" . $username . "', '" . $hashed_password . "', '" . $pfp_path . "', '" . $default_pfp . "', 1);";
				if ($conn->query($sql) === true) return true;
				else throw new Exception("Couldn't register.", 0);
			} else throw new Exception("Username already in use.", 1);
		} catch (Exception $e) {
			switch ($e->getCode()) {
				case 0:
					$this->log->LogToFile($e->getMessage());
					header("Location: /@chan/error.php?e=500");
					break;

				case 1:
					return false;
					break;

				default:
					$this->log->LogToFile($e);
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}

	public function GetUserDataByID($id)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.users WHERE id = " . $id . ";";

		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				return $results->fetch_assoc();
			} else throw new Exception("Couldn't get user data.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function GetUserDataByUsername($uername)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.users WHERE username = '" . $uername . "';";

		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				return $results->fetch_assoc();
			} else throw new Exception("Couldn't get user data.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function Login($username, $password)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT password FROM atchan.users WHERE username = '" . $username . "' AND username <> '[deleted]';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				$result_array = $results->fetch_assoc();

				if (password_verify($password, $result_array['password'])) {
					return true;
				} else throw new Exception("Wrong password", 0);
			} else throw new Exception("User doesn't exist", 1);
		} catch (Exception $e) {
			switch ($e->getCode()) {
				case 0:
					header("Location: /@chan/pages/login.php?password");
					break;
				case 1:
					header("Location: /@chan/pages/login.php?nouser");
					break;
				default:
					$this->log->LogToFile($e->getMessage());
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}

	public function ChangePfp($username, $new_pfp)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "UPDATE atchan.users SET pfp = '" . $new_pfp . "' WHERE username = '" . $username . "';";
		$sql .= "UPDATE atchan.users SET default_pfp = 0 WHERE username = '" . $username . "';";
		try {
			if ($conn->multi_query($sql) === true) return true;
			else throw new Exception("Couldn't change profile picture.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function ChangePassword($username, $current_password, $new_password)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$hashed_password_new = password_hash($new_password, PASSWORD_DEFAULT);

		$sql = "SELECT password FROM atchan.users WHERE username = '" . $username . "';";
		try {
			$results = $conn->query($sql);

			if ($results->num_rows !== 0) {
				$result_array = $results->fetch_assoc();

				if (password_verify($current_password, $result_array['password'])) {
					if (!password_verify($new_password, $result_array['password'])) {
						$sql = "UPDATE atchan.users SET password = '" . $hashed_password_new . "' WHERE username = '" . $username . "';";

						if ($conn->query($sql) === true) return true;
						else throw new Exception("Couldn't change password.", 3);
					} else throw new Exception("New password is the same as the current one.", 2);
				} else throw new Exception("Wrong password.", 1);
			} else throw new Exception("Couldn't change password.", 0);
		} catch (Exception $e) {
			switch ($e->getCode()) {
				case 1:
					header("Location: /@chan/pages/user-panel.php?wrong_password");
					break;

				case 2:
					header("Location: /@chan/pages/user-panel.php?same_password");
					break;

				default:
					$this->log->LogToFile($e->getMessage());
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}

	public function ChangeUsername($username, $new_username)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT username FROM atchan.users WHERE username = '" . $username . "';";
		try {
			$results = $conn->query($sql);

			if ($results->num_rows !== 0) {
				$sql = "SELECT username FROM atchan.users WHERE username = '" . $new_username . "';";
				$results = $conn->query($sql);

				if ($results->num_rows === 0) {
					$sql = "UPDATE atchan.users SET username = '" . $new_username . "' WHERE username = '" . $username . "';";

					if ($conn->query($sql) === true) return true;
					else throw new Exception("Couldn't change username.", 2);
				} else throw new Exception("Username already in use.", 1);
			} else throw new Exception("User does not exist.", 0);
		} catch (Exception $e) {
			switch ($e->getCode()) {
				case 1:
					header("Location: /@chan/pages/user-panel.php?username_exists");
					break;

				default:
					$this->log->LogToFile($e->getMessage());
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}

	public function Comment($comment, $cid, $tid, $uid)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "INSERT INTO atchan.comments (comment, channel, thread, user) VALUES ('$comment', $cid, '$tid', $uid);";
		try {
			if ($conn->query($sql) === true) {
				$sql = "UPDATE atchan.channels SET comments = (SELECT COUNT(*) FROM atchan.comments WHERE channel = $cid) WHERE id = $cid;";
				$sql .= "UPDATE atchan.threads SET comments = (SELECT COUNT(*) FROM atchan.comments WHERE thread = '$tid') WHERE id = '$tid';";

				if ($conn->multi_query($sql)) return true;
			} else throw new Exception("Couldn't comment.", 0);
		} catch (Exception $e) {
			switch ($e->getCode()) {
				case 0:
					$this->log->LogToFile($e->getMessage());
					header("Location: /@chan/error.php?e=500");
					break;

				default:
					$this->log->LogToFile($e);
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}

	public function GetComments($id, $current_page, $limit)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$start = ($current_page - 1) * $limit;

		$sql = "SELECT * FROM atchan.comments WHERE user = $id LIMIT $start, $limit;";
		$sql2 = "SELECT * FROM atchan.comments WHERE user = $id;";

		try {
			$results = $conn->query($sql);
			$results2 = $conn->query($sql2);
			if ($results->num_rows !== 0) return array("comments" => $results->fetch_all(MYSQLI_ASSOC), "num_rows" => $results2->num_rows);
			else return null;
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function DeleteUser($id, $username)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.users WHERE id = '$id';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows > 0) {
				$result_array = $results->fetch_assoc();

				if ($result_array['username'] !== $username) throw new Exception("Wrong username.", 1);

				if ($result_array['default_pfp'] === '0') {
					$pfp_path = $_SERVER["DOCUMENT_ROOT"] . "/@chan/_storage/profile-pictures/" . $result_array['pfp'];

					if (!unlink($pfp_path)) throw new Exception("Couldn't delete profile picture.", 2);
				}

				$sql = "UPDATE atchan.users SET username = '[deleted]' WHERE id = '" . $id . "';";
				$sql .= "UPDATE atchan.users SET pfp = 'deleted.jpg' WHERE id = '" . $id . "';";

				if ($conn->multi_query($sql)) {
					return true;
				} else throw new Exception("Couldn't delete user.", 0);
			} else throw new Exception("Couldn't delete user.", 0);
		} catch (Exception $e) {
			switch ($e->getCode()) {
				case 0:
					$this->log->LogToFile($e->getMessage());
					header("Location: /@chan/error.php?e=500");
					break;

				case 1:
					header("Location: /@chan/index.php?p=user-panel&wrong_username");
					break;

				case 2:
					$this->log->LogToFile($e);
					break;

				default:
					$this->log->LogToFile($e);
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}

	public function DeleteComment($cid)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.comments WHERE id = " . $cid . ";";
		try {
			$results = $conn->query($sql);

			if ($results->num_rows > 0) {
				$comment = $results->fetch_assoc();

				$sql = "DELETE FROM atchan.comments WHERE id = " . $cid . ";";
				$sql .= "UPDATE atchan.channels SET comments = (SELECT COUNT(*) FROM atchan.comments WHERE channel = " . $comment['channel'] . ") WHERE id = " . $comment['channel'] . ";";
				$sql .= "UPDATE atchan.threads SET comments = (SELECT COUNT(*) FROM atchan.comments WHERE thread = '" . $comment['thread'] . "') WHERE id = '" . $comment['thread'] . "';";

				if ($conn->multi_query($sql)) return true;
				else throw new Exception("Couldn't delete comment.", 0);
			} else throw new Exception("Couldn't delete comment.", 0);
		} catch (Exception $e) {
			switch ($e->getCode()) {
				case 0:
					$this->log->LogToFile($e->getMessage());
					header("Location: /@chan/error.php?e=500");
					break;

				default:
					$this->log->LogToFile($e);
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}
}
