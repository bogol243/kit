{{if $table.filter}}
<!--    Begin filter section-->
<div class="well text-center filter-form">
    <form class="form form-inline" action="/" id="table_form">
        <input type="hidden" name="view" value="{{$table.view}}">
        <input type="hidden" id="table_action" name="action" value="search">
        <input type="hidden" id="table_row" name="row_id" value="0">
        <input type="hidden" id="page_number" name="page_number" value="1">
        <label for="input_search">Найти</label>
        <input type="text" id="input_search" name="search_string" value="{{$table.search_string}}">
        <label for="input_order">Упорядочить по</label>
        <select name="sort_column">
            {{for $table.columns as $col}}
            {{if $col.sortable}}
            <option value="{{$col.name}}"{{if $table.sort_column === $col.name}} selected{{/if}}>{{$col.title}}</option>
            {{/if}}
            {{/for}}
        </select>
        <select name="sort_dir" class="" id="input_order">
            <option value="asc" {{if $table.sort_dir == "asc"}}selected{{/if}}>по возрастанию</option>
            <option value="desc" {{if $table.sort_dir == "desc"}}selected{{/if}}>по убыванию</option>
        </select>
        <label for="page_limit">Выдавать по</label>
        <select name="page_limit" class="" id="page_limit">
            <option value="20" {{if $table.page_limit == "20"}}selected{{/if}}>20</option>
            <option value="50" {{if $table.page_limit == "50"}}selected{{/if}}>50</option>
            <option value="100" {{if $table.page_limit == "100"}}selected{{/if}}>100</option>
            <option value="500" {{if $table.page_limit == "500"}}selected{{/if}}>500</option>
            <option value="1000" {{if $table.page_limit == "1000"}}selected{{/if}}>1000</option>
        </select>
        <input type="submit" value="Поиск" class="btn btn-primary">
    </form>
</div>
<!--   Filter section end-->
<hr/>
{{/if}}

<table class="table table-striped table-bordered table-condensed">
    {{for $table.groups as $group}}
    {{if $group.display}}
    <tr>
        <td colspan="{{$table.columns_visible}}"><h3>{{$group.name}}</h3></td>
    </tr>
    {{/if}}
    <tr>
        {{for $table.columns as $col}}
        {{if !$col.hidden && $col.name != $table.group_column}}
        <th class="header text-center" style="{{$col.header_style}}">{{$col.title}}</th>
        {{/if}}
        {{/for}}
        {{if $table.actions}}
        <th class="header text-center" style="width: 10px;">Действия</th>
        {{/if}}
    </tr>
    {{if $table.items}}
    {{for $table.items as $item}}
    {{if $item._group == $group.id}}
    <tr>
        {{for $table.columns as $col}}
        {{for $item as $cc => $cell}}
        {{if $cc == $col.name && !$col.hidden && $col.name != $table.group_column}}
        <td{{if $col.nowrap}} nowrap{{/if}}{{if $col.style}} style="{{$col.style}}"{{/if}}>{{$cell}}</td>
        {{/if}}
        {{/for}}
        {{/for}}
        {{if $table.actions}}
        <td nowrap style="text-align: center;">
        {{for $table.actions as $key => $act}}
            {{if $act.control}}
                {{include $act.control add $item as "item"}}
            {{else}}
                {{if $act.href}}
                <a title="{{$act.title}}" {{if $act.target}}target="{{$act.target}}"{{/if}} href="{{$act.href}}&{{$act.id}}={{$item.id}}" class="btn btn-primary"><span class="fa fa-{{$act.icon}}"></span></a>
                {{else}}
                <a title="{{$act.title}}" href="javascript:void();" action="{{$act.name}}" rowid="{{$item.id}}" onclick="javascript:exec_action(this);" class="btn btn-primary"><span class="fa fa-{{$act.icon}}"></span></a>
                {{/if}}
            {{/if}}
        {{/for}}
        </td>
        {{/if}}
    </tr>
    {{/if}}
    {{/for}}
    {{else}}
    <tr>
        <td colspan="{{$table.columns_visible}}" class="text-center">Нет записей для отображения</td>
    </tr>
    {{/if}}
    {{/for}}
</table>

<div class="text-center">
    {{if $table.page_count > 1}}
    <ul class="pagination text-center">
        {{for 1..$table.page_count as $n}}
        <li {{if $n == $table.page_number}}class="active"{{/if}}><a href="javascript:goPage({{$n}});">{{$n}}</a></li>
        {{/for}}
    </ul>
    {{/if}}
</div>

<script type="text/javascript">
    function exec_action(btn) {
        $('#table_action').prop('value', $(btn).attr('action'));
        $('#table_row').prop('value', $(btn).attr('rowid'));
        $('#table_form').submit();
    }

    function goPage(n) {
        $('#page_number').prop('value', n);
        $('#table_form').submit();
    }
    /*
    $(document).ready(function () {
        $('.delete_btn').click(function () {
            var r = confirm("Are you sure?")
            if (r == true) {
                return true;
            } else {
                return false;
            }
        });
    });
    */
</script>
