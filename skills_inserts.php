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
 * Library of interface functions and constants for module update table user
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the cicleinscription specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage Atualização da tabela de usuários
 * @copyright  2013 CEAJUD | CNJ | Tecninys
 * @author	   Léo Santos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Replace skills with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

# Imprimindo cabeçalho da pagina
echo $OUTPUT->header();
// Verifica se é administrador
if ((!is_siteadmin($USER->id))) {
	echo $OUTPUT->notification('Você não tem permissão para acessar essa área do site.');
	
	// Finalizando pagina
	echo $OUTPUT->footer();
	die();
}

// Verifica se o usuário está logado
require_login();

$PAGE->set_title('Skills - Preparação de Ambiente');
echo $OUTPUT->heading('Preparação de Ambiente /mod (Skills) - Inserts');


// Recebendo post
if(!empty($_POST['inserts'])){
	
	global $CFG;
	$con = mysql_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass) or print(mysql_error());
	mysql_select_db($CFG->dbname, $con) or print(mysql_error());
	
	mysql_query("SET NAMES 'utf8'");
	mysql_query('SET character_set_connection=utf8');
	mysql_query('SET character_set_client=utf8');
	mysql_query('SET character_set_results=utf8');
	
	// Limpando a tabelas
	$sql = "TRUNCATE TABLE mdl_skills_areas";
	$rs = mysql_query($sql);
	
	if ($rs){
		echo "Tabela {skills_areas} limpa com sucesso! <br /> ";
	}
	
	$sql = "TRUNCATE TABLE mdl_skills_modality";
	$rs = mysql_query($sql);
	
	if ($rs){
		echo "Tabela {mdl_skills_modality} limpa com sucesso! <br /> ";
	}
	
	$sql = "TRUNCATE TABLE mdl_skills_training";
	$rs = mysql_query($sql);
	
	if ($rs){
		echo "Tabela {skills_training} limpa com sucesso! <br /> ";
	}
	
	// Inserindo novos dados
	$sql = "INSERT INTO `mdl_skills_areas` (`id`, `name`, `description`) VALUES 
			(1, 'TECNOLOGIA DA INFORMAÇÃO', '<strong>Assuntos envolvidos:</strong>  hardware, software, sistemas de comunicação, sistemas de telecomunicações, gestão de informações e de dados, segurança da informação e outros.'),
			(2, 'JUDICIÁRIA', '<strong>Assuntos envolvidos:</strong> normas jurídicas, jurisprudência, elaboração de textos jurídicos, procedimentos e rotinas judiciais, mediação, custas judiciais e outros.'),
			(3, 'ADMINISTRATIVA/GESTÃO', '<strong>Assunto envolvidos: </strong> contratação e convênios, licitação, gestão de pessoas, gestão por competências, gestão por projetos, gestão da qualidade, educação corporativa, comunicação, gestão estratégica, gestão documental, secretariado, finanças públicas, auditoria, administração de recursos materiais, organização de eventos e outros assuntos relacionados ao tema.'),
			(4, 'LÍNGUAS', '<strong>Assunto envolvidos: </strong> cursos de línguas estrangeiras e de português.'),
			(5, 'RESPONSABILIDADE SOCIAL/SAÚDE E QUALIDADE DE VIDA', '<strong>Assunto envolvidos: </strong> gestão socioambiental, sustentabilidade, comunidade e sociedade, estímulo à adoção de hábitos saudáveis (alimentação, atividade física, etc), prevenção de doenças ocupacionais e acidentes de trabalho, estilo de vida e produtividade e demais assuntos correlatos.'),
			(6, 'EDUCAÇÃO', '<strong>Assunto envolvidos: </strong> educação corporativa, formação de docentes, formação de conteudistas, pedagogia, epistemologia, metodologias de ensino-aprendizagem.')";
	
	$rs = mysql_query($sql);
	
	if($rs){
		echo "Tabela {skills_areas} Preenchida com sucesso! <br /> ";
	}else{
		print(mysql_error());
	}
	$sql = "INSERT INTO `mdl_skills_training` (`name`) VALUES 
			('PÓS-GRADUAÇÕES'),
			('PALESTRAS'),
			('CONGRESSOS'),
			('ENCONTROS'),
			('SEMINÁRIOS'),
			('FÓRUNS'),
			('WORKSHOPS'),
			('GRADUAÇÕES'),
			('OUTRO(S):')";
	
	$rs = mysql_query($sql);
	
	if($rs){
		echo "Tabela {skills_training} Preenchida com sucesso! <br /> ";
	}
	
	$sql = "INSERT INTO `mdl_skills_modality` (`name`, `description`) VALUES 
			('EAD', 'Educação a Distancia'),
			('PRESENCIAL', 'Presencial'),
			('SEMIPRESENCIAL', 'Semipresencial')";
	
	$rs = mysql_query($sql);
	
	if($rs){
		echo "Tabela {skills_modality} Preenchida com sucesso! <br /> ";
	}
	
	mysql_free_result($rs);
	mysql_close($con);
	
	if(!$rs){
		error(get_string('error'));
	}
}else{
	
	echo "<form action='#' method='post' >";
		echo "<input type='hidden' value='sim' name='inserts'>";
		echo "<input type='submit' value='Executar Inserts nas tabelas' style='float:left;' />";
	echo "</form>";
}

// Finalizando pagina
echo $OUTPUT->footer();