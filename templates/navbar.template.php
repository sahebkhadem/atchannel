<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
	<div class="container-fluid">
		<a class="navbar-brand" href="/@chan/index.php">
			<img src="/@chan/assets/img/logo.png" alt="@Channel logo" class="d-inline-block align-text-top" style="width: auto; height: 30px" />
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNavDropdown">
			<ul class="navbar-nav w-100">
				<li class="nav-item">
					<a class="nav-link" href="/@chan/index.php">Home</a>
				</li>

				<?php
				if (isset($_SESSION['username'])) {
					echo "
					<li class='nav-item'>
						<a class='nav-link' href='/@chan/index.php?p=profile'>Profile</a>
					</li>
					";
				} else {
					echo "
						<li class='nav-item'>
							<a class='nav-link' href='/@chan/index.php?p=login&unauthenticated'>Profile</a>
						</li>
					";
				}
				?>

				<?php
				if (isset($_SESSION['username'])) {
					echo "
					<li class='nav-item ms-lg-auto login-btn'>
						<a class='btn btn-outline-danger' href='/@chan/logout.php'><i class='bi bi-box-arrow-left'></i> Logout</a>
					</li>

					<li class='nav-item ms-lg-auto login-link'>
						<a class='nav-link text-danger' href='/@chan/logout.php'><i class='bi bi-box-arrow-left'></i> Logout</a>
					</li>
					";
				} else {
					echo "
						<li class='nav-item ms-lg-auto login-btn'>
							<a class='btn btn-outline-primary' href='/@chan/index.php?p=login'><i class='bi bi-box-arrow-in-right'></i> Log in</a>
						</li>

						<li class='nav-item ms-lg-auto login-link'>
							<a class='nav-link text-primary' href='/@chan/index.php?p=login'><i class='bi bi-box-arrow-in-right'></i> Log in</a>
						</li>
					";
				}
				?>
			</ul>
		</div>
	</div>
</nav>