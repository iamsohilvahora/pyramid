<?php

namespace Cart2cart;

class Plugin
{
  const PLUGINS_TITLE = 'Cart2cart: Magento to Woocommerce Plugin';

  const APP_LINK = 'https://woocommerce.app.shopping-cart-migration.com/';
  const PUBLIC_SITE_LINK = 'https://www.shopping-cart-migration.com/';

  const URL_LIMIT = 'from-magento-to-woocommerce';
  const SOURCE_CART_ID = 'Magento';

  const PLUGIN_PAGE = 'cart2cart-migration';
  const HOW_IT_WORKS_PAGE = 'cart2cart-how-it-works';
  const SERVICES_PAGE = 'cart2cart-additional-services';
  const HELP_PAGE = 'cart2cart-migration-help';
  const SETTINGS_PAGE = 'cart2cart-migration-settings';

  const SETTINGS_SECTION = 'cart2cart-settings';

  /** @var \Cart2cart\View  */
  public $view;

  public function __construct()
  {
    $this->view = new View();
    $this->_addActions();
  }

  public static function init()
  {
    new self;
  }

  private function _addActions()
  {
    add_action('admin_menu', array($this, 'registerMenuItems'));
    add_action('admin_init', [$this, 'addSettingsPage']);
    add_action('wp_ajax_nopriv_actionsProcessor', array($this, 'actionsProcessor'));
    add_action('wp_ajax_setToken', [$this, 'setToken'], 1);
  }

  public function registerMenuItems()
  {
    add_menu_page(
      'Cart2Cart Migration',
      'Cart2Cart Migration',
      'manage_options',
      self::PLUGIN_PAGE,
      array($this, 'getPluginsPageContent'),
      plugins_url('/img/icon.png', CART2CART_PLUGIN_ROOT_DIR . 'dir'),
      60
    );

    add_submenu_page(self::PLUGIN_PAGE, 'How it works', 'How it works', 'manage_options', self::HOW_IT_WORKS_PAGE, array($this, 'howItWorksPageContent'));
    add_submenu_page(self::PLUGIN_PAGE, 'Additional Services', 'Additional Services', 'manage_options', self::SERVICES_PAGE, array($this, 'servicesPageContent'));
    add_submenu_page(self::PLUGIN_PAGE, 'Live Chat & Help', 'Live Chat & Help', 'manage_options', self::HELP_PAGE, array($this, 'faqHelpPageContent'));
    add_submenu_page(self::PLUGIN_PAGE, 'Settings', 'Settings', 'manage_options', self::SETTINGS_PAGE, [$this, 'settingsPageContent']);
  }

  public function getPluginsPageContent()
  {
    $this->view->assign('framePath', self::APP_LINK . self::URL_LIMIT . '/?' . http_build_query(array(
      'storeUrl' => get_site_url(),
      'storeAdminUrl' => admin_url('admin-ajax.php'),
    )));

    echo $this->view->render('main/index.phtml');
  }

  public static function actionsProcessor()
  {
    header('Access-Control-Allow-Origin: *');

    $bridge = new Bridge;
    $flags = FILTER_SANITIZE_STRING;

    $response = $bridge->perform(filter_input(INPUT_POST, 'mode', $flags), filter_input(INPUT_POST, 'token', $flags));

    wp_die(json_encode($response, JSON_UNESCAPED_UNICODE));
  }

  public function supportChatPageContent()
  {
    $this->view->assign('framePath', 'https://support.magneticone.com/visitor/index.php?/LiveChat/Chat/Request/_proactive=1/_filterDepartmentID=6%2C66/_randomNumber=23/_promptType=chat');

    echo $this->view->render('support/chat.phtml');
  }

  public function howItWorksPageContent()
  {
    $this->view->assign('framePath', self::APP_LINK . sprintf('module/woocommerce/how-it-works/sourceId/%s/targetId/Woocommerce', self::SOURCE_CART_ID));
    echo $this->view->render('support/how-it-works.phtml');
  }

  public function servicesPageContent()
  {
    $this->view->assign('framePath', self::APP_LINK . 'module/woocommerce/additional-services');
    echo $this->view->render('support/how-it-works.phtml');
  }

  public function faqHelpPageContent()
  {
    $this->view->assign('framePath', self::APP_LINK . 'module/woocommerce/help-and-faqs');
    echo $this->view->render('support/faqs-and-help.phtml');
  }

  public function addSettingsPage()
  {
    add_settings_section(self::SETTINGS_SECTION, '', null, self::SETTINGS_PAGE);

    $fields = array(
      array(
        'title' => 'Migration Token',
        'name' => 'c2c-migration-token',
        'type' => 'text',
        'description' => 'Tokens bounded to your personal account and provided by Cart2Cart. Will let no user, except you, access your store database',
        'value' => get_option('c2c-migration-token'),
      )
    );

    foreach ($fields as $field) {
      register_setting(
        self::SETTINGS_SECTION,
        $field['name']
      );

      add_settings_field(
        $field['name'],
        $field['title'],
        function() use ($field) {
          $this->view->assign('field', $field);
          echo $this->view->render('settings/fields/' . $field['type'] . '.phtml');
        },
        self::SETTINGS_PAGE,
        self::SETTINGS_SECTION
      );
    }
  }

  public function settingsPageContent()
  {
    global $wpdb;
    $res = $wpdb->get_results(
      "select value from {$wpdb->prefix}c2c_config where `key` = 'c2cMigrationToken'",
      ARRAY_A
    );
    
    $token = isset($res[0]['value']) ? $res[0]['value'] : false;
    $this->view->assign('token', $token);
    echo $this->view->render('settings/index.phtml');
  }

  public function setToken()
  {
    $success = true;

    try {

      global $wpdb;

      if (!$wpdb->insert("{$wpdb->prefix}c2c_config", [
        'key' => 'c2cMigrationToken',
        'value' => $token = preg_replace('#\W#', '', $_POST['c2cMigrationToken']),
      ]) && !$wpdb->update(
          "{$wpdb->prefix}c2c_config",
          array('value' => $token),
          array('key' => 'c2cMigrationToken')
        )) {
        throw new \Exception('Can\'t update token');
      }
    } catch (\Throwable $e) {
      $success = false;
      $message = $e->getMessage();
    }

    wp_die(json_encode([
      'success' => $success,
      'message' => isset($message) ? $message : $message = "Token successfully saved!",
    ], JSON_UNESCAPED_UNICODE));
  }
}