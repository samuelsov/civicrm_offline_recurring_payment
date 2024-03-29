{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}
    {include file="CRM/Contribute/Form/Contribution.tpl"}
{elseif $action eq 4}
    {include file="CRM/Contribute/Form/ContributionView.tpl"}
{else}
    <div class="view-content">
        <div id="help">
            {if $permission EQ 'edit'}
                {capture assign=newContribURL}{crmURL p="civicrm/contact/view/contribution" q="reset=1&action=add&cid=`$contactId`&context=contribution"}{/capture}
                {ts 1=$newContribURL}Click <a href='%1'>Record Contribution (Check, Cash, EFT ...)</a> to record a new contribution received from this contact.{/ts}
                {if $newCredit}
                    {capture assign=newCreditURL}{crmURL p="civicrm/contact/view/contribution" q="reset=1&action=add&cid=`$contactId`&context=contribution&mode=live"}{/capture}
                    {ts 1=$newCreditURL}Click <a href='%1'>Submit Credit Card Contribution</a> to process a new contribution on behalf of the contributor using their credit card.{/ts}
                {/if}
            {else}
                {ts 1=$displayName}Contributions received from %1 since inception.{/ts} 
            {/if}
        </div>
    
        {if $action eq 16 and $permission EQ 'edit'}
            <div class="action-link">
                <a accesskey="N" href="{$newContribURL}" class="button"><span><div class="icon add-icon"></div>{ts}Record Contribution (Check, Cash, EFT ...){/ts}</span></a>
                {if $newCredit}
                    <a accesskey="N" href="{$newCreditURL}" class="button"><span><div class="icon add-icon"></div>{ts}Submit Credit Card Contribution{/ts}</span></a>
                {/if}
                <br /><br />
            </div>
	    <div class='clear'> </div>
        {/if}


        {include file="CRM/Contribute/Form/Recurring.tpl"}
        <p> </p>

        {if $rows}
            {include file="CRM/Contribute/Page/ContributionTotals.tpl" mode="view"}
            <p> </p>
            {include file="CRM/Contribute/Form/Selector.tpl"} 
        {else}
            <div class="messages status">
                    <div class="icon inform-icon"></div>
                    {ts}No contributions have been recorded from this contact.{/ts}
            </div>
        {/if}

        {if $honor}	
            <div class="solid-border-top">
                <br /><label>{ts 1=$displayName}Contributions made in honor of %1{/ts}</label>
            </div>
            {include file="CRM/Contribute/Page/ContributionHonor.tpl"}	
        {/if} 
        
        {if $softCredit}
            <div class="solid-border-top">
                <br />
                <div class="label">{ts}Soft credits{/ts} {help id="id-soft_credit"}</div>
                <div class="spacer"></div>
            </div>
            {include file="CRM/Contribute/Page/ContributionSoft.tpl"}
        {/if}
    </div>
{/if}
