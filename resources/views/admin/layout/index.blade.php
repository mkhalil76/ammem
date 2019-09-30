<!DOCTYPE html>

<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" dir="rtl">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8"/>
    <title>لوحة التحكم - {{$title or 'عمم'}}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="Preview page of Metronic Admin RTL Theme #2 for statistics, charts, recent events and reports"
          name="description"/>
    <meta content="" name="author"/>
    @include(admin_layout_vw().'.css')
    <script src="{{url('/')}}/assets/apps/scripts/pusher.min.js"></script>

    <script>
        var baseURL = '{{url("/")}}';
    </script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-md">
<!-- BEGIN HEADER -->
@include(admin_layout_vw().'.header')

<!-- END HEADER -->
<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"></div>
<!-- END HEADER & CONTENT DIVIDER -->
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->

    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
@include(admin_layout_vw().'.sidebar')
<!-- END SIDEBAR -->
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->

            {{--<h1 class="page-title"> Admin Dashboard 2--}}
            {{--<small>statistics, charts, recent events and reports</small>--}}
            {{--</h1>--}}
            <div class="page-bar">
                @include(admin_layout_vw().'.breadcrumb')

            </div>
            <!-- END PAGE HEADER-->
            @yield('content')
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->
</div>
<div id="results-modals"></div>

<div id="wait_msg">
    العملية قيد التنفيذ ... الرجاء الانتظار
</div>
<div id="overlay">
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
@include(admin_layout_vw().'.footer')


<!-- END FOOTER -->
@include(admin_layout_vw().'.js')
{{--<script src="https://js.pusher.com/4.1/pusher.min.js"></script>--}}

@if(isset($my_groups))

    @foreach($my_groups as $my_group)
        <input type="hidden" name="channel[]" class="channels" value="{{$my_group->slug}}">
    @endforeach
    <input type="hidden" name="auth_user_id" class="auth_user_id" value="{{auth()->user()->id}}">
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var auth_user_id = $('.auth_user_id').val();
        var pusher = new Pusher('6905ea279b00cd9932b7', {
            encrypted: true
        });

        $('input[name^="channel"]').each(function () {

            console.log($(this).val());
            var channel = pusher.subscribe('my-channel' + $(this).val());
            channel.bind('my-event', function (data) {

                if (data.type === 1) {
                    $('.mt-messages').prepend('<a href="{{url(user_vw().'/conversation/')}}/' + data.group_id + '">\n' +
                        '    <div class="mt-comment">\n' +
                        '\n' +
                        '        <div class="mt-comment-img">\n' +
                        '            <img src="' + data.sender_photo + '" width="100%"\n' +
                        '                 onerror="this.src=\'{{url('/')}}/assets/layouts/layout2/img/avatar1.jpg\'"/>\n' +
                        '        </div>\n' +
                        '        <div class="mt-comment-body">\n' +
                        '            <div class="mt-comment-info">\n' +
                        '                <span class="mt-comment-author">' + data.sender_name + '</span>\n' +
                        '                <span class="mt-comment-date">' + data.created_date + '</span>\n' +
                        '            </div>\n' +
                        '            <div class="mt-comment-text">\n' +
                        '                 ' + data.title + '<br>\n' + data.message +
                        '            </div>\n' +
                        '\n' +
                        '        </div>\n' +
                        '    </div>\n' +
                        '</a>');
                }

                if (jQuery.inArray(auth_user_id, data.user_message) !== -1) {
                    if (Notification.permission !== 'default' && data.sender_id != auth_user_id) {
                        notify = new Notification('تطبيق عمم -' + data.sender_name + ' - ' + data.group_name, {
                            body: data.message
                            {{--icon:'{{url('/')}}/assets/pages/img/ammem.png'--}}
                        });
                    }
                }


            });
        });
        //                console.log(data);
        //            var user_image = '/assets/layouts/layout2/img/avatar2.jpg';
        if (!window.Notification) {
            alert('Sorry');
        } else {

            var notify;
            Notification.requestPermission(function (p) {

                if (p === 'denied') {
                    console.log('Notification denied');
                } else if (p === 'granted') {
                    if (Notification.permission === 'default') {
                        alert('Please allow notifications before doing this.');
                    }

                }
            });
        }
        //            });
        //        });
    </script>
@endif
</body>

</html>