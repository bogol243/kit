{{if $form.title}}
<div class="col-lg-12">
    <h2 class="page-header">{{$form.title}}</h2>
</div>
{{/if}}
<form class="{{if $form.well}}well {{/if}}form-horizontal" action="/index.php" method="get" id="{{$form.id}}">
    <input id="{{$form.id}}_action" type="hidden" name="action" value=""/>
    <input id="{{$form.id}}_view" type="hidden" name="view" value="{{$form.view}}"/>
    <input id="{{$form.id}}_id" type="hidden" name="id" value="{{$form.record_id}}"/>
    <fieldset>
        {{for $form.fields as $field}}
        <div class="form-group">
            {{include $field.control add $field as "data"}}
        </div>
        {{/for}}
        <div id="{{$form.id}}_msg" class="alert hidden"></div>
        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-9">
                {{for $form.buttons as $btn}}
                <button onclick="$('#{{$form.id}}_action').val('{{$btn.value}}');" type="{{$btn.type}}" class="btn btn-warning" ><span class="fa fa-{{$btn.icon}}"></span> {{$btn.title}}</button>
                {{/for}}
            </div>
        </div>
    </fieldset>
</form>
<script>
    var tm_{{$form.id}} = null;
    $("#{{$form.id}}").submit(function(event){
        event.preventDefault();
        $("#{{$form.id}}_action").val(event.currentTarget.action.value);
        $.ajax({
            type: "POST",
            url: "/index.php",
            data: $('#{{$form.id}}').serialize(),
            success : function(text){
                var res = eval('res = ' + text);
                if (res.result == "success"){
                    $.each(res.data, function(index, value) {
                        $("#{{$form.id}}_"+index).val(value);
                    });
                    $("#{{$form.id}}_msg").removeClass("alert-danger");
                    $("#{{$form.id}}_msg").addClass("alert-success");
                    $("#{{$form.id}}_msg").text('Данные успешно сохранены');
					if('location' in res) {
						if(res.location != '') {
							window.location.href = res.location;
						}
					}
                } else {
                    $("#{{$form.id}}_msg").addClass("alert-danger");
                    $("#{{$form.id}}_msg").removeClass("alert-success");
                    $("#{{$form.id}}_msg").text('Ошибка при сохранении данных. ' + text);
                }
                $("#{{$form.id}}_msg").removeClass("hidden");
                tm_{{$form.id}} = setTimeout(function () {
                    clearTimeout(tm_{{$form.id}});
                    $("#{{$form.id}}_msg").addClass("hidden");
                }, 5000);
            }
        });
    });
</script>