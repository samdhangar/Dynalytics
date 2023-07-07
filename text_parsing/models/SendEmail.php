<?php

class SendEmail
{
    public static $instance = null;
    public static $fromName;
    public static $fromEmail;
    public static $supportEmail;
    public static $siteName;
    public static $siteUrl;

    public function __construct()
    {
        
    }

    public static function instance()
    {
        if (empty(self::$instance)) {
            self::init();
        }

        return self::$instance;
    }

    public static function init()
    {

        $sqlQuery = "SELECT `key`, `value` FROM `site_configs` where `key` in ('Site.FromEmail', 'Site.ContactEmail', "
            . "'Site.FromName', 'Site.Name', 'Site.SupportEmail', 'Site.SupportPhone', 'Site.Url') ";
        $stmt = Db::getInstance()->prepare($sqlQuery);
        $siteKeys = $responseArr = array(
            "FromEmail" => '',
            "FromName" => '',
            "Name" => '',
            "SupportEmail" => '',
            "SupportPhone" => '',
            "Url" => ''
        );
        $siteKeys = implode(',', array_keys($siteKeys));
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $key = explode('.', $row['key']);
            $responseArr[$key[1]] = $row['value'];
        }
        self::$fromEmail = $responseArr['FromEmail'];
        self::$fromName = $responseArr['FromName'];
        self::$siteName = $responseArr['Name'];
        self::$supportEmail = $responseArr['SupportEmail'];
        self::$siteUrl = $responseArr['Url'];
    }

    public static function send_ticket_mail($inputData = array(), $type = 'company')
    {

        $emailTemplete = self::getTemplate($type . '_ticket');
        if (empty($emailTemplete) || empty($inputData['Dealer']['email']) || empty($inputData['Company']['email'])) {
            return false;
        }
        $email = ($type == 'company') ? $inputData['Company']['email'] : $inputData['Dealer']['email'];
        $replacement = array(
            '{DEALER_NAME}' => isset($inputData['Dealer']['first_name']) ? ucfirst($inputData['Dealer']['first_name']) : '',
            '{COMPANY_NAME}' => isset($inputData['Company']['first_name']) ? ucfirst($inputData['Company']['first_name']) : '',
            '{PHONE_NUMBER}' => isset($inputData['Company']['phone_no']) ? ucfirst($inputData['Company']['phone_no']) : '',
            '{BRANCH_NAME}' => isset($inputData['Branch']['name']) ? ucfirst($inputData['Branch']['name']) : '',
            '{STATION}' => isset($inputData['Branch']['station']) ? ucfirst($inputData['Branch']['station']) : '',
            '{TICKET_DATE}' => isset($inputData['Ticket']['ticket_date']) ? ucfirst($inputData['Ticket']['ticket_date']) : '',
            '{ERROR_MESSAGE}' => isset($inputData['Ticket']['error']) ? ucfirst($inputData['Ticket']['error']) : '',
            '{SITE_LOGIN_URL}' => self::$siteUrl . '/users/login',
            '{SITE_NAME}' => self::$siteName,
            '{SITE_URL}' => self::$siteUrl,
        );
        $emailTemplete['body'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['body']);
        $emailTemplete['subject'] = str_replace(array_keys($replacement), array_values($replacement), $emailTemplete['subject']);

        self::send($email, $emailTemplete['subject'], $emailTemplete['body']);
    }

    public static function getTemplate($emailTemplete)
    {
        $stmt = Db::getInstance()->prepare("SELECT body, subject FROM `email_templates` where name = :template_name limit 1");
        $stmt->bindParam(":template_name", $emailTemplete, PDO::PARAM_STR);
        $stmt->execute();
        $a = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($a)) {
            return $a;
        }
        return false;
    }

    public static function send($to, $subject, $body)
    {
        try {
            $fromName = self::$fromName;
            $fromEmail = self::$fromEmail;
            $replacement = array(
                '{SITE_NAME}' => self::$siteName,
                '{SITE_URL}' => self::$siteUrl,
                '{SITE_SUPPORT_EMAIL}' => self::$supportEmail,
                '{CURRENT_TIME}' => date('Y-m-d H:i:s'),
            );
            $layout = self::get_layout();
            $body = str_replace('{BODY}', $body, $layout);
            $body = str_replace(array_keys($replacement), array_values($replacement), $body);
            $subject = str_replace(array_keys($replacement), array_values($replacement), $subject);
            $mail = new PHPMailer;

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = MAIL_HOST;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = MAIL_USERNAME;                 // SMTP username
            $mail->Password = MAIL_PASSWORD;                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//        $mail->Port = 587;                                    // TCP port to connect to
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
     public static function send_new($to, $subject, $body , $filepath , $filename)
    {
        try {
            $fromName = self::$fromName;
            $fromEmail = self::$fromEmail;
            $replacement = array(
                '{SITE_NAME}' => self::$siteName,
                '{SITE_URL}' => self::$siteUrl,
                '{SITE_SUPPORT_EMAIL}' => self::$supportEmail,
                '{CURRENT_TIME}' => date('Y-m-d H:i:s'),
            );
            $layout = self::get_layout();
            $body = str_replace('{BODY}', $body, $layout);
            $body = str_replace(array_keys($replacement), array_values($replacement), $body);
            $subject = str_replace(array_keys($replacement), array_values($replacement), $subject);
            $mail = new PHPMailer;

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = MAIL_HOST;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = MAIL_USERNAME;                 // SMTP username
            $mail->Password = MAIL_PASSWORD;                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//        $mail->Port = 587;                                    // TCP port to connect to
           
            $mail->Port = MAIL_PORT;                                    // TCP port to connect to
            $mail->Host;
            $mail->Username;       
            $mail->Password;
            
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);     // Add a recipient
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $subject;

            $mail->Body = $body;

            $filepath = str_replace('\\', '/', $filepath);
            $mail->addStringAttachment(file_get_contents($filepath."/".$filename) ,$filename,'base64', 'txt');
            
            if (!$mail->send()) {
                echo "Mail could not be sent to $to \n";
                echo 'Mailer Error: ' . $mail->ErrorInfo ."\n";
            } else {
               // echo "Mail has been sent to $to \n";
            }
        } catch (Exception $e) {
           // echo "Mail could not be sent.Exception Arrise at SendEmail.php at Line No 128 \n";
            return false;
        }
    }

     public static function send_report($to, $subject, $body, $flag)
    {
        try {
            $fromName = self::$fromName;
            $fromEmail = self::$fromEmail;
            $layout='';
            $replacement = array(
                '{SITE_NAME}' => self::$siteName,
                '{SITE_URL}' => self::$siteUrl,
                '{SITE_SUPPORT_EMAIL}' => self::$supportEmail,
                '{CURRENT_TIME}' => date('Y-m-d H:i:s'),
            );
            if ($flag == 1) {
                $layout = self::report_layout($body);    
            }

            if ($flag == 2) {
                $layout = self::branch_report_layout($body);    
            }
            
            $body = str_replace(array_keys($replacement), array_values($replacement), $layout);
            $subject = str_replace(array_keys($replacement), array_values($replacement), $subject);
            $mail = new PHPMailer;

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = MAIL_HOST;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = MAIL_USERNAME;                 // SMTP username
            $mail->Password = MAIL_PASSWORD;                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//        $mail->Port = 587;                                    // TCP port to connect to
           
            $mail->Port = MAIL_PORT;                                    // TCP port to connect to
            $mail->Host;
            $mail->Username;       
            $mail->Password;
            
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);     // Add a recipient
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $subject;

            $mail->Body = $body;            
            
            if (!$mail->send()) {
                echo "Mail could not be sent to $to \n";
                echo 'Mailer Error: ' . $mail->ErrorInfo ."\n";
            } else {
               // echo "Mail has been sent to $to \n";
            }
        } catch (Exception $e) {
           // echo "Mail could not be sent.Exception Arrise at SendEmail.php at Line No 128 \n";
            return false;
        }
    }

      public static function red_new()
    {
        try {
            $mail = new PHPMailer;
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = MAIL_HOST;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = MAIL_USERNAME;                 // SMTP username
            $mail->Password = MAIL_PASSWORD;                           // SMTP password
            $mail->Port = MAIL_PORT;                                    // TCP port to connect to 
            function pop3_login($host,$port,$user,$pass,$folder="INBOX",$ssl=false) 
            { 
                $a=EmailRead::getLastTime();  
                $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
                $inbox=imap_open($hostname,$user,$pass); 
                $emails = imap_search($inbox,'from "adarsh64681994@gmail.com" SINCE "'.$a.'"'); 
                if($emails) { 
                    $output = '';  
                    rsort($emails); 
                    foreach($emails as $email_number) { 
                        $overview = imap_fetch_overview($inbox,$email_number,0); 
                        $attachment['data'] = imap_fetchbody($inbox, $email_number, 2);  
                        $attachment['data'] = base64_decode($attachment['data']);  
                        $message = imap_fetchbody($inbox,$email_number,2); 
                        $message = imap_binary($message);  
                        $sub=$overview[0]->subject.'queue'; 
                        $content = $attachment['data']; 
                        $file =fopen("".dirname(dirname(dirname(__FILE__))).$sub."\abd.zip", "w");
                        $file_path=dirname(dirname(dirname(__FILE__))).$sub."\abd.zip";
                        $file_path2=dirname(dirname(dirname(__FILE__))).$sub;
                        echo fwrite($file,$content);
                        fclose($file); 
                        $filename = $file_path;
                        $zip = new ZipArchive;
                        $res = $zip->open($filename); 
                        if ($res === TRUE) { 
                            $path =$file_path2; 
                            $zip->extractTo($path);
                            $zip->close();  
                            $fileList = glob($file_path2.'/*'); 
                            foreach($fileList as $filename){ 
                                $info = new SplFileInfo($filename); 
                                (($info->getExtension())!='txt')?unlink($filename):''; 
                            } 
                        } else {
                            echo 'failed!';
                        }
                    } 
                    EmailRead::updateLastTime();
                }  
                imap_close($inbox);
            }
            pop3_login($mail->Host,$mail->Port,$mail->Username,$mail->Password,"INBOX","TLS"); 
        } catch (Exception $e) {
            return false;
        }
    } 
    public static function get_layout()
    {
        $year = date('Y');
        $todayDate = date('jS F Y');
        $siteUrl = self::$siteUrl;
        $siteName = self::$siteName;
        $supportEmail = self::$supportEmail;
        $email_layout = "<div>"
            . "<div style = 'overflow: hidden;'>"
            . "<div dir = 'ltr'>"
            . "<div style = 'font-family:verdana,sans-serif;font-size:small;color:#000000'><br></div>"
            . "<div>"
            . "<div style = 'background-color:#ffffff'>"
            . "<table width = '600' align = 'center' style = 'margin:20px auto;padding:20px 0;border-collapse:collapse'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "<td bgcolor = '#FFFFFF' style = 'margin:0 auto;padding:0;border:1px solid #d5d5d5;display:block;max-width:600px;clear:both'>"
            . "<table bgcolor = '#FFFFFF' style = 'margin:0;padding:0;width:100%;border-bottom:1px solid #d5d5d5'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:10px 20px 10px 10px'>"
            . "<table style = 'margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td width = '170' style = 'margin:0;padding:0;color:#fff;font-size:12px'>"
            . "<a target = '_blank' style = 'color:#fff' title = '$siteName' href = '$siteUrl'><img width = '170' style = 'max-width:100%' alt = '$siteName' src = '$siteUrl/img/logo.png'></a>"
            . "</td>"
            . "<td align = 'right' style = 'margin:0;padding:0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;color:#777777;font-size:12px;line-height:14px;'> $todayDate "
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td style='margin:0;padding:10px 15px 10px;color:#777;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . "{BODY}"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "<table cellspacing = '0' style = 'margin:0;padding:0;border-top:1px solid #e5e5e5;color:#7b7b7b;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td bgcolor = '#EEEEEE' style = 'background-image:url('$siteUrl/img/noise.png');margin:0;padding:10px 15px;font-size:10px'>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td style='margin:0;padding:0 10px 0 0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;font-size:12px;line-height:18px'>"
            . "<strong>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='mailto:$supportEmail'>$supportEmail</a>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='$siteUrl/'>$siteName</a>"
            . "</strong>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td style='text-align:center;clear:both;max-width:600px;display:block;padding:0px;margin:0px auto'>"
            . "<img width='100%' src='$siteUrl/img/email_shadow.png'>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>Questions? Please do not reply to this email; responses are not monitored. If you have questions, please use the contact information above."
            . "</p>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>"
            . "Copyright Â&copy; $year DynaLytics. All Rights Reserved."
            . "</p>"
            . "</td>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>";
        return $email_layout;
    }

    public static function report_layout($body)
    {
        $year = date('Y');
        $todayDate = date('jS F Y');
        $siteUrl = self::$siteUrl;
        $siteName = self::$siteName;
        $supportEmail = self::$supportEmail;

        $body_details='';

        foreach ($body as $key => $value) 
        {
            $body_details = $body_details."<tr>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['file_processing_detail_id']
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['filename']
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['company_id']
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['station']
            . "</td>"
         
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['row_number']
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['dealer_id']
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['branch_id']
            . "</td>"
            . "</tr>";
        }      

        $email_layout = "<div>"
            . "<div style = 'overflow: hidden;'>"
            . "<div dir = 'ltr'>"
            . "<div style = 'font-family:verdana,sans-serif;font-size:small;color:#000000'><br></div>"
            . "<div>"
            . "<div style = 'background-color:#ffffff'>"
            . "<table width = '600' align = 'center' style = 'margin:20px auto;padding:20px 0;border-collapse:collapse'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "<td bgcolor = '#FFFFFF' style = 'margin:0 auto;padding:0;border:1px solid #d5d5d5;display:block;max-width:600px;clear:both'>"
            . "<table bgcolor = '#FFFFFF' style = 'margin:0;padding:0;width:100%;border-bottom:1px solid #d5d5d5'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:10px 20px 10px 10px'>"
            . "<table style = 'margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td width = '170' style = 'margin:0;padding:0;color:#fff;font-size:12px'>"
            . "<a target = '_blank' style = 'color:#fff' title = '$siteName' href = '$siteUrl'><img width = '170' style = 'max-width:100%' alt = '$siteName' src = '$siteUrl/img/logo.png'></a>"
            . "</td>"
            . "<td align = 'right' style = 'margin:0;padding:0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;color:#777777;font-size:12px;line-height:14px;'> $todayDate "
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<thead>"
            . "<th>Id</th>"
            . "<th>Filename</th>"
            . "<th>Company_id</th>"
            . "<th>Station</th>"           
            . "<th>Row Number</th>"            
            . "<th>Dealer id</th>"
            . "<th>Branch id</th>"
            . "</thead>"
            . "<tbody>"
            . "$body_details"
            . "</tbody>"
            . "<table cellspacing = '0' style = 'margin:0;padding:0;border-top:1px solid #e5e5e5;color:#7b7b7b;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td bgcolor = '#EEEEEE' style = 'background-image:url('$siteUrl/img/noise.png');margin:0;padding:10px 15px;font-size:10px'>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td style='margin:0;padding:0 10px 0 0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;font-size:12px;line-height:18px'>"
            . "<strong>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='mailto:$supportEmail'>$supportEmail</a>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='$siteUrl/'>$siteName</a>"
            . "</strong>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td style='text-align:center;clear:both;max-width:600px;display:block;padding:0px;margin:0px auto'>"
            . "<img width='100%' src='$siteUrl/img/email_shadow.png'>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>Questions? Please do not reply to this email; responses are not monitored. If you have questions, please use the contact information above."
            . "</p>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>"
            . "Copyright Â&copy; $year DynaLytics. All Rights Reserved."
            . "</p>"
            . "</td>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>";
        return $email_layout;
    }

    public static function branch_report_layout($body)
    {
        $year = date('Y');
        $todayDate = date('jS F Y');
        $siteUrl = self::$siteUrl;
        $siteName = self::$siteName;
        $supportEmail = self::$supportEmail;

        $body_details='';

        foreach ($body as $key => $value) 
        {
            $id = $key+1;
            $body_details = $body_details."<tr>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $id
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['name']
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['ftpuser']
            . "</td>"
            . "<td style='margin:0;padding:5px 5px 5px;color:#777; text-align:center; font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . $value['company_name']
            . "</td>" 
            . "</tr>";
        }      

        $email_layout = "<div>"
            . "<div style = 'overflow: hidden;'>"
            . "<div dir = 'ltr'>"
            . "<div style = 'font-family:verdana,sans-serif;font-size:small;color:#000000'><br></div>"
            . "<div>"
            . "<div style = 'background-color:#ffffff'>"
            . "<table width = '600' align = 'center' style = 'margin:20px auto;padding:20px 0;border-collapse:collapse'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "<td bgcolor = '#FFFFFF' style = 'margin:0 auto;padding:0;border:1px solid #d5d5d5;display:block;max-width:600px;clear:both'>"
            . "<table bgcolor = '#FFFFFF' style = 'margin:0;padding:0;width:100%;border-bottom:1px solid #d5d5d5'>"
            . "<tbody>"
            . "<tr>"
            . "<td style = 'margin:0;padding:10px 20px 10px 10px'>"
            . "<table style = 'margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td width = '170' style = 'margin:0;padding:0;color:#fff;font-size:12px'>"
            . "<a target = '_blank' style = 'color:#fff' title = '$siteName' href = '$siteUrl'><img width = '170' style = 'max-width:100%' alt = '$siteName' src = '$siteUrl/img/logo.png'></a>"
            . "</td>"
            . "<td align = 'right' style = 'margin:0;padding:0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;color:#777777;font-size:12px;line-height:14px;'> $todayDate "
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<thead>"
            . "<th>Id</th>"
            . "<th>Name</th>"
            . "<th>FTP User</th>"
            . "<th>Company Name</th>" 
            . "</thead>"
            . "<tbody>"
            . "$body_details"
            . "</tbody>"
            . "<table cellspacing = '0' style = 'margin:0;padding:0;border-top:1px solid #e5e5e5;color:#7b7b7b;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td bgcolor = '#EEEEEE' style = 'background-image:url('$siteUrl/img/noise.png');margin:0;padding:10px 15px;font-size:10px'>"
            . "<table style='margin:0;padding:0;width:100%'>"
            . "<tbody>"
            . "<tr>"
            . "<td style='margin:0;padding:0 10px 0 0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;font-size:12px;line-height:18px'>"
            . "<strong>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='mailto:$supportEmail'>$supportEmail</a>"
            . "<br>"
            . "<a target='_blank' style='text-decoration:none; color: #777777;' href='$siteUrl/'>$siteName</a>"
            . "</strong>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td style='text-align:center;clear:both;max-width:600px;display:block;padding:0px;margin:0px auto'>"
            . "<img width='100%' src='$siteUrl/img/email_shadow.png'>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>Questions? Please do not reply to this email; responses are not monitored. If you have questions, please use the contact information above."
            . "</p>"
            . "</td>"
            . "<td style='margin:0;padding:0'></td>"
            . "</tr>"
            . "<tr>"
            . "<td style='margin:0;padding:0'></td>"
            . "<td align='center' style='clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif'>"
            . "<p style='margin-bottom:10px;font-weight:normal;font-size:12px'>"
            . "Copyright Â&copy; $year DynaLytics. All Rights Reserved."
            . "</p>"
            . "</td>"
            . "<td style = 'margin:0;padding:0'></td>"
            . "</tr>"
            . "</tbody>"
            . "</table>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>";
        return $email_layout;
    }
}
