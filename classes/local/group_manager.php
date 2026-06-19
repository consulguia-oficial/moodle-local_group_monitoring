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
 * Group Monitoring management helper.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_group_monitoring\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for groups, tutors, polos and members.
 */
class group_manager {
    /**
     * Return configured polo profile field shortname.
     *
     * @return string
     */
    public static function get_polo_field_name(): string {
        $field = get_config('local_group_monitoring', 'polofield');
        return empty($field) ? 'polo' : clean_param($field, PARAM_ALPHANUMEXT);
    }

    /**
     * Ensure the configured polo custom profile field exists.
     */
    public static function check_and_create_polo_field(): void {
        global $DB;

        $fieldname = self::get_polo_field_name();
        if ($DB->record_exists('user_info_field', ['shortname' => $fieldname])) {
            return;
        }

        $category = $DB->get_record('user_info_category', [], '*', IGNORE_MULTIPLE);
        if (!$category) {
            $category = (object)[
                'name' => 'Group Monitoring',
                'sortorder' => 1,
            ];
            $category->id = $DB->insert_record('user_info_category', $category);
        }

        $field = (object)[
            'shortname' => $fieldname,
            'name' => ucfirst($fieldname),
            'datatype' => 'text',
            'categoryid' => $category->id,
            'visible' => 2,
            'locked' => 0,
            'sortorder' => 1,
        ];
        $DB->insert_record('user_info_field', $field);
    }

    /**
     * Create or update a reduced tutor role without copying manager permissions.
     *
     * The plugin capability is assigned only after Moodle has registered it in
     * the capabilities table. This avoids installation/upgrade failures while
     * keeping the tutor role available when the tool is used.
     */
    public static function create_tutor_role(): void {
        global $DB, $CFG;

        require_once($CFG->libdir . '/accesslib.php');

        $role = $DB->get_record('role', ['shortname' => 'tutor']);
        if ($role) {
            $roleid = (int)$role->id;
        } else {
            $roleid = create_role('Tutor', 'tutor', 'Reduced tutor role created by Group Monitoring.');
            if (!$roleid) {
                return;
            }
        }

        set_role_contextlevels($roleid, [CONTEXT_COURSE]);

        $systemcontext = \context_system::instance();
        $capabilities = [
            'moodle/course:view',
            'moodle/course:viewparticipants',
            'moodle/site:accessallgroups',
            'local/group_monitoring:view',
        ];

        foreach ($capabilities as $capability) {
            if (!$DB->record_exists('capabilities', ['name' => $capability])) {
                continue;
            }
            assign_capability($capability, CAP_ALLOW, $roleid, $systemcontext->id, true);
        }
    }

    /**
     * Generate monitored group name.
     *
     * @param string $polo Polo name.
     * @param \stdClass $tutor Tutor user record.
     * @return string
     */
    public static function generate_group_name(string $polo, \stdClass $tutor): string {
        $initial = \core_text::strtoupper(\core_text::substr($tutor->lastname, 0, 1));
        return clean_param($polo . '_' . $tutor->firstname . '_' . $initial, PARAM_TEXT);
    }

    /**
     * Create a monitored group.
     *
     * @param int $courseid Course ID.
     * @param string $polo Polo name.
     * @param int $tutorid Tutor user ID.
     * @return int New monitored group ID.
     */
    public static function create_group(int $courseid, string $polo, int $tutorid): int {
        global $DB, $USER;

        $context = \context_course::instance($courseid);
        require_capability('local/group_monitoring:creategroup', $context);

        $polo = clean_param($polo, PARAM_TEXT);
        $tutor = $DB->get_record('user', ['id' => $tutorid, 'deleted' => 0], '*', MUST_EXIST);
        $name = self::generate_group_name($polo, $tutor);

        if ($DB->record_exists('local_group_monitoring_groups', ['courseid' => $courseid, 'name' => $name])) {
            throw new \moodle_exception('error_duplicate', 'local_group_monitoring');
        }

        $moodlegroup = (object)[
            'courseid' => $courseid,
            'name' => $name,
            'timecreated' => time(),
            'description' => '',
        ];
        $moodlegroupid = groups_create_group($moodlegroup);

        if ($moodlegroupid) {
            groups_add_member($moodlegroupid, $tutorid);
        }

        $record = (object)[
            'name' => $name,
            'courseid' => $courseid,
            'polo' => $polo,
            'tutorid' => $tutorid,
            'moodle_groupid' => $moodlegroupid,
            'timecreated' => time(),
            'timemodified' => time(),
            'usermodified' => $USER->id,
        ];

        return $DB->insert_record('local_group_monitoring_groups', $record);
    }

    /**
     * Create and enrol a tutor user.
     *
     * @param int $courseid Course ID.
     * @param object $data Submitted tutor data.
     * @return int Created user ID.
     */
    public static function create_tutor(int $courseid, object $data): int {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/user/lib.php');

        $context = \context_course::instance($courseid);
        require_capability('local/group_monitoring:createtutor', $context);

        $data->username = \core_text::strtolower(clean_param($data->username, PARAM_USERNAME));
        $data->firstname = clean_param($data->firstname, PARAM_TEXT);
        $data->lastname = clean_param($data->lastname, PARAM_TEXT);
        $data->email = clean_param($data->email, PARAM_EMAIL);
        $data->polo = clean_param($data->polo ?? '', PARAM_TEXT);

        if ($DB->record_exists('user', ['username' => $data->username])) {
            throw new \moodle_exception('error_usernameexists', 'local_group_monitoring');
        }

        if (!validate_email($data->email)) {
            throw new \moodle_exception('error_invalidemail', 'local_group_monitoring');
        }

        if (!self::is_allowed_tutor_role((int)$data->roleid)) {
            throw new \moodle_exception('error_invalidrole', 'local_group_monitoring');
        }

        $user = (object)[
            'username' => $data->username,
            'password' => $data->password,
            'firstname' => $data->firstname,
            'lastname' => $data->lastname,
            'email' => $data->email,
            'auth' => 'manual',
            'confirmed' => 1,
            'mnethostid' => $CFG->mnet_localhost_id,
        ];
        // Validate password policy before creating user.
        $errmsg = null;
        if (!check_password_policy($data->password, $errmsg)) {
            throw new \moodle_exception('errorpasswordpolicy', 'error', '', $errmsg);
        }
        $userid = user_create_user($user, true, false); // updatepassword=true hashes via Moodle

        self::enrol_user_manually($courseid, $userid, (int)$data->roleid);

        if ($data->polo !== '') {
            self::set_user_polo($userid, $data->polo, $USER->id);
        }

        return $userid;
    }

    /**
     * Add an existing Moodle user to a monitored group.
     *
     * @param int $groupid Monitored group ID.
     * @param string $username Username.
     * @param string $addedvia Added via source.
     * @return int New member ID, or 0 when already exists.
     */
    public static function add_member(int $groupid, string $username, string $addedvia = 'manual'): int {
        global $DB, $USER;

        $group = $DB->get_record('local_group_monitoring_groups', ['id' => $groupid], '*', MUST_EXIST);
        $context = \context_course::instance($group->courseid);
        require_capability('local/group_monitoring:addmember', $context);

        $username = clean_param($username, PARAM_USERNAME);
        $addedvia = clean_param($addedvia, PARAM_ALPHA);

        if ($DB->record_exists('local_group_monitoring_members', ['groupid' => $groupid, 'username' => $username])) {
            return 0;
        }

        $moodleuser = $DB->get_record('user', ['username' => $username, 'deleted' => 0]);
        if (!$moodleuser) {
            throw new \moodle_exception('error_usernotfound', 'local_group_monitoring', '', s($username));
        }

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $roleid = $studentrole ? (int)$studentrole->id : 0;
        self::enrol_user_manually($group->courseid, (int)$moodleuser->id, $roleid);

        $member = (object)[
            'groupid' => $groupid,
            'userid' => $moodleuser->id,
            'username' => $username,
            'added_via' => $addedvia,
            'timecreated' => time(),
            'usermodified' => $USER->id,
        ];
        $id = $DB->insert_record('local_group_monitoring_members', $member);

        if ($group->moodle_groupid) {
            groups_add_member($group->moodle_groupid, $moodleuser->id);
        }

        return $id;
    }

    /**
     * Import group members from uploaded CSV path.
     *
     * @param int $groupid Monitored group ID.
     * @param string $csvpath Uploaded CSV path.
     * @return int Imported users count.
     */
    public static function import_csv(int $groupid, string $csvpath): int {
        if (!is_readable($csvpath)) {
            throw new \moodle_exception('error_csv_invalid', 'local_group_monitoring');
        }

        $content = file_get_contents($csvpath);
        if ($content === false || trim($content) === '') {
            throw new \moodle_exception('error_csv_empty', 'local_group_monitoring');
        }

        $lines = preg_split('/\r\n|\r|\n/', $content);
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || \core_text::strtolower($line) === 'username') {
                continue;
            }

            $columns = str_getcsv($line);
            $username = clean_param(trim($columns[0] ?? ''), PARAM_USERNAME);
            if ($username === '') {
                continue;
            }

            try {
                $result = self::add_member($groupid, $username, 'csv');
                if ($result > 0) {
                    $count++;
                }
            } catch (\moodle_exception $e) {
                continue;
            }
        }

        return $count;
    }

    /**
     * Return allowed roles for tutor creation.
     *
     * @return array Role ID => role display name.
     */
    public static function get_roles(): array {
        global $DB;

        self::create_tutor_role();

        $roles = $DB->get_records_list('role', 'shortname', ['tutor', 'teacher', 'editingteacher']);
        $result = [];
        foreach ($roles as $role) {
            $result[$role->id] = role_get_name($role);
        }

        return $result;
    }

    /**
     * Return real polos from profile field and plugin table.
     *
     * @return array
     */
    public static function get_real_polos(): array {
        global $DB;

        self::check_and_create_polo_field();
        $fieldname = self::get_polo_field_name();

        $sql = "SELECT uid.id, uid.data
                  FROM {user_info_data} uid
                  JOIN {user_info_field} uif ON uid.fieldid = uif.id
                 WHERE uif.shortname = :fieldname
                   AND uid.data <> ''";
        $records = $DB->get_records_sql($sql, ['fieldname' => $fieldname]);
        $polos = [];

        foreach ($records as $record) {
            $value = trim($record->data);
            if ($value !== '') {
                $polos[$value] = $value;
            }
        }

        if ($DB->get_manager()->table_exists('local_group_monitoring_polos')) {
            $dbpolos = $DB->get_records('local_group_monitoring_polos');
            foreach ($dbpolos as $polo) {
                $value = trim($polo->name);
                if ($value !== '') {
                    $polos[$value] = $value;
                }
            }
        }

        $result = array_values($polos);
        sort($result);
        return $result;
    }

    /**
     * Check if role can be selected for a tutor created by the plugin.
     *
     * @param int $roleid Role ID.
     * @return bool
     */
    protected static function is_allowed_tutor_role(int $roleid): bool {
        return array_key_exists($roleid, self::get_roles());
    }

    /**
     * Enrol a user using manual enrolment, creating or enabling the instance when needed.
     *
     * @param int $courseid Course ID.
     * @param int $userid User ID.
     * @param int $roleid Role ID.
     */
    protected static function enrol_user_manually(int $courseid, int $userid, int $roleid): void {
        global $DB;

        $enrolplugin = enrol_get_plugin('manual');
        if (!$enrolplugin) {
            role_assign($roleid, $userid, \context_course::instance($courseid)->id);
            return;
        }

        $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual']);
        if (!$instance) {
            $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
            $enrolplugin->add_instance($course);
            $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual'], '*', MUST_EXIST);
        }

        if ((int)$instance->status !== ENROL_INSTANCE_ENABLED) {
            $instance->status = ENROL_INSTANCE_ENABLED;
            $DB->update_record('enrol', $instance);
        }

        $enrolplugin->enrol_user($instance, $userid, $roleid);
    }

    /**
     * Set the configured polo profile field for a user and register it in plugin table.
     *
     * @param int $userid User ID.
     * @param string $polo Polo value.
     * @param int $usermodified User ID that modified the record.
     */
    protected static function set_user_polo(int $userid, string $polo, int $usermodified): void {
        global $DB;

        self::check_and_create_polo_field();
        $fieldname = self::get_polo_field_name();
        $fieldid = $DB->get_field('user_info_field', 'id', ['shortname' => $fieldname], MUST_EXIST);

        $record = $DB->get_record('user_info_data', ['userid' => $userid, 'fieldid' => $fieldid]);
        if ($record) {
            $record->data = $polo;
            $record->dataformat = 0;
            $DB->update_record('user_info_data', $record);
        } else {
            $DB->insert_record('user_info_data', (object)[
                'userid' => $userid,
                'fieldid' => $fieldid,
                'data' => $polo,
                'dataformat' => 0,
            ]);
        }

        if ($DB->get_manager()->table_exists('local_group_monitoring_polos')
                && !$DB->record_exists('local_group_monitoring_polos', ['name' => $polo])) {
            $DB->insert_record('local_group_monitoring_polos', (object)[
                'name' => $polo,
                'timecreated' => time(),
                'timemodified' => time(),
                'usermodified' => $usermodified,
            ]);
        }
    }
}
