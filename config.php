<?php

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
    array('sso' => get_string('login_type_sso', 'auth_unilogin'), 'sli' => get_string('login_type_sli', 'auth_unilogin'))
    )
);

$settings->add(new admin_setting_configselect(
    'auth_unilogin/validation_behaviour',
    get_string('validation_behaviour', 'auth_unilogin'),
    get_string('validation_behaviour_description', 'auth_unilogin'),
    'time',
    array('time' => get_string('validation_behaviour_time', 'auth_unilogin'), 'db' => get_string('validation_behaviour_db', 'auth_unilogin'))
    )
);

$settings->add(new admin_setting_configtext(
    'auth_unilogin/validatetime',
    get_string('validatetime', 'auth_unilogin'),
    get_string('validatetime_description', 'auth_unilogin'),
    60
    )
);

echo $settings->output_html();