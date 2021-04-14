<?php
/**
 * Plugin Name: JivoChat
 * Author: JivoChat
 * Author URI: www.jivochat.com
 * Plugin URI: https://jivochat.com/
 * Description: Omnichannel Live Chat and Help Desk plugin, optimized for Wordpress. Free, fast, easy to install and to use. Turn your visitors into happy customers!
 * Version: 1.3.5.3
 *
 * Text Domain: jivochat
 * Domain Path: /languages/
 */


if (!defined('ABSPATH')) die("go away!");

load_plugin_textdomain('jivochat', false, PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));

$siteUrl = get_site_url();
$paths = explode('.', $siteUrl);
$domain = end($paths);
$domain = str_replace('/', '', $domain);

if (get_bloginfo("language") == "ru_RU") {
    $jivo_addr = 'https://www.jivo.ru';
} else {
    $jivo_addr = 'https://www.jivochat.com';
}

$jivositeUrl = 'https://api.jivosite.com';
if (!extension_loaded('openssl')) {
    str_replace('https:','http:', $jivositeUrl);
}

define("DOMAIN", $domain);
define("JIVOSITE_URL", $jivositeUrl);
define("JIVOSITE_WIDGET_URL", "//code.jivosite.com/widget/");
define("JIVO_URL", $jivo_addr);
define("JIVO_LANGUAGES_URL", JIVOSITE_URL."/web/integration/languages");
define("JIVO_INTEGRATION_URL", JIVOSITE_URL."/web/integration");
define("JIVO_PLUGIN_URL", plugin_dir_url(__FILE__));
define("JIVO_IMG_URL", plugin_dir_url(__FILE__)."/img/");
define("PLUGIN_VERSION", "1.3.5.3");

/*
 * Register the settings
 */
function jivosite_register_settings(){
    register_setting('jivosite_token', 'jivosite_token');
    register_setting('jivosite_widget_id', 'jivosite_widget_id');
    register_setting('jivosite_lang_code', 'jivosite_lang_code');
    register_setting('jivosite_plugin_version', 'jivosite_plugin_version');
}
//add plugin to options menu
function catalog_admin_menu(){
    load_plugin_textdomain('jivochat', false, PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
    jivosite_register_settings();
    add_menu_page(__('JivoChat','jivochat'), __('JivoChat','jivochat'), 'manage_options', basename(__FILE__), 'jivositePreferences', JIVO_IMG_URL."icon.png");
}
add_action('admin_menu', 'catalog_admin_menu');

add_action('wp_footer', 'jivositeAppend', 100000);

function jivositeAppend() {
    echo jivosite::getInstance()->append(
        get_option('jivosite_widget_id')
    );
}

function jivositePreferences() {
    load_plugin_textdomain('jivochat', false, PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
    jivositeUpdate();
    echo jivosite::getInstance()->render();
}

function jivositeUpdate() {
    if (PLUGIN_VERSION !== get_option('jivosite_plugin_version')) {
        update_option('jivosite_plugin_version', PLUGIN_VERSION);
    }
}

class jivosite {

    protected static $instance;

    private function __construct()
    {
        $this->transportEnabled = $this->isTransportEnabled();
    }

    private function __clone()    {}
    private function __wakeup()   {}

    private $transportEnabled;

    public static function getInstance() {
        if ( is_null(self::$instance) ) {
            self::$instance = new jivosite();
        }

        return self::$instance;
    }

    public function catchPost(){
        if (!empty($_POST['email']) && !empty($_POST['userPassword']) && !empty($_POST['languageList'])) {
            // получаем данные для запроса
            $query['body']['partnerId'] = "wordpress";
            $query['body']['email'] = $_POST['email'];
            $query['body']['userPassword'] = $_POST['userPassword'];
            $query['body']['userDisplayName'] = $_POST['userDisplayName'];
            $query['body']['lang'] = $_POST['languageList'];
            $query['body']['siteUrl'] = get_site_url();
            $authToken = md5(time().get_site_url());
            $query['body']['authToken'] = $authToken;

            $response = $this->getIntegrationInstallResponse($query);

            if ($response) {
                if(strstr($response,'Error')){
                    return ["error" => $response];
                } else {
                    $this->updateJivoOption('widget_id', $response);
                    $this->updateJivoOption('token', $authToken);
                    $this->updateJivoOption('lang_code', $query['body']['lang']);
                    return true;
                }
            }
        } elseif (!empty($_POST['email']) && !empty($_POST['userPassword']) && empty($_POST['languageList'])) {
            $query['body']['partnerId'] = "wordpress";
            $query['body']['email'] = $_POST['email'];
            $query['body']['userPassword'] = $_POST['userPassword'];
            $query['body']['userDisplayName'] = 'userDisplayName';
            $query['body']['siteUrl'] = get_site_url();

            $response = $this->getIntegrationInstallResponse($query);

            if ($response) {
                if(strstr($response,'Error')){
                    return ["error" => $response, "login" => true,];
                } else {
                    $this->updateJivoOption('widget_id', $response);
                    return true;
                }
            }
        } elseif (!empty($_POST['reset']) && $_POST['reset'] == 'reset') {
            $this->updateJivoOption('widget_id', '');
            $this->updateJivoOption('token', '');
            $this->updateJivoOption('lang_code', '');
        }
    }

    /**
     * render admin page
     */
    public function render(){
        if ($this->transportEnabled) {
            try {
                if (!$this->widget_id) {
                    $languageList = $this->getLanguageList();
                }

                $result = $this->catchPost();
                if ($result === true) {
                    $post_install = true;
                }

                if (!empty($result['login'])) {
                    $login = true;
                }

                $error = '';
                if (is_array($result) && isset($result['error'])) {
                    $error = $result['error'];
                }

                require_once "templates/page.php";
            } catch (\Exception $e) {
                require_once "templates/error.php";
            }
        } else {
            require_once "templates/error.php";
        }
    }

    public function append($widget_id = false){
        if ($widget_id)
            require_once "templates/script.php";
    }

    private function isTransportEnabled()
    {
        if (!extension_loaded('curl') && !ini_get('allow_url_fopen')) {
            return false;
        }

        return true;
    }

    private function getIntegrationInstallResponse($query)
    {
        if (extension_loaded('curl')) {
            return wp_remote_post(JIVO_INTEGRATION_URL.'/install', $query)['body'];
        }

        if (ini_get('allow_url_fopen')) {
            foreach ($query['body'] as $key => $value){
                $content[$key] = $value;
            }

            return file_get_contents(
                JIVO_INTEGRATION_URL.'/install',
                false,
                stream_context_create(
                    [
                        'http' => [
                            'method' => 'POST',
                            'header' => 'Content-Type: application/x-www-form-urlencoded',
                            'content' => http_build_query($content),
                        ],
                    ]
                )
            );
        }

        return null;
    }

    private function updateJivoOption($optionName, $optionValue)
    {
        if (current_user_can('manage_options')) {
            update_option('jivosite_'.$optionName, $optionValue);
        }
    }

    private function getLanguageList()
    {
        $languageList = $this->fetchSupportedLanguages();

        $suggestedLanguage = $this->getSuggestedLanguage(DOMAIN, $languageList);

        if ($suggestedLanguage) {
            $languageList = $this->getReorderedLanguageList(
                $suggestedLanguage,
                $languageList
            );
        }

        return json_encode($languageList);
    }

    private function fetchSupportedLanguages()
    {
        if (extension_loaded('curl')) {
            return json_decode(wp_remote_get(JIVO_LANGUAGES_URL)['body'], true);
        }

        if (ini_get('allow_url_fopen')) {
            return json_decode(file_get_contents(JIVO_LANGUAGES_URL), true);
        }

        return null;
    }

    private function getSuggestedLanguage($domain, $languageList)
    {
        $suggestedLanguage = null;

        $suggestedLanguage = $this->getSuggestedLanguageByDomain($domain, $languageList);

        if (! $suggestedLanguage) {
            $suggestedLanguage = $this->getSuggestedLanguageByLocale(
                str_replace('-', '_', get_bloginfo("language")),
                $languageList
            );
        }

        return $suggestedLanguage;
    }

    private function getSuggestedLanguageByDomain($domain, $languageList)
    {
        $suggestedLanguage = null;

        foreach ($languageList as $language) {
            if (in_array($domain, $language['domains'])) {
                $suggestedLanguage = $language;
                break;
            }
        }

        return $suggestedLanguage;
    }

    private function getSuggestedLanguageByLocale($locale, $languageList)
    {
        $suggestedLanguage = null;

        foreach ($languageList as $language) {
            if ($language['name'] == 'Spanish (General)') {
                $codes = explode(',', $language['code']);
                foreach ($codes as $code) {
                    if (strpos($locale, $code) !== false) {
                        $language['code'] = $locale;
                        $suggestedLanguage = $language;
                        break;
                    }
                }
            } else {
                if (strpos($locale, $language['code']) !== false) {
                    $suggestedLanguage = $language;
                }
            }
            if ($suggestedLanguage) {
                break;
            }
        }

        return $suggestedLanguage;
    }

    private function getReorderedLanguageList($suggestedLanguage, $languageList)
    {
        $suggestedLanguageIndex = array_search($suggestedLanguage, $languageList);
        array_splice($languageList, $suggestedLanguageIndex, 1);
        array_unshift($languageList, $suggestedLanguage);

        return $languageList;
    }
}