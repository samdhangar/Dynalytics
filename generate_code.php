<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/models/DB.php");
// Set configration
$client_id = "f55f2aea-9433-4234-b4b2-c5472d659795";
$client_secret = "--M8Q~c0rR8x0GU39aJUarGj9a2SVWSdbzW5nbzp";
$redirect_uri = "http://localhost/text_parsing/generate_code.php";
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
token();
function token() {
    $text = file_exists("office_auth_config.txt") ? file_get_contents("office_auth_config.txt") : null;
    if($text != null && strlen($text) > 0) {
        return json_decode($text);
    }
    return null;
}
if (isset($_GET["code"])) {
    $token_request_data = array (
        "grant_type" => "authorization_code",
        "code" => $_GET["code"],
        "redirect_uri" => $redirect_uri,
        "scope" => implode(" ", $scopes),
        "client_id" => $client_id,
        "client_secret" => $client_secret
    );
    $body = http_build_query($token_request_data);
    $response = runCurl($authority.$token_url, $body);
    $response = json_decode($response);
    store_token($response);
}else {
    $accessUrl = $authority.$auth_url;
    echo $accessUrl;exit;
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
    if($http_code >= 400) {
        echo "Error executing request to Office365 api with error code=$http_code<br/><br/>\n\n";
        echo "<pre>"; print_r($response); echo "</pre>";
        die();
    }
    return $response;
}

function store_token($o) {
    $data = $o;
    $access_token = $o->access_token;
    $refresh_token = $o->refresh_token;
    $id_token = $o->id_token;

    $db = DB::getInstance();
    $sql = $db->prepare("INSERT INTO  outlook_access_token (access_token , refresh_token , id_token, user_id) VALUES('$access_token','$refresh_token','$id_token', '')");
    $sql->execute();
    file_put_contents("office_auth_config.txt", json_encode($o));
}

?>