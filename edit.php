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

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);
// Pages
$PAGE->set_url('/mod/skills/edit.php', array('id' => $cm->id));
$PAGE->set_title(format_string($skills->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/mod/skills/assets/css/style.css'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/mod/skills/assets/css/collapse.css'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/form.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/formedit.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/bootstrap.min.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/jquery.maskedinput.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/jquery.maskMoney.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/mod/skills/assets/js/masks.js'));
// Output starts here.

// Print the page header.
echo $OUTPUT->header();

if($dtfid){
	$dataeditform = $DB->get_record('skills_dataform', array('id'=>$dtfid, 'skillsid'=>$skills->id), '*', MUST_EXIST);
	
	$mform  = new mod_skills_edit_report_form($CFG->wwwroot.'/mod/skills/edit.php?id='.$cm->id.'&dtfid='.$dtfid, array('dataform'=>$dataeditform));
	$mform->set_data($dataeditform);
}else{
	redirect($CFG->wwwroot.'/mod/skills/view.php?id='.$cm->id);
}

if($mform->is_cancelled()){
	redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
}else if ($dataform = $mform->get_data()){
	
	$dataform->id = $dtfid;
	
	// Transformando array de dados em strings ou json
	$dataform->methodology = implode("; ", $dataform->methodology);
	$dataform->formativeactionsyearprevious = implode("; ", $dataform->formativeactionsyearprevious);
	$dataform->stageprogramskills = implode("; ", $dataform->stageprogramskills);
	$dataform->phasestageprogramskills = json_encode($dataform->phasestageprogramskills);
	$dataform->typeevaluation = implode("; ", $dataform->typeevaluation);
	$dataform->formativeactionsyearcurrent = implode("; ", $dataform->formativeactionsyearcurrent);
	$dataform->structuretraining = json_encode($dataform->structuretraining);
	$dataform->runvalbyformaction = json_encode($dataform->runvalbyformaction);
	$dataform->othersvalrun = json_encode($dataform->othersvalrun);
	$dataform->trainingactionsociety = $dataform->trainingactionsociety === 'yes' ? json_encode($dataform->dtcapsociedade) : $dataform->trainingactionsociety;
	
	// Recebendo dados das matrizes
	$lowercourse360hours = $dataform->lowercourse360hours;
	$trainings = $dataform->trainings;
	
	// Removendo propriedades do objeto que ja foram utilizadas
	unset($dataform->lowercourse360hours);
	unset($dataform->trainings);
	unset($dataform->fullname);
	unset($dataform->dtcapsociedade);
	unset($dataform->showorgan);
	
	// Registrando dados no banco
	if(skills_save('skills_dataform', $dataform)){
		
		// atualizando dataform_areas
		if(skills_delete_dataform_areas($dataform->id)){
			// Salvando novas areas
			skills_set_skills_dataform_areas($lowercourse360hours, $dataform->id);
		}
		
		// atualizando dataform_areas
		if(skills_delete_dataform_trainings($dataform->id)){
			// Salvando novos treinamentos
			skills_set_skills_dataform_training($trainings, $dataform->id);
		}
		// Salvando arquivo anexo
		file_save_draft_area_files($dataform->fileplannextyear, $context->id, 'mod_skills', 'fileplannextyear',
		$dataform->id, array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
		
		// Confirmacao de fechamento do formulario
		if(property_exists ($dataform, 'submitclosebutton')){
			echo $OUTPUT->heading("Confirmação");
			echo $OUTPUT->confirm("Prezado(a), se o relatório for finalizado, você não terá acesso à área de edição dele. Tem certeza de que deseja finalizar o envio do Relatório?", "savefinish.php?id=".$cm->id."&dtfid=".$dtfid."&confirm=1", "overview.php?id=".$cm->id."&perpage=30&page=0");
			echo $OUTPUT->footer();
			die();
		}
		
		$message = "Prezado(a) ".skills_get_serFullName().", Relatório atualizado com sucesso!";
		redirect($CFG->wwwroot.'/mod/skills/overview.php?id='.$cm->id.'&perpage=30&page=0', $message, 10);

	}else{
		echo $OUTPUT->notification("Erro ao registrar formulário.");
		redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
	}
	
	echo $OUTPUT->footer();
	die();
}


// Replace the following lines with you own code.
echo $OUTPUT->heading("Edição do ".$skills->name);

// Conditions to show the intro can change to look for own settings or whatever.
if ($skills->intro) {
	echo $OUTPUT->box(format_module_intro('skills', $skills, $cm->id), 'generalbox mod_introbox skillsintro', 'intro');
}

echo $mform->display();
// Finish the page.
echo $OUTPUT->footer();
