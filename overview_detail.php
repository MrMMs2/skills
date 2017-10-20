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
$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... skills instance ID - it should be named as the first character of the module.
$dataformid  = optional_param('dtfid', 0, PARAM_INT);  // id do formulario

if ($id) {
	$cm         = get_coursemodule_from_id('skills', $id, 0, false, MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$skills  = $DB->get_record('skills', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
	$skills  = $DB->get_record('skills', array('id' => $n), '*', MUST_EXIST);
	$course     = $DB->get_record('course', array('id' => $skills->course), '*', MUST_EXIST);
	$cm         = get_coursemodule_from_instance('skills', $skills->id, $course->id, false, MUST_EXIST);
} else {
	error('Você deve especificar um ID course_module ou uma instância ID');
}

require_login($course, true, $cm);
//add_to_log($course->id, 'skills', 'overview detail', 'overview_detail.php?id='.$cm->id, $skills->id, $cm->id);

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
 
// Mostrando formulario
echo $OUTPUT->heading($skills->name);
if (!$dataformid){
	error('Você não informou o ID do formulário.');
	die();
}
$objReport = skills_get_details_report($dataformid);
// Verificando se usuario tem acesso (Capabilities)
if (has_capability('mod/skills:submit', $context) || ($USER->id == $objReport->userid)){
	$organs = skills_generate_array_organs();
	
	$objReport = skills_get_details_report($dataformid);
	//echo "<pre>".var_dump($objReport); echo "</pre>";
	
	if(!$objReport->savefinish){
		echo $OUTPUT->box("<p>
							<strong>Prezado(a) {$objReport->userfullname}</strong>,</p> <p>Seu relatório ainda não foi finalizado. Uma vez finalizado, não será possível editá-lo.
							<form method='POST' action='savefinish.php?id={$objReport->coursemodulesid}'>
								<input type='hidden' name='dtfid' value={$objReport->id}>
								<input type='hidden' name='confirm' value='1'>
								<input type='submit' value='Finalizar relatório'>
							</form>
							</p> ", "alert alert-warning");
		
		echo "<div class='text-right'><a class='btn btn-default text-right' href='edit.php?id={$objReport->coursemodulesid}&dtfid={$objReport->id}' style='color: white;' title='Editar retalório' ><i class='fa-edit fa fa-fw'></i> Editar relatório</a></div>";
		
	}
	
	echo '<div id="mform1" class="mform">';
	echo '<fieldset id="id_general" class="clearfix collapsible">';
	echo '<legend class="ftoggler">
			<a class="fheader" href="#" role="button" aria-controls="id_general" aria-expanded="true">Dados Gerais '.$objReport->yearprevious.'</a>
		</legend>';
	
	echo "<div class='fcontainer clearfix'>";
		echo '<div id="fitem_id_fullname" class="fitem fitem_ftext ">';
			echo "<div class='fitemtitle'> <label >Responsável: </label> </div>";
			echo "<div class='felement ftext'>{$objReport->userfullname} </div>";
		echo "</div>";
		
		echo '<div id="fitem_id_fullname" class="fitem fitem_ftext ">';
			echo "<div class='fitemtitle'> <label >Órgão: </label> </div>";
			echo "<div class='felement ftext'>{$organs[$objReport->organ]} </div>";
		echo "</div>";
		
		echo '<div id="fitem_id_fullname" class="fitem fitem_ftext ">';
			echo "<div class='fitemtitle'> <label >Quantidade de Servidores: </label> </div>";
			echo "<div class='felement ftext'>{$objReport->qtdeservers} servidores</div>";
		echo "</div>";
		
		echo '<div id="fitem_id_fullname" class="fitem fitem_ftext ">';
			echo "<div class='fitemtitle'> <label >Quantidade de Magistrados: </label> </div>";
			echo "<div class='felement ftext'>{$objReport->qtdemagistrates} Magistrados</div>";
		echo "</div>";
		
		echo '<div id="fitem_id_fullname" class="fitem fitem_ftext ">';
			echo "<div class='fitemtitle'> <label >UNIDADE RESPONSÁVEL PELA FORMAÇÃO E APERFEIÇOAMENTO DE SERVIDORES:</label> </div>";
			echo "<div class='felement ftext'>{$objReport->trainingunit}</div>";
		echo "</div>";
		
		echo '<div class="question">1. HÁ UM PLANEJAMENTO ANUAL QUE DETERMINE A NECESSIDADE DE FORMAÇÃO E APERFEIÇOAMENTO?</div>';
		echo '<div class="items_questions">'.strtoupper(get_string($objReport->annualplanning, 'skills')).'</div>';
		
		if($objReport->annualplanning == 'yes'){
			echo '<div class="question child">1.1  METODOLOGIA(S) UTILIZADA(S) PARA REALIZAR O PLANEJAMENTO</div>';
			echo '<div class="items_questions">'.skills_format_options_report($objReport->methodology).'</div>';
		}
			
		echo '<div class="question">2. AÇÕES FORMATIVAS ADOTADAS PELO ÓRGÃO NA FORMAÇÃO E APERFEIÇOAMENTO DE SERVIDORES EFETIVOS.</div>';
		echo '<div class="items_questions">'.skills_format_options_report_with_areas_and_trainings($objReport->formativeactionsyearprevious, $objReport->id).'</div>';
			
		// Item 3
		echo '<div class="question">3. HÁ AÇÕES DE CAPACITAÇÃO ABERTAS À SOCIEDADE?</div>';
		$response = $objReport->trainingactionsociety !== "no" ? strtoupper(get_string('yes', 'skills')) : strtoupper(get_string('no', 'skills'));

		echo "<div class='items_questions'>{$response}</div>";
		if($objReport->trainingactionsociety !== 'no'){
			$tngactsct = json_decode($objReport->trainingactionsociety);
			echo "<table id='trainingactionsociety' class='generaltable table-striped table-hover child'>
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
					<td class='cell'>{$tngactsct->nturmas->presencial}</td>
					<td class='cell'>{$tngactsct->nturmas->ead->comtutoria}</td>
					<td class='cell'>{$tngactsct->nturmas->ead->semtutoria}</td>
					<td class='cell'>{$tngactsct->nturmas->semipresencial}</td>
				</tr>
				<tr>
					<td class='cell'>Nº DE INSCRITOS</td>
					<td class='cell'>{$tngactsct->ninscritos->presencial}</td>
					<td class='cell'>{$tngactsct->ninscritos->ead->comtutoria}</td>
					<td class='cell'>{$tngactsct->ninscritos->ead->semtutoria}</td>
					<td class='cell'>{$tngactsct->ninscritos->semipresencial}</td>
				</tr>
				<tr>
					<td class='cell'>Nº DE CAPACITADOS</td>
					<td class='cell'>{$tngactsct->ncapacitados->presencial}</td>
					<td class='cell'>{$tngactsct->ncapacitados->ead->comtutoria}</td>
					<td class='cell'>{$tngactsct->ncapacitados->ead->semtutoria}</td>
					<td class='cell'>{$tngactsct->ncapacitados->semipresencial}</td>
				</tr>
			</table>";
		}
		// Item 4
		echo '<div class="question">4. ESTRUTURA PARA CAPACITAÇÃO</div>';
		$stritems  = "<div class='items_questions'>";
		$jsonsttng = json_decode($objReport->structuretraining);
		$stritems .= property_exists($jsonsttng,'trainingrom') ? $jsonsttng->trainingrom."<br />" : null;
		$stritems .= property_exists($jsonsttng,'computerlab') ? $jsonsttng->computerlab."<br />" : null;
		$stritems .= property_exists($jsonsttng,'library') ? $jsonsttng->library."<br />" : null;
		$stritems .= property_exists($jsonsttng,'auditorium') ? $jsonsttng->auditorium."<br />" : null;
		$stritems .= property_exists($jsonsttng,'studios') ? $jsonsttng->studios."<br />" : null;
		$stritems .= property_exists($jsonsttng,'learningplatform') ? $jsonsttng->learningplatform."<br />" : null;
		$plataform = property_exists($jsonsttng,'learningplatform') ? $jsonsttng->plataform : null;
		if($plataform){
			$stritems .= "<table class='generaltable boxaligncenter'>
					<tr>
						<th class='header'>TIPO</th>
						<th class='header'>VERSÃO</th>
					</tr>
					<tr>
						<td class='cell'>{$plataform->type}</td>
						<td class='cell'>{$plataform->version}</td>
					</tr>
				</table>";
		}
		$stritems .=  "</div>";
		echo $stritems;
		
		// Item 4.1
		echo '<div class="question child">4.1 A ESTRUTURA DE CAPACITAÇÃO ATENDE ÀS NECESSIDADES DO ÓRGÃO?</div>';
		echo '<div class="items_questions">'.strtoupper(get_string($objReport->structtrainingsuficiente, 'skills')).'</div>';
		// Item 5
		echo '<div class="question">5. RECURSOS TOTAIS:</div>';
		echo "<table id='resources' class='generaltable boxaligncenter'>
					<tr>
						<th class='header'>DOTAÇÃO ORÇAMENTÁRIA PREVISTA PARA <br />ATENDER ÀS NECESSIDADES DE FORMAÇÃO E <br />APERFEIÇOAMENTO</th>
						<th class='header'>DESPESA REALIZADA</th>
					</tr>
					<tr class='r0'>
					<td class='cell' align='center'>{$objReport->budgettraining}</td>
					<td class='cell' align='center'>{$objReport->runvalue}</td>
				</tr>
			</table>";
		// Item 5.0
		echo '<div class="question child">ESSA DOTAÇÃO ORÇAMENTÁRIA INCLUI CAPACITAÇÃO DE MAGISTRADOS?</div>';
		echo '<div class="items_questions">'.strtoupper(get_string($objReport->budgetaddmagistrates, 'skills')).'</div>';
		
		// Item 5.1
		echo '<div class="question child">5.1 DESPESAS REALIZADAS POR AÇÃO FORMATIVA:</div>';
		echo '<div class="items_questions">'.skills_get_table_runvalbyformaction_view_user($objReport->runvalbyformaction).'</div>';
		
		// Item 5.2
		echo '<div class="question child">5.2 OUTRAS DESPESAS:</div>';
		echo '<div class="items_questions">'.skills_get_table_othersvalrun_view_user($objReport->othersvalrun).'</div>';
		
		// Item 6
		echo '<div class="question">6. HÁ UM PROGRAMA INSTITUCIONALIZADO DE GESTÃO POR COMPETÊNCIAS? </div>';
		echo '<div class="items_questions">'.strtoupper(get_string($objReport->programskills, 'skills')).'</div>';
		
		if($objReport->programskills == 'yes'){
			echo '<div class="question child">OPÇÕES SOBRE QUE ESTÁGIO ENCONTRA-SE NO PROGRAMA DA GESTÃO POR COMPETÊNCIAS: </div>';
			echo '<div class="items_questions">'.skills_format_phasestageprogramskills_report($objReport->stageprogramskills, $objReport->phasestageprogramskills).'</div>';
		}
		
		echo '<div class="question"><strong>7. HÁ ALGUM TIPO DE AVALIAÇÃO ADOTADA NOS CURSOS?</strong></div>';
		echo '<div class="items_questions">'.strtoupper(get_string($objReport->evaluation, 'skills')).'</div>';
		
		if($objReport->evaluation == 'yes'){
			echo '<div class="question child"><span class="child">Tipo(s) de avaliação utilizado(s):</span></div>';
			echo '<div class="items_questions">'.skills_format_options_report($objReport->typeevaluation).'</div>';
		}
		
		echo "</div>";
	echo '</fieldset>'; // Fim dados ano anterior
	echo '</div>';
	
	echo '<div id="mform1" class="mform">';
	echo '<fieldset id="id_general" class="clearfix collapsible">';
	echo '<legend class="ftoggler">
			<a class="fheader" href="#" role="button" aria-controls="id_general" aria-expanded="true">Dados Gerais '.$objReport->currentyear.'</a>
		</legend>';
	echo "<div class='fcontainer clearfix'>";
	
		echo "<div class='question'>1. HÁ UM PLANEJAMENTO PARA AÇÕES DE CAPACITAÇÃO EM {$objReport->currentyear}? </div>";
		echo '<div class="items_questions">'.strtoupper(get_string($objReport->planactiontngnextyear, 'skills')).'</div>';
		
		if($objReport->planactiontngnextyear == 'yes'){
			// link file
			$fileplannextyear = skills_getfileplannextyear($context, $objReport);
			$lkdown = false;
			if($fileplannextyear){
				echo "<div class='question child'>ANEXO DO PLANEJAMENTO:
						<a href='{$fileplannextyear->fullurl}'>
							<img src='{$OUTPUT->pix_url(file_mimetype_icon($fileplannextyear->mimetype))->out()}' alt='Baixar anexo' title='Baixar anexo' />
						</a>
					</div>";
			}
		}
	
		echo '<div class="question">2. AÇÕES FORMATIVAS PREVISTAS: </div>';
		echo '<div class="items_questions">'.skills_format_options_report($objReport->formativeactionsyearcurrent).'</div>';
		
		echo '<div class="question">3. PREVISÃO ORÇAMENTÁRIA PARA ATENDER ÀS NECESSIDADES DE FORMAÇÃO E APERFEIÇOAMENTO  </div>';
		echo '<div class="items_questions">'.$objReport->budgetnextyear.'</div>';
		
		echo '<div class="question child"><strong>ESSA DOTAÇÃO ORÇAMENTÁRIA INCLUI CAPACITAÇÃO DE MAGISTRADOS? </strong></div>';
		echo '<div class="items_questions">'.strtoupper(get_string($objReport->budgetaddmgtdnextyear, 'skills')).'</div>';
		
	echo "</div>";
	echo '</fieldset>'; // Fim dados ano corrente
	echo "</div>";
	
	
	echo '<div id="mform1" class="mform">';
	echo '<fieldset id="id_general" class="clearfix collapsible">';
	echo '<legend class="ftoggler">
			<a class="fheader" href="#" role="button" aria-controls="id_general" aria-expanded="true">Comentários</a>
		</legend>';
	echo "<div class='fcontainer clearfix'>";
	
	echo '<div class="question child">Comentários</div>';
	echo '<div class="items_questions">'.$objReport->comments.'</div>';
	
	echo "</div>";
	echo '</fieldset>'; // Fim dados ano corrente
	echo "</div>";
	
	echo '<div id="mform1" class="mform">';
	echo '<fieldset id="id_general3" class="clearfix collapsible">';
	echo '<legend class="ftoggler">
			<a class="fheader" href="#" role="button" aria-controls="id_general" aria-expanded="true">Sugestões para o Relatório '.date("Y").'/'.(date("Y")+1).'</a>
		</legend>';
	echo "<div class='fcontainer clearfix'>";
	
	echo '<div class="question child">Sugestões</div>';
	echo '<div class="items_questions">'.$objReport->sugestions.'</div>';
	
	echo "</div>";
	echo '</fieldset>'; // Fim dados ano corrente
	echo "</div>";
}
else{
	error('Você não tem permissão para acessar esse relatório.');
}

// Finish the page.
echo $OUTPUT->footer();
