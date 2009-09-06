
<script type="text/javascript">
<?php
    require("config.php");
    $siteId = get_bubblecast_option("bubblecast_site_id");
    if(!$siteId){
        $siteId = bubblecast_login();
    }
    global $embeddedQuickcastMovieURL, $playerMovieURL,$pluginMode;
    $flashVars = 'siteId='.$siteId.'&amp;languages=' . get_bubblecast_option('bubblecast_language') . '&amp;pluginMode='.$pluginMode.'&amp;pluginUserName='.$user_login.'&amp;pluginUserEmail='.$user_email.'&amp;adminEmail='.$admin_email;
?>
function onPostInsert(html){
    var win = window.opener ? window.opener : window.dialogArguments;
    if ( !win )
        win = top;
    tinyMCE = win.tinyMCE;
    if ( typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content') ) {
        tinyMCE.selectedInstance.getWin().focus();
        tinyMCE.execCommand('mceInsertContent', false, html);
    } else {
	if (win.edInsertContent != null) {
            win.edInsertContent(win.edCanvas, html);
        } else {
    	    // fallback to manual insert
    	    insertAtCaret(win.document, 'content', 'post', 'content', html);
        }
    }

}
function onCommentInsert(html){
    insertAtCaret(document, 'comment', 'commentform', 'comment', html);
}
</script>
<style>
    a {
    color: #777; text-decoration: underline; font-size: 12px;
    }
</style>
<div align="center">
<?php if (!$siteId): ?>
<div align="center" style="color: red; padding: 5px;">
<?php if (bubblecast_is_admin()): ?>
<?php _e('You haven\'t set up Bubblecast login and password. Please, follow installation instructions to finish setup in your administration console at <b>Site Admin -&gt; Settings -&gt; Bubblecast</b>', 'bubblecast');?>
<?php else: ?>
<?php _e('Bubblecast plugin is not configured properly. Please, contact administrator.', 'bubblecast');?>
<?php endif; ?>
</div>
<?php else: ?>
<div><a href="http://bubble-cast.com">http://bubble-cast.com</a></div>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"                width="475" height="375" id="quickcast" align="middle">            <param name="allowScriptAccess" value="always" />            <param name="movie" value="<? echo $embeddedQuickcastMovieURL;?>" />            <param name="flashvars" value="<? echo $flashVars; ?>" />            <param name="quality" value="high" />            <param name="allowfullscreen" value="true"/>            <param name="bgcolor" value="#ededed" />                <embed src="<? echo $embeddedQuickcastMovieURL;?>"                       quality="high" bgcolor="#ededed" width="475" height="375" name="quickcast"                       flashvars="<? echo $flashVars; ?>" allowfullscreen="true"                       align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />        </object>
<?php endif; ?>
</div>