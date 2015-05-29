<?php
/**
 *
 * @package phpBB Extension - Podnapisi connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace podnapisi\connector\acp;

class main_module
{
  public $u_action;

  function main($id, $mode)
  {
    global $db, $user, $auth, $template, $cache, $request, $table_prefix;
    global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

    $user->add_lang('acp/common');

    $this->tpl_name = 'connector_edit';
    $this->page_title = $user->lang('ACP_CONNECTOR_TITLE');

    add_form_key('podnapisi/connector');
    if ($request->is_set_post('submit')) {
      // Submit changes
      if (!check_form_key('podnapisi/connector'))
        trigger_error('FORM_INVALID', E_USER_ERROR);

      $config->set('podnapisi_connector_auth_key', $request->variable('auth_key', ''));
      $config->set('podnapisi_connector_user_safe_attributes', $request->variable('user_safe_attributes', ''));

      trigger_error($user->lang('ACP_CONNECTOR_SAVED') . adm_back_link($this->u_action));
    }

    $template->assign_vars(array(
      'AUTH_KEY'             => $config['podnapisi_connector_auth_key'],
      'USER_SAFE_ATTRIBUTES' => $config['podnapisi_connector_user_safe_attributes'],
    ));
  }
}
