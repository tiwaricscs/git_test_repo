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
 * myurlactivity configuration form
 *
 * @author     <ballisticlearning.com>
 * @package    mod_myurlactivity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/myurlactivity/locallib.php');

class mod_myurlactivity_mod_form extends moodleform_mod {
    /*
     * setting up form stucture and fields for module..
    */
    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;
        $config = get_config('myurlactivity');
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name', 'myurlactivity'), array('size' => '48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addElement('url', 'externalurl', get_string('externalurl', 'myurlactivity'
                . ''), array('size' => '60'), array('usefilepicker' => true));
        $mform->setType('externalurl', PARAM_RAW_TRIMMED);
        $mform->addRule('externalurl', null, 'required', null, 'client');
        $options = myurlactivity_get_variable_options($config);
        if (empty($this->current->parameters)) {
            $userid = $courseid = '';
        } else {
            $value = unserialize($this->current->parameters);
            $userid = !empty($value['userid']) ? true : false ;
            $courseid = !empty($value['courseid']) ? true : false ;
        }
        //---fields for userid and courseid
        $group = array(
            $mform->createElement('advcheckbox', 'courseid', '', get_string('courseid', 'myurlactivity'), array('group' => 1),
                    array(0, 'courseid')),
            $mform->createElement('advcheckbox', 'userid', '', get_string('userid', 'myurlactivity'), array('group' => 1),
                    array(0, 'userid'))
        );
        $mform->setDefault('userid', $userid);
        $mform->setDefault('courseid', $courseid);
        $mform->addGroup($group, 'pargoup', get_string('parameterinfo', 'myurlactivity'), ' ', false);
        $this->standard_intro_elements();
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);
        // ----------------------------------Display Option---------------------.
        if ($this->current->instance) {
            $options = activitylib_get_displayoptions(explode(',', $config->displayoptions), $this->current->display);
        } else {
            $options = activitylib_get_displayoptions(explode(',', $config->displayoptions));
        }
        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'display', get_string('displayselect', 'myurlactivity'), $options);
            $mform->setDefault('display', $config->display);
            $mform->addHelpButton('display', 'displayselect', 'myurlactivity');
        }
        if (array_key_exists(RESOURCELIB_DISPLAY_POPUP, $options)) {
            $mform->addElement('text', 'popupwidth', get_string('popupwidth', 'url'), array('size'=>3));
            if (count($options) > 1) {
                $mform->hideIf('popupwidth', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupwidth', PARAM_INT);
            $mform->setDefault('popupwidth', $config->popupwidth);

            $mform->addElement('text', 'popupheight', get_string('popupheight', 'url'), array('size'=>3));
            if (count($options) > 1) {
                $mform->hideIf('popupheight', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupheight', PARAM_INT);
            $mform->setDefault('popupheight', $config->popupheight);
        }
        if (array_key_exists(ACTIVITYLIB_DISPLAY_EMBED, $options) or
                array_key_exists(ACTIVITYLIB_DISPLAY_FRAME, $options)) {
            $mform->addElement('checkbox', 'printintro', get_string('printintro', 'myurlactivity'));
            $mform->disabledIf('printintro', 'display', 'eq', ACTIVITYLIB_DISPLAY_EMBED);
            $mform->disabledIf('printintro', 'display', 'eq', ACTIVITYLIB_DISPLAY_FRAME);
             $mform->setDefault('printintro', $config->printintro);
        }

        //------------------------------------------------
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    //validating given url in form..
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!empty($data['externalurl'])) {
            $url = $data['externalurl'];
            if (preg_match('|^[a-z]+://|i', $url) or preg_match('|^https?:|i', $url) or preg_match('|^ftp:|i', $url)) {
                // Normal URL.
                if (!myurlactivity_appears_valid_url($url)) {
                    $errors['externalurl'] = get_string('invalidurl', 'myurlactivity');
                }
            } else {
                // Invalid URI, we try to fix it by adding 'http://' prefix.
                // Relative links are NOT allowed because we display the link on different pages!.
                if (!myurlactivity_appears_valid_url('http://' . $url)) {
                    $errors['externalurl'] = get_string('invalidurl', 'myurlactivity');
                }
            }
        }
        return $errors;
    }
}
