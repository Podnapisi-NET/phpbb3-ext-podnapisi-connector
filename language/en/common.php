<?php
/**
 *
 * @package phpBB Extension - Podnapisi Connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

if (!defined('IN_PHPBB'))
  exit;

if (empty($lang) || !is_array($lang))
  $lang = array();

$lang = array_merge($lang, array(
  'AUTH_KEY'                         => "Authentication key",
  'AUTH_KEY_DESCRIPTION'             => "Used to authenticate requests, might be good idea to use .htacces or nginx configs to allow only proper source IPs",
  'USER_SAFE_ATTRIBUTES'             => "User safe attributes",
  'USER_SAFE_ATTRIBUTES_DESCRIPTION' => "Comma separated attributes of the users table (phpbb_users) the API will be allowed to modify",

  'ACP_CONNECTOR'       => 'Settings',
  'ACP_CONNECTOR_TITLE' => 'External website connector',
  'ACP_CONNECTOR_SAVED' => "General connector settings saved.",
));
