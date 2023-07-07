<?php

require_once(__DIR__ . "/text_parsing/PHPMailer/PHPMailerAutoload.php");

define('DB_SERVER', 'dynalytics.czmshulrueed.us-east-2.rds.amazonaws.com');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', 'gbSLLJwPWLLeM2iaM6HK');
define('DB_DATABASE', 'dynalytics');

define('MAIL_HOST',"ssl://smtp.gmail.com");
define('MAIL_USERNAME',"gaikwadsharad1512@gmail.com");
define('MAIL_PASSWORD',"Sharad@1512");
define('MAIL_PORT',"465");

$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

$url = 'https://thedynaco.com/check_connection.php';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
$data = curl_exec($curl);
curl_close($curl);

if ($data != 'active') {
    $to='amitsolanki78@yahoo.com'; 
    $subject='Apache status'; 
    $body='Server is not running';
    send_mail($to, $subject, $body);
}else{
    $emails =array('info@securemetasys.com','dealer@mindpowerit.com','dynalitics@dynalitics.com');
    for ($i=0; $i < 3; $i++) 
    {
        $email = mysqli_real_escape_string($db,$emails[$i]);
        $mypassword = mysqli_real_escape_string($db,'453dcc12c14a599d5e6ca8350e5366debb3e93761');
      
        $sql = "SELECT id FROM users WHERE email = '$email' and password = '$mypassword'";
        $result = mysqli_query($db,$sql);

        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
        $name='';
        if ($i == 0) {
            $name='Admin'; 
        }
        if ($i == 1) {
            $name='Dealer';
        }
        if ($i == 2) {
            $name='Company';
        }
        if (empty($row)) 
        {
            $to='amitsolanki78@yahoo.com';  
            $subject='User login status'; 
            $body=$name.' User not able to login on server';     
            send_mail($to, $subject, $body);
        }
    }
}

function send_mail($to, $subject, $body){
  try 
  {
        $fromName = 'Sharad';
        $fromEmail = 'gaikwadsharad1512@gmail.com';
        
        //$mail = new PHPMailer;
        $mail = new PHPMailer();

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = MAIL_HOST;  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = MAIL_USERNAME;                 // SMTP username
        $mail->Password = MAIL_PASSWORD;                           // SMTP password
        $mail->SMTPSecure = 'tls';                       // TCP port to connect to
        $mail->Port = MAIL_PORT;                                    // TCP port to connect to

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);     // Add a recipient
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;

        $mail->Body = $body;

        if (!$mail->send()) {
            echo "Mail could not be sent to $to \n";
            echo 'Mailer Error: ' . $mail->ErrorInfo ."\n";
        } else {
            echo "Mail has been sent to $to \n";
        }
    } catch (Exception $e) {
        echo "Mail could not be sent.Exception Arrise at SendEmail.php at Line No 128 \n";
        return false;
    }
}

?>