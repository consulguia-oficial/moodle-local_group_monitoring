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
 * Privacy API provider for the Group Monitoring plugin.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @author     Consulguia Tech
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_group_monitoring\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy API provider for local_group_monitoring.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Describe the personal data stored by this plugin.
     *
     * @param collection $collection The metadata collection.
     * @return collection The updated metadata collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'local_group_monitoring_groups',
            [
                'name' => 'privacy:metadata:local_group_monitoring_groups:name',
                'courseid' => 'privacy:metadata:local_group_monitoring_groups:courseid',
                'polo' => 'privacy:metadata:local_group_monitoring_groups:polo',
                'tutorid' => 'privacy:metadata:local_group_monitoring_groups:tutorid',
                'moodle_groupid' => 'privacy:metadata:local_group_monitoring_groups:moodle_groupid',
                'timecreated' => 'privacy:metadata:local_group_monitoring_groups:timecreated',
                'timemodified' => 'privacy:metadata:local_group_monitoring_groups:timemodified',
                'usermodified' => 'privacy:metadata:local_group_monitoring_groups:usermodified',
            ],
            'privacy:metadata:local_group_monitoring_groups'
        );

        $collection->add_database_table(
            'local_group_monitoring_members',
            [
                'groupid' => 'privacy:metadata:local_group_monitoring_members:groupid',
                'userid' => 'privacy:metadata:local_group_monitoring_members:userid',
                'username' => 'privacy:metadata:local_group_monitoring_members:username',
                'added_via' => 'privacy:metadata:local_group_monitoring_members:added_via',
                'timecreated' => 'privacy:metadata:local_group_monitoring_members:timecreated',
                'usermodified' => 'privacy:metadata:local_group_monitoring_members:usermodified',
            ],
            'privacy:metadata:local_group_monitoring_members'
        );

        $collection->add_database_table(
            'local_group_monitoring_polos',
            [
                'name' => 'privacy:metadata:local_group_monitoring_polos:name',
                'timecreated' => 'privacy:metadata:local_group_monitoring_polos:timecreated',
                'timemodified' => 'privacy:metadata:local_group_monitoring_polos:timemodified',
                'usermodified' => 'privacy:metadata:local_group_monitoring_polos:usermodified',
            ],
            'privacy:metadata:local_group_monitoring_polos'
        );

        return $collection;
    }

    /**
     * Get the contexts that contain personal data for a user.
     *
     * @param int $userid The user ID.
     * @return contextlist The list of contexts.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {local_group_monitoring_groups} g
                    ON g.courseid = ctx.instanceid
             LEFT JOIN {local_group_monitoring_members} m
                    ON m.groupid = g.id
                 WHERE ctx.contextlevel = :contextcourse
                   AND (
                        g.tutorid = :tutorid
                        OR g.usermodified = :groupusermodified
                        OR m.userid = :memberuserid
                        OR m.usermodified = :memberusermodified
                   )";

        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'tutorid' => $userid,
            'groupusermodified' => $userid,
            'memberuserid' => $userid,
            'memberusermodified' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export personal data for the approved contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_COURSE) {
                continue;
            }

            $data = new \stdClass();
            $data->groups_as_tutor = [];
            $data->groups_modified = [];
            $data->memberships = [];
            $data->member_records_modified = [];
            $data->polos_modified = [];

            $groupsastutor = $DB->get_records('local_group_monitoring_groups', [
                'courseid' => $context->instanceid,
                'tutorid' => $userid,
            ]);
            foreach ($groupsastutor as $group) {
                $data->groups_as_tutor[] = self::format_group_record($group);
            }

            $groupsmodified = $DB->get_records('local_group_monitoring_groups', [
                'courseid' => $context->instanceid,
                'usermodified' => $userid,
            ]);
            foreach ($groupsmodified as $group) {
                $data->groups_modified[] = self::format_group_record($group);
            }

            $sql = "SELECT m.*
                      FROM {local_group_monitoring_members} m
                      JOIN {local_group_monitoring_groups} g ON g.id = m.groupid
                     WHERE g.courseid = :courseid
                       AND m.userid = :userid
                  ORDER BY m.timecreated ASC";
            $memberships = $DB->get_records_sql($sql, [
                'courseid' => $context->instanceid,
                'userid' => $userid,
            ]);
            foreach ($memberships as $member) {
                $data->memberships[] = self::format_member_record($member);
            }

            $sql = "SELECT m.*
                      FROM {local_group_monitoring_members} m
                      JOIN {local_group_monitoring_groups} g ON g.id = m.groupid
                     WHERE g.courseid = :courseid
                       AND m.usermodified = :userid
                  ORDER BY m.timecreated ASC";
            $modifiedmembers = $DB->get_records_sql($sql, [
                'courseid' => $context->instanceid,
                'userid' => $userid,
            ]);
            foreach ($modifiedmembers as $member) {
                $data->member_records_modified[] = self::format_member_record($member);
            }

            $polosmodified = $DB->get_records('local_group_monitoring_polos', ['usermodified' => $userid]);
            foreach ($polosmodified as $polo) {
                $data->polos_modified[] = self::format_polo_record($polo);
            }

            writer::with_context($context)->export_data([
                get_string('pluginname', 'local_group_monitoring'),
            ], $data);
        }
    }

    /**
     * Delete all personal data for all users in a context.
     *
     * @param \context $context The context.
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_COURSE) {
            return;
        }

        $groups = $DB->get_records('local_group_monitoring_groups', ['courseid' => $context->instanceid], '', 'id');
        if (empty($groups)) {
            return;
        }

        list($groupsql, $params) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $DB->delete_records_select('local_group_monitoring_members', "groupid {$groupsql}", $params);
        $DB->delete_records('local_group_monitoring_groups', ['courseid' => $context->instanceid]);
    }

    /**
     * Delete personal data for a user in approved contexts.
     *
     * @param approved_contextlist $contextlist The approved context list.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        self::delete_user_data($contextlist->get_user()->id, $contextlist->get_contexts());
    }

    /**
     * Get users who have personal data in a context.
     *
     * @param userlist $userlist The userlist.
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();

        if ($context->contextlevel !== CONTEXT_COURSE) {
            return;
        }

        $sql = "SELECT g.tutorid AS userid
                  FROM {local_group_monitoring_groups} g
                 WHERE g.courseid = :courseid1
                   AND g.tutorid <> 0
                 UNION
                SELECT g.usermodified AS userid
                  FROM {local_group_monitoring_groups} g
                 WHERE g.courseid = :courseid2
                   AND g.usermodified <> 0
                 UNION
                SELECT m.userid AS userid
                  FROM {local_group_monitoring_members} m
                  JOIN {local_group_monitoring_groups} g ON g.id = m.groupid
                 WHERE g.courseid = :courseid3
                   AND m.userid IS NOT NULL
                   AND m.userid <> 0
                 UNION
                SELECT m.usermodified AS userid
                  FROM {local_group_monitoring_members} m
                  JOIN {local_group_monitoring_groups} g ON g.id = m.groupid
                 WHERE g.courseid = :courseid4
                   AND m.usermodified <> 0";

        $params = [
            'courseid1' => $context->instanceid,
            'courseid2' => $context->instanceid,
            'courseid3' => $context->instanceid,
            'courseid4' => $context->instanceid,
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete data for multiple approved users in a context.
     *
     * @param approved_userlist $userlist The approved userlist.
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        self::delete_user_data($userlist->get_userids(), [$userlist->get_context()]);
    }

    /**
     * Delete or anonymise user data in plugin tables.
     *
     * @param int|array $userids The user ID or user IDs.
     * @param array $contexts The contexts.
     */
    protected static function delete_user_data($userids, array $contexts): void {
        global $DB;

        $userids = (array)$userids;
        if (empty($userids)) {
            return;
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'usr');

        foreach ($contexts as $context) {
            if ($context->contextlevel !== CONTEXT_COURSE) {
                continue;
            }

            $groups = $DB->get_records('local_group_monitoring_groups', ['courseid' => $context->instanceid], '', 'id');
            if (empty($groups)) {
                continue;
            }

            list($groupsql, $groupparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED, 'grp');

            $params = array_merge($groupparams, $userparams);
            $DB->delete_records_select('local_group_monitoring_members', "groupid {$groupsql} AND userid {$usersql}", $params);

            $params = array_merge($groupparams, $userparams);
            $DB->set_field_select('local_group_monitoring_members', 'usermodified', 0, "groupid {$groupsql} AND usermodified {$usersql}", $params);

            $params = array_merge(['courseid' => $context->instanceid], $userparams);
            $DB->set_field_select('local_group_monitoring_groups', 'tutorid', 0, "courseid = :courseid AND tutorid {$usersql}", $params);

            $params = array_merge(['courseid' => $context->instanceid], $userparams);
            $DB->set_field_select('local_group_monitoring_groups', 'usermodified', 0, "courseid = :courseid AND usermodified {$usersql}", $params);
        }

        $DB->set_field_select('local_group_monitoring_polos', 'usermodified', 0, "usermodified {$usersql}", $userparams);
    }

    /**
     * Format a group record for export.
     *
     * @param \stdClass $record Group record.
     * @return \stdClass Exportable record.
     */
    protected static function format_group_record(\stdClass $record): \stdClass {
        return (object)[
            'name' => $record->name,
            'courseid' => $record->courseid,
            'polo' => $record->polo,
            'tutorid' => $record->tutorid,
            'moodle_groupid' => $record->moodle_groupid,
            'timecreated' => transform::datetime($record->timecreated),
            'timemodified' => transform::datetime($record->timemodified),
            'usermodified' => $record->usermodified,
        ];
    }

    /**
     * Format a member record for export.
     *
     * @param \stdClass $record Member record.
     * @return \stdClass Exportable record.
     */
    protected static function format_member_record(\stdClass $record): \stdClass {
        return (object)[
            'groupid' => $record->groupid,
            'userid' => $record->userid,
            'username' => $record->username,
            'added_via' => $record->added_via,
            'timecreated' => transform::datetime($record->timecreated),
            'usermodified' => $record->usermodified,
        ];
    }

    /**
     * Format a polo record for export.
     *
     * @param \stdClass $record Polo record.
     * @return \stdClass Exportable record.
     */
    protected static function format_polo_record(\stdClass $record): \stdClass {
        return (object)[
            'name' => $record->name,
            'timecreated' => transform::datetime($record->timecreated),
            'timemodified' => transform::datetime($record->timemodified),
            'usermodified' => $record->usermodified,
        ];
    }
}
