<!DOCTYPE html>
<?php
$con = ($GLOBALS["___mysqli_ston"] = mysqli_connect("localhost", "smdtest1_teclub", "asdfasdf88"));
if (!$con) {
  die('Could not connect: ' . mysqli_error($GLOBALS["___mysqli_ston"]));
  }

mysqli_select_db( $con, smdtest1_28_club);
$teamSql = "select * from team";
$playerSql = "select * from player";
?>

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>2018 28 Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Matt Christianson">
	<link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
th, td {
	text-align:center;
}
</style>
</head>
<body style="margin:20px;">
<?php
// print_r($scoreArray);
$result = mysqli_query($GLOBALS["___mysqli_ston"], $playerSql);
?>


<h1>2018 28 Club</h1>
<p class="lead">Trevor Winter's 28 Club</p>
<form action="save_pick_teams.php">
<table class="table">
<?php while($row = mysqli_fetch_array($result)) { ?>

	<tr>
		<td><?=$row['first_name']. ' ' . $row['last_name']?></td>
		<td>
			<select name="team_<?=$row['id']?>">
        <option value="">none</option>
				<?php
				$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $teamSql);
				while($innerRow = mysqli_fetch_array($result2)) { ?>
          <option value="<?=$innerRow['id']?>"><?=$innerRow['nickname']?></option>
				<?php } ?>

			</select>
		</td>
	</tr>
<?php } ?>
</table>
<button class="btn btn-large btn-primary" type="submit">Submit</button>
</form>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="js/bootstrap.js"></script>
</body>
</html>
