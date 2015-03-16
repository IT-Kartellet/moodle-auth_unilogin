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
 * This is a one-line short description of the file.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    auth_unilogin
 * @copyright  2015 Jan Aagaard Meier (IT-Kartellet)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

$settings = new admin_settingpage('auth_unilogin', 'UNI Login');

$settings->add(new admin_setting_heading(
    'auth_unilogin/h1',
    get_string('application_info_header', 'auth_unilogin'),
    get_string('application_info_description', 'auth_unilogin')
    )
);

$settings->add(new admin_setting_configtext(
    'auth_unilogin/id',
    get_string('application_id', 'auth_unilogin'),
    get_string('application_id_description', 'auth_unilogin'),
    null)
);

$settings->add(new admin_setting_configtext(
    'auth_unilogin/secret',
    get_string('application_secret', 'auth_unilogin'),
    get_string('application_secret_description', 'auth_unilogin'),
    null)
);

$settings->add(new admin_setting_configtext(
    'auth_unilogin/wsid',
    get_string('ws_id', 'auth_unilogin'),
    get_string('ws_id_description', 'auth_unilogin'),
    null)
);

$settings->add(new admin_setting_configtext(
    'auth_unilogin/wssecret',
    get_string('ws_secret', 'auth_unilogin'),
    get_string('ws_secret_description', 'auth_unilogin'),
    null)
);

$settings->add(new admin_setting_heading(
    'auth_unilogin/h2',
    get_string('application_settings_header', 'auth_unilogin'),
    get_string('application_settings_description', 'auth_unilogin')
    )
);

$settings->add(new admin_setting_configselect(
    'auth_unilogin/login_type',
    get_string('login_type', 'auth_unilogin'),
    get_string('login_type_description', 'auth_unilogin'),
    'sso',
    array(
        'sso' => get_string('login_type_sso', 'auth_unilogin'),
        'sli' => get_string('login_type_sli', 'auth_unilogin'))
    )
);

$settings->add(new admin_setting_configselect(
    'auth_unilogin/validation_behaviour',
    get_string('validation_behaviour', 'auth_unilogin'),
    get_string('validation_behaviour_description', 'auth_unilogin'),
    'time',
    array(
        'time' => get_string('validation_behaviour_time', 'auth_unilogin'),
        'db' => get_string('validation_behaviour_db', 'auth_unilogin'))
    )
);

$settings->add(new admin_setting_configtext(
    'auth_unilogin/validatetime',
    get_string('validatetime', 'auth_unilogin'),
    get_string('validatetime_description', 'auth_unilogin'),
    60
    )
);

$settings->add(new admin_setting_configselect(
    'auth_unilogin/login_behaviour',
    get_string('login_behaviour', 'auth_unilogin'),
    get_string('login_behaviour_description', 'auth_unilogin', $CFG->wwwroot),
    'link',
    array(
        'link' => get_string('login_behaviour_link', 'auth_unilogin'),
        'redirect' => get_string('login_behaviour_redirect', 'auth_unilogin'))
    )
);

$settings->add(new admin_setting_configtextarea(
    'auth_unilogin/login_behaviour_link_text',
    get_string('login_behaviour_link_text', 'auth_unilogin'),
    get_string('login_behaviour_link_text_description', 'auth_unilogin'),
    get_string('login_behaviour_link_text_default', 'auth_unilogin')
    )
);

$settings->add(new admin_setting_configtext(
    'auth_unilogin/login_behaviour_link_selector',
    get_string('login_behaviour_link_selector', 'auth_unilogin'),
    get_string('login_behaviour_link_selector_description', 'auth_unilogin'),
    get_string('login_behaviour_link_selector_default', 'auth_unilogin')
    )
);

echo $settings->output_html();



// Taken from auth/admin_config, but cut down to only show lock and updatelock selectors
global $OUTPUT;
echo $OUTPUT->heading(get_string('auth_fieldlocks', 'auth'));
echo '<div class="box generalbox formsettingheading"><p>' . get_string('fieldlocks', 'auth_unilogin') . '</p>
</div>';

$lockoptions = array ('unlocked'        => get_string('unlocked', 'auth'),
                      'unlockedifempty' => get_string('unlockedifempty', 'auth'),
                      'locked'          => get_string('locked', 'auth'));
$updatelocaloptions = array('oncreate'  => get_string('update_oncreate', 'auth'),
                            'onlogin'   => get_string('update_onlogin', 'auth'));

$pluginconfig = get_config("auth_unilogin");

foreach (array('firstname', 'lastname', 'email') as $field) {
    // Define some vars we'll work with.
    if (!isset($pluginconfig->{"field_updatelocal_$field"})) {
        $pluginconfig->{"field_updatelocal_$field"} = '';
    }
    if (!isset($pluginconfig->{"field_lock_$field"})) {
        $pluginconfig->{"field_lock_$field"} = '';
    }

    $fieldname = get_string($field);

    $varname = 'field_map_' . $field;

    echo '<div class="form-item"><div class="form-label"><label>' . $fieldname . '</label></div>';

    echo '<div class="form-setting">';
    echo '<label for="menulockconfig_field_updatelocal_'.$field.'">'.get_string('auth_updatelocal', 'auth') . '</label>';
    echo html_writer::select($updatelocaloptions, "lockconfig_field_updatelocal_{$field}", $pluginconfig->{"field_updatelocal_$field"}, false);

    echo '<label for="menulockconfig_field_lock_'.$field.'">'.get_string('auth_fieldlock', 'auth') . '</label>';
    echo html_writer::select($lockoptions, "lockconfig_field_lock_{$field}", $pluginconfig->{"field_lock_$field"}, false);
    echo '</div></div>';
    echo '</td>';
    echo '</tr>';
}