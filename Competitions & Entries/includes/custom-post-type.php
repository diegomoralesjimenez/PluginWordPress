<?php
// custom-post-type.php

// Register custom post type "Competitions"
function competition_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Competitions',
            'singular_name' => 'Competition',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'competition'), // Add the rewrite argument here
    );
    
    register_post_type('competition', $args);
}

add_action('init', 'competition_post_type');


// Register custom post type "Entries"
function entry_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Entries',
            'singular_name' => 'Entry',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
    );
    
    register_post_type('entry', $args);
}

add_action('init', 'entry_post_type');
