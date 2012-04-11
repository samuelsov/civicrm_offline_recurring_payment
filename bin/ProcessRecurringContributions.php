<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */
 
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

$debug = false;

require_once 'CRM/Utils/Request.php';

function ProcessRecurringContributions( ) {
    global $debug;
    
    $config =& CRM_Core_Config::singleton();
          
    require_once 'CRM/Utils/System.php';
    require_once 'CRM/Utils/Hook.php';
 
    $dtDay = mktime(0, 0, 0, date("m") , date("d") , date("Y"));
    //$dtDay = mktime(0, 0, 0, '10', '14', '2011'); 
    $dtCurrentDay = date("Ymd", $dtDay);
    $dtCurrentDayStart  = $dtCurrentDay."000000"; 
    $dtCurrentDayEnd = $dtCurrentDay."235959"; 
    
    // Select the recurring payment, where current date is equal to next scheduled date   
    $sql = "SELECT * FROM civicrm_contribution_recur ccr 
                    WHERE ccr.end_date IS NULL AND ccr.next_sched_contribution >= {$dtCurrentDayStart} AND ccr.next_sched_contribution <= {$dtCurrentDayEnd}";
                    //AND cm.status_id = 3              

    $dao = CRM_Core_DAO::executeQuery( $sql );
    
    while($dao->fetch()) {
        
        $exp = explode_trnx_id($dao->trxn_id);
        print_r($exp, 1);
        
        $contact_id                 = $dao->contact_id;
        $hash                       = md5(uniqid(rand(), true)); 
        $total_amount               = $dao->amount;
        $contribution_recur_id      = $dao->id;
        //$contribution_type_id       = !empty($exp['contribution_type_id']) ? $exp['contribution_type_id'] : 1;
        $contribution_type_id       = $dao->contribution_type_id;
        $source                     = ts("Offline Recurring Contribution");
        //$receive_date               = date("YmdHis", $dtDay);
        $receive_date               = date('YmdHis', strtotime($dao->next_sched_contribution));
        $contribution_status_id     = 1;
        //$payment_instrument_id      = !empty($exp['payment_instrument_id']) ? $exp['payment_instrument_id'] : 5;
        $payment_instrument_id      = $dao->payment_instrument_id;
        

        require_once 'api/v2/Contribution.php';
        $params = array(
                'version'                => '3',
                'contact_id'             => $contact_id,
                //'campaign_id'            => $exp['campaign_id'],
                'campaign_id'            => $dao->campaign_id,
                'receive_date'           => $receive_date,
                'total_amount'           => $total_amount,
                'payment_instrument_id'  => $payment_instrument_id,
                'trxn_id'                => $hash,
                'source'                 => $source,
                'contribution_status_id' => $contribution_status_id,
                'contribution_type_id'   => $contribution_type_id,
                'contribution_recur_id'  => $contribution_recur_id,
                'contribution_page_id'   => $entity_id,
                );

        print_r($params);

        $contributionArray = civicrm_api("Contribution","create", $params);
        //$contributionArray =& civicrm_contribution_add($params);

        $contribution_id = $contributionArray['id'];

        // campaign_id doesn't work ?? update manually
        $campaign_id = $exp['campaign_id'];
        if (!empty($campaign_id)) {
          $update_sql = "UPDATE civicrm_contribution SET campaign_id = $campaign_id WHERE id = '".$contribution_id."'";
          echo $update_sql;
          CRM_Core_DAO::executeQuery( $update_sql );
        }

        $mem_end_date = $member_dao->end_date;
        $temp_date = strtotime($dao->next_sched_contribution);
        
        $next_collectionDate = strtotime ( "+$dao->frequency_interval $dao->frequency_unit" , $temp_date ) ;
        $next_collectionDate = date ( 'YmdHis' , $next_collectionDate );
        
        $update_sql = "UPDATE civicrm_contribution_recur SET next_sched_contribution = '$next_collectionDate' WHERE id = '".$dao->id."'";
        CRM_Core_DAO::executeQuery( $update_sql );
        
        require_once 'api/v2/Activity.php';
        $params = array(
             'activity_type_id' => 6,
             'source_contact_id' => $contact_id,
             'assignee_contact_id' => $contact_id,
             'subject' => "Offline Recurring Contribution - ".$total_amount,
             'status_id' => 2,
             'activity_date_time' => date("YmdHis"), 
            );
        $act = civicrm_activity_create($params);
    }
    
    echo "Contribution records created successfully....";
    
    exit( );
}    


function explode_trnx_id($trxn_id) {
  // because the table civicrm_contribution_recurring doesn't allow campaign and other important data - need to change core
  // trxn_id = num::campaign_id::contrib_type_id::instrument_id

  $exp = '';
  $exp = explode("::", $trxn_id);

  return array(
    'trxn_id' => isset($exp[0]) ? $exp[0] : $trxn_id,
    'campaign_id' => isset($exp[1]) ? $exp[1] : NULL,
    'contribution_type_id'=> isset($exp[2]) ? $exp[2] : NULL,
    'payment_instrument_id'=> isset($exp[3]) ? $exp[3] : NULL,
  );
}

ProcessRecurringContributions();

?>

