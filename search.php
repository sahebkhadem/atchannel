<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/channel.service.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");

if (!isset($_SESSION)) session_start();

$channel = new ChannelService();

$search_results = null;

if (isset($_GET['search']) && $_GET['search'] !== null) {
	$limit = 2;
	$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
	$search_results = $channel->SearchChannels($_GET['search'], $current_page, $limit);
	if ($search_results !== null) {
		$results = $search_results['results'];
		$num_rows = $search_results['num_rows'];
		$num_pages = ceil($num_rows / $limit);
		$previous = $current_page - 1;
		$next = $current_page + 1;
		$previous_disabled = $current_page == 1 ? 'disabled' : '';
		$next_disabled = $current_page == $num_pages ? 'disabled' : '';

		if (isset($_GET['page']) && $num_pages < 2) {
			if ($_GET['page'] > 1) header("Location: /@chan/error.php?e=404");
		}
	} else $num_pages = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
	<link rel="stylesheet" href="css/style.css" />
	<title>@Channel | Search</title>
</head>

<body>
	<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/templates/navbar.template.php"); ?>

	<main class="container d-flex flex-column justify-content-center align-items-center p-0 p-sm-0" style="min-height: 87.9vh">
		<section class="container flex-grow-1 d-flex flex-column align-items-center bg-light border-start border-end px-0 py-2">
			<h1 class="m-3">Search for a channel</h1>
			<form action="" method="get" class="search-bar-resizer mb-5">
				<div class="input-group">
					<input type="text" name="search" class="form-control" placeholder="Channel name" />
					<button class="btn btn-primary" type="submit">
						<i class="bi bi-search"></i> Search
					</button>
				</div>
			</form>

			<div class="container d-flex flex-column justify-content-center align-items-center px-0 px-sm-5">
				<?php
				if ($num_pages > 1) {
					echo "
						<nav aria-label='Page navigation'>
							<ul class='pagination mt-2'>
								<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=1'>First</a></li>
								<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$previous'>&laquo; Previous</a></li>
								<li class='page-item'>
									<div class='dropdown'>
										<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
											Page $current_page of $num_pages
										</button>
										<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
					";

					for ($i = 1; $i <= $num_pages; $i++) {
						$is_active = $i == $current_page ? "active" : "";
						echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$i'>$i</a></li>";
					}

					echo "
										</ul>
									</div>
								</li>
								<li class='page-item $next_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$next'>Next &raquo;</a></li>
								<li class='page-item $next_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$num_pages'>Last</a></li>
							</ul>
						</nav>
					";
				}

				if (empty($results)) {
					echo "<h3 class='text-muted mt-5'>No channels were found...</h3>";
				} else {
					foreach ($results as $result) {
						echo "
					<div class='card mb-3 w-100'>
						<a style='text-decoration: none !important;' href='/@chan/index.php?p=channel&at=" . $result['name'] . "'>
							<div class='card-header'>
								<h3 class='text-dark'>" . $result['name'] . "</h3>
							</div>
						</a>
						<div class='card-body'>
							<p class='card-text'>" . $result['description'] . "</p>
							<a class='btn btn-primary' href='/@chan/index.php?p=channel&at=" . $result['name'] . "'>Go to " . $result['name'] . "</a>
						</div>
					</div>
				";
					}
				}

				if ($num_pages > 1) {
					echo "
						<nav aria-label='Page navigation'>
							<ul class='pagination mt-2'>
								<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=1'>First</a></li>
								<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$previous'>&laquo; Previous</a></li>
								<li class='page-item'>
									<div class='dropdown'>
										<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
											Page $current_page of $num_pages
										</button>
										<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
					";

					for ($i = 1; $i <= $num_pages; $i++) {
						$is_active = $i == $current_page ? "active" : "";
						echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$i'>$i</a></li>";
					}

					echo "
										</ul>
									</div>
								</li>
								<li class='page-item $next_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$next'>Next &raquo;</a></li>
								<li class='page-item $next_disabled'><a class='page-link' href='/@chan/search.php?search=" . $_GET['search'] . "&page=$num_pages'>Last</a></li>
							</ul>
						</nav>
					";
				}
				?>
			</div>
		</section>
	</main>

	<button class="btn btn-secondary" id="to-top-btn" title="Go to top"><i class="bi bi-arrow-up-square"></i></button>

	<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/templates/footer.template.html"); ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<script src="/@chan/javascript/to-top.js"></script>
</body>

</html>