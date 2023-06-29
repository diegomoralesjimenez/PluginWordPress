<?php 
function add_entry_custom_fields() {
    add_meta_box('entry_fields', 'Entry Details', 'render_entry_fields', 'entry', 'normal', 'default');
}
add_action('add_meta_boxes', 'add_entry_custom_fields');

function render_entry_fields($post) {
    // Retrieve the current values of the custom fields
    $first_name = get_post_meta($post->ID, 'first_name', true);
    $last_name = get_post_meta($post->ID, 'last_name', true);
    $email = get_post_meta($post->ID, 'email', true);
    $phone = get_post_meta($post->ID, 'phone', true);
    $description = get_post_meta($post->ID, 'description', true);
    $competition_id = get_post_meta($post->ID, 'competition_id', true);

    // Output HTML for custom fields
    ?>
    <label for="first_name_field">First Name:</label>
    <input type="text" id="first_name_field" name="first_name_field" value="<?php echo esc_attr($first_name); ?>"><br>

    <label for="last_name_field">Last Name:</label>
    <input type="text" id="last_name_field" name="last_name_field" value="<?php echo esc_attr($last_name); ?>"><br>

    <label for="email_field">Email:</label>
    <input type="email" id="email_field" name="email_field" value="<?php echo esc_attr($email); ?>"><br>

    <label for="phone_field">Phone:</label>
    <input type="text" id="phone_field" name="phone_field" value="<?php echo esc_attr($phone); ?>"><br>

    <label for="description_field">Description:</label>
    <textarea id="description_field" name="description_field"><?php echo esc_textarea($description); ?></textarea><br>

    <label for="competition_id_field">Competition ID:</label>
    <input type="text" id="competition_id_field" name="competition_id_field" value="<?php echo esc_attr($competition_id); ?>"><br>
    <?php
}

function save_entry_custom_fields($post_id) {
    // Save custom field values
    if (isset($_POST['first_name_field'])) {
        update_post_meta($post_id, 'first_name', sanitize_text_field($_POST['first_name_field']));
    }
    if (isset($_POST['last_name_field'])) {
        update_post_meta($post_id, 'last_name', sanitize_text_field($_POST['last_name_field']));
    }
    if (isset($_POST['email_field'])) {
        update_post_meta($post_id, 'email', sanitize_email($_POST['email_field']));
    }
    if (isset($_POST['phone_field'])) {
        update_post_meta($post_id, 'phone', sanitize_text_field($_POST['phone_field']));
    }
    if (isset($_POST['description_field'])) {
        update_post_meta($post_id, 'description', sanitize_textarea_field($_POST['description_field']));
    }
    if (isset($_POST['competition_id_field'])) {
        update_post_meta($post_id, 'competition_id', sanitize_text_field($_POST['competition_id_field']));
    }
}
add_action('save_post', 'save_entry_custom_fields');
