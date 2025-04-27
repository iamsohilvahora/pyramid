<?php

namespace Cart2cart;

class Installer
{
  public static function activate()
  {
    global $wpdb;
    $query = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}c2c_config` (
              `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
              `key` varchar(128) NOT NULL,
              `value` text NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `config_key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    $wpdb->query($query);
  }

  public static function uninstall()
  {
    global $wpdb;
    $query = "DROP TABLE IF EXISTS `{$wpdb->prefix}c2c_config`";

    $wpdb->query($query);
  }

  public static function deactivate()
  {
  }
}