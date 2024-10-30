<?php 
/*
Plugin Name:Image Gallery and comments
Plugin URI: http://www.matainja.com/wordpress/
Description: Plugin for Comment of each images with silder gallery
Author: C. Matainja
Version: 1.0
Author URI: http://www.matainja.com
*/

define('PLUGIN_GALLERYCOMMENTS_PATH', WP_PLUGIN_URL.'/gallerycomment/'); 
define('PLUGIN_GALLERYCOMMENTS_FILE_1', PLUGIN_GALLERYCOMMENTS_PATH.'ajax/commentshow.php');
define('PLUGIN_GALLERYCOMMENTS_FILE_2', PLUGIN_GALLERYCOMMENTS_PATH.'ajax/post-ajax-comment.php');



add_shortcode('gallery_comment', 'matagallery_getproducts');
function matagallery_getproducts($atts) {
?>
<script language="javascript">
var loading_header = '<div id="preloader" align="center"><img src="<?php echo  PLUGIN_GALLERYCOMMENTS_PATH; ?>images/preajax.gif" /></div>';
var ajax_url_loading='<?php echo PLUGIN_GALLERYCOMMENTS_PATH?>';
function commentshow(id)
{
$("#comment_show").empty().append(loading_header);
var result = $.ajax({
		type: "POST",
		url: "<?php echo  PLUGIN_GALLERYCOMMENTS_FILE_1; ?>",
		async: false,
		data: "id="+id  
	}).responseText;
	

$("#comment_show").empty().append(result);
	$("#preloader").remove();
}

function Postcommentshow()
{
var author;
var email;
var url;
var comment;
var comment_post_ID;
$("#post_comment_show").empty().append(loading_header);
author=$("#author").val();
email=$("#email").val();
url=$("#url").val();
comment=$("#comment").val();
comment_post_ID=$("#comment_post_ID").val();
var result = $.ajax({
		type: "POST",
		url: "<?php echo  PLUGIN_GALLERYCOMMENTS_FILE_2; ?>",
		async: false,
		data: "submit_post=1&author="+author+"&email="+email+"&url=" +url+"&comment="+comment+"&comment_post_ID="+comment_post_ID+"&remember=1",
	}).responseText;
	
if(result=='')
{
commentshow(comment_post_ID)
$("#author").val()='';
$("#email").val()='';
$("#url").val()='';
$("#comment").val()='';
}
else
{
$("#post_comment_show").empty().append(result);
}


$("#preloader").remove();
return false;


}
</script>

 <script type="text/javascript" src="<?php echo  PLUGIN_GALLERYCOMMENTS_PATH; ?>js/jquery-1.4.4.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo  PLUGIN_GALLERYCOMMENTS_PATH; ?>css/jquery.ad-gallery.css">
<link href="<?php echo  PLUGIN_GALLERYCOMMENTS_PATH; ?>css/gallery.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="<?php echo  PLUGIN_GALLERYCOMMENTS_PATH; ?>js/jquery.ad-gallery.js?rand=995"></script>
<script language="javascript" src="<?php echo  PLUGIN_GALLERYCOMMENTS_PATH; ?>js/ajax-comment-submit.js"></script>


<?php

extract(shortcode_atts(array(
	      'id' => '-1',
	     
     ), $atts));


$args = array(
	'post_type' => 'attachment',
	'numberposts' => -1,
	'post_status' => null,
	'post_parent' => $id, // any parent
	); 
$attachments = get_posts($args);
if ($attachments) {
?>
		  <div id="photogallery"><div id="gallery" class="ad-gallery">
            <div class="ad-image-wrapper"> </div>
            <div class="ad-controls"> </div>
            <div class="ad-nav">
              <div class="ad-thumbs">
                <ul class="ad-thumb-list">
		<?php 		

$image_count=0;
	foreach ($attachments as $post) {
	
	if($image_count==0)
	$id=$post->ID;
	
	
	$long_des=strip_tags($post->post_content);
	$big_img=wp_get_attachment_image_src($post->ID,'large',false);
	
	$Thumb_img=wp_get_attachment_image_src($post->ID,'thumbnail',false);
	?>

                <li> <a href="<?php echo $big_img[0]?>"> <img src="<?php echo $Thumb_img[0]?>" class="image<?php echo $image_count?>" title="<?php echo the_title()?>" longdesc="<?php echo $long_des ?>"  id="<?php echo $post->ID ?>"> </a> </li>
                  
				  <?php 
				
				 $image_count++;
				 
				 }  ?>
                </ul>
              </div>
            </div>
          </div></div>
		  
		  <?php } 
		  ?>
		  <img  class="arrow" src="<?php echo WP_PLUGIN_URL ?>/gallerycomment/images/comments.png" width="96" height="33" />
<div id="commentsbox">
<div id="comment_show">
            
            </div>
            
            
            <div class="clear"></div>
          </div>
		  <?php

	
	
	return $retval;
}

//*************** Admin function ***************
function matagallery_admin() {
	include('gallery_comment_admin.php');
}

function matagallery_admin_actions() {
    add_options_page("Gallery Comment", "Gallery Comment Manual", 1, "image_gallery_comment", "matagallery_admin");
}

add_action('admin_menu', 'matagallery_admin_actions');

?>