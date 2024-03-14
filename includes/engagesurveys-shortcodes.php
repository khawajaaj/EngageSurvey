<?php
/**
 * Shortcode handlers for EngageSurveys plugin.
 */

// Shortcode handler for displaying a survey
function engagesurveys_survey_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'id' => '', // Set default value to empty string
    ), $atts);

    // Get survey ID from shortcode attributes
    $survey_id = $atts['id'];

    // Check if survey ID is provided and not empty
    if (empty($survey_id)) {
        return '<p>Error: Survey ID is required.</p>';
    }

    // Get survey HTML based on the survey ID
    $survey_html = engagesurveys_get_survey_html($survey_id);

    return $survey_html;
}
add_shortcode('engagesurvey', 'engagesurveys_survey_shortcode');

// Function to generate HTML for displaying a survey
function engagesurveys_get_survey_html($survey_id) {
    // Retrieve survey data from the database
    $survey_data = get_option('engagesurveys_survey_' . $survey_id);

    // Check if survey data exists
    if (empty($survey_data)) {
        return '<p>Error: Survey not found.</p>';
    }

    // Generate HTML for displaying the survey
    $survey_html = '<form method="post">';
    foreach ($survey_data as $question => $options) {
        $survey_html .= '<p>' . esc_html($question) . '</p>';
        foreach ($options as $option) {
            $survey_html .= '<input type="radio" name="' . esc_attr($question) . '" value="' . esc_attr($option) . '"> ' . esc_html($option) . '<br>';
        }
    }
    $survey_html .= '<input type="submit" value="Submit">';
    $survey_html .= '</form>';

    return $survey_html;
}
