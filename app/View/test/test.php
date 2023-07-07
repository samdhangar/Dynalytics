<?php
 $dbc=mysqli_connect('localhost','root','P@ssword0','dynalitics');
 $sql="SELECT * 
FROM bill_adjustments
INNER JOIN file_processing_detail ON bill_adjustments.file_processing_detail_id = file_processing_detail.id
WHERE file_processing_detail.company_id =30
LIMIT 0 , 30";
 $result=mysqli_query($dbc,$sql);
 while ($row=$result->fetch_assoc()) {
 
 	/*$date=$row['file_date'];
 	echo $futureDate=date('Y-m-d H:m:s', strtotime('+4 year', strtotime($date)) );*/
 	$date2=$row['created_date'];
 	echo $futureDate2=date('Y-m-d H:m:s', strtotime('+3 year', strtotime($date2)) );
 	$date3=$row['updated_date'];
 	echo $futureDate3=date('Y-m-d H:m:s', strtotime('+3 year', strtotime($date3)) );
 	echo $sql2="UPDATE file_processing_detail set   created_date='$futureDate2' , updated_date='$futureDate3' where id='".$row['id']."'";
 	mysqli_query($dbc,$sql2);

 }


?>