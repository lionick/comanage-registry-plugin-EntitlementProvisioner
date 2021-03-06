<?php
App::uses('CakeLog', 'Log');
/**
 * This class is quering the MITREid Connect Database
 *
 * 
 */
class MitreId
{

  //public static $entitlementFormat = '/(^urn:mace:egi.eu:(.*)#aai.egi.eu$)|(^urn:mace:egi.eu:aai.egi.eu:(.*))/i';
  
  /**
   * config
   *
   * @param  mixed $mitreId
   * @param  mixed $datasource
   * @param  string $table_name
   * @return void
   */
  public static function config($mitreId, $datasource, $table_name, $entitlement_format = NULL)
  {
    $mitreId->useDbConfig = $datasource->configKeyName;
    $mitreId->useTable = $table_name;
    $mitreId->entitlementFormat = $entitlement_format;
  }
  
  /**
   * getCurrentEntitlements
   *
   * @param  mixed $mitreId
   * @param  integer $user_id
   * @return void
   */
  public static function getCurrentEntitlements($mitreId, $user_id) {
    $current_entitlements = $mitreId->find('all', array('conditions' => array('MitreIdEntitlements.user_id' => $user_id)));
    $current_entitlements = Hash::extract($current_entitlements, '{n}.MitreIdEntitlements.edu_person_entitlement');
    return $current_entitlements;
  }
  
  /**
   * deleteOldEntitlements
   *
   * @param  mixed $mitreId
   * @param  integer $user_id
   * @param  array $current_entitlements
   * @param  array $new_entitlements
   * @return void
   */
  public static function deleteOldEntitlements($mitreId, $user_id, $current_entitlements, $new_entitlements) {
    $deleteEntitlements = array_diff($current_entitlements, $new_entitlements);
    //Remove only those from check-in
    if(!empty($mitreId->entitlementFormat)) {
      $deleteEntitlements  = preg_grep($mitreId->entitlementFormat, $deleteEntitlements);
    }
    CakeLog::write('debug', __METHOD__ . ':: entitlements to be deleted from MitreId' . var_export($deleteEntitlements, true), LOG_DEBUG);
    if(!empty($deleteEntitlements)) {
      //Delete
      $deleteEntitlementsParam = '(\'' . implode("','", $deleteEntitlements) . '\')';
    //  $mitreId->query('DELETE FROM user_edu_person_entitlement'
    //    . ' WHERE user_id=' . $user_id
    //    . ' AND edu_person_entitlement IN ' . $deleteEntitlementsParam);
    }
  }
  
  /**
   * deleteAllEntitlements
   *
   * @param  mixed $mitreId
   * @param  integer $user_id
   * @return void
   */
  public static function deleteAllEntitlements($mitreId, $user_id) {
    CakeLog::write('debug', __METHOD__ . ':: delete all entitlements from mitreid', LOG_DEBUG);
  //  $mitreId->query('DELETE FROM user_edu_person_entitlement'
  //  . ' WHERE user_id=' . $user_id);
  }
  
  /**
   * insertNewEntitlements
   *
   * @param  mixed $mitreId
   * @param  integer $user_id
   * @param  array $current_entitlements
   * @param  array $new_entitlements
   * @return void
   */
  public static function insertNewEntitlements($mitreId, $user_id, $current_entitlements, $new_entitlements) {
    $insertEntitlements = array_diff($new_entitlements, $current_entitlements);
    CakeLog::write('debug', __METHOD__ . ':: entitlements to be inserted to MitreId' . var_export($insertEntitlements, true), LOG_DEBUG);
    if(!empty($insertEntitlements)) {
      //Insert
      $insertEntitlementsParam = '';
      foreach ($insertEntitlements as $entitlement) {
        $insertEntitlementsParam .= '(' . $user_id . ',\'' . $entitlement . '\'),';
      }
     // $mitreId->query('INSERT INTO user_edu_person_entitlement (user_id, edu_person_entitlement) VALUES ' . substr($insertEntitlementsParam, 0, -1));
    }
  }
}
