<?php
/***************************************** Year Shortcode ********************************/
function wp_year_shortcode_func(){
	$year = date('Y');
	return $year;
}
add_shortcode('year', 'wp_year_shortcode_func');

/******************************** Header cart menu and search form ***********************/
function wp_menu_serach_shortcode_func(){
		wp_nav_menu(
			array(
				'theme_location' => 'menu-2',
				'menu_id'        => 'secondary-menu'
			)
		);
?>

<!-- Product search form -->
<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
	<div class="search-bar-select hidden-sm hidden-xs">
			<?php
			global $wp_query;
			wc_product_dropdown_categories(array(
                'orderby'    => 'id',
                'order'      => 'asc',
                'hide_empty' => false,
               	'parent' => 0,
                'hierarchical' => false,		
                'show_count' => 0,
				'show_uncategorized' => false,
				'show_option_all'   => 'All',
				'selected'           => isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : '',
				'show_option_none'   => '',
				'id' => '',
				'option_none_value'  => 0,
				'value_field'        => 'term_id',
				'taxonomy'           => 'product_cat',
				'name'               => 'product_cat',
				'class'              => 'dropdown_product_cat',
				'exclude' => '15, 453, 454',
			));
			?>
    </div>

	<div class="search-bar-input">
        <input type="search" class="search-field" name="s" id="s" value="<?php echo get_search_query(); ?>" placeholder="Search entire store here..." autocomplete="off" maxlength="128" required />
    </div>
    <input type="hidden" name="post_type" value="product">

	<div class="search-bar-btn">
	    <button type="submit"><i class="fa fa-search"></i></button>
	</div>
</form>
<?php
}
add_shortcode('menu_search_shortcode', 'wp_menu_serach_shortcode_func');

/******************************** Shortcode for latest post ********************************/
function latest_post_shortcode_func(){ ?>
	<div class="latest-articles-container">
	<?php
		$latest_news_blog_args = array(
			'post_type' => 'news',
			'posts_per_page' => 3,
			'post_status' => 'publish',
			'orderby'=> 'post_date', 
			'order' => 'DESC',
		);
	$latest_news_blog_query = new WP_Query($latest_news_blog_args);

	if($latest_news_blog_query->have_posts()):	   
		while($latest_news_blog_query->have_posts()):
			$latest_news_blog_query->the_post();
			$post_id = $latest_news_blog_query->ID; 
			?>
				<div class="articles-post">
					<h3><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title();?></a></h3>
					<div class="articles-inner"> 
						<div class="articles-img">
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
						<div class="articles-text">
							<?php
								$content = get_the_content();
								$contentText = substr($content, 0, 100);
							?>
							<p><?php echo $contentText; ?></p>
							<a class="read-more-button" href="<?php echo get_the_permalink(); ?>">Read More</a>
						</div>
					</div>
				</div>						
	<?php
		endwhile; 
	else:
		echo "<p>No post found</p>";
	endif;
	/* Restore original Post Data */
	wp_reset_postdata(); 

	?>
	</div>
	<?php
}
add_shortcode('latest_post_shortcode', 'latest_post_shortcode_func');

/******************************** Header cart menu and search form for mobile ***********************/
function wp_menu_serach_shortcode_func_mobile(){ ?>
<div class="mob-nav">
	<div class="form-wrap">
		<a href="#" class="search-toggle-menu"><i class="fas fa-search"></i><span>Search</span></a>
		<div class="search-form-desc">
			<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
				<div class="search-bar-select hidden-sm hidden-xs">
					<?php
					wc_product_dropdown_categories(array(
			           'orderby'    => 'id',
		               'order'      => 'asc',
		               'hide_empty' => false,
		               'parent' => 0,
		               'hierarchical' => true,		
		               'show_count' => 0,
		   				'show_uncategorized' => false,
		   				'show_option_all'   => 'All',
		   				'show_option_none'   => '',
		   				'id' => 'mob_product_cat',
		   				'option_none_value'  => 0,
		   				'value_field'        => 'term_id',
		   				'taxonomy'           => 'product_cat',
		   				'name'               => 'product_cat',
		   				'class'              => 'dropdown_product_cat',
		   				'exclude' => '15, 453, 454',
			            )
					);
					?>
			    </div>

				<div class="search-bar-input">
			        <input type="search"  class="search-field" name="s" value="<?php echo get_search_query(); ?>" placeholder="Search entire store here..." autocomplete="off" maxlength="128" required />
			    </div>

			    <input type="hidden" name="post_type" value="product">
				<div class="search-bar-btn">
				    <button type="submit"><i class="fa fa-search"></i></button>
				</div>
			</form>
		</div>
	</div>
	<nav id="site-navigation" class="main-navigation">
		<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'pyramidtimesystems' ); ?></button>
			<?php
		global $wp_query;
		wp_nav_menu(
			array(
				'theme_location' => 'menu-17',
				'menu_id'        => 'mobile-menu',
			)
		);
		?>
	</nav>
</div>
<?php
}
add_shortcode('mobile_menu_search_shortcode', 'wp_menu_serach_shortcode_func_mobile');

// For news category archive page
function display_news_category_post(){
	get_template_part('taxonomy-news_category');
}
add_shortcode('news_category_shortcode', 'display_news_category_post');

// For news tag archive page
function display_news_tags_post(){
	get_template_part('taxonomy-news_tags');
}
add_shortcode('news_tags_shortcode', 'display_news_tags_post');

/******************************** Shortcode for news post ********************************/
function news_post_shortcode_func(){ ?>
	<div class="container"> 
	<?php 
		$news_total_blog_wp_query = new WP_Query(
			array(
				'post_type' => 'news',
				'posts_per_page' => -1,
		));

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$posts_per_page = 10;

		$start_post = ($paged > 1) ? (($posts_per_page * ($paged - 1)) + 1) : $paged;
		$last_post = $posts_per_page * $paged;
		$total_post = $news_total_blog_wp_query->post_count;
		$last_post = ($last_post > $total_post) ? $total_post : $last_post; 

		if($_GET['order'] == 'created_time'){
			$latest_news_blog_args = array(
				'post_type' => 'news',
				'posts_per_page' => $posts_per_page,
				'post_status' => 'publish',
				'orderby'=> 'post_date', 
				'order' => 'DESC',
				'paged' => $paged,
			);
		}
		else if($_GET['order'] == 'user'){
			$latest_news_blog_args = array(
				'post_type' => 'news',
				'posts_per_page' => $posts_per_page,
				'post_status' => 'publish',
				'orderby'=> 'author + post_date', 
				'order' => 'ASC',
				'paged' => $paged,
			);
		}
		else{
			$latest_news_blog_args = array(
				'post_type' => 'news',
				'posts_per_page' => $posts_per_page,
				'post_status' => 'publish',
				'orderby'=> 'post_date', 
				'order' => 'ASC',
				'paged' => $paged,
			);
		}
		
		$wp_query_news = new WP_Query($latest_news_blog_args); 

		global $wp;
		$current_url = home_url( add_query_arg( array(), $wp->request ) );
		?>

		<?php if($wp_query_news->have_posts()): ?>	
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
					<p class="amount amount--has-pages"><?php echo $start_post; echo '-'; echo $last_post; ?> of <?php echo $total_post; ?></p>
				</div>

				<div class="d-flex justify-content-center"> 
					<?php wpex_pagination($wp_query_news); ?> 			
			</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
		if($wp_query_news->have_posts()):	   
			while($wp_query_news->have_posts()):
				$wp_query_news->the_post();
				$post_id = $wp_query_news->ID;
				$news_tag_terms = get_the_terms($post_id, 'news_tags');
				$news_cat_terms = get_the_terms($post_id, 'news_category');
				$news_cat_list = "";

				if($news_cat_terms && !is_wp_error($news_cat_terms)){
				    foreach($news_cat_terms as $term){
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
									$contentText = substr($content, 0, 50);
								?>
								<p><?php 
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
		<?php
			endwhile; 
		else:
			echo "<p>No post found</p>";
		endif;
		wp_reset_postdata();  ?>

		<?php if($wp_query_news->have_posts()): ?>	
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
					<p class="amount amount--has-pages"><?php echo $start_post; echo '-'; echo $last_post; ?> of <?php echo $total_post; ?></p>
				</div>

				<div class="d-flex justify-content-center"> 
					<?php wpex_pagination($wp_query_news); ?> 			
				</div>
			</div>
		</div>
		<?php endif; ?>

	</div>
	<?php
}
add_shortcode('news_post_shortcode', 'news_post_shortcode_func');

/*********************** Shortcode for manual punch clocks - savings per employee (calculator) ********************************/
function manual_punch_clocks_savings_per_employee_func(){ ?>
	<form action="" id="contactForm" method="post" class="savings_per_employee_form">
	    <div class="fieldset">
	        <ul class="form-list">
	            <li class="fields">
	                <div class="field">
	                    <label for="employee_wage1" class="required">Enter Your Average Employee Hourly Wage</label>
	                    <div class="input-box">
	                        <input name="employee_wage" id="employee_wage1" title="Average Employee Wage" value="" class="wage-change input-text required-entry" type="number" min="0" max="9999999">
	                    </div>
	                </div>

	                <div class="field">
	                    <label for="personnel_wage1" class="required">Enter Your Payroll Personnel Hourly Wage</label>
	                    <div class="input-box">
	                        <input name="personnel_wage" id="personnel_wage1" title="Average Personnel Wage" value="" class="wage-change input-text required-entry" type="number" min="0" max="9999999">
	                    </div>
	                </div>
	            </li>
	        </ul>
	    </div>

	    <div class="savings-table">
			<table>
				<tbody>
					<tr>
					  <th>Time Clock Savers</th>
					  <th>Minutes Saved Per Day Per Employee</th>
					  <th>Sample Annual Savings Per Employee</th>
					  <th>Your Annual Savings Per Employee</th>
					</tr>
					<tr>
					  <td>Overspending &amp; Fraud</td>
					  <td>48</td>
					  <td>$2088.00</td>
					  <td class="employee-saving" id="saving-table-over">-</td>
					</tr>
					<tr>
					  <td>Calculation Errors</td>
					  <td>7% of payroll</td>
					  <td>$1461.60</td>
					  <td class="employee-saving" id="saving-table-error">-</td>
					</tr>
					<tr>
					  <td>Time Card Audit Time</td>
					  <td>.5</td>
					  <td>$43.50</td>
					  <td class="employee-saving" id="saving-table-audit">-</td>
					</tr>
					<tr>
					  <td>Manual Payroll Management</td>
					  <td>.5</td>
					  <td>$43.50</td>
					  <td class="employee-saving" id="saving-table-payroll">-</td>
					</tr>
					<tr>
					  <td>Buddy Punching</td>
					  <td>2% of payroll</td>
					  <td>$417.60</td>
					  <td class="employee-saving" id="saving-table-buddy">-</td>
					</tr>
					<tr>
					  <td>Swipe Card Punch Method</td>
					  <td>.5</td>
					  <td>$21.75</td>
					  <td class="employee-saving" id="saving-table-swipe">-</td>
					</tr>
					<tr>
					  <td>Prox Badge Punch Method</td>
					  <td>1</td>
					  <td>$43.50</td>
					  <td class="employee-saving" id="saving-table-prox">-</td>
					</tr>
					<tr>
					  <td>Biometric Punch Method</td>
					  <td>.5</td>
					  <td>$21.75</td>
					  <td class="employee-saving" id="saving-table-bio">-</td>
					</tr>
				</tbody>
			</table>
	        <p>
	        	<small>Sample <b><i>Savings Per Employee</i></b> based on Average Employee Wage of $10/HR, Payroll Personnel Wage of $20/HR and a 261-day work year. Actual SPE dependent upon each individual situation.
	        	</small>
	        </p>
	    </div>  
	</form>
<?php }
add_shortcode('manual_punch_clocks_savings_per_employee', 'manual_punch_clocks_savings_per_employee_func');

/*********************** Shortcode for Synchronized clock systems - savings per employee (calculator) *************************/
function synchronized_clock_systems_savings_per_employee_func(){ ?>
	<form action="" id="calc_contact_form" method="post" class="calc_savings_per_employee_form">
	    <div class="fieldset">
	        <ul class="form-list">
	            <li class="fields">
	                <div class="field three-field">
	                    <label for="employee_wage" class="required">Enter Your Average Employee Hourly Wage</label>
	                    <div class="input-box">
	                        <input name="employee_wage" id="employee_wage" title="Average Employee Wage" value="" type="number" min="0" max="9999999" class="wage-change input-text required-entry">
	                    </div>
	                </div>
	                <div class="field three-field">
	                    <label for="personnel_wage" class="required">Enter  Your Maintenance Personnel Hourly Wage</label>
	                    <div class="input-box">
	                        <input name="personnel_wage" id="personnel_wage" title="Average Personnel Wage" value="" type="number" min="0" max="9999999" class="wage-change input-text required-entry">
	                    </div>
	                </div>
	                <div class="field three-field">
	                    <label for="no_clocks" class="required">Enter  Number of Clocks</label>
	                    <div class="input-box">
	                        <input name="no_clocks" id="no_clocks" title="Number of Clocks" value="" type="number" min="0" max="9999999" class=" wage-change input-text required-entry">
	                    </div>
	                </div>
	            </li>
	        </ul>
	    </div>
	   
	    <div class="savings-table">
	        <h3>Labor Savings</h3>
	        <table>
	            <tbody>
	                <tr>
	                    <th>Minutes Saved Per Day Per Employee</th>
	                    <th>Average Employee Hourly Wage</th>
	                    <th>Sample Annual Savings Per Employee</th>
	                    <th>Your Annual Savings Per Employee</th>
	                </tr>
	                <tr>
	                    <td>48</td>
	                    <td>$10</td>
	                    <td>$2129.76</td>
	                    <td class="employee-saving" id="wage-td">-</td>
	                </tr>
	            </tbody>
	        </table>
	        <p>
	            <small>Sample <b><i>Savings Per Employee</i></b> based on Average Employee Wage of $10/HR and a 261-day work year.</small>
	        </p>
	    </div>
	    <div class="savings-table">
	        <h3>Labor Maintenance Savings</h3>
	        <table>
	            <tbody>
	                <tr>
	                    <th>Daylight Saving Time Change</th>
	                    <th>Minutes Saved Per Clock</th>
	                    <th>Average Maintenance Personnel Hourly Wage</th>
	                    <th>Sample Annual Savings</th>
	                    <th>Your Annual Savings</th>
	                </tr>
	                <tr>
	                    <td>2</td>
	                    <td>15</td>
	                    <td>$20</td>
	                    <td>$1020.00</td>
	                    <td class="employee-saving" id="daylight-td">-</td>
	                </tr>
	            </tbody>
	        </table>
	        <p>
	            <small>Sample <b><i>Savings Per Employee</i></b> based on Average Maintenance Personnel Wage of $20/HR and 100 Clocks.</small>
	        </p>
	    </div>
	</form>
<?php }
add_shortcode('synchronized_clock_systems_savings_per_employee', 'synchronized_clock_systems_savings_per_employee_func');

/******************************** Shortcode for woocommerce registration form *******************************/
function wc_separate_registration_form(){
   if(is_admin()) wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
   if(is_user_logged_in()) wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
   ob_start();  
 
   do_action('woocommerce_before_customer_login_form');
   ?>
   	<div class="woocommerce-account-container">
        <div class="page-title">
			<h1>Create an Account</h1>
		</div>
        <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

        	<?php do_action( 'woocommerce_register_form_start' ); ?>

			<div class="fieldset">
				<h2 class="legend">Personal Information</h2>

	        	<!-- <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"> -->
	        	<div class="col-two">
		        	<div class="field">
		        		<label for="firstname"><?php esc_html_e( 'First Name', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>

		        		<div class="input-box">
		        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="firstname" id="firstname" value="<?php echo ( ! empty( $_POST['firstname'] ) ) ? esc_attr( wp_unslash( $_POST['firstname'] ) ) : ''; ?>" title="First Name" maxlength="255" />
		        		</div>
		        	</div>

		        	<div class="field">
		        		<label for="middlename"><?php esc_html_e( 'Middle Name/Initial (optional)', 'woocommerce' ); ?></label>
		        		
		        		<div class="input-box">
		        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="middlename" id="middlename" value="<?php echo ( ! empty( $_POST['middlename'] ) ) ? esc_attr( wp_unslash( $_POST['middlename'] ) ) : ''; ?>" title="Middle Name/Initial" />
		        		</div>
		        	</div>
	        	</div>

	        	<div class="field">
	        		<label for="lastname"><?php esc_html_e( 'Last Name', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>
	        		
	        		<div class="input-box">
	        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="lastname" id="lastname" value="<?php echo ( ! empty( $_POST['lastname'] ) ) ? esc_attr( wp_unslash( $_POST['lastname'] ) ) : ''; ?>" title="Last Name" maxlength="255" />
	        		</div>
	        	</div>

	        	<div class="field">
	        		<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>

	        		<div class="input-box">
	        			<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
	        		</div>
	        	</div>
	        </div>	

	        <div class="fieldset">
	        	<h2 class="legend">Address Information</h2>
	        	
	        	<div class="col-two">
		        	<div class="field">
		        		<label for="company"><?php esc_html_e( 'Company name (optional)', 'woocommerce' ); ?></label>

		        		<div class="input-box">
		        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="company" id="company" value="<?php echo ( ! empty( $_POST['company'] ) ) ? esc_attr( wp_unslash( $_POST['company'] ) ) : ''; ?>" title="Company" />
		        		</div>
		        	</div>

		        	<div class="field">
		        		<label for="telephone"><?php esc_html_e( 'Telephone', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>

		        		<div class="input-box">
		        			<input type="number" class="woocommerce-Input woocommerce-Input--text input-text" name="telephone" id="telephone" value="<?php echo ( ! empty( $_POST['telephone'] ) ) ? esc_attr( wp_unslash( $_POST['telephone'] ) ) : ''; ?>" title="Telephone" />
		        		</div>
		        	</div>
		        </div>	

	        	<div class="field">
	        		<label for="street"><?php esc_html_e( 'Street Address', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>
	        		
	        		<div class="input-box">
	        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="street" id="street" value="<?php echo ( ! empty( $_POST['street'] ) ) ? esc_attr( wp_unslash( $_POST['street'] ) ) : ''; ?>" title="Street Address" placeholder="House number and street name" />
	        		</div>	

	        		<div class="input-box">
	        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="street_2" id="street_2" value="<?php echo ( ! empty( $_POST['street_2'] ) ) ? esc_attr( wp_unslash( $_POST['street_2'] ) ) : ''; ?>" title="Street Address" placeholder="Apartment, suite, unit, etc. (optional)" />
	        		</div>
	        	</div>

	        	<div class="col-two">
		        	<div class="field">
		                <label for="country">&nbsp;<span class="required"></span>Country</label>
		                <div class="input-box">
		                    <select name="country_id" id="country_id" class="validate-select validation-passed" title="Country">
		                    	<option value="">Select Country</option>
		                    	<option value="CA">Canada</option>
		                    	<option value="PR">Puerto Rico</option>
		                    	<option value="US">United States</option>
		                    </select>                            
		                </div>
		            </div>

		            <div class="field">
		                <label for="region_id">&nbsp;<span class="required"></span>State/Province</label>
		                <div class="input-box">
		                    <select id="region_id" name="region_id" title="State/Province" class="customer_js_states">
		                    </select>

		                    <input type="text" id="region" name="region" value="" title="State/Province" style="display:none;" class="js_other-states">
		                </div>
		            </div>
		        </div>    

		        <div class="col-two">
		            <div class="field">
		        		<label for="city"><?php esc_html_e( 'City', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>
		        		
		        		<div class="input-box">
		        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="city" id="city" value="<?php echo ( ! empty( $_POST['city'] ) ) ? esc_attr( wp_unslash( $_POST['city'] ) ) : ''; ?>" title="City" />
		        		</div>
		        	</div>

		        	<div class="field">
		        		<label for="postcode"><?php esc_html_e( 'Zip/Postal Code', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>

		        		<div class="input-box">
		        			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="postcode" id="postcode" value="<?php echo ( ! empty( $_POST['postcode'] ) ) ? esc_attr( wp_unslash( $_POST['postcode'] ) ) : ''; ?>" title="Zip/Postal Code" />
		        		</div>
		        	</div>
		        </div>	
	        </div>
	        

	        <div class="fieldset">
            	<h2 class="legend">Login Information</h2>
        		<?php if('no' === get_option('woocommerce_registration_generate_password')): ?>

        		<div class="col-two">	
	        		<div class="field">
	        			<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>

	        			<div class="input-box">
	        				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" title="Password" />
	        			</div>
	        		</div>

	        		<div class="field">
	        			<label for="confirmation"><?php esc_html_e( 'Confirm Password', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>
	        			
	        			<div class="input-box">
	        				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="confirmation" id="confirmation" title="Confirm Password" />
	        			</div>
	        		</div>
	        	</div>	

        		<?php else: ?>
        			<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
        		<?php endif; ?>

        	<?php do_action( 'woocommerce_register_form' ); ?>
            </div>

            <div class="buttons-set">
	        	<p class="required">* Required Fields</p>
	        	<p class="back-link">
	        		<a href="<?php echo get_permalink(32584); ?>" class="back-link">
	        			<small>«</small>Back
	        		</a>
	        	</p>

	        	<!-- <p class="woocommerce-form-row form-row"> -->
	        		<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
	        		<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Submit', 'woocommerce' ); ?>"><?php esc_html_e( 'Submit', 'woocommerce' ); ?></button>
	        	<!-- </p> -->

	        </div>	
	        <?php do_action( 'woocommerce_register_form_end' ); ?>

        </form>
    </div>
 
   <?php  
   return ob_get_clean();
}
add_shortcode('wc_reg_form', 'wc_separate_registration_form');

/******************************** Shortcode for woocommerce login form *******************************/  
function wc_separate_login_form(){
	if(is_admin()) wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
	if(is_user_logged_in()) wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
	ob_start();  

	do_action('woocommerce_before_customer_login_form');
    ?>
    <div class="woocommerce-account-container">
	    <div class="page-title">
	        <h1>Login or Create an Account</h1>
	    </div>

	    <div class="col2-set">
		    <div class="new-users">
		        <div class="content">
		            <h2>New Here?</h2>
		            <p class="form-instructions">Registration is free and easy!</p>
		            <ul class="benefits">
		                <li>Faster checkout</li>
		                <li>Save multiple shipping addresses</li>
		                <li>View and track orders and more</li>
		            </ul>
		        </div>
		        <div class="buttons-set">
		            <a title="Create an Account" class="button" href="<?php echo get_permalink(32565); ?>"><span><span>Create an Account</span></span></a>
		        </div>
		    </div>

		    <div class="registered-users">
				<h2>Already registered?</h2>
		        <p class="form-instructions">If you have an account with us, please log in.</p>
		        <p class="required">* Required Fields</p>

				<form class="woocommerce-form woocommerce-form-login login" method="post">

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="email"><?php esc_html_e( 'Email Address', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" autocapitalize="off" autocorrect="off" spellcheck="false" name="username" id="username" autocomplete="username" title="Email Address" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required/>
					</p>
					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>
						<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" title="Password" required />
					</p>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<p class="form-row">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
						</label>
						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
						<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
					</p>
					<p class="woocommerce-LostPassword lost_password">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot Your Password?', 'woocommerce' ); ?></a>
					</p>

					<?php do_action( 'woocommerce_login_form_end' ); ?>

				</form>

			</div>
		</div>
	</div>	

  <?php  
  return ob_get_clean();
}
add_shortcode('wc_login_form', 'wc_separate_login_form');
