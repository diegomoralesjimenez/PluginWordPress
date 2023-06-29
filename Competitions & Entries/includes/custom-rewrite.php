<?php

function custom_rewrite_endpoints() {
    add_rewrite_endpoint( 'submit-entry', EP_PAGES );
}
add_action( 'init', 'custom_rewrite_endpoints' );

function custom_rewrite_rules() {
    add_rewrite_rule(
        '^([^/]+)/submit-entry/?$',
        'index.php?pagename=submit-entry&competition_slug=$matches[1]',
        'top'
    );
}
add_action('init', 'custom_rewrite_rules');

function add_query_vars_filter( $vars ){
  $vars[] = "competition_slug";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter');