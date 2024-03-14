<?php
/**
 * Functions for EngageSurveys plugin.
 */

// Function to create a new survey and store it in the database
function engagesurveys_create_survey($survey_data) {
    // Generate a unique survey ID
    $survey_id = 'engagesurveys_survey_' . uniqid();

    // Save survey data to the database
    update_option($survey_id, $survey_data);

    // Return the survey ID
    return $survey_id;
}


// Function to display a survey
function engagesurveys_display_survey($survey_id) {
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

// Function to save survey responses
function engagesurveys_save_response($survey_id, $response_data) {
    // Check if survey ID and response data are provided
    if (empty($survey_id) || empty($response_data)) {
        return false;
    }

    // Save response data to the database
    update_option('engagesurveys_responses_' . $survey_id, $response_data);

    return true;
}

// Function to get survey results
function engagesurveys_get_results($survey_id) {
    // Retrieve response data from the database
    $response_data = get_option('engagesurveys_responses_' . $survey_id);

    // Check if response data exists
    if (empty($response_data)) {
        return false;
    }

    return $response_data;
}
