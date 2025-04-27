<?php
// Create custom post type - news
function news_post_type(){
    register_post_type('news',
    // CPT Options
        array(
            'labels' => array(
                'name' => __('News'),
                'singular_name' => __('News')
            ),
            'public' => true,
            'has_archive' => true,
            'show_ui' => true,
            'query_var' => true,
            'menu_icon' => 'dashicons-clock',
            'show_in_rest' => true,
            'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes'),
            'show_admin_column' => true,
            'exclude_from_search' => true,
            'show_in_nav_menus'     => true,
            'show_in_admin_bar'     => true,
            'show_in_menu'          => true,
            'can_export' => true,
            'publicly_queryable'    => true,
            'hierarchical' => true,
            'capability_type' => 'post',
            'rewrite' => array('slug' => 'news')
        )
    );

    // Create News Type Taxonomy  
    $args = array(
            'label' => __('News Categories'),
            'public' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'has_archive' => true,
            'query_var' => true,
            'show_tagcloud' => true,
            'show_admin_column' => true,
            'can_export' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'rewrite' => array('slug' => 'cat'),
        );
    register_taxonomy('news_category', array('news'), $args);

    $args = array(
            'label' => __('News Tags'),
            'public' => true,
            'show_ui' => true,
            'has_archive' => false,
            'show_in_nav_menus' => true,
            'show_admin_column' => true,
            'show_tagcloud' => true,
            'can_export' => true,
            'publicly_queryable' => true,
            'hierarchical'  => false, 
            'rewrite' => array('slug' => 'tag'), 
            'query_var' => true,
        );
    register_taxonomy('news_tags', array('news'), $args);
}
add_action('init', 'news_post_type');