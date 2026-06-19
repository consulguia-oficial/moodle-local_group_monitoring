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
 * Main page for the Group Monitoring plugin.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once(__DIR__ . '/classes/local/group_manager.php');

$courseid = required_param('courseid', PARAM_INT);
$tab = optional_param('tab', 'dashboard', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_ALPHA);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);
require_capability('local/group_monitoring:view', $context);

if ($action !== '' && data_submitted()) {
    require_sesskey();

    try {
        if ($action === 'createtutor') {
            require_capability('local/group_monitoring:createtutor', $context);

            $data = (object)[
                'username' => required_param('username', PARAM_USERNAME),
                'password' => required_param('password', PARAM_RAW_TRIMMED),
                'lastname' => required_param('lastname', PARAM_TEXT),
                'email' => required_param('email', PARAM_EMAIL),
                'roleid' => required_param('roleid', PARAM_INT),
                'polo' => optional_param('polo', '', PARAM_TEXT),
            ];
            \local_group_monitoring\local\group_manager::create_tutor($courseid, $data);
            \core\notification::success(get_string('tutor_created', 'local_group_monitoring'));
            $tab = 'tutors';
        } else if ($action === 'creategroup') {
            require_capability('local/group_monitoring:creategroup', $context);

            $polo = required_param('polo', PARAM_TEXT);
            $tutorid = required_param('tutorid', PARAM_INT);
            \local_group_monitoring\local\group_manager::create_group($courseid, $polo, $tutorid);
            \core\notification::success(get_string('group_created_plain', 'local_group_monitoring'));
            $tab = 'groups';
        } else if ($action === 'createpolo') {
            require_capability('local/group_monitoring:managepolo', $context);

            \local_group_monitoring\local\group_manager::check_and_create_polo_field();
            \core\notification::success(get_string('polo_field_created', 'local_group_monitoring'));
            $tab = 'polos';
        } else if ($action === 'addmember') {
            require_capability('local/group_monitoring:addmember', $context);

            $groupid = required_param('groupid', PARAM_INT);
            $username = required_param('username', PARAM_USERNAME);
            \local_group_monitoring\local\group_manager::add_member($groupid, $username, 'manual');
            \core\notification::success(get_string('member_added', 'local_group_monitoring'));
            $tab = 'groups';
        } else if ($action === 'importcsv') {
            require_capability('local/group_monitoring:addmember', $context);

            $groupid = required_param('groupid', PARAM_INT);
            if (empty($_FILES['csvfile']) || $_FILES['csvfile']['error'] !== UPLOAD_ERR_OK) {
                throw new moodle_exception('error_csv_invalid', 'local_group_monitoring');
            }

            $upload = $_FILES['csvfile'];
            $filename = clean_param($upload['name'], PARAM_FILE);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($extension !== 'csv' || !is_uploaded_file($upload['tmp_name'])) {
                throw new moodle_exception('error_csv_invalid', 'local_group_monitoring');
            }

            $count = \local_group_monitoring\local\group_manager::import_csv($groupid, $upload['tmp_name']);
            \core\notification::success(get_string('members_imported', 'local_group_monitoring', $count));
            $tab = 'groups';
        }

        redirect(new moodle_url('/local/group_monitoring/index.php', ['courseid' => $courseid, 'tab' => $tab]));
    } catch (moodle_exception $e) {
        \core\notification::error($e->getMessage());
    } catch (Throwable $e) {
        debugging($e->getMessage(), DEBUG_DEVELOPER);
        \core\notification::error(get_string('error_unexpected', 'local_group_monitoring'));
    }
}

$PAGE->set_url(new moodle_url('/local/group_monitoring/index.php', ['courseid' => $courseid, 'tab' => $tab]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_group_monitoring'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('incourse');

$metrics = new stdClass();
$metrics->courses = 1;
$metrics->groups = $DB->count_records('local_group_monitoring_groups', ['courseid' => $courseid]);
$metrics->polos = count(\local_group_monitoring\local\group_manager::get_real_polos());
$metrics->tutors = $DB->count_records_select(
    'local_group_monitoring_members',
    'groupid IN (SELECT id FROM {local_group_monitoring_groups} WHERE courseid = ?)',
    [$courseid]
);

$realgroups = $DB->get_records('local_group_monitoring_groups', ['courseid' => $courseid], 'timecreated DESC') ?: [];
foreach ($realgroups as $group) {
    $sqlmembers = "SELECT m.id, m.username, m.added_via, u.firstname, u.lastname
                     FROM {local_group_monitoring_members} m
                LEFT JOIN {user} u ON u.id = m.userid
                    WHERE m.groupid = ?
                 ORDER BY m.timecreated ASC";
    $group->members = array_values($DB->get_records_sql($sqlmembers, [$group->id]) ?: []);
    $group->membercount = count($group->members);
}

$realpolos = \local_group_monitoring\local\group_manager::get_real_polos();
$realroles = \local_group_monitoring\local\group_manager::get_roles();

$like = $DB->sql_like('r.shortname', ':tutorlike');
$sqltutors = "SELECT u.id, u.firstname, u.lastname, u.email
                FROM {user} u
                JOIN {role_assignments} ra ON ra.userid = u.id
                JOIN {role} r ON r.id = ra.roleid
               WHERE (r.shortname = 'tutorpedagogico' OR r.shortname = 'tutor' OR $like)
                 AND u.deleted = 0
                 AND u.id IN (
                    SELECT ue.userid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                     WHERE e.courseid = :courseid
                 )";
$realtutors = $DB->get_records_sql($sqltutors, ['courseid' => $courseid, 'tutorlike' => '%tutor%']) ?: [];

$renderable = new \local_group_monitoring\output\main_page(
    $courseid,
    $tab,
    $metrics,
    $course,
    $realgroups,
    $realpolos,
    $realtutors,
    $realroles,
    has_capability('local/group_monitoring:creategroup', $context),
    has_capability('local/group_monitoring:addmember', $context),
    has_capability('local/group_monitoring:createtutor', $context),
    has_capability('local/group_monitoring:managepolo', $context)
);

$renderer = $PAGE->get_renderer('local_group_monitoring');

echo $OUTPUT->header();
echo $renderer->render($renderable);
echo $OUTPUT->footer();
