<label class="col-md-3 control-label">{{$data.title}}</label>
<div class="col-md-5">
    {{for $data.items as $val => $opt}}
    <div class="radio">
        <label>
            <input type="radio" name="{{$data.name}}" value="{{$val}}"{{if $data.required}} required{{/if}}{{if $data.value == $val}} checked="yes"{{/if}}/> {{$opt}}
        </label>
    </div>
    {{/for}}
</div>