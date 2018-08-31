<label class="col-md-3 control-label">{{$data.title}}</label>
<div class="col-md-5 selectContainer">
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-{{$data.icon}}"></i></span>
        <select name="{{$data.name}}" class="form-control selectpicker"{{if $data.required}} required{{/if}}>
            {{for $data.items as $val => $opt}}
            <option value="{{$val}}"{{if $data.value == $val}} selected{{/if}}>{{$opt}}</option>
            {{/for}}
        </select>
    </div>
</div>