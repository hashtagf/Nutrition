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
?>
<div class="container">
  <div class="jumbotron">
    <br>
    <p>ข้อมูลการจัดการวัตถุดิบในเมนูอาหาร</p>
       <div class="modal-body">
         <h4>
          <form action="#" method="get">
            <input type="hidden" name="id_raw" value="<?php echo $id_raw; ?>">
            <input type="hidden" name="raw" value="<?php echo $raw; ?>">
            <input type="hidden" name="date" value="<?php echo $date; ?>">
         <table>
           <tr >
             <td style="padding-bottom : 10px;">รหัสจัดการวัตถุดิบ </td>
             <td style="padding-bottom : 10px;">&nbsp; : &nbsp;</td>
             <td style="padding-bottom : 10px;"><?php echo $id_raw; ?></td>
             <td style="padding-bottom : 10px; width:10%;"></td>
             <td style="padding-bottom : 10px;">เมนูอาหาร </td>
             <td style="padding-bottom : 10px;">&nbsp; : &nbsp;</td>
             <td style="padding-bottom : 10px;"> <?php echo $raw; ?> </td>
           </tr>
           <tr>
             <td style="padding-bottom : 10px;">วันที่ </td>
             <td style="padding-bottom : 10px;">&nbsp; : &nbsp;</td>
             <td style="padding-bottom : 10px;"> <?php echo $date; ?></td>
           </tr>
           <tr>
             <td style="padding-bottom : 10px;">เลือกวัตถุดิบ </td>
             <td style="padding-bottom : 10px;">&nbsp; : &nbsp;</td>
             <td style="padding-bottom : 10px;">
               <select class="" name="id_stock" required >
                 <option >เลือกประเภทวัตถุดิบ</option>
                 <?php

                     $sql = "SELECT DISTINCT * FROM stock";
                     $select = mysql_query($sql,$connect1);
                     while ($row = mysql_fetch_array($select)) {
                 ?>
                       <option value="<?php echo $row['id_stock']; ?>" >
                         <?php echo $row['id_stock']; ?> // <?php echo $row['name_stock']; ?>
                       </option>
                 <?php
                     }
                  ?>
               </select> <input type="submit" value="เปิด" class="btn btn-success">
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
               <th>ลำดับ</th>
               <th>รหัสวัตถุดิบ</th>
               <th>ชื่อวัตถุดิบ</th>
               <th>จำนวน</th>
               <th>หน่วยนับ</th>
               <th>จำนวนที่เบิก</th>
               <th>เบิก</th>
             </tr>
             <?php
               $sql = "SELECT SUM(count),stock_detail.mat_id,mat_name,feed_name,unit_name,stock_detail.unit_id FROM stock_detail LEFT JOIN material ON stock_detail.mat_id = material.mat_id
                                                   LEFT JOIN feed ON stock_detail.mat_id = feed.feed_id JOIN unit ON unit.unit_id = stock_detail.unit_id
                                                   WHERE stock_id = '$ID' GROUP BY stock_detail.mat_id";
               $objQuery = mysql_query($sql,$connect1);
               $i = 1;
             while ($objReSult = mysql_fetch_array($objQuery)) {

           ?>
           <form class="" action="update_detail_raw.php" method="GET">
             <tr class ="info">
               <td><div class="text-center"><?php echo $i++; ?></div></td>
               <td><div class="text-center"><?php echo $objReSult['mat_id']; ?></div></td>
               <?php if ($objReSult["feed_name"] != NULL): ?>
                 <td><div align = "left"><? echo $objReSult["feed_name"];?></div></td>
               <?php else: ?>
                 <td><div align = "left"><? echo $objReSult["mat_name"];?></div></td>
               <?php endif; ?>
               <td><div align = "left"><? echo $objReSult["SUM(count)"];?></div></td>
               <td><div align = "left"><? echo $objReSult["unit_name"];?></div></td>
               <td><div align = "center"><input type="number" name="count" min="1" max="<? echo $objReSult["SUM(count)"];?>" required></div></td>
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
             </form>
             <?
           }
           ?>
           </table>
         <?php endif; ?>
       </div>
  </div>
  <div class="detail">
    <form action="raw.php" method="POST">
      <input type="hidden" name="stat" value="show">
    <table class="table table-striped table-bordered">
      <tr class="warning">
        <th><div align="center">ลำดับ</div></th>
        <th><div align="center">ประเภทวัตถุดิบ</div></th>
        <th><div align="center">ชื่อวัตถุดิบ</div></th>
        <th><div align="center">จำนวนที่เบิก</div></th>
        <th><div align="center">หน่วยนับ</div></th>
        <th><div align="center">ลบ</div></th>
      </tr>

    <?
        $sql = "SELECT d.mat_id,f.feed_id,f.feed_name,m.mat_name,SUM(count),u.unit_name,u.unit_id FROM detail_raw d LEFT JOIN material m ON d.mat_id = m.mat_id
                                                 LEFT JOIN feed f ON d.mat_id = f.feed_id
                                                 JOIN unit u ON d.unit_id = u.unit_id
                                                 WHERE d.id_raw = '$id_raw' GROUP BY mat_id";
        $objQuery = mysql_query($sql,$connect1);
        $i = 1;
        while ($objReSult = mysql_fetch_array($objQuery)) {

    ?>
      <tr class ="info">
         <?php $mat = $objReSult["mat_id"]; ?>
        <?php
           $sqlb = "SELECT stock_detail.stock_id,stock.name_stock FROM stock_detail JOIN stock ON stock_detail.stock_id = stock.id_stock WHERE mat_id = '$mat' GROUP BY mat_id";
           $query = mysql_query($sqlb,$connect1);
           $row = mysql_fetch_array($query);
         ?>
        <td><div align = "center"><?php echo $i; ?></div></td>
        <td><div align = "center"><? echo $row["name_stock"];?></div></td>
        <?php if ($objReSult['feed_id'] != NULL): ?>
          <td><div align = "left"><? echo $objReSult["feed_name"];?></div></td>
        <?php else: ?>
          <td><div align = "left"><? echo $objReSult["mat_name"];?></div></td>
        <?php endif; ?>
        <td><div align = "left"><? echo $objReSult["SUM(count)"];?></div></td>
        <td><div align = "left"><? echo $objReSult["unit_name"];?></div></td>
          <td align="center">
            <a href="delete_detail_raw.php?id_raw=<?php echo $id_raw; ?>&mat_id=<?php echo $mat; ?>&count=<?php echo $objReSult["SUM(count)"]; ?>
              &stock_id=<?php echo $row['stock_id']; ?>&unit_id=<?php echo $objReSult["unit_id"]; ?>
              &date=<?php echo $date; ?>&raw=<?php echo $raw; ?>"
            onclick="return confirm('ยืนยันการลบข้อมูล')"><b><font color="red"><img src='img/delete.png' width=25></font></b></a>
          </td>
      </tr>
      <?
      $i++;
    }
    ?>
      <tr>
        <td colspan="7" class="text-right">
          <a href="delete_raw.php?id_raw=<?php echo $id_raw; ?>"><input type="button" class="btn btn-danger" value="ยกเลิก"  onclick="return confirm('ยืนยันการยกเลิกข้อมูล')"></a>
          <input type="submit" class="btn btn-success" name = "บันทึกข้อมูลวัตถุดิบ" value="บันทึกข้อมูลวัตถุดิบ">
        </td>
      </tr>
    </table>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
