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
 * Strings for component 'auth_unilogin', language 'en'.
 *
 * @package    auth_unilogin
 * @copyright  2014 IT Kartellet
 * @license    MIT
 */

// Settings overview page
$string['auth_unilogindescription'] = 'A plugin to login users using the Danish UNI Login service.';
$string['pluginname'] = 'UNI Login';

// Plugin settings page
$string['application_info_header'] = 'Application info';
$string['application_info_description'] = 'Basic information about your application. You get this infromation by registering with UNI C';

$string['application_id'] = 'Application ID';
$string['application_id_description'] = 'Each application has a unique used to identify to UNI C';

$string['application_secret'] = 'Application secret';
$string['application_secret_description'] = 'The secret that is used, together with the application ID to identify to UNI C';

$string['application_settings_header'] = 'Login settings';
$string['application_settings_description'] = 'Settings for how the user should login';

$string['login_type'] = 'Login type';
$string['login_type_description'] = 'How the login procedure should work for users. Single Sign On (SSO) means, that if the user is 
already logged in to another service that uses UNI C the user will not be required to enter their password, while Single Login (SLI) 
means that users will always be required to enter their password, even though they are signed into other UNI C services that use SSO.';
$string['login_type_sso'] = 'Single Sign On';
$string['login_type_sli'] = 'Single Login';

$string['validation_behaviour'] = 'Validation behaviour';
$string['validation_behaviour_description'] = 'How should tickets returned from UNI C be validated? Database saves tickets in a table in the DB to ensure that each ticket is only used once, while time ensures that the ticket has been issued within the last x seconds (see option below)';
$string['validation_behaviour_db'] = 'Database';
$string['validation_behaviour_time'] = 'Time';

$string['validatetime'] = 'Validation time limit';
$string['validatetime_description'] = 'The maximum age in seconds for a ticket to be considered valid.';
