<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package pyramidtimesystems
 */

?>
	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<?php get_template_part('template-parts/footer/footer-widgets'); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
