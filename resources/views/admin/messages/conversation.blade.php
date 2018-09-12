@extends(admin_layout_vw().'.index')

@section('css')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bubble font-hide hide"></i>
                        <span class="caption-subject font-hide bold uppercase">{{$title}} - {{$group->name}}</span>
                    </div>
                    {{--<div class="actions">--}}
                    {{--<div class="portlet-input input-inline">--}}
                    {{--<div class="input-icon right">--}}
                    {{--<i class="icon-magnifier"></i>--}}
                    {{--<input type="text" class="form-control input-circle" placeholder="search..."></div>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    @if($group->user_id == auth()->user()->id)
                        <div class="actions">
                            <a href="{{url(user_vw().'/send-message/'.$group->id)}}"
                               class="btn btn-circle  btn-success send-new-message">
                                <i class="fa fa-plus"></i> انشاء تعميم
                            </a>
                        </div>
                    @endif
                </div>
                <div class="portlet-body" id="chats">
                    <div class="scroller" style="height: 525px;" data-always-visible="1" data-rail-visible1="1">
                        <ul class="chats">

                            @if(isset($messages) && count($messages) > 0)
                                @foreach($messages as $message)

                                    <li class="in">
                                        <img class="avatar" alt="" src="{{$message->user->photo->name or ''}}"
                                             onerror="this.src='{{url('/')}}/assets/site/layouts/layout2/img/avatar1.jpg'"/>
                                        <div class="message">
                                            <span class="arrow"> </span>
                                            <a href="javascript:;"
                                               class="name"> {{$message->user->name}} </a>
                                            <span class="datetime">{{$message->created_date}}</span>
                                            <span class="body">
                                    {{$message->title}}<br>
                                                {{$message->text}} <br>
                                    </span>
                                        </div>
                                    </li>

                                    @foreach($message->Replies as $reply)
                                        <li class="out">
                                            <img class="avatar" alt="" src="{{$reply->user->photo->name or ''}}"
                                                 onerror="this.src='{{url('/')}}/assets/site/layouts/layout2/img/avatar2.jpg'"/>
                                            <div class="message">
                                                <span class="arrow"> </span>
                                                <a href="javascript:;" class="name"> {{$reply->user->name}} </a>
                                                <span class="datetime"> {{$reply->created_date}} </span>
                                                <span class="body"> {{$reply->text}} </span>
                                            </div>
                                        </li>
                                    @endforeach

                                @endforeach
                            @endif
                        </ul>
                    </div>


                    <div class="chat-form reply"
                         @if(count($messages) > 0 && $messages[count($messages) - 1]->is_reply == 1) style="display: block;"
                         @else style="display: none;" @endif>
                        <div class="input-cont">
                            <input class="form-control" type="text"
                                   placeholder="اكتب ردك هنا..."

                                   name="message"
                                   id="message"/></div>


                        <div class="btn-cont send-reply-btn">
                            @if(count($messages) > 0)
                                <span class="arrow"> </span>
                                <a href="javascript:;"
                                   pull-link="{{url(user_vw().'/send-reply/'.$messages[0]->id)}}"
                                   class="btn blue icn-only send-reply">
                                    <i class="fa fa-check icon-white"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <!-- END PORTLET-->
        </div>
    </div>

    <input type="hidden" id="auth_id" value="{{auth()->user()->id}}"/>

@endsection
@section('js')
    <script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>

    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('6905ea279b00cd9932b7', {
            encrypted: true
        });

        var channel = pusher.subscribe('my-channel{{$group->slug}}');
        channel.bind('my-event', function (data) {
//            var user_image = '/site/layouts/layout2/img/avatar2.jpg';

            if (data.type === 1) {

                var user_image = "'" + '{{url('/')}}' + "/site/layouts/layout2/img/avatar1.jpg\'";

                $('.chats').prepend('<li class="in">' +
                    '<img class="avatar" alt="" src="' + data.sender_photo + '" onerror="this.src=' + user_image + '" /><div class="message"><span class="arrow"> </span>' +
                    '<a href="javascript:;" class="name">' + data.sender_name + '</a>' +
                    '<span class="datetime"> ' + data.created_date + '</span>' +
                    '<span class="body">' + data.message + ' <br> ' + data.title + ' </span>' +
                    '</div></li>');
                if (data.is_reply === 1) {
                    $('.reply').hide();
                } else {
                    $('.reply').show();

                    $('.send-reply-btn').html('<span class="arrow"> </span><a href="javascript:;" pull-link="" class="btn blue icn-only send-reply"><i class="fa fa-check icon-white"></i></a>');
                    $('.send-reply').attr('pull-link', '{{url(user_vw().'/send-reply/')}}/' + data.message_id);

                }

            } else {
                var user_image = "'" + '{{url('/')}}' + "/site/layouts/layout2/img/avatar2.jpg\'";
                $('.chats li:nth-child(2)').prepend('<li class="out">' +
                    '<img class="avatar" alt="" src="' + data.sender_photo + '" onerror="this.src=' + user_image + '" /><div class="message"><span class="arrow"> </span>' +
                    '<a href="javascript:;" class="name">' + data.sender_name + '</a>' +
                    '<span class="datetime"> ' + data.created_date + '</span>' +
                    '<span class="body">' + data.message + '</span>' +
                    '</div></li>');
            }

        });

        $(document).ready(function () {

            $(document).on('click', '.send-new-message', function (e) {

                e.preventDefault();
                var action = $(this).attr('href');
                $.ajax({
                    url: action,
                    type: 'GET',
                    headers: {
                        'Accept-Language': 'ar'
                    },
                    success: function (data) {
                        $('#results-modals').html(data);
                        $('#addNewMessage').modal('show');
                    }
                });
            });
            $(document).on('submit', '#formAdd', function (e) {
                e.preventDefault();

                var formData = new FormData($(this)[0]);

                var action = $(this).attr('action');
                var method = $(this).attr('method');
                $("#wait_msg,#overlay").show();

                $.ajax({
                    url: action,
                    type: method,
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'Accept-Language': 'ar'
                    },
                    success: function (response) {
                        $("#wait_msg,#overlay").hide();
                        if (response.status) {
                            toastr["success"](response.message);
                            $('#addGroup').modal('hide');

                        } else {
                            toastr["error"](response.message);
                        }
                    }
                });
            });
            $(document).on('click', '.send-reply', function () {

                var action = $(this).attr('pull-link');
                var message = $('#message').val();
                $.ajax({
                    url: action,
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'Accept-Language': 'ar'
                    },
                    data: {_token: '{{csrf_token()}}', message: message},
                    success: function (data) {
//console.log(data);
                        if (data.status) {
                            $('#message').val('');
                        }
                    }
                })
            });
        });
    </script>
@stop