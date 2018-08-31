<label class="col-md-3 control-label">{{$data.title}}</label>
<div class="col-md-5 inputGroupContainer">
    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-{{$data.icon}}"></i></span>
        <input type="password" name="{{$data.name}}" placeholder="{{$data.placeholder}}" class="form-control" value="{{$data.value}}"{{if $data.required}} required{{/if}}/>
    </div>
</div>
