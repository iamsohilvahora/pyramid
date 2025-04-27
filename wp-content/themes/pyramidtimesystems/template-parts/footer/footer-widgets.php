<?php
/**
 * Displays the footer widget area.
 *
 */
if(is_active_sidebar('sidebar-1')): ?>
    <aside class="widget-area">
        <?php dynamic_sidebar('footer-1'); ?>
    </aside><!-- .widget-area -->

    <aside class="widget-area">
        <?php dynamic_sidebar('footer-2'); ?>
    </aside>

    <aside class="widget-area">
        <?php dynamic_sidebar('footer-3'); ?>
    </aside>
<?php endif; ?>
