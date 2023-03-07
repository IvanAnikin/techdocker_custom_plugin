<?php

/*
Plugin Name: TechDocker Custom Plugin
Plugin URI: https://github.com/IvanAnikin/techdocker_custom_plugin
Description: This plugin adds custom functionality to TechDocker.com site on WordPress.
Version: 1.0
Author: Ivan Anikin
Author URI: https://ivan-anikin.com/
License: GPL2
*/

// Limit post stats to the current user's posts 
function custom_dashboard_stats_post_count( $views ) { 
    if ( current_user_can( 'wcfm_vendor' ) && get_current_screen()->post_type == 'post' ) { 
        global $wpdb; 
        $current_user = wp_get_current_user(); 
        $post_count = $wpdb->get_var( $wpdb->prepare( 
            "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_author = %d AND post_type = 'post' AND (post_status = 'publish' OR post_status = 'draft' OR post_status = 'private')" 
            , $current_user->ID 
        ) ); 
        $views['mine'] = preg_replace( '/(?<=\(.+?\))/', number_format_i18n( $post_count ), $views['mine'] ); 
    } 
    return $views; 
} 
add_filter( 'views_edit-post', 'custom_dashboard_stats_post_count' ); 
 
// Limit product stats to the current user's products 
function custom_dashboard_stats_product_count( $views ) { 
    if ( current_user_can( 'wcfm_vendor' ) && get_current_screen()->post_type == 'product' ) { 
        global $wpdb; 
        $current_user = wp_get_current_user(); 
        $product_count = $wpdb->get_var( $wpdb->prepare( 
            "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_author = %d AND post_type = 'product' AND (post_status = 'publish' OR post_status = 'draft' OR post_status = 'pending' OR post_status = 'private' OR post_status = 'archive')" 
            , $current_user->ID 
        ) ); 
        $views['mine'] = preg_replace( '/(?<=\(.+?\))/', number_format_i18n( $product_count ), $views['mine'] ); 
    } 
    return $views; 
} 
add_filter( 'views_edit-product', 'custom_dashboard_stats_product_count' );


/**
 * Remove the 'all', 'publish', 'future', 'sticky', 'draft', 'pending', 'trash' 
 * views for non-admins
 */
add_filter( 'views_edit-post', function( $views )
{
    if( current_user_can( 'manage_options' ) )
        return $views;

    $remove_views = [ 'all','publish','future','sticky','draft','pending','trash', 'private' ];

    foreach( (array) $remove_views as $view )
    {
        if( isset( $views[$view] ) )
            unset( $views[$view] );
    }
    return $views;
} );

/**
 * Remove the 'all', 'publish', 'future', 'sticky', 'draft', 'pending', 'trash' 
 * views for non-admins
 */
add_filter( 'views_edit-product', function( $views )
{
    if( current_user_can( 'manage_options' ) )
        return $views;

    $remove_views = [ 'all','publish','future','sticky','draft','pending','trash', 'archived' ];

    foreach( (array) $remove_views as $view )
    {
        if( isset( $views[$view] ) )
            unset( $views[$view] );
    }
    return $views;
} );

