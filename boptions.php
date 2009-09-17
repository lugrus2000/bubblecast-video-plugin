<?php
    global $user_login, $user_email,$admin_email;
    get_currentuserinfo();
    $reg_url = "http://bubble-cast.com/register.html?userName=".$user_email."&email=".$user_email."&siteURL=".get_option('siteurl');
    $action = "options.php";
    $updated = $_GET['updated'];
    if($updated){
        require_once("bubblecast_utils.php");
        $bubblecast_username = get_option("bubblecast_username");
        $bubblecast_password = get_option("bubblecast_password");
        $siteId = bubblecast_remote_login($bubblecast_username,$bubblecast_password);
?>
<div id="message" class="updated fade"><p><strong>
<?php
        if(!$siteId){
            if(is_wpmu()){
                update_site_option('bubblecast_site_id',"");
            }
            else{
                update_option('bubblecast_site_id',"");
            }
             _e('Login to bubblecast failed.', 'bubblecast');
        }
        else{
            if(is_wpmu()){
                update_site_option('bubblecast_username',$bubblecast_username);
                update_site_option('bubblecast_password',$bubblecast_password);
                update_site_option('bubblecast_language',get_option('bubblecast_language'));
                update_site_option('bubblecast_site_id',$siteId);
            }
            else{
                update_option('bubblecast_site_id',$siteId);
            }
            _e('Login successful', 'bubblecast');

        }
?>
</strong></p></div>
<?php
    }
?>
<div class="wrap" style="padding-top:5px">
<h2 style="height:50px;background-repeat:no-repeat;background-image:url('<?php echo get_plugin_base_dir().'/i/bubble-big.gif'; ?>');vertical-align:middle;padding-left:65px;">Bubblecast</h2>

<h3><?php _e('Bubblecast plugin for Wordpress brings users\' video to your blog.', 'bubblecast');?></h3>
<div class="inside" style="left:10px;">
<ul style="list-style:circle;margin-left:30px">
      <li><?php _e('Add video to the post when you\'re writing it', 'bubblecast');?></li>
      <li><?php _e('Add video to your comments', 'bubblecast');?></li>
      <li><?php _e('The tag <b>[bubblecast id=123]</b> is pasted from the widget', 'bubblecast');?></li>

</ul>

<?php _e('Type in your Bubblecast login and password below and log in.', 'bubblecast');?>
<?php _e('It should be done only once, after successful logon the plugin will remember the credentials.', 'bubblecast');?>
<?php _e('If you still don\'t have Bubblecast account, please,', 'bubblecast');?> <a href="<?php echo $reg_url;?>"><?php _e('register here', 'bubblecast');?></a>
</div>
<br/>
<br/>
<form method="post" action="<?php echo $action; ?>">
<?php
    wp_nonce_field('update-options');
?>
<table id="postcustomstuff">
<tr valign="top">
<th scope="row">
<?php _e('User name', 'bubblecast') ?>
</th>
<td><input type="text" name="bubblecast_username" value="<?php echo get_bubblecast_option('bubblecast_username'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Password', 'bubblecast') ?></th>
<td><input type="password" name="bubblecast_password" value="<?php echo get_bubblecast_option('bubblecast_password'); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Language', 'bubblecast') ?></th>
<td>
    <select name="bubblecast_language" value="<?php echo get_option('bubblecast_language'); ?>">
        <option value="en"<?php if (get_bubblecast_option('bubblecast_language') == 'en') { ?> selected="selected"<?php } ?>><?php _e('English', 'bubblecast') ?></option>
        <option value="ru"<?php if (get_bubblecast_option('bubblecast_language') == 'ru') { ?> selected="selected"<?php } ?>><?php _e('Russian', 'bubblecast') ?></option>
        <option value="it"<?php if (get_bubblecast_option('bubblecast_language') == 'it') { ?> selected="selected"<?php } ?>><?php _e('Italian', 'bubblecast') ?></option>
        <option value="nl"<?php if (get_bubblecast_option('bubblecast_language') == 'nl') { ?> selected="selected"<?php } ?>><?php _e('Dutch', 'bubblecast') ?></option>
    </select>
</td>
</tr>
<tr>
<th colspan="2" align="left">
<a href="<?php echo $reg_url;?>"><?php _e('Get login here', 'bubblecast') ?></a> &nbsp;<img src = "<?php echo get_plugin_base_dir()."/i/go.gif" ?>"/>
</th>
</tr>
</table>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="bubblecast_username,bubblecast_password,bubblecast_language" />
<?php
    settings_fields( 'bubblecast-group' );
?>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save and Login', 'bubblecast') ?>" />
</p>
</form>
</div>
