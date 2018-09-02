<!DOCTYPE html>
<?php
require('simple_html_dom.php');

$con = ($GLOBALS["___mysqli_ston"] = mysqli_connect("localhost", "smdtest1_teclub", "asdfasdf88"));
if (!$con) {
  die('Could not connect: ' . mysqli_error($GLOBALS["___mysqli_ston"]));
  }

mysqli_select_db( $con, smdtest1_28_club);
$sql = "select p.first_name, p.last_name, t.nickname, t.abbr, t.id from player p join player_team_year pt on pt.player_id = p.id join team t on pt.team_id = t.id order by p.last_name asc";

$target = "http://www.nfl.com/scores/2018/REG";

$date = new DateTime();
$week = ($date->format("W")) - 35;
$week = $_GET['week']?$_GET['week']:$week;
$html = file_get_html($target.$week);
for ($i=0; $i < 32; $i++) {
	$scoreArray[$html->find('p[class=team-name] a',$i)->innertext] = $html->find('p[class=total-score]',$i)->innertext;
//	$timeArray[$html->find('p[class=team-name] a',$i)->innertext] = $html->find('div[class=new-score-box] span[class=time-left]',$i)->innertext;
	//$dateArray[$html->find('p[class=team-name] a',$i)->innertext] = $html->find('div[class=new-score-box-heading] span[class=date]',$i)->innertext;
}
foreach($html->find('div.scorebox-wrapper') as $game) {
	$timeArray[$game->find('p.team-name a',0)->innertext] = $game->find('span.time-left',0)->innertext;
	$timeArray[$game->find('p.team-name a',1)->innertext] = $game->find('span.time-left',0)->innertext;
}

$url="http://www.nfl.com/liveupdate/scores/scores.json?random=".rand();

$json = file_get_contents($url);
$data = json_decode($json, TRUE);
$liveData = array();
$redZone = array();
foreach ($data as $key => $value) {
  $dateString = $key;
  $home = $value['home'];
	$homeTeamAbbr = $home['abbr'];
	$homeTeamScore = $home['score']['T'];
	$scoreArray[$homeTeamAbbr] = $homeTeamScore;

	$awayTeamAbbr = $value['away']['abbr'];
	$awayTeamScore = $value['away']['score']['T'];
	$scoreArray[$awayTeamAbbr] = $awayTeamScore;
	if ($value['qtr'] != null && 0 !== strpos(strtolower($value['qtr']), 'final')) {
		$liveStatus = addPostfix($value['down']).' &amp; '.$value['togo'].' '.$value['posteam'].' ball on the '.$value['yl'].'<br/>'.$value['clock'].' remaining in the '.addPostfix($value['qtr']);
	}
  $dateArray[$homeTeamAbbr] = substr($key, 4, 2).'/'.substr($key, 6, 2).'/'.substr($key, 0, 4);
  $dateArray[$awayTeamAbbr] = substr($key, 4, 2).'/'.substr($key, 6, 2).'/'.substr($key, 0, 4);;
	$liveData[$homeTeamAbbr] = $liveStatus;
	$liveData[$awayTeamAbbr] = $liveStatus;
	$redZone[$homeTeamAbbr] = ($value['redzone'] && ($value['posteam'] == $homeTeamAbbr));
	$redZone[$awayTeamAbbr] = ($value['redzone'] && ($value['posteam'] == $awayTeamAbbr));
}
function addPostfix($number) {
	if ($number == 1) return "1st";
	if ($number == 2) return "2nd";
	if ($number == 3) return "3rd";
	return $number;
}
// print_r($timeArray);

$purse =  array(
1	=> array(),
2	=> array(),
3	=> array(),
4	=> array('Panthers', 'Redskins'),
5	=> array('Bears', 'Buccaneers'),
6	=> array('Lions', 'Saints'),
7	=> array('Packers', 'Raiders', 'Seahawks', 'Steelers'),
8	=> array('Chargers', 'Cowboys', 'Falcons', 'Titans'),
9	=> array('Bengals', 'Cardinals', 'Colts', 'Eagles', 'Giants', 'Jaguars'),
10	=> array('Broncos', 'Ravens', 'Texans', 'Vikings'),
11	=> array('Bills', 'Browns', 'Dolphins', '49ers', 'Jets', 'Patriots'),
12	=> array('Chiefs', 'Rams'),
13	=> array(),
14	=> array(),
15	=> array(),
16	=> array(),
17	=> array()
);


?>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>NFL 28 Club</title>
	<meta name="author" content="Matt Christianson">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="css/club.css" rel="stylesheet">
    <style>
th, td {
	text-align:center;
}
.redzone {
	color:#f00;
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="js/bootstrap.js"></script>
<script>
$(function() {
	initTimeout();
});
function initTimeout() {
	setTimeout(hiliteRedzone,2000);
}
function hiliteRedzone() {
		$(".redzone").fadeOut("fast").delay(25).fadeIn("fast");
		initTimeout();
}
</script>
</head>
<body>
	<?php
// print_r($dateArray);
//print_r($scoreArray);
	?>
<header class="jumbotron subhead" id="overview">
  <div class="container">
    <h1>NFL 28 Club</h1>
    <p class="lead">Purse is <?=money_format('$%i', (16 - (count($purse[$week]) / 2)) * 6.25)?> for week <?=$week?>.</p>
  </div>
</header>
<div class="container">
<div class="row-fluid">
  <div class="span8">

<div class="btn-group dropdown">
	<button class="btn btn-large btn-block dropdown-toggle" data-toggle="dropdown">Week <?=$week?><span class="caret"></span></button>
	<ul class="dropdown-menu">
		<?php
		for ($i=1; $i < 17; $i++) {
			?>
		<li><a href="index.php?week=<?=$i?>">Week <?=$i?> - Purse: <?=money_format('$%i', (16 - (count($purse[$i]) / 2)) * 6.25)?></a></li>
		<?php } ?>
	</ul>
</div>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Name</th>
			<th style="text-align:center;">Team</th>
			<th style="text-align:center;">Live Scores Week <?=$week?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		while($row = mysqli_fetch_array($result)) {
			$status = "";
			$score = $scoreArray[$row['nickname']];
			if (strpos($score, "score")) {
				$score = $scoreArray[$row['abbr']];
			}
		?>
		<tr class="<?
		if ($score == "28") {
			echo 'success';
		} elseif ($score < "28") {
			echo 'none';
		} elseif ($score > "28") {
			echo 'error';
		}
		?>">
			<td><?=$row['first_name'] . ' ' . $row['last_name']?></td>
			<td style="text-align:center;"><img src="http://i.nflcdn.com/static/site/7.5/img/logos/teams-matte-80x53/<?=$row['abbr']?>.png" class="team-logo" alt="<?=$row['nickname']?>"><br/><?=$row['nickname']?></td>
			<td style="text-align:center;"><?=$dateArray[$row['abbr']]?><br/><?=$timeArray[$row['nickname']]?><h1 class="<?if ($redZone[$row['abbr']]) { echo " redzone"; }?>"><?=($score == null && $liveData[$row['abbr']] == null) ? 'BYE' : $score ?></h1></td>
		</tr>
		<?php
		if ($liveData[$row['abbr']]) { ?>
		<tr>
			<td colspan="3" style="border-top:0;text-align:center;">
				<small><?=$liveData[$row['abbr']]?></small>
			</td>
		</tr>
<?php
		}
	}
	((is_null($___mysqli_res = mysqli_close($con))) ? false : $___mysqli_res);
?>
	</tbody>
</table>
</div>
  <div class="span4">
  	<h3>Rules:</h3>
  	<ul>
  		<li>Team must score <strong>exactly</strong> 28 points to win.</li>
  		<li>If no team scores <strong>exactly</strong> 28 in a given week, the purse carries over to the next.</li>
  		<li>If more than one team scores <strong>exactly</strong> 28 in a week, it will be split evenly.</li>
  		<li>If no team scores <strong>exactly</strong> 28 in week 17, it will go to the closest to 28 <em>without</em> going over.</li>
  		<li>Entry Fee is $50 per team.</li>
  		<li>The board is open to the first 32 that pay.</li>
	</ul>

	<h3>Purses</h3>
	<table class="table">
		<thead>
		<tr>
			<th style="text-align:center;">Week</th>
			<th style="text-align:center;">Open Dates</th>
			<th style="text-align:center;">Games</th>
			<th style="text-align:center;">Purse</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($purse as $key => $value) { ?>
		<tr>
			<td style="text-align:center;"><?=$key?></td>
			<td style="text-align:center;"><?=implode(", ", $value)?></td>
			<td style="text-align:center;"><?=16 - (count($value)/2)?></td>
			<td style="text-align:center;"><?=money_format('$%i', (16 - (count($value)/2)) * 6.25)?></td>
		</tr>
		<?php } ?>
	</tbody>
	</table>
		</div>
</div>
</div>
</body>
</html>
