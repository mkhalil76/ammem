@extends(admin_layout_vw().'.index')

@section('css')
    <link href="{{url('/')}}/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
          type="text/css"/>

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

                        {!! Form::open(['files'=>true,'method'=>'POST','url'=>url(user_vw().'/contacts'),'id'=>'upload-contacts']) !!}
                        <div class="fileinput fileinput-new" data-provides="fileinput">

                                                            <span class="btn blue btn-file">
                                                                <i class="fa fa-file"></i>
                                                                <span class="fileinput-new"> اختيار الملف </span>
                                                                <span class="fileinput-exists"> تغير </span>
                                                                <input type="file" name="contacts" class="contacts"
                                                                       accept=".xls,.xlsx"> </span>
                            <span class="fileinput-filename"> </span> &nbsp;
                            <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput">

                            </a>
                            <button type="submit"
                                    class="btn btn-circle  btn-success upload_excel"
                                    style="display: none;">
                                <i class="fa fa-upload"></i>استيراد جهات اتصال
                            </button>
                            <a href="{{url('assets/upload/excel.xlsx')}}" style="font-size: small">نموذج</a>
                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>

                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_comments_1">
                            <!-- BEGIN: Comments -->
                            <div class="mt-comments mt-contacts">
                                @foreach($my_contacts as $contact)
                                    <a href="#">
                                        <div class="mt-comment"
                                             @if($contact->is_exist == 1) style="background-color: #17C4BB"
                                             @else style="background-color: #AD1457" @endif>

                                            <div class="mt-comment-img">
                                                <img src="" width="100%"
                                                     onerror="this.src='{{url('/')}}/assets/layouts/layout2/img/avatar1.jpg'"/>
                                            </div>
                                            <div class="mt-comment-body">
                                                <div class="mt-comment-info">
                                                    <span class="mt-comment-author"
                                                          style="color: #ffffff">{{$contact->name}}</span>
                                                    <span class="mt-comment-date"></span>
                                                </div>
                                                <div class="mt-comment-text" style="color: #ffffff">
                                                    {{$contact->mobile.' - '.$contact->email}}
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
    <script src="{{url('/')}}/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
            type="text/javascript"></script>

    <script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    @push('js')

        <script>

            $(document).ready(function () {

                $('input[type="file"]').change(function () {
                    if ($(this).val().length === 0)
                        $('.upload_excel').hide();
                    else
                        $('.upload_excel').show();

                    console.log($(this).val());
                });
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

                $(document).on('submit', '#upload-contacts', function (e) {
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
                                var contacts = '';
                                $.each(response.items, function (i, v) {

                                    var style = '';
                                    if (v.is_exist == 1) {
                                        style = 'style="background-color: #17C4BB"';

                                    } else {
                                        style = 'style="background-color: #AD1457"';

                                    }

                                    $('.mt-contacts').prepend('<a href="#">\n' +
                                        '    <div class="mt-comment" ' + style + '>\n' +
                                        '\n' +
                                        '        <div class="mt-comment-img">\n' +
                                        '            <img src="" width="100%"\n' +
                                        '                 onerror="this.src=\'{{url('/')}}/assets/layouts/layout2/img/avatar1.jpg\'"/>\n' +
                                        '        </div>\n' +
                                        '        <div class="mt-comment-body">\n' +
                                        '            <div class="mt-comment-info">\n' +
                                        '                <span class="mt-comment-author">' + v.name + '</span>\n' +
                                        '                <span class="mt-comment-date"></span>\n' +
                                        '            </div>\n' +
                                        '            <div class="mt-comment-text">\n' +
                                            {{--{{$contact->mobile.' - '.$contact->email}}--}}
                                                v.mobile + ' - ' + v.email +
                                        '            </div>\n' +
                                        '\n' +
                                        '        </div>\n' +
                                        '    </div>\n' +
                                        '</a>'
                                    )
                                    ;
                                });
                            } else {
                                toastr["error"](response.message);
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