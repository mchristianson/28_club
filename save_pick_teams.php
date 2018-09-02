<?php
$con = ($GLOBALS["___mysqli_ston"] = mysqli_connect("localhost", "smdtest1_teclub", "asdfasdf88"));
if (!$con) {
  die('Could not connect: ' . mysqli_error($GLOBALS["___mysqli_ston"]));
  }

mysqli_select_db( $con, smdtest1_28_club);

//mysql_query('delete from smdtest1_28_club.player_team_year');

for ($i=1; $i < 48; $i++) {
  if ($_GET["team_".$i] != null) {
    mysqli_query($GLOBALS["___mysqli_ston"], 'insert into player_team_year (player_id, team_id, year) values ('.$i.', '.$_GET["team_".$i].',\'2018\')');
  }
}

((is_null($___mysqli_res = mysqli_close($con))) ? false : $___mysqli_res);

//header( 'Location: http://28club.smd-test.com/') ;
?>
