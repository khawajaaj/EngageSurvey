<?php
/**
 * Admin functions for EngageSurveys plugin.
 */

// Add menu item for plugin settings page
function engagesurveys_add_menu() {
    add_menu_page(
        'EngageSurveys Settings',
        'EngageSurveys',
        'manage_options',
        'engagesurveys-settings',
        'engagesurveys_settings_page'
    );
}
add_action('admin_menu', 'engagesurveys_add_menu');

// Callback function for plugin settings page
function engagesurveys_settings_page() {
    // Handle form submissions
    if (isset($_POST['submit_survey'])) {
        // Process submitted survey data
        $survey_data = array();

        // Example: Loop through submitted questions and options
        foreach ($_POST['survey_question'] as $index => $question) {
            $options = explode(',', $_POST['survey_options'][$index]);
            $survey_data[$question] = $options;
        }

        // Create a new survey
        $survey_id = engagesurveys_create_survey($survey_data);

        if ($survey_id) {
            echo '<div class="updated"><p>Survey created successfully. ID: ' . $survey_id . '</p></div>';
        } else {
            echo '<div class="error"><p>Error creating survey.</p></div>';
        }
    }

    // Display existing surveys
    ?>
    <div class="wrap">
        <h2>EngageSurveys Settings</h2>
        <h3>Existing Surveys</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Questions</th>
                    <th>Action</th> <!-- New column for action links -->
                </tr>
            </thead>
            <tbody>
                <?php
                $survey_ids = engagesurveys_get_all_survey_ids();
                foreach ($survey_ids as $survey_id) {
                    $survey_data = get_option($survey_id);
                    if (!empty($survey_data)) {
                        echo '<tr>';
                        echo '<td>' . esc_html($survey_id) . '</td>';
                        echo '<td>';
                        foreach ($survey_data as $question => $options) {
                            echo '<p><strong>' . esc_html($question) . ':</strong> ' . implode(', ', $options) . '</p>';
                        }
                        echo '</td>';
                        echo '<td><a href="' . esc_url(admin_url('admin.php?page=engagesurveys-analytics&survey_id=' . $survey_id)) . '">View Analytics</a></td>'; // Link to analytics page
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>

        <h3>Create New Survey</h3>
        <form method="post">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Survey Questions</th>
                    <td>
                        <div id="survey-questions">
                            <div class="survey-question">
                                <input type="text" name="survey_question[]" placeholder="Question">
                                <input type="text" name="survey_options[]" placeholder="Options (comma-separated)">
                            </div>
                        </div>
                        <button type="button" class="button" id="add-question">Add Question</button>
                    </td>
                </tr>
            </table>
            <input type="submit" name="submit_survey" class="button-primary" value="Create Survey">
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#add-question').click(function() {
            $('#survey-questions').append('<div class="survey-question"><input type="text" name="survey_question[]" placeholder="Question"><input type="text" name="survey_options[]" placeholder="Options (comma-separated)"></div>');
        });
    });
    </script>
    <?php
}

// Callback function for analytics page
function engagesurveys_analytics_page() {
    // Check if survey ID is provided in the URL
    if (!isset($_GET['survey_id']) || empty($_GET['survey_id'])) {
        echo '<div class="error"><p>Survey ID not provided.</p></div>';
        return;
    }

    $survey_id = $_GET['survey_id'];

    // Retrieve survey data from the database
    $survey_data = get_option($survey_id);

    // Retrieve submission data
    global $wpdb;
    $table_name = $wpdb->prefix . 'survey_submissions';
    $submissions = $wpdb->get_results("SELECT * FROM $table_name WHERE survey_id = '$survey_id'", ARRAY_A);

    // Display analytics
    ?>
    <div class="wrap">
        <h2>Survey Analytics - <?php echo esc_html($survey_id); ?></h2>
        <p>Analytics for survey ID: <?php echo esc_html($survey_id); ?></p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <?php foreach ($survey_data as $question => $options) : ?>
                        <th><?php echo esc_html($question); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission) : ?>
                    <tr>
                        <td><?php echo esc_html($submission['user_id']); ?></td>
                        <?php foreach ($survey_data as $question => $options) : ?>
                            <td><?php echo esc_html($submission[$question]); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Add submenu item for analytics page
function engagesurveys_add_submenu() {
    add_submenu_page(
        'engagesurveys-settings',
        'EngageSurveys Analytics',
        'Analytics',
        'manage_options',
        'engagesurveys-analytics',
        'engagesurveys_analytics_page'
    );
}
add_action('admin_menu', 'engagesurveys_add_submenu');

// Function to get all survey IDs
function engagesurveys_get_all_survey_ids() {
    global $wpdb;
    $survey_ids = $wpdb->get_col("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'engagesurveys_survey_%'");
    return $survey_ids;
}
