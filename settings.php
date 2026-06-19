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
 * Admin settings for the Group Monitoring plugin.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_group_monitoring',
        get_string('pluginnamesettings', 'local_group_monitoring')
    );
    $ADMIN->add('localplugins', $settings);

    global $DB;
    $fields = $DB->get_records_menu('user_info_field', null, 'name ASC', 'shortname, name');
    if (empty($fields)) {
        $fields = ['polo' => 'Polo'];
    }

    $settings->add(new admin_setting_configselect(
        'local_group_monitoring/polofield',
        get_string('polofield', 'local_group_monitoring'),
        get_string('polofield_desc', 'local_group_monitoring'),
        'polo',
        $fields
    ));
}
