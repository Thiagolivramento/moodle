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

declare(strict_types=1);

namespace mod_quiz\reportbuilder\datasource;

use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\course;
use core_course\reportbuilder\local\entities\course_category;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\helpers\database;

/**
 * Quiz datasource
 *
 * @package  mod_quiz
 * @copyright TODO
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 class quiz extends datasource {


     /**
      * Return user friendly name of the datasource
      *
      * @return string
      */
     
     public static function get_name(): string {
         return get_string('quizreport', 'mod_quiz');
     }

     protected function initialise(): void {
         global $CFG;
         require_once($CFG->dirroot.'/mod/quiz/locallib.php');

         $quizentity = new \mod_quiz\local\entities\quiz();
         $quizalias = $quizentity->get_table_alias('quiz');


