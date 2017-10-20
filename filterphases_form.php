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
 * The main skills configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_skills
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 *
 * @package    mod_skills
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_skills_filterphases_form extends moodleform {

    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;
        $select = array(''=> get_string('select', 'skills'));

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('filter'));

        // Adicionando campo justicebranche
		$mform->addElement('select', 'justicebranch', get_string('justicebranch','skills'), skills_generate_array_names_justice_branches(), array('class'=>'input-width'));
		$mform->addHelpButton('justicebranch', 'justicebranch', 'skills');
		$mform->addRule('justicebranch', null, 'required', null, 'client');
		
		// Adicionando campo stageprogramskills
		$mform->addElement('select', 'stageprogramskills', get_string('stageprogramskills','skills'), $select + skills_generate_array_stagesprogram(), array('class'=>'input-width'));
		$mform->addHelpButton('stageprogramskills', 'stageprogramskills', 'skills');
		$mform->addRule('stageprogramskills', null, 'required', null, 'client');
		
		// Adicionando campo phasestageprogramskills
		$mform->addElement('select', 'phasestageprogramskills', get_string('phasestageprogramskills','skills'), $select + skills_generate_array_phasesstagesprogram(), array('class'=>'input-width'));
		$mform->addHelpButton('phasestageprogramskills', 'phasestageprogramskills', 'skills');
		$mform->addRule('phasestageprogramskills', null, 'required', null, 'client');
		
		// Adicionando campo phasestageprogramskills
		$mform->addElement('select', 'situation', get_string('situation','skills'), $select + array('andamento'=> 'Em andamento', 'concluido'=>'Concluído'), array('class'=>'input-width'));
		$mform->addHelpButton('situation', 'situation', 'skills');
		$mform->addRule('situation', null, 'required', null, 'client');

        // Add standard buttons, common to all modules.
        $this->add_action_buttons(true, get_string('filter','skills'));
    }
}
