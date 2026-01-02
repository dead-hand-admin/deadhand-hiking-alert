<?php
/**
 * English language
 */

return [
    // Common
    'app_name' => 'DeadHand',
    'app_tagline' => 'Tourist Safety System',
    'hours' => 'hours',
    'days' => 'days',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
    'trip_save' => 'Save',
    'file_delete_confirm' => 'Delete file?',
    
    // Navigation
    'nav_home' => 'Home',
    'nav_profile' => 'Profile',
    'nav_login' => 'Login',
    'nav_register' => 'Register',
    'nav_logout' => 'Logout',
    
    // Home page
    'home_title' => 'Dead Man\'s Switch for Tourists',
    'home_subtitle' => 'Automatic notification to rescuers if you don\'t return from a hike on time',
    'home_cta_start' => 'Get Started',
    'home_cta_login' => 'Login',
    'home_cta_profile' => 'My Profile',
    
    'home_step1_title' => '1. Register Trip',
    'home_step1_desc' => 'Specify route, stages and checkpoints with return dates',
    'home_step2_title' => '2. Automatic Monitoring',
    'home_step2_desc' => 'System monitors deadlines and waits for your confirmation',
    'home_step3_title' => '3. Alert',
    'home_step3_desc' => 'If you don\'t confirm return — emails are sent to emergency services and your contacts',
    
    // Registration
    'register_title' => 'Registration',
    'register_email' => 'Email',
    'register_password' => 'Password (minimum 8 characters)',
    'register_password_confirm' => 'Confirm Password',
    'register_gdpr_text' => 'By continuing registration, I agree to the processing of personal data (name, photos, habits, contacts) for emergency notification purposes',
    'register_submit' => 'Register',
    'register_have_account' => 'Already have an account?',
    'register_login_link' => 'Login',
    
    // Validation
    'error_csrf' => 'Invalid security token',
    'error_email_invalid' => 'Invalid email',
    'error_password_short' => 'Password must be at least 8 characters',
    'error_passwords_mismatch' => 'Passwords do not match',
    'error_gdpr_required' => 'Consent to personal data processing is required',
    'error_email_exists' => 'This email is already registered',
    'error_registration_failed' => 'Registration error. Please try again later.',
    'success_registration' => 'Registration successful! A confirmation email has been sent.',
    
    // Footer
    'footer_copyright' => 'DeadHand. Tourist Safety System.',
    
    // 404
    'error_404' => 'Page not found',

    // Login
    'login_title' => 'Login',
    'login_email' => 'Email',
    'login_password' => 'Password',
    'login_submit' => 'Login',
    'login_no_account' => 'Don\'t have an account?',
    'login_register_link' => 'Register',

    'error_login_failed' => 'Invalid email or password',
    'error_email_not_confirmed' => 'Email not confirmed. Check your inbox.',
    'error_invalid_token' => 'Invalid confirmation link',

    'success_login' => 'You are logged in',
    'success_logout' => 'You have logged out',
    'success_email_confirmed' => 'Email confirmed! You can now login.',

// Profile
'profile_title' => 'My Profile',
'profile_tab_personal' => 'Personal Info',
'profile_tab_contacts' => 'Contacts',
'profile_tab_photos' => 'Photos',

'profile_personal_info' => 'Personal Information',
'profile_email' => 'Email',
'profile_email_hint' => 'Email cannot be changed',
'profile_fio' => 'Full Name',
'profile_fio_placeholder' => 'John Smith',
'profile_habits' => 'Habits and Characteristics',
'profile_habits_placeholder' => 'Clothing colors, distinguishing features, allergies, radio frequency, etc.',
'profile_habits_hint' => 'This information will help rescuers identify you',
'profile_timezone' => 'Timezone',
'profile_country' => 'Country',
'profile_emergency_service' => 'Default Emergency Service',
'profile_emergency_service_none' => 'Not selected',
'profile_emergency_default' => 'default',
'profile_save' => 'Save',

'profile_contacts_title' => 'Emergency Contacts',
'profile_contacts_description' => 'These people will receive emails if you don\'t return from a trip on time',
'profile_no_contacts' => 'No contacts yet',
'profile_add_contact' => 'Add Contact',
'profile_contact_name' => 'Name',
'profile_contact_email' => 'Email',
'profile_contact_add' => 'Add',
'profile_contacts_limit' => 'Maximum number of contacts reached',
'profile_contact_delete_confirm' => 'Delete this contact?',
'profile_delete' => 'Delete',

'profile_photos_title' => 'Your Photos',
'profile_photos_description' => 'Photos in hiking gear will help rescuers find you faster',
'profile_no_photos' => 'No photos yet',
'profile_upload_photo' => 'Upload Photo',
'profile_photo_file' => 'File',
'profile_photo_hint' => 'Maximum 5 MB, JPG or PNG',
'profile_photo_description' => 'Description (optional)',
'profile_photo_description_placeholder' => 'In red jacket, with backpack',
'profile_photo_upload' => 'Upload',
'profile_photos_limit' => 'Maximum number of photos reached',
'profile_photo_delete_confirm' => 'Delete this photo?',

'error_login_required' => 'You must be logged in',

// Profile errors
'error_fio_required' => 'Please enter your full name',
'error_profile_update_failed' => 'Failed to update profile',
'error_contact_required' => 'Please enter contact name and email',
'error_contacts_limit' => 'Maximum number of contacts reached',
'error_contact_add_failed' => 'Failed to add contact',
'error_contact_not_found' => 'Contact not found',
'error_contact_delete_failed' => 'Failed to delete contact',
'error_photos_limit' => 'Maximum number of photos reached',
'error_photo_upload_failed' => 'File upload failed',
'error_photo_too_large' => 'File is too large (maximum 5 MB)',
'error_photo_invalid_type' => 'Invalid file format (only JPG, PNG)',
'error_photo_processing_failed' => 'Image processing error',
'error_photo_not_found' => 'Photo not found',
'error_photo_delete_failed' => 'Failed to delete photo',

// Success messages
'success_profile_updated' => 'Profile updated',
'success_contact_added' => 'Contact added',
'success_contact_deleted' => 'Contact deleted',
'success_photo_uploaded' => 'Photo uploaded',
'success_photo_deleted' => 'Photo deleted',

// Navigation
'nav_trips' => 'Trips',

// Trips - list
'trips_title' => 'My Trips',
'trips_create' => 'Create Trip',
'trips_no_trips' => 'You have no trips yet',
'trips_active' => 'Active',
'trips_draft' => 'Drafts',
'trips_completed' => 'Completed',
'trips_cancelled' => 'Cancelled',

'trip_status_draft' => 'Draft',
'trip_status_active' => 'Active',
'trip_status_completed' => 'Completed',
'trip_status_cancelled' => 'Cancelled',

'trip_created' => 'Created',
'trip_stages' => 'stages',
'trip_view' => 'View',

// Create trip
'trip_create_title' => 'Create New Trip',
'trip_country' => 'Trip Country',
'trip_emergency_service' => 'Emergency Service',
'trip_emergency_service_hint' => 'Can be changed for each stage separately',

'trip_stages_title' => 'Trip Stages',
'trip_stages_description' => 'Break your trip into checkpoints. At each point you will need to confirm that everything is OK.',

'trip_stage_number' => 'Stage',
'trip_stage_description' => 'Name',
'trip_stage_description_placeholder' => 'Ascent to base camp',
'trip_stage_deadline_time' => 'By time',
'trip_stage_emergency_service' => 'Emergency Service',
'trip_stage_add' => 'Add Stage',
'trip_stage_remove' => 'Remove',

'trip_files_title' => 'Files',
'trip_track' => 'Route Track (GPX, KML)',
'trip_photos' => 'Route Photos (up to 2)',

'trip_submit' => 'Create Trip',
'trip_save_draft' => 'Save Draft',

// Errors
'error_trip_country_required' => 'Select country',
'error_trip_stages_required' => 'Add at least one stage',
'error_trip_stage_description_required' => 'Enter stage name',
'error_trip_stage_duration_required' => 'Enter stage duration',
'error_trip_create_failed' => 'Failed to create trip',
'error_trip_not_found' => 'Trip not found',
'error_trip_not_active' => 'Trip is not active',
'error_no_active_stage' => 'No active stage',
'error' => 'Error',

// Success
'success_trip_created' => 'Trip created! Check your email for activation.',
'success_trip_draft_saved' => 'Draft saved',

// Create trip
'trip_name' => 'Trip Name',
'trip_name_placeholder' => 'Khan Tengri Expedition',
'trip_name_hint' => 'Use a clear name to distinguish similar routes',
'trip_start_date' => 'Start Date',
'trip_start_date_hint' => 'Start date of the first stage',

// Trip list
'trip_copy' => 'Copy',
'success_trip_copied' => 'Trip copied to drafts',

// Errors
'error_trip_name_required' => 'Enter trip name',
'error_trip_start_date_required' => 'Enter start date',
'error_trip_start_date_past' => 'Start date cannot be in the past',

'error_trip_already_active' => 'You already have an active trip. Complete it before creating a new one.',
'error_track_invalid_format' => 'Invalid track format (only GPX, KML, KMZ)',
'error_track_too_large' => 'Track is too large (maximum 5 MB)',

'trip_stage_requires_confirmation' => 'Require stage confirmation',
'trip_stage_requires_confirmation_hint' => 'Uncheck for rest days/technical stages',
'trip_stage_duration_full_days' => 'Full days',
'trip_stage_checkpoint' => 'Checkpoint (stage finish)',
'trip_stage_checkpoint_placeholder' => 'Camp at 3200m altitude',

// Trip view
'trip_activate' => 'Activate Trip',
'trip_cancel' => 'Cancel Trip',
'trip_cancel_confirm' => 'Are you sure you want to cancel this trip?',
'trip_back_to_list' => 'Back to List',
'trip_no_stages' => 'No stages',
'trip_tracks' => 'Route Tracks',
'trip_photos_list' => 'Route Photos',
'trip_photo_alt' => 'Route photo',

// Stage statuses
'stage_status_active' => 'Active',
'stage_status_pending' => 'Pending',
'stage_status_confirmed' => 'Confirmed',
'stage_status_overdue' => 'Overdue',
'stage_status_cancelled' => 'Cancelled',

// Stage fields
'stage_duration' => 'Duration',
'stage_days_unit' => 'days',
'stage_deadline_time' => 'Deadline Time',
'stage_deadline' => 'Deadline',
'stage_auto_transition' => 'auto transition',
'stage_confirm_button' => 'Confirm Stage Completion',

// Common
'yes' => 'Yes',
'no' => 'No',

// Success
'success_stage_confirmed' => 'Stage confirmed',

'stage_requires_confirmation' => 'Requires Confirmation',
'trip_edit' => 'Edit',
'trip_edit_title' => 'Edit Trip',

'error_trip_not_draft' => 'Trip is already activated or completed',
'error_trip_not_active' => 'Trip is not active',
'error_trip_cannot_edit' => 'Only drafts can be edited',
'error_trip_activation_failed' => 'Failed to activate trip',
'error_trip_cancel_failed' => 'Failed to cancel trip',
'error_trip_update_failed' => 'Failed to update trip',
'error_file_not_found' => 'File not found',
'error_file_delete_failed' => 'Failed to delete file',

'success_trip_activated' => 'Trip activated! First stage started.',
'success_trip_cancelled' => 'Trip cancelled',
'success_trip_updated' => 'Trip updated',
'success_file_deleted' => 'File deleted',

'error_trip_no_stages' => 'Trip has no stages',

'trip_activate_confirm' => 'Make sure all stages are filled. Activate trip? Start date will be set to today.',

'trip_delete' => 'Delete',
'trip_delete_confirm' => 'Delete trip draft? This action cannot be undone.',
'error_trip_cannot_delete' => 'Only drafts can be deleted',
'error_trip_delete_failed' => 'Failed to delete trip',
'success_trip_deleted' => 'Trip deleted',

// Action tokens (short links from emails)
'action_confirm_stage_title' => 'Confirm Stage Completion',
'action_confirm_stage_text' => 'Do you really want to confirm completion of stage "{stage}" and proceed to the next stage?',
'action_confirm_stage_last_text' => 'Do you really want to confirm completion of the last stage "{stage}" and complete the trip?',
'action_confirm_stage_button' => 'Yes, confirm',
'action_cancel_trip_title' => 'Cancel Trip',
'action_cancel_trip_text' => 'Do you really want to cancel trip "{trip}"? All scheduled alerts will be cancelled.',
'action_cancel_trip_button' => 'Yes, cancel trip',
'action_complete_trip_title' => 'Complete Trip',
'action_complete_trip_text' => 'Do you really want to complete trip "{trip}"? All scheduled alerts will be cancelled.',
'action_complete_trip_button' => 'Yes, complete trip',
'action_extend_stage_title' => 'Extend Stage Deadline',
'action_extend_stage_text' => 'Choose how to extend deadline for stage "{stage}":',
'action_extend_hours' => 'Add hours',
'action_extend_to_date' => 'Move to date',
'action_extend_date' => 'Date',
'action_extend_time' => 'Until time',
'action_extend_button' => 'Extend',
'action_back_to_trips' => 'Back to trips',

'action_token_invalid' => 'Invalid or expired link',
'action_success' => 'Action completed successfully',

'error_extend_hours_required' => 'Specify number of hours',
'error_extend_date_required' => 'Specify date and time',

];