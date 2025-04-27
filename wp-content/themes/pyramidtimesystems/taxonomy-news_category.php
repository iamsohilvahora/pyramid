<?php
	$queried_object = get_queried_object();
	$term_id = $queried_object->term_id;

	$news_total_blog_wp_query = new WP_Query(
		array(
			'post_type' => 'news',
			'posts_per_page' => -1,
	));

	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$posts_per_page = -1;

	$start_post = ($paged > 1) ? (($posts_per_page * ($paged - 1)) + 1) : $paged;
	$last_post = $posts_per_page * $paged;
	$total_post = $news_total_blog_wp_query->post_count;
	$last_post = ($last_post > $total_post) ? $total_post : $last_post;

	if($_GET['order'] == 'created_time'){
		$args = array(
			'post_status' => 'publish',
			'post_type' => 'news',
			'posts_per_page' => $posts_per_page,
			'orderby'=> 'post_date', 
			'order' => 'DESC',
			// 'paged' => $paged,
			'tax_query' => array(
			    array(
			      'taxonomy'=> 'news_category',
			      'field' => 'term_id',
			      'terms' => $term_id,
			      'oparator' => 'IN'
			    ))
		);
	}
	else if($_GET['order'] == 'user'){
		$args = array(
			'post_status' => 'publish',
			'post_type' => 'news',
			'posts_per_page' => $posts_per_page,
			'orderby'=> 'author + post_date', 
			'order' => 'ASC',
			'tax_query' => array(
			    array(
			      'taxonomy'=> 'news_category',
			      'field' => 'term_id',
			      'terms' => $term_id,
			      'oparator' => 'IN'
			    ))
		);
	}
	else{
		$args =  array(
	 		'post_status' => 'publish',
			'post_type' => 'news',
			'posts_per_page' => -1,
			'orderby'=> 'post_date', 
			'order' => 'ASC',
			'tax_query' => array(
			    array(
			      'taxonomy'=> 'news_category',
			      'field' => 'term_id',
			      'terms' => $term_id,
			      'oparator' => 'IN'
			    ))
			);	
	}
			
	// the query
	$wp_query = new WP_Query( $args ); 

	global $wp;
	$current_url = home_url(add_query_arg(array(), $wp->request));
?>

	<div class="container"> 
		<?php if ($wp_query->have_posts()): ?>
			<div class="toolbar">
				<div class="sorter">
					<div class="sort-by">
						<label>Sort By</label>
						<select id="sort_news" title="Sort By">
							<option value="<?php echo $current_url; ?>/?order=created_time" <?php echo $_GET['order'] == 'created_time' ? 'selected' : ''; ?>>Created At</option>
							<option value="<?php echo $current_url; ?>/?order=user" <?php echo $_GET['order'] == 'user' ? 'selected' : ''; ?>>Added By</option>
						</select>
					</div>
				</div>
	            <div class="pager-right">
					<div class="count-container">
						<p class="amount amount--has-pages">
							<strong><?php echo $wp_query->post_count; ?> Item(s)</strong>	
						</p>
					</div>

					<div class="d-flex justify-content-center"> 			
					</div>
				</div>
			</div>

			<?php
			/* Start the Loop */
			while ( $wp_query->have_posts() ) :
				$wp_query->the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part('template-parts/content', 'news');
			endwhile;
			?>

			<div class="toolbar">
				<div class="sorter">
					<div class="sort-by">
						<label>Sort By</label>
						<select id="sort_news" title="Sort By">
							<option value="<?php echo $current_url; ?>/?order=created_time" <?php echo $_GET['order'] == 'created_time' ? 'selected' : ''; ?>>Created At</option>
							<option value="<?php echo $current_url; ?>/?order=user" <?php echo $_GET['order'] == 'user' ? 'selected' : ''; ?>>Added By</option>
						</select>
					</div>
				</div>
	            <div class="pager-right">
					<div class="count-container">
						<p class="amount amount--has-pages">
							<strong><?php echo $wp_query->post_count; ?> Item(s)</strong>	
						</p>
					</div>

					<div class="d-flex justify-content-center">
					</div>
				</div>
			</div>
		<?php
		else :
			echo "No Posts in this Category.";
		endif;
		wp_reset_postdata();
		?>
	</div>

<?php