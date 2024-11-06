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

//declare(strict_types=1); Verificar se é necessário.

namespace mod_quiz\local\entities;

use core_reportbuilder\local\filters\{date, duration, number, text};
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\format;
use lang_string;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Quiz entity class implementation quiz
 *
 * This entity defines all the quiz columns and filters to be used in any report.
 *
 * @package     mod_quiz
 * @copyright   TODO
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 class quiz extends base {

     /** @var array */
     private $acronyms = [];

     /**
      * Database tables that this entity uses
      *
      * @return string[]
      */
     protected function get_default_tables(): array {
         return ['quiz'];
     }
     
     /**
      * the default title for this entity
      *
      * @return lagn_string
      */

     protected function get_default_entity_title(): lang_string {
         return new lang_string('quizreport', 'mod_quiz');
     }

     /**
     * Initialise the entity, add all quiz fields
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
     * Return list of all available columns
     *
     * These are all columns available to use in report that use this entity.
     *
     * @return column[]
     */

    protected function get_all_columns(): array {

       $columns = [];

       $quizalias = $this->get_table_alias('quiz');
       //$quizattemptsalias = $this->get_table_alias('quiz_attempts'); 

       // $join = $this->quizjoin();
       //$quizattemptsjoin = $this->quizattempsjoin();

       //Quiz name column.
       $columns[] = (new column(
           'name',
           new lang_string('name', 'mod_quiz'),
           $this->get_entity_name()
       ))
          // ->add_join($join)
           ->set_is_sortable(true)
           ->add_field("{$quizalias}.name");

       //Quiz timeopen column.
       $columns[] = (new column(
           'timeopen',
           new lang_string('timeopen', 'mod_quiz'),
           $this->get_entity_name()
       ))
          //->add_join($join)
          ->set_type(column::TYPE_TIMESTAMP)
          ->set_is_sortable(true)
          ->add_field("{$quizalias}.timeopen")
          ->add_callback([format::class, 'userdate']);

       //Handle quiz attempts columns.

       //Attempts column.
       //$columns[] = (new column(
       //    'state',
       //    new lang_string('state', 'mod_quiz'),
       //    $this->get_entity_name()
       //))
       //    ->add_join($quizattemptsjoin)
       //    ->set_sortable(true)
       //    ->add_field("{$quizattemptsalias}.state");

      return $columns;
    }
       
    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {

       $filters = [];
       $quizalias = $this->get_table_alias('quiz');
      // $quizattemptsalias = $this->get_table_alias('quiz_attempts'); 

      // $join = $this->quizjoin();
      // $quizattemptsjoin = $this->quizattempsjoin();

       //Quiz name filter.
       $filters[] = (new filter(
           text::class,
           'nameselector',
           new lang_string('name', 'mod_quiz'),
           $this->get_entity_name(),
           "{$quizalias}.name"
       ));
          // ->add_join($join);

      return $filters;
    }
 }
