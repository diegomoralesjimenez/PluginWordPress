<?php
  /**
 * Plugin Name: Competitions & Entries
 * Description: In this plugin you will be able to create a list of competitions and add entries for each competition.
 * Version: 1.0.0
 * Author: Diego Morales Jimenez
 * Developer: Diego Morales Jimenez
 *
 */

 if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-rewrite.php';
?>