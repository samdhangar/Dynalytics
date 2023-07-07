<?php 
if(!empty($_POST)){
	echo "<pre>";
	print_r($_POST);
	exit;	
}
?>
<!DOCTYPE html>
<html>
<body>

<h2>HTML Forms</h2>

<form action="" id="UserEditProfileForm" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<?php
		for($i = 0;$i<100;$++){
			echo '<input type="text" id="fname'.$i.'" name="fname'.$i.'" value="fname'.$i.'"><br>';
		}
	?>
  <input type="submit" value="Submit">
</form> 

<p>If you click the "Submit" button, the form-data will be sent to a page called "/action_page.php".</p>

</body>
</html>
