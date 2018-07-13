<?php

/**
 * Plugin Name: Shortcodes to REST API
 * Description: Automatically turns shortcodes to HTML content in WP API
 * Author: Frédéric Langlade-Bellone
 * Author URI: https://github.com/fredericlb
 * Version: 1.0.0
 * Plugin URI: https://github.com/fredericlb/wp-api-shortcodes
 */

add_action( 'rest_api_init', function ()
{
    register_rest_field(
        'page',
        'content',
        array(
            'get_callback'    => 'duo_get_divi_content',
            'update_callback' => null,
            'schema'          => null,
        )
    );
});

function duo_get_divi_content( $object, $field_name, $request )
{
    //Set is_singular to true to ovoid "read more issue"
    //Issue come from is_singular () in divi-builder.php line 73
    global $wp_query;
    $wp_query->is_singular = true;


    //Set divi shortcode
    //The 2 function bellow are define in 'init' but they are call in 'wp'
    //REST Api exit after 'parse_request' hook, it's before 'wp' so divi's shortcode are not set
    et_builder_init_global_settings ();
    et_builder_add_main_elements ();


    //Define $post, if not defined, divi will not add outter_content and inner_content warper
    //Issue come from get_the_ID() in divi-builder.php line 69
    global $post;
    $post = get_post ($object['id']);

    //Apply the_content's filter, one of them interpret shortcodes
    $output =  apply_filters( 'the_content', $post->post_content );

    return $output;
}
