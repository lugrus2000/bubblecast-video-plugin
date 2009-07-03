
<div id="leightbox_container">
<div id="bubblecast_comment" class="leightbox">

<?
    global $user_login, $user_email,$admin_email,$pluginMode ;
    $pluginMode = 'wpc';
    get_currentuserinfo();
    $admin_email = get_option('admin_email');
    include("iquickcast.php");
?>
	<p class="footer" align="center">
		<a href="#" class="lbAction" rel="deactivate">Close</a>
	</p>
</div>
</div>

