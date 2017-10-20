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
require_once($CFG->dirroot.'/mod/skills/overview_form.php');
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
//add_to_log($course->id, 'skills', 'overview', 'overview.php?id='.$cm->id, $skills->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

// Pages
$PAGE->set_url('/mod/skills/overview.php', array('id' => $cm->id));
$PAGE->set_title(format_string($skills->name));
$PAGE->set_heading(format_string($course->fullname));

$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/mod/skills/assets/css/style.css'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/mod/skills/assets/css/collapse.css'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/form.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/bootstrap.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/jquery.maskedinput.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/jquery.maskMoney.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/masks.js'));

// Print the page header.
echo $OUTPUT->header();

// Items per page
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 0, PARAM_INT);
$perpage = $perpage ? $perpage : SKILLS_ITEMS_PER_PAGE;

// Instanciando classe do formulario
$mform  = new mod_skills_overview_form($CFG->wwwroot.'/mod/skills/overview.php?id='.$cm->id.'&year='.date("Y").'&perpage='.SKILLS_ITEMS_PER_PAGE.'&page=0');

if($mform->is_cancelled()){
	redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
	die();
}
$currentyear = $year ? $year : date("Y");
// Verificando se usuario tem acesso (Capabilities)
if (has_capability('mod/skills:submit', $context)){
	
	if ($dataform = $mform->get_data()){
		$currentyear = $dataform->currentyear;
		$objReports = skills_get_reports($page, $perpage, $dataform->organ , null, $currentyear, $skills->id);
		$objReportsCount = count(skills_get_reports(null, null, $dataform->organ, null, $currentyear, $skills->id));
		
	}else{
		$objReports = skills_get_reports($page, $perpage, null, null, $currentyear, $skills->id);
		$objReportsCount = count(skills_get_reports(null, null, null, null, $currentyear, $skills->id));
	}
	echo $mform->display(); # Filtro
} 
else{
	// Mostrando formulario
	echo $OUTPUT->heading($skills->name);
	
	// Usuario sem acesso ao filtro e dados de outros orgaos
	$objReports = skills_get_reports($page, $perpage, null, $USER->id, $currentyear, $skills->id);
	$objReportsCount = count(skills_get_reports(null, null,null, $USER->id, $currentyear, $skills->id));
	
	// verifica se o relatorio foi finalizado
	foreach ($objReports as $record){
		if(!$record->savefinish){
			echo $OUTPUT->box("
					<p>
						<strong>Prezado(a) {$record->userfullname}</strong>,</p> <p>Seu relatório ainda não foi finalizado. Uma vez finalizado, não será possível editá-lo.
						 <form method='POST' action='savefinish.php?id={$record->coursemodulesid}'>
							  <input type='hidden' name='dtfid' value={$record->id}>
							  <input type='hidden' name='confirm' value='1'>
							  <input type='submit' value='Finalizar relatório'>
						</form> 
					</p> ", "alert alert-warning");
		}
	}
}

// Criando GRID de orgaos
$table = new html_table();
$table->width = "100%";
$table->tablealign = "center";
$table->head  = array('Responsável', get_string('organ', 'skills') , get_string('qtdeservers', 'skills'), "Orçamento Treinamento ".($currentyear - 1), "Despesa Realizada", "Orçamento ".$currentyear, "Data de Envio", "Ações");
$table->align = array("left", "left", "center", "center", "center", "center", "center", "center");
$organs = skills_generate_array_organs();
foreach ($objReports as $objReport){

	$userfullname = "<a href=\"{$CFG->wwwroot}/user/view.php?id=".$objReport->userid." \" target='_black'>".
			$objReport->userfullname
			."</a>";
	
	$organ = $organs[$objReport->organ];
	$qtdeservers = $objReport->qtdeservers;
	#$hourclassserver = $objReport->hourclassserver;
	$budgettraining = $objReport->budgettraining;
	$runvalue = $objReport->runvalue;
	$budgetnextyear = $objReport->budgetnextyear;
	$createdate = date('d/m/Y', $objReport->createdate);
	
	// link details
	$lkdetails = "<a href='{$CFG->wwwroot}/mod/skills/overview_detail.php?id={$cm->id}&dtfid={$objReport->id}'>".
			" <img src=\"" . $OUTPUT->pix_url('a/view_list_active') . "\" alt=\"Detalhes\" title='Detalhamento' />";
	"</a>";
	
	// link file
	$fileplannextyear = skills_getfileplannextyear($context, $objReport);
	$lkdown = false;
	if($fileplannextyear){
		$lkdown = "<a href='{$fileplannextyear->fullurl}'>".
				" <img src=\"" . $OUTPUT->pix_url(file_mimetype_icon($fileplannextyear->mimetype))->out() . "\" alt=\"Baixar anexo \" title='Baixar anexo' />";
		"</a>";
	}
	
	// Anexar file
	/*$attachfile = "<a href='#'>".
			" <img src=\"" . $OUTPUT->pix_url('a/add_file') . "\" alt=\"Anexar\" title='Anexar planejamento' />";
	"</a>";*/
	
	$lkunlock = null;
	$sendfullreport = null;
	if(has_capability('mod/skills:submit', $context)){
		
		if($objReport->savefinish){
			$lkunlock = "<a href='{$CFG->wwwroot}/mod/skills/unlock.php?id={$cm->id}&dtfid={$objReport->id}&confirm=1'>".
					" <img src=\"" . $OUTPUT->pix_url('t/lock') . "\" alt=\"Liberar relatório\" title='Liberar relatório' />";
			"</a>";
		}
		
		if($objReport->beinfullreport){
			$sendfullreport = "<a href='{$CFG->wwwroot}/mod/skills/alterbefullreport.php?id={$cm->id}&dtfid={$objReport->id}&confirm=1&p=1'>".
					" <img src=\"" . $OUTPUT->pix_url('i/hide') . "\" alt=\"Hide\" title='Não contabilizar na Visão Completa' />";
			"</a>";
		}else{
			$sendfullreport = "<a href='{$CFG->wwwroot}/mod/skills/alterbefullreport.php?id={$cm->id}&dtfid={$objReport->id}&confirm=1&p=0'>".
					" <img src=\"" . $OUTPUT->pix_url('i/show') . "\" alt=\"Show\" title='Contabilizar na Visão Completa' />";
			"</a>";
		}
	}
	// link edit
	$lkedit = null;
	if(!$objReport->savefinish){
		$lkedit = "<a href='{$CFG->wwwroot}/mod/skills/edit.php?id={$cm->id}&dtfid={$objReport->id}'>".
				" <img src=\"" . $OUTPUT->pix_url('i/edit') . "\" alt=\"Editar\" title='Editar relatorio' />";
		"</a>";
	}
	$table->data[] = array($userfullname, $organ, $qtdeservers, $budgettraining, $runvalue, $budgetnextyear, $createdate, $lkdetails. $lkdown. $lkedit. $lkunlock. $sendfullreport);
}

$baseurl = new moodle_url('/mod/skills/overview.php', array('id'=>$cm->id,'year'=>$currentyear, 'perpage' => $perpage));
echo $OUTPUT->paging_bar($objReportsCount, $page, $perpage, $baseurl);
echo '<br />';
echo html_writer::table($table);

$strAllRecords = get_string('allrecords', 'skills');
$strRecords = get_string('records', 'skills');
echo "<div style='width: 100%; margin: 0 auto; text-align: right;'><a href='#' title='Total de registros'> Total de Registros: {$objReportsCount}</a></div>";

// Finish the page.
echo $OUTPUT->footer();
