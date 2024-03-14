<?php
/**
 * Plugin Name: EngageSurveys
 * Description: Add interactive polls and surveys to your WordPress site.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: engagesurveys
 */

// Include plugin files
include_once(plugin_dir_path(__FILE__) . 'includes/engagesurveys-functions.php');
include_once(plugin_dir_path(__FILE__) . 'includes/engagesurveys-shortcodes.php');

if (is_admin()) {
    include_once(plugin_dir_path(__FILE__) . 'includes/engagesurveys-admin.php');
}

// Activation hook: Create necessary tables
register_activation_hook(__FILE__, 'engagesurveys_create_tables');

// Callback function to create necessary tables
function engagesurveys_create_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'survey_submissions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        survey_id VARCHAR(255) NOT NULL,
        user_id INT NOT NULL,
        submission_data TEXT,
        submission_date DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Plugin initialization
function engagesurveys_init() {
    // Add initialization code here
    // For example, you can register custom post types, taxonomies, or enqueue scripts/styles
    
}
add_action('init', 'engagesurveys_init');
