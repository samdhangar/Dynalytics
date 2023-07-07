<?php
 $dbc=mysqli_connect('localhost','root','','cake2');
 
echo $sql="SELECT * 
FROM   manager_log where logon_datetime > logoff_datetime";
 $result=mysqli_query($dbc,$sql);
 while ($row=$result->fetch_assoc()) {
 	$date=$row['logon_datetime'];
   
 	 $login_time=date('Y-m-d 23:m:s', strtotime($row['logon_datetime']) );
 	  
 	   echo $sql3="UPDATE manager_log set   logoff_datetime='$login_time' where id='".$row['id']."' ";
 	  mysqli_query($dbc,$sql3);
 	    echo "<br>";

 }

 


?>