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

use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;
use core_question_generator;
use mod_quiz_generator;
use question_engine;
use mod_quiz\quiz_settings;

/**
 * Unit tests for quiz datasource
 *
 * @package     mod_quiz
 * @covers      \mod_quiz\reportbuilder\datasource\quiz
 * @copyright   2025 Thiago Livramento <thiago@adapta.online>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class quiz_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        global $DB;
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->assertNotEmpty($studentrole);
        $this->assertTrue(enrol_try_internal_enrol($course->id, $user1->id, $studentrole->id));
        
        $usertimes = [];

        /** @var mod_quiz_generator $quizgenerator */
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        // Basic quiz settings

        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'timeclose' => 1200, 'timelimit' => 600]);
        $attemptid = $DB->insert_record('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $user1->id, 'state' => 'inprogress',
                'timestart' => 100, 'timecheckstate' => 0, 'layout' => '', 'uniqueid' => $this->usage_id($quiz)]);
        $usertimes[$attemptid] = ['timeclose' => 1200, 'timelimit' => 600, 'message' => 'Test1A', 'time1000state' => 'finished'];
        
        $attemptid = $DB->insert_record('quiz_attempts', ['quiz' => $quiz->id, 'userid' => $user2->id, 'state' => 'inprogress',
                'timestart' => 100, 'timecheckstate' => 0, 'layout' => '', 'uniqueid' => $this->usage_id($quiz)]);
        $usertimes[$attemptid] = ['timeclose' => 1200, 'timelimit' => 600, 'message' => 'Test1A', 'time1000state' => 'finished'];
        
        // Compute expected end time for each attempt.
        foreach ($usertimes as $attemptid => $times) {
            $attempt = $DB->get_record('quiz_attempts', ['id' => $attemptid], '*', MUST_EXIST);

            if ($times['timeclose'] > 0 && $times['timelimit'] > 0) {
                $usertimes[$attemptid]['timedue'] = min($times['timeclose'], $attempt->timestart + $times['timelimit']);
            } else if ($times['timeclose'] > 0) {
                $usertimes[$attemptid]['timedue'] = $times['timeclose'];
            } else if ($times['timelimit'] > 0) {
                $usertimes[$attemptid]['timedue'] = $attempt->timestart + $times['timelimit'];
            }
        }
        quiz_update_open_attempts(['courseid' => $course->id]);
        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Quiz', 'source' => quiz::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns .
        $courseoneurl = course_get_url($course);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
            $quizurl = new \moodle_url('/mod/quiz/view.php', ['id'=> $cm->id]);

        $this->assertEquals([
            ["<a href=\"{$courseoneurl}\">{$course->fullname}</a>",
             "<a href=\"{$quizurl}\">{$quiz->name}</a>"],
        ], array_map('array_values', $content));
    }
    
    /**
     * Make any old question usage for a quiz.
     *
     * The attempts used in test_bulk_update_functions must have some
     * question usage to store in uniqueid, but they don't have to be
     * very realistic.
     *
     * @param \stdClass $quiz
     * @return int question usage id.
     */
    protected function usage_id(\stdClass $quiz): int {
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz',
                \context_module::instance($quiz->cmid));
        $quba->set_preferred_behaviour('deferredfeedback');
        question_engine::save_questions_usage_by_activity($quba);
        return $quba->get_id();
    }
}
