<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Themes
    |--------------------------------------------------------------------------
    |
    */
    'jwt' => [
	    'secret' => env('JWT_SECRET', ''),
	 ],
    'theme_login' => env('THEME_LOGIN', 'theme_a'),
    'theme_login_color' => env('THEME_LOGIN_COLOR', 'main'),
    'theme_front' => env('THEME_FRONT', ''),
    'theme_front_color' => env('THEME_FRONT_COLOR', 'main'),
    'theme_back' => env('THEME_BACK', 'theme_metronic'),

    'web_view' => [
        'theme_login' => env('WEBVIEW_THEME_LOGIN', env('THEME_LOGIN')),
        'theme_front' => env('WEBVIEW_THEME_FRONT', env('THEME_FRONT')),
        'app_project' => env('WEBVIEW_APP_PROJECT', env('APP_PROJECT')),
        'app_folder' => env('WEBVIEW_APP_FOLDER', env('APP_FOLDER')),
    ],

    'sso' => [
        'login_3rd' => env('SSO_AUTH_3RD', false),
        'auth_3rd_url' => env('SSO_AUTH_3RD_URL', ""),
    ],

    'app' => [
        'url' => env('APP_URL', ''),
     	'name' => env('APP_NAME', 'Bookdose'),
        'code' => env('APP_CODE', 'bd_auth'),
        'project' => env('APP_PROJECT', 'tomahawk'),
     	'folder' => env('APP_FOLDER', 'bookdose'),
     	'logo' => env('APP_LOGO', ''),
     	'logo_white' => env('APP_LOGO_WHITE', ''),
     	'logo_login_height' => env('APP_LOGO_LOGIN_HEIGHT', '70px'),
     	'background_login' => env('APP_BACKGROUND_LOGIN', ''),
     	'fav_ico' => env('APP_FAV_ICON', 'favicon.ico'),
     	'custom_css' => env('APP_CUSTOM_CSS', 'css/base.css'),
     	'url_forgot_password' => env('APP_URL_FORGOT_PASSWORD', ''),
     	'main_product_redirect' => env('APP_MAIN_PRODUCT_REDIRECT', ''),
     	'belib_url' => env('APP_BELIB_URL', ''),
     	'learnext_url' => env('APP_LEARNEXT_URL', ''),
     	'km_url' => env('APP_KM_URL', ''),
     	'cms_url' => env('APP_CMS_URL', ''),
     	'reCaptcha' => env('RECAPTCHA', false),
     	'prefix_route_org' => env('PREFIX_ROUTE_ORG', false),
        'store_prefix' => env('APP_PROJECT', 'bdpath').'/'.env('APP_ENV', 'develop'),
        // IOS Mobile App
        'ios_id' => env('APP_IOS_ID', ''),
        'ios_path' => env('APP_IOS_PATH', ''),
        // Android Mobile App
        'android_id' => env('APP_ANDROID_ID', ''),
        'android_path' => env('APP_ANDROID_PATH', ''),
        // Forgot password
        'forgot' => [
            'password' => env('APP_FORGOT_PASSWORD', false),
            'send_mail' => env('APP_FORGOT_SENDMAIL', false),
        ],
 	],

	 'default_image' => [
        'banner_login' => env('DEFAULT_IMAGE_BANNER_LOGIN', 'images/login_bg.jpg'),
        'banner_register' => env('DEFAULT_IMAGE_BANNER_REGISTER', 'images/register_bg.png'),
        'avatar' => env('DEFAULT_IMAGE_AVATAR', 'images/default_avatar.png'),
        'reward_item' => env('DEFAULT_IMAGE_REWARD_ITEM', 'images/default_reward_item.png'),
	 ],

	 'ldap' => [
        'locate_users_by' => env('LDAP_LOCATE_USERS_BY', 'userprincipalname'),
        'login_username_placeholder' => env('LDAP_LOGIN_USERNAME_PLACEHOLDER', 'Username'),
        'host' => env('LDAP_HOSTS', ''),
        'port' => env('LDAP_PORT', ''),
        'base_dn' => env('LDAP_BASE_DN', ''),
        'domain' => env('LDAP_DOMAIN', ''),
        'default_password' => env('LDAP_DEFAULT_PASSWORD', 'libr@ryBD'),
	 ],

    'default' => [
        'user_org' => env('DEFAULT_USER_ORG_ID','1'),
        'org_slug' => env('DEFAULT_USER_ORG_SLUG','bookdose'),
        'store_org' => env('DEFAULT_STORE_USER_ORG_ID', '2'),
        'store_slug' => env('DEFAULT_STORE_USER_ORG_SLUG', 'bookdose-store'),
        'home_slug' => env('DEFAULT_HOME_USER_ORG_SLUG','bookdose'),
        'year_start' => env('YEAR_START', date('Y')),
        'member' => [
            'format_setting' => env('MEMBER_FORMAT_SETTING', false),
            'word_start' => env('MEMBER_WORD_START', null),
            'word_end' => env('MEMBER_WORD_END', null),
            'pad_length' => env('MEMBER_PAD_LENGTH', 6),
            'pad_string' => env('MEMBER_PAD_STRING', '0'),
            'pad_type' => env('MEMBER_PAD_TYPE', 'left'),
            'max_running' => env('MEMBER_MAX_RUNNING_ROUND', 10),
        ]
    ],

    'mail' => [
        'from_address' => env('MAIL_FROM_ADDRESS','noreply@belib.app'),
        'from_name' => env('MAIL_FROM_NAME', 'Belib App Team.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Login & Registration
    |--------------------------------------------------------------------------
    |
    */
    'regis' => [
        'online' => env('USER_REGIS_ONLINE', false),
        'verify' => env('USER_REGIS_VERIFY', false),
        'verify_by_admin' => env('USER_REGIS_VERIFY_BY_ADMIN', false),
        'email_contact' => env('USER_REGIS_MAIL_CONTACT', ''),
        'consent_enable' => env('USER_REGIS_CONSENT_ENABLE', false),
        'km_role' => env('USER_REGIS_KM_ROLE', true),
    ],

    'login_adldap' => env('LOGIN_ADLDAP', false),
    'login_social' => env('LOGIN_SOCIAL', true),
    'login_social_default_role' => env('LOGIN_SOCIAL_DEFAULT_ROLE', 'Member'),
    'login_auth_using_field' => env('LOGIN_AUTH_USING_FIELD', 'email'),
    'login_auth_field_placeholder' => env('LOGIN_AUTH_FIELD_PLACEHOLDER', 'Email address'),
    'login_multiple' => env('LOGIN_MULTIPLE', '1'),

    'facebook_client_id' => env('FACEBOOK_CLIENT_ID', ''),
    'facebook_client_secret' => env('FACEBOOK_CLIENT_SECRET', ''),
    'facebook_redirect' => env('FACEBOOK_REDIRECT', ''),

    'google_client_id' => env('GOOGLE_CLIENT_ID', ''),
    'google_client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
    'google_redirect' => env('GOOGLE_REDIRECT', ''),

    'tkpark_url' => env('TKPARK_URL', ''),
    'tkpark_client_id' => env('TKPARK_CLIENT_ID', ''),
    'tkpark_client_secret' => env('TKPARK_CLIENT_SECRET', ''),
    'tkpark_redirect' => env('TKPARK_REDIRECT', ''),
    'tkpark_redirect_logout' => env('TKPARK_REDIRECT_LOGOUT', ''),

    //oic oauth2 api
    'login_oic_oauth2' => env('LOGIN_OIC_OAUTH2', false),
    'oic_oauth2_token_url' => env('OIC_OAUTH2_TOKEN_URL', ''),
    'oic_oauth2_login_url' => env('OIC_OAUTH2_LOGIN_URL', ''),
    'oic_oauth2_client_id' => env('OIC_OAUTH2_CLIENT_ID', ''),
    'oic_oauth2_client_secret' => env('OIC_OAUTH2_CLIENT_SECRET', ''),
    'oic_oauth2_field_login' => env('OIC_OAUTH2_FIELD_LOGIN', 'email'),
    'oic_oauth2_default_pwd' => env('OIC_OAUTH2_DEFAULT_PWD', 'email'),
    'oic_oauth2_prefix_name' => env('OIC_OAUTH2_PREFIX_NAME', 'นาย,นางสาว,นาง,ดร.,ว่าที่ร้อยตรี'),

    //etech login soap
    'login_etech' => env('LOGIN_ETECH', false),
    'login_etech_url' => env('LOGIN_ETECH_URL', 'http://app-service.e-tech.ac.th/wservice/auth.asmx'),

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    */
    'notification' => [
        'pushmsg_url' => env('CONST_NOTIFICATION_PUSHMSG_URL', 'https://pushmsg.belib.app/pushmsg_v2.php'),
        'line_api' => env('CONST_NOTIFICATION_LINE_API', 'https://notify-api.line.me/api/notify'),
        'line_token' => env('CONST_NOTIFICATION_LINE_TOKEN', 'QcdGmGXZBBCFINe0HcsOwhf267cxukQNNo0X0QNQOfM'),
        'has_app' => env('CONST_NOTIFICATION_HAS_APP', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Room Setting
    |--------------------------------------------------------------------------
    |
    */
    'room' => [
        'in_advance_day' => env('ROOM_IN_ADVANCE_DAY', '7'),
        'per_day' => env('ROOM_PER_DAY', '2'),
        'max_hour' => env('ROOM_MAX_HOUR', '2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Article Module
    |--------------------------------------------------------------------------
    |
    */
    'article' => [
        'category_level' => env('ARTICLE_CATEGORY_LEVEL', 1),
    ],
];
