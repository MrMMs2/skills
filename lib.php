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
 * Library of interface functions and constants for module skills
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the skills specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_skills
 * @copyright  2015 Leo Santos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Example constant, you probably want to remove this :-)
 */
define('SKILLS_MODALITY_EAD', 1);
define('SKILLS_MODALITY_PRESENCIAL', 2);
define('SKILLS_MODALITY_SEMIPRESENCIAL', 3);
define('SKILLS_ITEMS_PER_PAGE', 30);

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function skills_supports($feature) {

    switch($feature) {
    	case FEATURE_GROUPS:
    		return true;
    	case FEATURE_GROUPINGS:
    		return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the skills into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $skills Submitted data from the form in mod_form.php
 * @param mod_skills_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted skills record
 */
function skills_add_instance(stdClass $skills, mod_skills_mod_form $mform = null) {
    global $DB;

    $skills->timecreated = time();

    // You may have to add extra stuff in here.

    $skills->id = $DB->insert_record('skills', $skills);

    skills_grade_item_update($skills);

    return $skills->id;
}

/**
 * Updates an instance of the skills in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $skills An object from the form in mod_form.php
 * @param mod_skills_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function skills_update_instance(stdClass $skills, mod_skills_mod_form $mform = null) {
    global $DB;

    $skills->timemodified = time();
    $skills->id = $skills->instance;

    // You may have to add extra stuff in here.

    $result = $DB->update_record('skills', $skills);

    skills_grade_item_update($skills);

    return $result;
}

/**
 * Removes an instance of the skills from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function skills_delete_instance($id) {
    global $DB;

    if (! $skills = $DB->get_record('skills', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('skills', array('id' => $skills->id));

    skills_grade_item_delete($skills);

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $skills The skills instance record
 * @return stdClass|null
 */
function skills_user_outline($course, $user, $mod, $skills) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $skills the module instance record
 */
function skills_user_complete($course, $user, $mod, $skills) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in skills activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function skills_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link skills_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function skills_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link skills_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function skills_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function skills_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function skills_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of skills?
 *
 * This function returns if a scale is being used by one skills
 * if it has support for grading and scales.
 *
 * @param int $skillsid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given skills instance
 */
function skills_scale_used($skillsid, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('skills', array('id' => $skillsid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of skills.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any skills instance
 */
function skills_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('skills', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given skills instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $skills instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function skills_grade_item_update(stdClass $skills, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($skills->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($skills->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $skills->grade;
        $item['grademin']  = 0;
    } else if ($skills->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$skills->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('mod/skills', $skills->course, 'mod', 'skills',
            $skills->id, 0, null, $item);
}

/**
 * Delete grade item for given skills instance
 *
 * @param stdClass $skills instance object
 * @return grade_item
 */
function skills_grade_item_delete($skills) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/skills', $skills->course, 'mod', 'skills',
            $skills->id, 0, null, array('deleted' => 1));
}

/**
 * Update skills grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $skills instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function skills_update_grades(stdClass $skills, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();

    grade_update('mod/skills', $skills->course, 'mod', 'skills', $skills->id, 0, $grades);
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function skills_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for skills file areas
 *
 * @package mod_skills
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function skills_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the skills file areas
 *
 * @package mod_skills
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the skills's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function skills_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);
    
    $itemid = 0;
    switch ($filearea) {
    	case 'fileplannextyear':
    		$dataid = (int) array_shift($args);
    		$itemid = $dataid;
    			
    		break;
    	default:
    		return false;
    		break;
    }
    
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_skills/$filearea/$itemid/$relativepath";
    
    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
    	return false;
    }
    
    // Nasty hack because we do not have fSile revisions in icontent yet.
    $lifetime = $CFG->filelifetime;
    if ($lifetime > 60*10) {
    	$lifetime = 60*10;
    }
    
    send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!

    send_file_not_found();
}
/**
 * Recupera arquivo anexado no campo fileplannextyear.
 *
 * @package mod_skills
 *
 * @param stdClass $context
 * @param stdClass $dataform
 * @return object $fileplannextyear
 */
function skills_getfileplannextyear($context, $dataform){
	global $CFG;
	$fs = get_file_storage();
	$files = $fs->get_area_files($context->id, 'mod_skills', 'fileplannextyear', $dataform->id, 'sortorder DESC, id ASC', false); // TODO: this is not very efficient!!
	
	if (count($files) >= 1) {
		$file = reset($files);
		unset($files);
	
		$path = '/'.$context->id.'/mod_skills/fileplannextyear/'.$dataform->id.$file->get_filepath().$file->get_filename();
		$fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
	
		$mimetype = $file->get_mimetype();
		
		//var_dump($mimetype);die;
	
		if ( file_mimetype_in_typegroup($mimetype, 'application/pdf') || file_mimetype_in_typegroup($mimetype, 'document') ){   // It's an PDF or Document
			$fileplannextyear = new stdClass;
			$fileplannextyear->fullurl = $fullurl;
			$fileplannextyear->mimetype = $mimetype;
			
			return $fileplannextyear;
		}
		return false;
	}
	return false;
}

/* Navigation API */

/**
 * Extends the global navigation tree by adding skills nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the skills module instance
 * @param stdClass $course current course record
 * @param stdClass $module current skills instance record
 * @param cm_info $cm course module information
 */
function skills_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, cm_info $cm) {
    // TODO Delete this function and its docblock, or implement it.
}

/**
 * Extends the settings navigation with the skills settings
 *
 * This function is called when the context for the page is a skills module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav complete settings navigation tree
 * @param navigation_node $skillsnode skills administration node
 */
function skills_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $skillsnode=null) {
	global $USER, $PAGE, $CFG, $DB, $OUTPUT;
    // TODO Delete this function and its docblock, or implement it.
    
	$skillsobject = $DB->get_record("skills", array("id" => $PAGE->cm->instance));
	if (empty($PAGE->cm->context)) {
		$PAGE->cm->context = context_module::instance($PAGE->cm->instance);
	}
	
	$enrolled = is_enrolled($PAGE->cm->context, $USER, '', false);
	$activeenrolled = is_enrolled($PAGE->cm->context, $USER, '', true);
	
	$mode = $skillsnode->add(get_string('reports'), null, navigation_node::TYPE_CONTAINER);
	$notenode = $mode->add(get_string('overview', 'skills'), 'overview.php?id='.$PAGE->cm->id.'&year='.date("Y").'&perpage=30&page=0');
	if (has_capability('mod/skills:submit', $PAGE->cm->context)){
		$notenode = $mode->add(get_string('completeview', 'skills'), 'completeview.php?id='.$PAGE->cm->id);
		
		$filtersnode = $mode->add(get_string('filter'));
		$url = new moodle_url('/mod/skills/filterphases.php', array('id'=>$PAGE->cm->id));
		$filtersnode->add(get_string('phasesstageprogramskills', 'skills'), $url);
	}
}


/**
 * Recupera registros da tabela de areas <mdl_skills_areas>
 *
 * @return array areas
 */
function skills_get_areas(){
	global $DB;
	return $DB->get_records_select('skills_areas', 'status = ?', array(0 => 1));
}

/**
 * Recupera registros da tabela de areas <mdl_skills_training>
 *
 * @return array trainings
 */
function skills_get_trainings(){
	global $DB;
	return $DB->get_records_select('skills_training', 'status = ?', array(0 => 1));
}

/**
 * Monta tabela de captura de registros para a tabela de treinamentos <skills_dataform_training>
 * @param int $training
 * @return array areas
 */
function skills_generate_table_training($training){
	return "<table id='training{$training->id}' class='generaltable boxaligncenter training'>
					<tbody>
						<tr>
							<th class='header'>Área</th>
							<th class='header'>Nº Participantes</th>
							<th class='header'>Nº {$training->name}</th>
							<th class='header'>Ações</th>
						</tr>
						<tr class='rowtraining{$training->id}'>
							<td class='area'>
								<select class='span12' onchange='validateTrainingArea(this)' name='trainings[training_{$training->id}][row1][tema]' data-training='{$training->id}'>
								  <option value=''>SELECIONE</option> 
								  <option value='TECNOLOGIA DA INFORMAÇÃO'>TECNOLOGIA DA INFORMAÇÃO</option> 
								  <option value='JUDICIÁRIA'>JUDICIÁRIA</option>
								  <option value='ADMINISTRATIVA/GESTÃO'>ADMINISTRATIVA/GESTÃO</option>
								  <option value='LÍNGUAS'>LÍNGUAS</option>
								  <option value='RESPONSABILIDADE SOCIAL/SAÚDE E QUALIDADE DE VIDA'>RESPONSABILIDADE SOCIAL/SAÚDE E QUALIDADE DE VIDA</option>
								  <option value='EDUCAÇÃO'>EDUCAÇÃO</option>
								</select>
							</td>
							<td class='cell'><input type='text' maxlength='6' onkeypress='return onlyNumbers(event)' name='trainings[training_{$training->id}][row1][participantes]'> </td>
							<td class='cell'><input type='text' maxlength='6' onkeypress='return onlyNumbers(event)' name='trainings[training_{$training->id}][row1][ntrainings]'> </td>
							<td>
						     <button class='btn btn-danger' onclick='RemoveTableRow(this)' type='button'><i class='fa fa-trash'></i></button>
						   </td>
						</tr>
					</tbody>
					<tfoot>
						 <tr>
						   <td colspan='4' style='text-align: left;'>
						     <button id='btntng-{$training->id}' class='btn btn-info' onclick='AddTableRow({$training->id})' type='button'><i class='fa fa-plus-circle'></i> Adicionar</button>
						   </td>
						 </tr>
					</tfoot>
				</table>";
}
/**
 * Gera tabela de edicao de  registros da tabela de areas <skills_dataform_training>
 * @param int $training
 * @return array areas
 */
function skills_generate_table_edit_training($training, $dataformid){
	global $DB;
	$optselect = array();
	$optselect[''] = 'SELECIONE';
	$optselect[1] = 'TECNOLOGIA DA INFORMAÇÃO';
	$optselect[2] = 'JUDICIÁRIA';
	$optselect[3] = 'ADMINISTRATIVA/GESTÃO';
	$optselect[4] = 'LÍNGUAS';
	$optselect[5] = 'RESPONSABILIDADE SOCIAL';
	$optselect[6] = 'EDUCAÇÃO';
	
	$objdatatngs = $DB->get_records('skills_dataform_training', array('dataformid'=>$dataformid, 'trainingid'=>$training->id)); // recupera registros
	
	$table = "<table id='training{$training->id}' class='generaltable boxaligncenter training'>
					<tbody>
						<tr>
							<th class='header'>Área</th>
							<th class='header'>Nº Participantes</th>
							<th class='header'>Nº {$training->name}</th>
							<th class='header'>Ações</th>
						</tr>
						";
						$row = 1;
						foreach ($objdatatngs as $datatng){
							//echo "<pre>"; var_dump($datatng);echo "</pre>";
							$table .="<tr class='rowtraining{$training->id}'>
										<td class='area'>
											<select onchange='validateTrainingArea(this)' name='trainings[training_{$training->id}][row{$row}][tema]' data-training='{$training->id}'>
											  ";
												$selected = null;
												foreach ($optselect as $opt){
													$selected = ($opt === $datatng->theme) ? 'selected' : null;
													$table .= "<option {$selected} value='{$opt}'>$opt</option>";
												}
											  
									$table .="</select>
										</td>
										<td class='cell'><input type='text' maxlength='6' onkeypress='return onlyNumbers(event)' value='{$datatng->numberparticipants}' name='trainings[training_{$training->id}][row{$row}][participantes]'> </td>
										<td class='cell'><input type='text' maxlength='6' onkeypress='return onlyNumbers(event)' value='{$datatng->numbertraining}' name='trainings[training_{$training->id}][row{$row}][ntrainings]'> </td>
										<td>
									     <button class='btn btn-danger' onclick='RemoveTableRow(this)' type='button'><i class='fa fa-trash'></i></button>
									   </td>
									</tr>";
							$row ++;
						}
		$table .=" </tbody>
					<tfoot>
						 <tr>
						   <td colspan='4' style='text-align: left;'>
						     <button id='btntng-{$training->id}' class='btn btn-info' onclick='AddTableRow({$training->id})' type='button'><i class='fa fa-plus-circle'></i> Adicionar</button>
						   </td>
						 </tr>
					</tfoot>
				</table>";
		return $table;
}
/**
 * Recupera uma area pelo id
 * @param int $areaid
 * @return array area
 */
function skills_get_areabyid($areaid){
	global $DB;
	
	return $DB->get_record('skills_areas', array('id'=>$areaid), 'id, name');
}
/**
 * Gera tabela com campos de trainingactionsociety
 * @param string $data
 * @return array areas
 */
function skills_get_table_trainingactionsociety($data = null){
	$objdata = new stdClass;
	$objdata->nturmas = new stdClass;
	$objdata->nturmas->presencial = null;
	$objdata->nturmas->semipresencial = null;
	$objdata->nturmas->ead = new stdClass;
	$objdata->nturmas->ead->comtutoria = null;
	$objdata->nturmas->ead->semtutoria = null;
	
	$objdata->ninscritos = new stdClass;
	$objdata->ninscritos->presencial = null;
	$objdata->ninscritos->semipresencial = null;
	$objdata->ninscritos->ead = new stdClass;
	$objdata->ninscritos->ead->comtutoria = null;
	$objdata->ninscritos->ead->semtutoria = null;
	
	$objdata->ncapacitados = new stdClass;
	$objdata->ncapacitados->presencial = null;
	$objdata->ncapacitados->semipresencial = null;
	$objdata->ncapacitados->ead = new stdClass;
	$objdata->ncapacitados->ead->comtutoria = null;
	$objdata->ncapacitados->ead->semtutoria = null;
	
	
	$objvalues = $data ? json_decode($data) : $objdata;
	
	return "<table id='trainingactionsociety' class='generaltable table-striped table-hover child'>
				<tr>
					<th class='header' rowspan='2'>DADOS / MODALIDADE</th>
					<th class='header' style='text-align: center;' rowspan='2'>PRESENCIAL</th>
					<th class='header' style='text-align: center;' colspan='2'>EAD</th>
					<th class='header' style='text-align: center;' rowspan='2'>SEMIPRESENCIAL</th>
				</tr>
				<tr style='background-color: #f9f9f9;'>
					<th class='header' style='text-align: center; border: 0; padding-top: 0;'>Com tutoria</th>
					<th class='header' style='text-align: center; border: 0; padding-top: 0;'>Sem tutoria</th>
				</tr>
				<tr>
					<td class='cell'>Nº DE TURMAS</td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->nturmas->presencial}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[nturmas][presencial]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->nturmas->ead->comtutoria}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[nturmas][ead][comtutoria]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->nturmas->ead->semtutoria}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[nturmas][ead][semtutoria]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->nturmas->semipresencial}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[nturmas][semipresencial]'></td>
				</tr>
				<tr>
					<td class='cell'>Nº DE INSCRITOS</td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ninscritos->presencial}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ninscritos][presencial]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ninscritos->ead->comtutoria}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ninscritos][ead][comtutoria]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ninscritos->ead->semtutoria}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ninscritos][ead][semtutoria]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ninscritos->semipresencial}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ninscritos][semipresencial]'></td>
				</tr>
				<tr>
					<td class='cell'>Nº DE CAPACITADOS</td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ncapacitados->presencial}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ncapacitados][presencial]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ncapacitados->ead->comtutoria}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ncapacitados][ead][comtutoria]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ncapacitados->ead->semtutoria}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ncapacitados][ead][semtutoria]'></td>
					<td class='cell'><input type='text' maxlength='6' value='{$objvalues->ncapacitados->semipresencial}' onkeypress='return onlyNumbers(event)' name='dtcapsociedade[ncapacitados][semipresencial]'></td>
				</tr>
			</table>";
}
/**
 * Gera tabela com campos de estrutura para capacitacao [structuretraining]
 * @param plataform
 * @return string
 */
function skills_get_table_structuretraining($platform = null){

	if(!is_object($platform)) {$platform = new stdClass;}
	$objvalues = new stdClass;
	$objvalues->type = property_exists($platform, 'type') ? $platform->type : null;
	$objvalues->version = property_exists($platform, 'version') ? $platform->version : null;
	
	return "<table class='generaltable boxaligncenter fieldsplataforms'>
						<tbody>
							<tr>
								<th class='header'>TIPO</th>
								<th class='header'>VERSÃO</th>
							</tr>
							<tr>
								<td class='cell'><input type='text' placeholder='Ex. Moodle, Blackboard' maxlength='32' onkeyup='this.value = this.value.toUpperCase()' value='{$objvalues->type}' name='structuretraining[plataform][type]'></td>
								<td class='cell'><input type='text' placeholder='Ex. 2.9, 3.0' maxlength='32' onkeyup='this.value = this.value.toUpperCase()' value='{$objvalues->version}' name='structuretraining[plataform][version]'></td>
								</tr>
						</tbody>
					</table>";
}

/**
 * Gera tabela com campos de despesas realizadas por acao formativa
 * @param $data
 * @return string
 */
function skills_get_table_runvalbyformaction($data = null){

	$runval  = json_decode($data);
	$objvalues = new stdClass;
	$objvalues->courses360h = new stdClass;
	$objvalues->courses360h->presencial = $runval ? $runval->courses360h->presencial : null;
	$objvalues->courses360h->ead = $runval ? $runval->courses360h->ead : null;
	$objvalues->courses360h->semipresencial = $runval ? $runval->courses360h->semipresencial : null;
	$objvalues->pos = $runval ? $runval->pos : null;
	$objvalues->palestras = $runval ? $runval->palestras : null;
	$objvalues->congressos = $runval ? $runval->congressos : null;
	$objvalues->encontros = $runval ? $runval->encontros : null;
	$objvalues->seminarios = $runval ? $runval->seminarios : null;
	$objvalues->foruns = $runval ? $runval->foruns : null;
	$objvalues->workshop = $runval ? $runval->workshop : null;
	$objvalues->graduacao = $runval ? $runval->graduacao : null;
	$objvalues->outros = $runval ? $runval->outros : null;

	return "<table class='generaltable boxaligncenter skillsimpletable'>
				<tbody>
					<tr>
						<td><label for='idcoursespres360h'>CURSOS PRESENCIAIS COM MENOS DE 360H</label></td>
						<td>
							<input type='text' id='idcoursespres360h' data-prefix='R$ ' class='money' value='{$objvalues->courses360h->presencial}' name='runvalbyformaction[courses360h][presencial]'>
						</td>
					</tr>
					<tr>
						<td><label for='idcoursesead360h'>CURSOS EAD COM MENOS DE 360H</label></td>
						<td>
							<input type='text' id='idcoursesead360h' data-prefix='R$ ' class='money' value='{$objvalues->courses360h->ead}' name='runvalbyformaction[courses360h][ead]'>
						</td>
					</tr>
					<tr>
						<td><label for='idcoursessemi360h'>CURSOS SEMIPRESENCIAIS COM MENOS DE 360H</label></td>
						<td>
							<input type='text' id='idcoursessemi360h' data-prefix='R$ ' class='money' value='{$objvalues->courses360h->semipresencial}' name='runvalbyformaction[courses360h][semipresencial]'>
						</td>
					</tr>
					<tr>
						<td><label for='idpos'>PÓS-GRADUAÇÃO</label></td>
						<td><input type='text' id='idpos' data-prefix='R$ ' class='money' value='{$objvalues->pos}' name='runvalbyformaction[pos]'></td>
					</tr>
					<tr>
						<td><label for='idpalestras'>PALESTRAS</label></td>
						<td><input type='text' id='idpalestras' data-prefix='R$ ' class='money' value='{$objvalues->palestras}' name='runvalbyformaction[palestras]'></td>
					</tr>
					<tr>
						<td><label for='idcongressos'>CONGRESSOS</label></td>
						<td><input type='text' id='idcongressos' data-prefix='R$ ' class='money' value='{$objvalues->congressos}' name='runvalbyformaction[congressos]'></td>
					</tr>
					<tr>
						<td><label for='idencontros'>ENCONTROS</label></td>
						<td><input type='text' id='idencontros' data-prefix='R$ ' class='money' value='{$objvalues->encontros}' name='runvalbyformaction[encontros]'></td>
					</tr>
					<tr>
						<td><label for='idseminarios'>SEMINÁRIOS</label></td>
						<td><input type='text' id='idseminarios' data-prefix='R$ ' class='money' value='{$objvalues->seminarios}' name='runvalbyformaction[seminarios]'></td>
					</tr>
					<tr>
						<td><label for='idforuns'>FÓRUNS</label></td>
						<td><input type='text' id='idforuns' data-prefix='R$ ' class='money' value='{$objvalues->foruns}' name='runvalbyformaction[foruns]'></td>
					</tr>
					<tr>
						<td><label for='idworkshop'>WORKSHOP</label></td>
						<td><input type='text' id='idworkshop' data-prefix='R$ ' class='money' value='{$objvalues->workshop}' name='runvalbyformaction[workshop]'></td>
					</tr>
					<tr>
						<td><label for='idgraduacao'>GRADUAÇÃO</label></td>
						<td><input type='text' id='idgraduacao' data-prefix='R$ ' class='money' value='{$objvalues->graduacao}' name='runvalbyformaction[graduacao]'></td>
					</tr>
					<tr>
						<td><label for='idoutros'>OUTRAS</label></td>
						<td><input type='text' id='idoutros' data-prefix='R$ ' class='money' value='{$objvalues->outros}' name='runvalbyformaction[outros]'></td>
					</tr>
				</tbody>
			</table>";
}

/**
 * Gera tabela com dados de despesas realizadas por acao formativa para visualizacao do usuario.
 * @param $data
 * @return string
 */
function skills_get_table_runvalbyformaction_view_user($data = null){
	$runval  = json_decode($data);
	$objvalues = new stdClass;
	$objvalues->courses360h = new stdClass;
	$objvalues->courses360h->ead = $runval ? $runval->courses360h->ead : null;
	$objvalues->courses360h->presencial = $runval ? $runval->courses360h->presencial : null;
	$objvalues->courses360h->semipresencial = $runval ? $runval->courses360h->semipresencial : null;
	$objvalues->pos = $runval ? $runval->pos : null;
	$objvalues->palestras = $runval ? $runval->palestras : null;
	$objvalues->congressos = $runval ? $runval->congressos : null;
	$objvalues->encontros = $runval ? $runval->encontros : null;
	$objvalues->seminarios = $runval ? $runval->seminarios : null;
	$objvalues->foruns = $runval ? $runval->foruns : null;
	$objvalues->outros = $runval ? $runval->outros : null;

	return "<table class='generaltable boxaligncenter'>
				<tr>
					<th>AÇÃO FORMATIVA</th>
					<th>DESPESA REALIZADA</th>
				</tr>
				<tbody>
					<tr>
						<td>CURSOS PRESENCIAIS COM MENOS DE 360H</td>
						<td>
							{$objvalues->courses360h->presencial}
						</td>
					</tr>
					<tr>
						<td>CURSOS EAD COM MENOS DE 360H</td>
						<td>
							{$objvalues->courses360h->ead}
						</td>
					</tr>
					<tr>
						<td>CURSOS SEMIPRESENCIAIS COM MENOS DE 360H</td>
						<td>
							{$objvalues->courses360h->semipresencial}
						</td>
					</tr>
					<tr>
						<td>PÓS-GRADUAÇÃO</td>
						<td>{$objvalues->pos}</td>
					</tr>
					<tr>
						<td>PALESTRAS</td>
						<td>{$objvalues->palestras}</td>
					</tr>
					<tr>
						<td>CONGRESSOS</td>
						<td>{$objvalues->congressos}</td>
					</tr>
					<tr>
						<td>ENCONTROS</td>
						<td>{$objvalues->encontros}</td>
					</tr>
					<tr>
						<td>SEMINÁRIOS</td>
						<td>{$objvalues->seminarios}</td>
					</tr>
					<tr>
						<td>FÓRUNS</td>
						<td>{$objvalues->foruns}</td>
					</tr>
					<tr>
						<td>OUTRAS</td>
						<td>{$objvalues->outros}</td>
					</tr>
				</tbody>
			</table>";
}


/**
 * Gera tabela com campos de outras despesas
 * @param $data
 * @return array areas
 */
function skills_get_table_othersvalrun($data = null){
	
	$othersval  = json_decode($data);
	$objvalues = new stdClass;
	$objvalues->coffee = $othersval ? $othersval->coffee : null;
	$objvalues->diarias = $othersval ? $othersval->diarias : null;
	$objvalues->others = $othersval ? $othersval->others : null;
	
	return "<table class='generaltable boxaligncenter skillsimpletable'>
				<tbody>
					<tr>
						<td><label for='idcoffee'>COFFEE BREAK</label></td>
						<td><input type='text' id='idcoffee' data-prefix='R$ ' value='{$objvalues->coffee}' class='money' name='othersvalrun[coffee]'></td>
					</tr>
					<tr>
						<td><label for='iddiarias'>DIÁRIAS E PASSAGENS</label></td>
						<td><input type='text' id='iddiarias' data-prefix='R$ ' class='money' value='{$objvalues->diarias}' name='othersvalrun[diarias]'></td>
					</tr>
					<tr>
						<td><label for='idothers'>OUTRAS</label></td>
						<td><input type='text' id='idothers' data-prefix='R$ ' class='money' value='{$objvalues->others}' name='othersvalrun[others]'></td>
					</tr>
				</tbody>
			</table>";
}

/**
 * Gera tabela com campos referentes as fases do estágio do programama de gestão por competencias
 * @param $field	parametro responsavel por informar a opcao sobre o estagio da competencia.
 * @param $ckbid	parametro responsavel por informar o campo imput checkbox selecionado.
 * @return array areas
 */
function skills_get_table_phasestageprogramskills($field, $ckbid){

	return "<table id='tblphase_{$ckbid}' class='generaltable table-striped table-hover child'>
				<tr>
					<td class='cell bold'>COMPETÊNCIAS ORGANIZACIONAIS</td>
					<td class='cell'>
						<input id='id_{$field}-organizacionais_andamento' type='radio' value='andamento' name='phasestageprogramskills[{$field}][organizacionais]'>
						<label for='id_{$field}-organizacionais_andamento'>Em andamento</label>
					</td>
					<td class='cell'>
						<input id='id_{$field}-organizacionais_concluido' type='radio' value='concluido' name='phasestageprogramskills[{$field}][organizacionais]'>
						<label for='id_{$field}-organizacionais_concluido'>Concluído</label>
					</td>
				</tr>
				<tr>
					<td class='cell bold'>COMPETÊNCIAS SETORIAIS</td>
					<td class='cell'>
						<input id='id_{$field}-setoriais_andamento' type='radio' value='andamento' name='phasestageprogramskills[{$field}][setoriais]'>
						<label for='id_{$field}-setoriais_andamento'>Em andamento</label>
					</td>
					<td class='cell'>
						<input id='id_{$field}-setoriais_concluido' type='radio' value='concluido' name='phasestageprogramskills[{$field}][setoriais]'>
						<label for='id_{$field}-setoriais_concluido'>Concluído</label>
					</td>
				</tr>
				<tr>
					<td class='cell bold'>COMPETÊNCIAS INDIVIDUAIS</td>
					<td class='cell'>
						<input id='id_{$field}-individuais_andamento' type='radio' value='andamento' name='phasestageprogramskills[{$field}][individuais]'>
						<label for='id_{$field}-individuais_andamento'>Em andamento</label>
					</td>
					<td class='cell'>
						<input id='id_{$field}-individuais_concluido' type='radio' value='concluido' name='phasestageprogramskills[{$field}][individuais]'>
						<label for='id_{$field}-individuais_concluido'>Concluído</label>
					</td>
				</tr>
			</table>";
}

/**
 * Gera tabela com campos de outras despesas para visualizacao do usuario.
 * @param $data
 * @return array areas
 */
function skills_get_table_othersvalrun_view_user($data = null){

	$othersval  = json_decode($data);
	$objvalues = new stdClass;
	$objvalues->coffee = $othersval ? $othersval->coffee : null;
	$objvalues->diarias = $othersval ? $othersval->diarias : null;
	$objvalues->others = $othersval ? $othersval->others : null;

	return "<table class='generaltable'>
				<tr>
					<th>AÇÃO FORMATIVA</th>
					<th>DESPESA REALIZADA</th>
				</tr>
				<tbody>
					<tr>
						<td>COFFEE BREAK</td>
						<td>{$objvalues->coffee}</td>
					</tr>
					<tr>
						<td>DIÁRIAS E PASSAGENS</td>
						<td>{$objvalues->diarias}</td>
					</tr>
					<tr>
						<td>OUTRAS</td>
						<td>{$objvalues->others}</td>
					</tr>
				</tbody>
			</table>";
	}

/**
 * Gera campo textarea
 *
 * @return array areas
 */
function skills_generate_textarea($name, $class = null, $id = null, $value=null){
	$value = $value ? str_replace("OUTRO(S):", '', $value) : null;
	return $textarea = "<textarea id='{$id}' onkeyup='this.value = this.value.toUpperCase()' class='{$class}' name='{$name}'>{$value}</textarea>";
}
/**
 * Recupera registros da tabela de areas <mdl_skills_areas>
 *
 * @return array areas
 */
function skills_generate_table_areas($areaid, $dataformid = null){
	$objead = new stdClass; $objeadvalue = new stdClass;
	$objpres = new stdClass; $objpresvalue = new stdClass;
	$objsemi = new stdClass; $objsemivalue = new stdClass;
	if($dataformid){
		global $DB;
		$objead = $DB->get_record('skills_dataform_areas', array('dataformid'=>$dataformid, 'areasid'=>$areaid, 'modalityid'=>1)); // EAD
		$objpres = $DB->get_record('skills_dataform_areas', array('dataformid'=>$dataformid, 'areasid'=>$areaid, 'modalityid'=>2)); // Presencial
		$objsemi = $DB->get_record('skills_dataform_areas', array('dataformid'=>$dataformid, 'areasid'=>$areaid, 'modalityid'=>3)); // Semi-presencial
	}
	// Presencial = 2
	$objpresvalue->numbercourses = (is_object($objpres) && property_exists($objpres, 'numbercourses')) ? $objpres->numbercourses : null;
	$objpresvalue->numbervacancies = (is_object($objpres) && property_exists($objpres, 'numbervacancies')) ? $objpres->numbervacancies : null;
	$objpresvalue->number_trained = (is_object($objpres) && property_exists($objpres, 'number_trained')) ? $objpres->number_trained : null;
	$objpresvalue->numberenrollees = (is_object($objpres) && property_exists($objpres, 'numberenrollees')) ? $objpres->numberenrollees : null;
	$objpresvalue->numberdisapproved = (is_object($objpres) && property_exists($objpres, 'numberdisapproved')) ? $objpres->numberdisapproved : null;
	$objpresvalue->evasion = (is_object($objpres) && property_exists($objpres, 'evasion')) ? $objpres->evasion : null;
	$objpresvalue->numberinternalinstructors = (is_object($objpres) && property_exists($objpres, 'numberinternalinstructors')) ? $objpres->numberinternalinstructors : null;
	$objpresvalue->numberexternallinstructors = (is_object($objpres) && property_exists($objpres, 'numberexternallinstructors')) ? $objpres->numberexternallinstructors : null;
	
	// EAD = 1
	$objeadvalue->numbercourses = (is_object($objead) && property_exists($objead, 'numbercourses')) ? $objead->numbercourses : null;
	$objeadvalue->numbervacancies = (is_object($objead) && property_exists($objead, 'numbervacancies')) ? $objead->numbervacancies : null;
	$objeadvalue->number_trained = (is_object($objead) && property_exists($objead, 'number_trained')) ? $objead->number_trained : null;
	$objeadvalue->numberenrollees = (is_object($objead) && property_exists($objead, 'numberenrollees')) ? $objead->numberenrollees : null;
	$objeadvalue->numberdisapproved = (is_object($objead) && property_exists($objead, 'numberdisapproved')) ? $objead->numberdisapproved : null;
	$objeadvalue->evasion = (is_object($objead) && property_exists($objead, 'evasion')) ? $objead->evasion : null;
	$objeadvalue->numberinternalinstructors = (is_object($objead) && property_exists($objead, 'numberinternalinstructors')) ? $objead->numberinternalinstructors : null;
	$objeadvalue->numberexternallinstructors = (is_object($objead) && property_exists($objead, 'numberexternallinstructors')) ? $objead->numberexternallinstructors : null;
	
	// EAD = 3
	$objsemivalue->numbercourses = (is_object($objsemi) && property_exists($objsemi, 'numbercourses')) ? $objsemi->numbercourses : null;
	$objsemivalue->numbervacancies = (is_object($objsemi) && property_exists($objsemi, 'numbervacancies')) ? $objsemi->numbervacancies : null;
	$objsemivalue->number_trained = (is_object($objsemi) && property_exists($objsemi, 'number_trained')) ? $objsemi->number_trained : null;
	$objsemivalue->numberenrollees = (is_object($objsemi) && property_exists($objsemi, 'numberenrollees')) ? $objsemi->numberenrollees : null;
	$objsemivalue->numberdisapproved = (is_object($objsemi) && property_exists($objsemi, 'numberdisapproved')) ? $objsemi->numberdisapproved : null;
	$objsemivalue->evasion = (is_object($objsemi) && property_exists($objsemi, 'evasion')) ? $objsemi->evasion : null;
	$objsemivalue->numberinternalinstructors = (is_object($objsemi) && property_exists($objsemi, 'numberinternalinstructors')) ? $objsemi->numberinternalinstructors : null;
	$objsemivalue->numberexternallinstructors = (is_object($objsemi) && property_exists($objsemi, 'numberexternallinstructors')) ? $objsemi->numberexternallinstructors : null;
	
	return "<table class='generaltable boxaligncenter'>
					<tr>
						<th class='header'>Dados / Modalidade</th>
						<th class='header'>Presencial</th>
						<th class='header'>EAD</th>
						<th class='header'>SEMIPRESENCIAL</th>
					</tr>
					<tr>
						<td class='cell'>Nº DE CURSOS</td>
						<td class='cell'><input type='text' value='{$objpresvalue->numbercourses}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][ncourses][presencial]'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->numbercourses}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][ncourses][ead]'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->numbercourses}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][ncourses][semipresencial]'> </td>
					</tr>
					<tr>
						<td class='cell'>Nº DE VAGAS</td>
						<td class='cell'><input type='text' value='{$objpresvalue->numbervacancies}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][nvacancies][presencial]' data-mod='pres' data-area='{$areaid}' id='area{$areaid}_nvac_pres'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->numbervacancies}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][nvacancies][ead]' data-mod='ead' data-area='{$areaid}' id='area{$areaid}_nvac_ead'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->numbervacancies}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][nvacancies][semipresencial]' data-mod='semi' data-area='{$areaid}' id='area{$areaid}_nvac_semi'> </td>
					</tr>
					<tr>
						<td class='cell'>Nº DE INSCRITOS</td>
						<td class='cell'><input type='text' value='{$objpresvalue->numberenrollees}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][numberenrollees][presencial]' data-mod='pres' data-area='{$areaid}' id='area{$areaid}_nenroll_pres'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->numberenrollees}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][numberenrollees][ead]' data-mod='ead' data-area='{$areaid}' id='area{$areaid}_nenroll_ead'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->numberenrollees}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][numberenrollees][semipresencial]' data-mod='semi' data-area='{$areaid}' id='area{$areaid}_nenroll_semi'> </td>
					</tr>
					<tr>
						<td class='cell'>Nº DE CAPACITADOS</td>
						<td class='cell'><input type='text' value='{$objpresvalue->number_trained}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][nqualified][presencial]' data-mod='pres' data-area='{$areaid}' id='area{$areaid}_nqual_pres'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->number_trained}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][nqualified][ead]' data-mod='ead' data-area='{$areaid}' id='area{$areaid}_nqual_ead'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->number_trained}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][nqualified][semipresencial]' data-mod='semi' data-area='{$areaid}' id='area{$areaid}_nqual_semi'> </td>
					</tr>
					<tr>
						<td class='cell'>Nº DE REPROVADOS</td>
						<td class='cell'><input type='text' value='{$objpresvalue->numberdisapproved}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][numberdisapproved][presencial]' data-mod='pres' data-area='{$areaid}' id='area{$areaid}_ndisap_pres'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->numberdisapproved}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][numberdisapproved][ead]' data-mod='ead' data-area='{$areaid}' id='area{$areaid}_ndisap_ead'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->numberdisapproved}' onblur='processHandler(this)' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][numberdisapproved][semipresencial]' data-mod='semi' data-area='{$areaid}' id='area{$areaid}_ndisap_semi'> </td>
					</tr>
					<tr>
						<td class='cell'>Nº DE DESISTENTES</td>
						<td class='cell'><input type='text' value='' readonly='readonly' name='lowercourse360hours[area_{$areaid}][desist][presencial]' data-mod='pres' data-area='{$areaid}' id='area{$areaid}_ndesist_pres'> </td>
						<td class='cell'><input type='text' value='' readonly='readonly' name='lowercourse360hours[area_{$areaid}][desist][ead]' data-mod='ead' data-area='{$areaid}' id='area{$areaid}_ndesist_ead'> </td>
						<td class='cell'><input type='text' value='' readonly='readonly' name='lowercourse360hours[area_{$areaid}][desist][semipresencial]' data-mod='semi' data-area='{$areaid}' id='area{$areaid}_ndesist_semi'> </td>
					</tr>
					<tr>
						<td class='cell'>PERCENTUAL DE EVASÃO</td>
						<td class='cell'><input type='text' value='{$objpresvalue->evasion}' readonly='readonly' name='lowercourse360hours[area_{$areaid}][evasion][presencial]' data-mod='pres' data-area='{$areaid}' id='area{$areaid}_percent_pres'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->evasion}' readonly='readonly' name='lowercourse360hours[area_{$areaid}][evasion][ead]' data-mod='ead' data-area='{$areaid}' id='area{$areaid}_percent_ead'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->evasion}' readonly='readonly' name='lowercourse360hours[area_{$areaid}][evasion][semipresencial]' data-mod='semi' data-area='{$areaid}' id='area{$areaid}_percent_semi'> </td>
					</tr>
					<tr>
						<td class='cell'>Nº DE INSTRUTORES INTERNOS</td>
						<td class='cell'><input type='text' value='{$objpresvalue->numberinternalinstructors}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][instructorsInternal][presencial]'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->numberinternalinstructors}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][instructorsInternal][ead]'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->numberinternalinstructors}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][instructorsInternal][semipresencial]'> </td>
					</tr>
					<tr>
						<td class='cell'>Nº DE INSTRUTORES EXTERNOS</td>
						<td class='cell'><input type='text' value='{$objpresvalue->numberexternallinstructors}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][instructorsExternal][presencial]'> </td>
						<td class='cell'><input type='text' value='{$objeadvalue->numberexternallinstructors}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][instructorsExternal][ead]'> </td>
						<td class='cell'><input type='text' value='{$objsemivalue->numberexternallinstructors}' onkeypress='return onlyNumbers(event)' name='lowercourse360hours[area_{$areaid}][instructorsExternal][semipresencial]'> </td>
					</tr>
				</table>";
}

/**
 * Funcao responsavel por salvar um registro no banco de dados
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function skills_save($table, $data){
	//echo "<pre>"; var_dump($data); echo "</pre>"; die();
	global $DB;
	// Insert
	if (!$data->id) {
		return $DB->insert_record($table, $data);
	}
	// Update
	else{
		return $DB->update_record($table, $data);
	}
}
/**
 * Funcao responsavel por deletar dataform_areas
 *
 * @package		mod/skills
 * @param 		$dataformid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function skills_delete_dataform_areas($dataformid){
	global $DB;
	return $DB->delete_records('skills_dataform_areas', array('dataformid'=>$dataformid));
}

/**
 * Funcao responsavel por deletar dataform_trainings
 *
 * @package		mod/skills
 * @param 		$dataformid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function skills_delete_dataform_trainings($dataformid){
	global $DB;
	return $DB->delete_records('skills_dataform_training', array('dataformid'=>$dataformid));
}
/**
 * Funcao responsavel por salvar cursos com menos de 360 horas
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function skills_set_skills_dataform_areas($data, $dataformid){
	$i = 0;
	foreach ($data as $keyArea => $valueArea){
		 
		$arr = explode('_', $keyArea);
		$areaid = end($arr);
		//var_dump($areaid);
		
		$objPresencial = new stdClass();
		$objPresencial->id = null;
		$objPresencial->dataformid = $dataformid;
		$objPresencial->areasid = (int) $areaid;
		$objPresencial->modalityid = SKILLS_MODALITY_PRESENCIAL;
		$objPresencial->numbercourses =  $valueArea['ncourses']['presencial'];
		$objPresencial->numbervacancies = $valueArea['nvacancies']['presencial'];
		$objPresencial->number_trained = $valueArea['nqualified']['presencial'];
		$objPresencial->numberenrollees = $valueArea['numberenrollees']['presencial'];
		$objPresencial->numberdisapproved = $valueArea['numberdisapproved']['presencial'];
		$objPresencial->evasion =  $valueArea['evasion']['presencial'];
		$objPresencial->numberinternalinstructors = $valueArea['instructorsInternal']['presencial'];
		$objPresencial->numberexternallinstructors = $valueArea['instructorsExternal']['presencial'];
		
		$objEad = new stdClass();
		$objEad->id = null;
		$objEad->dataformid = $dataformid;
		$objEad->areasid = (int) $areaid;
		$objEad->modalityid = SKILLS_MODALITY_EAD; // Ead
		$objEad->numbercourses =  $valueArea['ncourses']['ead'];
		$objEad->numbervacancies = $valueArea['nvacancies']['ead'];
		$objEad->number_trained = $valueArea['nqualified']['ead'];
		$objEad->numberenrollees = $valueArea['numberenrollees']['ead'];
		$objEad->numberdisapproved = $valueArea['numberdisapproved']['ead'];
		$objEad->evasion =  $valueArea['evasion']['ead'];
		$objEad->numberinternalinstructors = $valueArea['instructorsInternal']['ead'];
		$objEad->numberexternallinstructors = $valueArea['instructorsExternal']['ead'];
		
		$objSemi = new stdClass();
		$objSemi->id = null;
		$objSemi->dataformid = $dataformid;
		$objSemi->areasid = (int) $areaid;
		$objSemi->modalityid = SKILLS_MODALITY_SEMIPRESENCIAL; // Ead
		$objSemi->numbercourses =  $valueArea['ncourses']['semipresencial'];
		$objSemi->numbervacancies = $valueArea['nvacancies']['semipresencial'];
		$objSemi->number_trained = $valueArea['nqualified']['semipresencial'];
		$objSemi->numberenrollees = $valueArea['numberenrollees']['semipresencial'];
		$objSemi->numberdisapproved = $valueArea['numberdisapproved']['semipresencial'];
		$objSemi->evasion =  $valueArea['evasion']['semipresencial'];
		$objSemi->numberinternalinstructors = $valueArea['instructorsInternal']['semipresencial'];
		$objSemi->numberexternallinstructors = $valueArea['instructorsExternal']['semipresencial'];
		
		// Gravando dados
		if(skills_valida_skills_dataform_areas($objPresencial)){
			skills_save('skills_dataform_areas', $objPresencial);
		}
		if(skills_valida_skills_dataform_areas($objEad)){
			skills_save('skills_dataform_areas', $objEad);
		}
		if(skills_valida_skills_dataform_areas($objSemi)){
			skills_save('skills_dataform_areas', $objSemi);
		}
		
	}// fim foreach
} // fim functuion



/**
 * Funcao responsavel por validar registro de dataform_area
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_valida_skills_dataform_areas($objDataform){
	if($objDataform->numbercourses ||
		$objDataform->numbervacancies ||
		$objDataform->number_trained ||
		$objDataform->evasion ||
		$objDataform->numberinternalinstructors ||
		$objDataform->numberexternallinstructors){
		return true;
	}
	return false;
}
/**
 * Funcao responsavel por salvar treinamnetos, palestras, congressos...
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function skills_set_skills_dataform_training($data, $dataformid){
	
	foreach ($data as $keyTrainings=>$valuesTrainings){
		$arr = explode('_', $keyTrainings);
		$trainingid = end($arr);
		
		foreach ($valuesTrainings as $keyRows => $valuesRows){
			
			// Montando obj
			$objTraining = new stdClass();
			$objTraining->id = null;
			$objTraining->trainingid = $trainingid;
			$objTraining->dataformid = $dataformid;
			$objTraining->theme = $valuesRows['tema'];
			$objTraining->numberparticipants = $valuesRows['participantes'];
			$objTraining->numbertraining = $valuesRows['ntrainings'];
			
			// Gravando
			if (skills_valida_skills_dataform_training($objTraining)){
				// Setando alguns valores caso o usuario deixe em branco
				$objTraining->theme = $objTraining->theme ? $objTraining->theme : 'NAO/IDENTIFICADO';
				$objTraining->numbertraining = $objTraining->numbertraining ? $objTraining->numbertraining : '0.0';	
			
				skills_save('skills_dataform_training', $objTraining);
			}
		} // fim foreach 
		
	} // fim foreach

} // fim function

/**
 * Funcao responsavel por validar registro de dataform_training
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_valida_skills_dataform_training($objData){
	if($objData->theme ||
		$objData->numberparticipants ||
		$objData->numbertraining){
		return true;
	}
	return false;
}

/**
 * Transforma valores de um array em string no formato aceitavel pela clausula IN do mysql
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @param 		array $data
 * @return 		array $organs
 * @copyright	CEAJUD - CNJ
 * */
function skills_make_str_in($data){
	$str = "";
	foreach ($data as $value) {
		$str .= "'{$value}',";
	}
	return substr($str, 0 , -1);
}

/**
 * Gera lista de tribunais/orgaos do judiciario brasileiro por ramos de justiça.
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		array $branches
 * @copyright	CEAJUD - CNJ
 * */
function get_justice_branches($branch = null){
	
	$branches = array(
			'superiores' => array(
					'cjf', 'cnj', 'csjt', 'stj', 'stf', 'tst', 'tse',
			),
			'federal' => array(
					'trf1', 'trf2', 'trf3', 'trf4', 'trf5',
			),
			'estadual'=>array(
					'tjdft', 'tjba', 'tjpb', 'tjal', 'tjgo', 'tjmg', 'tjpe', 'tjro', 'tjrr', 'tjsc', 'tjsp',
					'tjse', 'tjto', 'tjac', 'tjap', 'tjam', 'tjce', 'tjes', 'tjma', 'tjmt', 'tjms', 'tjpa',
					'tjpr', 'tjpi', 'tjrj', 'tjrn', 'tjrs',
			),
			'militar'=>array(
					'stm', 'tjm.mg', 'tjmsp', 'tjmrs',
			),
			'trabalho'=>array(
					'trt10', 'trt11', 'trt12', 'trt13', 'trt14', 'trt15', 'trt16', 'trt17', 'trt18', 'trt19', 
					'trt1', 'trt20', 'trt21', 'trt22', 'trt23', 'trt24', 'trt2', 'trt3', 'trt4', 'trt5', 'trt6',
					'trt7', 'trt8', 'trt9',
			),
			'eleitoral'=>array(
					'tre-am', 'tre-ba', 'tre-al', 'tre-go', 'tre-ms', 'tre-mg', 'tre-pe', 'tre-se', 'tre-ac',
					'tre-ap', 'tre-ce', 'tre-df', 'tre-es', 'tre-ma', 'tre-mt', 'tre-pa', 'tre-pb', 'tre-pr',
					'tre-pi', 'tre-rj', 'tre-rn', 'tre-rs', 'tre-ro', 'tre-rr', 'tre-sc', 'tre-sp', 'tre-to',
			),
			
	);
	if ($branch && array_key_exists($branch, $branches)) {
		return $branches[$branch];
	}
	return $branches;
}
/**
 * Gera lista com nomes por extenso dos ramos da justica
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @copyright	CEAJUD - CNJ
 * */
function skills_generate_array_names_justice_branches(){
	$names = [];
	$names[''] = get_string('select', 'mod_skills');
	$names['superiores'] = "JUSTIÇA SUPERIOR";
	$names['federal'] = "JUSTIÇA FEDERAL";
	$names['estadual'] = "JUSTIÇA ESTADUAL";
	$names['militar'] = "JUSTIÇA MILITAR";
	$names['trabalho'] = "JUSTIÇA DO TRABALHO";
	$names['eleitoral'] = "JUSTIÇA ELEITORAL";
	return $names;
}
/**
 * Gera lista de metodologias
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		array $methodologies
 * @copyright	CEAJUD - CNJ
 * */
function skills_generate_array_methodologies(){
	return array(
			'AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS',
			'AVALIAÇÃO DE DESEMPENHO SEM COMPETÊNCIAS',
			'LEVANTAMENTO DE NECESSIDADE DE TREINAMENTO',
			'HISTÓRICO DOS ANOS ANTERIORES',
			'PLANEJAMENTO ESTRATÉGICO',
			'ANÁLISE DOS MACROPROCESSOS',
	);
}
/**
 * Gera lista de estagios do programa de gestao por competencias
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		array $phasestages
 * @copyright	CEAJUD - CNJ
 * */
function skills_generate_array_stagesprogram(){
	$phasestages = [];
	$phasestages['map'] = 'MAPEAMENTO DAS COMPETÊNCIAS';
	$phasestages['diag'] = 'DIAGNÓSTICO DE COMPETÊNCIAS E ANÁLISE DO GAP';
	$phasestages['cap'] = 'CAPACITAÇÃO POR COMPETÊNCIAS';
	$phasestages['aval'] = 'AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS';
	return $phasestages;
}

/**
 * Gera lista de fases do estagios do programa de gestao por competencias
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		array $phasestages
 * @copyright	CEAJUD - CNJ
 * */
function skills_generate_array_phasesstagesprogram(){
	$phasestages = [];
	$phasestages['organizacionais'] = 'ORGANIZACIONAIS';
	$phasestages['setoriais'] = 'SETORIAIS';
	$phasestages['individuais'] = 'INDIVIDUAIS';
	return $phasestages;
}

/**
 * Gera lista de tribunais/orgaos do judiciario brasileiro
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		array $organs
 * @copyright	CEAJUD - CNJ
 * */
function skills_generate_array_organs(){
	return array(
			'' => get_string('select', 'mod_skills'),
			'cjf' => 'Conselho da Justiça Federal',
			'cnj' => 'Conselho Nacional de Justiça',
			'csjt' => 'Conselho Superior da Justiça do Trabalho',
			//'enamat' => 'Escola Nacional da Magistratura do Trabalho',
			//'enfam' => 'Escola Nacional de Formação e Aperfeiçoamento de Magistrados',
			'stj' => 'Superior Tribunal de Justiça',
			'stm' => 'Superior Tribunal Militar',
			'stf' => 'Supremo Tribunal Federal',
			'tjdft' => 'Tribunal de Justiça do Distrito Federal e Territórios',
			'tjba' => 'Tribunal de Justiça do Estado da Bahia',
			'tjpb' => 'Tribunal de Justiça do Estado da Paraiba',
			'tjal' => 'Tribunal de Justiça do Estado de Alagoas',
			'tjgo' => 'Tribunal de Justiça do Estado de Goiás',
			'tjmg' => 'Tribunal de Justiça do Estado de Minas Gerais',
			'tjpe' => 'Tribunal de Justiça do Estado de Pernambuco',
			'tjro' => 'Tribunal de Justiça do Estado de Rondônia',
			'tjrr' => 'Tribunal de Justiça do Estado de Roraima',
			'tjsc' => 'Tribunal de Justiça do Estado de Santa Catarina',
			'tjsp' => 'Tribunal de Justiça do Estado de São Paulo',
			'tjse' => 'Tribunal de Justiça do Estado de Sergipe',
			'tjto' => 'Tribunal de Justiça do Estado de Tocantins',
			'tjac' => 'Tribunal de Justiça do Estado do Acre',
			'tjap' => 'Tribunal de Justiça do Estado do Amapá',
			'tjam' => 'Tribunal de Justiça do Estado do Amazonas',
			'tjce' => 'Tribunal de Justiça do Estado do Ceará',
			'tjes' => 'Tribunal de Justiça do Estado do Espírito Santo',
			'tjma' => 'Tribunal de Justiça do Estado do Maranhão',
			'tjmt' => 'Tribunal de Justiça do Estado do Mato Grosso',
			'tjms' => 'Tribunal de Justiça do Estado do Mato Grosso do Sul',
			'tjpa' => 'Tribunal de Justiça do Estado do Pará',
			'tjpr' => 'Tribunal de Justiça do Estado do Paraná',
			'tjpi' => 'Tribunal de Justiça do Estado do Piauí',
			'tjrj' => 'Tribunal de Justiça do Estado do Rio de Janeiro',
			'tjrn' => 'Tribunal de Justiça do Estado do Rio Grande do Norte',
			'tjrs' => 'Tribunal de Justiça do Estado do Rio Grande do Sul',
			'tjm.mg' => 'Tribunal de Justiça Militar do Estado de Minas Gerais',
			'tjmsp' => 'Tribunal de Justiça Militar do Estado de São Paulo',
			'tjmrs' => 'Tribunal de Justiça Militar do Estado do Rio Grande do Sul',
			'trt10' => 'Tribunal Regional do Trabalho da 10ª Região - DF',
			'trt11' => 'Tribunal Regional do Trabalho da 11ª Região - AM/RR',
			'trt12' => 'Tribunal Regional do Trabalho da 12ª Região - SC',
			'trt13' => 'Tribunal Regional do Trabalho da 13ª Região - PB',
			'trt14' => 'Tribunal Regional do Trabalho da 14ª Região - AC/RO',
			'trt15' => 'Tribunal Regional do Trabalho da 15ª Região - Campinas',
			'trt16' => 'Tribunal Regional do Trabalho da 16ª Região - MA',
			'trt17' => 'Tribunal Regional do Trabalho da 17ª Região - ES',
			'trt18' => 'Tribunal Regional do Trabalho da 18ª Região - GO',
			'trt19' => 'Tribunal Regional do Trabalho da 19ª Região - AL',
			'trt1' => 'Tribunal Regional do Trabalho da 1ª Região - RJ',
			'trt20' => 'Tribunal Regional do Trabalho da 20ª Região - SE',
			'trt21' => 'Tribunal Regional do Trabalho da 21ª Região - RN',
			'trt22' => 'Tribunal Regional do Trabalho da 22ª Região - PI',
			'trt23' => 'Tribunal Regional do Trabalho da 23ª Região - MT',
			'trt24' => 'Tribunal Regional do Trabalho da 24ª Região - MS',
			'trt2' => 'Tribunal Regional do Trabalho da 2ª Região - SP',
			'trt3' => 'Tribunal Regional do Trabalho da 3ª Região - MG',
			'trt4' => 'Tribunal Regional do Trabalho da 4ª Região - RS',
			'trt5' => 'Tribunal Regional do Trabalho da 5ª Região - BA',
			'trt6' => 'Tribunal Regional do Trabalho da 6ª Região - PE',
			'trt7' => 'Tribunal Regional do Trabalho da 7ª Região - CE',
			'trt8' => 'Tribunal Regional do Trabalho da 8ª Região - PA/AP',
			'trt9' => 'Tribunal Regional do Trabalho da 9ª Região - PR',
			'tre-am' => 'Tribunal Regional Eleitoral  do Amazonas',
			'tre-ba' => 'Tribunal Regional Eleitoral da Bahia',
			'tre-al' => 'Tribunal Regional Eleitoral de Alagoas',
			'tre-go' => 'Tribunal Regional Eleitoral de Goiás',
			'tre-ms' => 'Tribunal Regional Eleitoral de Mato Grosso do Sul',
			'tre-mg' => 'Tribunal Regional Eleitoral de Minas Gerais',
			'tre-pe' => 'Tribunal Regional Eleitoral de Pernambuco',
			'tre-se' => 'Tribunal Regional Eleitoral de Sergipe',
			'tre-ac' => 'Tribunal Regional Eleitoral do Acre',
			'tre-ap' => 'Tribunal Regional Eleitoral do Amapá',
			'tre-ce' => 'Tribunal Regional Eleitoral do Ceará',
			'tre-df' => 'Tribunal Regional Eleitoral do DF',
			'tre-es' => 'Tribunal Regional Eleitoral do Espirito Santo',
			'tre-ma' => 'Tribunal Regional Eleitoral do Maranhão',
			'tre-mt' => 'Tribunal Regional Eleitoral do Mato Grosso',
			'tre-pa' => 'Tribunal Regional Eleitoral Pará',
			'tre-pb' => 'Tribunal Regional Eleitoral Paraíba',
			'tre-pr' => 'Tribunal Regional Eleitoral Paraná',
			'tre-pi' => 'Tribunal Regional Eleitoral Piauí',
			'tre-rj' => 'Tribunal Regional Eleitoral Rio de Janeiro',
			'tre-rn' => 'Tribunal Regional Eleitoral Rio Grande do Norte',
			'tre-rs' => 'Tribunal Regional Eleitoral Rio Grande do Sul',
			'tre-ro' => 'Tribunal Regional Eleitoral Rondônia',
			'tre-rr' => 'Tribunal Regional Eleitoral Roraima',
			'tre-sc' => 'Tribunal Regional Eleitoral Santa Catarina',
			'tre-sp' => 'Tribunal Regional Eleitoral São Paulo',
			'tre-to' => 'Tribunal Regional Eleitoral Tocantins',
			'trf1' => 'Tribunal Regional Federal da 1ª Região',
			'trf2' => 'Tribunal Regional Federal da 2ª Região',
			'trf3' => 'Tribunal Regional Federal da 3ª Região',
			'trf4' => 'Tribunal Regional Federal da 4ª Região',
			'trf5' => 'Tribunal Regional Federal da 5ª Região',
			'tst' => 'Tribunal Superior do Trabalho',
			'tse' => 'Tribunal Superior Eleitoral',
	);
}
/**
 * Recupera os anos em os relatorios foram enviados
 *
 * @package		mod/skills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		array $years
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_array_years(){
	global $DB;
	
	$objyears = $DB->get_records_sql('SELECT distinct(currentyear) FROM {skills_dataform} ORDER BY currentyear DESC');
	
	if(!$objyears){
		$years = array(date('Y')=> date('Y'));
		return $years;
	}
	foreach ($objyears as $objyear){
		$years[$objyear->currentyear] = $objyear->currentyear;
	}
	
	return $years;
}
/**
 * Recupera nome completo usuario
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_serFullName(){
	global $USER, $DB;
	return $USER->firstname.' '.$USER->lastname;
}

/**
 * Funcao responsavel por verificar se relatorio ja existe
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$dataform
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_report_by_organ($dataform){
	global $DB;
	$rs = $DB->get_record('skills_dataform', array('organ'=>$dataform->organ, 'yearprevious'=>$dataform->yearprevious, 'coursemodulesid'=>$dataform->coursemodulesid));
	if($rs)
		return $rs;
	
	return false;
}

/**
 * Funcao responsavel por verificar se usuario ja enviou
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$dataform
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_report_by_user($dataform){
	global $DB;
	$rs = $DB->get_record('skills_dataform', array('userid'=>$dataform->userid, 'yearprevious'=>$dataform->yearprevious, 'coursemodulesid'=>$dataform->coursemodulesid));
	if($rs)
		return $rs;

	return false;
}

/**
 * Funcao responsavel verificar se usuario ja enviou formulario.
 *
 * @package	mod/skills
 * @author Leo Santos<leo.santos@cnj.jus.br>
 * */
function skills_user_form_send($cmid){
	global $USER, $CFG;
	$data = new stdClass;
	$data->userid = $USER->id;
	$data->yearprevious = date("Y")-1;
	$data->coursemodulesid = $cmid;
	
	if($obj = skills_get_report_by_user($data)){
		$message = "Prezado(a) ".skills_get_serFullName().", você já salvou informações referente ao orgão ".strtoupper($obj->organ).".";
		redirect($CFG->wwwroot."/mod/skills/overview.php?id={$cmid}&year=".date("Y")."&perpage=30&page=0", $message, 5); // espera 5 segundos 
	}
	return;
}

/**
 * Funcao responsavel por recuperar usuario por id
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_user_by_id($userid){
	global $DB;
	$rs = $DB->get_record('user', array('id'=>$userid));
	if($rs)
		return $rs;

	return false;
}

/**
 * Funcao responsavel por recuperar relatorios enviados
 *
 * @package		mod/skills
 * @param 		$table
 * @param 		$data
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_reports($page = 0, $perpage = 0, $organ = null, $userid = null, $currentyear = null, $skillsid , $sort = 'dtf.id ASC'){
	global $DB;
	
	$limitsql = '';
	$page = (int) $page;
	$perpage = (int) $perpage;
	
	# Iniciando paginacao
	if($page || $perpage){
		if ($page < 0) {
			$page = 0;
		}else if ($perpage < 1) {
			$perpage = SKILLS_ITEMS_PER_PAGE;
		}
		$limitsql = " LIMIT $perpage" . " OFFSET " . $page * $perpage;
	}
	
	// validando filtro
	$andfilter = false;
	$arrayfilter = array($skillsid);
	
	if($organ){
		$andfilter .= 'AND dtf.organ = ? ';
		array_push($arrayfilter, $organ);
	}
	if($userid){
		$andfilter .= 'AND dtf.userid = ? ';
		array_push($arrayfilter, $userid);
	}
	if($currentyear){
		$andfilter .= 'AND dtf.currentyear = ? ';
		array_push($arrayfilter, $currentyear);
	}
	
	// recupera itens cadastrados
	$reports = $DB->get_records_sql("
			SELECT Concat(u.firstname, ' ', u.lastname) AS userfullname,
			       dtf.*
			FROM   {skills_dataform} dtf
			       INNER JOIN {user} u
			       ON dtf.userid = u.id
			WHERE  dtf.skillsid = ?
			{$andfilter}
			ORDER BY {$sort} {$limitsql};",
			$arrayfilter
	);
	
	return $reports;
}

/**
 * Funcao responsavel por recuperar relatorios de acordo com os ramos da justica
 *
 * @package		mod/skills
 * @param 		$skillsid
 * @param 		$justicebranch
 * @param 		$page
 * @param 		$perpage
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_reports_by_justicebranch($skillsid, $justicebranch = null, $page = 0, $perpage = 0, $sort = 'dtf.id ASC'){
	global $DB;

	$limitsql = '';
	$page = (int) $page;
	$perpage = (int) $perpage;

	# Iniciando paginacao
	if($page || $perpage){
		if ($page < 0) {
			$page = 0;
		}else if ($perpage < 1) {
			$perpage = SKILLS_ITEMS_PER_PAGE;
		}
		$limitsql = " LIMIT $perpage" . " OFFSET " . $page * $perpage;
	}

	// validando filtro
	$andfilter = false;
	$arrayfilter = array($skillsid);

	if($justicebranch){
		$organs = skills_make_str_in(get_justice_branches($justicebranch));
		$andfilter .= 'AND dtf.organ IN('.$organs.') ';
	}
	// Ano corrente
	$andfilter .= 'AND dtf.currentyear IN(?) ';
	array_push($arrayfilter, date('Y'));

	// recupera itens cadastrados
	$reports = $DB->get_records_sql("
			SELECT Concat(u.firstname, ' ', u.lastname) AS userfullname,
			dtf.organ,
			dtf.id,
			dtf.userid,
			dtf.qtdeservers,
			dtf.budgettraining,
			dtf.runvalue,
			dtf.budgetnextyear,
			dtf.createdate,
			dtf.phasestageprogramskills
			FROM   {skills_dataform} dtf
			INNER JOIN {user} u
			ON dtf.userid = u.id
			WHERE  dtf.skillsid = ?
			{$andfilter}
			ORDER BY {$sort} {$limitsql};",
			$arrayfilter
		);

	return $reports;
}
/**
 * Funcao responsavel por filtrar dados conforme valores do filtro
 * @package		mod/skills
 * @param 		$reports
 * @param 		$formdata
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 */
function skills_set_filter_to_phasestageprogramskills($reports, $dataform){
	$arrObjects = [];
	if ($reports){
		foreach ($reports as $report){
			$objphases = json_decode($report->phasestageprogramskills);
			if (property_exists($objphases, $dataform->stageprogramskills)){
				if (property_exists($objphases->{$dataform->stageprogramskills}, $dataform->phasestageprogramskills)){
					$phasereport = trim($objphases->{$dataform->stageprogramskills}->{$dataform->phasestageprogramskills});
					if($phasereport === trim($dataform->situation)){
						$arrObjects[] = $report;
					}
				}
			}
		}
	}
	return $arrObjects;
}

/**
 * Funcao responsavel por recuperar relatorios enviados por envio / orgao
 * @package		mod/skills
 * @param 		$dataformid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 */
function skills_get_details_report($dataformid){
	global $DB;
	return $DB->get_record_sql("SELECT Concat(u.firstname, ' ', u.lastname) AS userfullname,
			       dtf.*
			FROM   {skills_dataform} dtf
			       INNER JOIN {user} u
			       ON dtf.userid = u.id
			WHERE  dtf.id = ?;", array($dataformid));
}

/**
 * Funcao responsavel por formatar opções e retornar string.
 * @package		mod/skills
 * @param 		$str
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 */
function skills_format_options_report($str){
	$arr = explode(';', trim($str));
	$result = "";
	$i = 0;
	foreach ($arr as $a){
		$i ++;
		if(!trim($a) == ""){
			$result .= $i. '. '. $a. "<br />";
		}else 
			$result .= '';
	}
	return $result;
}

/**
 * Funcao responsavel por formatar as fases do estagio do programa de gestao por competencias.
 * @package		mod/skills
 * @param 		$str
 * @param 		$phases
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 */
function skills_format_phasestageprogramskills_report($str, $phases){
	$arr = explode(';', trim($str));
	$result = "";
	$i = 0;
	foreach ($arr as $a){
		$i ++;
		if(!trim($a) == ""){
			$result .= $i. '. '. $a. "<br />";
			$result .= skills_make_table_phasestageprogramskills($phases, $a);
			$phases;
		}else
			$result .= '';
	}
	return $result;
}
/**
 * Funcao responsavel por preparar uma tabela com as fases do estagio do programa de gestao por competencias.
 * @package		mod/skills
 * @param 		$phases
 * @param 		$stage
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 */
function skills_make_table_phasestageprogramskills($phases, $stage){
	$stage = trim($stage);
	$objphases = json_decode($phases);
	$arrphases = skills_generate_array_stagesprogram(); // Obtem array com os estagios disponiveis
	$property = array_search($stage, $arrphases);
	return "<table class='generaltable table-striped table-hover child'>
				<tr>
					<td class='cell'>COMPETÊNCIAS ORGANIZACIONAIS</td>
					<td class='cell'>
						<label>".$objphases->{$property}->organizacionais."</label>
					</td>
				</tr>
				<tr>
					<td class='cell'>COMPETÊNCIAS SETORIAIS</td>
					<td class='cell'>
						<label>".$objphases->{$property}->setoriais."</label>
					</td>
				</tr>
				<tr>
					<td class='cell'>COMPETÊNCIAS INDIVIDUAIS</td>
					<td class='cell'>
						<label>".$objphases->{$property}->individuais."</label>
					</td>
				</tr>
			</table>";
}

/**
 * Funcao responsavel por formatar opções e retornar string com os dados das areas e treinamentos.
 * @package		mod/skills
 * @param 		$str
 * @param 		$dataformid
 */
function skills_format_options_report_with_areas_and_trainings($str, $dataformid){
	$arr = explode(';', trim($str));
	$result = "";
	$i = 0;
	foreach ($arr as $a){
		$i ++;
		if(!trim($a) == ""){
			$result .= $i. '. '. $a. "<br />";
			if($a == 'CURSOS COM MENOS DE 360h'){
				$records = skills_get_dataform_areas($dataformid);
				$table = "<table class='generaltable boxaligncenter'>
							<tbody>
								<tr>
									<th class='header'>MODALIDADE</th>
									<th class='header'>Nº DE CURSOS</th>
									<th class='header'>Nº DE VAGAS</th>
									<th class='header'>Nº DE INSCRITOS</th>
									<th class='header'>Nº DE CAPACITADOS</th>
									<th class='header'>Nº DE REPROVADOS</th>
									<th class='header'>PERCENTUAL DE EVASÃO</th>
									<th class='header'>Nº DE INSTRUTORES INTERNOS</th>
									<th class='header'>Nº DE INSTRUTORES EXTERNOS</th>
								</tr>";
				$j = 0;
				$areasid_previous = 0;
				foreach ($records as $record){
					$r = $j % 2;
					
					if($record->areasid !== $areasid_previous){
						$table .= "<tr class='r{$r}'>
									<td colspan='9' class='areas'>{$record->name_area}</td>
								   </tr>";
					}
					
					$table .= "<tr class='r{$r}'>
								<td class='cell'>{$record->name_modality} </td>
								<td class='cell'>{$record->numbercourses} </td>
								<td class='cell'>{$record->numbervacancies} </td>
								<td class='cell'>{$record->numberenrollees} </td>
								<td class='cell'>{$record->number_trained} </td>
								<td class='cell'>{$record->numberdisapproved} </td>
								<td class='cell'>{$record->evasion} </td>
								<td class='cell'>{$record->numberinternalinstructors} </td>
								<td class='cell'>{$record->numberexternallinstructors} </td>
							  </tr>";
						$j ++;
						$areasid_previous = $record->areasid;
				}
				$table .= "</tbody>
						</table>";
				$result .= $table;
			}else if(trim($a) !== 'OUTRO(S):'){
				$result .= skills_generate_table_report_trainings($a, $dataformid);
			}
		}else
			$result .= '';
	}
	return $result;
}
/**
 * Funcao responsavel por recuperar cursos com menos de 360 horas
 *
 * @package		mod/skills
 * @param 		$dataformid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_dataform_areas($dataformid){
	global $DB;
	
	return $DB->get_records_sql("
			SELECT dtfa.*,
			       a.NAME        AS name_area,
			       m.NAME        AS name_modality
			FROM   {skills_dataform_areas} dtfa
			       INNER JOIN {skills_areas} a
			               ON dtfa.areasid = a.id
			       INNER JOIN {skills_modality} m
			               ON dtfa.modalityid = m.id
			WHERE  dtfa.dataformid = ? 
			ORDER BY dtfa.areasid ASC;", array($dataformid));
}
/**
 * Funcao responsavel por recuperar trainamentos
 *
 * @package		mod/skills
 * @param 		$dataformid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_dataform_trainings($training, $dataformid){
	global $DB;
	
	return $DB->get_records_sql("
			SELECT dtft.*,
			       	t.name        AS name_training 
			FROM   mdl_skills_dataform_training dtft
			       INNER JOIN mdl_skills_training t
			               ON dtft.trainingid = t.id
			WHERE  t.name like ?
			AND dtft.dataformid = ?", array($training, $dataformid));
}

/**
 * Funcao responsavel por gerar tabela de de treinamentos para o detalhamento do relatorio
 *
 * @package		mod/skills
 * @param 		$dataformid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_generate_table_report_trainings($trainings, $dataformid){
	$trainings = trim($trainings); // Removendo espacos em branco
	$records = skills_get_dataform_trainings("%".$trainings."%", $dataformid);
	if($records){
		$table = "<table class='generaltable boxaligncenter'>
					<tbody>
						<tr>
							<th class='header'>ÁREA</th>
							<th class='header'> Nº DE PARTICIPANTES</th>
							<th class='header'>Nº {$trainings}</th>
						</tr>";
		$j = 0;
		foreach ($records as $record){
			$r = $j % 2;
			$table .= "
			<tr class='r{$r}'>
			<td class='cell'>{$record->theme} </td>
			<td class='cell'>{$record->numberparticipants} </td>
			<td class='cell'>{$record->numbertraining} </td>
			</tr>";
			$j ++;
		}
		$table .= "</tbody>
			</table>";
		
		return $table;
	}
	
	return false;
}
/** FUNCOES DO RELATORIO VISAO COMPLETA **/

/**
 * Funcao responsavel por recuperar dados do planejamento anual
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$annualplanning
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 
function skills_get_total_annualplanning($yearprevious, $annualplanning){
	global $DB;
	$sql = "SELECT Count(*) AS total 
			FROM   {skills_dataform} 
			WHERE  yearprevious = ?
			AND	   annualplanning = ?
			AND beinfullreport = 1;";
	 return $DB->get_record_sql($sql, array($yearprevious, $annualplanning))->total;
 }
 
 /**
  * Funcao responsavel por recuperar total de respostas sobre determinado campo e codicoes informadas via parametro.
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$condition
  * @param 		$value
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_total_field_dataform($yearprevious, $field, $condition = '=', $value = 'no'){
 	global $DB;
 	$sql = "SELECT Count(".$field.") AS total
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND 	beinfullreport = 1
			AND	   ".$field." ".$condition." '".$value."';";
 	return $DB->get_record_sql($sql, array($yearprevious))->total;
 }
 
 /**
  * Funcao responsavel por recuperar registros JSON de trainingactionsociety.
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_trainingactionsociety($yearprevious){
 	global $DB;
 	$sql = "SELECT trainingactionsociety 
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND 	beinfullreport = 1
			AND	   trainingactionsociety <> 'no';";
 	return $DB->get_records_sql($sql, array($yearprevious));
 }
 
 /**
  * Funcao responsavel por recuperar TOTAL de servidores e magistrados
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 
 function skills_get_total_servers_and_magistrates($yearprevious){
 	global $DB;
 	$sql = "SELECT 
 					SUM(qtdeservers) AS totalserver,
					SUM(qtdemagistrates) as totalmagistrates
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND beinfullreport = 1;";
 	return $DB->get_record_sql($sql, array($yearprevious));
 }
 
 /**
 * Funcao responsavel por recuperar dados do planejamento anual por metodologia
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$methodology
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 
function skills_get_total_annualplanning_by_methodology($yearprevious, $methodology){
	global $DB;
	$sql = "SELECT Count(*) AS total 
			FROM   {skills_dataform} 
			WHERE  yearprevious = ?
			AND	   annualplanning = ?
			AND    methodology like ?
			AND    beinfullreport = 1;";
	 return $DB->get_record_sql($sql, array($yearprevious, 'yes', $methodology))->total;
 }
 
 /**
  * Funcao responsavel por recuperar total de registro por estrutura de capacitação
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_totaltraining_by_structure($yearprevious, $itemstructure){
 	global $DB;
 	$sql = "SELECT Count(structuretraining) AS total
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND 	beinfullreport = 1
			AND    structuretraining like ?;";
 	return $DB->get_record_sql($sql, array($yearprevious, $itemstructure))->total;
 }
 
 /**
  * Funcao responsavel por recuperar total de registro por estrutura de capacitação por ramo da justica
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @param		$organs
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_totalstructuretraining_by_branch($yearprevious, $itemstructure, $organs){
 	global $DB;
 	$sql = "SELECT Count(structuretraining) AS total
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND 	beinfullreport = 1
 			AND		organ IN(".$organs.")
			AND    structuretraining like ?;";
 	return $DB->get_record_sql($sql, array($yearprevious, $itemstructure))->total;
 }
 
 /**
  * Funcao responsavel por recuperar total de registro por estrutura de capacitação
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_plataform_field_structuretraining($yearprevious){
 	global $DB;
 	$sql = "SELECT organ, structuretraining
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND 	beinfullreport = 1
			AND    structuretraining like '%PLATAFORMA DE APRENDIZAGEM%';";
 	return $DB->get_records_sql($sql, array($yearprevious));
 }
 
 /**
  * Funcao responsavel por recuperar total de registro de valores executados por acao formativa
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_plataform_field_runvalbyformaction($yearprevious, $organs = null){
 	global $DB;
 	$q = "";
 	if($organs){
 		$q = "AND organ IN(".$organs.")";
 	}
 	$sql = "SELECT runvalbyformaction
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND beinfullreport = 1 {$q};";
 	return $DB->get_records_sql($sql, array($yearprevious));
 }
 
 /**
  * Funcao responsavel por gerar tabela com total de recursos por acao formativa
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_make_table_runvalbyformaction($yearprevious, $organs = null){
 	
 	$runvalbyform = skills_get_plataform_field_runvalbyformaction($yearprevious, $organs);
	
	$tcoursepresmin360 = 0; $tcourseeadmin360 = 0; $tcoursesemimin360 = 0; $pos = 0; $palestras = 0; $congressos = 0;
	$icoursepresmin360 = 0; $icourseeadmin360 = 0; $icoursesemimin360 = 0; $ipos = 0; $ipalestras = 0; $icongressos = 0;
	
	$encontros = 0; $seminarios = 0; $foruns = 0; $workshops = 0; $graduacoes = 0; $outros = 0;
	$iencontros = 0; $iseminarios = 0; $iforuns = 0; $iworkshops = 0; $igraduacoes = 0; $ioutros = 0;
	
	foreach ($runvalbyform as $runval){
		$objval = json_decode($runval->runvalbyformaction);
		$tcoursepresmin360 += skills_remove_mask_money($objval->courses360h->presencial);
		$icoursepresmin360 = $objval->courses360h->presencial ? $icoursepresmin360 + 1 : $icoursepresmin360;
		$tcourseeadmin360 += skills_remove_mask_money($objval->courses360h->ead);
		$icourseeadmin360 = $objval->courses360h->ead ? $icourseeadmin360 + 1 : $icourseeadmin360;
		$tcoursesemimin360 += skills_remove_mask_money($objval->courses360h->semipresencial);
		$icoursesemimin360 = $objval->courses360h->semipresencial ? $icoursesemimin360 + 1 : $icoursesemimin360;
		$pos += skills_remove_mask_money($objval->pos);
		$ipos = $objval->pos ? $ipos + 1 : $ipos;
		$palestras += skills_remove_mask_money($objval->palestras);
		$ipalestras = $objval->palestras ? $ipalestras + 1 : $ipalestras;
		$congressos += skills_remove_mask_money($objval->congressos);
		$icongressos = $objval->congressos ? $icongressos + 1 : $icongressos;
		$encontros += skills_remove_mask_money($objval->encontros);
		$iencontros = $objval->encontros ? $iencontros + 1 : $iencontros;
		$seminarios += skills_remove_mask_money($objval->seminarios);
		$iseminarios = $objval->seminarios ? $iseminarios + 1 : $iseminarios;
		$foruns += skills_remove_mask_money($objval->foruns);
		$iforuns = $objval->foruns ? $iforuns + 1 : $iforuns;
		$workshops += skills_remove_mask_money($objval->workshop);
		$iworkshops = $objval->foruns ? $iworkshops + 1 : $iforuns;
		$graduacoes += skills_remove_mask_money($objval->graduacao);
		$igraduacoes = $objval->foruns ? $igraduacoes + 1 : $iforuns;
		$outros += skills_remove_mask_money($objval->outros);
		$ioutros = $objval->outros ? $ioutros + 1 : $ioutros;
	}
	
	return "<table class='generaltable boxaligncenter items_questions'>
				<tr>
					<th class='header table-head0-areas'>TOTAL DE ÓRGÃOS POR AÇÃO FORMATIVA</th>
					<th class='header table-head0-orcamento'>TOTAL EM DESPESA REALIZADA EM {$yearprevious}</th>
				</tr>
				<tr class='r0'>
					<td class='cell table-head1-areas' align='center'>CURSOS PRESENCIAIS COM MENOS DE 360H ({$icoursepresmin360})</td>
					<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($tcoursepresmin360)."</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-head1-areas' align='center'>CURSOS EAD COM MENOS DE 360H ({$icourseeadmin360})</td>
					<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($tcourseeadmin360)."</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-head1-areas' align='center'>CURSOS SEMIPRESENCIAIS COM MENOS DE 360H ({$icoursesemimin360})</td>
					<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($tcoursesemimin360)."</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-head0-areas' align='center'>PÓS-GRADUAÇÃO ({$ipos})</td>
					<td class='cell table-head0-orcamento' align='center'>R$ ".skills_add_mask_money($pos)."</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-head1-areas' align='center'>PALESTRAS ({$ipalestras})</td>
					<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($palestras)."</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-head0-areas' align='center'>CONGRESSOS ({$icongressos})</td>
					<td class='cell table-head0-orcamento' align='center'>R$ ".skills_add_mask_money($congressos)."</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-head1-areas' align='center'>ENCONTROS ({$iencontros})</td>
					<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($encontros)."</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-head0-areas' align='center'>SEMINÁRIOS ({$iseminarios})</td>
					<td class='cell table-head0-orcamento' align='center'>R$ ".skills_add_mask_money($seminarios)."</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-head1-areas' align='center'>FÓRUNS ({$iforuns})</td>
					<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($foruns)."</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-head0-areas' align='center'>WORKSHOP ({$iworkshops})</td>
					<td class='cell table-head0-orcamento' align='center'>R$ ".skills_add_mask_money($workshops)."</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-head1-areas' align='center'>GRADUAÇÃO ({$igraduacoes})</td>
					<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($graduacoes)."</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-head0-areas' align='center'>OUTRAS ({$ioutros})</td>
					<td class='cell table-head0-orcamento' align='center'>R$ ".skills_add_mask_money($outros)."</td>
				</tr>
			</table>";
 }
 
 /**
  * Funcao responsavel por recuperar total de registro de outros valores executados por acao formativa
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_plataform_field_othersvalrun($yearprevious, $organs = null){
 	global $DB;
 	$q = "";
 	if($organs){
 		$q = "AND organ IN(".$organs.")";
 	}
 	$sql = "SELECT othersvalrun
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND beinfullreport = 1 {$q};";
 	return $DB->get_records_sql($sql, array($yearprevious));
 }
 
 /**
  * Funcao responsavel por gerar tabela com total de recursos por acao formativa de outros valores executados
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 function skills_make_table_othersvalrun($yearprevious, $organs = null){
 	
 		$othersrunval = skills_get_plataform_field_othersvalrun($yearprevious, $organs);
 		$coffee = 0; $diarias = 0; $others = 0;
 		$icoffee = 0; $idiarias = 0; $iothers = 0;
 		
 		foreach ($othersrunval as $otherrunval){
 			$objother = json_decode($otherrunval->othersvalrun);
 			$coffee += skills_remove_mask_money($objother->coffee);
 			$icoffee = $objother->coffee ? $icoffee + 1 : $icoffee;
 			$diarias += skills_remove_mask_money($objother->diarias);
 			$idiarias = $objother->diarias ? $idiarias + 1 : $idiarias;
 			$others += skills_remove_mask_money($objother->others);
 			$iothers = $objother->others ? $iothers + 1 : $iothers;
 		}
 		
 		return "<table class='generaltable boxaligncenter items_questions'>
		 			<tr>
		 				<th class='header table-head0-areas'>TOTAL DE ÓRGÃOS POR AÇÃO FORMATIVA</th>
		 				<th class='header table-head0-orcamento'>TOTAL EM DESPESA REALIZADA EM {$yearprevious}</th>
		 			</tr>
		 			<tr class='r0'>
		 				<td class='cell table-head1-areas' align='center'>COFFEE BREAK ({$icoffee})</td>
		 				<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($coffee)."</td>
		 			</tr>
		 			<tr class='r1'>
		 				<td class='cell table-head0-areas' align='center'>DIÁRIAS E PASSAGENS ({$idiarias})</td>
		 				<td class='cell table-head0-orcamento' align='center'>R$ ".skills_add_mask_money($diarias)."</td>
		 			</tr>
		 			<tr class='r0'>
		 				<td class='cell table-head1-areas' align='center'>OUTRAS ({$iothers})</td>
		 				<td class='cell table-head1-orcamento' align='center'>R$ ".skills_add_mask_money($others)."</td>
		 			</tr>
		 		</table>";
 }
 
 /**
  * Funcao responsavel por gerar linhas de tabela de estrutura de capacitacao com total de registros por ramo da justica
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$rowstable
  * @copyright	CEAJUD - CNJ
  * */
 function skills_make_rowstable_totalstructuretraining_by_branch($yearprevious, $itemstructure){
 	
 	$inSuperiores = skills_make_str_in(get_justice_branches('superiores'));
 	$inEleitoral = skills_make_str_in(get_justice_branches('eleitoral'));
 	$inTrabalho = skills_make_str_in(get_justice_branches('trabalho'));
 	$inFederal = skills_make_str_in(get_justice_branches('federal'));
 	$inEstadual = skills_make_str_in(get_justice_branches('estadual'));
 	$inMilitar = skills_make_str_in(get_justice_branches('militar'));
 	// make table
 	$rowtable = "<tr class='r1'>
					<td class='cell table-row-superior' style='text-align: left;'>JUSTIÇA SUPERIOR</td>
					<td class='cell table-row-superior' align='center'>".skills_get_totalstructuretraining_by_branch($yearprevious, $itemstructure, $inSuperiores)."</td>
				</tr>
 				<tr class='r0'>
					<td class='cell table-row-eleitoral' style='text-align: left;'>JUSTIÇA ELEITORAL</td>
					<td class='cell table-row-eleitoral' align='center'>".skills_get_totalstructuretraining_by_branch($yearprevious, $itemstructure, $inEleitoral)."</td>
				</tr>
 				<tr class='r1'>
					<td class='cell table-row-trabalho' style='text-align: left;'>JUSTIÇA TRABALHO</td>
					<td class='cell table-row-trabalho' align='center'>".skills_get_totalstructuretraining_by_branch($yearprevious, $itemstructure, $inTrabalho)."</td>
				</tr>
 				<tr class='r0'>
					<td class='cell table-row-federal' style='text-align: left;'>JUSTIÇA FEDERAL</td>
					<td class='cell table-row-federal' align='center'>".skills_get_totalstructuretraining_by_branch($yearprevious, $itemstructure, $inFederal)."</td>
				</tr>
 				<tr class='r1'>
					<td class='cell table-row-estadual' style='text-align: left;'>JUSTIÇA ESTADUAL</td>
					<td class='cell table-row-estadual' align='center'>".skills_get_totalstructuretraining_by_branch($yearprevious, $itemstructure, $inEstadual)."</td>
				</tr>
 				<tr class='r0'>
					<td class='cell table-row-militar' style='text-align: left;'>JUSTIÇA MILITAR</td>
					<td class='cell table-row-militar' align='center'>".skills_get_totalstructuretraining_by_branch($yearprevious, $itemstructure, $inMilitar)."</td>
				</tr>";
 	return $rowtable;
 }
 /**
  * Funcao responsavel por remover mascara string e retornar um float
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$itemstructure
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
function skills_remove_mask_money($value){
	$number = str_replace("R$ ", "", $value);
	$number = str_replace(".", "", $number);
	$number = str_replace(",", ".", $number);
	return floatval($number);
}

/**
 * Funcao responsavel por adicionar mascara de moeda em um numero e retornar uma string
 *
 * @package	mod/skills
 * @param 		$yearprevious
 * @param float	$value
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 	string $number
 * @copyright	CEAJUD - CNJ
 * */
function skills_add_mask_money($value){
	return number_format($value,'2',',','.');
}
 
 /**
  * Funcao responsavel por recuperar todas as outras acoes
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	String
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_others_methodologies($yearprevious){
 	global $DB;
 	
 	$sql = "SELECT id, organ, methodology
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
			AND	   annualplanning = ?
 			AND    beinfullreport = 1;";
 	return $DB->get_records_sql($sql, array($yearprevious, 'yes'));
 }
 /**
  * Funcao responsavel por extrair outras metodologias e retorna array de elementos
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	Array $rsother
  * @copyright	CEAJUD - CNJ
  * */
 function skills_extract_others_methodologies($yearprevious){
 	
 	$methodologies = skills_generate_array_methodologies();
 	$othesmethodologies = skills_get_others_methodologies($yearprevious);
 	$rsother = [];
 	foreach ($othesmethodologies as $other){
 		$meths = explode(';', $other->methodology);
 		if (is_array($meths)){
 			foreach ($meths as $meth){
 				$meth = trim($meth);
 				if(!in_array($meth, $methodologies)){
 					if ($meth){
 						$rsother[] = strtoupper($other->organ). " - ". $meth;
 					}
 				}
 			}
 		}
 	}
 	return $rsother;
 }
 /**
  * Funcao responsavel por gerar tabela com outras metologias
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	String
  * @copyright	CEAJUD - CNJ
  * */
 function skills_generate_table_others_methodologies($yearprevious){
 	
 	$items = skills_extract_others_methodologies($yearprevious);
 	$count = 0;
 	$table = html_writer::start_tag("table", array('class'=>'generaltable boxaligncenter items_questions other_methodologies'));
 	$table .= html_writer::start_tag("tr");
 	$table .= html_writer::start_tag("th", array('class'=>'header table-head0-areas'));
 	$table .= "OUTRAS (".count($items).")";
 	$table .= html_writer::end_tag("th");
 	$table .= html_writer::end_tag("tr");
 	foreach ($items as $item){
 		$r = $count % 2;
 		$table .= html_writer::start_tag("tr");
 		$table .= html_writer::start_tag("td", array('class'=> "cell table-r$r-areas"));
 		$table .= $item;
 		$table .= html_writer::end_tag("td");
 		$table .= html_writer::end_tag("tr");
 		$count ++;
 	}
 	$table .= html_writer::end_tag("table");
 	return $table;
 }
 
 
 /**
  * Funcao responsavel por recuperar total de relatorios enviados e total de relatorios finalizados.
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 		$total
  * @copyright	CEAJUD - CNJ
  * */
 
 function skills_get_total_reportsend_and_reportfinish($yearprevious){
 	global $DB;
 	$sql = "SELECT Count(*) AS totalsend,
 				   Sum(savefinish) AS totalsavefinish,
 				   Sum(beinfullreport) AS beinfullreport
			FROM   {skills_dataform}
			WHERE  yearprevious = ?;";
 	return $DB->get_record_sql($sql, array($yearprevious));
 }

/**
 * Funcao responsavel por recuperar total de registros por area
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 
function skills_get_total_fields_by_area($yearprevious, $areaid){
	global $DB;
	$sql = "SELECT  sum(da.numbercourses) 	 AS totalcourses, 
					sum(da.numbervacancies)  AS totalvacancies,
					sum(da.numberenrollees)  AS totalenrollees,
					sum(da.number_trained)   AS totaltrained,
					sum(da.numberdisapproved)   AS totaldisapproved
			FROM   {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d
			ON da.dataformid = d.id
			WHERE d.yearprevious = ?
			AND d.beinfullreport = 1
			AND da.areasid = ?;";
			
	 return $DB->get_record_sql($sql, array($yearprevious, $areaid));
 }

 /**
 * Funcao responsavel por recuperar total de registros por area
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$organ
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_fields_by_justice_branch($yearprevious, $organs){
	global $DB;
	$sql = "SELECT  sum(numbercourses) 	 AS totalcourses, 
					sum(numbervacancies) AS totalvacancies,
					sum(numberenrollees) AS totalenrollees,
					sum(number_trained)  AS totaltrained,
					sum(numberdisapproved)  AS totaldisapproved
			FROM   {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d
			ON da.dataformid = d.id
			WHERE d.yearprevious = ?
			AND d.beinfullreport = 1
			AND d.organ in(".$organs.");";
			
	 return $DB->get_record_sql($sql, array($yearprevious));
}
 
 /**
 * Funcao responsavel por recuperar total de registros de pos-graduacao (trainingid=1)
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$organs
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_fields_training_by_justice_branch($yearprevious, $organs, $trainingid){
	global $DB;
	$sql = "SELECT Sum(dt.numberparticipants)		AS totalparticipants,
       		Sum(dt.numbertraining)					AS totalnumbertraining 
			FROM   {skills_dataform_training} dt 
			       INNER JOIN {skills_dataform} d 
			               ON dt.dataformid = d.id 
			WHERE  d.yearprevious = ? 
			AND d.beinfullreport = 1
			       AND dt.trainingid = ?
			       AND d.organ IN(".$organs.");";
			
	 return $DB->get_record_sql($sql, array($yearprevious, $trainingid));
 }
 
 /**
  * Funcao responsavel por recuperar total de registros de trainings cruzando com ramos da justica e areas
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$organs
  * @param 		$trainingid
  * @param 		$areaname
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	boolean
  * @copyright	CEAJUD - CNJ
  * */
 function skills_get_total_trainings_by_area_and_justice_branch($yearprevious, $trainingid, $areaname, $organs){
 	global $DB;
 	$areaname = trim($areaname)."%";
 	$sql = "SELECT Sum(dt.numberparticipants)	AS totalparticipants,
       		Sum(dt.numbertraining)		AS totalnumbertraining
			FROM   {skills_dataform_training} dt
			       INNER JOIN {skills_dataform} d
			               ON dt.dataformid = d.id
			WHERE  d.yearprevious = ?
			AND d.beinfullreport = 1
			       AND dt.trainingid = ?
 				   AND dt.theme LIKE ?
			       AND d.organ IN(".$organs.");";
 		
 	return $DB->get_record_sql($sql, array($yearprevious, $trainingid, $areaname));
 }
 /**
 * Funcao responsavel por recuperar registros Tribunais, total capacitados(pos + cursos360), media horas-aulas, dotacao...
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_dataform($yearprevious){
	global $DB;
	$sql = "SELECT  d.id,
					d.organ, 
			       (SELECT Sum(dt.numberparticipants) 
			        FROM   {skills_dataform_training} dt 
			        WHERE  dt.dataformid = d.id 
			               AND trainingid = 1)   AS participants_pos, 
			       (SELECT Sum(da.number_trained) 
			        FROM   {skills_dataform_areas} da 
			        WHERE  da.dataformid = d.id) AS participants_cursos,
			        (SELECT Sum(da.numbercourses) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 1) AS courses_ead,
					(SELECT Sum(da.numbercourses) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 2) AS courses_classroom,
					(SELECT Sum(da.numbercourses) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 3) AS courses_sem,
					(SELECT Sum(da.numbervacancies) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 1) AS numbervacancies_ead,
					(SELECT Sum(da.numbervacancies) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 2) AS numbervacancies_classroom,
					(SELECT Sum(da.numbervacancies) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 3) AS numbervacancies_sem,
					(SELECT Sum(da.numberenrollees) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 1) AS numberenrollees_ead,
					(SELECT Sum(da.numberenrollees) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 2) AS numberenrollees_classroom,
					(SELECT Sum(da.numberenrollees) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 3) AS numberenrollees_sem,
					(SELECT Sum(da.number_trained) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 1) AS trained_ead,
					(SELECT Sum(da.number_trained) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 2) AS trained_classroom,
					(SELECT Sum(da.number_trained) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 3) AS trained_sem,
					(SELECT Sum(da.numberdisapproved) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 1) AS numberdisapproved_ead,
					(SELECT Sum(da.numberdisapproved) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 2) AS numberdisapproved_classroom,
					(SELECT Sum(da.numberdisapproved) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id AND da.modalityid = 3) AS numberdisapproved_sem,
					(SELECT Sum(da.numberinternalinstructors) 
					FROM   {skills_dataform_areas} da 
					WHERE  da.dataformid = d.id) AS total_numberinternalinstructors,
					(SELECT Sum(da.numberexternallinstructors) 
					FROM   mdl_skills_dataform_areas da 
					WHERE  da.dataformid = d.id) AS total_numberexternallinstructors, 
			       d.budgettraining, 
			       d.runvalue 
			FROM   {skills_dataform} d
			WHERE d.yearprevious = ?
			AND  d.beinfullreport = 1;";
			
	 return $DB->get_records_sql($sql, array($yearprevious));
 }
 
 /**
  * Funcao responsavel por recuperar comentarios e sugestoes.
  *
  * @package	mod/skills
  * @param		string $yearprevious
  * @author 	Leo Santos<leo.santos@cnj.jus.br>
  * @return 	string $comments
  * */
 function skills_get_comments_by_yearprevious($yearprevious) {
 	global $DB;
 	$sql = "SELECT id, organ, comments, sugestions FROM {skills_dataform} WHERE yearprevious = ? AND beinfullreport = 1 AND (comments <> '' OR sugestions <> '');";
 	
 	return $DB->get_records_sql($sql, array($yearprevious));
 }

 /**
 * Funcao responsavel por recuperar total dotacao orcamentaria e despesa realizada.
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_fields_dataform($yearprevious){
	global $DB;
	$sql = "SELECT Sum(Replace(Substring(d.budgettraining, 4), '.', '')) AS totalbudgettraining, 
			       Sum(Replace(Substring(d.runvalue, 4), '.', ''))       AS totalrunvalue 
			FROM   {skills_dataform} d 
			WHERE  d.yearprevious = ?;";
			
	 return $DB->get_record_sql($sql, array($yearprevious));
 }
 /**
 * Funcao responsavel por recuperar dados do programa institucionalizado gestao por competencias
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$programskills
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 
function skills_get_total_programskills($yearprevious, $programskills){
	global $DB;
	$sql = "SELECT Count(*) AS total 
			FROM   {skills_dataform} 
			WHERE  yearprevious = ?
			AND 	beinfullreport = 1
			AND	   programskills = ?;";
	 return $DB->get_record_sql($sql, array($yearprevious, $programskills))->total;
 }
 
 /**
 * Funcao responsavel por recuperar dados das avaliacoes
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$evaluation
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 
function skills_get_total_evaluation($yearprevious, $evaluation){
	global $DB;
	$sql = "SELECT Count(*) AS total 
			FROM   {skills_dataform} 
			WHERE  yearprevious = ?
			AND 	beinfullreport = 1
			AND	   evaluation = ?;";
	 return $DB->get_record_sql($sql, array($yearprevious, $evaluation))->total;
 }
 /**
 * Funcao responsavel por recuperar dados de avaliacao por tipo
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$typeevaluation
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 
function skills_get_total_evaluation_by_typeevaluation($yearprevious, $typeevaluation){
	global $DB;
	$sql = "SELECT Count(*) AS total 
			FROM   {skills_dataform} 
			WHERE  yearprevious = ?
			AND 	beinfullreport = 1
			AND	    evaluation = ?
			AND    typeevaluation like ?;";
	 return $DB->get_record_sql($sql, array($yearprevious, 'yes', $typeevaluation))->total;
 }
 
 /**
  * Funcao responsavel por recuperar dados de estagio do programa de gestao por competencias
  *
  * @package	mod/skills
  * @param 		$yearprevious
  * @param 		$stageprogram
  * @author		Leo Santos<leo.santos@cnj.jus.br>
  * @return 	$total
  * @copyright	CEAJUD - CNJ
  * */
 
 function skills_get_total_stageprogramskills($yearprevious, $stageprogram){
 	global $DB;
 	$sql = "SELECT Count(stageprogramskills) AS total
			FROM   {skills_dataform}
			WHERE  yearprevious = ?
 			AND 	beinfullreport = 1
			AND	   programskills = ?
			AND    stageprogramskills like ?;";
 	return $DB->get_record_sql($sql, array($yearprevious, 'yes', $stageprogram))->total;
 }

/**
 * Funcao responsavel por recuperar dados de avaliacao do tipo outros
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 
function skills_get_total_evaluation_others_typeevaluation($yearprevious){
	global $DB;
	$sql = "SELECT Count(*) AS total 
			FROM   {skills_dataform} 
			WHERE  yearprevious = ?
			AND 	beinfullreport = 1
			AND	   evaluation = ?
			AND    LENGTH(typeevaluation) > 48;";
	 return $DB->get_record_sql($sql, array($yearprevious, 'yes'))->total;
 }
 
 /**
 * Funcao responsavel por recuperar total revisao orcamentaria currentyear por ramo da justica.
 *
 * @package		mod/skills
 * @param 		$currentyear
 * @param 		$organ
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_budget_justice_branch($currentyear, $organs){
	global $DB;
	$sql = "SELECT Sum(Replace(Substring(d.budgetnextyear, 4), '.', '')) AS total 
			FROM   {skills_dataform} d 
			WHERE  d.currentyear = ? 
			AND    d.beinfullreport = 1
			AND    d.organ IN(".$organs.");";
	 return $DB->get_record_sql($sql, array($currentyear))->total;
}

 /**
 * Funcao responsavel por recuperar total revisao orcamentaria currentyear da justica militar.
 *
 * @package		mod/skills
 * @param 		$currentyear
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_budget_justice_militar($currentyear){
	global $DB;
	$sql = "SELECT Sum(Replace(Substring(d.budgetnextyear, 4), '.', '')) AS total 
			FROM   {skills_dataform} d 
			WHERE  (d.organ LIKE '%tjmsp%'
			        OR d.organ LIKE '%tjmrs%'
			        OR d.organ LIKE '%tjm.mg%'
			        OR d.organ = 'stm')
			AND d.currentyear = ?;";
	 return $DB->get_record_sql($sql, array($currentyear))->total;
}

 /**
 * Funcao responsavel por recuperar total cursos, vagas e capacitados por ramo da justica.
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$organ
 * @param 		$areasid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
 function skills_get_total_fields_by_area_and_justice_branch($yearprevious, $organs, $areasid){
 	global $DB;
	$sql = "SELECT  sum(da.numbercourses) 	AS totalcourses, 
					sum(da.numbervacancies) AS totalvacancies,
					sum(da.numberenrollees) AS totalenrollees,
					sum(da.number_trained)  AS totaltrained,
					sum(da.numberdisapproved)  AS totaldisapproved
			FROM {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d
			ON da.dataformid = d.id
			WHERE d.yearprevious = ?
			AND d.beinfullreport = 1
			AND d.organ IN(".$organs.")
			AND da.areasid = ?;";
 	return $DB->get_record_sql($sql, array($yearprevious, $areasid));
 }

/**
 * Funcao responsavel por recuperar total de campos por area e modalidade.
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$areasid
 * @param 		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
  function skills_get_total_fieldsbyarea_and_modality($yearprevious, $areasid, $modalityid){
 	global $DB;
  	$sql = "SELECT  sum(da.numbercourses) 	AS totalcourses, 
					sum(da.numbervacancies) AS totalvacancies,
					sum(da.numberenrollees) AS totalenrollees,
					sum(da.number_trained)  AS totaltrained,
					sum(da.numberdisapproved)  AS totaldisapproved,
					sum(da.numberexternallinstructors)  AS totalexternallinstructors,
					sum(da.numberinternalinstructors)  AS totalinternalinstructors
			FROM   {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d
			ON da.dataformid = d.id
			WHERE d.yearprevious = ?
  			AND d.beinfullreport = 1
			AND da.areasid = ?
			AND da.modalityid = ?;";
 	return $DB->get_record_sql($sql, array($yearprevious, $areasid, $modalityid));
  }
  
 /**
 * Funcao responsavel por recuperar total de campos por e modalidade para ramos da justica.
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$organs
 * @param 		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
  function skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $organs, $modalityid){
 	global $DB;
  	$sql = "SELECT  sum(da.numbercourses) 	 	AS totalcourses, 
					sum(da.numbervacancies) 	AS totalvacancies,
					sum(da.numberenrollees) 	AS totalenrollees,
					sum(da.number_trained)  	AS totaltrained,
					sum(da.numberdisapproved)	AS totaldisapproved,
					sum(da.numberexternallinstructors)  AS totalexternallinstructors,
					sum(da.numberinternalinstructors)  AS totalinternalinstructors
			FROM   {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d
			ON da.dataformid = d.id
			WHERE d.yearprevious = ?
  			AND d.beinfullreport = 1
			AND d.organ IN(".$organs.")
			AND da.modalityid = ?;";
 	return $DB->get_record_sql($sql, array($yearprevious, $modalityid));
  }
  
/**
 * Funcao responsavel por recuperar total de relatorios por ramo da justica.
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$organs
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 **/
  function skills_get_total_dataformbybranchjustice($yearprevious, $organs){
  	global $DB;
  	  	$sql = "SELECT count(*) AS totalbybranch
			FROM   {skills_dataform}
			WHERE yearprevious = ?
  			AND beinfullreport = 1
			AND organ IN(".$organs.");";
 	return $DB->get_record_sql($sql, array($yearprevious));
  }
  
  /**
 * Funcao responsavel por recuperar total de campos por e modalidade de todos os tribunais.
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		boolean
 * @copyright	CEAJUD - CNJ
 * */
  function skills_get_total_fields_report($yearprevious, $modalityid){
 	global $DB;
 	$sql = "SELECT Sum(da.numbercourses)              AS totalcourses,
			       Sum(da.numbervacancies)            AS totalvacancies,
			       Sum(da.numberenrollees)            AS totalenrollees,
			       Sum(da.number_trained)             AS totaltrained,
			       Sum(da.numberdisapproved)             AS totaldisapproved,
			       Sum(da.numberexternallinstructors) AS totalexternallinstructors,
			       Sum(da.numberinternalinstructors)  AS totalinternalinstructors
			FROM   {skills_dataform_areas} da
			       INNER JOIN {skills_dataform} d
			               ON da.dataformid = d.id
			WHERE  d.yearprevious = ?
 			AND d.beinfullreport = 1
			       AND da.modalityid = ?;";
 	return $DB->get_record_sql($sql, array($yearprevious, $modalityid));
 }
  
/**
 * Funcao responsavel por formatar string em valores moedas.
 *
 * @package		mod/skills
 * @param 		$value
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
 function skills_set_format_moeda($value){
 	// Recebe formato R$ 50.000,00
 	// Retorna 50000.00
 	return str_replace(',', '.', str_replace('.', '', substr($value, 3)));
 }
 
 /**
 * Funcao responsavel por recuperar numero que representa evasao 
 *
 * @package		mod/skills
 * @param 		$dataformid
 * @param		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_evasion_by_dataformid($dataformid, $modalityid){
	global $DB;
 	$sql = "SELECT *
			FROM   {skills_dataform_areas}
			WHERE  dataformid = ?
			       AND modalityid = ?;";
			       
 	$objPercentual = $DB->get_records_sql($sql, array($dataformid, $modalityid));
 	
 	return skills_calc_total_evasion($objPercentual);}

 /**
 * Funcao responsavel por recuperar percentual de evasao 
 *
 * @package		mod/skills
 * @param 		$dataformid
 * @param		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_percentual_evasion_by_dataformid($dataformid, $modalityid){
	global $DB;
 	$sql = "SELECT *
			FROM   {skills_dataform_areas}
			WHERE  dataformid = ?
			       AND modalityid = ?;";
			       
 	$objPercentual = $DB->get_records_sql($sql, array($dataformid, $modalityid));
 	
 	return skills_calc_percentual_evasion($objPercentual);
}

 /**
 * Funcao responsavel por recuperar numero que representa evasao por area
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$areaid
 * @param		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_evasion_by_area($yearprevious, $areaid, $modalityid){
	global $DB;
 	$sql = "SELECT 
	 			da.id, 
	 			da.numbervacancies,
	 			da.numberenrollees,
	 			da.number_trained,
	 			da.numberdisapproved,
	 			da.evasion, 
	 			d.id, 
	 			d.yearprevious
			FROM   {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d
			ON da.dataformid = d.id
			WHERE d.yearprevious = ?
 			AND d.beinfullreport = 1
			AND da.areasid = ?
			AND da.modalityid = ?;";
			       
 	$objPercentual = $DB->get_records_sql($sql, array($yearprevious, $areaid, $modalityid));
 	
 	return skills_calc_total_evasion($objPercentual);
}

/**
 * Funcao responsavel por recuperar percentual de evasao por area
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$areaid
 * @param		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_percentual_evasion_by_area($yearprevious, $areaid, $modalityid){
	global $DB;
 	$sql = "SELECT  
	 			da.id, 
	 			da.numbervacancies, 
	 			da.numberenrollees,
		 		da.number_trained,
		 		da.numberdisapproved,
	 			da.evasion, 
	 			d.id, 
	 			d.yearprevious
			FROM   {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d
			ON da.dataformid = d.id
			WHERE d.yearprevious = ?
 			AND d.beinfullreport = 1
			AND da.areasid = ?
			AND da.modalityid = ?;";
			       
 	$objPercentual = $DB->get_records_sql($sql, array($yearprevious, $areaid, $modalityid));
 	
 	return skills_calc_percentual_evasion($objPercentual);
}
 /**
 * Funcao responsavel por recuperar numero que representa evasao por ramo da justica
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$organ1
 * @param 		$organ2
 * @param		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_total_evasion_by_justice_branch($yearprevious, $modalityid, $organs){
	global $DB;
	
 	$sql = "SELECT da.id as dataformid,
			       da.numbervacancies,
			       da.numberenrollees,
			       da.numberdisapproved,
			       da.evasion,
			       d.id,
			       d.yearprevious
			FROM {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d ON da.dataformid = d.id
			WHERE d.organ IN (".$organs.")
			  AND d.yearprevious = ?
			  AND d.beinfullreport = 1
			  AND da.modalityid = ?;";
			       
 	$objperc = $DB->get_records_sql($sql, array($yearprevious, $modalityid));
 	
	return skills_calc_total_evasion($objperc);
}


/**
 * Funcao responsavel por recuperar percentual de evasao por ramo da justica
 *
 * @package		mod/skills
 * @param 		$yearprevious
 * @param 		$organ1
 * @param 		$organ2
 * @param		$modalityid
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_get_percentual_evasion_by_justice_branch($yearprevious, $modalityid, $organs){
	global $DB;
	
 	$sql = "SELECT da.id as dataformid,
			       da.numbervacancies,
			       da.numberenrollees,
			       da.numberdisapproved,
			       da.evasion,
			       d.id,
			       d.yearprevious
			FROM {skills_dataform_areas} da
			INNER JOIN {skills_dataform} d ON da.dataformid = d.id
			WHERE d.organ IN(".$organs.")
			  AND d.yearprevious = ?
			  AND d.beinfullreport = 1
			  AND da.modalityid = ?;";
			       
 	$objpercent = $DB->get_records_sql($sql, array($yearprevious, $modalityid));
 	
 	return skills_calc_percentual_evasion($objpercent);
}

/**
 * Funcao responsavel por calcular total de vagas com base no percentual de evasao
 *
 * @package		mod/skills
 * @param 		$objPercentual
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_calc_total_evasion($objPercentual){
	$tevasion = 0;
 	foreach ($objPercentual as $percent) {
 		$evasion = str_replace('%', '', $percent->evasion);
		
		$nvasion = $percent->numberenrollees * ($evasion / 100);
		
		$tevasion += $nvasion;
	 }
	return round($tevasion);
}

/**
 * Funcao responsavel por calcular percentual de evasao com base no percentual informado
 *
 * @package		mod/skills
 * @param 		$objPercentual
 * @author		Leo Santos<leo.santos@cnj.jus.br>
 * @return 		$result
 * @copyright	CEAJUD - CNJ
 * */
function skills_calc_percentual_evasion($objPercentual){
	
	$tnvasion = 0;
 	$tinscritos = 0;
 	
	if($objPercentual){
	 	foreach ($objPercentual as $percent) {
	 		$evasion = str_replace('%', '', $percent->evasion);
			
			$nevasion = $percent->numberenrollees * ($evasion / 100);	// Descobre total de evadidos
			
			$tnvasion += $nevasion;
			$tinscritos += $percent->numberenrollees;
		 }
		return round(($tnvasion * 100) / $tinscritos);
	}
	return 0;
}

/**
 * Funcao responsavel por marcar checkbox
 *
 * @package	mod/skills
 * @param	string $dbfilds
 * @param 	string $valuecheck
 * @author 	Leo Santos<leo.santos@cnj.jus.br>
 * @return 	$checked
 * */
function skills_check_dataform($dbfilds, $valuecheck) {
	if($dbfilds){
		
		$valuecheck = trim($valuecheck);
		$dbfilds = explode(';', $dbfilds);
		$dbfilds = array_map('trim', $dbfilds);
		
		if(in_array($valuecheck, $dbfilds)){
			return "checked='checked'";
		}
		return false;
	}
}

/**
 * Funcao responsavel por recuperar texto escrito nos campos: Outros(as).
 *
 * @package	mod/skills
 * @param	string $dbfilds
 * @param 	array $optfields
 * @author 	Leo Santos<leo.santos@cnj.jus.br>
 * @return 	string $others
 * */
function skills_get_fields_others($dbfilds, $optfields) {
	if($dbfilds){

		$dbfilds = explode(';', $dbfilds);
		$dbfilds = array_map('trim', $dbfilds);
		
		$strother = '';
		foreach ($dbfilds as $field){
			if(!in_array($field, $optfields)){
				$strother .= $field;
			}
		}
		return $strother;
	}
	return null;
}