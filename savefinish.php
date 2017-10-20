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
 * Prints a particular instance of skills
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_skills
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace skills with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/mod/skills/edit_report_form.php');
$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... skills instance ID - it should be named as the first character of the module.
$dtfid = optional_param('dtfid', 0, PARAM_INT); // Page ID
$confirm = optional_param('confirm', 0, PARAM_INT); // Page ID

if ($id) {
    $cm         = get_coursemodule_from_id('skills', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $skills  = $DB->get_record('skills', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $skills  = $DB->get_record('skills', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $skills->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('skills', $skills->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
//add_to_log($course->id, 'skills', 'view', 'view.php?id='.$cm->id, $skills->id, $cm->id);

// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

// alterando flag savefinish
if($_POST['confirm']){
	$data = new stdClass;
	$data->id = $_POST['dtfid'];
	$data->savefinish = 1;
	skills_save('skills_dataform', $data);

	$message = "Prezado(a) ".skills_get_serFullName().", Relatório finalizado com sucesso! A partir desse momento, não será possível editá-lo.";
	redirect($CFG->wwwroot.'/mod/skills/overview.php?id='.$cm->id.'&perpage=30&page=0', $message, 5);
}