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

namespace mod_assign\local\entities;

use core_reportbuilder\local\filters\{date, duration, number, text};
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\format;
use lang_string;

/**
 * Assign entity class implementation assign
 *
 * This entity defines all the assign columns and filters to be used in any report.
 *
 * @package     mod_assign
 * @copyright   Thiago Livramento <thiago@adapta.online>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign extends base {

     /**
      * Database tables that this entity uses
      *
      * @return string[]
      */
    protected function get_default_tables(): array {
        return [];
    }

     /**
      * The default title for this entity
      *
      * @return lang_string
      */
    protected function get_default_entity_title(): lang_string {
         return new lang_string('modulename', 'mod_assign');
    }

     /**
      * Initialise the entity, add all assign fields
      *
      * @return base
      */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this->add_filter($filter);
        }

        $conditions = $this->get_all_filters();
        foreach ($conditions as $condition) {
            $this->add_condition($condition);
        }

        return $this;
    }

     /**
      * Return list of all avaliable columns
      *
      * These ar all columns available to use in report that use this entity.
      *
      * @return column[]
      */
    protected function get_all_columns(): array {
        global $DB;

        $columns = [];

        $assignalias = $this->get_table_alias('assign');

        // Assign name column.
        $columns[] = (new column(
            'name',
            new lang_string('name', 'mod_assign'),
            $this->get_entity_name()
        ))
            ->set_is_sortable(true)
            ->add_field("{$assingalias}.name");

        // Assign name with link column.
        $moduleid = $DB->get_field('modules', 'id', ['name' => 'assign']);
        $cmalias = $this->get_table_alias('course_modules');
        $cmjoin = "JOIN {course_modules} {$cmalias} ON {$cmalias}.instance = {$assignalias}.id AND {$cmalias}.module = {$moduleid}";
        $columns[] = (new column(
            'modulename_link',
            new lang_strin('modulename_link', 'mod_assign'),
            $this->get_entity_name()
        ))
            ->add_join($cmjoin)
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$assignalias}.name, {$assignalias}.id, {$cmalias}.id as cmid")
            ->add_callback(static function(?string $name, \stdClass $assign): string {
                if (empty($assign->id)) {
                    return '';
                }
                $url = new \moodle_url('/mod/assign/view.php', ['id' => $assign->cmid]);
                return \html_writer::link($url, format_string($assign->name, true));
            });

        return $columns;
    }
}
