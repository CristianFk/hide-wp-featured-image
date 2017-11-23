<?php
/*

  Plugin Name: Hide Featured Image on Posts

  Plugin URI: https://www.mydomain.com/

  Description: Hide featured images on single posts.

  Version: 1.0.0

  Author: Firstname Lastname

  Author URI: https://www.mydomain.com/

  License: GPLv2 or later

 */

/**
 * If this file is called directly, abort.
 */
if ( !defined( 'WPINC' ) ) {
    die;
}

/**
 * Register meta box
 */
function hfiop_register_meta_boxes() {
    add_meta_box( 
        'hide_featured_image', 
        __( 'Hide Featured Image?', 'textdomain' ), 
        'hfiop_callback_function', 
        'post', 
        'side' 
    );
}
add_action( 'add_meta_boxes', 'hfiop_register_meta_boxes' );
 
/**
 * Meta box callback function. Creates the checkbox.
 */
function hfiop_callback_function( $post ) { 
    wp_nonce_field(basename(__FILE__), "hfiop-nonce");

    $hide_featured = get_post_meta( $post->ID, '_hide_featured', true ); ?>

    <input type="checkbox" name="_hide_featured" value="1" <?php checked( $hide_featured, 1 ); ?>><?php _e( 'Yes', 'HideImage' );
}

/**
 * Save data
 */
function save_custom_meta_box($post_id, $post, $update) {
    if ( !isset($_POST["hfiop-nonce"] ) || !wp_verify_nonce($_POST["hfiop-nonce"], basename(__FILE__)))
        return $post_id;

    if( defined("DOING_AUTOSAVE") && DOING_AUTOSAVE )
        return $post_id;

    if ( 'revision' === $post->post_type ) {
        return $post_id;
    }

    if( !current_user_can("edit_post", $post_id) ) {
        return $post_id;
    } else {
        $hide_featured = ( isset( $_POST['_hide_featured'] ) && $_POST['_hide_featured'] == 1 ) ? '1' : $_POST['_hide_featured'];
        update_post_meta( $post_id, '_hide_featured', $hide_featured );        
    }
}
add_action("save_post", "save_custom_meta_box", 10, 3);

/**
 *  Hide featured image from single post page
 */
function hide_featured_image() {
    
    if( is_single() ){

      $hide = false;
      $hide_image =  get_post_meta( get_the_ID(), '_hide_featured', true );
      $hide = ( isset( $hide_image ) && $hide_image && $hide_image == 1 )? true : $hide;
      
      if( $hide ){ ?>
          <style>
              .tm-article-image img{ display: none !important; }
          </style><?php
      }
    }
}
add_action( 'wp_head', 'hide_featured_image'); /* Add inline styling inside the head tag */

?>