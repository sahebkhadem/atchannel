<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/database.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/logging.service.php");

define("PFP_PATH", $_SERVER["DOCUMENT_ROOT"] . "/@chan/_storage/profile-pictures");
define("CHANNEL_BANNER_PATH", $_SERVER["DOCUMENT_ROOT"] . "/@chan/_storage/channel-banners");

class StorageService
{
	private $log;

	public function Upload($file, $type)
	{
		$this->log = new LoggingService();

		$file_name = $file['name'];
		$file_tmp_name = $file['tmp_name'];
		$file_error = $file['error'];

		$file_ext = explode(".", $file_name);
		$file_actual_ext = strtolower(end($file_ext));

		$allowed_ext = array("jpg", "jpeg", "png");

		try {
			if (in_array($file_actual_ext, $allowed_ext)) {
				if ($file_error === UPLOAD_ERR_OK) {
					$file_name_new = uniqid("", true) . "." . $file_actual_ext;

					if ($type === "pfp") {
						$file_destination = PFP_PATH . "/" . $file_name_new;
					} else if ($type === "banner") {
						$file_destination = CHANNEL_BANNER_PATH . "/" . $file_name_new;
					}

					if (move_uploaded_file($file_tmp_name, $file_destination)) {
						return $file_name_new;
					} else throw new Exception("Failed to upload file.", 6);
				}
			} else throw new Exception("Wrong file type.", 0);
		} catch (Exception $e) {
			if ($e === 0) {
				header("Location: /@chan/pages/register.php?wrong_type=true");
			} else {
				$this->log->LogToFile($e->getMessage());
				header("Location: /@chan/error.php?e=500");
			}
		}
	}
}
