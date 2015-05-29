<?php
/**
 *
 * @package phpBB Extension - Podnapisi Connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace podnapisi\connector\migrations;

class release_0_0_1 extends \phpbb\db\migration\migration
{

  public function effectively_installed()
  {
    return isset($this->config['podnapisi_connector_auth_key']);
  }

  public function update_data()
  {
    return array(
      array('config.add', array('podnapisi_connector_auth_key', '')),
      array('config.add', array('podnapisi_connector_user_safe_attributes', 'sig')),

      array('module.add', array(
        'acp',
        'ACP_CAT_DOT_MODS',
        'ACP_CONNECTOR_TITLE'
      )),
      array('module.add', array(
          'acp',
          'ACP_CONNECTOR_TITLE',
        array(
          'module_basename' => '\podnapisi\connector\acp\main_module',
          'modes'           => array('settings'),
        ),
      )),
    );
  }

}
