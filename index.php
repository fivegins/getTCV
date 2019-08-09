<?php

include 'db.php';
include 'functions.php';

if (isset($_POST['submit'])) {

	extract($_POST);

	$loc = isset($loc) ? 'yes' : 'no';
	$nl2p = isset($nl2p) ? 'yes' : 'no';

	if (empty($start) || empty($end)) {
		$error = 'Trống!';
	}
	if ($start >= $end) {
		$error = 'Start phải bé hơn End';
	}

	if (!isset($error)) {
		$query = "UPDATE site SET start = :start, end = :end, nl2p = :nl2p, loc = :loc, remove = :remove, date_update = :date_update WHERE id = :id";
		$stmt = $db->prepare($query);
		$stmt->execute(array(
			':id' => $id,
			':start' => $start,
			':end' => $end,
			':nl2p' => $nl2p,
			':loc' => $loc,
			':remove' => $remove,
			':date_update' => date('Y-m-d H:i:s')
		));

		$url = 'https://truyencv.com/' . $link . '/';

		for ($i = $start; $i <= $end; $i++) { 
			$urls[] = $url . 'chuong-' . $i . '/';
		}

		$content = multi_curl($urls);
		preg_match_all('#<title>(.*?)</title>#is', $content, $tit);

		echo '<p><a style="background-color: yellow" href="get.php?link=' . $url . '&s=' . $start . '&e=' . $end . '&slug=' . $link . '">
			get.php?link=' . $url . '&s=' . $start . '&e=' . $end . '
		</a></p>';
		echo '<div style="white-space: nowrap;overflow: auto;">';
		foreach ($tit[1] as $key => $value) {
			preg_match('/truyencv\.com\/([a-z0-9-]+)\//', $url, $name);
			$value = preg_replace('/.*?-\s*(.*)/', '$1', $value);
			echo "<pre><a href='bookmark.php?title={$name[1]} • {$value}' onclick = 'if (! confirm(\"Bookmark?\")) { return false; }'>$key</a> => <a href='getTCV.php?link={$urls[$key]}&slug=$link'>$value</a></pre>\n";
		}
		echo '</div>';


    }


}

$query = "SELECT * FROM site ORDER BY date_update DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$site = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $site['name'] ?></title>
<style>
	a { text-decoration: none; }
	input[name=url],
	input[type=submit],
	input[type=number],
	textarea {
		display: block;
		margin: 10px 0;
	}
	input[name=url],
	input[type=submit],
	textarea {
		width: 100%;
	}
	input[type=number] {
		display: block;
		margin: 10px 0;
	}
	input[type=radio] {
		display: inline-block;
		padding-right: 10px;
	}
</style>
<form method="post">
	<input type="hidden" name="id" value="<?php echo $site['id'] ?>">
	<input type="text" name="link" value="<?php echo $site['slug'] ?>">
	<input type="number" name="start" onfocus="this.value=''" value="<?php echo $site['start'] ?>">
	<input type="number" name="end" onfocus="this.value=''" value="<?php echo $site['end'] ?>">
	<input type="checkbox" name="loc" <?php if ($site['loc'] == 'yes') { echo 'checked="checked"'; } ?>> lọc
	<input type="checkbox" name="nl2p" <?php if ($site['nl2p'] == 'yes') { echo 'checked="checked"'; } ?>> nl2p
	<textarea name="remove" style="width: 100%;"><?php echo $site['remove'] ?></textarea>
	<input type="submit" name="submit" value="Option">
</form>
<hr>
<a href="regex.php?slug=<?php echo $site['slug'] ?>">Regex</a>
<hr>
<a href="site.php">Site</a> | <a href="bookmark.php">Bookmark</a> | <a href="note.php">Note</a>
