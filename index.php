<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/database.service.php");

date_default_timezone_set("Asia/Tehran");

if (!isset($_SESSION)) session_start();

$db = new DatabaseService();
$db->Init();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/x-icon" href="./favicon.ico">

	<?php
	if (isset($_GET['p'])) {
		switch ($_GET['p']) {
			case 'login':
			case 'register':
			case 'user-panel':
			case 'channel-panel':
			case 'create-thread':
			case 'thread':
				echo "<link rel='stylesheet' href='/@chan/css/form-resizer.css' />";
				break;

			case 'channel':
				echo "<link rel='stylesheet' href='/@chan/css/channel-page.css' />";
				break;
		}
	}
	?>

	<title>@Channel | <?php
						if (isset($_GET['p'])) {
							switch ($_GET['p']) {
								case 'login':
									echo "Login";
									break;

								case 'register':
									echo "Register";
									break;

								case 'profile':
									if (isset($_GET['user'])) {
										echo $_GET['user'] . "'s profile";
									} else echo $_SESSION['username'] . "'s profile";
									break;

								case 'user-panel':
									echo $_SESSION['username'] .  "'s panel";
									break;

								case 'create-channel':
									echo "Create channel";
									break;

								case 'channel-panel':
									echo $_GET['at'] . "'s panel";
									break;

								case 'create-thread':
									echo "Create thread";
									break;
							}
						} else echo "Home";
						?>
	</title>
</head>

<body>
	<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/templates/navbar.template.php"); ?>

	<main class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 87.9vh">
		<?php
		if (isset($_GET['p'])) {
			switch ($_GET['p']) {
				case 'login':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/login.page.php");
					break;

				case 'register':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/register.page.php");
					break;

				case 'profile':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/profile.page.php");
					break;

				case 'user-panel':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/user-panel.page.php");
					break;

				case 'create-channel':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/create-channel.page.php");
					break;

				case 'channel':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/channel.page.php");
					break;

				case 'channel-panel':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/channel-panel.page.php");
					break;

				case 'create-thread':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/create-thread.page.php");
					break;

				case 'thread':
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/thread.page.php");
					break;

				default:
					require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/error.php?e=404");
					break;
			}
		} else if (!isset($_GET['p']) || $_GET['p'] === "" || $_GET['p'] === "home" || $_GET['p'] === "main") {
			require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/pages/main.page.php");

			if (isset($_SESSION['username'])) {
				echo "<a class='btn btn-primary my-3' href='/@chan/index.php?p=create-channel'><i class='bi bi-plus-square'></i> Create a new channel!</a>";
			} else echo "<a class='btn btn-primary my-3' href='/@chan/index.php?p=login&unauthenticated'><i class='bi bi-plus-square'></i> Create a new channel!</a>";
		}
		?>

	</main>

	<button class="btn btn-secondary" id="to-top-btn" title="Go to top"><i class="bi bi-arrow-up-square"></i></button>
	<?php
	if (isset($_GET['p'])) {
		switch ($_GET['p']) {
			case 'thread':
				echo "<a href='#form' class='btn btn-primary' id='to-comment-btn' title='Comment'>Comment</a>";
				break;
		}
	}
	?>

	<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/templates/footer.template.html"); ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<script src="/@chan/javascript/to-top.js"></script>
	<script src="/@chan/javascript/to-comment.js"></script>

	<?php
	if (isset($_GET['p'])) {
		switch ($_GET['p']) {
			case 'login':
			case 'register':
				echo "<script src='/@chan/javascript/form-validator.js'></script>";
				echo "<script src='/@chan/javascript/toggle-password.js'></script>";
				break;

			case 'user-panel':
				echo "<script src='/@chan/javascript/panel-forms-validator.js'></script>";
				echo "<script src='/@chan/javascript/toggle-password.js'></script>";
				break;

			case 'create-channel':
				echo "<script src='/@chan/javascript/create-channel-form-validator.js'></script>";
				echo "<script src='/@chan/javascript/channel-description-countdown.js'></script>";
				break;

			case 'channel-panel':
				echo "<script src='/@chan/javascript/channel-panel-forms-validator.js'></script>";
				break;

			case 'create-thread':
				echo "<script src='/@chan/javascript/create-thread-form-validator.js'></script>";
				echo "<script src='/@chan/javascript/channel-description-countdown.js'></script>";
				break;

			case 'thread':
				echo "<script src='/@chan/javascript/comment-validator.js'></script>";
				echo "<script src='/@chan/javascript/to-comment.js'></script>";
				break;

			default:
				echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>";
				echo "<script src='/@chan/javascript/to-top.js'></script>";
				break;
		}
	}
	?>
</body>

</html>