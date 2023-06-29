<?php
// custom-post-type.php

// Register custom post type "Character"
function register_character_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Characters',
            'singular_name' => 'Character',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
    );
    register_post_type('character', $args);
}
add_action('init', 'register_character_post_type');
