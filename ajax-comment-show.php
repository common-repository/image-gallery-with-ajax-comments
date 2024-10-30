<?php // Do not delete these lines

if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');
if (!empty($post->post_password)) { // if there's a password
	if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
?>

<h2><?php _e('Password Protected'); ?></h2>
<p><?php _e('Enter the password to view comments.'); ?></p>

<?php return;
	}
}

	/* This variable is for alternating comment background */

$oddcomment = 'alt';

?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>
	
	
		<?php foreach ($comments as $comment) : ?>
		
		<div class="commentblock">
              <div class="userimage"> <?php echo get_avatar( $comment, $size = '44', $default = '' ); ?> <span class="guestid"><?php comment_author_link() ?></span><span class="timeadded"><?php comment_date('F jS, Y') ?> <?php _e('at');?> <?php comment_time() ?></span> </div>
              <div class="usercomment">
                <p><?php comment_text() ?></p>
				<?php if ($comment->comment_approved == '0') : ?>
				<em><?php _e('Your comment is awaiting moderation.'); ?></em>
<?php endif; ?>
                <div class="actionbtns"><?php edit_comment_link('Edit','<span>','</span>'); ?> </div>
              </div>
              
              
              <div class="clear"></div>
            </div>

		
<?php /* Changes every other comment to a different class */
	if ('alt' == $oddcomment) $oddcomment = '';
	else $oddcomment = 'alt';
?>

<?php endforeach; /* end for each comment */ ?>
	

<?php else : // this is displayed if there are no comments so far ?>

<?php 

if ('open' == $post->comment_status) : 

?>
	<!-- If comments are open, but there are no comments. -->
	<?php else : // comments are closed ?>

	<!-- If comments are closed. -->
<p class="nocomments">Comments are closed.</p>

	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

		<h3 id="respond">Leave a Reply</h3>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>

<?php else : ?>
<div id="post_comment_show" align="center"></div>
<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform" onSubmit="return Postcommentshow()">
<?php if ( $user_ID ) : ?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a> &#124 <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout</a></p>

<?php else : ?>

<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="40" tabindex="1"  class="text_field"/>
<label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="40" tabindex="2" class="text_field" />
<label for="email"><small>Email (will not be published) <?php if ($req) echo "(required)"; ?></small></label></p>

<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="40" tabindex="3" class="text_field" />
<label for="url"><small>Website</small></label></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> <?php _e('You can use these tags&#58;'); ?> <?php echo allowed_tags(); ?></small></p>-->

<p><textarea name="comment" id="comment" cols="60" rows="10" tabindex="4" class="text_field"></textarea></p>

<p align="center"><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment"  class="post_comment"/>
<input type="hidden" name="comment_post_ID"  id="comment_post_ID" value="<?php echo $post->ID; ?>" />
</p>

<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>