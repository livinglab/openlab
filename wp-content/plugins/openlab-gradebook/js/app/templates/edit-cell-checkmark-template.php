<div class="checkbox">
    <label>
        <input class="grade-checkmark" type="checkbox" <% if(cell.get('assign_points_earned') > 59) { %>checked="checked"<% } %> <%= role === 'instructor' ? '' : 'disabled="disabled"' %>>
    </label>
</div>