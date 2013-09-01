<?php
$con = mysql_connect("localhost","smdtest1_teclub","asdfasdf88");
if (!$con) {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("smdtest1_28_club", $con);

mysql_query('delete from smdtest1_28_club.player_team_year');

for ($i=1; $i < 32; $i++) { 
	mysql_query('insert into player_team_year (player_id, team_id, year) values ('.$i.', '.$_GET["team_".$i].',\'2012\')');
}

mysql_close($con);

header( 'Location: http://28club.smd-test.com/') ;
?>
