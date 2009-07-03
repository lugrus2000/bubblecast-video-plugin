<?
require_once("load_http.php");
require_once("bubblecast_xmlparser.php");
function bubblecast_login(){
        global $authURL;
        $bubblecast_username = get_option("bubblecast_username");
        $bubblecast_password = get_option("bubblecast_password");
        $xml = load($authURL."?username=".$bubblecast_username."&password=".$bubblecast_password,
                array('return_info'    => true));
        $user_doc = XML_unserialize($xml["body"]);
        $siteId = $user_doc["root"]["siteId"];
        update_option("bubblecast_site_id",$siteId);
        return $siteId;
}
    function bubblecast_send_post_data($sendPostDatURL,&$message,$link,$title){
        if(preg_match("/\\[bubblecast(.*?)\\]/",$message)){
            load($sendPostDatURL,
                 array('method' => "post",
                'return_info'    => false,
                "post_data" => array (
                    "message" => $message,
                    "link" => $link,
                    "title" => $title
                )));
        }
    }

function bubblecast_is_admin() {
    global $user_level;
    get_currentuserinfo();
    return $user_level == 10;
}

?>