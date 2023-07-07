<?php 
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/models/DB.php");


$client_id = "f55f2aea-9433-4234-b4b2-c5472d659795";
$client_secret = "--M8Q~c0rR8x0GU39aJUarGj9a2SVWSdbzW5nbzp";
$redirect_uri = "https://schooltap.com/text_parsing/generate_code.php";
$authority = "https://login.microsoftonline.com";
$scopes = array("offline_access", "openid");
if(true) {
    array_push($scopes, "https://outlook.office.com/mail.read");
}
/* If you need to send email, then need to add following scope */
if(true) {
    array_push($scopes, "https://outlook.office.com/mail.send");
}
$auth_url = "/common/oauth2/v2.0/authorize";
$auth_url .= "?client_id=".$client_id;
$auth_url .= "&redirect_uri=".$redirect_uri;
$auth_url .= "&response_type=code&scope=".implode(" ", $scopes);
$token_url = "/common/oauth2/v2.0/token";
$api_url = "https://outlook.office.com/api/v2.0";

// Get Data from db
$db = DB::getInstance();
$refresh_token = $db->prepare("SELECT * FROM `outlook_access_token`");
$refresh_token->execute();
$tokenData = $refresh_token->fetch();

if(!empty($tokenData)){
    list_email($tokenData['access_token']);
}



function list_email($access_token) {
    $email_count = 0;
    $db = DB::getInstance();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $s = $db->prepare(" select date, received_date_time , uid, id from notifications  order by date desc limit 1");
    $s->execute();
    $arr = $s->fetch();
    $date = "";
    if(!empty($arr)){
        $date = $arr['date'];
        $date = date_create($date);
        $date = date_format($date,"Y-m-d");
        $received_date_time = $arr['received_date_time'];
    }
    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".$access_token,
        "Accept: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true",
        "X-AnchorMailbox: ". get_user_email($access_token)
    );
    $api_url = "https://outlook.office.com/api/v2.0";
    $top = 200;
    $skip = isset($_GET["skip"]) ? intval($_GET["skip"]) : 0;
    $search = array (
        // Only return selected fields
        "\$select" => "Subject,ReceivedDateTime,Sender,From,ToRecipients,HasAttachments,BodyPreview,InternetMessageId,ConversationId,Id,Flag,IsRead,IsDraft",
        // Sort by ReceivedDateTime, newest first
        "\$filter" => "ReceivedDateTime gt ".$received_date_time,
        "\$orderby" => "ReceivedDateTime ASC",
        // Return at most n results
        "\$top" => $top, "\$skip" => $skip
    );
    $outlookApiUrl = $api_url . "/Me/MailFolders/Inbox/Messages?" . http_build_query($search);
    $response = runCurl($outlookApiUrl, null, $headers);
    $response = explode("\n", trim($response));
    $response = $response[count($response) - 1];
    $response = json_decode($response, true);
    if(isset($response["value"]) && count($response["value"]) > 0) {
        $date = $response["value"];
        $filecontent = file_get_contents("mail_read_log.txt");
        $myfile = fopen("mail_read_log.txt", "w") or die("Unable to open file!");
        $txt = $filecontent . "\n" . json_encode($date);
        fwrite($myfile, $txt);
        fclose($myfile);
        foreach ($response["value"] as $key => $mail) {
          
            // $old_date_timestamp = strtotime($mail['ReceivedDateTime']);
            // $new_date = date('Y-m-d', $old_date_timestamp);   
            // if($new_date == "2022-12-15"){
            //     $msgNo = !empty($mail['ConversationId']) ? $mail['ConversationId'] : '';
            //     $checkReadMail = Db::getInstance()->prepare("SELECT `msgno` FROM `notifications` where `msgno` LIKE  '%$msgNo%'");
            //     $checkReadMail->execute();
            //     $availableMessages = $checkReadMail->fetch(PDO::FETCH_ASSOC);
            //     echo '<pre><b></b><br>';
            //     print_r($availableMessages);echo '<br>';exit;
            // }
            $msgNo = !empty($mail['ConversationId']) ? $mail['ConversationId'] : '';
            $checkReadMail = Db::getInstance()->prepare("SELECT `msgno` FROM `notifications` where `msgno` LIKE  '%$msgNo%'");
            $checkReadMail->execute();
            $availableMessages = $checkReadMail->fetch(PDO::FETCH_ASSOC);
           if(empty($availableMessages)){
                if(in_array($mail['Sender']['EmailAddress']['Address'], ['joshc@addontechnologies.com', 'donotreply@addontechnologies.com'])  && in_array($mail['Sender']['EmailAddress']['Name'], ['Logs','Log','Add-On Technologies', 'Josh Cannon'])){
                    $savepath = "";
                    $extractPath = "";
                    if($mail['HasAttachments'] == 1){
                        $mailID = $mail['Id'];
                        // $arr = explode(' ',$mail->subject);
                        // $kay = $arr[0];
                        $userID = get_user_id();
                        $headers = array(
                            "User-Agent: php-tutorial/1.0",
                            "Authorization: Bearer ".$access_token,
                            "Accept: application/json",
                            "client-request-id: ".makeGuid(),
                            "return-client-request-id: true",
                            "X-AnchorMailbox: ". get_user_email($access_token)
                        );
                        $outlookApiUrl = $api_url . "/Users('$userID')/Messages('$mailID')/Attachments";
                        $response = runCurl($outlookApiUrl, null, $headers);
                        $response = explode("\n", trim($response));
                        $response = $response[count($response) - 1];
                        $response = json_decode($response, true);
                        $file_links = "";
                        foreach ($response["value"] as $attachment) {
                            $filename = $attachment['Name']; 
                            if($filename == "dynalytics.zip"){
                                $file_name = explode('.',$filename);
                                $file_name = !empty($file_name[0]) ? $file_name[0] : '';
                                if(empty($filename)) $filename = $attachment['filename'];
    
                                $savepath = ROOT.DS.'files'.DS.$filename;
                                // $savepath = str_replace('\\', '/', $savepath);
                                // $extractPath = $cur_dir . "/storage/newfiles";
                                // $extractPath = str_replace('\\', '/', $extractPath);
                                $extractPath = ROOT.DS.'storage'.DS.'newfiles';
                                // $extractPath = str_replace('\\', '/', $extractPath);
                                if (!file_exists(ROOT.DS.'files')) {
                                    mkdir(ROOT.DS.'files', 0777, true);
                                }
                                if (!file_exists(ROOT.DS.'storage'.DS.'newfiles')) {
                                    mkdir(ROOT.DS.'storage'.DS.'newfiles', 0777, true);
                                }
                                // echo '<pre><b></b><br>';
                                // print_r($extractPath);echo '<br>';exit;
                                file_put_contents($savepath, base64_decode($attachment["ContentBytes"]));
                                sleep(15);
                                $result = array();
                                exec("unzip -o ".$savepath." -d ".$extractPath);
                                // $zip = new \ZipArchive;
                                // $res = $zip->open($savepath);                                   
                                if (file_exists($savepath)) {    
                                    unlink($savepath);
                                }else {
                                    echo 'file not found';
                                }
                            }
                        }
                        sleep(15);
                        $files=glob($extractPath."/*.*");
                        foreach ($files as $key => $file) {
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            if ($ext != 'txt') {
                                unlink($file);
                            }else{
                                chown($file, "ubuntu");
                                chgrp($file, "ubuntu");

                                $file = str_replace('\\', '/', $file);
                                $filename = basename($file, ".txt");
                                $filename = str_replace("(", "[", $filename);
                                $filename = str_replace(")", "]", $filename);
                               
                                $renameFile = $extractPath."/".$filename.".txt";
                                $renameFile = str_replace('\\', '/', $renameFile);
                                $file_data = explode("/",$renameFile);
                                $file_name = !empty($file_data[7]) ?  $file_data[7] : '';
                                $checkFile = Db::getInstance()->prepare("SELECT * FROM `read_files` where `name` LIKE  '%$file_name%'");
                                $checkFile->execute();
                                $availableFile = $checkFile->fetch(PDO::FETCH_ASSOC);
                                if(empty($availableFile)){
                                    $sqlQuery = "INSERT INTO read_files (name, status) VALUES ('".$file_name."','no')";
                                    $stmt = Db::getInstance()->prepare($sqlQuery);
                                    $stmt->execute();
                                }else{
                                    $file_id = !empty($availableFile['id']) ? $availableFile['id'] : 0;
                                    $update_logoff =  Db::getInstance()->prepare("UPDATE read_files set status='no' where id=$file_id");
                                    if ($update_logoff->execute()) {
                                    } else {
                                        echo "query Not Executed";
                                    }
                                }
                                rename($file, $renameFile);
                            }
                        }
                    }
                }
                $email_details = array(
                    'subject' => !empty($mail['Subject']) ? $mail['Subject'] : '',
                    'body' => !empty($mail['BodyPreview']) ? $mail['BodyPreview'] : '',
                    'from' => !empty($mail['From']['EmailAddress']['Address']) ? $mail['From']['EmailAddress']['Address'] : '',
                    'to' =>!empty($mail['ToRecipients'][0]['EmailAddress']['Address']) ? $mail['ToRecipients'][0]['EmailAddress']['Address'] : '',
                    'date' => !empty($mail['ReceivedDateTime']) ? $mail['ReceivedDateTime'] : '',
                    'message_id' => $mail['InternetMessageId'],
                    'size' => '',
                    'uid' => $mail['Id'],
                    'msgno' => $mail['ConversationId'],
                    'recent' =>'',
                    'flagged' => $mail['Flag']['FlagStatus'],
                    'answered' => '',
                    'deleted' => '',
                    'seen' => $mail['IsRead'],
                    'draft' =>  $mail['IsDraft'],
                    'udate' => '',
                    'mail_date' => !empty($mail['ReceivedDateTime']) ? $mail['ReceivedDateTime'] : '',
                );
                $date = date('Y-m-d');
                if(!empty($email_details['date'])){
                    //$date=date_create($email_details['date']);
                    $email_details['date'] = date("Y-m-d H:i:s", strtotime($email_details['date']));
                }else{
                    $email_details['date'] = '';
                }
                $email_details['body'] = str_replace("'", "", $email_details['body']);
                $sqlQuery = "INSERT INTO notifications (subject, body, email_from, email_to, date, message_id, size, uid, msgno, recent, flagged, answered, deleted, seen, draft, udate, created, received_date_time) VALUES ('".$email_details['subject']."','". $email_details['body']."','".$email_details['from']."','".$email_details['to']."','".$email_details['date']."','".$email_details['message_id']."','".$email_details['size']."','".$email_details['uid']."','".$email_details['msgno']."','".$email_details['recent']."','".$email_details['flagged']."','".$email_details['answered']."','".$email_details['deleted']."','".$email_details['seen']."','".$email_details['draft']."','".$email_details['udate']."','".$date."', '".$email_details['mail_date']."')";
                $stmt = Db::getInstance()->prepare($sqlQuery);
                $stmt->execute();
            }
           // return array('emails' => $email_details, 'totalEmails' => $email_count);
        }
        echo "SuccessFully Read";exit;
    }
   
}


function store_token($o) {
    $data = $o;
    $access_token = $o->access_token;
    $refresh_token = $o->refresh_token;
    $id_token = $o->id_token;

    $db = DB::getInstance();
    $update_logoff = $db->prepare("UPDATE outlook_access_token set access_token='" . $access_token . "', refresh_token='" . $refresh_token . "', id_token='" . $id_token . "' where id=1 ");
    //echo "update side_log set logoff_datetime='".$logoff_time."' where id=".$row['id']." ";
    if ($update_logoff->execute()) {
    } else {
        echo "query Not Executed";
    }
    // $sql = $db->prepare("UPDATE outlook_access_token SET `access_token` = 'samadhan', `refresh_token` = '$refresh_token', `id_token` = '$id_token' WHERE id = 1");
    // // $sql = Db::getInstance()->prepare($sqlQuery);
    // $sql->execute();
    file_put_contents("office_auth_config.txt", json_encode($o));
    return true;
}


function runCurl($url, $post = null, $headers = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $post == null ? 0 : 1);
    if($post != null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if($headers != null) {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($http_code == 400) {
        echo "Error executing request to Office365 api with error code=$http_code<br/><br/>\n\n";
        echo "<pre>"; print_r($response); echo "</pre>";
        die();
    }elseif($http_code == 401){
        refresh_token();
    }
    return $response;
}

function makeGuid(){
    if (function_exists('com_create_guid')) {
        error_log("Using 'com_create_guid'.");
        return strtolower(trim(com_create_guid(), '{}'));
    }
    else {
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid, 12, 4).$hyphen
            .substr($charid, 16, 4).$hyphen
            .substr($charid, 20, 12);
        return $uuid;
    }
}

function get_user_email($access_token) {
    $api_url = "https://outlook.office.com/api/v2.0";
    $headers = array(
        "User-Agent: php-tutorial/1.0",
        "Authorization: Bearer ".$access_token,
        "Accept: application/json",
        "client-request-id: ".makeGuid(),
        "return-client-request-id: true"
    );
    $outlookApiUrl = $api_url . "/Me";
    $response = runCurl($outlookApiUrl, null, $headers);
    $response = explode("\n", trim($response));
    $response = $response[count($response) - 1];
    file_put_contents("office_user_data.txt", $response);
    $response = json_decode($response);
   return $response->EmailAddress;
}


function refresh_token() {
    $client_id = "f55f2aea-9433-4234-b4b2-c5472d659795";
$client_secret = "PFQ8Q~frNh1NNPpqakhpLyhoYdGg9SLu6qWEuc3H";
$redirect_uri = "https://schooltap.com/text_parsing/generate_code.php";
$authority = "https://login.microsoftonline.com";
$scopes = array("offline_access", "openid");
if(true) {
    array_push($scopes, "https://outlook.office.com/mail.read");
}
/* If you need to send email, then need to add following scope */
if(true) {
    array_push($scopes, "https://outlook.office.com/mail.send");
}
$auth_url = "/common/oauth2/v2.0/authorize";
$auth_url .= "?client_id=".$client_id;
$auth_url .= "&redirect_uri=".$redirect_uri;
$auth_url .= "&response_type=code&scope=".implode(" ", $scopes);
$token_url = "/common/oauth2/v2.0/token";
$api_url = "https://outlook.office.com/api/v2.0";


    $db = DB::getInstance();
    $refresh_token = $db->prepare("SELECT * FROM `outlook_access_token`");
    $refresh_token->execute();
    $tokenData = $refresh_token->fetch();
    if(!empty($tokenData['refresh_token'])){
        $token_request_data = array (
            "grant_type" => "refresh_token",
            "refresh_token" => $tokenData['refresh_token'],
            "redirect_uri" => $redirect_uri,
            "scope" => implode(" ", $scopes),
            "client_id" => $client_id,
            "client_secret" => $client_secret
        );
        $body = http_build_query($token_request_data);
        $response = runCurl($authority.$token_url, $body);
        $response = json_decode($response);
        store_token($response);
        file_put_contents("office_access_token.txt", $response->access_token);
    }else{
        echo "Please login again";
    }
    
}
function get_user_id() {
    $response = json_decode(file_get_contents("office_user_data.txt"));
    return $response->Id;
}

?>