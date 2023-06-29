<?php
 /**
 * Plugin Name: Character API Plugin
 * Description: Plugin to update post meta and retrieve data from Thrones API for Character posts.
 * Version: 1.5.0
 * Author: Diego Morales Jimenez
 * Developer: Diego Morales Jimenez
 *
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include plugin files
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-functions.php';

