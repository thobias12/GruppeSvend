<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#2D2D2D" id="theme_color">
	<title>Fashion Online | <?= $pageTitle ?> <?= $pageSection ?></title>
	<!-- Custom stylesheet -->
	<link rel="stylesheet" href="/assets/css/master.css">
	<!-- Google fonts -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!-- Favicon -->
	<link rel="icon" href="favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
</head>

<!-- Body (start) -->
<body>
<!-- Header (start) -->
<header class="header__container">
	<div class="header__row" id="secondMenu">
		<button type="button" class="header__button" data-menu-close="secondMenu"><i class="material-icons">close</i></button>
		<ul>
			<li><a href="">find retailer</a></li>
			<li><a href="">news</a></li>
			<li><a href="">contact</a></li>
		</ul>
		<form class="header__signin-form" action="" method="post">
			<p>sign in</p>
			<div class="form__group">
				<input type="text" name="username" placeholder="Username" />
			</div>
			<div class="form__group">
				<input type="password" name="password" placeholder="Password" />
			</div>
			<button type="submit">Go</button>
		</form>
	</div>
	<button type="button" class="header__button" data-menu-open="secondMenu"><i class="material-icons">menu</i></button>
	<figure class="header__logo-container">
		<img src="/assets/img/logo.png" alt="fashion-online logo" />
	</figure>
	<button type="button" class="header__button" data-search-open="searchForm"><i class="material-icons">search</i></button>
	<div class="header__row--nav">
		<ul>
			<li><a href="">home</a></li>
			<li><a href="">men</a></li>
			<li><a href="">women</a></li>
			<li><a href="">collections</a></li>
		</ul>
		<form class="search__form" action="index.php" method="post" id="searchForm">
			<div class="search__container">
				<label for="search"><i class="material-icons">search</i></label>
				<input type="text" name="search" id="search" placeholder="Search" />
				<button type="button" class="header__button" data-search-close="searchForm"><i class="material-icons">close</i></button>
			</div>
		</form>
	</div>
</header>
<!-- Header (end) -->
