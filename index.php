<!DOCTYPE html>
<?php
require('simple_html_dom.php');

$con = mysql_connect("localhost","smdtest1_teclub","asdfasdf88");
if (!$con) {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("smdtest1_28_club", $con);
$sql = "select p.first_name, p.last_name, t.nickname, t.abbr, t.id from player p join player_team_year pt on pt.player_id = p.id join team t on pt.team_id = t.id order by p.last_name asc";

$target = "http://www.nfl.com/scores/2014/REG";

$week = $_GET['week']?$_GET['week']:1;
$html = file_get_html($target.$week);
for ($i=0; $i < 32; $i++) { 
	$scoreArray[$html->find('p[class=team-name] a',$i)->innertext] = $html->find('p[class=total-score]',$i)->innertext;
	$dateArray[$html->find('p[class=team-name] a',$i)->innertext] = $html->find('div[class=new-score-box-heading] span[class=date]',$i)->innertext;
}
$url="http://www.nfl.com/liveupdate/scores/scores.json?random=".rand();

$json = file_get_contents($url); 
$data = json_decode($json, TRUE);
$liveData = array();
$redZone = array();
foreach ($data as $key => $value) {
	$home = $value['home'];
	$homeTeamAbbr = $home['abbr'];
	$homeTeamScore = $home['score']['T'];
	$scoreArray[$homeTeamAbbr] = $homeTeamScore;
	
	$awayTeamAbbr = $value['away']['abbr'];
	$awayTeamScore = $value['away']['score']['T'];
	$scoreArray[$awayTeamAbbr] = $awayTeamScore;
	if ($value['qtr'] != 'Final') {
		$liveStatus = addPostfix($value['down']).' &amp; '.$value['togo'].' '.$value['posteam'].' ball on the '.$value['yl'].'<br/>'.$value['clock'].' remaining in the '.addPostfix($value['qtr']);
	}
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
// print_r($scoreArray);

$purse =  array(
1	=> array('None',16,	80),
2	=> array('None',16,	80),
3	=> array('None',16, 80),
4	=> array('Cardinals, Bengals, Browns, Broncos, Seahawks, Rams',			13,	65),
5	=> array('Dolphins, Raiders',	15,	75),
6	=> array('Chiefs, Saints',		15,	75),
7	=> array('Eagles, Buccaneers',	15,	75),
8	=> array('Giants, 49ers',	    15, 75),
9	=> array('Falcons, Bills, Bears, Lions, Packers, Titans',        13,	65	),
10	=> array('Texans, Colts, Vikings, Patriots, Chargers, Redskins', 13,	65	),
11	=> array('Ravens, Cowboys, Jaguars, Jets',			             14,	70	),
12	=> array('Panthers, Steelers',	15,	75),
13	=> array('None',	16	,	80	),
14	=> array('None',	16	,	80	),
15	=> array('None',	16	,	80	),
16	=> array('None',	16	,	80	),
17	=> array('None',	16	,	80	)
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
    <p class="lead">Purse is $<?=$purse[$week][2]?> for week <?=$week?>.</p>
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
		<li><a href="index.php?week=<?=$i?>">Week <?=$i?> - Purse: $<?=$purse[$i][2]?></a></li>
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
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)) { 
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
			<td style="text-align:center;"><img src="http://i.nflcdn.com/static/site/4.5/img/logos/teams-matte-80x53/<?=$row['abbr']?>.png" class="team-logo" alt="<?=$row['nickname']?>"><br/><?=$row['nickname']?></td>
			<td style="text-align:center;"><?=$dateArray[$row['nickname']]?><h1 class="<?if ($redZone[$row['abbr']]) { echo " redzone"; }?>"><?=($score == null && $liveData[$row['abbr']] == null) ? 'BYE' : $score ?></h1><small></td>
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
	mysql_close($con);
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
  		<li>Entry Fee is $40 per team.</li>
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
			<td style="text-align:center;"><?=$value[0]?></td>
			<td style="text-align:center;"><?=$value[1]?></td>
			<td style="text-align:center;">$<?=$value[2]?></td>
		</tr>
		<?php } ?>
	</tbody>
	</table>
		</div>
</div>
</div>
</body>
</html>