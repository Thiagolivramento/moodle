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

namespace mod_assign\local\entities;

use core_reportbuilder\local\filters\{date, duration, number, text};
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\format;
use lang_string;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Assign submission entity class implementation quiz
 *
 * This entity defines all the assign submission columns and filters to be use in any report.
 *
 * @package mod_assign
 * @copyright Thiago Livramento <thiago@adapta.online>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return ['assign_submission'];
    }
}
