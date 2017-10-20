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
echo $OUTPUT->heading('Preparação de Ambiente /mod (Skills) - Alters');


// Recebendo post
if(!empty($_POST['alters'])){
	
	global $CFG;
	$con = mysql_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass) or print(mysql_error());
	mysql_select_db($CFG->dbname, $con) or print(mysql_error());
	
	
	// Alterando colunas das tabelas
	// phasestageprogramskills
	/*$sql = "ALTER TABLE `mdl_skills_dataform` ADD COLUMN `phasestageprogramskills` VARCHAR(1024) NULL DEFAULT NULL AFTER `stageprogramskills`";
	$rs = mysql_query($sql);
	
	if ($rs){
		echo "Coluna {phasestageprogramskills} adicionada com sucesso! <br /> ";
	}
	
	// trainingactionsociety
	$sql = "ALTER TABLE `mdl_skills_dataform` CHANGE COLUMN `trainingactionsociety` `trainingactionsociety` VARCHAR(512) NULL DEFAULT NULL COMMENT 'Armazena se ha acoes de capacitacao abertas a sociedade. Se sim, recebe JSON com nturmas, ninscritos e ncapacitados.'";
	$rs = mysql_query($sql);
	
	if ($rs){
		echo "Coluna {trainingactionsociety} atualizado com sucesso! <br /> ";
	}
	
	// sugestions
	$sql = "ALTER TABLE `mdl_skills_dataform` ADD COLUMN `sugestions` LONGTEXT NULL AFTER `comments`";
	$rs = mysql_query($sql);
	
	if ($rs){
		echo "Coluna {trainingactionsociety} atualizado com sucesso! <br /> ";
	}*/
	// Fechando conexao
	mysql_free_result($rs);
	mysql_close($con);
	
	if(!$rs){
		error(get_string('error'));
	}
}else{
	
	echo "<form action='#' method='post' >";
		echo "<input type='hidden' value='sim' name='alters'>";
		echo "<input type='submit' value='Executar Alters nas tabelas' style='float:left;' />";
	echo "</form>";
}

// Finalizando pagina
echo $OUTPUT->footer();