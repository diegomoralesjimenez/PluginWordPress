<?php
// meta-boxes.php

// Add custom field ID to the Character post type
function character_add_custom_fields() {
    add_meta_box('character_id', 'Character ID', 'character_id_callback', 'character', 'side', 'default');
}
add_action('add_meta_boxes', 'character_add_custom_fields');

// Custom field ID callback function
function character_id_callback($post) {
    $character_id = get_post_meta($post->ID, 'character_id', true);
    echo '<input type="text" name="character_id" id="character_id" value="' . esc_attr($character_id) . '" style="width: 100%;" />';
}