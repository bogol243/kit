{{include "common.header"}}

{{if $print}}
<h1 class="page-header">{{$title}}</h1>
{{else}}
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-6">
            <h1 class="page-header">{{$title}}</h1>
        </div>
        <div class="col-lg-6" style="">
            <div class="page-action-links text-right">
                {{for $title_buttons as $item}}
                <a href="{{$item.url}}"{{if $item.target}} target="{{$item.target}}"{{/if}}> <button class="btn btn-success{{if $item.icon}} fa fa-{{$item.icon}}{{/if}}"> {{$item.name}}</button></a>
                {{/for}}
            </div>
        </div>
    </div>
{{/if}}