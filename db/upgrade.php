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
 * Upgrade steps for the Group Monitoring plugin.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute plugin upgrade steps.
 *
 * @param int $oldversion Installed version.
 * @return bool
 */
function xmldb_local_group_monitoring_upgrade($oldversion): bool {
    global $CFG;

    require_once($CFG->dirroot . '/local/group_monitoring/classes/local/group_manager.php');

    if ($oldversion < 2026050505) {
        \local_group_monitoring\local\group_manager::check_and_create_polo_field();
        upgrade_plugin_savepoint(true, 2026050505, 'local', 'group_monitoring');
    }

    return true;
}
