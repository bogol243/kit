<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            {{for $items as $item}}
            {{if $item.items}}
            <li>
            <a href="{{$item.url}}"><i class="fa fa-{{$item.icon}} fa-fw"></i> {{$item.name}}<span class="fa arrow"></span></a>
            <ul class="nav nav-second-level collapse in" aria-expanded="true" style="">
                {{for $item.items as $sub_item}}
                <li>
                    <a href="{{$sub_item.url}}">{{if $sub_item.icon}}<i class="fa fa-{{$sub_item.icon}} fa-fw"></i> {{/if}}{{$sub_item.name}}</a>
                </li>
                {{/for}}
            </ul>
            </li>
            {{else}}
            <li>
                <a href="{{$item.url}}"><i class="fa fa-{{$item.icon}} fa-fw"></i> {{$item.name}}</a>
            </li>
            {{/if}}
            {{/for}}
        </ul>
    </div>
</div>