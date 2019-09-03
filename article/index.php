<!-- This is an article page. It is blank until the PHP code fills it in with the article, selected by the 'subject' and 'article' params in the GET request. For example, if I went to https://automagic.edosphere.org/article?subject=GCSE+History&article=Norman+conquest it would show me the Norman Conquest article from the GCSE History section. At the moment this page has very little CSS, more coming soon. -->

<html>
  <head>
    <title><?=$_GET["article"] . " - Edosphere " . $_GET["subject"]?></title>
		<link href="https://fonts.googleapis.com/css?family=Merriweather:300|Patua+One|Ubuntu:500,700&display=swap" rel="stylesheet">
		<link href="article/style.css" rel="stylesheet" type="text/css"/>
		<link href="globalStyle.css" rel="stylesheet" type="text/css"/>
  </head>
  <body>

		<header style="position: fixed; width: 100%; z-index: 1;">
			<ul id="navbar">
				<li><a href="index.php">Home</a></li>
  			<li><a href="news/index.html">News</a></li>
				<?php
$dir = new DirectoryIterator("articles");
foreach ($dir as $fileInfo) {
	if (!$fileInfo->isDot()) {
		$subject = $fileInfo->getFilename();
		$menu = '<li class="dropdown"><a href="javascript:void(0)" class="dropbtn">' . $subject . '</a><div class="dropdown-content">';
		$subDir = new DirectoryIterator("articles/" . $subject);
		foreach ($subDir as $subFileInfo) {
			$article = $subFileInfo->getFilename();
			if (!$fileInfo->isDot() and strpos( $article , '.' ) === false) { // Only includes folders
				$menu = $menu . "<a href='article?subject=" . str_replace(' ', '+', $subject) . "&article=" . str_replace(' ', '+', $article) . "'>" . $article . "</a>";
			}
		}
		$menu = $menu . "</div></li>";
		echo $menu;
	}
}
				?>
				<li style="float:right"><a href="register/index.html">Register</a></li>
				<li style="float:right"><a href="login/index.html">Login</a></li>
				<li style="float:right;  border-left: 1px solid #bbb;"><a href="about/index.html">About</a></li>
			</ul>
		</header>

		<div id="contents">
			<ol id="contentsList">
				<?php
$article = fopen("articles/" . $_GET["subject"] . "/" . $_GET["article"] . "/text.txt", "r") or die("Could not find that article!");

// Loops through each line of the article
while(!feof($article)) {
  $line = fgets($article);
	if (substr($line, 0, 4) == "### ") {
		$line = substr($line, 4);
		echo '<li><a href="#' . str_replace("'", '', str_replace(' ', '_', $line)) . '">' . $line . "</a></li>";
	}
}
				?>
			</ol>
		</div>

		<div id="article">
			<?php
$article = fopen("articles/" . $_GET["subject"] . "/" . $_GET["article"] . "/text.txt", "r") or die();

// Loops through each line of the article
while(!feof($article)) {
  $line = fgets($article);

	if ($line[0] == "#") { // Headers
		$h = 0;
		while ($line[$h] == "#") {
			$h++;
		}
		$line = substr($line, $h+1, -1);
		echo "<h" . $h . " class='anchor' id='" . str_replace("'", '', str_replace(' ', '_', $line)) . "'>" . $line . "</h" . $h . ">";
	}

	else if ($line[0] == "}") { // Author
		echo "<br><author><i>written by " . substr($line, 2) . "</i></author>";
	}

	else if ($line[0] == "[") { // Images
		$srcAltHref = explode(",", substr($line, 1, -1), 3);
		echo "<a href='" . $srcAltHref[2] . "'><img src='article/articles/" . $_GET["subject"] . "/" . $_GET["article"] . "/images/" . $srcAltHref[0] . "' alt='" . $srcAltHref[1] . "'></a>";
	}

	else { // Paragraph text
		$i = 0;
		$italics = $bold = $underlined = $strikethrough = false;
		while ($i < strlen($line)) {
			$char = $line[$i];
			// Italics
			if ($char == "|") {
				if ($italics) {
					$line = substr_replace($line, "</i>", $i, 1);
				} else {
					$line = substr_replace($line, "<i>", $i, 1);
				};
				$italics = !$italics;
			}
			// Bold
			else if ($char == "*") {
				if ($bold) {
					$line = substr_replace($line, "</b>", $i, 1);
				} else {
					$line = substr_replace($line, "<b>", $i, 1);
				};
				$bold = !$bold;
			}
			// Underlined
			else if ($char == "_") {
				if ($underlined) {
					$line = substr_replace($line, "</u>", $i, 1);
				} else {
					$line = substr_replace($line, "<u>", $i, 1);
				};
				$underlined = !$underlined;
			}
			// Strikethrough
			else if ($char == "~") {
				if ($strikethrough) {
					$line = substr_replace($line, "</del>", $i, 1);
				} else {
					$line = substr_replace($line, "<del>", $i, 1);
				};
				$strikethrough = !$strikethrough;
			};
			$i++;
		};

		echo "<p>" . $line . "</p>";
	}
	
};

fclose($article);
			?>
		</div>
  </body>
</html>