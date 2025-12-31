<?php

return [
    // Navigation
    'help_documentation' => 'Help & Documentation',
    'search_placeholder' => 'Search documentation...',
    'overview' => 'Overview',
    'getting_started' => 'Getting Started',
    'features' => 'Features',
    'library' => 'Blueprint Library',
    'requests' => 'Request System',
    'statistics' => 'Statistics',
    'settings' => 'Settings',
    'permissions' => 'Permissions',
    'faq' => 'FAQ',
    'troubleshooting' => 'Troubleshooting',

    // Plugin Information
    'plugin_info_title' => 'Plugin Information',
    'version' => 'Version',
    'license' => 'License',
    'author' => 'Author',
    'github_repo' => 'GitHub Repository',
    'report_issues' => 'Report Issues',
    'changelog' => 'Full Changelog',
    'readme' => 'README',
    'support_project' => 'Support the Project',
    'support_list' => '<ul style="margin-top: 10px; margin-bottom: 0;">
        <li>⭐ Star the GitHub repository</li>
        <li>🐛 Report bugs and issues</li>
        <li>💡 Suggest new features</li>
        <li>🔧 Contributing code improvements</li>
        <li>🌟 Share with other SeAT users</li>
    </ul>',

    // Overview Section
    'welcome_title' => 'Welcome to Blueprint Manager',
    'welcome_desc' => 'Your comprehensive blueprint library and request management system for EVE Online corporations using SeAT.',
    
    'what_is_title' => 'What is Blueprint Manager?',
    'what_is_desc' => 'Blueprint Manager is a comprehensive blueprint library and request management system for EVE Online corporations using SeAT. It provides powerful tools for organizing, tracking, and managing blueprint copies within your corporation.',
    
    'key_benefit' => 'Key Benefits',
    'key_benefit_desc' => 'Centralized blueprint library, automated request workflow, Discord/Slack notifications, and comprehensive statistics tracking.',

    'core_features' => 'Core Features',
    
    // Quick Links
    'quick_links' => 'Quick Links',
    'view_library' => 'View Library',
    'view_requests' => 'View Requests',
    'view_statistics' => 'View Statistics',
    
    // Core Feature Cards
    'feature_library_title' => 'Blueprint Library',
    'feature_library_desc' => 'Organize blueprints by custom categories using container name patterns',
    
    'feature_requests_title' => 'Request System',
    'feature_requests_desc' => 'Members can request blueprints with full approval/rejection workflow',
    
    'feature_notifications_title' => 'Notifications',
    'feature_notifications_desc' => 'Discord/Slack webhooks for request updates and status changes',
    
    'feature_statistics_title' => 'Statistics',
    'feature_statistics_desc' => 'Track request activity, popular blueprints, and character usage',
    
    'feature_autosync_title' => 'Auto-Sync',
    'feature_autosync_desc' => 'Automatically updates from SeAT\'s ESI data sync',
    
    'feature_permissions_title' => 'Permissions',
    'feature_permissions_desc' => 'Flexible role-based access control system',

    // Quick Start Guide
    'quick_start_title' => 'Quick Start Guide',
    'quick_start_step1' => 'Install the plugin and run migrations',
    'quick_start_step2' => 'Configure container patterns in Settings to organize your blueprints',
    'quick_start_step3' => 'Set up Discord/Slack webhooks for notifications (optional)',
    'quick_start_step4' => 'Configure permissions for your corporation members',
    'quick_start_step5' => 'Start using the Blueprint Library and Request System!',
    
    'quick_start_note' => 'Note',
    'quick_start_note_desc' => 'Blueprint data automatically syncs from SeAT\'s regular ESI updates. New blueprints and research changes appear after SeAT\'s next sync cycle.',

    // Getting Started Section
    'installation' => 'Installation',
    'installation_desc' => 'Install Blueprint Manager using Composer:',
    'installation_command' => 'composer require mattfalahe/blueprint-manager
php artisan migrate',
    'installation_automatic' => 'The plugin will automatically:',
    'installation_auto_1' => 'Register navigation menu items',
    'installation_auto_2' => 'Create database tables',
    'installation_auto_3' => 'Set up default permissions',
    'installation_auto_4' => 'Begin tracking blueprints on next SeAT sync',

    'initial_config' => 'Initial Configuration',
    
    'config_step1' => '1. Configure Container Patterns',
    'config_step1_desc' => 'Go to <strong>Settings</strong> and define how your blueprints are organized:',
    'config_step1_pattern' => '<strong>Container Name Pattern:</strong> Use wildcards (e.g., <code>*Cap*</code> matches "Capital BPCs")',
    'config_step1_category' => '<strong>Display Category:</strong> How this group appears in the library (e.g., "Capital Ships")',
    'config_step1_filter' => '<strong>Corporation Filter:</strong> Optionally limit to specific corporations',
    'config_tip' => 'Tip',
    'config_tip_desc' => 'Use the "Detect Containers" button to scan your corporation\'s existing containers and quickly add them.',

    'config_step2' => '2. Set Up Notifications (Optional)',
    'config_step2_desc' => 'Configure Discord or Slack webhooks to receive notifications for:',
    'config_step2_event1' => 'New blueprint requests',
    'config_step2_event2' => 'Request approvals',
    'config_step2_event3' => 'Request rejections',
    'config_step2_event4' => 'Request fulfillments',

    'config_step3' => '3. Configure Permissions',
    'config_step3_desc' => 'Assign permissions to your corporation roles:',
    'config_step3_perm1' => '<strong>View Library:</strong> Browse available blueprints',
    'config_step3_perm2' => '<strong>Create Requests:</strong> Submit blueprint requests',
    'config_step3_perm3' => '<strong>Manage Requests:</strong> Approve/reject/fulfill requests',
    'config_step3_perm4' => '<strong>Settings:</strong> Configure container patterns and webhooks',

    'first_steps' => 'First Steps',
    'first_steps_step1' => '<strong>Organize Your Blueprints</strong>',
    'first_steps_step1_desc' => 'Name your in-game containers descriptively (e.g., "Frigates BPC", "Modules - T2", "Capital Ships")',
    'first_steps_step2' => '<strong>Wait for SeAT Sync</strong>',
    'first_steps_step2_desc' => 'SeAT automatically syncs asset and blueprint data. This typically happens every 15-60 minutes.',
    'first_steps_step3' => '<strong>Configure Container Patterns</strong>',
    'first_steps_step3_desc' => 'In Settings, add patterns matching your container names to create organized categories',
    'first_steps_step4' => '<strong>Test the System</strong>',
    'first_steps_step4_desc' => 'Create a test request and approve/reject it to verify notifications work',

    // Features Section
    'features_complete' => 'Complete Feature List',
    
    'blueprint_organization' => 'Blueprint Organization',
    'feat_org_1' => '<strong>Pattern-Based Categorization:</strong> Use wildcards to match container names',
    'feat_org_2' => '<strong>Custom Categories:</strong> Group blueprints by ship class, module type, or any system you prefer',
    'feat_org_3' => '<strong>Automatic Detection:</strong> Scan existing containers to quickly build your library',
    'feat_org_4' => '<strong>Multi-Corporation Support:</strong> Manage blueprints across multiple corporations',
    'feat_org_5' => '<strong>Real-Time Data:</strong> Always shows current blueprint data from SeAT',

    'request_management' => 'Request Management',
    'feat_req_1' => '<strong>Simple Request Creation:</strong> Select blueprint, specify quantity and runs',
    'feat_req_2' => '<strong>Request States:</strong> Pending, Approved, Rejected, Fulfilled',
    'feat_req_3' => '<strong>Status Tracking:</strong> View all requests with filtering by state',
    'feat_req_4' => '<strong>Request Notes:</strong> Add context when requesting or responding',
    'feat_req_5' => '<strong>Self-Service Deletion:</strong> Users can delete their own pending/rejected requests',

    'notifications_features' => 'Notifications',
    'feat_notif_1' => '<strong>Rich Embeds:</strong> Color-coded Discord/Slack messages',
    'feat_notif_2' => '<strong>Per-Event Configuration:</strong> Enable/disable notifications for each event type',
    'feat_notif_3' => '<strong>Corporation Filtering:</strong> Send notifications only for specific corporations',
    'feat_notif_4' => '<strong>Test Function:</strong> Verify webhook configuration before going live',
    'feat_notif_5' => '<strong>Clean Formatting:</strong> Minimal emojis, easy to scan',

    'statistics_analytics' => 'Statistics & Analytics',
    'feat_stats_1' => '<strong>Overall Metrics:</strong> Total requests, approval rates, average processing time',
    'feat_stats_2' => '<strong>Character Statistics:</strong> Track most active requesters',
    'feat_stats_3' => '<strong>Blueprint Popularity:</strong> See which blueprints are requested most',
    'feat_stats_4' => '<strong>Time Series Data:</strong> Visualize request trends over time (7, 30, 90 days)',
    'feat_stats_5' => '<strong>Corporation Breakdown:</strong> Compare activity across multiple corporations',

    'search_filtering' => 'Search & Filtering',
    'feat_search_1' => '<strong>Category Filtering:</strong> Browse blueprints by configured categories',
    'feat_search_2' => '<strong>Text Search:</strong> Find blueprints by name',
    'feat_search_3' => '<strong>Request Filtering:</strong> Filter by state, character, date range',
    'feat_search_4' => '<strong>Detail Views:</strong> Click blueprints to see all copies with ME/TE/runs',

    // Blueprint Library Section
    'library_guide' => 'Blueprint Library Guide',
    'library_overview' => 'Overview',
    'library_overview_desc' => 'The Blueprint Library displays all blueprints organized by your configured categories. It shows aggregated data for each blueprint type including quantity, ME/TE ranges, and runs.',

    'viewing_blueprints' => 'Viewing Blueprints',
    'viewing_bp_1' => '<strong>Category Filter:</strong> Select a category to show only those blueprints',
    'viewing_bp_2' => '<strong>Blueprint Cards:</strong> Each card shows:',
    'viewing_bp_2_1' => 'Blueprint name and icon',
    'viewing_bp_2_2' => 'Quantity available',
    'viewing_bp_2_3' => 'ME range (min/avg/max material efficiency)',
    'viewing_bp_2_4' => 'TE range (min/avg/max time efficiency)',
    'viewing_bp_2_5' => 'Runs available (for BPCs)',
    'viewing_bp_3' => '<strong>Detail View:</strong> Click "View Details" to see individual blueprint copies',

    'blueprint_details' => 'Blueprint Details',
    'blueprint_details_desc' => 'The detail page shows every individual blueprint copy with:',
    'bp_detail_1' => '<strong>Location:</strong> Which container holds this blueprint',
    'bp_detail_2' => '<strong>Material Efficiency:</strong> ME level (0-10)',
    'bp_detail_3' => '<strong>Time Efficiency:</strong> TE level (0-20)',
    'bp_detail_4' => '<strong>Runs:</strong> Number of runs remaining (BPCs only)',
    'bp_detail_5' => '<strong>Type:</strong> BPO (original) or BPC (copy)',
    
    'auto_update' => 'Auto-Update',
    'auto_update_desc' => 'Blueprint data automatically updates when SeAT syncs with ESI. New blueprints, research changes, and moved items appear after the next sync.',

    'organizing_library' => 'Organizing Your Library',
    'container_naming' => 'Container Naming Best Practices',
    'container_naming_desc' => 'For best results, use descriptive container names in-game:',
    
    'good_examples' => 'Good Examples',
    'good_example_1' => '• "Frigates - T1 BPC"',
    'good_example_2' => '• "Capital Ships - Carriers"',
    'good_example_3' => '• "Modules - T2 Guns"',
    'good_example_4' => '• "Ammo - All Types"',
    'good_example_5' => '• "Industrial - Mining"',

    'pattern_matching' => 'Pattern Matching',
    'pattern_matching_desc' => 'Use wildcards to match multiple containers:',
    'pattern_ex_1' => '<code>*Frigate*</code> matches "Frigates BPC", "Tech 2 Frigates", "Faction Frigates"',
    'pattern_ex_2' => '<code>*Capital*</code> matches "Capital BPOs", "Capital Ships", "Capital Modules"',
    'pattern_ex_3' => '<code>T2*</code> matches "T2 Ships", "T2 Modules", "T2 Ammo"',
    
    'pattern_priority' => 'Pattern Priority',
    'pattern_priority_desc' => 'If a container matches multiple patterns, only the first match is used. Order your patterns from most specific to most general.',

    // Request System Section
    'request_system_guide' => 'Request System Guide',
    'creating_requests' => 'Creating Requests',
    'creating_requests_desc' => 'To request a blueprint:',
    'create_req_1' => 'Navigate to the <strong>Requests</strong> page',
    'create_req_2' => 'Click <strong>"New Request"</strong>',
    'create_req_3' => 'Select your corporation',
    'create_req_4' => 'Choose the blueprint from the dropdown (shows only available blueprints)',
    'create_req_5' => 'Specify quantity and runs (optional)',
    'create_req_6' => 'Add notes explaining your need (optional but recommended)',
    'create_req_7' => 'Submit the request',
    
    'create_req_tip' => 'Tip',
    'create_req_tip_desc' => 'Include details like "For market stock" or "Personal production" in notes to help managers prioritize.',

    'request_states' => 'Request States',
    'req_state_1' => '<strong>Pending:</strong> Awaiting manager review (yellow badge)',
    'req_state_2' => '<strong>Approved:</strong> Accepted but not yet provided (green badge)',
    'req_state_3' => '<strong>Rejected:</strong> Declined by manager with reason (red badge)',
    'req_state_4' => '<strong>Fulfilled:</strong> Blueprint has been provided (purple badge)',

    'managing_requests' => 'Managing Your Requests',
    'manage_req_1' => '<strong>View All:</strong> See your request history with status filters',
    'manage_req_2' => '<strong>Delete:</strong> Remove pending or rejected requests you no longer need',
    'manage_req_3' => '<strong>Track Status:</strong> Receive Discord/Slack notifications when status changes',

    'manager_workflow' => 'Manager Workflow',
    'processing_requests' => 'Processing Requests',
    'processing_desc' => 'As a manager, you can:',
    
    'approve_requests' => '1. Approve Requests',
    'approve_desc_1' => 'Review the request details',
    'approve_desc_2' => 'Click "Approve" to accept',
    'approve_desc_3' => 'Add notes about when/where to collect (optional)',
    'approve_desc_4' => 'Request moves to "Approved" state',

    'reject_requests' => '2. Reject Requests',
    'reject_desc_1' => 'Click "Reject" on the request',
    'reject_desc_2' => 'Provide a clear reason (e.g., "Out of stock", "Not for personal use")',
    'reject_desc_3' => 'Requester is notified via Discord/Slack',

    'fulfill_requests' => '3. Fulfill Requests',
    'fulfill_desc_1' => 'After providing the blueprint in-game, click "Fulfill"',
    'fulfill_desc_2' => 'Add notes about delivery location (optional)',
    'fulfill_desc_3' => 'Request is marked complete and archived',
    
    'best_practice' => 'Best Practice',
    'best_practice_desc' => 'Mark requests as fulfilled immediately after contracting to keep accurate records.',

    'request_notifications' => 'Request Notifications',
    'notif_desc' => 'When webhooks are configured, notifications are sent for:',
    
    'notif_new' => 'New Request (Blue)',
    'notif_new_1' => 'Triggered when a member submits a request',
    'notif_new_2' => 'Shows blueprint, quantity, requester, and notes',

    'notif_approved' => 'Request Approved (Green)',
    'notif_approved_1' => 'Sent when a manager approves the request',
    'notif_approved_2' => 'Includes approver name and any notes',

    'notif_rejected' => 'Request Rejected (Red)',
    'notif_rejected_1' => 'Sent when a manager rejects the request',
    'notif_rejected_2' => 'Shows rejector name and rejection reason',

    'notif_fulfilled' => 'Request Fulfilled (Purple)',
    'notif_fulfilled_1' => 'Sent when the blueprint is provided',
    'notif_fulfilled_2' => 'Includes fulfiller name and delivery details',

    // Statistics Section
    'statistics_analytics_title' => 'Statistics & Analytics',
    'overall_statistics' => 'Overall Statistics',
    'overall_stats_desc' => 'The main statistics page shows:',
    'stat_1' => '<strong>Total Requests:</strong> All-time request count',
    'stat_2' => '<strong>Approval Rate:</strong> Percentage of requests approved',
    'stat_3' => '<strong>Fulfillment Rate:</strong> Percentage of approved requests fulfilled',
    'stat_4' => '<strong>Average Processing Time:</strong> Time from request to fulfillment',
    'stat_5' => '<strong>Pending Count:</strong> Currently awaiting review',

    'time_series_graphs' => 'Time Series Graphs',
    'time_series_desc' => 'Visualize request activity over time:',
    'time_7days' => '<strong>7 Days:</strong> Week-over-week trends',
    'time_30days' => '<strong>30 Days:</strong> Monthly activity patterns',
    'time_90days' => '<strong>90 Days:</strong> Quarterly analysis',

    'character_statistics' => 'Character Statistics',
    'char_stats_desc' => 'See which members are most active:',
    'char_stat_1' => 'Request count per character',
    'char_stat_2' => 'Approval/rejection breakdown',
    'char_stat_3' => 'Click character to view their request history',

    'blueprint_popularity' => 'Blueprint Popularity',
    'bp_popularity_desc' => 'Identify most-requested blueprints:',
    'bp_pop_1' => 'Total requests per blueprint type',
    'bp_pop_2' => 'Average quantity requested',
    'bp_pop_3' => 'Fulfillment success rate',

    'corporation_comparison' => 'Corporation Comparison',
    'corp_comp_desc' => 'Compare activity across multiple corporations:',
    'corp_comp_1' => 'Request volume by corporation',
    'corp_comp_2' => 'Approval rates comparison',
    'corp_comp_3' => 'Processing time differences',
    
    'use_cases' => 'Use Cases',
    'use_cases_desc' => 'Statistics help identify popular blueprints to keep in stock, track member activity, and measure manager responsiveness.',

    // Settings Section
    'settings_configuration' => 'Settings Configuration',
    'container_configurations' => 'Container Configurations',
    'container_config_desc' => 'Define how blueprints are organized in your library:',
    
    'adding_pattern' => 'Adding a Container Pattern',
    'add_pattern_1' => 'Click <strong>"Add Container Configuration"</strong>',
    'add_pattern_2' => 'Enter the <strong>Container Name Pattern</strong> (e.g., <code>*Capital*</code>)',
    'add_pattern_3' => 'Set the <strong>Display Category</strong> (e.g., "Capital Ships")',
    'add_pattern_4' => 'Optionally select a specific corporation',
    'add_pattern_5' => 'Enable/disable the configuration',
    'add_pattern_6' => 'Save',

    'container_detection' => 'Container Detection',
    'detection_desc' => 'Automatically scan for blueprint containers:',
    'detect_1' => 'Select a corporation',
    'detect_2' => 'Optionally filter by hangar divisions (e.g., CorpSAG6, CorpSAG7)',
    'detect_3' => 'Click <strong>"Detect Containers"</strong>',
    'detect_4' => 'Review detected containers',
    'detect_5' => 'Click containers to add them as configurations',
    
    'time_saver' => 'Time Saver',
    'time_saver_desc' => 'Detection scans all your corporation\'s containers and suggests configurations based on actual blueprint locations.',

    'webhook_configuration' => 'Webhook Configuration',
    'webhook_desc' => 'Set up Discord or Slack notifications:',
    
    'creating_webhook' => 'Creating a Webhook',
    'webhook_1' => 'In Discord/Slack, create a webhook URL',
    'webhook_2' => 'Click <strong>"Add Webhook"</strong> in Blueprint Manager settings',
    'webhook_3' => 'Give it a descriptive name (e.g., "Production Discord Channel")',
    'webhook_4' => 'Paste the webhook URL',
    'webhook_5' => 'Select which events to notify:',
    'webhook_5_1' => 'New requests created',
    'webhook_5_2' => 'Requests approved',
    'webhook_5_3' => 'Requests rejected',
    'webhook_5_4' => 'Requests fulfilled',
    'webhook_6' => 'Optionally filter by corporation',
    'webhook_7' => 'Save and click <strong>"Test"</strong> to verify',
    
    'security_warning' => 'Security',
    'security_desc' => 'Never share webhook URLs publicly. Anyone with the URL can post to your channel.',

    'detection_settings' => 'Detection Settings',
    'detection_settings_desc' => 'Configure which hangars are scanned during detection:',
    'detect_setting_1' => '<strong>Hangar Divisions:</strong> Select which CorpSAG divisions to scan',
    'detect_setting_2' => '<strong>Per Corporation:</strong> Different settings for each corporation',
    'detect_setting_3' => '<strong>Default:</strong> All divisions if not configured',

    // Permissions Section
    'permission_system' => 'Permission System',
    'available_permissions' => 'Available Permissions',
    
    'perm_view' => 'View Library',
    'perm_view_access' => '<strong>Access:</strong> Blueprint Library page',
    'perm_view_purpose' => '<strong>Purpose:</strong> Browse available blueprints',
    'perm_view_recommended' => '<strong>Recommended For:</strong> All corporation members',

    'perm_request' => 'Create Requests',
    'perm_request_access' => '<strong>Access:</strong> Request creation and viewing own requests',
    'perm_request_purpose' => '<strong>Purpose:</strong> Submit blueprint requests',
    'perm_request_recommended' => '<strong>Recommended For:</strong> Active members, builders, market traders',

    'perm_manage' => 'Manage Requests',
    'perm_manage_access' => '<strong>Access:</strong> Approve, reject, and fulfill all requests',
    'perm_manage_purpose' => '<strong>Purpose:</strong> Process blueprint requests and view statistics',
    'perm_manage_recommended' => '<strong>Recommended For:</strong> Directors, blueprint librarians, managers',

    'perm_settings' => 'Settings',
    'perm_settings_access' => '<strong>Access:</strong> Configure container patterns and webhooks',
    'perm_settings_purpose' => '<strong>Purpose:</strong> System configuration and setup',
    'perm_settings_recommended' => '<strong>Recommended For:</strong> Directors, administrators',

    'permission_scenarios' => 'Permission Scenarios',
    'scenario_basic' => 'Basic Member',
    'scenario_basic_1' => 'View Library',
    'scenario_basic_2' => 'Create Requests',
    
    'scenario_manager' => 'Blueprint Manager',
    'scenario_manager_1' => 'View Library',
    'scenario_manager_2' => 'Create Requests',
    'scenario_manager_3' => 'Manage Requests',
    
    'scenario_director' => 'Director',
    'scenario_director_1' => 'View Library',
    'scenario_director_2' => 'Create Requests',
    'scenario_director_3' => 'Manage Requests',
    'scenario_director_4' => 'Settings',
    
    'configuration_note' => 'Configuration',
    'configuration_desc' => 'Assign permissions in SeAT\'s role management under Corporation → Access → Blueprint Manager.',

    // FAQ Section
    'faq_title' => 'Frequently Asked Questions',
    
    // FAQ Items
    'faq_1_q' => 'Do blueprints automatically update when I add new ones in-game?',
    'faq_1_a' => 'Yes! Blueprint Manager reads directly from SeAT\'s asset and blueprint tables, which sync automatically with EVE\'s ESI API. New blueprints, research changes, and location updates appear after SeAT\'s next sync (typically 15-60 minutes).',

    'faq_2_q' => 'Why don\'t I see any blueprints in the library?',
    'faq_2_a' => 'Check these common issues:<ul><li>Have you configured container patterns in Settings?</li><li>Are your in-game containers named?</li><li>Has SeAT synced your corporation\'s assets recently?</li><li>Do your container name patterns match the actual container names?</li></ul>Try using "Detect Containers" in Settings to automatically find your blueprint containers.',

    'faq_3_q' => 'Can I organize blueprints from multiple corporations?',
    'faq_3_a' => 'Yes! Each container configuration can optionally be filtered to a specific corporation, or left blank to apply to all corporations. This allows you to manage blueprints across multiple corporations in the same SeAT installation.',

    'faq_4_q' => 'How do wildcards work in container patterns?',
    'faq_4_a' => 'Use <code>*</code> to match any characters:<ul><li><code>*Frigate*</code> matches "T1 Frigates", "Faction Frigates BPC", "Frigate Hulls"</li><li><code>Capital*</code> matches "Capital Ships", "Capital Modules" (starts with)</li><li><code>*BPC</code> matches "Ships BPC", "Modules BPC" (ends with)</li></ul>Patterns are case-insensitive.',

    'faq_5_q' => 'Can members request blueprints that aren\'t in the library?',
    'faq_5_a' => 'No. The request system only shows blueprints that exist in configured containers. This prevents requests for items you don\'t have and keeps the workflow realistic.',

    'faq_6_q' => 'Do notifications work with both Discord and Slack?',
    'faq_6_a' => 'Yes! Both Discord and Slack support webhook functionality. Simply create a webhook in your channel and paste the URL into Blueprint Manager\'s webhook configuration. The formatting works for both platforms.',

    'faq_7_q' => 'Can I have different notifications go to different channels?',
    'faq_7_a' => 'Yes! Create multiple webhook configurations with different URLs. Each webhook can be configured to notify for specific events (created, approved, rejected, fulfilled) and optionally filtered by corporation.',

    'faq_8_q' => 'What\'s the difference between Approved and Fulfilled?',
    'faq_8_a' => '<ul><li><strong>Approved:</strong> Manager has accepted the request and will provide it</li><li><strong>Fulfilled:</strong> Blueprint has been physically provided (contracted, placed in hangar, etc.)</li></ul>This two-step process helps track outstanding commitments versus completed deliveries.',

    'faq_9_q' => 'Can members delete their requests?',
    'faq_9_a' => 'Members can delete their own requests that are <strong>Pending</strong> or <strong>Rejected</strong>. Once a request is Approved or Fulfilled, it becomes part of the permanent record and cannot be deleted.',

    'faq_10_q' => 'How far back does the statistics data go?',
    'faq_10_a' => 'All request data is kept indefinitely in the database. The time series graphs show data for the selected period (7, 30, or 90 days), but the underlying data is never deleted. This allows for long-term trend analysis.',

    'faq_11_q' => 'Does this plugin handle BPOs or just BPCs?',
    'faq_11_a' => 'Both! The library shows all blueprints in configured containers, whether they\'re BPOs (originals) or BPCs (copies). BPOs are indicated by -1 runs. The plugin doesn\'t restrict based on blueprint type.',

    'faq_12_q' => 'Can I export statistics or request data?',
    'faq_12_a' => 'Currently there\'s no built-in export function. However, all data is stored in standard database tables and can be queried directly if needed. Export functionality may be added in a future version.',

    // Troubleshooting Section
    'troubleshooting_guide' => 'Troubleshooting Guide',
    
    'trouble_no_blueprints' => 'No Blueprints Showing in Library',
    'symptom' => 'Symptom',
    'symptom_no_bp' => 'Library is empty or showing no categories',
    'possible_causes' => 'Possible Causes & Solutions',
    
    'cause_1' => '1. No Container Configurations',
    'cause_1_fix' => '<ul><li>Go to Settings → Container Configurations</li><li>Add at least one container pattern</li><li>Use "Detect Containers" to find existing containers</li></ul>',

    'cause_2' => '2. Containers Not Named In-Game',
    'cause_2_fix' => '<ul><li>In EVE Online, right-click containers and select "Set Name"</li><li>Give them descriptive names</li><li>Wait for next SeAT asset sync</li></ul>',

    'cause_3' => '3. Pattern Mismatch',
    'cause_3_fix' => '<ul><li>Check that container patterns match actual container names</li><li>Patterns are case-insensitive</li><li>Use <code>*</code> wildcards for flexibility (e.g., <code>*ship*</code>)</li></ul>',

    'cause_4' => '4. SeAT Not Synced',
    'cause_4_fix' => '<ul><li>Check SeAT\'s job queue status</li><li>Verify corporation API tokens are valid</li><li>Ensure assets and blueprints scopes are enabled</li></ul>',

    'trouble_webhooks' => 'Webhooks Not Working',
    'symptom_webhook' => 'No notifications appearing in Discord/Slack',
    'webhook_solutions' => 'Solutions:',
    'webhook_sol_1' => '<strong>Test the Webhook:</strong> Use the Test button in webhook configuration',
    'webhook_sol_2' => '<strong>Check URL:</strong> Ensure webhook URL is correct and not expired',
    'webhook_sol_3' => '<strong>Verify Events:</strong> Make sure the event type is enabled (created, approved, etc.)',
    'webhook_sol_4' => '<strong>Corporation Filter:</strong> If set, webhook only fires for that corporation',
    'webhook_sol_5' => '<strong>Check Logs:</strong> Look at SeAT logs for error messages',

    'trouble_not_updating' => 'Blueprints Not Updating',
    'symptom_update' => 'New blueprints or research changes not appearing',
    'update_solutions' => 'Solutions:',
    'update_sol_1' => 'Blueprint Manager doesn\'t sync data itself - it reads from SeAT',
    'update_sol_2' => 'Check when SeAT last synced this corporation\'s assets',
    'update_sol_3' => 'Typical sync interval is 15-60 minutes',
    'update_sol_4' => 'Force a manual sync from SeAT if needed',
    'update_sol_5' => 'Refresh the page after SeAT syncs',
    
    'understanding_flow' => 'Understanding Data Flow',
    'data_flow' => 'EVE ESI → SeAT Sync → SeAT Database → Blueprint Manager reads from database',

    'trouble_permissions' => 'Permission Errors',
    'symptom_perm' => '"Access denied" or "Insufficient permissions"',
    'perm_solutions' => 'Solutions:',
    'perm_sol_1' => 'Check SeAT role configuration',
    'perm_sol_2' => 'Verify the user has the correct Blueprint Manager permissions',
    'perm_sol_3' => 'Ensure corporation access is granted',
    'perm_sol_4' => 'Log out and back in to refresh permissions',

    'trouble_statistics' => 'Statistics Not Showing Data',
    'symptom_stats' => 'Statistics page empty or showing zeros',
    'stats_reasons' => 'Common Reasons:',
    'stats_reason_1' => 'No requests have been created yet',
    'stats_reason_2' => 'Time period selected has no activity',
    'stats_reason_3' => 'Corporation filter excluding all requests',
    'stats_reason_4' => 'Try selecting "All Time" or a longer period',

    'trouble_detection' => 'Container Detection Not Finding Containers',
    'symptom_detect' => 'Detection shows no results',
    'detect_checklist' => 'Checklist:',
    'detect_check_1' => 'Containers must be named in-game',
    'detect_check_2' => 'Containers must actually contain blueprints',
    'detect_check_3' => 'SeAT must have synced assets recently',
    'detect_check_4' => 'Try different hangar filters or "All Hangars"',
    'detect_check_5' => 'Check the selected corporation has blueprints',

    'getting_help' => 'Getting Help',
    'help_steps' => 'If you\'re still experiencing issues:',
    'help_1' => 'Check the <a href="https://github.com/MattFalahe/blueprint-manager/issues" target="_blank">GitHub Issues</a> page',
    'help_2' => 'Review SeAT logs for error messages',
    'help_3' => 'Verify your SeAT installation is up to date',
    'help_4' => 'Create a new issue with:',
    'help_4_1' => 'SeAT version',
    'help_4_2' => 'Blueprint Manager version',
    'help_4_3' => 'Description of the problem',
    'help_4_4' => 'Steps to reproduce',
    'help_4_5' => 'Any error messages',
];
