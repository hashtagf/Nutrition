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
  $id_raw = $_GET['id_raw'];
  $raw = $_GET['raw'];
  $date = $_GET['date'];
  $sql = "INSERT INTO raw_system (id_raw,name_raw,date) VALUES ('$id_raw','$raw','$date')";
  mysql_query($sql,$connect1);
  $sql = "SELECT * FROM raw_system WHERE id_raw = '$id_raw'";
  $res = mysql_query($sql,$connect1);
  $row = mysql_fetch_array($res);
  $sel0 = "SELECT * FROM detail_raw WHERE id_raw = '$id_raw'";
  $res0 = mysql_query($sel0,$connect1);
  $num_rows = mysql_num_rows($res0);
  if ($num_rows <= 0) {
    $raw = $row['name_raw'];
    $sql2 = "SELECT * FROM raw_system WHERE name_raw = '$raw' ORDER BY id_raw";
    $res2 = mysql_query($sql2,$connect1);
    $row2 = mysql_fetch_array($res2);
    $id_raw2 = $row2['id_raw'];
    $sel = "SELECT * FROM detail_raw WHERE id_raw = '$id_raw2'";
    $res3 = mysql_query($sel,$connect1);
    while ($row3 = mysql_fetch_array($res3)){
      $mat_id = $row3['mat_id'];
      $unit_id = $row3['unit_id'];
      $insert = "INSERT INTO detail_raw (id_raw,mat_id,count,unit_id) VALUES('$id_raw','$mat_id','0','$unit_id')";
      mysql_query($insert,$connect1);
    }
  }

?>

<div class="container">
  <div class="jumbotron">
    <br>
    <p>ระบบจัดการเบิกวัตถุดิบที่ใช้ในการทำอาหาร</p>
       <div class="modal-body">
         <h4>
          <form action="#" method="get">
            <input type="hidden" name="id_raw" value="<?php echo $id_raw; ?>">
            <input type="hidden" name="raw" value="<?php echo $raw; ?>">
            <input type="hidden" name="date" value="<?php echo $date; ?>">
         <table>
           <tr >
             <td style="padding-bottom : 10px;">รหัสการเบิกวัตถุดิบ </td>
             <td style="padding-bottom : 10px;">&nbsp; : &nbsp;</td>
             <td style="padding-bottom : 10px;"><?php echo $id_raw; ?></td>
             <td style="padding-bottom : 10px; width:10%;"></td>
             <td style="padding-bottom : 10px;">เมนูอาหาร </td>
             <td style="padding-bottom : 10px;">&nbsp; : &nbsp;</td>
             <td style="padding-bottom : 10px;"> <?php echo $row['name_raw']; ?> </td>
           </tr>
           <tr>
             <td style="padding-bottom : 10px;">เลือกวัตถุดิบ </td>
             <td style="padding-bottom : 10px;">&nbsp; : &nbsp;</td>
             <td style="padding-bottom : 10px;">
               <select class="" name="id_stock" required >
                 <option value=""  disabled selected>เลือกประเภทวัตถุดิบ</option>
                 <?php
                     $sql = "SELECT DISTINCT * FROM stock";
                     $select = mysql_query($sql,$connect1);
                     while ($row = mysql_fetch_array($select)) {
                 ?>
                    <?php if ($row['id_stock'] != 'MT-06'): ?>
                       <option value="<?php echo $row['id_stock']; ?>" >
                         <?php echo $row['id_stock']; ?> // <?php echo $row['name_stock']; ?>
                       </option>
                     <?php endif; ?>
                 <?php
                     }
                  ?>
               </select> <input type="submit" value="ค้นหา" class="btn btn-success">
             </td>
           </tr>
         </table>
         </form>

         </h4>
         <?php
             if ($_GET && isset($_GET['id_stock'])):
               $ID = $_GET['id_stock'];
           ?>
           <table class="table table-striped table-bordered">
             <tr class="warning">
               <?php
                   $sel = "SELECT * FROM stock WHERE id_stock = '$ID'";
                   $select = mysql_query($sel,$connect1);
                   $st = mysql_fetch_array($select);
                ?>
               <th><div align="center"><?php echo $ID; ?> : <?php echo $st['name_stock']; ?></div></th>
             </tr>
             <tr class="warning">
               <th width = "20%"><div align="center">ลำดับ</th>
               <th><div align="center">รหัสวัตถุดิบ</th>
               <th><div align="center">ชื่อวัตถุดิบ</th>
               <th width="15%"><div align="center">จำนวนคงเหลือ</th>
               <th><div align="center">หน่วยนับ</th>
               <th width="10%"><div align="center">จำนวนที่เบิก</th>
               <th width="10%"><div align="center">เบิก</th>
             </tr>
             <?php
               $sql = "SELECT SUM(count),stock_detail.mat_id,mat_name,unit_name,stock_detail.unit_id FROM stock_detail JOIN material ON stock_detail.mat_id = material.mat_id
                                                   JOIN unit ON unit.unit_id = stock_detail.unit_id
                                                   WHERE stock_id = '$ID' GROUP BY mat_name";
                if ($ID == 'MT-06') {
                  $sql = "SELECT SUM(count),stock_detail.mat_id,feed_name,unit_name FROM stock_detail
                                                      JOIN feed ON stock_detail.mat_id = feed.feed_id JOIN unit ON unit.unit_id = stock_detail.unit_id
                                                      GROUP BY feed_name";
                }
               $objQuery = mysql_query($sql,$connect1);
               $i = 1;
             while ($objReSult = mysql_fetch_array($objQuery)) {

           ?>
           <form class="" action="update_detail_raw.php" method="GET">
             <?php if ($objReSult["SUM(count)"] > 0): ?>
               <tr class ="info">
                 <td><div class="text-center"><?php echo $i++; ?></div></td>
                 <td><div class="text-left"><?php echo $objReSult['mat_id']; ?></div></td>
                 <?php if ($objReSult["feed_name"] != NULL): ?>
                   <td><div align = "left"><? echo $objReSult["feed_name"];?></div></td>
                 <?php else: ?>
                   <td><div align = "left"><? echo $objReSult["mat_name"];?></div></td>
                 <?php endif; ?>
                 <td><div align = "right"><? echo $objReSult["SUM(count)"];?></div></td>
                 <td><div align = "left"><? echo $objReSult["unit_name"];?></div></td>
                 <td><div align = "right"><input type="number" name="count" min="1" max="<? echo $objReSult["SUM(count)"];?>" required></div></td>
                 <td><div align = "center">
                         <button type="submit" name="button" class = "btn btn-success">เพิ่มในรายการ +</button>
                         <input type="hidden" name="mat_id" value="<?php echo $objReSult['mat_id']; ?>">
                         <input type="hidden" name="id_stock" value="<?php echo $ID; ?>">
                         <input type="hidden" name="unit_id" value="<?php echo $objReSult["unit_id"]; ?>">
                         <input type="hidden" name="id_raw" value="<?php echo $id_raw; ?>">
                         <input type="hidden" name="raw" value="<?php echo $raw; ?>">
                         <input type="hidden" name="date" value="<?php echo $date; ?>">
                     </div>
                 </td>
               </tr>
             <?php endif; ?>
             </form>
             <?
           }
           ?>
           </table>
         <?php endif; ?>
       </div>
  </div>
  <div class="detail">
    <form action="finish_raw.php" method="GET">
      <input type="hidden" name="stat" value="show">
    <table class="table table-striped table-bordered">
      <tr class="warning">
        <th width="4%"><div align="center">ลำดับ</div></th>
        <th width="10%"><div align="center">ประเภทวัตถุดิบ</div></th>
        <th width="15%"><div align="center">ชื่อวัตถุดิบ</div></th>
        <th width="7.5%"><div align="center">จำนวนที่เบิก</div></th>
        <th width="7.5%"><div align="center">จำนวนคงเหลือ</div></th>
        <th width="10%"><div align="center">หน่วยนับ</div></th>
        <th width="10%"><div align="center">ลบ</div></th>
      </tr>

    <?

        $sql = "SELECT d.mat_id,m.mat_name,SUM(count),u.unit_name,u.unit_id FROM detail_raw d LEFT JOIN material m ON d.mat_id = m.mat_id
                                                 JOIN unit u ON d.unit_id = u.unit_id
                                                 WHERE d.id_raw = '$id_raw' GROUP BY m.mat_name";
        $objQuery = mysql_query($sql,$connect1);
        $i = 1;
        while ($objReSult = mysql_fetch_array($objQuery)) {

    ?>
      <tr class ="info">
         <?php
            $mat = $objReSult["mat_name"];
            $matID = $objReSult["mat_id"];
          ?>
        <?php
           $sqlb = "SELECT stock_detail.mat_id,stock_detail.stock_id,stock.name_stock,SUM(stock_detail.count) FROM stock_detail
                            JOIN stock ON stock_detail.stock_id = stock.id_stock
                            JOIN material ON stock_detail.mat_id = material.mat_id
                            WHERE material.mat_name = '$mat' GROUP BY material.mat_name";
           $query = mysql_query($sqlb,$connect1);
           $row = mysql_fetch_array($query);
         ?>
        <td><div align = "center"><?php echo $i; ?></div></td>
        <td><div align = "left"><? echo $row["name_stock"];?></div></td>
        <?php if ($objReSult['feed_id'] != NULL): ?>
          <td><div align = "left"><? echo $objReSult["feed_name"];?></div></td>
        <?php else: ?>
          <td><div align = "left"><? echo $objReSult["mat_name"];?></div></td>
        <?php endif; ?>
        <td>
            <?php if ($row['SUM(stock_detail.count)'] < $objReSult["SUM(count)"]): ?>
                <input type="number" name="count<?php echo $i; ?>" value="<? echo $objReSult["SUM(count)"];?>" min="0" max="<?php echo $objReSult['SUM(count)']; ?>" required>
            <?php else: ?>
              <div align = "right">  <input type="number" name="count<?php echo $i; ?>" value="<? echo $objReSult["SUM(count)"];?>" min="0" max="<?php echo $row['SUM(stock_detail.count)']; ?>" required>
            </div><?php endif; ?>
        </td>
        <td>
          <div align = "right">
            <?php if ($row['SUM(stock_detail.count)'] < $objReSult["SUM(count)"]): ?>
               <?php echo $objReSult['SUM(count)']; ?>
            <?php else: ?>
              <?php echo $row['SUM(stock_detail.count)']; ?>
            <?php endif; ?>
          </div>
      </td>
        <td><div align = "left"><? echo $objReSult["unit_name"];?></div></td>
          <td align="center">
            <a href="delete_detail_raw.php?id_raw=<?php echo $id_raw; ?>&mat_id=<?php echo $objReSult["mat_id"]; ?>&count=<?php echo $objReSult['SUM(count)']; ?>
              &stock_id=<?php echo $row['stock_id']; ?>&unit_id=<?php echo $objReSult['unit_id']; ?>
              &date=<?php echo $date; ?>&raw=<?php echo $raw; ?>"
            onclick="return confirm('ยืนยันการลบข้อมูล')"><b><font color="red"><img src='img/delete.png' width=25></font></b></a>
          </td>
      </tr>
          <input type="hidden" name="id_raw<?php echo $i; ?>" value="<?php echo $id_raw; ?>">
          <input type="hidden" name="mat_id<?php echo $i; ?>" value="<?php echo $matID; ?>">
          <input type="hidden" name="id_stock<?php echo $i; ?>" value="<?php echo $row['stock_id']; ?>">
          <input type="hidden" name="unit_id<?php echo $i; ?>" value="<?php echo $objReSult["unit_id"]; ?>">
      <?
      $i++;
    }
    ?>
      <tr>
        <td colspan="7" class="text-right">
          <?php if (isset($_GET['edit'])) : ?>
              <a href="raw.php"><input type="button" class="btn btn-danger" value="ย้อนกลับ"></a>
          <?php else: ?>
            <a href="delete_raw.php?id_raw=<?php echo $id_raw; ?>"><input type="button" class="btn btn-danger" value="ยกเลิก"  onclick="return confirm('ยืนยันการยกเลิกข้อมูล')"></a>
          <?php endif; ?>
          <input type="submit" class="btn btn-success" name = "บันทึกข้อมูลวัตถุดิบ" value="บันทึกข้อมูลวัตถุดิบ" onclick="return confirm('บันทึกข้อมูลนี้?')">
        </td>
      </tr>
    </table>

    </form>
  </div>
</div>
<?php include 'footer.php'; ?>
