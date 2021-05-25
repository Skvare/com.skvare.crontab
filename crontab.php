<?php

require_once 'crontab.civix.php';
// phpcs:disable
use CRM_Crontab_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function crontab_civicrm_config(&$config) {
  _crontab_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function crontab_civicrm_xmlMenu(&$files) {
  _crontab_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function crontab_civicrm_install() {
  _crontab_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function crontab_civicrm_postInstall() {
  _crontab_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function crontab_civicrm_uninstall() {
  _crontab_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function crontab_civicrm_enable() {
  _crontab_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function crontab_civicrm_disable() {
  _crontab_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function crontab_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _crontab_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function crontab_civicrm_managed(&$entities) {
  _crontab_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function crontab_civicrm_caseTypes(&$caseTypes) {
  _crontab_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function crontab_civicrm_angularModules(&$angularModules) {
  _crontab_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function crontab_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _crontab_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_thems().
 */
function crontab_civicrm_themes(&$themes) {
  _crontab_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function crontab_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function crontab_civicrm_navigationMenu(&$menu) {
//  _crontab_civix_insert_navigation_menu($menu, 'Mailings', array(
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ));
//  _crontab_civix_navigationMenu($menu);
//}

function crontab_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Job') {
    $month = CRM_Crontab_Utils::month();
    $days = CRM_Crontab_Utils::days();
    $weekdays = CRM_Crontab_Utils::weekdays();
    $hours = CRM_Crontab_Utils::hours();
    $minutes = CRM_Crontab_Utils::minute();
    $moment = CRM_Crontab_Utils::basic();
    $select2style = [
      'multiple' => TRUE,
      'style' => 'width: 100%; max-width: 60em;height:220px',
      'class' => 'crm-select2',
      //'placeholder' => ts('- select -'),
    ];

    $form->add('checkbox', 'crontab_apply', ts('Advanced Job Scheduling'));
    $form->add('select', 'basic_crontab', ts('Schedule Time'), ['' => 'Advanced Settings'] + $moment, FALSE);
    $form->add('select', 'hour', ts('Hour'), $hours, FALSE, $select2style);
    $form->add('select', 'minute', ts('Minute'), $minutes, FALSE);
    $form->add('select', 'day', ts('Day'), $days, FALSE, $select2style);
    $form->add('select', 'month', ts('Month'), $month, FALSE, $select2style);
    $form->add('select', 'weekday', ts('Day of week'), $weekdays, FALSE, $select2style);
    $form->add('text', 'crontab_frequency', ts('New Run Frequency'));
    $form->add('text', 'crontab_offset', ts('Run Frequency Time Margin'));
    if ($form->_action & CRM_Core_Action::UPDATE || $form->_action & CRM_Core_Action::VIEW) {
      $jobExtras = CRM_Crontab_Utils::getSettings($form->_id);
      CRM_Core_Error::debug_var('$form $jobExtras', $jobExtras);
      if (!empty($jobExtras)) {
        $jobExtras['crontab_apply'] = 1;
        if (empty($jobExtras['crontab_offset'])) {
          $jobExtras['crontab_offset'] = 5;
        }
        $form->setDefaults($jobExtras);
      }
    }
    if ($form->_action & CRM_Core_Action::ADD) {
      $jobExtras = [];
      $jobExtras['crontab_offset'] = 5;
      $form->setDefaults($jobExtras);
    }
  }
}

function crontab_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  if ($formName == 'CRM_Admin_Form_Job') {
    if (!empty($fields['crontab_apply']) && empty($fields['basic_crontab'])) {
      if (empty($fields['hour'])) {
        $errors['hour'] = ts("Hour is required field.");
      }
      if (empty($fields['day'])) {
        $errors['day'] = ts("Day is required field.");
      }
      if (empty($fields['month'])) {
        $errors['month'] = ts("Month is required field.");
      }
      if (empty($fields['weekday'])) {
        $errors['weekday'] = ts("Day of week is required field.");
      }
    }
  }
}

/**
 * Implements hook_civicrm_apiWrappers().
 */
function crontab_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Core_DAO_Job']['fields_callback'][]
    = function ($class, &$fields) {
    $fields['crontab_frequency'] = [
      'name' => 'crontab_frequency',
      'type' => CRM_Utils_Type::T_INT,
      'title' => ts('Cront Tab Frequency'),
      'description' => 'Cront Tab Frequency, this overwrite civicrm default frequency time',
      'table_name' => 'civicrm_job',
      'entity' => 'Job',
      'bao' => 'CRM_Core_BAO_Job',
      'localizable' => 0,
    ];

    $fields['crontab_offset'] = [
      'name' => 'crontab_offset',
      'type' => CRM_Utils_Type::T_INT,
      'title' => ts('Cron Tab offset'),
      'description' => 'Offset margine to consider job eligiable for run.',
      'table_name' => 'civicrm_job',
      'entity' => 'Job',
      'bao' => 'CRM_Core_BAO_Job',
      'localizable' => 0,
    ];
  };
}

/**
 * Implements hook_civicrm_postProcess().
 */
function crontab_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Job') {
    $submit = $form->getVar('_submitValues');
    if (empty($submit['crontab_apply'])) {
      return;
    }
    $crontab_frequency = $submit['crontab_frequency'];
    $crontab_offset = $submit['crontab_offset'];
    $paramCrontab = [1 => [$submit['crontab_frequency'], 'String'], 2 => [$submit['crontab_offset'], 'String']];
    if (empty($form->_id) && !empty($submit['crontab_frequency'])) {
      $result = civicrm_api3('Job', 'get', [
        'sequential' => 1,
        'return' => ["id"],
        'name' => $submit['name'],
      ]);
      if ($result['count'] == 1) {
        $jobID = $result['values'][0]['id'];
        $query = "
        UPDATE civicrm_job
        SET crontab_frequency = %1,
            crontab_offset = %2
        WHERE id = $jobID
        ";
        CRM_Core_DAO::executeQuery($query, $paramCrontab);
      }
    }
    else {
      $jobID = $form->_id;
      $query = "
      UPDATE civicrm_job
        SET crontab_frequency = %1,
            crontab_offset = %2
      WHERE id = $jobID
      ";
      CRM_Core_DAO::executeQuery($query, $paramCrontab);
    }
  }
}


function crontab_civicrm_cron(&$jobManager) {
  $jobManager->jobs = CRM_Crontab_Utils::_getJobs();
}