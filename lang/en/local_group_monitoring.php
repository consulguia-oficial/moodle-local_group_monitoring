<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English strings for the Group Monitoring plugin.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Group monitoring';
$string['pluginnamesettings'] = 'Group monitoring settings';
$string['dashboard'] = 'Group monitoring dashboard';
$string['groups'] = 'Groups';
$string['creategroup'] = 'Create group';
$string['groupdetail'] = 'Group detail';
$string['members'] = 'Members';
$string['addmember'] = 'Add member';
$string['importcsv'] = 'Import via CSV';
$string['courseid'] = 'Course';
$string['polo'] = 'Polo';
$string['tutor'] = 'Tutor';
$string['groupname'] = 'Group name';
$string['username'] = 'Username';
$string['fullname'] = 'Full name';
$string['added_via'] = 'Added via';
$string['added_via_manual'] = 'Manual';
$string['added_via_csv'] = 'CSV';
$string['error_no_polo'] = 'No polo found for the selected course.';
$string['error_no_tutor'] = 'No tutors assigned to this polo.';
$string['error_duplicate'] = 'A group with this name already exists in this course.';
$string['error_csv_empty'] = 'No usernames were found in the uploaded CSV file.';
$string['error_csv_invalid'] = 'Invalid CSV file. Expected one username per line.';
$string['error_invalidemail'] = 'Invalid email address.';
$string['error_invalidrole'] = 'The selected role is not allowed for tutor creation.';
$string['error_unexpected'] = 'An unexpected error occurred. Please contact the site administrator.';
$string['error_usernotfound'] = 'The user {$a} was not found in Moodle.';
$string['error_usernameexists'] = 'This username already exists in Moodle.';
$string['group_created'] = 'Group "{$a}" created successfully.';
$string['group_created_plain'] = 'Group created successfully.';
$string['group_deleted'] = 'Group deleted successfully.';
$string['member_added'] = 'Member added successfully.';
$string['members_imported'] = '{$a} members imported successfully.';
$string['polo_field_created'] = 'The configured polo profile field is available.';
$string['tutor_created'] = 'Tutor created and enrolled successfully.';
$string['courses'] = 'Courses';
$string['polos'] = 'Regional center';
$string['tutors'] = 'Tutors';
$string['polofield'] = 'Profile field for polo';
$string['polofield_desc'] = 'Select the custom user profile field used to group polos. If no field exists, the default "polo" field will be used and created.';

// ---------------------------------------------------------------------------
// Capability strings - correct Frankenstyle keys (pluginname:capabilityname)
// ---------------------------------------------------------------------------
$string['group_monitoring:view'] = 'View group monitoring dashboard';
$string['group_monitoring:creategroup'] = 'Create monitored groups';
$string['group_monitoring:addmember'] = 'Add members to monitored groups';
$string['group_monitoring:createtutor'] = 'Create tutor users';
$string['group_monitoring:managepolo'] = 'Manage polo profile field';
$string['group_monitoring:deletegroup'] = 'Delete monitored groups';
$string['group_monitoring:config'] = 'Configure group monitoring plugin';

// ---------------------------------------------------------------------------
// UI strings for templates and AMD modules (Issue 5 - i18n)
// ---------------------------------------------------------------------------
$string['subtitle'] = 'Overview and management of Group Monitoring';
$string['tab_dashboard'] = 'Dashboard';
$string['current_course'] = 'Current Course';
$string['students_linked'] = 'linked students';
$string['no_members'] = 'No students linked.';
$string['no_groups'] = 'No monitored groups in this course. Create a new group above.';
$string['no_polos'] = 'No polos registered in users. Click the button above to ensure the structure and then register tutors.';
$string['no_tutors'] = 'No users with the tutor role enrolled in this course.';
$string['students'] = 'Students';
$string['btn_polo'] = 'Ensure Polo Field';
$string['btn_tutor'] = 'New Tutor';

// Modal titles and buttons
$string['modal_title_polo'] = 'Ensure Polo Field';
$string['modal_btn_polo'] = 'Create Field';
$string['modal_hint_polo'] = 'This will create the profile_field_polo field natively in Moodle.';
$string['modal_title_tutor'] = 'New Tutor';
$string['modal_btn_tutor'] = 'Create Tutor';
$string['modal_title_group'] = 'New Group';
$string['modal_btn_group'] = 'Create Group';
$string['modal_add_to'] = 'Add member to {$a}';
$string['modal_btn_add_member'] = 'Add Student';
$string['modal_import_to'] = 'Import CSV to {$a}';
$string['modal_btn_import'] = 'Import Students';

// Form field labels used in AMD modals
$string['form_firstname'] = 'First name *';
$string['form_lastname'] = 'Last name *';
$string['form_email'] = 'Email *';
$string['form_username_req'] = 'Username *';
$string['form_password'] = 'Initial Password *';
$string['form_role'] = 'Course Role *';
$string['form_polo_optional'] = 'Link to a Polo';
$string['form_polo_placeholder'] = 'Enter polo name (Optional)';
$string['form_polo_existing'] = 'Existing Polo *';
$string['form_tutor_select'] = 'Tutor (User) *';
$string['form_group_hint'] = 'Generated format: Polo_Name_LastInitial';
$string['form_select_placeholder'] = 'Select...';
$string['form_student_username'] = 'Student Username *';
$string['form_csv_file'] = 'CSV File *';
$string['form_csv_hint'] = 'The file must contain student usernames, one per line.';

// ---------------------------------------------------------------------------
// Privacy metadata strings - Frankenstyle table names (Issue 2)
// ---------------------------------------------------------------------------
$string['privacy:metadata:local_group_monitoring_groups'] = 'Stores monitored groups created and managed by the Group Monitoring plugin.';
$string['privacy:metadata:local_group_monitoring_groups:name'] = 'The monitored group name.';
$string['privacy:metadata:local_group_monitoring_groups:courseid'] = 'The course where the monitored group was created.';
$string['privacy:metadata:local_group_monitoring_groups:polo'] = 'The polo associated with the monitored group.';
$string['privacy:metadata:local_group_monitoring_groups:tutorid'] = 'The user ID of the tutor associated with the monitored group.';
$string['privacy:metadata:local_group_monitoring_groups:moodle_groupid'] = 'The corresponding Moodle group ID.';
$string['privacy:metadata:local_group_monitoring_groups:timecreated'] = 'The time when the monitored group was created.';
$string['privacy:metadata:local_group_monitoring_groups:timemodified'] = 'The time when the monitored group was last modified.';
$string['privacy:metadata:local_group_monitoring_groups:usermodified'] = 'The user ID of the user who last modified the monitored group.';
$string['privacy:metadata:local_group_monitoring_members'] = 'Stores members added to monitored groups.';
$string['privacy:metadata:local_group_monitoring_members:groupid'] = 'The monitored group ID.';
$string['privacy:metadata:local_group_monitoring_members:userid'] = 'The user ID of the group member.';
$string['privacy:metadata:local_group_monitoring_members:username'] = 'The username of the group member.';
$string['privacy:metadata:local_group_monitoring_members:added_via'] = 'The method used to add the member to the group.';
$string['privacy:metadata:local_group_monitoring_members:timecreated'] = 'The time when the member was added.';
$string['privacy:metadata:local_group_monitoring_members:usermodified'] = 'The user ID of the user who added or last modified the member record.';
$string['privacy:metadata:local_group_monitoring_polos'] = 'Stores polos managed by the Group Monitoring plugin.';
$string['privacy:metadata:local_group_monitoring_polos:name'] = 'The polo name.';
$string['privacy:metadata:local_group_monitoring_polos:timecreated'] = 'The time when the polo was created.';
$string['privacy:metadata:local_group_monitoring_polos:timemodified'] = 'The time when the polo was last modified.';
$string['privacy:metadata:local_group_monitoring_polos:usermodified'] = 'The user ID of the user who last modified the polo.';
