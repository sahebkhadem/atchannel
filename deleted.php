<?php if (!isset($_SESSION)) session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
	<link rel="stylesheet" href="/@chan/css/style.css" />
	<title>@Channel | Deleted account</title>
</head>

<body>
	<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/templates/navbar.template.php"); ?>

	<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 87.9vh">
		<img src="/@chan/assets/img/deleted.png" alt="Deleted" class="img-fluid w-25" />

		<?php
		if (isset($_GET['d'])) {
			if ($_GET['d'] === "user") {
				echo "
					
					<p class='lead text-danger'>User Does Not Exist</p>
					<p class='text-dark'>Sorry, that user has deleted their account :(</p>
				";
			}

			if ($_GET['d'] === "channel") {
				echo "
					<p class='lead text-danger'>Channel Does Not Exist</p>
					<p class='text-dark'>Sorry, this channel has been deleted :(</p>
				";
			}

			if ($_GET['d'] === "thread") {
				echo "
					<p class='lead text-danger'>Thread Does Not Exist</p>
					<p class='text-dark'>Sorry, this thread has been deleted :(</p>
				";
			}
		}
		?>
	</div>

	<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/templates/footer.template.html"); ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>