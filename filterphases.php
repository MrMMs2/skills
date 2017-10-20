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
 * @copyright  2015 Leo Santos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace skills with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/mod/skills/filterphases_form.php');
$id 	= optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  	= optional_param('n', 0, PARAM_INT);  // ... skills instance ID - it should be named as the first character of the module.
$year  	= optional_param('year', 0, PARAM_INT);

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

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

// Pages
$PAGE->set_url('/mod/skills/filterphases.php', array('id' => $cm->id));
$PAGE->set_title(format_string($skills->name));
$PAGE->set_heading(format_string($course->fullname));

$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/mod/skills/assets/css/style.css'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/mod/skills/assets/css/collapse.css'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/form.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/bootstrap.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/jquery.maskedinput.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/jquery.maskMoney.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/masks.js'));

// Items per page
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 0, PARAM_INT);
$perpage = $perpage ? $perpage : SKILLS_ITEMS_PER_PAGE;

// Instanciando classe do formulario
$mform  = new mod_skills_filterphases_form($CFG->wwwroot.'/mod/skills/filterphases.php?id='.$cm->id.'&perpage='.SKILLS_ITEMS_PER_PAGE.'&page=0');

if($mform->is_cancelled()){
	redirect($CFG->wwwroot.'/mod/skills/filterphases.php?id='.$cm->id);
	die();
}
// Print the page header.
echo $OUTPUT->header();
// Verificando se usuario tem acesso (Capabilities)
if (has_capability('mod/skills:submit', $context)){
	
	echo $mform->display(); # Filtro
	
	if ($dataform = $mform->get_data()){
		$objReports = skills_get_reports_by_justicebranch($skills->id, $dataform->justicebranch, $page, $perpage);
		$allReports = skills_get_reports_by_justicebranch($skills->id, $dataform->justicebranch);
		$filteredreports = skills_set_filter_to_phasestageprogramskills($objReports, $dataform);
		$countfilteredreports = count(skills_set_filter_to_phasestageprogramskills($allReports, $dataform));
		
		// Monta tabela
		$currentyear = date("Y");
		// Criando GRID de orgaos
		$table = new html_table();
		$table->width = "100%";
		$table->tablealign = "center";
		$table->head  = array('Responsável', get_string('organ', 'skills') , get_string('qtdeservers', 'skills'), "Orçamento Treinamento ".($currentyear - 1), "Despesa Realizada", "Orçamento ".$currentyear, "Data de Envio", "#");
		$table->align = array("left", "left", "center", "center", "center", "center", "center", "center");
		$organs = skills_generate_array_organs();
		foreach ($filteredreports as $objReport){
		
			$userfullname = "<a href=\"{$CFG->wwwroot}/user/view.php?id=".$objReport->userid." \" target='_black'>".
			$objReport->userfullname
			."</a>";

			$organ = $organs[$objReport->organ];
			$qtdeservers = $objReport->qtdeservers;
			$budgettraining = $objReport->budgettraining;
			$runvalue = $objReport->runvalue;
			$budgetnextyear = $objReport->budgetnextyear;
			$createdate = date('d/m/Y', $objReport->createdate);

			// link details
			$lkdetails = "<a href='{$CFG->wwwroot}/mod/skills/overview_detail.php?id={$cm->id}&dtfid={$objReport->id}' target='blank'>".
					" <img src=\"" . $OUTPUT->pix_url('a/view_list_active') . "\" alt=\"Detalhes\" title='Detalhamento' />";
			"</a>";
			$table->data[] = array($userfullname, $organ, $qtdeservers, $budgettraining, $runvalue, $budgetnextyear, $createdate, $lkdetails);
		}
		
		$baseurl = new moodle_url('/mod/skills/filterphases.php', array('id'=>$cm->id, 'perpage' => $perpage));
		echo $OUTPUT->paging_bar($countfilteredreports, $page, $perpage, $baseurl);
		echo PHP_EOL;
		echo html_writer::table($table);
		
		$strAllRecords = get_string('allrecords', 'skills');
		$strRecords = get_string('records', 'skills');
		echo "<div style='width: 100%; margin: 0 auto; text-align: right;'><a href='#' title='Total de registros'> Total de Registros: {$countfilteredreports}</a></div>";
	}
}
else{
	 error('Acesso negado!');
}
// Finish the page.
echo $OUTPUT->footer();
