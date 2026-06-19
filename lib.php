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
 * Library callbacks for the Group Monitoring plugin.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend course navigation with the Group Monitoring dashboard link.
 *
 * @param navigation_node $navigation The course navigation node.
 * @param stdClass $course Course record.
 * @param context_course $context Course context.
 */
function local_group_monitoring_extend_navigation_course($navigation, $course, $context): void {
    if (!has_capability('local/group_monitoring:view', $context)) {
        return;
    }

    $url = new moodle_url('/local/group_monitoring/index.php', ['courseid' => $course->id]);
    $navigation->add(
        get_string('dashboard', 'local_group_monitoring'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'local_group_monitoring',
        new pix_icon('i/group', '')
    );
}
