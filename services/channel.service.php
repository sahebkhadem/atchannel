<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/database.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/logging.service.php");

class ChannelService
{
	private $log;

	public function CreateChannel($name, $description, $banner, $creator)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.channels WHERE name = '" . $name . "';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows === 0) {
				$sql = "INSERT INTO atchan.channels (name, description, banner, threads, comments, creator) VALUES ('" . $name . "', '" . $description . "', '" . $banner . "', 0, 0, " . $creator . ");";

				if ($conn->query($sql) === true) return true;
				else throw new Exception("Couldn't create channel.", 0);
			} else throw new Exception("A channel with that name already exists.", 1);
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

	public function GetChannelDataByName($at)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.channels WHERE name = '" . $at . "';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				return $results->fetch_assoc();
			} else throw new Exception("Couldn't get channel data.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function GetChannelDataById($cid)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.channels WHERE id = " . $cid . ";";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				return $results->fetch_assoc();
			} else throw new Exception("Couldn't get channel data.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function ChangeBanner($channel, $new_banner)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "UPDATE atchan.channels SET banner = '" . $new_banner . "' WHERE name = '" . $channel . "';";
		try {
			if ($conn->multi_query($sql) === true) return true;
			else throw new Exception("Couldn't change banner.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function CreateThread($channel, $title, $content, $creator)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$id = uniqid();

		$sql = "INSERT INTO atchan.threads (id, title, content, comments, creator, channel) VALUES ('" . $id . "', '" . $title . "', '" . $content . "', 0, " . $creator . ", " . $channel . ");";
		try {
			if ($conn->query($sql) === true) {
				$sql = "UPDATE atchan.channels SET threads = (SELECT COUNT(*) FROM atchan.threads WHERE channel = " . $channel . ") WHERE id = " . $channel . ";";

				if ($conn->query($sql) === true) return $id;
				else throw new Exception("Couldn't update thread count.", 1);
			} else throw new Exception("Couldn't create thread.", 0);
		} catch (mysqli_sql_exception $e) {
			switch ($e->getCode()) {
				case 0:
					$this->log->LogToFile($e);
					header("Location: /@chan/error.php?e=500");
					break;

				default:
					$this->log->LogToFile($e);
					header("Location: /@chan/error.php?e=500");
					break;
			}
		}
	}

	public function GetThreads($cid, $current_page, $limit)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$start = ($current_page - 1) * $limit;

		$sql = "SELECT * FROM atchan.threads WHERE channel = $cid ORDER BY created LIMIT $start, $limit;";
		$sql2 = "SELECT * FROM atchan.threads WHERE channel = $cid;";
		try {
			$threads_results = $conn->query($sql);
			$threads_count = $conn->query($sql2);
			if ($threads_results->num_rows !== 0) return array("threads" => $threads_results->fetch_all(MYSQLI_ASSOC), "num_rows" => $threads_count->num_rows);
			else return null;
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function GetThreadData($tid)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.threads WHERE id = '$tid';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				return $results->fetch_assoc();
			} else throw new Exception("Couldn't get comments.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function GetThreadComments($tid, $current_page, $limit)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$start = ($current_page - 1) * $limit;

		$sql = "SELECT c.id AS cid, c.comment, c.channel, c.thread, c.written, u.id AS uid, u.username, u.pfp, u.accesslevel FROM atchan.comments AS c INNER JOIN atchan.users AS u ON c.user = u.id WHERE c.thread = '$tid' ORDER BY c.written LIMIT $start, $limit;";
		$sql2 = "SELECT c.id AS cid, c.comment, c.channel, c.thread, c.written, u.id AS uid, u.username, u.pfp, u.accesslevel FROM atchan.comments AS c INNER JOIN atchan.users AS u ON c.user = u.id WHERE c.thread = '$tid';";
		try {
			$comments_results = $conn->query($sql);
			$comments_count = $conn->query($sql2);
			if ($comments_results->num_rows !== 0) return array("comments" => $comments_results->fetch_all(MYSQLI_ASSOC), "num_rows" => $comments_count->num_rows);
			else return null;
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function DeleteChannel($channel)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.channels WHERE name = '" . $channel . "';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				$results_array = $results->fetch_assoc();

				$sql = "DELETE FROM atchan.comments WHERE channel = " . $results_array['id'] . ";";
				$sql .= "DELETE FROM atchan.threads WHERE channel = " . $results_array['id'] . ";";
				$sql .= "DELETE FROM atchan.channels WHERE id = " . $results_array['id'] . ";";

				if ($conn->multi_query($sql) === true) return true;
				else throw new Exception("Couldn't delete channel.", 0);
			} else throw new Exception("A channel with that name doesn't exists.", 1);
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

	public function DeleteThread($tid)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$sql = "SELECT * FROM atchan.threads WHERE id = '" . $tid . "';";
		try {
			$results = $conn->query($sql);
			if ($results->num_rows !== 0) {
				$results_array = $results->fetch_assoc();

				$sql = "DELETE FROM atchan.comments WHERE thread = '" . $results_array['id'] . "';";
				$sql .= "DELETE FROM atchan.threads WHERE id = '" . $results_array['id'] . "';";
				$sql .= "UPDATE atchan.channels SET threads = (SELECT COUNT(*) FROM atchan.threads WHERE channel = " . $results_array['channel'] . ") WHERE id = " . $results_array['channel'] . ";";
				$sql .= "UPDATE atchan.channels SET comments = (SELECT COUNT(*) FROM atchan.comments WHERE channel = " . $results_array['channel'] . ") WHERE id = " . $results_array['channel'] . ";";

				if ($conn->multi_query($sql) === true) return true;
				else throw new Exception("Couldn't delete thread.", 0);
			} else throw new Exception("Couldn't delete thread.", 0);
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}

	public function SearchChannels($search, $current_page, $limit)
	{
		$this->log = new LoggingService();

		$db = new DatabaseService();
		$conn = $db->Connect();

		$start = ($current_page - 1) * $limit;

		$sql = "SELECT * FROM atchan.channels WHERE name LIKE '%$search%' LIMIT $start, $limit;";
		$sql2 = "SELECT * FROM atchan.channels WHERE name LIKE '%$search%';";
		try {
			$search_results = $conn->query($sql);
			$count_results = $conn->query($sql2);
			if ($search_results->num_rows !== 0) return array("results" => $search_results->fetch_all(MYSQLI_ASSOC), "num_rows" => $count_results->num_rows);
			else return null;
		} catch (Exception $e) {
			$this->log->LogToFile($e->getMessage());
			header("Location: /@chan/error.php?e=500");
		}
	}
}
