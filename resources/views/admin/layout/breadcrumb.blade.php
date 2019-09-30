<ul class="page-breadcrumb">
    <li>
        <i class="icon-home"></i>
        <a href="index.html">{{$title or 'الصفحة الرئيسية'}}</a>
        @if(isset($sub_title))
            <i class="fa fa-angle-right"></i>
        @endif
    </li>
    @if(isset($sub_title))

        <li>
            <span>{!! $sub_title or '' !!}</span>
        </li>
    @endif

</ul>
