<?php
// This file is part of cicleinscription - http://cnj.jus.br/eadcnj
// Extension moodle for Cicle of Inscriptions
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
 * Form organ_form for cicleinscription
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage cicleinscription
 * @copyright  2013 CEAJUD - Sector CNJ
 * @author		Leo Renis Santos <leo.santos@cnj.jus.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/skills/lib.php');
/**
 * Module instance settings form
 */
class mod_skills_edit_report_form extends moodleform {

	/**
	 * Defines forms elements
	 */
	public function definition() {
		global $CFG, $USER, $COURSE;
		$dataform = $this->_customdata['dataform'];
		$mform = $this->_form;
		
		//var_dump($dataform);
		
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('generalyearprevious', 'skills'));
		
		//
		$mform->addElement('text', 'fullname', 'Responsável', array('class'=>'input-width', ' disabled', 'value'=>skills_get_serFullName()));
		$mform->setType('fullname', PARAM_RAW);
		
		// campo skillsid
		$mform->addElement('hidden', 'skillsid');
		$mform->setType('skillsid', PARAM_INT);
		$mform->setDefault('skillsid', $dataform->skillsid);
		
				// campo userid
		$mform->addElement('hidden', 'userid');
		$mform->setType('userid', PARAM_INT);
		$mform->setDefault('userid', $USER->id);
		
				// campo coursemodulesid
		$mform->addElement('hidden', 'coursemodulesid');
		$mform->setType('coursemodulesid', PARAM_INT);
		$mform->setDefault('coursemodulesid', optional_param('id', 0, PARAM_INT));
		
						// campo yearprevious
		$mform->addElement('hidden', 'yearprevious');
		$mform->setType('yearprevious', PARAM_INT);
		$mform->setDefault('yearprevious', date("Y")-1);
		
		// campo currentyear
		$mform->addElement('hidden', 'currentyear');
		$mform->setType('currentyear', PARAM_INT);
		$mform->setDefault('currentyear', date("Y"));
		
		// Orgao
		$organs = skills_generate_array_organs();
		if($USER->institution && $organs[strtolower($USER->institution)]){
			$mform->addElement('text', 'showorgan', get_string('organ', 'skills'), array('class'=>'input-width', 'readonly'=>'readonly'));
			$mform->setType('showorgan', PARAM_RAW);
			$mform->setDefault('showorgan', $organs[strtolower($USER->institution)]);
						
			$mform->addElement('hidden', 'organ');
			$mform->setType('organ', PARAM_RAW);
			$mform->addRule('organ', null, 'required', null, 'client');
			$mform->setDefault('organ', strtolower($USER->institution));
		}else {
				// Adicionando campo organ
		$mform->addElement('select', 'organ', get_string('organ','skills'), skills_generate_array_organs(), array('class'=>'input-width'));
		$mform->addRule('organ', null, 'required', null, 'client');
		$mform->addHelpButton('organ', 'organhelp', 'skills');
		}
		
		// Adicionando campo qtdeservers
		$mform->addElement('text', 'qtdeservers', get_string('qtdeservers', 'skills'), array('class'=>'input-width'));
		$mform->setType('qtdeservers', PARAM_INT);
		$mform->addRule('qtdeservers', null, 'required', null, 'client');
		$mform->addHelpButton('qtdeservers', 'qtdeservershelp', 'skills');
		
				// Adicionando campo qtde
		$mform->addElement('text', 'qtdemagistrates', get_string('qtdemagistrates', 'skills'), array('class'=>'input-width'));
		$mform->setType('qtdemagistrates', PARAM_INT);
		$mform->addRule('qtdemagistrates', null, 'required', null, 'client');
		
				// Adicionando campo unidade de formacao
		$mform->addElement('text', 'trainingunit', get_string('trainingunit', 'skills'), array('class'=>'input-width', 'onkeyup'=>'this.value = this.value.toUpperCase()', 'maxlength'=>255));
		$mform->addRule('trainingunit', null, 'required', null, 'client');
		$mform->setType('trainingunit', PARAM_RAW);
		
		// Planejamento anual
		$annualplanning = array();
		$annualplanning[] =& $mform->createElement('radio', 'annualplanning','', get_string('yes','skills'), 'yes');
		$annualplanning[] =& $mform->createElement('radio', 'annualplanning', '', get_string('no','skills'), 'no');
		$mform->addGroup($annualplanning, 'annualplanning', get_string('annualplanningquestion','skills'), array(' '), false);
		$mform->addHelpButton('annualplanning', 'annualplanninghelp', 'skills');
		
		$optfields = array('AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS', 'AVALIAÇÃO DE DESEMPENHO SEM COMPETÊNCIAS', 'LEVANTAMENTO DE NECESSIDADE DE TREINAMENTO', 'HISTÓRICO DOS ANOS ANTERIORES', 'PLANEJAMENTO ESTRATÉGICO', 'ANÁLISE DOS MACROPROCESSOS');
		$mform->addElement('hidden', 'methodology');
		$mform->setType('methodology', PARAM_TEXT);
		$mform->addElement('html', "<div id='id_methodology'>
				<div class='question child'>1.1	QUAL (QUAIS) O(S) INSTRUMENTO(S) UTILIZADO(S) PARA ELABORAR O PLANEJAMENTO DE CAPACITAÇÃO?</div>
				<div class='items_questions'>
					<input type='checkbox' id='ck1' ".skills_check_dataform($dataform->methodology, "AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS")." name='methodology[]' value='AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS'> <label for='ck1'>AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS</label><br />
					<input type='checkbox' id='ck2' ".skills_check_dataform($dataform->methodology, "AVALIAÇÃO DE DESEMPENHO SEM COMPETÊNCIAS")." name='methodology[]' value='AVALIAÇÃO DE DESEMPENHO SEM COMPETÊNCIAS'> <label for='ck2'>AVALIAÇÃO DE DESEMPENHO</label><br />
					<input type='checkbox' id='ck3' ".skills_check_dataform($dataform->methodology, "LEVANTAMENTO DE NECESSIDADE DE TREINAMENTO")." name='methodology[]' value='LEVANTAMENTO DE NECESSIDADE DE TREINAMENTO'> <label for='ck3'>LEVANTAMENTO DE NECESSIDADE DE TREINAMENTO</label><br />
					<input type='checkbox' id='ck4' ".skills_check_dataform($dataform->methodology, "HISTÓRICO DOS ANOS ANTERIORES")." name='methodology[]' value='HISTÓRICO DOS ANOS ANTERIORES'> <label for='ck4'>HISTÓRICO DOS ANOS ANTERIORES</label><br />
					<input type='checkbox' id='ck5' ".skills_check_dataform($dataform->methodology, "PLANEJAMENTO ESTRATÉGICO")." name='methodology[]' value='PLANEJAMENTO ESTRATÉGICO'> <label for='ck5'>PLANEJAMENTO ESTRATÉGICO</label><br />
					<input type='checkbox' id='ck6' ".skills_check_dataform($dataform->methodology, "ANÁLISE DOS MACROPROCESSOS")." name='methodology[]' value='ANÁLISE DOS MACROPROCESSOS'> <label for='ck6'>ANÁLISE DOS MACROPROCESSOS</label><br />
					<label for='ck7'>OUTRA(S): </label><br />
					<textarea id='ck7' onkeyup='this.value = this.value.toUpperCase()' class='textarea' name='methodology[]'>".skills_get_fields_others($dataform->methodology, $optfields)."</textarea>
				</div>
			</div>");
		
		// Pergunta 2
		$mform->addElement('hidden', 'formativeactionsyearprevious');
		$mform->setType('formativeactionsyearprevious', PARAM_TEXT);
		$mform->addElement('hidden', 'lowercourse360hours');
		$mform->setType('lowercourse360hours', PARAM_TEXT);
		$mform->addElement('html', "<div class='question'>2. MARQUE AS AÇÕES FORMATIVAS ADOTADAS PELO ÓRGÃO NA FORMAÇÃO E NO APERFEIÇOAMENTO DE SERVIDORES EFETIVOS.</div>");
		$mform->addElement('html', "<div class='items_questions'>
								<input type='checkbox' id='courses360h' ".skills_check_dataform($dataform->formativeactionsyearprevious, "CURSOS COM MENOS DE 360h")." name='formativeactionsyearprevious[]' value='CURSOS COM MENOS DE 360h'> <label for='courses360h'>CURSOS COM MENOS DE 360h</label><br />
							</div>");
		
		// Areas
		$areas = skills_get_areas();
		$mform->addElement('html', "<div class='items_questions intructions'>
										<div class='alert alert-info' role='alert'>
											<p><strong>INSTRUTORES INTERNOS: </strong> aqueles que possuem vínculo com a administração pública, compondo o quadro próprio do órgão ou não.</p>
										  	<p><strong>INSTRUTORES EXTERNOS: </strong> pessoa física ou jurídica que não possui vínculo com a administração pública.</p>
										  	<p><strong>MODALIDADE SEMIPRESENCIAL: </strong> cursos que são promovidos simultaneamente nas modalidades a distância e presencial, não importando o percentual em que ocorre cada uma.</p>
										  	<p><strong>NÚMERO DE CAPACITADOS: </strong> aqueles inscritos que concluíram o curso com aproveitamento (obtenção de certificado, cumprimento de carga horária, aprovação em avaliação, etc).</p>
										  	<p><strong>PERCENTUAL DE EVASÃO: </strong> refere-se à quantidade de alunos que se inscreveram num curso e não o concluíram. Neste caso, não serão considerados os alunos reprovados.</p>
										</div>
									</div>");
		
		$areas_html = "<div id='accordion' class='panel-group list-skill-areas' aria-multiselectable='true' role='tablist'>";
		$cont = 1;
		foreach ($areas as $area){
			$bool = ($cont <= 1) ? 'true' : 'false';
			$classin = ($cont <= 1) ? 'in' : ' ';
			$areas_html .= "<div class='panel panel-default'>";
				$areas_html .= "<div class='panel-heading' role='tab' id='heading".$cont."'>";
					$areas_html .= "<h4 class='panel-title'>
					<a role='button' id='area".$area->id."' data-toggle='collapse' data-parent='#accordion' href='#collapse".$cont."' aria-expanded='".$bool."' aria-controls='collapse".$cont."'>
						{$area->name}
					</a>
					</h4>"; // title
				$areas_html .= "</div>";
		
				$areas_html .= "<div id='collapse".$cont."' class='panel-collapse collapse ".$classin."' role='tabpanel' aria-labelledby='heading".$cont."'>";
					$areas_html .= "<div class='panel-body'><p>{$area->description}</p> ".skills_generate_table_areas($area->id, $dataform->id)."</div>"; // description
				$areas_html .= "</div>";
			$areas_html .= "</div>";
		
			$cont ++;
		}
		$areas_html .= "</div>";
		
		// fim areas
		$mform->addElement('html', $areas_html);
		
		// Trainings
		$trainings = skills_get_trainings();
		$mform->addElement('hidden', 'trainings');
		$mform->setType('trainings', PARAM_TEXT);
		$training_html = "";
		$optfields = array('CURSOS COM MENOS DE 360h');
		foreach ($trainings as $training){
			$training_html .= "<div class='items_questions'>
									<input class='ckformativeaction' onclick='return training_verify({$training->id});' ".skills_check_dataform($dataform->formativeactionsyearprevious, $training->name)." type='checkbox' id='tgn_{$training->id}' data-id='$training->id' name='formativeactionsyearprevious[]' value='{$training->name}'> <label for='tgn_{$training->id}'>{$training->name}</label><br />
								</div>";
			$optfields[] = $training->name;
			if(strtoupper(trim($training->name)) == strtoupper(trim('OUTRO(S): '))){
					$othervalue = skills_get_fields_others($dataform->formativeactionsyearprevious, $optfields);
					$training_html .= skills_generate_textarea('formativeactionsyearprevious[]','training ', 'training'.$training->id, $othervalue);
			}else{
					$training_html .= skills_generate_table_edit_training($training, $dataform->id);
			}
		}
			$mform->addElement('html', $training_html);
		
		// Acoes de capacitacao abertas a sociedade
		$jsontrainingactionsociety = null;
		if($dataform->trainingactionsociety !== 'no'){
			$jsontrainingactionsociety = $dataform->trainingactionsociety;
			$dataform->trainingactionsociety = 'yes';
		}
		$trainingactionsociety = array();
		$trainingactionsociety[] =& $mform->createElement('radio', 'trainingactionsociety','', get_string('yes','skills'), 'yes');
		$trainingactionsociety[] =& $mform->createElement('radio', 'trainingactionsociety', '', get_string('no','skills'), 'no');
		$mform->addGroup($trainingactionsociety, 'trainingactionsociety', get_string('trainingactionsociety', 'skills'), array(' '), false);
		$mform->addHelpButton('trainingactionsociety', 'trainingactionsocietyhelp', 'skills');
		
		$mform->addElement('hidden', 'dtcapsociedade');
		$mform->setType('dtcapsociedade', PARAM_RAW);
		
		$mform->addElement('html', "<div id='id_trainingactionsociety'>
						<div class='question child'> PREENCHA A TABELA ABAIXO: </div>".skills_get_table_trainingactionsociety($jsontrainingactionsociety).
				"</div>");
		
		// Estrutura de capacitação
		$jsonsttng = json_decode($dataform->structuretraining);
		$jsonsttng->trainingrom = property_exists($jsonsttng,'trainingrom') ? $jsonsttng->trainingrom : null;
		$jsonsttng->computerlab = property_exists($jsonsttng,'computerlab') ? $jsonsttng->computerlab : null;
		$jsonsttng->library = property_exists($jsonsttng,'library') ? $jsonsttng->library : null;
		$jsonsttng->auditorium = property_exists($jsonsttng,'auditorium') ? $jsonsttng->auditorium : null;
		$jsonsttng->studios = property_exists($jsonsttng,'studios') ? $jsonsttng->studios : null;
		$jsonsttng->learningplatform = property_exists($jsonsttng,'learningplatform') ? $jsonsttng->learningplatform : null;
		$plataform = property_exists($jsonsttng,'learningplatform') ? $jsonsttng->plataform : null;
		
		$mform->addElement('hidden', 'structuretraining');
		$mform->setType('structuretraining', PARAM_RAW);
		$mform->addElement('html', "<div id='id_structuretraining'>
				<strong>4. ESTRUTURA PARA CAPACITAÇÃO</strong>
				<div class='items_questions'>
					<input type='checkbox' id='ckstruct1' ".skills_check_dataform($jsonsttng->trainingrom, 'SALAS DE TREINAMENTO')." name='structuretraining[trainingrom]' value='SALAS DE TREINAMENTO'> <label for='ckstruct1'>SALAS DE TREINAMENTO</label><br />
					<input type='checkbox' id='ckstruct2' ".skills_check_dataform($jsonsttng->computerlab, 'LABORATORIO DE INFORMATICA')." name='structuretraining[computerlab]' value='LABORATORIO DE INFORMATICA'> <label for='ckstruct2'>LABORATÓRIO DE INFORMÁTICA</label><br />
					<input type='checkbox' id='ckstruct3' ".skills_check_dataform($jsonsttng->library, 'BIBLIOTECA')." name='structuretraining[library]' value='BIBLIOTECA'> <label for='ckstruct3'>BIBLIOTECA</label><br />
					<input type='checkbox' id='ckstruct4' ".skills_check_dataform($jsonsttng->auditorium, 'AUDITORIO')." name='structuretraining[auditorium]' value='AUDITORIO'> <label for='ckstruct4'>AUDITÓRIO</label><br />
					<input type='checkbox' id='ckstruct5' ".skills_check_dataform($jsonsttng->studios, 'ESTUDIOS DE GRAVACAO')." name='structuretraining[studios]' value='ESTUDIOS DE GRAVACAO'> <label for='ckstruct5'>ESTÚDIOS DE GRAVAÇÃO</label><br />
					<input type='checkbox' id='ckstruct6' ".skills_check_dataform($jsonsttng->learningplatform, 'PLATAFORMA DE APRENDIZAGEM')." name='structuretraining[learningplatform]' value='PLATAFORMA DE APRENDIZAGEM'> <label for='ckstruct6'>PLATAFORMA DE APRENDIZAGEM</label><br />
					".skills_get_table_structuretraining($plataform)."
				</div>
			</div>");
		
		// A Estrutura de capacitação atende as necessidades do orgao
		$structtrainingsuficiente = array();
		$structtrainingsuficiente[] =& $mform->createElement('radio', 'structtrainingsuficiente','', get_string('yes','skills'), 'yes');
		$structtrainingsuficiente[] =& $mform->createElement('radio', 'structtrainingsuficiente', '', get_string('no','skills'), 'no');
		$mform->addGroup($structtrainingsuficiente, 'structtrainingsuficiente', get_string('structtrainingsuficiente', 'skills'), array(' '), false);
		
		// Campos de Recursos totais
		$mform->addElement('html', "<div class='question' for='id_budgettraining'>5. RECURSOS TOTAIS</div>");
		
		$mform->addElement('text', 'budgettraining', get_string('budgettraining', 'skills'), array('class'=>'input-width money child'));
		$mform->addRule('budgettraining', null, 'required', null, 'client');
		$mform->setType('budgettraining', PARAM_RAW);
		
		$mform->addElement('text', 'runvalue', get_string('runvalue', 'skills'), array('class'=>'input-width money child'));
		$mform->addRule('runvalue', null, 'required', null, 'client');
		$mform->setType('runvalue', PARAM_RAW);

		// A dotacao inclui a capacitacao de magistrados?
		$budgetaddmagistrates = array();
		$budgetaddmagistrates[] =& $mform->createElement('radio', 'budgetaddmagistrates','', get_string('yes','skills'), 'yes');
		$budgetaddmagistrates[] =& $mform->createElement('radio', 'budgetaddmagistrates', '', get_string('no','skills'), 'no');
		$mform->addGroup($budgetaddmagistrates, 'budgetaddmagistrates', get_string('budgetaddmagistrates', 'skills'), array(' '), false);
		
		// 5.1 Despesas realizadas por acao formativa
		$mform->addElement('hidden', 'runvalbyformaction');
		$mform->setType('runvalbyformaction', PARAM_RAW);
			$mform->addElement('html', "<div id='id_runvalbyformaction'>
				<div class='question'>5.1 DESPESAS REALIZADAS POR AÇÃO FORMATIVA:</div>
				<div class='items_questions'>
				".skills_get_table_runvalbyformaction($dataform->runvalbyformaction)."
				</div>
			</div>");
		
		// 5.2 Outras despesas
		$mform->addElement('hidden', 'othersvalrun');
		$mform->setType('othersvalrun', PARAM_RAW);
			$mform->addElement('html', "<div id='id_othersvalrun'>
			<div class='question'>5.2 OUTRAS DESPESAS:</div>
					<div class='items_questions'>
							".skills_get_table_othersvalrun($dataform->othersvalrun)."
					</div>
		</div>");
		
		// Programa institucionalizado Gestão por Competencias
		$programskills = array();
		$programskills[] =& $mform->createElement('radio', 'programskills','', get_string('yes','skills'), 'yes');
		$programskills[] =& $mform->createElement('radio', 'programskills', '', get_string('no','skills'), 'no');
		$mform->addGroup($programskills, 'programskills', get_string('programskills','skills'), array(' '), false);
		
		// Estagio do programa de gestao por competencias
		$mform->addElement('hidden', 'stageprogramskills');
		$mform->setType('stageprogramskills', PARAM_TEXT);
		// Fase do estagio do programa de gestao por competencias
		$mform->addElement('hidden', 'phasestageprogramskills');
		$mform->setType('phasestageprogramskills', PARAM_TEXT);
		$mform->addElement('html', "<div id='id_programskills'>
					<div class='question child'>Apesar das diversas tipologias existentes, serão adotadas as seguintes definições para este Relatório: </div>
					<div class='child'>
						<div class='alert alert-info' role='alert'>
							<p><strong>Competências organizacionais:</strong> são essenciais à organização e constituem requisitos e expectativas que os cidadãos e/ou a alta administração esperam que seus todos os seus membros possuam.</p>
						  	<p><strong>Competências setoriais:</strong> são os atributos e as capacidades das unidades ou departamentos da organização.</p>
						  	<p><strong>Competências individuais:</strong>  estão relacionadas aos padrões de desempenho que a organização espera de cada profissional.</p>
						</div>
					</div>
				<div class='question child'> PREENCHA AS OPÇÕES SOBRE QUE ESTÁGIO ENCONTRA-SE NO PROGRAMA DA GESTÃO POR COMPETÊNCIAS: </div>
					<div class='items_questions'>
						<input type='checkbox' id='psk1' ".skills_check_dataform($dataform->stageprogramskills, 'MAPEAMENTO DAS COMPETÊNCIAS')." name='stageprogramskills[]' value='MAPEAMENTO DAS COMPETÊNCIAS'> <label for='psk1'>MAPEAMENTO DAS COMPETÊNCIAS <span class='label label-info'> (descrição das competências necessárias e desejáveis para alcance da estratégia organizacional).</span></label><br />".skills_get_table_phasestageprogramskills('map', 'psk1')."
						<input type='checkbox' id='psk2' ".skills_check_dataform($dataform->stageprogramskills, 'DIAGNÓSTICO DE COMPETÊNCIAS E ANÁLISE DO GAP')." name='stageprogramskills[]' value='DIAGNÓSTICO DE COMPETÊNCIAS E ANÁLISE DO GAP'> <label for='psk2'>DIAGNÓSTICO DE COMPETÊNCIAS E ANÁLISE DO GAP <span class='label label-info'> (identificação das competências existentes nos profissionais do tribunal).</span></label><br />".skills_get_table_phasestageprogramskills('diag', 'psk2')."
						<input type='checkbox' id='psk3' ".skills_check_dataform($dataform->stageprogramskills, 'CAPACITAÇÃO POR COMPETÊNCIAS')." name='stageprogramskills[]' value='CAPACITAÇÃO POR COMPETÊNCIAS'> <label for='psk3'>CAPACITAÇÃO POR COMPETÊNCIAS <span class='label label-info'> (integração dos programas de capacitação e desenvolvimento institucional ao modelo de gestão por competências).</span></label><br />".skills_get_table_phasestageprogramskills('cap', 'psk3')."
						<input type='checkbox' id='psk4' ".skills_check_dataform($dataform->stageprogramskills, 'AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS')." name='stageprogramskills[]' value='AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS'> <label for='psk4'>AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS <span class='label label-info'> (avaliação sistemática do desempenho do profissional, conforme as competências já mapeadas pelo tribunal, visando identificar e corrigir os desvios).</span></label><br />".skills_get_table_phasestageprogramskills('aval', 'psk4')."
					</div>
				</div>");
		
		// Avaliação
		$evaluation = array();
		$evaluation[] =& $mform->createElement('radio', 'evaluation','', get_string('yes','skills'), 'yes');
		$evaluation[] =& $mform->createElement('radio', 'evaluation', '', get_string('no','skills'), 'no');
		$mform->addGroup($evaluation, 'evaluation', get_string('evaluation','skills'), array(' '), false);
		
		// Tipos de avaliacao
		$optfields = array('REAÇÃO', 'APRENDIZAGEM', 'APLICAÇÃO', 'RESULTADO');
		$mform->addElement('hidden', 'typeevaluation');
		$mform->setType('typeevaluation', PARAM_TEXT);
		$mform->addElement('html', "<div id='id_typeevaluation'>
				<div class='question child'>Indique o(s) tipo(s) de avaliação utilizado(s):</div>
				<div class='items_questions'>
					<input type='checkbox' id='tpev1' ".skills_check_dataform($dataform->typeevaluation, 'REAÇÃO')." name='typeevaluation[]' value='REAÇÃO'> <label for='tpev1'>REAÇÃO <span class='label label-info'>(avalia a impressão dos servidores sobre o conteúdo, instrutores, recursos, ambiente, etc).</span></label><br />
					<input type='checkbox' id='tpev2' ".skills_check_dataform($dataform->typeevaluation, 'APRENDIZAGEM')." name='typeevaluation[]' value='APRENDIZAGEM'> <label for='tpev2'>APRENDIZAGEM <span class='label label-info'>(examina se servidores absorveram os conhecimentos e aperfeiçoaram habilidades e atitudes).</span></label><br />
					<input type='checkbox' id='tpev3' ".skills_check_dataform($dataform->typeevaluation, 'APLICAÇÃO')." name='typeevaluation[]' value='APLICAÇÃO'> <label for='tpev3'>APLICAÇÃO <span class='label label-info'>(identifica se estão utilizando em seu trabalho os conhecimentos, habilidades e atitudes apreendidos).</span></label><br />
					<input type='checkbox' id='tpev4' ".skills_check_dataform($dataform->typeevaluation, 'RESULTADO')." name='typeevaluation[]' value='RESULTADO'> <label for='tpev4'>RESULTADO <span class='label label-info'>(analisa se a ação formativa contribuiu para o alcance da estratégia do órgão).</span></label><br />
					<label for='tpev5'>OUTROS: </label> <br />
					<textarea id='tpev5' onkeyup='this.value = this.value.toUpperCase()' class='textarea' name='typeevaluation[]'>".skills_get_fields_others($dataform->typeevaluation, $optfields)."</textarea>
				</div>
			</div>");
		
		// DADOS DO ANO SEGUINTE [2016]
		$mform->addElement('header', 'general', get_string('generalcurrentyear', 'skills'));
		
		// Planejamento para acoes de capacitacao no ano seguinte
		$planactiontngnextyear = array();
		$planactiontngnextyear[] =& $mform->createElement('radio', 'planactiontngnextyear','', get_string('yes','skills'), 'yes');
		$planactiontngnextyear[] =& $mform->createElement('radio', 'planactiontngnextyear', '', get_string('no','skills'), 'no');
		$mform->addGroup($planactiontngnextyear, 'planactiontngnextyear', get_string('planactiontngnextyear','skills'), array(' '), false);
		
		// Anexar planejamento
		$filemanager_options = array();
		$filemanager_options['accepted_types'] = array('.doc', '.pdf', '.docx');
		$filemanager_options['maxbytes'] = $COURSE->maxbytes;
		$filemanager_options['maxfiles'] = 1;
		$filemanager_options['subdirs'] = 0;
		
		$mform->addElement('filemanager', 'fileplannextyear', get_string('fileplannextyear', 'skills'), null, $filemanager_options);
		$mform->setType('fileplannextyear', PARAM_INT);
		$mform->addHelpButton('fileplannextyear', 'fileplannextyear', 'skills');
		
		// Acoes formativas do ano corrente
		$optfields = array('CURSOS PRESENCIAIS COM MENOS DE 360H', 'CURSOS EAD COM MENOS DE 360H', 'PÓS-GRADUAÇÃO', 'PALESTRAS', 'CONGRESSOS', 'ENCONTROS', 'SEMINÁRIOS', 'WORKSHOPS', 'GRADUAÇÕES', 'FÓRUNS');
		$mform->addElement('hidden', 'formativeactionsyearcurrent');
		$mform->setType('formativeactionsyearcurrent', PARAM_TEXT);
		$mform->addElement('html', "<div id='id_formativeactionsyearcurrent'>
				<div class='question'>2. INDIQUE AS AÇÕES FORMATIVAS PREVISTAS:</div>
						<div class='items_questions'>
							<input type='checkbox' id='fa1' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'CURSOS PRESENCIAIS COM MENOS DE 360H')." name='formativeactionsyearcurrent[]' value='CURSOS PRESENCIAIS COM MENOS DE 360H'> <label for='fa1'>CURSOS PRESENCIAIS COM MENOS DE 360H</label><br />
							<input type='checkbox' id='fa2' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'CURSOS EAD COM MENOS DE 360H')." name='formativeactionsyearcurrent[]' value='CURSOS EAD COM MENOS DE 360H'> <label for='fa2'>CURSOS EAD COM MENOS DE 360H</label><br />
							<input type='checkbox' id='fa3' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'PÓS-GRADUAÇÃO')." name='formativeactionsyearcurrent[]' value='PÓS-GRADUAÇÃO'> <label for='fa3'>PÓS-GRADUAÇÃO</label><br />
							<input type='checkbox' id='fa4' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'PALESTRAS')." name='formativeactionsyearcurrent[]' value='PALESTRAS'> <label for='fa4'>PALESTRAS</label><br />
							<input type='checkbox' id='fa5' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'CONGRESSOS')." name='formativeactionsyearcurrent[]' value='CONGRESSOS'> <label for='fa5'>CONGRESSOS</label><br />
							<input type='checkbox' id='fa6' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'ENCONTROS')." name='formativeactionsyearcurrent[]' value='ENCONTROS'> <label for='fa6'>ENCONTROS</label><br />
							<input type='checkbox' id='fa7' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'SEMINÁRIOS')." name='formativeactionsyearcurrent[]' value='SEMINÁRIOS'> <label for='fa7'>SEMINÁRIOS</label><br />
							<input type='checkbox' id='fa8' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'FÓRUNS')." name='formativeactionsyearcurrent[]' value='FÓRUNS'> <label for='fa8'>FÓRUNS</label><br />
							<input type='checkbox' id='fa9' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'WORKSHOPS')." name='formativeactionsyearcurrent[]' value='WORKSHOPS'> <label for='fa9'>WORKSHOPS</label><br />
							<input type='checkbox' id='fa10' ".skills_check_dataform($dataform->formativeactionsyearcurrent, 'GRADUAÇÕES')." name='formativeactionsyearcurrent[]' value='GRADUAÇÕES'> <label for='fa10'>GRADUAÇÕES</label><br />
							<label for='tpev9'>OUTRAS: </label><br />
							<textarea id='tpev9' onkeyup='this.value = this.value.toUpperCase()' class='textarea' name='formativeactionsyearcurrent[]' />".skills_get_fields_others($dataform->formativeactionsyearcurrent, $optfields)."</textarea>
						</div>
					</div>");
		
		// Recursos
		$mform->addElement('text', 'budgetnextyear', get_string('budgetnextyear', 'skills'), array('class'=>'input-width money'));
		$mform->addRule('budgetnextyear', null, 'required', null, 'client');
		$mform->setType('budgetnextyear', PARAM_RAW);
		
		// A dotacao do ano seguinte inclui a capacitacao de magistrados?
		$budgetaddmgtdnextyear = array();
		$budgetaddmgtdnextyear[] =& $mform->createElement('radio', 'budgetaddmgtdnextyear','', get_string('yes','skills'), 'yes');
		$budgetaddmgtdnextyear[] =& $mform->createElement('radio', 'budgetaddmgtdnextyear', '', get_string('no','skills'), 'no');
		$mform->addGroup($budgetaddmgtdnextyear, 'budgetaddmgtdnextyear', get_string('budgetaddmagistrates', 'skills'), array(' '), false);
		
		// COMENTARIOS
		$mform->addElement('header', 'general', get_string('comments', 'skills'));
		$mform->addElement('textarea', 'comments', get_string('comments', 'skills'), array('style'=>'width: 104.5%;', 'rows'=>7, 'onkeyup'=>'this.value = this.value.toUpperCase()'));
		$mform->setType('comments', PARAM_TEXT);
		
		// SUGESTOES
		$mform->addElement('header', 'general', get_string('sugestions', 'skills'));
		$mform->addElement('html', "<div>
				<p>Gostaríamos de contar com a colaboração de vocês no aprimoramento do próximo Relatório Anual de Formação e Aperfeiçoamento dos Servidores do Poder Judiciário. Solicitamos a indicação de sugestões, a fim de tornar o Relatório mais prático, completo e inteligível.</p>
				<p>Ressaltamos que, a partir desse ano, todas as sugestões devem ser inseridas ao final do Relatório. Para que sejam incorporadas ao Relatório, as sugestões apresentadas serão analisadas e aprovadas por instâncias superiores do Conselho Nacional de Justiça.</p>
			</div>");
		$mform->addElement('textarea', 'sugestions', get_string('sugestions', 'skills'), array('style'=>'width: 104.5%;', 'rows'=>8, 'onkeyup'=>'this.value = this.value.toUpperCase()'));
		$mform->setType('sugestions', PARAM_TEXT);
		
		$mform->addElement('hidden', 'createdate');
		$mform->setType('createdate', PARAM_TEXT);
		$mform->setDefault('createdate', time());
		
		# termresponse
		$mform->addElement('header', 'general', 'Confirmação');
		$mform->addElement('html', "<div><strong>Declaro, para os devidos fins, que as informações apresentadas acima são verdadeiras e atendem ao estabelecido no art. 17 da Resolução n° 192 de 8 de maio de 2014.</strong></div>");
		$mform->addElement('checkbox', 'confirmation', 'Confirme o preenchimento');
		$mform->addRule('confirmation', null, 'required', null, 'client');
				
		// add standard buttons, common to all modules
		//$this->add_action_buttons();
		
		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges', 'skills'));
		$buttonarray[] = &$mform->createElement('submit', 'submitclosebutton', get_string('saveclose', 'skills'));
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');
	}
}