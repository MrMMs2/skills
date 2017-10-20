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
$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... skills instance ID - it should be named as the first character of the module.

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
//add_to_log($course->id, 'skills', 'completeview', 'completeview.php?id='.$cm->id, $skills->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Context
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

// Pages
$PAGE->set_url('/mod/skills/completeview.php', array('id' => $cm->id));
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

// Cabecalho
echo $OUTPUT->heading($skills->name);

// Verificando se usuario tem acesso (Capabilities)
if (!has_capability('mod/skills:submit', $context)){
	
	print_error('You do not have permission to access this report.');
	
	// Finish the page.
	echo $OUTPUT->footer();	
	die();
}
// variaveis
$yearprevious = date("Y") - 1;
$currentyear = date('Y');
$total_annualplanning_no = skills_get_total_annualplanning($yearprevious, 'no');
$total_annualplanning_yes = skills_get_total_annualplanning($yearprevious, 'yes');

?>

<div id="mform1" class="mform">
	<fieldset id="id_general" class="clearfix collapsible">
		<legend class="ftoggler">
			<a class="fheader" href="#" role="button" aria-controls="id_general" aria-expanded="true">Dados Gerais <?php echo $yearprevious ?></a>
		</legend>

	<div class='fcontainer clearfix'>
	
		<div class="question">TOTAL DE RELATÓRIOS</div>
		<?php $objtreportsendfinish = skills_get_total_reportsend_and_reportfinish($yearprevious); ?>
		<table id="resources" class="items_questions generaltable boxaligncenter" style="max-width: 300px;">
			<tr>
				<th class='header table-head0-areas'>ENVIADOS</th>
				<th class='header table-r1-areas'>FINALIZADOS</th>
				<th class='header'>CONTABILIZADOS</th>
			</tr>
			<tr class='r0'>
				<td class='cell' align='center'><?php echo $objtreportsendfinish->totalsend ?></td>
				<td class='cell' align='center'><?php echo $objtreportsendfinish->totalsavefinish ?></td>
				<td class='cell' align='center'><?php echo $objtreportsendfinish->beinfullreport ?></td>
			</tr>
		</table>
		
		<div class="question">TOTAL DE SERVIDORES E MAGISTRADOS</div>
		<?php $objservandmag = skills_get_total_servers_and_magistrates($yearprevious); ?>
		<table id="resources" class="items_questions generaltable boxaligncenter">
			<tr>
				<th class='header table-head0-areas'>MAGISTRADOS</th>
				<th class='header'>SERVIDORES</th>
			</tr>
			<tr class='r0'>
				<td class='cell' align='center'><?php echo $objservandmag->totalmagistrates ?></td>
				<td class='cell' align='center'><?php echo $objservandmag->totalserver ?></td>
			</tr>
		</table>
		<div class="question">1. HÁ UM PLANEJAMENTO ANUAL QUE DETERMINE A NECESSIDADE DE FORMAÇÃO E APERFEIÇOAMENTO?</div>
			<div class='items_questions'>
				<table id='resources' class='generaltable boxaligncenter'>
					<tr>
						<th class='header table-head0-areas'>NÃO</th>
						<th class='header'>SIM</th>
					</tr>
					<tr class='r0'>
						<td class='cell' align='center'><?php echo $total_annualplanning_no ?></td>
						<td class='cell' align='center'><?php echo $total_annualplanning_yes ?></td>
					</tr>
				</table>
			</div>
		
			<div class="question child">1.1  INSTRUMENTO(S) UTILIZADO(S) PARA ELABORAR O PLANEJAMENTO DE CAPACITAÇÃO</div>
			<div class='items_questions'>
				<table id='resources' class='generaltable boxaligncenter'>
					<tr>
						<th class='header table-head0-areas'>AÇÕES FORMATIVAS</th>
						<th class='header table-head0-vagas'>Nº TOTAL (<?php echo $total_annualplanning_yes ?>)</th>
					</tr>
					<tr class='r0'>
						<td class='cell table-r0-areas' style='text-align: left;'>AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS</td>
						<td class='cell table-r0-vagas' align='center'><?php echo skills_get_total_annualplanning_by_methodology($yearprevious, '%AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS%') ?></td>
					</tr>
					<tr class='r1'>
						<td class='cell table-r1-areas' style='text-align: left;'>AVALIAÇÃO DE DESEMPENHO</td>
						<td class='cell table-r1-vagas' align='center'><?php echo skills_get_total_annualplanning_by_methodology($yearprevious, '%AVALIAÇÃO DE DESEMPENHO SEM COMPETÊNCIAS%') ?></td>
					</tr>
					<tr class='r0'>
						<td class='cell table-r0-areas' style='text-align: left;'>LEVANTAMENTO DE NECESSIDADE DE TREINAMENTO</td>
						<td class='cell table-r0-vagas' align='center'><?php echo skills_get_total_annualplanning_by_methodology($yearprevious, '%LEVANTAMENTO DE NECESSIDADE DE TREINAMENTO%') ?></td>
					</tr>
					<tr class='r1'>
						<td class='cell table-r1-areas' style='text-align: left;'>HISTÓRICO DOS ANOS ANTERIORES</td>
						<td class='cell table-r1-vagas' align='center'><?php echo skills_get_total_annualplanning_by_methodology($yearprevious, '%HISTÓRICO DOS ANOS ANTERIORES%') ?></td>
					</tr>
					<tr class='r0'>
						<td class='cell table-r0-areas' style='text-align: left;'>PLANEJAMENTO ESTRATÉGICO</td>
						<td class='cell table-r0-vagas' align='center'><?php echo skills_get_total_annualplanning_by_methodology($yearprevious, '%PLANEJAMENTO ESTRATÉGICO%') ?></td>
					</tr>
					<tr class='r1'>
						<td class='cell table-r1-areas' style='text-align: left;'>ANÁLISE DOS MACROPROCESSOS</td>
						<td class='cell table-r1-vagas' align='center'><?php echo skills_get_total_annualplanning_by_methodology($yearprevious, '%ANÁLISE DOS MACROPROCESSOS%') ?></td>
					</tr>
				</table>
			</div>
			
			<!-- Outras acoes formativas -->
			<?php echo skills_generate_table_others_methodologies($yearprevious); ?>
	
			<div class="question">2. AÇÕES FORMATIVAS ADOTADAS PELO ÓRGÃO NA FORMAÇÃO E APERFEIÇOAMENTO DE SERVIDORES EFETIVOS.</div>
			<strong class="items_questions">TOTAL POR ÁREA</strong>
			<?php $objAreas = skills_get_areas(); ?>
			<div class='items_questions'>
				<table class='generaltable boxaligncenter'>
					<tr>
						<th class='header table-head0-areas'>ÁREA</th>
						<th class='header table-head0-cursos'>CURSOS</th>
						<th class='header table-head0-vagas'>VAGAS</th>
						<th class='header table-head0-inscritos'>INSCRITOS</th>
						<th class='header table-head0-capacitados'>CAPACITADOS</th>
						<th class='header table-head0-reprovados'>REPROVADOS</th>
					</tr>
					<?php foreach ($objAreas as $area): ?>
						<?php $r = $area->id %2; ?>
						<?php $fieldstotal = skills_get_total_fields_by_area($yearprevious, $area->id); ?>
						<tr class='r<?php echo $r?>'>
							<td class='cell table-r<?php echo $r?>-areas' style='text-align: left;'><?php echo $area->name ?></td>
							<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo $fieldstotal->totalcourses ?></td>
							<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo $fieldstotal->totalvacancies ?></td>
							<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo $fieldstotal->totalenrollees ?></td>
							<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $fieldstotal->totaltrained ?> </td>
							<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo $fieldstotal->totaldisapproved ?> </td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
			
			<strong>DETALHAMENTO POR ÁREA</strong>
			<div class=' bigtable'>
				<table class='generaltable boxaligncenter'>
					<tr>
						<th class='header table-head0-areas'>#</th>
						<th class='header table-head0-cursos' colspan='3'>CURSOS</th>
						<th class='header table-head0-vagas' colspan='3'>VAGAS</th>
						<th class='header table-head0-inscritos' colspan='3'>INSCRITOS</th>
						<th class='header table-head0-capacitados' colspan='3'>CAPACITADOS</th>
						<th class='header table-head0-reprovados' colspan='3'>REPROVADOS</th>
						<th class='header table-head0-nevasao' colspan='3'>Nº EVS</th>
						<th class='header table-head0-pevasao' colspan='3'>% EVS</th>
						<th class='header table-head0-instrutores' colspan='6'>INSTRUTORES</th>
					</tr>
					<tr>
						<th class='header table-head1-areas'>ÁREA</th>
						<th class='header table-head1-cursos'>EAD</th>
						<th class='header table-head1-cursos'>PRE</th>
						<th class='header table-head1-cursos'>SEM.</th>
						<th class='header table-head1-vagas'>EAD</th>
						<th class='header table-head1-vagas'>PRES.</th>
						<th class='header table-head1-vagas'>SEM.</th>
						<th class='header table-head1-inscritos'>EAD</th>
						<th class='header table-head1-inscritos'>PRES.</th>
						<th class='header table-head1-inscritos'>SEM.</th>
						<th class='header table-head1-capacitados'>EAD</th>
						<th class='header table-head1-capacitados'>PRE</th>
						<th class='header table-head1-capacitados'>SEM.</th>
						<th class='header table-head1-reprovados'>EAD</th>
						<th class='header table-head1-reprovados'>PRE</th>
						<th class='header table-head1-reprovados'>SEM.</th>
						<th class='header table-head1-nevasao'>EAD</th>
						<th class='header table-head1-nevasao'>PRE</th>
						<th class='header table-head1-nevasao'>SEM.</th>
						<th class='header table-head1-pevasao'>EAD</th>
						<th class='header table-head1-pevasao'>PRE</th>
						<th class='header table-head1-pevasao'>SEM.</th>
						<th class='header table-head1-instrutores'>INT EAD</th>
						<th class='header table-head1-instrutores'>EXT EAD</th>
						<th class='header table-head1-instrutores'>INT PRE</th>
						<th class='header table-head1-instrutores'>EXT PRE</th>
						<th class='header table-head1-instrutores'>INT SEM</th>
						<th class='header table-head1-instrutores'>EXT SEM</th>
					</tr>
			
					<?php foreach ($objAreas as $area):?> 
						<?php $r = $area->id %2; ?>
						<?php $totalfields_ead = skills_get_total_fieldsbyarea_and_modality($yearprevious, $area->id, 1); ?>
						<?php $totalfields_pres = skills_get_total_fieldsbyarea_and_modality($yearprevious, $area->id, 2); ?>
						<?php $totalfields_sem = skills_get_total_fieldsbyarea_and_modality($yearprevious, $area->id, 3); ?>
						<tr class='r<?php echo $r ?>'>
							<td class='cell table-r<?php echo $r?>-areas' style='text-align: left;'><?php echo $area->name ?></td>
							<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo $totalfields_ead->totalcourses ?></td>
							<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo $totalfields_pres->totalcourses ?></td>
							<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo $totalfields_sem->totalcourses ?></td>
							<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo $totalfields_ead->totalvacancies ?></td>
							<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo $totalfields_pres->totalvacancies ?></td>
							<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo $totalfields_sem->totalvacancies ?></td>
							<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo $totalfields_ead->totalenrollees ?></td>
							<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo $totalfields_pres->totalenrollees ?></td>
							<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo $totalfields_sem->totalenrollees ?></td>
							<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $totalfields_ead->totaltrained ?></td>
							<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $totalfields_pres->totaltrained ?></td>
							<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $totalfields_sem->totaltrained ?></td>
							<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo $totalfields_ead->totaldisapproved ?></td>
							<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo $totalfields_pres->totaldisapproved ?></td>
							<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo $totalfields_sem->totaldisapproved ?></td>
							<td class='cell table-r<?php echo $r?>-nevasao' align='center'><?php echo skills_get_total_evasion_by_area($yearprevious, $area->id, 1) ?></td>
							<td class='cell table-r<?php echo $r?>-nevasao' align='center'><?php echo skills_get_total_evasion_by_area($yearprevious, $area->id, 2) ?></td>
							<td class='cell table-r<?php echo $r?>-nevasao' align='center'> <?php echo skills_get_total_evasion_by_area($yearprevious, $area->id, 3) ?></td>
							<td class='cell table-r<?php echo $r?>-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_area($yearprevious, $area->id, 1) ?>%</td>
							<td class='cell table-r<?php echo $r?>-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_area($yearprevious, $area->id, 2) ?>%</td>
							<td class='cell table-r<?php echo $r?>-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_area($yearprevious, $area->id, 3) ?>%</td>
							<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $totalfields_ead->totalinternalinstructors ?></td>
							<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $totalfields_ead->totalexternallinstructors ?></td>
							<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $totalfields_pres->totalinternalinstructors ?></td>
							<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $totalfields_pres->totalexternallinstructors ?></td>
							<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $totalfields_sem->totalinternalinstructors ?></td>
							<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $totalfields_sem->totalexternallinstructors ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
	
	<?php // echo "<pre>"; var_dump(skills_make_str_in(get_justice_branches('superiores'))); echo "</pre>"; ?>
	
	<!-- TABELA RAMO DA JUSTICA/AREA -->
	<strong class="items_questions">TOTAL DE REGISTROS POR RAMO DA JUSTIÇA/ÁREA</strong>
	
	<?php
	//Justiça Superior
	$inSuperiores = skills_make_str_in(get_justice_branches('superiores'));
	$objJusSuperior = skills_get_total_fields_by_justice_branch($yearprevious, $inSuperiores);
	
	// Justiça eleitoral
	$inEleitoral = skills_make_str_in(get_justice_branches('eleitoral'));
	$objJusEleit = skills_get_total_fields_by_justice_branch($yearprevious, $inEleitoral);
	
	// Justiça do trabalho
	$inTrabalho = skills_make_str_in(get_justice_branches('trabalho'));
	$objJusTrabalho = skills_get_total_fields_by_justice_branch($yearprevious, $inTrabalho);
	
	// TRF
	$inFederal = skills_make_str_in(get_justice_branches('federal'));
	$objJusFederal = skills_get_total_fields_by_justice_branch($yearprevious, $inFederal);
	
	// TJ
	$inEstadual = skills_make_str_in(get_justice_branches('estadual'));
	$objJusEstadual = skills_get_total_fields_by_justice_branch($yearprevious, $inEstadual);
	
	// JM
	$inMilitar = skills_make_str_in(get_justice_branches('militar'));
	$objJusMilitar = skills_get_total_fields_by_justice_branch($yearprevious, $inMilitar);
	?>
	<div class='items_questions'>
		<table class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>RAMO / ÁREAS</th>
				<th class='header table-head0-cursos'>CURSOS</th>
				<th class='header table-head0-vagas'>VAGAS</th>
				<th class='header table-head0-inscritos'>INSCRITOS</th>
				<th class='header table-head0-capacitados'>CAPACITADOS</th>
				<th class='header table-head0-reprovados'>REPROVADOS</th>
			</tr>
			<tr class='table-row-superior'>
				<td class='cell table-row-superior' style='text-align: left;'> JUSTIÇA SUPERIOR </td>
				<td class='cell table-row-superior' align='center'><?php echo (int) $objJusSuperior->totalcourses ?></td>
				<td class='cell table-row-superior' align='center'><?php echo (int) $objJusSuperior->totalvacancies ?></td>
				<td class='cell table-row-superior' align='center'><?php echo (int) $objJusSuperior->totalenrollees ?></td>
				<td class='cell table-row-superior' align='center'><?php echo (int) $objJusSuperior->totaltrained ?></td>
				<td class='cell table-row-superior' align='center'><?php echo (int) $objJusSuperior->totaldisapproved ?></td>
			</tr>
			<tr class='r0'>
				<td class='cell td-agregador' align='center' colspan='6'>
					<table class='generaltable boxaligncenter table-ramo-justica'>
						<?php foreach($objAreas as $objarea): ?>
							<?php $totalAreasSup = skills_get_total_fields_by_area_and_justice_branch($yearprevious, $inSuperiores, $objarea->id); ?>
							<?php $r = $objarea->id %2; ?>
							<tr class='r<?php echo $r ?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 16%'><?php echo $objarea->name?></td>
								<td class='cell table-r<?php echo $r?>-cursos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasSup->totalcourses ?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 14%'><?php echo (int) $totalAreasSup->totalvacancies?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasSup->totalenrollees?></td>
								<td class='cell table-r<?php echo $r?>-capacitados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasSup->totaltrained?></td>
								<td class='cell table-r<?php echo $r?>-reprovados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasSup->totaldisapproved?></td>
							</tr>
						<?php endforeach;?>
					</table>
				</td>
			</tr>
			<tr class='table-row-eleitoral'>
				<td class='cell table-row-eleitoral' style='text-align: left;'> JUSTIÇA ELEITORAL </td>
				<td class='cell table-row-eleitoral' align='center'><?php echo (int) $objJusEleit->totalcourses?></td>
				<td class='cell table-row-eleitoral' align='center'><?php echo (int) $objJusEleit->totalvacancies?></td>
				<td class='cell table-row-eleitoral' align='center'><?php echo (int) $objJusEleit->totalenrollees?></td>
				<td class='cell table-row-eleitoral' align='center'><?php echo (int) $objJusEleit->totaltrained?></td>
				<td class='cell table-row-eleitoral' align='center'><?php echo (int) $objJusEleit->totaldisapproved?></td>
			</tr>
			<tr class='r0'>
				<td class='cell td-agregador' align='center' colspan='6'>
					<table class='generaltable boxaligncenter table-ramo-justica'>
						<?php foreach($objAreas as $objarea) : ?>
							<?php $totalAreasEleit = skills_get_total_fields_by_area_and_justice_branch($yearprevious, $inEleitoral, $objarea->id); ?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 16%'><?php echo $objarea->name?></td>
								<td class='cell table-r<?php echo $r?>-cursos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEleit->totalcourses?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEleit->totalvacancies?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEleit->totalenrollees?></td>
								<td class='cell table-r<?php echo $r?>-capacitados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEleit->totaltrained?></td>
								<td class='cell table-r<?php echo $r?>-reprovados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEleit->totaldisapproved?></td>
							</tr>
						<?php endforeach;?>
					</table>
				</td>
			</tr>
			<tr class='table-row-trabalho'>
				<td class='cell table-row-trabalho' style='text-align: left;'> JUSTIÇA DO TRABALHO </td>
				<td class='cell table-row-trabalho' align='center'><?php echo (int) $objJusTrabalho->totalcourses?></td>
				<td class='cell table-row-trabalho' align='center'><?php echo (int) $objJusTrabalho->totalvacancies?></td>
				<td class='cell table-row-trabalho' align='center'><?php echo (int) $objJusTrabalho->totalenrollees?></td>
				<td class='cell table-row-trabalho' align='center'><?php echo (int) $objJusTrabalho->totaltrained?></td>
				<td class='cell table-row-trabalho' align='center'><?php echo (int) $objJusTrabalho->totaldisapproved?></td>
			</tr>
			<tr class='r0'>
				<td class='cell td-agregador' align='center' colspan='6'>
					<table class='generaltable boxaligncenter table-ramo-justica'>
						<?php foreach($objAreas as $objarea):?>
							<?php $totalAreasTrab = skills_get_total_fields_by_area_and_justice_branch($yearprevious, $inTrabalho, $objarea->id);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 16%'><?php echo $objarea->name?></td>
								<td class='cell table-r<?php echo $r?>-cursos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasTrab->totalcourses?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 14%'><?php echo (int) $totalAreasTrab->totalvacancies?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasTrab->totalenrollees?></td>
								<td class='cell table-r<?php echo $r?>-capacitados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasTrab->totaltrained?></td>
								<td class='cell table-r<?php echo $r?>-reprovados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasTrab->totaldisapproved?></td>
							</tr>
						<?php endforeach;?>
					</table>
				</td>
			</tr>
			<tr class='table-row-federal'>
				<td class='cell table-row-federal' style='text-align: left;'> JUSTIÇA FEDERAL </td>
				<td class='cell table-row-federal' align='center'><?php echo (int) $objJusFederal->totalcourses?></td>
				<td class='cell table-row-federal' align='center'><?php echo (int) $objJusFederal->totalvacancies?></td>
				<td class='cell table-row-federal' align='center'><?php echo (int) $objJusFederal->totalenrollees?></td>
				<td class='cell table-row-federal' align='center'><?php echo (int) $objJusFederal->totaltrained?></td>
				<td class='cell table-row-federal' align='center'><?php echo (int) $objJusFederal->totaldisapproved?></td>
			</tr>
			<tr class='r0'>
				<td class='cell td-agregador' align='center' colspan='6'>
					<table class='generaltable boxaligncenter table-ramo-justica'>
						<?php foreach($objAreas as $objarea):?>
							<?php $totalAreasFed = skills_get_total_fields_by_area_and_justice_branch($yearprevious, $inFederal, $objarea->id);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 16%'><?php echo $objarea->name?></td>
								<td class='cell table-r<?php echo $r?>-cursos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasFed->totalcourses?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 14%'><?php echo (int) $totalAreasFed->totalvacancies?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasFed->totalenrollees?></td>
								<td class='cell table-r<?php echo $r?>-capacitados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasFed->totaltrained?></td>
								<td class='cell table-r<?php echo $r?>-reprovados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasFed->totaldisapproved?></td>
							 </tr>
						<?php endforeach;?>
					</table>
				</td>
			</tr>
			<tr class='table-row-estadual'>
				<td class='cell table-row-estadual' style='text-align: left;'> JUSTIÇA ESTADUAL </td>
				<td class='cell table-row-estadual' align='center'><?php echo (int) $objJusEstadual->totalcourses?></td>
				<td class='cell table-row-estadual' align='center'><?php echo (int) $objJusEstadual->totalvacancies?></td>
				<td class='cell table-row-estadual' align='center'><?php echo (int) $objJusEstadual->totalenrollees?></td>
				<td class='cell table-row-estadual' align='center'><?php echo (int) $objJusEstadual->totaltrained?></td>
				<td class='cell table-row-estadual' align='center'><?php echo (int) $objJusEstadual->totaldisapproved?></td>
			</tr>
			<tr class='r0'>
				<td class='cell td-agregador' align='center' colspan='6'>
					<table class='generaltable boxaligncenter table-ramo-justica'>
						<?php foreach($objAreas as $objarea):?>
							<?php $totalAreasEst = skills_get_total_fields_by_area_and_justice_branch($yearprevious, $inEstadual, $objarea->id);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
									<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 16%'><?php echo $objarea->name?></td>
									<td class='cell table-r<?php echo $r?>-cursos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEst->totalcourses?></td>
									<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEst->totalvacancies?></td>
									<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEst->totalenrollees?></td>
									<td class='cell table-r<?php echo $r?>-capacitados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEst->totaltrained?></td>
									<td class='cell table-r<?php echo $r?>-reprovados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasEst->totaldisapproved?></td>
								 </tr>
						<?php endforeach;?>
				</table>
				</td>
			</tr>
			
			<tr class='table-row-militar'>
				<td class='cell table-row-militar' style='text-align: left;'> JUSTIÇA MILITAR </td>
				<td class='cell table-row-militar' align='center'><?php echo (int) $objJusMilitar->totalcourses?></td>
				<td class='cell table-row-militar' align='center'><?php echo (int) $objJusMilitar->totalvacancies?></td>
				<td class='cell table-row-militar' align='center'><?php echo (int) $objJusMilitar->totalenrollees?></td>
				<td class='cell table-row-militar' align='center'><?php echo (int) $objJusMilitar->totaltrained?></td>
				<td class='cell table-row-militar' align='center'><?php echo (int) $objJusMilitar->totaldisapproved?></td>
			</tr>
			<tr class='r0'>
				<td class='cell td-agregador' align='center' colspan='6'>
					<table class='generaltable boxaligncenter table-ramo-justica'>
						<?php foreach($objAreas as $objarea):?>
							<?php $totalAreasMil = skills_get_total_fields_by_area_and_justice_branch($yearprevious, $inMilitar, $objarea->id);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 16%'><?php echo $objarea->name ?></td>
								<td class='cell table-r<?php echo $r?>-cursos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasMil->totalcourses?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 14%'><?php echo (int) $totalAreasMil->totalvacancies?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 14%'><?php echo (int) $totalAreasMil->totalenrollees?></td>
								<td class='cell table-r<?php echo $r?>-capacitados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasMil->totaltrained?></td>
								<td class='cell table-r<?php echo $r?>-reprovados' style='text-align: center; width: 14%'><?php echo (int) $totalAreasMil->totaldisapproved?></td>
							 </tr>
						<?php endforeach;?>
					</table>
				</td>
			</tr>
		</table>
	</div>
	
	<!-- Detalhamento completo por ramo da justiça -->
	<strong>DETALHAMENTO DE CURSOS, VAGAS, INSCRITOS, CAPACITADOS, REPROVADOS E INSTRUTORES POR RAMO DA JUSTIÇA</strong>
	<div class='bigtable'>
		<table class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>#</th>
				<th class='header table-head0-cursos' colspan='3'>CURSOS</th>
				<th class='header table-head0-vagas' colspan='3'>VAGAS</th>
				<th class='header table-head0-inscritos' colspan='3'>INSCRITOS</th>
				<th class='header table-head0-capacitados' colspan='3'>CAPACITADOS</th>
				<th class='header table-head0-reprovados' colspan='3'>REPROVADOS</th>
				<th class='header table-head0-nevasao' colspan='3'>Nº EVS</th>
				<th class='header table-head0-pevasao' colspan='3'>% EVS</th>
				<th class='header table-head0-instrutores' colspan='6'>INSTRUTORES</th>
			</tr>
			<tr>
				<th class='header table-head1-areas'>RAMO (RELATÓRIOS)</th>
				<th class='header table-head1-cursos'>EAD</th>
				<th class='header table-head1-cursos'>PRES.</th>
				<th class='header table-head1-cursos'>SEM.</th>
				<th class='header table-head1-vagas'>EAD</th>
				<th class='header table-head1-vagas'>PRES.</th>
				<th class='header table-head1-vagas'>SEM.</th>
				<th class='header table-head1-inscritos'>EAD</th>
				<th class='header table-head1-inscritos'>PRES.</th>
				<th class='header table-head1-inscritos'>SEM.</th>
				<th class='header table-head1-capacitados'>EAD</th>
				<th class='header table-head1-capacitados'>PRES.</th>
				<th class='header table-head1-capacitados'>SEM.</th>
				<th class='header table-head1-reprovados'>EAD</th>
				<th class='header table-head1-reprovados'>PRES.</th>
				<th class='header table-head1-reprovados'>SEM.</th>
				<th class='header table-head1-nevasao'>EAD</th>
				<th class='header table-head1-nevasao'>PRE</th>
				<th class='header table-head1-nevasao'>SEM.</th>
				<th class='header table-head1-pevasao'>EAD</th>
				<th class='header table-head1-pevasao'>PRE</th>
				<th class='header table-head1-pevasao'>SEM.</th>
				<th class='header table-head1-instrutores'>INT. EAD</th>
				<th class='header table-head1-instrutores'>INT. PRES.</th>
				<th class='header table-head1-instrutores'>INT. SEM.</th>
				<th class='header table-head1-instrutores'>EXT. EAD</th>
				<th class='header table-head1-instrutores'>EXT. PRES.</th>
				<th class='header table-head1-instrutores'>EXT. SEM.</th>
			</tr>
	
		<?php 
		//Justiça Superior
		$objFieldsSupEad  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inSuperiores, 1);
		$objFieldsSupPres = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inSuperiores, 2);
		$objFieldsSupSem  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inSuperiores, 3);
		$totalDtfSup = skills_get_total_dataformbybranchjustice($yearprevious, $inSuperiores);
		?>
		<tr class='r0'>
			<td class='cell table-r1-areas' style='text-align: left;'>JUSTIÇA SUPERIOR (<?php echo (int) $totalDtfSup->totalbybranch; ?>)</td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsSupEad->totalcourses?></td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsSupPres->totalcourses ?></td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsSupSem->totalcourses?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsSupEad->totalvacancies?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsSupPres->totalvacancies?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsSupSem->totalvacancies?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsSupEad->totalenrollees?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsSupPres->totalenrollees?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsSupSem->totalenrollees?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsSupEad->totaltrained ?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsSupPres->totaltrained?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsSupSem->totaltrained?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsSupEad->totaldisapproved ?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsSupPres->totaldisapproved?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsSupSem->totaldisapproved?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 1, $inSuperiores)?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 2, $inSuperiores)?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 3, $inSuperiores)?></td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 1, $inSuperiores)?>%</td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 2, $inSuperiores)?>%</td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 3, $inSuperiores)?>%</td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsSupEad->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsSupPres->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsSupSem->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsSupEad->totalexternallinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsSupPres->totalexternallinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsSupSem->totalexternallinstructors?></td>
		</tr>
		<?php 
		// Justiça eleitoral
		$objFieldsEleitEad  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inEleitoral, 1);
		$objFieldsEleitPres = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inEleitoral, 2);
		$objFieldsEleitSem  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inEleitoral, 3);
		$totalDtfEleit = skills_get_total_dataformbybranchjustice($yearprevious, $inEleitoral);
		?>
		<tr class='r0'>
				<td class='cell table-r0-areas' style='text-align: left;'>JUSTIÇA ELEITORAL (<?php echo (int) $totalDtfEleit->totalbybranch; ?>)</td>
				<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsEleitEad->totalcourses?></td>
				<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsEleitPres->totalcourses?></td>
				<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsEleitSem->totalcourses?></td>
				<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsEleitEad->totalvacancies?></td>
				<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsEleitPres->totalvacancies?></td>
				<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsEleitSem->totalvacancies?></td>
				<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsEleitEad->totalenrollees?></td>
				<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsEleitPres->totalenrollees?></td>
				<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsEleitSem->totalenrollees?></td>
				<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsEleitEad->totaltrained?></td>
				<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsEleitPres->totaltrained?></td>
				<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsEleitSem->totaltrained?></td>
				<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsEleitEad->totaldisapproved?></td>
				<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsEleitPres->totaldisapproved?></td>
				<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsEleitSem->totaldisapproved?></td>
				<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 1, $inEleitoral)?></td>
				<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 2, $inEleitoral)?></td>
				<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 3, $inEleitoral)?></td>
				<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 1, $inEleitoral)?>%</td>
				<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 2, $inEleitoral)?>%</td>
				<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 3, $inEleitoral)?>%</td>
				<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsEleitEad->totalinternalinstructors?></td>
				<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsEleitPres->totalinternalinstructors?></td>
				<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsEleitSem->totalinternalinstructors?></td>
				<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsEleitEad->totalexternallinstructors?></td>
				<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsEleitPres->totalexternallinstructors?></td>
				<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsEleitSem->totalexternallinstructors?></td>
			</tr>
		<?php 
		// Justiça do trabalho
		$objFieldsTrabEad  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inTrabalho, 1);
		$objFieldsTrabPres = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inTrabalho, 2);
		$objFieldsTrabSem  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inTrabalho, 3);
		$totalDtfTrab = skills_get_total_dataformbybranchjustice($yearprevious, $inTrabalho);
		?>
		<tr class='r0'>
			<td class='cell table-r1-areas' style='text-align: left;'>JUSTIÇA DO TRABALHO (<?php echo (int) $totalDtfTrab->totalbybranch; ?>)</td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsTrabEad->totalcourses?></td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsTrabPres->totalcourses?></td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsTrabSem->totalcourses?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsTrabEad->totalvacancies?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsTrabPres->totalvacancies?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsTrabSem->totalvacancies?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsTrabEad->totalenrollees?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsTrabPres->totalenrollees?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsTrabSem->totalenrollees?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsTrabEad->totaltrained?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsTrabPres->totaltrained?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsTrabSem->totaltrained?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsTrabEad->totaldisapproved?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsTrabPres->totaldisapproved?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsTrabSem->totaldisapproved?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 1, $inTrabalho)?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 2, $inTrabalho)?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 3, $inTrabalho)?></td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 1, $inTrabalho)?>%</td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 2, $inTrabalho)?>%</td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 3, $inTrabalho)?>%</td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsTrabEad->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsTrabPres->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsTrabSem->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsTrabEad->totalexternallinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsTrabPres->totalexternallinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsTrabSem->totalexternallinstructors?></td>
		</tr>
		<?php 		
		// Justiça Federal
		$objFieldsFedEad  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inFederal, 1);
		$objFieldsFedPres = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inFederal, 2);
		$objFieldsFedSem  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inFederal, 3);
		$totalDtfFed = skills_get_total_dataformbybranchjustice($yearprevious, $inFederal);
		?>
		<tr class='r0'>
			<td class='cell table-r0-areas' style='text-align: left;'>JUSTIÇA FEDERAL (<?php echo (int) $totalDtfFed->totalbybranch; ?>)</td>
			<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsFedEad->totalcourses?></td>
			<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsFedPres->totalcourses?></td>
			<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsFedSem->totalcourses?></td>
			<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsFedEad->totalvacancies?></td>
			<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsFedPres->totalvacancies?></td>
			<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsFedSem->totalvacancies?></td>
			<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsFedEad->totalenrollees?></td>
			<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsFedPres->totalenrollees?></td>
			<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsFedSem->totalenrollees?></td>
			<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsFedEad->totaltrained?></td>
			<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsFedPres->totaltrained?></td>
			<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsFedSem->totaltrained?></td>
			<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsFedEad->totaldisapproved?></td>
			<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsFedPres->totaldisapproved?></td>
			<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsFedSem->totaldisapproved?></td>
			<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 1, $inFederal)?></td>
			<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 2, $inFederal)?></td>
			<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 3, $inFederal)?></td>
			<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 1, $inFederal)?>%</td>
			<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 2, $inFederal)?>%</td>
			<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 3, $inFederal)?>%</td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsFedEad->totalinternalinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsFedPres->totalinternalinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsFedSem->totalinternalinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsFedEad->totalexternallinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsFedPres->totalexternallinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsFedSem->totalexternallinstructors?></td>
		</tr>
		<?php 
		// Justiça estadual
		$objFieldsEstEad  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inEstadual, 1);
		$objFieldsEstPres = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inEstadual, 2);
		$objFieldsEstSem  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inEstadual, 3);
		$totalDtfEst = skills_get_total_dataformbybranchjustice($yearprevious, $inEstadual);
		?>
		<tr class='r0'>
			<td class='cell table-r1-areas' style='text-align: left;'>JUSTIÇA ESTADUAL (<?php echo (int) $totalDtfEst->totalbybranch; ?>)</td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsEstEad->totalcourses?></td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsEstPres->totalcourses?></td>
			<td class='cell table-r1-cursos' align='center'><?php echo (int) $objFieldsEstSem->totalcourses?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsEstEad->totalvacancies?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsEstPres->totalvacancies?></td>
			<td class='cell table-r1-vagas' align='center'><?php echo (int) $objFieldsEstSem->totalvacancies?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsEstEad->totalenrollees?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsEstPres->totalenrollees?></td>
			<td class='cell table-r1-inscritos' align='center'><?php echo (int) $objFieldsEstSem->totalenrollees?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsEstEad->totaltrained?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsEstPres->totaltrained?></td>
			<td class='cell table-r1-capacitados' align='center'><?php echo (int) $objFieldsEstSem->totaltrained?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsEstEad->totaldisapproved?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsEstPres->totaldisapproved?></td>
			<td class='cell table-r1-reprovados' align='center'><?php echo (int) $objFieldsEstSem->totaldisapproved?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 1, $inEstadual)?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 2, $inEstadual)?></td>
			<td class='cell table-r1-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 3, $inEstadual)?></td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 1, $inEstadual)?>%</td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 2, $inEstadual)?>%</td>
			<td class='cell table-r1-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 3, $inEstadual)?>%</td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsEstEad->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsEstPres->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsEstSem->totalinternalinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsEstEad->totalexternallinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsEstPres->totalexternallinstructors?></td>
			<td class='cell table-r1-instrutores' align='center'><?php echo (int) $objFieldsEstSem->totalexternallinstructors?></td>
		</tr>
		<?php 
		// Justiça militar
		$objFieldsMilEad  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inMilitar, 1);
		$objFieldsMilPres = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inMilitar, 2);
		$objFieldsMilSem  = skills_get_total_fieldsbybranchjustice_and_modality($yearprevious, $inMilitar, 3);
		$totalDtfMil = skills_get_total_dataformbybranchjustice($yearprevious, $inMilitar);
		?>
		<tr class='r0'>
			<td class='cell table-r0-areas' style='text-align: left;'>JUSTIÇA MILITAR (<?php echo (int) $totalDtfMil->totalbybranch; ?>)</td>
			<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsMilEad->totalcourses?></td>
			<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsMilPres->totalcourses?></td>
			<td class='cell table-r0-cursos' align='center'><?php echo (int) $objFieldsMilSem->totalcourses?></td>
			<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsMilEad->totalvacancies?></td>
			<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsMilPres->totalvacancies?></td>
			<td class='cell table-r0-vagas' align='center'><?php echo (int) $objFieldsMilSem->totalvacancies?></td>
			<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsMilEad->totaltrained?></td>
			<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsMilPres->totaltrained?></td>
			<td class='cell table-r0-inscritos' align='center'><?php echo (int) $objFieldsMilSem->totaltrained?></td>
			<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsMilEad->totaltrained?></td>
			<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsMilPres->totaltrained?></td>
			<td class='cell table-r0-capacitados' align='center'><?php echo (int) $objFieldsMilSem->totaltrained?></td>
			<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsMilEad->totaldisapproved?></td>
			<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsMilPres->totaldisapproved?></td>
			<td class='cell table-r0-reprovados' align='center'><?php echo (int) $objFieldsMilSem->totaldisapproved?></td>
			<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 1, $inMilitar)?></td>
			<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 2, $inMilitar)?></td>
			<td class='cell table-r0-nevasao' align='center'><?php echo skills_get_total_evasion_by_justice_branch($yearprevious, 3, $inMilitar)?></td>
			<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 1, $inMilitar)?>%</td>
			<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 2, $inMilitar)?>%</td>
			<td class='cell table-r0-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_justice_branch($yearprevious, 3, $inMilitar)?>%</td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsMilEad->totalinternalinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsMilPres->totalinternalinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsMilSem->totalinternalinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsMilEad->totalexternallinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsMilPres->totalexternallinstructors?></td>
			<td class='cell table-r0-instrutores' align='center'><?php echo (int) $objFieldsMilSem->totalexternallinstructors?></td>
		</tr>		
	</table>
	</div>
	
	<!-- Detalhamento por orgaos -->
	<strong class="question">DETALHAMENTO DE DADOS POR ÓRGÃO</strong>
	<div class='bigtable'>
		<table width='100%' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>#</th>
				<th class='header table-head0-cursos' colspan='4'>CURSOS</th>
				<th class='header table-head0-vagas' colspan='4'>VAGAS</th>
				<th class='header table-head0-inscritos' colspan='4'>INSCRITOS</th>
				<th class='header table-head0-capacitados' colspan='5'>CAPACITADOS</th>
				<th class='header table-head0-reprovados' colspan='4'>REPROVADOS</th>
				<th class='header table-head0-instrutores' colspan='2'>INST.</th>
				<th class='header table-head0-nevasao' colspan='3'>Nº EVS</th>
				<th class='header table-head0-pevasao' colspan='3'>% EVS</th>
				<th class='header table-head0-orcamento' colspan='2'>ORÇAMENTO <?php echo $yearprevious?></th>
			</tr>
			<tr>
				<th class='header table-head1-areas'>ORG</th>
				<th class='header table-head1-cursos'>EAD</th>
				<th class='header table-head1-cursos'>PRE</th>
				<th class='header table-head1-cursos'>SEM</th>
				<th class='header table-head1-cursos'>TOTAL</th>
				<th class='header table-head1-vagas'>EAD</th>
				<th class='header table-head1-vagas'>PRE</th>
				<th class='header table-head1-vagas'>SEM</th>
				<th class='header table-head1-vagas'>TOTAL</th>
				<th class='header table-head1-inscritos'>EAD</th>
				<th class='header table-head1-inscritos'>PRE</th>
				<th class='header table-head1-inscritos'>SEM</th>
				<th class='header table-head1-inscritos'>TOTAL</th>
				<th class='header table-head1-capacitados'>EAD</th>
				<th class='header table-head1-capacitados'>PRE</th>
				<th class='header table-head1-capacitados'>SEM</th>
				<th class='header table-head1-capacitados'>POS</th>
				<th class='header table-head1-capacitados'>TOTAL</th>
				<th class='header table-head1-reprovados'>EAD</th>
				<th class='header table-head1-reprovados'>PRE</th>
				<th class='header table-head1-reprovados'>SEM</th>
				<th class='header table-head1-reprovados'>TOTAL</th>
				<th class='header table-head1-instrutores'>EXT</th>
				<th class='header table-head1-instrutores'>INT</th>
				<th class='header table-head1-nevasao'>EAD</th>
				<th class='header table-head1-nevasao'>PRE</th>
				<th class='header table-head1-nevasao'>SEM</th>
				<th class='header table-head1-pevasao'>EAD</th>
				<th class='header table-head1-pevasao'>PRE</th>
				<th class='header table-head1-pevasao'>SEM</th>
				<th class='header table-head1-orcamento'>TOTAL DOT.</th>
				<th class='header table-head1-orcamento'>TOTAL EXE.</th>
			</tr>
	 
		<?php $objDataforms = skills_get_total_dataform($yearprevious); ?>
		<?php $r = 0; $totalbt = 0; $totalrv = 0; ?>
		
		<?php foreach ($objDataforms as $dataform):?>
			<?php $r = $r %2;?>
			<tr class='r<?php echo $r?>'>
				<td class='cell table-r<?php echo $r?>-areas' style='text-align: center;'><?php echo strtoupper($dataform->organ)?></td>
				<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo $dataform->courses_ead?></td>
				<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo $dataform->courses_classroom?></td>
				<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo $dataform->courses_sem?></td>
				<td class='cell table-r<?php echo $r?>-cursos' align='center'><?php echo ($dataform->courses_ead + $dataform->courses_classroom + $dataform->courses_sem)?></td>
				<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo $dataform->numbervacancies_ead?></td>
				<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo $dataform->numbervacancies_classroom?></td>
				<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo $dataform->numbervacancies_sem?></td>
				<td class='cell table-r<?php echo $r?>-vagas' align='center'><?php echo ($dataform->numbervacancies_ead + $dataform->numbervacancies_classroom + $dataform->numbervacancies_sem)?></td>
				<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo $dataform->numberenrollees_ead?></td>
				<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo $dataform->numberenrollees_classroom?></td>
				<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo $dataform->numberenrollees_sem?></td>
				<td class='cell table-r<?php echo $r?>-inscritos' align='center'><?php echo ($dataform->numberenrollees_ead + $dataform->numberenrollees_classroom + $dataform->numberenrollees_sem)?></td>
				<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $dataform->trained_ead?></td>
				<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $dataform->trained_classroom?></td>
				<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $dataform->trained_sem?></td>
				<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo $dataform->participants_pos?></td>
				<td class='cell table-r<?php echo $r?>-capacitados' align='center'><?php echo ($dataform->participants_pos + $dataform->participants_cursos)?></td>
				<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo $dataform->numberdisapproved_ead?></td>
				<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo $dataform->numberdisapproved_classroom?></td>
				<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo $dataform->numberdisapproved_sem?></td>
				<td class='cell table-r<?php echo $r?>-reprovados' align='center'><?php echo ($dataform->numberdisapproved_ead + $dataform->numberdisapproved_classroom + $dataform->numberdisapproved_sem)?></td>
				<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $dataform->total_numberexternallinstructors?></td>
				<td class='cell table-r<?php echo $r?>-instrutores' align='center'><?php echo $dataform->total_numberinternalinstructors?></td>
				<td class='cell table-r<?php echo $r?>-nevasao' align='center'><?php echo skills_get_total_evasion_by_dataformid($dataform->id, 1)?></td>
				<td class='cell table-r<?php echo $r?>-nevasao' align='center'><?php echo skills_get_total_evasion_by_dataformid($dataform->id, 2)?></td>
				<td class='cell table-r<?php echo $r?>-nevasao' align='center'><?php echo skills_get_total_evasion_by_dataformid($dataform->id, 3)?></td>
				<td class='cell table-r<?php echo $r?>-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_dataformid($dataform->id, 1)?>%</td>
				<td class='cell table-r<?php echo $r?>-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_dataformid($dataform->id, 2)?>%</td>
				<td class='cell table-r<?php echo $r?>-pevasao' align='center'><?php echo skills_get_percentual_evasion_by_dataformid($dataform->id, 3)?>%</td>
				<td class='cell table-r<?php echo $r?>-orcamento' align='center'><?php echo $dataform->budgettraining?></td>
				<td class='cell table-r<?php echo $r?>-orcamento' align='center'><?php echo $dataform->runvalue?></td>
			</tr>
			<?php 
			$r ++;
			// CALCULOS
			$totalbt = $totalbt + skills_set_format_moeda($dataform->budgettraining);
			$totalrv = $totalrv + skills_set_format_moeda($dataform->runvalue);
			?>
		<?php endforeach;?>
		</table>
	</div>
	<?php 
	// Total de cursos
	$totalCoursesEad = skills_get_total_fields_report($yearprevious, 1);
	$totalCoursesPres = skills_get_total_fields_report($yearprevious, 2);
	$totalCoursesSem = skills_get_total_fields_report($yearprevious, 3);
	?>
	<strong class="items_questions">TOTAL DE CURSOS </strong>
	<div class='items_questions'>
		<table class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>Nº TOTAL DE CURSOS EAD</th>
				<th class='header table-head0-areas'>Nº TOTAL DE CURSOS PRESENCIAIS</th>
				<th class='header table-head0-areas'>Nº TOTAL DE CURSOS SEMIPRESENCIAIS</th>
			</tr>
			<tr class='r0'>
			<td class='cell table-r1-areas'> <?php echo $totalCoursesEad->totalcourses?></td>
			<td class='cell table-r1-areas'> <?php echo $totalCoursesPres->totalcourses?></td>
			<td class='cell table-r1-areas'> <?php echo $totalCoursesSem->totalcourses?></td>
		</table>
	</div>
	
	<?php 
	// Recupera todos os trainings (POS-GRADUACAO, PALESTRAS, CONGRESSOS, ENCONTROS, SEMINARIOS, FORUNS)
	$objtrainings = skills_get_trainings();
	
	foreach($objtrainings as $objtraining) :
		if(trim($objtraining->name) !== 'OUTRO(S):') :
		// Justiça Superior
		$objTngSup = skills_get_total_fields_training_by_justice_branch($yearprevious, $inSuperiores, $objtraining->id);
		
		// Justiça eleitoral
		$objTngEleit = skills_get_total_fields_training_by_justice_branch($yearprevious, $inEleitoral, $objtraining->id);
		
		// Justiça do trabalho
		$objTngTrab = skills_get_total_fields_training_by_justice_branch($yearprevious, $inTrabalho, $objtraining->id);
		
		// Justiça federal
		$objTngFed = skills_get_total_fields_training_by_justice_branch($yearprevious, $inFederal, $objtraining->id);
		
		// Justiça de estadual
		$objTngEst = skills_get_total_fields_training_by_justice_branch($yearprevious, $inEstadual, $objtraining->id);
		
		// Justiça militar
		$objTngMil = skills_get_total_fields_training_by_justice_branch($yearprevious, $inMilitar, $objtraining->id);
		?>
		<strong class="items_questions"><?php echo $objtraining->name; ?></strong>
		<div class='items_questions'>
			<table class='generaltable boxaligncenter'>
				<tr>
					<th class='header table-head0-areas'>RAMO / ÁREA</th>
					<th class='header table-head0-areas'>TOTAL DE PARTICIPANTES</th>
					<th class='header table-head0-areas'>TOTAL DE <?php echo $objtraining->name; ?></th>
				</tr>
				<tr class='r1'>
					<td class='cell table-row-superior' style='text-align: left; width: 40%'> JUSTIÇA SUPERIOR </td>
					<td class='cell table-row-superior' align='center' style="width: 30%"><?php echo (int) $objTngSup->totalparticipants?></td>
					<td class='cell table-row-superior' align='center' style="width: 29%"><?php echo (int) $objTngSup->totalnumbertraining?></td>
				</tr>
				<tr class='r0'>
					<td class='cell td-agregador' align='center' colspan='3'>
						<table class='generaltable boxaligncenter table-ramo-justica'>
						
						<?php foreach($objAreas as $objarea):?>
							<?php $totalTngByAreaAndBranchSup = skills_get_total_trainings_by_area_and_justice_branch($yearprevious, $objtraining->id, $objarea->name, $inSuperiores);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 40%'><?php echo $objarea->name;?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchSup->totalparticipants; ?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchSup->totalnumbertraining; ?></td>
							</tr>
						<?php endforeach;?>
					</table>
					</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-row-eleitoral' style='text-align: left;'> JUSTIÇA ELEITORAL </td>
					<td class='cell table-row-eleitoral' align='center'><?php echo (int) $objTngEleit->totalparticipants?></td>
					<td class='cell table-row-eleitoral' align='center'><?php echo (int) $objTngEleit->totalnumbertraining?></td>
				</tr>
				<tr class='r0'>
					<td class='cell td-agregador' align='center' colspan='3'>
						<table class='generaltable boxaligncenter table-ramo-justica'>
						
						<?php foreach($objAreas as $objarea):?>
							<?php $totalTngByAreaAndBranchEleit = skills_get_total_trainings_by_area_and_justice_branch($yearprevious, $objtraining->id, $objarea->name, $inEleitoral);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 40%'><?php echo $objarea->name;?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchEleit->totalparticipants; ?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchEleit->totalnumbertraining; ?></td>
							</tr>
						<?php endforeach;?>
					</table>
					</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-row-trabalho' style='text-align: left;'> JUSTIÇA DO TRABALHO </td>
						<td class='cell table-row-trabalho' align='center'><?php echo (int) $objTngTrab->totalparticipants?></td>
						<td class='cell table-row-trabalho' align='center'><?php echo (int) $objTngTrab->totalnumbertraining?></td>
				</tr>
				<tr class='r0'>
					<td class='cell td-agregador' align='center' colspan='3'>
						<table class='generaltable boxaligncenter table-ramo-justica'>
						
						<?php foreach($objAreas as $objarea):?>
							<?php $totalTngByAreaAndBranchTrab = skills_get_total_trainings_by_area_and_justice_branch($yearprevious, $objtraining->id, $objarea->name, $inTrabalho);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 40%'><?php echo $objarea->name;?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchTrab->totalparticipants; ?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchTrab->totalnumbertraining; ?></td>
							</tr>
						<?php endforeach;?>
					</table>
					</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-row-federal' style='text-align: left;'> JUSTIÇA FEDERAL </td>
					<td class='cell table-row-federal' align='center'><?php echo (int) $objTngFed->totalparticipants?></td>
					<td class='cell table-row-federal' align='center'><?php echo (int) $objTngFed->totalnumbertraining?></td>
				</tr>
				<tr class='r0'>
					<td class='cell td-agregador' align='center' colspan='3'>
						<table class='generaltable boxaligncenter table-ramo-justica'>
						
						<?php foreach($objAreas as $objarea):?>
							<?php $totalTngByAreaAndBranchFed = skills_get_total_trainings_by_area_and_justice_branch($yearprevious, $objtraining->id, $objarea->name, $inFederal);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 40%'><?php echo $objarea->name;?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchFed->totalparticipants; ?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchFed->totalnumbertraining; ?></td>
							</tr>
						<?php endforeach;?>
					</table>
					</td>
				</tr>
				<tr class='r1'>
					<td class='cell table-row-estadual' style='text-align: left;'> JUSTIÇA ESTADUAL </td>
					<td class='cell table-row-estadual' align='center'><?php echo (int) $objTngEst->totalparticipants?></td>
					<td class='cell table-row-estadual' align='center'><?php echo (int) $objTngEst->totalnumbertraining?></td>
				</tr>
				<tr class='r0'>
					<td class='cell td-agregador' align='center' colspan='3'>
						<table class='generaltable boxaligncenter table-ramo-justica'>
						
						<?php foreach($objAreas as $objarea):?>
							<?php $totalTngByAreaAndBranchEst = skills_get_total_trainings_by_area_and_justice_branch($yearprevious, $objtraining->id, $objarea->name, $inEstadual);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 40%'><?php echo $objarea->name;?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchEst->totalparticipants; ?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchEst->totalnumbertraining; ?></td>
							</tr>
						<?php endforeach;?>
					</table>
					</td>
				</tr>
				<tr class='r0'>
					<td class='cell table-row-militar' style='text-align: left;'> JUSTIÇA MILITAR </td>
					<td class='cell table-row-militar' align='center'><?php echo (int) $objTngMil->totalparticipants?></td>
					<td class='cell table-row-militar' align='center'><?php echo (int) $objTngMil->totalnumbertraining?></td>
				</tr>
				<tr class='r0'>
					<td class='cell td-agregador' align='center' colspan='3'>
						<table class='generaltable boxaligncenter table-ramo-justica'>
						
						<?php foreach($objAreas as $objarea):?>
							<?php $totalTngByAreaAndBranchMil = skills_get_total_trainings_by_area_and_justice_branch($yearprevious, $objtraining->id, $objarea->name, $inMilitar);?>
							<?php $r = $objarea->id %2;?>
							<tr class='r<?php echo $r?>'>
								<td class='cell table-r<?php echo $r?>-areas' style='text-align: left; width: 40%'><?php echo $objarea->name;?></td>
								<td class='cell table-r<?php echo $r?>-vagas' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchMil->totalparticipants; ?></td>
								<td class='cell table-r<?php echo $r?>-inscritos' style='text-align: center; width: 29%'><?php echo (int) $totalTngByAreaAndBranchMil->totalnumbertraining; ?></td>
							</tr>
						<?php endforeach;?>
					</table>
					</td>
				</tr>
			</table>
		</div>
	<?php endif;?>
	<?php endforeach;?>
	
	<!-- HA ACOES DE CAPACITACAO -->
	<?php $trainingactionsocietyno = skills_get_total_field_dataform($yearprevious,"trainingactionsociety", "=", "no");?>
	<?php $trainingactionsocietyyes = skills_get_total_field_dataform($yearprevious,"trainingactionsociety", "<>", "no");?>
	
	<strong class="question">3. HÁ AÇÕES DE CAPACITAÇÃO ABERTAS À SOCIEDADE?</strong>
	<table id='resources' class='items_questions generaltable boxaligncenter'>
		<tr>
			<th class='header table-head0-areas'>NÃO</th>
			<th class='header'>SIM</th>
		</tr>
		<tr class='r0'>
			<td class='cell' align='center'><?php echo $trainingactionsocietyno ?></td>
			<td class='cell' align='center'><?php echo $trainingactionsocietyyes ?></td>
		</tr>
	</table>
	<?php $tngsociety = skills_get_trainingactionsociety($yearprevious)?>
	<?php $tturmaspres = 0; $tturmaseadct = 0; $tturmaseadst = 0; $tturmassemi = 0;?>
		<?php $tinscritospres = 0; $tinscritoseadct = 0; $tinscritoseadst = 0; $tinscritossemi = 0;?>
		<?php $tcapacitadospres = 0; $tcapacitadoseadct = 0; $tcapacitadoseadst = 0; $tcapacitadossemi = 0;?>
		<?php if ($tngsociety): ?>
			<?php foreach ($tngsociety as $tng):?>
				<?php $objtngsociety = json_decode($tng->trainingactionsociety);?>
				<?php $tturmaspres += $objtngsociety->nturmas->presencial;?>
				<?php $tturmaseadct += $objtngsociety->nturmas->ead->comtutoria;?>
				<?php $tturmaseadst += $objtngsociety->nturmas->ead->semtutoria;?>
				<?php $tturmassemi += $objtngsociety->nturmas->semipresencial;?>
				<?php $tinscritospres += $objtngsociety->ninscritos->presencial;?>
				<?php $tinscritoseadct += $objtngsociety->ninscritos->ead->comtutoria;?>
				<?php $tinscritoseadst += $objtngsociety->ninscritos->ead->semtutoria;?>
				<?php $tinscritossemi += $objtngsociety->ninscritos->semipresencial;?>
				<?php $tcapacitadospres += $objtngsociety->ncapacitados->presencial;?>
				<?php $tcapacitadoseadct += $objtngsociety->ncapacitados->ead->comtutoria;?>
				<?php $tcapacitadoseadst += $objtngsociety->ncapacitados->ead->semtutoria;?>
				<?php $tcapacitadossemi += $objtngsociety->ncapacitados->semipresencial;?>
			<?php endforeach;?>
		<?php endif;?>
	<table id='trainingactionsociety' class='items_questions generaltable'>
		<tr>
			<th class='header table-head0-areas' rowspan='2'>DADOS / MODALIDADE</th>
			<th class='header table-head1-areas' style='text-align: center;' rowspan='2'>PRESENCIAL</th>
			<th class='header table-head1-areas' style='text-align: center;' colspan='2'>EAD</th>
			<th class='header table-head1-areas' style='text-align: center;' rowspan='2'>SEMIPRESENCIAL</th>
		</tr>
		<tr style='background-color: #f9f9f9;'>
			<th class='header table-head1-areas' style='text-align: center; border: 0; padding-top: 0;'>Com tutoria</th>
			<th class='header table-head1-areas' style='text-align: center; border: 0; padding-top: 0;'>Sem tutoria</th>
		</tr>
		<tr>
			<td class='cell table-head0-cursos'>Nº DE TURMAS</td>
			<td class='cell table-r0-cursos'><?php echo $tturmaspres; ?></td>
			<td class='cell table-r0-cursos'><?php echo $tturmaseadct; ?></td>
			<td class='cell table-r0-cursos'><?php echo $tturmaseadst; ?></td>
			<td class='cell table-r0-cursos'><?php echo $tturmassemi; ?></td>
		</tr>
		<tr>
			<td class='cell table-head0-vagas'>Nº DE INSCRITOS</td>
			<td class='cell table-r0-vagas'><?php echo $tinscritospres; ?></td>
			<td class='cell table-r0-vagas'><?php echo $tinscritoseadct; ?></td>
			<td class='cell table-r0-vagas'><?php echo $tinscritoseadst; ?></td>
			<td class='cell table-r0-vagas'><?php echo $tinscritossemi; ?></td>
		</tr>
		<tr>
			<td class='cell table-head0-capacitados'>Nº DE CAPACITADOS</td>
			<td class='cell table-r0-capacitados'><?php echo $tcapacitadospres; ?></td>
			<td class='cell table-r0-capacitados'><?php echo $tcapacitadoseadct; ?></td>
			<td class='cell table-r0-capacitados'><?php echo $tcapacitadoseadst; ?></td>
			<td class='cell table-r0-capacitados'><?php echo $tcapacitadossemi; ?></td>
		</tr>
	</table>	
	
	<!-- ESTRUTURA DE CAPACITACAO -->
	<strong class="question">4. ESTRUTURA PARA CAPACITAÇÃO</strong>
	<table class='generaltable items_questions'>
		<tr>
			<th class='header table-head0-areas'>ESTRUTURA PARA CAPACITAÇÃO</th>
			<th class='header table-head0-areas'>Nº TOTAL</th>
		</tr>
		<tr class='r0'>
			<td class='cell table-r0-areas' style='text-align: left;'>SALAS DE TREINAMENTO</td>
			<td class='cell table-r0-areas' align='center'><?php echo skills_get_totaltraining_by_structure($yearprevious, "%SALAS DE TREINAMENTO%") ?></td>
		</tr>
		<?php echo skills_make_rowstable_totalstructuretraining_by_branch($yearprevious, "%SALAS DE TREINAMENTO%"); ?>
		<tr class='r1'>
			<td class='cell table-r1-areas' style='text-align: left;'>LABORATÓRIO DE INFORMÁTICA</td>
			<td class='cell table-r1-areas' align='center'><?php echo skills_get_totaltraining_by_structure($yearprevious, '%LABORATORIO DE INFORMATICA%') ?></td>
		</tr>
		<?php echo skills_make_rowstable_totalstructuretraining_by_branch($yearprevious, "%LABORATORIO DE INFORMATICA%"); ?>
		<tr class='r0'>
			<td class='cell table-r0-areas' style='text-align: left;'>BIBLIOTECA</td>
			<td class='cell table-r0-areas' align='center'><?php echo skills_get_totaltraining_by_structure($yearprevious, '%BIBLIOTECA%') ?></td>
		</tr>
		<?php echo skills_make_rowstable_totalstructuretraining_by_branch($yearprevious, "%BIBLIOTECA%"); ?>
		<tr class='r1'>
			<td class='cell table-r1-areas' style='text-align: left;'>AUDITÓRIO</td>
			<td class='cell table-r1-areas' align='center'><?php echo skills_get_totaltraining_by_structure($yearprevious, '%AUDITORIO%') ?></td>
		</tr>
		<?php echo skills_make_rowstable_totalstructuretraining_by_branch($yearprevious, "%AUDITORIO%"); ?>
		<tr class='r0'>
			<td class='cell table-r0-areas' style='text-align: left;'>ESTUDIOS DE GRAVAÇÃO</td>
			<td class='cell table-r0-areas' align='center'><?php echo skills_get_totaltraining_by_structure($yearprevious, '%ESTUDIOS DE GRAVACAO%') ?></td>
		</tr>
		<?php echo skills_make_rowstable_totalstructuretraining_by_branch($yearprevious, "%ESTUDIOS DE GRAVACAO%"); ?>
		<tr class='r1'>
			<td class='cell table-r1-areas' style='text-align: left;'>PLATAFORMA DE APRENDIZAGEM</td>
			<td class='cell table-r1-areas' align='center'><?php echo skills_get_totaltraining_by_structure($yearprevious, '%PLATAFORMA DE APRENDIZAGEM%') ?></td>
		</tr>
	</table>
	<?php $objstruct = skills_get_plataform_field_structuretraining($yearprevious)?>
	
	<strong class="items_questions">PLATAFORMAS</strong>
	<table class='items_questions generaltable'>
		<tr>
			<th class='header table-head0-areas'>ÓRGÃO</th>
			<th class='header table-head0-capacitados'>TIPO</th>
			<th class='header table-head0-vagas'>VERSÃO</th>
		</tr>
		<?php $i = 0;?>
		<?php foreach ($objstruct as $struct):?>
			<?php $r = $i % 2;?>
			<?php $objst = json_decode($struct->structuretraining);?>
			<tr>
				<td class='table-r<?php echo $r?>-areas'><?php echo strtoupper($struct->organ);?></td>
				<td class='table-r<?php echo $r?>-capacitados'><?php echo $objst->plataform->type?></td>
				<td class='table-r<?php echo $r?>-vagas'><?php echo $objst->plataform->version?></td>
			</tr>
			<?php $i++?>
		<?php endforeach;?>
	</table>
	
	<?php $structtrainingsuficienteno = skills_get_total_field_dataform($yearprevious,"structtrainingsuficiente", "=", "no");?>
	<?php $structtrainingsuficienteyes = skills_get_total_field_dataform($yearprevious,"structtrainingsuficiente", "=", "yes");?>
	<strong class="items_questions">A ESTRUTURA DE CAPACITAÇÃO ATENDE ÀS NECESSIDADES DO ÓRGÃO?</strong>
	<table id='resources' class='items_questions generaltable boxaligncenter'>
		<tr>
			<th class='header table-head0-areas'>NÃO</th>
			<th class='header'>SIM</th>
		</tr>
		<tr class='r0'>
			<td class='cell' align='center'><?php echo $structtrainingsuficienteno ?></td>
			<td class='cell' align='center'><?php echo $structtrainingsuficienteyes ?></td>
		</tr>
	</table>
	
	<strong class="question">5. TABELA SIMPLES COM RECURSOS TOTAIS</strong>
	<div class='child'>
		<table class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>DOTAÇÃO ORÇAMENTÁRIA TOTAL DO JUDICIÁRIO</th>
				<th class='header table-head0-areas'>DESPESA TOTAL REALIZADA EM <?php echo $yearprevious?></th>
			</tr>
			<tr class='r0'>
				<td class='cell table-head1-areas' align='center'>R$ <?php echo skills_add_mask_money($totalbt) ?></td>
				<td class='cell table-head1-areas' align='center'>R$ <?php echo skills_add_mask_money($totalrv)?></td>
			</tr>
		</table>
	</div>
	
	<?php 
	$total_budgetaddmagistrates_no = skills_get_total_field_dataform($yearprevious, "budgetaddmagistrates", "=", "no");
	$total_budgetaddmagistrates_yes = skills_get_total_field_dataform($yearprevious, "budgetaddmagistrates", "=", "yes");
	?>
	<div class="question">ESSA DOTAÇÃO ORÇAMENTÁRIA INCLUI CAPACITAÇÃO DE MAGISTRADOS?</div>
	<div class='items_questions'>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head1-areas'>NÃO</th>
				<th class='header'>SIM</th>
			</tr>
			<tr class='r0'>
				<td class='cell' align='center'><?php echo $total_budgetaddmagistrates_no?></td>
				<td class='cell' align='center'><?php echo $total_budgetaddmagistrates_yes?></td>
			</tr>
		</table>
	</div>
	
	<!-- Total de despesas -->
	<div><strong class='question'>TOTAL DE DESPESAS REALIZADAS POR AÇÃO FORMATIVA:</strong></div>
	<strong class='items_questions'>GERAL</strong>
	<?php echo skills_make_table_runvalbyformaction($yearprevious)?>
	
	<strong class='items_questions'>JUSTIÇA SUPERIOR</strong>
	<?php echo skills_make_table_runvalbyformaction($yearprevious, $inSuperiores)?>
	
	<strong class='items_questions'>JUSTIÇA ELEITORAL</strong>
	<?php echo skills_make_table_runvalbyformaction($yearprevious, $inEleitoral)?>
	
	<strong class='items_questions'>JUSTIÇA TRABALHO</strong>
	<?php echo skills_make_table_runvalbyformaction($yearprevious, $inTrabalho)?>
	
	<strong class='items_questions'>JUSTIÇA FEDERAL</strong>
	<?php echo skills_make_table_runvalbyformaction($yearprevious, $inFederal)?>
	
	<strong class='items_questions'>JUSTIÇA ESTADUAL</strong>
	<?php echo skills_make_table_runvalbyformaction($yearprevious, $inEstadual)?>
	
	<strong class='items_questions'>JUSTIÇA MILITAR</strong>
	<?php echo skills_make_table_runvalbyformaction($yearprevious, $inMilitar)?>
	
	<!-- Outras despesas -->
	<strong class="question">OUTRAS DESPESAS:</strong>
	<strong class='items_questions'>GERAL</strong>
	<?php echo skills_make_table_othersvalrun($yearprevious)?>
	
	<strong class='items_questions'>JUSTIÇA SUPERIOR</strong>
	<?php echo skills_make_table_othersvalrun($yearprevious, $inSuperiores)?>
	
	<strong class='items_questions'>JUSTIÇA ELEITORAL</strong>
	<?php echo skills_make_table_othersvalrun($yearprevious, $inEleitoral)?>
	
	<strong class='items_questions'>JUSTIÇA TRABALHO</strong>
	<?php echo skills_make_table_othersvalrun($yearprevious, $inTrabalho)?>
	
	<strong class='items_questions'>JUSTIÇA FEDERAL</strong>
	<?php echo skills_make_table_othersvalrun($yearprevious, $inFederal)?>
	
	<strong class='items_questions'>JUSTIÇA ESTADUAL</strong>
	<?php echo skills_make_table_othersvalrun($yearprevious, $inEstadual)?>
	
	<strong class='items_questions'>JUSTIÇA MILITAR</strong>
	<?php echo skills_make_table_othersvalrun($yearprevious, $inMilitar)?>
	
	<?php 
	$total_programskills_no = skills_get_total_programskills($yearprevious, 'no');
	$total_programskills_yes = skills_get_total_programskills($yearprevious, 'yes');
	?>
	<div class="question">6. HÁ UM PROGRAMA INSTITUCIONALIZADO DE GESTÃO POR COMPETÊNCIAS?</div>
	<div class='items_questions'>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head1-areas'>NÃO</th>
				<th class='header'>SIM</th>
			</tr>
			<tr class='r0'>
				<td class='cell' align='center'><?php echo $total_programskills_no?></td>
				<td class='cell' align='center'><?php echo $total_programskills_yes?></td>
			</tr>
		</table>
		
		<strong>ESTÁGIO EM QUE ENCONTRA-SE NO PROGRAMA DA GESTÃO POR COMPETÊNCIAS:</strong>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>ESTÁGIOS</th>
				<th class='header'>Nº TOTAL</th>
			</tr>
			<tr class='r0'>
				<td class='cell table-r1-areas' style='text-align: left;'>MAPEAMENTO DAS COMPETÊNCIAS</td>
				<td class='cell' align='center'><?php echo skills_get_total_stageprogramskills($yearprevious, '%MAPEAMENTO DAS COMPETÊNCIAS%')?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-r0-areas' style='text-align: left;'>DIAGNÓSTICO DE COMPETÊNCIAS E ANÁLISE DO GAP </td>
				<td class='cell' align='center'><?php echo skills_get_total_stageprogramskills($yearprevious, '%DIAGNÓSTICO DE COMPETÊNCIAS E ANÁLISE DO GAP%')?></td>
			</tr>
			<tr class='r0'>
				<td class='cell table-r1-areas' style='text-align: left;'>CAPACITAÇÃO POR COMPETÊNCIAS</td>
				<td class='cell' align='center'><?php echo skills_get_total_stageprogramskills($yearprevious, '%CAPACITAÇÃO POR COMPETÊNCIAS%')?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-r0-areas' style='text-align: left;'>AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS</td>
				<td class='cell' align='center'><?php echo skills_get_total_stageprogramskills($yearprevious, '%AVALIAÇÃO DE DESEMPENHO POR COMPETÊNCIAS%')?></td>
			</tr>
		</table>
	</div>
	<?php 
	$total_evaluation_no = skills_get_total_evaluation($yearprevious, 'no');
	$total_evaluation_yes = skills_get_total_evaluation($yearprevious, 'yes');
	?>
	<div class="question">7. HÁ ALGUM TIPO DE AVALIAÇÃO ADOTADA NOS CURSOS?</div>
	<div class='items_questions'>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head1-areas'>NÃO</th>
				<th class='header'>SIM</th>
			</tr>
			<tr class='r0'>
				<td class='cell' align='center'><?php echo $total_evaluation_no?></td>
				<td class='cell' align='center'><?php echo $total_evaluation_yes?></td>
			</tr>
		</table>
	</div>
		
	<div class="question child">Tipo(s) de avaliação utilizado(s):</div>
	<div class='items_questions'>
			<table id='resources' class='generaltable boxaligncenter'>
				<tr>
					<th class='header table-head0-areas'>TIPO DE AVALIAÇÃO</th>
					<th class='header'>Nº TOTAL (<?php echo $total_evaluation_yes?>)</th>
				</tr>
				<tr class='r0'>
					<td class='cell table-r1-areas' style='text-align: left;'>REAÇÃO</td>
					<td class='cell' align='center'><?php echo skills_get_total_evaluation_by_typeevaluation($yearprevious, '%REAÇÃO%')?></td>
				</tr>
				<tr class='r1'>
					<td class='cell table-r0-areas' style='text-align: left;'>APRENDIZAGEM</td>
					<td class='cell' align='center'><?php echo skills_get_total_evaluation_by_typeevaluation($yearprevious, '%APRENDIZAGEM%')?></td>
				</tr>
				<tr class='r0'>
					<td class='cell table-r1-areas' style='text-align: left;'>APLICAÇÃO</td>
					<td class='cell' align='center'><?php echo skills_get_total_evaluation_by_typeevaluation($yearprevious, '%APLICAÇÃO%')?></td>
				</tr>
				<tr class='r1'>
					<td class='cell table-r0-areas' style='text-align: left;'>RESULTADO</td>
					<td class='cell' align='center'><?php echo skills_get_total_evaluation_by_typeevaluation($yearprevious, '%RESULTADO%')?></td>
				</tr>
				
				<tr class='r0'>
					<td class='cell table-r1-areas' style='text-align: left;'>OUTROS</td>
					<td class='cell' align='center'><?php echo skills_get_total_evaluation_others_typeevaluation($yearprevious)?></td>
				</tr>
			</table>
		</div>
	</div>
</fieldset>
</div>

<div id="mform1" class="mform">
	<fieldset id="id_general" class="clearfix collapsible">
	<legend class="ftoggler">
		<a class="fheader" href="#" role="button" aria-controls="id_general" aria-expanded="true">Dados Gerais <?php echo $currentyear?></a>
	</legend>

	<div class='fcontainer clearfix'>
	<?php 
	$total_plactiontngnextyear_no = skills_get_total_field_dataform($yearprevious, "planactiontngnextyear", "=", "no");
	$total_plactiontngnextyear_yes = skills_get_total_field_dataform($yearprevious, "planactiontngnextyear", "=", "yes");
	?>
	<div class="question">1. HÁ UM PLANEJAMENTO PARA AÇÕES DE CAPACITAÇÃO EM <?php echo $currentyear?>? </div>
	<div class='items_questions'>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head1-areas'>NÃO</th>
				<th class='header'>SIM</th>
			</tr>
			<tr class='r0'>
				<td class='cell' align='center'><?php echo $total_plactiontngnextyear_no?></td>
				<td class='cell' align='center'><?php echo $total_plactiontngnextyear_yes?></td>
			</tr>
		</table>
	</div>
	
	<div class="question">2. AÇÕES FORMATIVAS PREVISTAS</div>
	<div class='items_questions'>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>AÇÕES FORMATIVAS</th>
				<th class='header table-head0-vagas'>Nº TOTAL</th>
			</tr>
			<tr class='r0'>
				<td class='cell table-r0-areas' style='text-align: left;'>CURSOS PRESENCIAIS COM MENOS DE 360H</td>
				<td class='cell table-r0-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%CURSOS PRESENCIAIS COM MENOS DE 360H%")?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-r1-areas' style='text-align: left;'>CURSOS EAD COM MENOS DE 360H</td>
				<td class='cell table-r1-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%CURSOS EAD COM MENOS DE 360H%")?></td>
			</tr>
			<tr class='r0'>
				<td class='cell table-r0-areas' style='text-align: left;'>PÓS-GRADUAÇÃO</td>
				<td class='cell table-r0-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%PÓS-GRADUAÇÃO%")?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-r1-areas' style='text-align: left;'>PALESTRAS</td>
				<td class='cell table-r1-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%PALESTRAS%")?></td>
			</tr>
			<tr class='r0'>
				<td class='cell table-r0-areas' style='text-align: left;'>CONGRESSOS</td>
				<td class='cell table-r0-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%CONGRESSOS%")?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-r1-areas' style='text-align: left;'>ENCONTROS</td>
				<td class='cell table-r1-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%ENCONTROS%")?></td>
			</tr>
			<tr class='r0'>
				<td class='cell table-r0-areas' style='text-align: left;'>SEMINÁRIOS</td>
				<td class='cell table-r0-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%SEMINÁRIOS%")?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-r1-areas' style='text-align: left;'>FÓRUNS</td>
				<td class='cell table-r1-vagas' align='center'><?php echo skills_get_total_field_dataform($yearprevious, "formativeactionsyearcurrent", "LIKE", "%FÓRUNS%")?></td>
			</tr>
		</table>
	</div>
	
	<?php 
	// Justiça Superior
	$budgetSup = skills_get_total_budget_justice_branch($currentyear, $inSuperiores);
	
	// Justiça eleitoral
	$budgetEleit = skills_get_total_budget_justice_branch($currentyear, $inEleitoral);
	
	// Justiça do trabalho 
	$budgetTrab = skills_get_total_budget_justice_branch($currentyear, $inTrabalho);
	
	//  Justiça federal
	$budgetFed = skills_get_total_budget_justice_branch($currentyear, $inFederal);
	
	//  Justiça estadual
	$budgetEst = skills_get_total_budget_justice_branch($currentyear, $inEstadual);
	
	//  Justiça militar
	$budgetMil = skills_get_total_budget_justice_branch($currentyear, $inMilitar);
	?>
	<div class="question">3. PREVISÃO ORÇAMENTÁRIA</div>
	<div class='items_questions'>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head0-areas'>POR RAMO</th>
				<th class='header table-head0-areas'>TOTAL</th>
			</tr>
			<tr class='r1'>
				<td class='cell table-row-superior' style='text-align: left;'>JUSTIÇA SUPERIOR</td>
				<td class='cell table-row-superior' align='center'>R$ <?php echo skills_add_mask_money($budgetSup)?></td>
			</tr>
			<tr class='r0'>
				<td class='cell table-row-eleitoral' style='text-align: left;'>JUSTIÇA ELEITORAL</td>
				<td class='cell table-row-eleitoral' align='center'>R$ <?php echo skills_add_mask_money($budgetEleit)?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-row-trabalho' style='text-align: left;'>JUSTIÇA DO TRABALHO</td>
				<td class='cell table-row-trabalho' align='center'>R$ <?php echo skills_add_mask_money($budgetTrab)?></td>
			</tr>
			<tr class='r0'>
				<td class='cell table-row-federal' style='text-align: left;'>JUSTIÇA FEDERAL</td>
				<td class='cell table-row-federal' align='center'>R$ <?php echo skills_add_mask_money($budgetFed)?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-row-estadual' style='text-align: left;'>JUSTIÇA ESTADUAL</td>
				<td class='cell table-row-estadual' align='center'>R$ <?php echo skills_add_mask_money($budgetEst)?></td>
			</tr>
			<tr class='r1'>
				<td class='cell table-row-militar' style='text-align: left;'>JUSTIÇA MILITAR</td>
				<td class='cell table-row-militar' align='center'>R$ <?php echo skills_add_mask_money($budgetMil)?></td>
			</tr>
		</table>
	</div>
	
	<?php 
	$total_budgetaddmgtdnextyear_no = skills_get_total_field_dataform($yearprevious, "budgetaddmgtdnextyear", "=", "no");
	$total_budgetaddmgtdnextyear_yes = skills_get_total_field_dataform($yearprevious, "budgetaddmgtdnextyear", "=", "yes");
	?>
	<div class="question">ESSA DOTAÇÃO ORÇAMENTÁRIA INCLUI CAPACITAÇÃO DE MAGISTRADOS?</div>
	<div class='items_questions'>
		<table id='resources' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head1-areas'>NÃO</th>
				<th class='header'>SIM</th>
			</tr>
			<tr class='r0'>
				<td class='cell' align='center'><?php echo $total_budgetaddmgtdnextyear_no?></td>
				<td class='cell' align='center'><?php echo $total_budgetaddmgtdnextyear_yes?></td>
			</tr>
		</table>
	</div>
		
	</div>
	</fieldset> <!-- Fim dados ano corrente -->
	
	<fieldset id="id_comments" class="clearfix collapsible">
		<legend class="ftoggler">
			<a class="fheader" role="button" aria-controls="id_general" aria-expanded="true">Comentários e sugestões</a>
		</legend>
		<?php $dataforms = skills_get_comments_by_yearprevious($yearprevious);?>
		<table id='comments' class='generaltable boxaligncenter'>
			<tr>
				<th class='header table-head1-areas'>ÓRGÃO</th>
				<th class='header'>COMENTÁRIO</th>
				<th class='header'>SUGESTÕES</th>
			</tr>
			<?php foreach ($dataforms as $dataform) :?>
				<tr class='r0'>
					<td class='cell'><?php echo strtoupper($dataform->organ); ?></td>
					<td class='cell'>
						<p style="text-align: left;"><?php echo $dataform->comments; ?></p>
					</td>
					<td class='cell'>
						<p style="text-align: left;"><?php echo $dataform->sugestions; ?></p>
					</td>
				</tr>
			<?php endforeach;?>
		</table>
	</fieldset>
</div>
<?php 
// Finish the page.
echo $OUTPUT->footer();
