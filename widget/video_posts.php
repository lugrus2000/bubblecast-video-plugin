<?php

    $defaultOptions = bubblecast_get_video_posts_widget_default_options();

    // build options to be displayed in the form
    $options = get_option('bubblecast_wvp_options', array());
    $options = array_merge($defaultOptions, $options);

    echo $before_widget;
    echo $before_title;
    echo $options['title'];
    echo $after_title;

    $layout = $options['layout'];
    $vertical = $layout == 'v' || empty($layout);

    $categoryIds = '';
    $first = true;
    foreach ($options['categories'] as $id) {
        if (!$first) {
            $categoryIds .= ',';
        }
        $categoryIds .= $id;
        $first = false;
    }

    $posts = get_posts('numberposts=' . $options['videos'] . '&order=DESC&orderby=date&category=' . $categoryIds);

    // outputting table beginning for horizontal layout
    if (!$vertical) : ?>
        <table>
        <tr>
<?php
    endif; // $vertical

    foreach ($posts as $post) :
        
        // locating first [bubblecast] code if exists to show a thumbnail
        $matches = array();
        $matched = preg_match("/\\[bubblecast id=(.*?)\\]/", $post->post_content, $matches);
        if ($matched > 0) {
            $video_id = $matches[1];
        } else {
    	    // do not render anything is no video is in the post
    	    continue;
        }

        $permalink = get_permalink($post->ID);
        $title = get_the_title($post->ID);

        // outputting cell beginning for horizontal layout
        if (!$vertical) : ?>
            <td>
<?php
        endif;

?>
        <div class="bubblecast_wvp_block">
            <a href="<?php echo $permalink; ?>" title="<?php echo $title; ?>" class="wp_caption"><?php echo $title; ?></a>
<?php
            if ($video_id !== null) : ?>
            <div><a href="<?php echo $permalink; ?>" title="<?php echo $title; ?>"><img src="<?php echo "$bubblecastThumbUrl?podcastId=$video_id&type=w&forceCheckProvider=true"; ?>" alt="<?php echo $title; ?>"/></a></div>
<?php
            endif; ?>
        </div>
<?php

        // outputting cell end for horizontal layout
        if (!$vertical) : ?>
            </td>
<?php
        endif;

    endforeach;

    // outputting table end for horizontal layout
    if (!$vertical) : ?>
        </tr>
        </table>
<?php
    endif; // $vertical

    echo $after_widget;
                        
?>