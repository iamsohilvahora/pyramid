<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pyramidtimesystems
 */

$post_id = $wp_query->ID;
$news_tag_terms = get_the_terms($post_id, 'news_tags');
// echo "<pre>";
// print_r($terms);


$news_cat_terms = get_the_terms($post_id, 'news_category');
$news_cat_list = "";
if($news_cat_terms && !is_wp_error($news_cat_terms)){
    foreach($news_cat_terms as $term){
        // echo "<a href=".get_term_link($term->term_id).">".$term->name."</a>";
        $news_cat_list .=  "<a href=".get_term_link($term->term_id).">".$term->name."</a>". ", ";
    }
}


?>

<div class="articales-blog-news">
	<span><?php echo get_the_date('m/d/Y'); echo '  '; echo get_the_time('g:i A');
	 echo "  ";  ?></span>

	<h3><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title();?></a></h3>
	<div class="articles-inner-new"> 
		<div class="articles-img-new">
			<?php 
				if(has_post_thumbnail($post_id)):
					$thumb_img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full'); 
			?>
					<img src="<?php echo $thumb_img[0]; ?>" alt="<?php echo get_the_title(); ?>" />
			<?php
				else: 
					 echo "<p>No image available</p>"; 
				endif;	
			?>	
		</div>
		<div class="articles-text-new">
			<?php
				$content = get_the_content();
				// $content = strip_tags($content);
				$contentText = substr($content, 0, 50);
				// print_r($contentText);
			?>
			<p><?php //echo $contentText;
				// the_excerpt();
						$excerpt = get_the_excerpt();
						
						$excerpt = substr( $excerpt, 0, 150 ); // Only display first 100 characters of excerpt
						$result = substr( $excerpt, 0, strrpos( $excerpt, ' ' ) );
						echo $result;
			?></p>
			<a class="" href="<?php echo get_the_permalink(); ?>">Read More</a>
		</div>
	</div>

	<strong>Tags:</strong>
	<div class="tag-link">
		<?php
			if($news_tag_terms && !is_wp_error($news_tag_terms)){
				foreach($news_tag_terms as $term){
					echo "<a href=".get_term_link($term->term_id).">".$term->name."</a>";
				}
			}
		?>
	</div>
	<p class="category-post">Posted in <?php echo $news_cat_list ? rtrim($news_cat_list, ", ") : ""; ?> By <?php echo get_the_author(); ?></p>



</div>	
