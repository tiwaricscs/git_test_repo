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
 * . 
 *
 * @package    mod_myurllactivity
 * @author     <Ballisticlearning.com> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/myurllactivity/locallib.php');

class mod_myurllactivity_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;
        $config = get_config('mod_myurllactivity');
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addElement('url', 'externalurl', get_string('externalurl', 'myurllactivity'), array('size' => '60'), array('usefilepicker' => true));
        $mform->setType('externalurl', PARAM_RAW_TRIMMED);
        $mform->addRule('externalurl', null, 'required', null, 'client');
        $mform->addElement('checkbox', 'userid', get_string('userid', 'myurllactivity'), 'user id', null, array(0, 1));
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('checkbox', 'courseid', get_string('courseid', 'myurllactivity'), 'course id', null, array(0, 1));
        $mform->setType('courseid', PARAM_INT);
        $this->standard_intro_elements();
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);
        $mform->addElement('header', 'optionssection', get_string('appearance'));
        if ($this->current->instance) {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions), $this->current->display);
        } else {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions));
        }
        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'display', get_string('displayselect', 'myurllactivity'), $options);
            $mform->setDefault('display', $config->display);
            $mform->addHelpButton('display', 'displayselect', 'myurllactivity');
        }
        if (array_key_exists(RESOURCELIB_DISPLAY_FRAME, $options)) {
            $mform->addElement('text', 'iframewidth', get_string('iframewidth', 'myurllactivity'), array('size' => 3));
            if (count($options) > 1) {
                $mform->hideIf('iframewidth', 'display', 'noteq', RESOURCELIB_DISPLAY_FRAME);
            }
            $mform->setType('iframewidth', PARAM_INT);
            $mform->setDefault('iframewidth', $config->iframewidth);

            $mform->addElement('text', 'iframeheight', get_string('iframeheight', 'myurllactivity'), array('size' => 3));
            if (count($options) > 1) {
                $mform->hideIf('iframeheight', 'display', 'noteq', RESOURCELIB_DISPLAY_FRAME);
            }
            $mform->setType('iframeheight', PARAM_INT);
            $mform->setDefault('iframeheight', $config->iframeheight);
        }
        if (array_key_exists(RESOURCELIB_DISPLAY_AUTO, $options) or
                array_key_exists(RESOURCELIB_DISPLAY_EMBED, $options) or
                array_key_exists(RESOURCELIB_DISPLAY_FRAME, $options)) {
            $mform->addElement('checkbox', 'printintro', get_string('printintro', 'myurllactivity'));

            $mform->setDefault('printintro', $config->printintro);
        }
        $mform->addElement('header', 'parameterssection', get_string('parametersheader', 'myurllactivity'));
        $mform->addElement('static', 'parametersinfo', '', get_string('parametersheader_help', 'myurllactivity'));

        if (empty($this->current->parameters)) {
            $parcount = 5;
        } else {
            $parcount = 5 + count(unserialize($this->current->parameters));
            $parcount = ($parcount > 100) ? 100 : $parcount;
        }
        $options = myurllactivity_get_variable_options($config);

        for ($i = 0; $i < $parcount; $i++) {
            $parameter = "parameter_$i";
            $variable = "variable_$i";
            $pargroup = "pargoup_$i";
            $group = array(
                $mform->createElement('text', $parameter, '', array('size' => '12')),
                $mform->createElement('selectgroups', $variable, '', $options),
            );
            $mform->addGroup($group, $pargroup, get_string('parameterinfo', 'myurllactivity'), ' ', false);
            $mform->setType($parameter, PARAM_RAW);
        }
        $this->standard_coursemodule_elements();
         $this->add_action_buttons();
    }
    function data_preprocessing(&$default_values) {
        if (!empty($default_values['displayoptions'])) {
            $displayoptions = unserialize($default_values['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $default_values['printintro'] = $displayoptions['printintro'];
            }
            if (!empty($displayoptions['iframewidth'])) {
                $default_values['iframewidth'] = $displayoptions['iframewidth'];
            }
            if (!empty($displayoptions['iframeheight'])) {
                $default_values['iframeheight'] = $displayoptions['iframeheight'];
            }
        }
        if (!empty($default_values['parameters'])) {
            $parameters = unserialize($default_values['parameters']);
            $i = 0;
            foreach ($parameters as $parameter => $variable) {
                $default_values['parameter_' . $i] = $parameter;
                $default_values['variable_' . $i] = $variable;
                $i++;
            }
        }
    }
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!empty($data['externalurl'])) {
            $url = $data['externalurl'];
            if (preg_match('|^/|', $url)) {
                
            } else if (preg_match('|^[a-z]+://|i', $url) or preg_match('|^https?:|i', $url) or preg_match('|^ftp:|i', $url)) {
                if (!myurllactivity_appears_valid_url($url)) {
                    $errors['externalurl'] = get_string('invalidurl', 'myurllactivity');
                }
            } else if (preg_match('|^[a-z]+:|i', $url)) {
                
            } else {
                if (!myurllactivity_appears_valid_url('http://' . $url)) {
                    $errors['externalurl'] = get_string('invalidurl', 'myurllactivity');
                }
            }
        }
        return $errors;
    }
}
