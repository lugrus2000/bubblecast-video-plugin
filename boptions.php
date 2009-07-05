<?
    global $user_login, $user_email,$admin_email;
    get_currentuserinfo();
    $reg_url = "http://bubble-cast.com/register.html?userName=".$user_email."&email=".$user_email."&siteURL=".get_option('siteurl');
    $action = "options.php";
    $updated = $_GET['updated'];
    if($updated){
        require_once("bubblecast_utils.php");
        $siteId = bubblecast_login();
?>
<div id="message" class="updated fade"><p><strong>
<?
        if(!$siteId){
             _e('Login to bubblecast failed.');
        }
        else{
             _e('Login successful');

        }
?>
</strong></p></div>
<?
    }
?>
<div class="wrap" style="padding-top:5px">
<h2 style="height:50px;background-repeat:no-repeat;background-image:url('<? echo get_plugin_base_dir().'/i/bubble-big.gif'; ?>');vertical-align:middle;padding-left:65px;">Bubblecast</h2>

<h3>Bubblecast plugin for Wordpress brings users' video to your blog.</h3>
<div id="trackbacksdiv" class="postbox " >
<div class="inside" style="left:10px;">
<ul style="list-style:circle;">
      <li>Add video to the post when you're writing it</li>
      <li>Add video to your comments</li>
      <li>The tag <b>[bubblecast id=123]</b> is pasted from the widget</li>

</ul>

Type in your Bubblecast login and password below and log in.
It should be done only once, after successful logon the plugin will remember the credentials.
If you still don't have Bubblecast account, please, <a href="<? echo $reg_url;?>">register here</a>
</div></div>
<br/>
<br/>
<form method="post" action="<? echo $action; ?>">
<?php
    wp_nonce_field('update-options');
?>
<table id="postcustomstuff">
<tr valign="top">
<th scope="row">
<?php _e('User name') ?>
</th>
<td><input type="text" name="bubblecast_username" value="<?php echo get_option('bubblecast_username'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Password') ?></th>
<td><input type="password" name="bubblecast_password" value="<?php echo get_option('bubblecast_password'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Language') ?></th>
<td>
    <select name="bubblecast_language" value="<?php echo get_option('bubblecast_language'); ?>">
        <option value="en"<?php if (get_option('bubblecast_language') == 'en') { ?> selected="selected"<?php } ?>><?php _e('English') ?></option>
        <option value="ru"<?php if (get_option('bubblecast_language') == 'ru') { ?> selected="selected"<?php } ?>><?php _e('Russian') ?></option>
    </select>
</td>
</tr>
<tr>
<th colspan="2" align="left">
<a href="<? echo $reg_url;?>"><?php _e('Get login here') ?></a> &nbsp;<img src = "<? echo get_plugin_base_dir()."/i/go.gif" ?>"/>
</th>
</tr>
</table>
<input type="hidden" readonly="true" disabled="true" value="<?php echo get_option('bubblecast_site_id'); ?>" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="bubblecast_username,bubblecast_password,bubblecast_language" />
<?php
    settings_fields( 'bubblecast-group' );
?>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save and Login') ?>" />
</p>
</form>
</div>
