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