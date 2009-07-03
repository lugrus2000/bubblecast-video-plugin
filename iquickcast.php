
<script type="text/javascript">
<?
    require("config.php");
    $siteId = bubblecast_login();    
    global $embeddedQuickcastMovieURL, $playerMovieURL,$pluginMode;
    $flashVars = 'siteId='.$siteId.'&amp;languages=' . get_option('bubblecast_language') . '&amp;pluginMode='.$pluginMode.'&amp;pluginUserName='.$user_login.'&amp;pluginUserEmail='.$user_email.'&amp;adminEmail='.$admin_email;
?>
function onPostInsert(html){
//    alert("onPostInsert = " + html);
    var win = window.opener ? window.opener : window.dialogArguments;
    if ( !win )
        win = top;
    tinyMCE = win.tinyMCE;
    if ( typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content') ) {
        tinyMCE.selectedInstance.getWin().focus();
        tinyMCE.execCommand('mceInsertContent', false, html);
    } else {
        win.edInsertContent(win.edCanvas, html);
    }

}
function onCommentInsert(html){
//    alert("insertAtCaret = " + html);
    insertAtCaret('comment',html);
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
You haven't set up Bubblecast login and password. Please, follow installation instructions to finish setup in your administration console
<?php else: ?>
Bubblecast plugin is not configured properly. Please, contact administrator.
<?php endif; ?>
</div>
<?php else: ?>
<a href="http://bubble-cast.com">http://bubble-cast.com</a>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"                width="475" height="375" id="quickcast" align="middle">            <param name="allowScriptAccess" value="always" />            <param name="movie" value="<? echo $embeddedQuickcastMovieURL;?>" />            <param name="flashvars" value="<? echo $flashVars; ?>" />            <param name="quality" value="high" />            <param name="allowfullscreen" value="true"/>            <param name="bgcolor" value="#ededed" />                <embed src="<? echo $embeddedQuickcastMovieURL;?>"                       quality="high" bgcolor="#ededed" width="475" height="375" name="quickcast"                       flashvars="<? echo $flashVars; ?>" allowfullscreen="true"                       align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />        </object>
<?php endif; ?>
</div>
<script type="text/javascript">
    var wtop = window.top ? window.top : window.parent;
			    wtop.jQuery("#TB_window").css(
    				{
    			        width: "510px",
    			        height: "415px"

    		      	}

    			);
			    wtop.jQuery("#TB_iframeContent").css(
    				{
    			        width: "510px",
    			        height: "415px"

    		      	}

    			);

</script>