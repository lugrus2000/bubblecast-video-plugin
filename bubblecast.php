<?php
/*
Plugin Name: Bubblecast Video for Wordpress
Plugin URI: http://bubble-cast.com/wordpress.html
Description: Bubblecast video plugin brings in video capabilities to your blog. It can upload, record and embed your own or Youtube video into your posts in couple clicks
Author: bubble-cast.com
Version: 1.1
Author URI: http://bubble-cast.com/
*/

// this is to check whether we have already been plugged in from mu-plugins
if (!function_exists('bubblecast_post')) {

require("config.php");
require_once("bubblecast_utils.php");
global $embeddedQuickcastMovieURL, $playerMovieURL, $siteId;

function get_bubblecast_logo(){
    return get_plugin_base_dir().'/i/bubblecast_icon.png';
}
function bubblecast_media_buttons_context($context){
		global $post_ID, $temp_ID;
        global $user_login, $user_email,$admin_email;
        $uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
        $url = get_option('siteurl').'/wp-admin/media-upload.php?type=image&tab=bubblecastvideos&post_id='.$uploading_iframe_ID.'&user_login='.$user_login.'&amp;user_email='.$user_email.'&amp;admin_email='.$admin_email;
		$image_btn = get_bubblecast_logo();
		$image_title = 'Bubblecast video';
		$out = ' <a href="'.$url.'&amp;TB_iframe=true&amp;height=320&amp;width=400" class="thickbox" title="'.$image_title.'"><img src="'.$image_btn.'" alt="'.$image_title.'" /></a>';
		return $context.$out;
}
function bubblecast_comment($content){
    return embed_quickcast($content);
}
function bubblecast_post($content){
    return embed_quickcast($content);

}
function embed_quickcast($content){
    global $embeddedQuickcastMovieURL, $playerMovieURL,$comment;
    $siteId = get_bubblecast_option("bubblecast_site_id");
    if(!$siteId){
        $siteId = bubblecast_login();
    }
    $ep =  '<div class="bubblecast_player_wp">';
    $ep .= '<div class="bubblecast_fl_wp"><a href="http://bubble-cast.com/wordpress.html" style="font-size:10px">http://bubble-cast.com</a></div>';
    if (!$siteId && bubblecast_is_admin()) {
        $ep .= ('<div class="bubblecast_cfg_err_wp">' . __('You haven\'t set up Bubblecast login and password. Please, follow installation instructions to finish setup in your administration console at <b>Site Admin -&gt; Settings -&gt; Bubblecast</b>', 'bubblecast') . ' </div>');
    }
    $ep .= '<div class="bubblecast_fl_wp"><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"                width="475" height="375" id="quickcast" align="middle">            <param name="allowScriptAccess" value="always" />            <param name="movie" value="'.$playerMovieURL.'" />            <param name="flashvars" value="siteId='.$siteId.'&amp;recordEnabled=false&amp;isVideo=true&amp;languages=' . get_option('bubblecast_language') . '&amp;pluginMode=wp&amp;streamName=\\1" />            <param name="quality" value="high" />            <param name="allowfullscreen" value="true"/>            <param name="bgcolor" value="#ededed" />                <embed src="'.$playerMovieURL.'" quality="high" bgcolor="#ededed" width="475" height="375" name="quickcast"                       flashvars="siteId='.$siteId.'&amp;recordEnabled=false&amp;isVideo=true&amp;languages=' . get_option('bubblecast_language') . '&amp;pluginMode=wp&amp;streamName=\\1" allowfullscreen="true"                       align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />        </object></div>';
    $ep .= '</div>';
    return preg_replace("/\\[bubblecast id=(.*?)\\]/",$ep,$content);
}
function bubblecast_comment_form($text='') {
    $url = "quickcast_comment.php";
    include($url); 
    $image_btn = get_bubblecast_logo();
    $v .= '<a href="#" onclick="showBubblecastComment(); return false;"><img src="'.$image_btn.'" /> ' . __('Add video comment', 'bubblecast') . '</a>'."\n";
	echo $v;
}
function bubblecast_js(){
        global $user_login, $user_email,$admin_email;
        get_currentuserinfo();
        $admin_email = get_option('admin_email');
        $pluginurl = get_plugin_base_dir().'/';
        echo "\n".'<link href="'.$pluginurl.'bubblecast.css" media="screen" rel="stylesheet" type="text/css"/>'."\n";
        echo "\n".'<script src="'.$pluginurl.'js/bubblecast.js" type="text/javascript"></script>'."\n";
}
function media_upload_bubblecastvideos()
{
  return wp_iframe('bubblecastvideos_page');
}
function bubblecastvideos_page(){
    global $pluginMode;
    $pluginMode = "wp";
    include("iquickcast.php");
}
function add_bubblecast_tab($content) {
	$content['bubblecastvideos'] = 'Bubblecast';
	return $content;
}

function on_wp_head(){
    bubblecast_js();
}

function on_admin_head() {
    bubblecast_js();
}


function bubblecast_save_post($postID,$postData){
    global $sendPostDatURL;
    error_log("bubblecast_save_post = ".$postData->guid);
    if($postData->post_type == "page" || $postData->post_type == "post"){
        $link = get_permalink( $postID );
        bubblecast_send_post_data($sendPostDatURL,$postData->post_content,$link,htmlentities($postData->post_title));
    }
}
function bubblecast_comment_post($commentPostID,$comment_approved){
        if($comment_approved == 1){
            handle_comment($commentPostID);
        }

}
function bubblecast_edit_comment($commentPostID){
    handle_comment($commentPostID);
}
function handle_comment($commentPostID){
    global $sendPostDatURL;
    $postData = & get_comment($commentPostID);
    $link = get_comment_link( $commentPostID );
    bubblecast_send_post_data($sendPostDatURL,$postData->post_content,$link,htmlentities($postData->post_title));

}

function bubblecast_get_video_posts_widget_default_options() {
    // first find out the category which we'll be using as a default one
    // trying 'Video' first
    $defaultCategoryId = get_cat_ID('Video');
    if (!$defaultCategoryId) {
        // trying to create it
        if (wp_create_category('Video')) {
            $defaultCategoryId = get_cat_ID('Video');
        } else {
            // select Uncategorized as a fallback
            $defaultCategoryId = 0;
        }
    }

    // building the default options
    $defaultOptions = array(
        'title' => __('Bubblecast Video Posts', 'bubblecast'),
        'layout' => 'v',
        'videos' => 3,
        'categories' => array($defaultCategoryId)
    );
    return $defaultOptions;
}

function bubblecast_widget_video_posts_control() {
    require 'widget/video_posts_control.php';
}

function bubblecast_widget_video_posts($args) {
    global $bubblecastThumbUrl;
    extract($args);
    require 'widget/video_posts.php';
}

function bubblecast_widget_video_posts_register() {
    // these two names must match!
    register_sidebar_widget('Bubblecast Video Posts', 'bubblecast_widget_video_posts');
    register_widget_control('Bubblecast Video Posts', 'bubblecast_widget_video_posts_control');
}


add_filter('media_buttons_context', 'bubblecast_media_buttons_context');
add_action('wp_head', 'on_wp_head');
add_action('admin_head', 'on_admin_head');
add_filter('comment_text', 'bubblecast_comment');
add_filter('the_content', 'bubblecast_post');
add_action('comment_form', 'bubblecast_comment_form');
add_action('media_upload_tabs','add_bubblecast_tab');
add_action('media_upload_bubblecastvideos', 'media_upload_bubblecastvideos');
add_action('save_post', 'bubblecast_save_post',10,2);
add_action('comment_post', 'bubblecast_comment_post',10,2);
add_action('edit_comment', 'bubblecast_edit_comment',10,1);
add_action('admin_init', 'reg_bubblecast_settings');
add_action('admin_menu', 'my_plugin_menu');
add_action('init', 'bubblecast_load_textdomain');

// registering widgets
add_action('init', 'bubblecast_widget_video_posts_register');

function bubblecast_load_textdomain() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('bubblecast', 'wp-content/plugins/' . $plugin_dir, $plugin_dir);
}

function reg_bubblecast_settings(){
    register_setting( 'bubblecast-group', 'bubblecast_username' ); 
    register_setting( 'bubblecast-group', 'bubblecast_password' );
    register_setting( 'bubblecast-group', 'bubblecast_language' );
    register_setting( 'bubblecast-group', 'bubblecast_wvp_options' );
}
function my_plugin_menu() {
    $show_bubblecast_options = false;
    if(is_wpmu()){
        // We're in WPMU
        if(is_site_admin()) {// We should show options page only to WPMU site admin
            $show_bubblecast_options = true;
        }
    }
    else{
        $show_bubblecast_options = true;
    }
    if($show_bubblecast_options){
            add_options_page(__('Bubblecast Plugin Options', 'bubblecast'), 'Bubblecast', 8, __FILE__, 'my_plugin_options');

    }
}
function my_plugin_options() {
    include("boptions.php");
}
function get_plugin_base_dir(){
    return WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
}

}// this is for if on the top which checks whether we need to plug in

?>
