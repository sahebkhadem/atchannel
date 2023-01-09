<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/@chan/services/user.service.php");
$user = new UserService();

$user_data;
$comments;

$limit = 50;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

if (isset($_GET['user'])) {
	$user_data = $user->GetUserDataByUsername($_GET['user']);
	$comments_results = $user->GetComments($user_data['id'], $current_page, $limit);
	$comments = $comments_results['comments'];
} else if (isset($_SESSION['username'])) {
	$user_data = $user->GetUserDataByUsername($_SESSION['username']);
	$comments_results = $user->GetComments($user_data['id'], $current_page, $limit);
	$comments = $comments_results['comments'];
}

if ($comments_results !== null) {
	$num_rows = $comments_results['num_rows'];
	$num_pages = ceil($num_rows / $limit);
	$previous = $current_page - 1;
	$next = $current_page + 1;
	$previous_disabled = $current_page == 1 ? 'disabled' : '';
	$next_disabled = $current_page == $num_pages ? 'disabled' : '';

	if (isset($_GET['page']) && $num_pages < 2) {
		if ($_GET['page'] > 1) header("Location: /@chan/error.php?e=404");
	}
} else $num_pages = 0;

if (isset($_GET['page']) && $num_pages < 2) header("Location: /@chan/error.php?e=404");
?>

<div class="container d-flex flex-column align-items-center bg-light border-start border-end p-0" style="min-height: 87.9vh">
	<header class="container d-flex align-items-center border-bottom mb-2 p-3">
		<?php echo "<img src='/@chan/_storage/profile-pictures/" . $user_data['pfp'] . "' class='img-crop img-thumbnail' style='width: 100px !important; height: 100px !important;' alt='Profile picture'>"; ?>
		<h3 class="py-2 px-2"><?php echo $user_data['username']; ?></h3>
		<?php
		if (isset($_SESSION['username'])) {
			if ($user_data['username'] === $_SESSION['username']) {
				echo "<a href='/@chan/index.php?p=user-panel' class='btn btn-outline-secondary'><i class='bi bi-pencil-square'></i></a>";
			}
		}
		?>
	</header>

	<section class="container d-flex flex-column jujstify-content-center align-items-center px-md-5 px-1 bg-light">
		<?php
		if ($num_pages > 1) {
			echo "
				<nav aria-label='Page navigation'>
					<ul class='pagination mt-2'>
			";

			if (isset($_GET['user'])) {
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=1'>First</a></li>";
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$previous'>&laquo; Previous</a></li>";
			} else {
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=1'>First</a></li>";
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=$previous'>&laquo; Previous</a></li>";
			}

			echo "
				<li class='page-item'>
					<div class='dropdown'>
						<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
							Page $current_page of $num_pages
						</button>
						<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
			";

			for ($i = 1; $i <= $num_pages; $i++) {
				$is_active = $i == $current_page ? "active" : "";

				if (isset($_GET['user'])) echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$i'>$i</a></li>";
				else echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=profile&page=$i'>$i</a></li>";
			}

			echo "
						</ul>
					</div>
				</li>
			";

			if (isset($_GET['user'])) {
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$next'>Next &raquo;</a></li>";
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$num_pages'>Last</a></li>";
			} else {
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=$next'>Next &raquo;</a></li>";
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=$num_pages'>Last</a></li>";
			}

			echo "
					</ul>
				</nav>
			";
		}

		if ($comments === null) {
			echo "<h3 class='text-muted'>Nothing to see here...</h3>";
		} else {
			foreach ($comments as $comment) {
				echo "
					<div class='card mb-3 w-100'>
						<div class='card-header'>
							" . $comment['written'] . "
				";

				if (isset($_SESSION['username']) && $_SESSION['username'] === $user_data['username']) {
					echo "<a href='/@chan/delete.php?cid=" . $comment['id'] . "&p' class='btn btn-outline-danger ms-3'><i class='bi bi-trash'></i> Delete</a>";
				}

				echo "
						</div>
						<div class='card-body'>
							<p class='card-text'>" . $comment['comment'] . "</p>
						</div>

						<div class='card-footer'>
							<a href='/@chan/index.php?p=thread&tid=" . $comment['thread'] . "&at=" . $comment['channel'] . "' class='btn btn-primary'>Go to thread</a>
					  	</div>
					</div>
				";
			}
		}

		if ($num_pages > 1) {
			echo "
				<nav aria-label='Page navigation'>
					<ul class='pagination mt-2'>
			";

			if (isset($_GET['user'])) {
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=1'>First</a></li>";
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$previous'>&laquo; Previous</a></li>";
			} else {
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=1'>First</a></li>";
				echo "<li class='page-item $previous_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=$previous'>&laquo; Previous</a></li>";
			}

			echo "
				<li class='page-item'>
					<div class='dropdown'>
						<button class='page-link dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
							Page $current_page of $num_pages
						</button>
						<ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
			";

			for ($i = 1; $i <= $num_pages; $i++) {
				$is_active = $i == $current_page ? "active" : "";

				if (isset($_GET['user'])) echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$i'>$i</a></li>";
				else echo "<li class='$is_active'><a class='dropdown-item' href='/@chan/index.php?p=profile&page=$i'>$i</a></li>";
			}

			echo "
						</ul>
					</div>
				</li>
			";

			if (isset($_GET['user'])) {
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$next'>Next &raquo;</a></li>";
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&user=" . $user_data['username'] . "&page=$num_pages'>Last</a></li>";
			} else {
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=$next'>Next &raquo;</a></li>";
				echo "<li class='page-item $next_disabled'><a class='page-link' href='/@chan/index.php?p=profile&page=$num_pages'>Last</a></li>";
			}

			echo "
					</ul>
				</nav>
			";
		}
		?>
	</section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>