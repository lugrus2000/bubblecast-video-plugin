<?php
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
        $xml = bubblecast_load($authURL."?username=".$bubblecast_username."&password=".$bubblecast_password,
                array('return_info'    => true));
        $user_doc = XML_unserialize($xml["body"]);
        $siteId = $user_doc["root"]["siteId"];
        return $siteId;
}
    function bubblecast_send_post_data($sendPostDatURL,&$message,$link,$title){
        if(preg_match("/\\[bubblecast(.*?)\\]/",$message)){
            bubblecast_load($sendPostDatURL,
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

function bubblecast_flash_object($width, $height, $video_id, $videoNum,
        $playerMovieURL, $siteId, $languages) {
    $flash_obj = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"                width="'.$width.'" height="'.$height.'" id="quickcast'.$video_id.'_'.$videoNum.'" align="middle">            <param name="allowScriptAccess" value="always" />            <param name="movie" value="'.$playerMovieURL.'" />            <param name="flashvars" value="siteId='.$siteId.'&amp;recordEnabled=false&amp;autoPlay=true&amp;isVideo=true&amp;languages=' . $languages . '&amp;pluginMode=wp&amp;streamName='.$video_id.'" />            <param name="quality" value="high" />            <param name="allowfullscreen" value="true"/>            <param name="bgcolor" value="#ededed" />                <embed src="'.$playerMovieURL.'" quality="high" bgcolor="#ededed" width="'.$width.'" height="'.$height.'" name="quickcast'.$video_id.'_'.$videoNum.'" flashvars="siteId='.$siteId.'&amp;recordEnabled=false&amp;autoPlay=true&amp;isVideo=true&amp;languages=' . $languages . '&amp;pluginMode=wp&amp;streamName='.$video_id.'" allowfullscreen="true"                       align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />        </object>';
    return $flash_obj;
}

?>