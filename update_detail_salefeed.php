<?php
include ('conn.php');
session_start();
if($_SESSION["Username"]=="") // ตรวจสอบว่าผ่านการ login หรือไม่
{


header('location:login.php');
exit();
}
$username=$_SESSION["Username"];
include 'header.php';
?>
<?php
  $salefeed_id = $_GET['salefeed_id'];
  $feed_id = $_GET['feed_id'];
  $count = $_GET['count'];
  $price = $_GET['price'];
  $unit_id = $_GET['unit_id'];

  $sql = "INSERT INTO detail_sale_feed (salefeed_id,feed_id,count,price,unit_id) VALUES ('$salefeed_id','$feed_id','$count','$price','$unit_id')";
  mysql_query($sql,$connect1);
  $select = "SELECT * FROM stock WHERE name_stock ='อาหารทางสายยาง'";
  $res = mysql_query($select,$connect1);
  $rw = mysql_fetch_array($res);
  $id_stock = $rw['id_stock'];
  $sql = "INSERT INTO stock_detail (stock_id,mat_id,count,unit_id) VALUES ('$id_stock','$feed_id','-$count','$unit_id')";
  mysql_query($sql,$connect1);
  header("LOCATION:sale_feed_con.php?salefeed_id=$salefeed_id");
 ?>
