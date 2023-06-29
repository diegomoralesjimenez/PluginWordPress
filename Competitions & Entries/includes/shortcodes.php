<?php
function render_competitions_list_shortcode()
{
    // Query competitions post type and display the list with title, description & image
    $args = array(
        'post_type' => 'competition',
        'posts_per_page' => -1,
    );

    $competitions_query = new WP_Query($args);

    if ($competitions_query->have_posts()) {
        $output = '<ul class="competitions-list">';

        while ($competitions_query->have_posts()) {
            $competitions_query->the_post();

            $title = get_the_title();
            $description = get_the_excerpt();
            $image = get_the_post_thumbnail();
            $permalink = get_permalink();

            $output .= '<li>';
            $output .= '<h3 style="font-weight:bold"><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h3>';
            $output .= '<div class="competition-description">' . $description . '</div>';
            $output .= '<div class="competition-image">' . $image . '</div>';
            $output .= '</li>';
        }

        $output .= '</ul>';

        wp_reset_postdata();

        return $output;
    }

    return 'No competitions found.';
}
add_shortcode('competitions_list', 'render_competitions_list_shortcode');

function render_entry_form_shortcode()
{
    $competition_slug = get_query_var('competition_slug'); // Retrieve the competition slug from the query variables
    $competition = get_page_by_path($competition_slug, OBJECT, 'competition'); // Get the competition post object based on the slug

    if ($competition) {
        $competition_id = $competition->ID; // Retrieve the competition ID

        ob_start();
        ?>
        <form id="entry-form" class="entry-form" method="post">
            <div class="form-field">
                <label for="first-name">First Name</label>
                <input type="text" name="first-name" id="first-name" required>
            </div>
            <div class="form-field">
                <label for="last-name">Last Name</label>
                <input type="text" name="last-name" id="last-name" required>
            </div>
            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-field">
                <label for="phone">Phone</label>
                <input type="tel" name="phone" id="phone" required>
            </div>
            <div class="form-field">
                <label for="description">Description</label>
                <textarea name="description" id="description" required></textarea>
            </div>
            <div class="form-field">
                <?php
                echo '<input type="hidden" name="competition-id" value="' . esc_attr($competition_id) . '">';
                ?>
            </div>
            <div class="form-field">
                <input type="submit" name="submit-entry" value="Submit Entry">
            </div>
        </form>
        <script>
            jQuery(document).ready(function ($) {
                $('#entry-form').submit(function (event) {
                    event.preventDefault();
                    var form = $(this);
                    var formData = form.serialize();

                    // Submit form data via Fetch API
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: new URLSearchParams({
                            action: 'submit_entry',
                            form_data: formData,
                        }),
                    })
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            // Handle the AJAX response
                            if (data.success) {
                                // Entry submitted successfully
                                alert('Entry submitted successfully.');
                                form[0].reset();
                            } else {
                                // Error in form submission
                                alert('Error submitting the entry. Please try again.');
                            }
                        })
                        .catch(function (error) {
                            console.log('An error occurred:', error);
                        });
                });
            });

        </script>
        <?php
        return ob_get_clean();
    }

    return 'Competition not found.';
}
add_shortcode('submit_entry', 'submit_entry_button_shortcode');
function submit_entry_button_shortcode() {
    // post_name = run_competition
    $competition_slug = get_post_field('post_name', get_post());
    $submit_entry_url = home_url() . "/$competition_slug/submit-entry/";

    // Check if competition slug exists
    if (!empty($competition_slug)) {
        ob_start();
        ?>
        <a href="<?php echo esc_url($submit_entry_url); ?>" class="submit-entry-button">Submit Entry</a>
        <?php
        return ob_get_clean();
    }
}
add_shortcode('entry_form', 'render_entry_form_shortcode');


//Form Submission Ajax Handler
add_action('wp_ajax_submit_entry', 'submit_entry');

add_action('wp_ajax_nopriv_submit_entry', 'submit_entry');

function submit_entry()
{
    if (isset($_POST['form_data'])) {
        // Retrieve the form data
        $form_data = $_POST['form_data'];

        // Parse the form data
        parse_str($form_data, $form_fields);

        // Extract the individual form fields
        $first_name = sanitize_text_field($form_fields['first-name']);
        $last_name = sanitize_text_field($form_fields['last-name']);
        $email = sanitize_email($form_fields['email']);
        $phone = sanitize_text_field($form_fields['phone']);
        $description = sanitize_textarea_field($form_fields['description']);
        $competition_id = absint($form_fields['competition-id']);

        // Create the post array
        $entry_data = array(
            'post_title' => $first_name . ' ' . $last_name,
            'post_type' => 'entry',
            'post_status' => 'publish',
            'meta_input' => array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'description' => $description,
                'competition_id' => $competition_id,
            ),
        );

        // Insert the post
        $entry_id = wp_insert_post($entry_data);

        // Prepare the AJAX response
        $response = array();
        if ($entry_id) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }

        // Send the JSON response
        wp_send_json($response);
    } else {
        wp_send_json_error();
    }
}