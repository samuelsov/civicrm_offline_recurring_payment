        <!-- Display recurring contribution section -->
        <br />
         <div class="view-content">
            <div id="help">
                Click <a accesskey="N" href="{crmURL p="civicrm/recurring/add" q="action=add&cid=$contactId&reset=1"}">Set up Recurring Payment</a> to set up a recurring payment. Please note this will not create a contribution record for the contact. You need to set up a background process (cron job) which will create contributions depending on the recurring payment settings you specify.
            </div>
          </div>

          <div class="action-link">
            <a accesskey="N" href="{crmURL p="civicrm/recurring/add" q="action=add&cid=$contactId&reset=1"}" class="button"><span><div class="icon add-icon"></div>{ts}Set up Recurring Payment{/ts}</span></a>
          </div>

          {if $recurArray}

          <table class="selector">
          <thead >

          <tr>
             <th>{ts}Amount{/ts}</th>
             <th>{ts}Frequency{/ts}</th>
             <th>{ts}Start Date{/ts}</th>
             <th>{ts}Next Scheduled Date{/ts}</th>
             <th>{ts}End Date{/ts}</th>
             <th>&nbsp;</th>
          </tr>

          </thead>

           {foreach from=$recurArray item=row}
           {assign var=id value=$row.id}
           <tr>
              <td>{$row.amount}</td>
              <td>every {$row.frequency_interval} {$row.frequency_unit} </td>
              <td>{$row.start_date|crmDate}</td>
              <td>{$row.next_sched_contribution|crmDate}</td>
              <td>{$row.end_date|crmDate}</td>
              <!--<td>{$row.standard_price}</td>
              <td>{$row.vat_rate}</td>-->
              <td>
                  <a href="{crmURL p="civicrm/recurring/add" q="action=update&id=$id&cid=$contactId&reset=1"}">Edit</a>
                  <!--| <a href="{crmURL p="civicrm/package/add" q="action=delete&id=$id&reset=1"}">Delete</a>-->
              </td>
            </tr>
           {/foreach}
           </table>
          {else}
           <div class="messages status">
                    <div class="icon inform-icon"></div>
                    {ts}No recurring payments have been setup for this contact.{/ts}
            </div>
          {/if}
        <!-- Display recurring contribution section -->

