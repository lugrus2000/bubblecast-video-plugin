<?
require_once("load_http.php");
require_once("bubblecast_xmlparser.php");
function is_wpmu(){
    return function_exists('wpmu_create_user');
}
function bubblecast_login(){
        $bubblecast_username = get_bubblecast_option("bubblecast_username");
        $bubblecast_password = get_bubblecast_option("bubblecast_password");
        $siteId = bubblecast_remote_login($bubblecast_username,$bubblecast_password);
        update_bubblecast_option("bubblecast_site_id",$siteId);
        return $siteId;
}
function bubblecast_remote_login($bubblecast_username,$bubblecast_password){
        global $authURL;
        $xml = load($authURL."?username=".$bubblecast_username."&password=".$bubblecast_password,
                array('return_info'    => true));
        $user_doc = XML_unserialize($xml["body"]);
        $siteId = $user_doc["root"]["siteId"];
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
function update_bubblecast_option($opt_name,$opt_val){
    if(is_wpmu()){
        update_site_option($opt_name,$opt_val);
    }
    else{
        update_option($opt_name,$opt_val);
    }
}
function get_bubblecast_option($opt_name){
    if(is_wpmu()){
        return get_site_option($opt_name);
    }
    else{
        return get_option($opt_name);
    }
}
?>