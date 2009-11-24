<?php
/*
Plugin Name: Bubblecast Video for Wordpress
Plugin URI: http://bubble-cast.com/wordpress.html
Description: Bubblecast video plugin brings in video capabilities to your blog. It can upload, record and embed video into your posts in couple clicks
Author: bubble-cast.com
Version: 1.2.0
Author URI: http://bubble-cast.com/
*/

// this is to check whether we have already been plugged in from mu-plugins
if (!function_exists('bubblecast_post')) {

require("config.php");
require_once("bubblecast_utils.php");
global $embeddedQuickcastMovieURL, $playerMovieURL, $siteId,$videoNum,$wideScreenVideos;

$videoNum = 0;
$wideScreenVideos = array();

function bubblecast_get_cat_ids_str(&$categories){
    if(!is_array($categories)){
        return "";
    }
    return join($categories,",");
}

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
function bubble_regexp(){
    return "/\\[bubblecast\\s*id=([^\\s\\]]+)\\s*(thumbnail=([^\\s\\]]+))?\\s*(player=([^\\s\\]]+))?\\s*.*\\]/";
}
function embed_quickcast($content){
    $q_content = preg_replace_callback(bubble_regexp(),"bubblecast_handle_tag_params",$content);
    return $q_content;
}
function bubblecast_handle_tag_params($matches){
    global $videoNum;
    
    get_currentuserinfo();
    
    $video_id = $matches[1];
    $thumbnail_dimensions = $matches[3];
    $player_dimensions = $matches[5];

    $default_width = 475;
    $default_height = 375;
    $min_player_width = 475;
    $min_player_height = 375;
    $min_thumbnail_width = 320;
    $min_thumbnail_height = 240;
    $max_width = 800;
    $max_height = 600;
    
    $player_width = $default_width;
    $player_height = $default_height;
    $thumbnail_width = $default_width;
    $thumbnail_height = $default_height;
    
    $thumbnail_dimensions_matched = preg_match('/^(\d+)x(\d+)$/', $thumbnail_dimensions, $thumbnail_dimensions_matches);
    $player_dimensions_matched = preg_match('/^(\d+)x(\d+)$/', $player_dimensions, $player_dimensions_matches);
    $params_valid = $thumbnail_dimensions_matched > 0 && $player_dimensions_matched > 0;
    if ($params_valid) {
    	$player_width = $player_dimensions_matches[1];
    	$player_height = $player_dimensions_matches[2];
    	$thumbnail_width = $thumbnail_dimensions_matches[1];
    	$thumbnail_height = $thumbnail_dimensions_matches[2];
    }
    
    // thumb dimensions: it must be between minimal and default dimensions
    $thumbnail_width = min($thumbnail_width, $max_width);
    $thumbnail_width = max($thumbnail_width, $min_thumbnail_width);
    $thumbnail_height = min($thumbnail_height, $max_height);
    $thumbnail_height = max($thumbnail_height, $min_thumbnail_height);
    
    $player_width = min($player_width, $max_width);
    $player_width = max($player_width, $min_player_width);
    $player_height = min($player_height, $max_height);
    $player_height = max($player_height, $min_player_height);
        
    $ep = bubblecast_get_clickable_video_thumbnail_html($video_id, $videoNum,
    		$player_width, $player_height, $thumbnail_width, $thumbnail_height);
    
    $videoNum++;
    return $ep;
}

function bubblecast_get_clickable_video_thumbnail_html($video_id, $videoNum,
		$player_width, $player_height, $thumbnail_width, $thumbnail_height,
		$additional_onplay_code = '') {
    global $embeddedQuickcastMovieURL, $playerMovieURL, $bubblecastThumbUrl;
    global $current_user;
    
    get_currentuserinfo();
    
	$is_wide = $player_width > $thumbnail_width || $player_height > $thumbnail_height;
    $is_wide_string = $is_wide ? 'true' : 'false';
    
    $thumbnail_type = ($thumbnail_width > $default_width || $thumbnail_height > $default_height)
    		? 'o' : 'b';
    
    if (!$is_wide) {
    	$player_width = max($player_width, $thumbnail_width);
    	$player_height = max($player_height, $thumbnail_height);
    }
			
    $div_width = $player_width;
    $div_height = $is_wide ? $player_height + 30 : $player_height; // 30 px for Close button
    
    $play_button_width = 135;
    $play_button_height = 135;
    $play_button_left = (int) ((($thumbnail_width - $play_button_width) / 2));
    $play_button_top = (int) (($thumbnail_height - $play_button_height) / 2);

    $bubblecast_player_style = $is_wide ? 'bubblecast_player_wide' : 'bubblecast_player';
    $siteId = get_bubblecast_option('bubblecast_site_id');
    if (!$siteId) {
        $siteId = bubblecast_login();
    }
    $ep =  '<div class="bubblecast_player_wp">';
    $ep .= '<div class="bubblecast_fl_wp"><a href="http://bubble-cast.com/wordpress.html" class="bubblecast_site_link">http://bubble-cast.com</a></div>';
    if (!$siteId && bubblecast_is_admin()) {
        $ep .= ('<div class="bubblecast_cfg_err_wp">' . __('You haven\'t set up Bubblecast login and password. Please, follow installation instructions to finish setup in your administration console at <b>Site Admin -&gt; Settings -&gt; Bubblecast</b>', 'bubblecast') . ' </div>');
    }
    
    $onclick = 'bubblecastShowPlayer(\''.$video_id.'_'.$videoNum.'\','.$is_wide_string.');';
    $onclick .= $additional_onplay_code;
    $onclick = apply_filters('bubblecast_play_button_onclick', $onclick);
    $onclick .= 'return true;';
    
    $ep .= '<div class="bubblecast_fl_wp_thumb"  id="t'.$video_id.'_'.$videoNum.'"><img src="'.$bubblecastThumbUrl.'?podcastId='.$video_id.'&type=' . $thumbnail_type . '&forceCheckProvider=true" width="' . $thumbnail_width . '" height="' . $thumbnail_height . '"/><a class="bubblecast_play_btn" style="left: ' . $play_button_left . 'px; top: ' . $play_button_top . 'px;" onclick="' . $onclick . '"><img src="'.get_plugin_base_dir().'/i/play.png"  alt="Play"/></a></div>';
    $flash_obj = bubblecast_flash_object($player_width, $player_height, $video_id, $videoNum, $playerMovieURL, $siteId, get_option('bubblecast_language'), $current_user->user_login, $current_user->user_pass);
    $flash_div_open = '<div class="'.$bubblecast_player_style.'" id="p'.$video_id.'_'.$videoNum.'" style="width: ' . $div_width . 'px; height: ' . $div_height . 'px;">';
    if (!$is_wide) {
        $flash_div_close = '</div>';
    } else {
        $flash_div_close = '<div class="bubblecast_ws_close_btn" align="center"><a href="#" onclick="javascript:bubblecastHidePlayer(\''.$video_id.'_'.$videoNum.'\','.$is_wide_string.');return false;">'.__("Close").'</a></div>';
        $flash_div_close .= '</div>';
    }
    $ep .= $flash_div_open.$flash_obj.$flash_div_close;
    $ep .= '</div>';
    return $ep;
}

function bubblecast_comment_form($text='') {
    $url = "quickcast_comment.php";
    include($url); 
    $image_btn = get_bubblecast_logo();
    $v .= '<a href="#" onclick="showBubblecastComment(); return false;"><img src="'.$image_btn.'" /> ' . __('Add video comment', 'bubblecast') . '</a>'."\n";
	echo $v;
}
function bubblecast_js(){
	global $current_user;
	
	get_currentuserinfo();
	
    $pluginurl = get_plugin_base_dir().'/';
    echo "\n".'<link href="'.$pluginurl.'bubblecast.css" media="screen" rel="stylesheet" type="text/css"/>'."\n";
    echo "\n".'<script src="'.$pluginurl.'js/bubblecast.js" type="text/javascript"></script>'."\n";
    echo "\n".'<script src="'.$pluginurl.'js/dynamic-js.php?username=' . $current_user->user_login . '&password_hash=' . $current_user->user_pass . '" type="text/javascript"></script>'."\n";
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
    //error_log("bubblecast_save_post = ".$postData->guid);
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
        if(function_exists('wp_create_category')){
            if (wp_create_category('Video')) {
                $defaultCategoryId = get_cat_ID('Video');
            } else {
                // select Uncategorized as a fallback
                $defaultCategoryId = 0;
            }
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
        'categories' => array($defaultCategoryId),
        'use_current_cat' => 'N'
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