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

    <input type="checkbox" name="_hide_featured" value="1" <?php checked( $hide_featured, 1 ); ?>><?php _e( 'Yes', 'textdomain' );
}

/**
 * Save data
 */
function hfiop_save_meta_box($post_id, $post, $update) {
    if ( !isset($_POST["hfiop-nonce"] ) || !wp_verify_nonce($_POST["hfiop-nonce"], basename(__FILE__)))
        return;

    if( defined("DOING_AUTOSAVE") && DOING_AUTOSAVE )
        return;

    if ( 'revision' === $post->post_type ) {
        return;
    }

    if( !current_user_can("edit_post", $post_id) ) {
        return;
    } else {
        $hide_featured = ( isset( $_POST['_hide_featured'] ) && $_POST['_hide_featured'] == 1 ) ? '1' : $_POST['_hide_featured'];
        update_post_meta( $post_id, '_hide_featured', $hide_featured ); /* Adds _hide_featured meta_key with meta_value of 1 */        
    }
}
add_action("save_post", "hfiop_save_meta_box", 10, 3);

/**
 *  Hide featured image from single post page
 */
function hfiop_hide_featured_image() {
    
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
add_action( 'wp_head', 'hfiop_hide_featured_image'); /* Adds inline styling inside the head tag */

/**
 *  Functions references
 *
 *  https://developer.wordpress.org/reference/functions/add_meta_box/
 *  
 *  https://codex.wordpress.org/Function_Reference/checked
 *
 *  https://developer.wordpress.org/reference/functions/get_post_meta/
 *
 *  https://codex.wordpress.org/Function_Reference/wp_nonce_field
 *
 *  https://developer.wordpress.org/reference/functions/_e/
 */

?>
