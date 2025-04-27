<?php

namespace Cart2cart;

class Bridge_Errors
{
  const MODULE_DEFAULT = 5060;
  const MODULE_BRIDGE_ALREADY_INSTALLED = 5061;
  const MODULE_BRIDGE_SUCCESSFULLY_INSTALLED = 5062;

  const MODULE_ERROR_DEFAULT = 'defaultError';
  const MODULE_ERROR_EMPTY_TOKEN = 5068;
  const MODULE_ERROR_ROOT_DIR_PERMISSION = 5069;
  const MODULE_ERROR_INSTALLED_PERMISSION = 5071;
  const MODULE_ERROR_PERMISSION = 5072;

  protected static $_errorMessages = array(
    self::MODULE_ERROR_DEFAULT => 'Bridge Install action executed successfully.',
    self::MODULE_ERROR_EMPTY_TOKEN => 'Token can\'t be blank.',
    self::MODULE_ERROR_ROOT_DIR_PERMISSION => 'Please change the permission for Wordpress root folder to <strong>777</strong> or install the Connection Bridge manually. If this error persists, please contact our Support Team.',
    self::MODULE_ERROR_INSTALLED_PERMISSION => 'Please provide following permissions: <strong>777</strong> for the Connection Bridge folder; <strong>666</strong> for the files inside the folder. If assistance needed, feel free to contact our Support Team.',
    self::MODULE_ERROR_PERMISSION => 'Ensure Wordpress store root folder is <strong>writable</strong>. The Connection Bridge folder permission should be set to <strong>777</strong>; files inside the folder - <strong>666</strong>. If this doesn’t resolve the issue, try reinstalling the Connection Bridge manually. If this error persists, please contact our Support Team for assistance.',
  );

  public static function getErrorMessage($code)
  {
    return (!empty(self::$_errorMessages[$code])) ? self::$_errorMessages[$code] : self::$_errorMessages[self::MODULE_ERROR_DEFAULT];
  }
}