<?php
error_reporting(E_ALL ^ E_DEPRECATED);
include("configuration.php");

    $con = mysql_connect($server,$db_user,$db_pw);
    if (!$db) {
        die('Could not connect to db: ' . mysql_error());
    }
    mysql_select_db($db, $con);


if(isset($_POST['token']) && is_array($_POST['token']))
{
if(count($_POST['token']) > 0)
{
$token = $_POST['token'];
//$token= json_decode( $token, TRUE );
$token_select = "";
$s = 0;
$token_start = "";
for($i=0; $i < count($token); $i++)
   {
   $result = mysql_query("SELECT * FROM token WHERE token='".$token[$i]."'");
   while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
   {
   	if($s == 0)
   	{
   	$s = 1;
   	$token_start = "id=".$row['module_id'];
   	}
   	else
   	{
   	$token_select = $token_select." OR id=".$row['module_id'];
   	}
   }
   }

	if($s == 1)
	{
	$result = mysql_query("select * from modules WHERE verified=1 OR ".$token_start.$token_select, $con); 
	}
	else
	{
    $result = mysql_query("select * from modules WHERE verified=1", $con); 
	}
    $json_response = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $row_array['id'] = $row['id'];
        $row_array['category'] = $row['category'];
        $row_array['author'] = $row['author'];
        $row_array['homepage'] = $row['homepage'];
        $row_array['icon'] = $row['icon'];
        $row_array['version'] = $row['version'];
        $row_array['maturity'] = $row['maturity'];
        $row_array['title'] = $row['title'];
        $row_array['desc'] = $row['description'];
        $row_array['last_updated'] = $row['last_updated'];
        $row_array['user_id'] = $row['user_id'];
        $row_array['modulename'] = $row['modulename'];
        $row_array['detail_images'] = $row['detail_images'];
        //push the values in the array
        array_push($json_response,$row_array);
    }
    
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($json_response);
}

}
?>
