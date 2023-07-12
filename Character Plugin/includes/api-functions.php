<?php
// api-functions.php

// Function to retrieve data from the Thrones API
function retrieve_character_data($character_id) {
    $cache_file = 'character_data_' . $character_id . '.json';

    // Check if the cached data file exists and is not expired
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 24 * 60 * 60) {
        $data = file_get_contents($cache_file);
        return json_decode($data, true);
    }

    $api_url = 'https://thronesapi.com/api/v2/Characters/' . $character_id;
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        wp_die('Failed to retrieve character data from the API.');
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data)) {
        wp_die('No data available for this character.');
    }

    // Store data in cache file
    file_put_contents($cache_file, $body);

    return $data;
}

// Function to update post with character data
function update_post_with_character_data($post_id) {
    // Check if the saved post is of type "character"
    if (get_post_type($post_id) !== 'character') {
        return;
    }

    // Retrieve character ID from custom field
    $character_id = get_post_meta($post_id, 'character_id', true);
    
    // Check if character ID is empty
    if (empty($character_id)) {
        return;
    }

    // Retrieve character data
    $character_data = retrieve_character_data($character_id);

    // Update post title
    $post_title = $character_data['name']; // Assuming 'name' is the key for the character's name in the retrieved data
    if (!empty($post_title)) {
        $post_data = array(
            'ID'         => $post_id,
            'post_title' => $post_title,
        );
        wp_update_post($post_data);
    }

    // Update featured image
    $featured_image_url = $character_data['image_url']; // Assuming 'image_url' is the key for the character's image URL in the retrieved data
    if (!empty($featured_image_url)) {
        $attach_id = save_featured_image_from_url($featured_image_url, $post_id);
        if ($attach_id) {
            set_post_thumbnail($post_id, $attach_id);
        }
    }
}

// Hook into post save to update the post title and set the featured image
add_action('save_post_character', 'update_post_with_character_data', 10, 1);

// Function to save the featured image from a URL
function save_featured_image_from_url($image_url, $post_id) {
    $upload_dir = wp_upload_dir();
    $image_data = wp_remote_get($image_url);

    if (is_wp_error($image_data)) {
        return false;
    }

    $image_name = basename($image_url);
    $image_path = $upload_dir['path'] . '/' . $image_name;
    $image_file = fopen($image_path, 'w');
    fwrite($image_file, $image_data['body']);
    fclose($image_file);

    $attachment = array(
        'guid'           => $upload_dir['url'] . '/' . $image_name,
        'post_mime_type' => $image_data['headers']['content-type'],
        'post_title'     => preg_replace('/\.[^.]+$/', '', $image_name),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $image_path, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
}