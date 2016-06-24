<% require css(silverstripe-data-object-version-viewer/css/history.css) %>
<% require javascript(silverstripe-data-object-version-viewer/javascript/history.js) %>

<div class="history-panel">
<h3>History</h3>

<table class="history-list">

<thead>
<tr>
<th>Ver</th><th>Status</th><th>Published by</th><th>Author</th><th>Last edited</th><th>Select</th>
</tr>
</thead>

<tbody>
<% loop $historyList %>
<tr data-vid="$version"<% if $is_selected %>class="selected"<% end_if %>>
<td>$version</td> <td>$published_status</td> <td>$published_by</td> <td>$authored_by</td> <td>$last_edit_dt.NiceUS $last_edit_dt.Time</td>
<td><% if not $is_selected %><a class="select-version" href="">Select</a><% end_if %></td>
</tr>
<% end_loop %>
</tbody>

</table>
</div>
<input id="vid" name="vid" type="hidden">
