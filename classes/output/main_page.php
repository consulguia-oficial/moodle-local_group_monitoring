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
 * Main page renderable.
 *
 * @package    local_group_monitoring
 * @copyright  2026 Consulguia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_group_monitoring\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

/**
 * Main page renderable.
 */
class main_page implements renderable, templatable {
    /** @var int Course ID. */
    protected $courseid;

    /** @var string Current tab. */
    protected $tab;

    /** @var \stdClass Metrics. */
    protected $metrics;

    /** @var \stdClass Course record. */
    protected $course;

    /** @var array Group records. */
    protected $groups;

    /** @var array Polo records. */
    protected $polos;

    /** @var array Tutor records. */
    protected $tutors;

    /** @var array Role records. */
    protected $roles;

    /** @var bool Whether current user can create groups. */
    protected $cancreategroup;

    /** @var bool Whether current user can add members. */
    protected $canaddmember;

    /** @var bool Whether current user can create tutors. */
    protected $cancreatetutor;

    /** @var bool Whether current user can manage polos. */
    protected $canmanagepolo;

    /**
     * Constructor.
     *
     * @param int $courseid Course ID.
     * @param string $tab Current tab.
     * @param \stdClass $metrics Metrics.
     * @param \stdClass $course Course record.
     * @param array $groups Group records.
     * @param array $polos Polo list.
     * @param array $tutors Tutor list.
     * @param array $roles Role list.
     * @param bool $cancreategroup Can create groups.
     * @param bool $canaddmember Can add members.
     * @param bool $cancreatetutor Can create tutors.
     * @param bool $canmanagepolo Can manage polos.
     */
    public function __construct(
        $courseid,
        $tab,
        $metrics,
        $course,
        $groups,
        $polos,
        $tutors,
        $roles,
        $cancreategroup = false,
        $canaddmember = false,
        $cancreatetutor = false,
        $canmanagepolo = false
    ) {
        $this->courseid = $courseid;
        $this->tab = $tab;
        $this->metrics = $metrics;
        $this->course = $course;
        $this->groups = $groups;
        $this->polos = $polos;
        $this->tutors = $tutors;
        $this->roles = $roles;
        $this->cancreategroup = $cancreategroup;
        $this->canaddmember = $canaddmember;
        $this->cancreatetutor = $cancreatetutor;
        $this->canmanagepolo = $canmanagepolo;
    }

    /**
     * Export data for template.
     *
     * @param renderer_base $output Renderer.
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new \stdClass();
        $data->courseid = $this->courseid;
        $data->sesskey = sesskey();
        $data->coursename = $this->course->fullname;
        $data->courseshortname = $this->course->shortname;
        $data->tab_dashboard = ($this->tab === 'dashboard');
        $data->tab_courses = ($this->tab === 'courses');
        $data->tab_groups = ($this->tab === 'groups');
        $data->tab_polos = ($this->tab === 'polos');
        $data->tab_tutors = ($this->tab === 'tutors');
        $data->totalcourses = $this->metrics->courses;
        $data->totalgroups = $this->metrics->groups;
        $data->totalpolos = $this->metrics->polos;
        $data->totaltutors = $this->metrics->tutors;
        $data->cancreategroup = $this->cancreategroup;
        $data->canaddmember = $this->canaddmember;
        $data->cancreatetutor = $this->cancreatetutor;
        $data->canmanagepolo = $this->canmanagepolo;

        $data->list_groups = [];
        foreach ($this->groups as $group) {
            $groupdata = (array)$group;
            $groupdata['has_members'] = !empty($group->members);
            $groupdata['canaddmember'] = $this->canaddmember;
            $data->list_groups[] = $groupdata;
        }

        $data->list_tutors = array_values((array)$this->tutors);
        $data->list_polos = [];
        foreach ($this->polos as $polo) {
            $data->list_polos[] = ['name' => $polo];
        }

        $data->list_roles = [];
        foreach ($this->roles as $id => $name) {
            $data->list_roles[] = ['id' => $id, 'name' => $name];
        }

        return $data;
    }
}
