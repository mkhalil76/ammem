@extends(admin_layout_vw().'.index')

@section('css')

@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-xs-12 col-sm-12">
            <div class="portlet light bordered">
                <div class="portlet-title tabbable-line">
                    <div class="caption">
                        <i class="icon-bubbles font-dark hide"></i>
                        <span class="caption-subject font-dark bold uppercase">{{$title}}</span>

                    </div>
                    <div class="actions">
                        <a href="{{url(user_vw().'/user-group-create')}}" class="btn btn-circle  btn-success add">
                            <i class="fa fa-plus"></i> انشاء مجموعة
                        </a>
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_comments_1">
                            <!-- BEGIN: Comments -->
                            <div class="mt-comments">
                                @foreach($my_groups as $my_group)
                                    <a href="{{url(user_vw().'/conversation/'.$my_group->id)}}">
                                        <div class="mt-comment">

                                            <div class="mt-comment-img">
                                                <img src="{{$my_group->image}}" width="100%"
                                                     onerror="this.src='{{url('/')}}/assets/layouts/layout2/img/avatar1.jpg'"/>
                                            </div>
                                            <div class="mt-comment-body">
                                                <div class="mt-comment-info">
                                                    <span class="mt-comment-author">{{$my_group->admin->name}}</span>
                                                    <span class="mt-comment-date">{{$my_group->created_date}}</span>
                                                </div>
                                                <div class="mt-comment-text">
                                                    {{$my_group->details}}
                                                </div>
                                                <div class="mt-comment-details">
                                                    <span class="mt-comment-status mt-comment-status-pending">{{$my_group->status_group}}</span>
                                                    {{--<ul class="mt-comment-actions">--}}
                                                        {{--<li>--}}
                                                            {{--<a href="#">Quick Edit</a>--}}
                                                        {{--</li>--}}
                                                        {{--<li>--}}
                                                            {{--<a href="#">View</a>--}}
                                                        {{--</li>--}}
                                                        {{--<li>--}}
                                                            {{--<a href="#">Delete</a>--}}
                                                        {{--</li>--}}
                                                    {{--</ul>--}}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <!-- END: Comments -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    @push('js')

        <script>
            $(document).ready(function () {

                $(document).on('click', '.add', function (e) {

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
                            $('#addGroup').modal('show');
                        }
                    });
                });

                $(document).on('submit', '#formEdit', function (e) {
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
                                errors.hide();
                                success.show();
                                success.find('strong').text(response.message);
                                table.ajax.reload();

                            } else {
                                toastr["error"](response.message);

                                errors.find('strong').text(response.message);
//
                                var messages = '<ol>';
                                $.each(response.errors, function (i, v) {

                                    messages += '<li>' + v.message + '</li>';

                                });
                                messages += '</ol>';
                                errors.find('span').html(messages);
                                errors.show();
                                success.hide();

                            }
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
                                toastr["success"](response.message + ' ... انتظر موافقة مدير النظام ');
                                $('#addGroup').modal('hide');

                            } else {
                                toastr["error"](response.message);
                            }
                        }
                    });
                });

            });


        </script>
    @endpush
@stop