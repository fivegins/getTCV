<?php

include 'db.php';
//include 'functions.php';

if (isset($_POST['link'])) {
	$link = preg_replace('/https:\/\/truyencv\.com\/(.*?)\//', '$1', $_POST['link']);
	$stmt = $db->prepare('SELECT slug FROM site WHERE slug = :slug');
	$stmt->execute(array(':slug' => $link ));

	if (!empty($link) && $stmt->rowCount() == 0) {
		$query = "INSERT INTO site (name, slug, flag, start, end, nl2p, loc, remove, date_update) VALUES (:name, :slug, :flag, :start, :end, :nl2p, :loc, :remove, :date_update)";
		$stmt = $db->prepare($query) ;
		$stmt->execute(array(
			':name' => $link,
			':slug' => $link,
			':flag' => 'tcv',
			':start' => '1',
			':end' => '10',
			':nl2p' => 'no',
			':loc' => 'yes',
			':remove' => 'iframe, script, style, a, div, p',
			':date_update' => date('Y-m-d H:i:s')
		));
	}
}

$query = "SELECT * FROM site ORDER BY date_update DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<title>TCV REGEX</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
	a { text-decoration: none; }
</style>
<a href="bookmark.php">Bookmark</a> | <a href="note.php">Note</a>
<hr>
<form action="" method="post">
	<input type="text" name="link">
	<input type="submit" value="New site">
</form>
<?php foreach ($result as $post): ?>
	<pre><a href="config_site.php?slug=<?php echo $post['slug'] ?>"><?php echo $post['name'] ?></a> <a href="regex.php?slug=<?php echo $post['slug'] ?>" style="background-color: yellow">Regex</a></pre>
<?php endforeach ?>