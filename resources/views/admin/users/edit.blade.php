@extends(admin_layout_vw().'.index')

@section('css')

    <link href="{{url('/')}}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/css/components-md-rtl.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/css/plugins-md-rtl.min.css" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <span class="caption-subject bold uppercase">تعديل المستخدم</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    {!! Form::open(['method'=>'PUT','id'=>'user-edit-frm']) !!}
                    <div class="form-body">
                        @include(admin_vw().'.alert')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input has-success form-md-floating-label">
                                    <div class="input-icon">
                                        <input type="text" class="form-control" name="name" id="name"
                                               value="{{$user->name or ''}}">
                                        <label for="form_control_1">اسم المستخدم</label>
                                        <span class="help-block">ادخل اسم المستخدم هنا...</span>
                                        <i class="fa fa-bell-o"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input has-success form-md-floating-label">
                                    <div class="input-icon">
                                        <input type="text" class="form-control" name="mobile" id="mobile"
                                               value="{{$user->mobile or ''}}">
                                        <label for="form_control_1">رقم الهاتف</label>
                                        <span class="help-block">ادخل رقم الهاتف هنا...</span>
                                        <i class="fa fa-bell-o"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input has-success form-md-floating-label">
                                    <div class="input-icon">
                                        <input type="email" class="form-control" name="email" id="email"
                                               value="{{$user->email or ''}}">
                                        <label for="form_control_1">البريد الالكتروني</label>
                                        <span class="help-block">ادخل البريد الالكتروني هنا...</span>
                                        <i class="fa fa-bell-o"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input form-md-floating-label has-success">
                                    <select class="form-control edited " id="gender" name="gender">
                                        <option value="male" @if($user->gender == 'male') selected @endif>ذكر
                                        </option>
                                        <option value="female" @if($user->gender == 'female') selected @endif>انثى
                                        </option>
                                    </select>
                                    <label for="form_control_1">الجنس</label>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group form-md-line-input has-success form-md-floating-label">
                                    <div class="input-icon">
                                        <input type="text" class="form-control" name="region" id="region"
                                               value="{{$user->region or ''}}">
                                        <label for="form_control_1">المنطقة</label>
                                        <span class="help-block">ادخل المنطقة هنا...</span>
                                        <i class="fa fa-bell-o"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input has-success form-md-floating-label">
                                    <div class="input-icon">
                                        <input type="text" class="form-control" name="country" id="country"
                                               value="{{$user->country or ''}}">
                                        <label for="form_control_1">الدولة</label>
                                        <span class="help-block">ادخل الدولة هنا...</span>
                                        <i class="fa fa-bell-o"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">

                                <div class="form-group form-md-line-input form-md-floating-label has-success">
                                    <select class="form-control edited " id="activity_id" name="activity_id">
                                        <option></option>
                                        @foreach($activities as $activity)
                                            <option value="{{$activity->id}}"
                                                    @if($user->activity_id == $activity->id) selected @endif>{{$activity->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="form_control_1">مجال النشاط</label>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input form-md-floating-label has-success">
                                    <select class="form-control edited " id="organization_id" name="organization_id">
                                        <option value=""></option>
                                        @foreach($organizations as $organization)
                                            <option value="{{$organization->id}}"
                                                    @if($user->organization_id == $organization->id) selected @endif>{{$organization->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="form_control_1">الجهة</label>

                                </div>
                            </div>
                            <div class="col-md-4">

                                <div class="form-group form-md-line-input form-md-floating-label has-success">

                                    <select class="form-control edited " id="interest_id" name="interest_id">
                                        <option></option>
                                        @foreach($interests as $interest)
                                            <option value="{{$interest->id}}"
                                                    @if($user->interest_id == $interest->id) selected @endif>{{$interest->name}}</option>
                                        @endforeach

                                    </select>
                                    <label for="form_control_1">الاهتمامات</label>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input form-md-floating-label has-success">

                                    <select class="form-control edited " id="job_id" name="job_id">
                                        <option></option>
                                        @foreach($jobs as $job)
                                            <option value="{{$job->id}}"
                                                    @if($user->job_id == $job->id) selected @endif>{{$job->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="form_control_1">الوظيفة</label>

                                </div>
                            </div>

                            @if($user->type == 'admin')
                            <div class="col-md-4">

                                <div class="form-group form-md-line-input form-md-floating-label has-success">
                                    <select class="form-control edited " id="type" name="type">
                                        <option value="admin" @if($user->type == 'admin') selected @endif>مدير
                                            النظام
                                        </option>
                                        <option value="user" @if($user->type == 'user') selected @endif>مستخدم
                                        </option>
                                    </select>
                                    <label for="form_control_1">نوع المستخدم</label>

                                </div>
                            </div>
                            @endif
                            <div class="col-md-4">

                                <div class="form-group form-md-line-input form-md-floating-label has-success">
                                    <select class="form-control edited " id="status" name="status">
                                        <option value="active" @if($user->status == 'active') selected @endif>نشط
                                        </option>
                                        <option value="block" @if($user->status == 'block') selected @endif>محظور
                                        </option>
                                    </select>
                                    <label for="form_control_1">حالة المستخدم</label>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions noborder">
                        <button type="submit" class="btn blue">تعديل المستخدم</button>
                        <a href="{{url(admin_vw().'/users')}}" class="btn default">قائمة المستخدمين</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>

    <style>
        .errorTxt {
            border: 1px solid red;
            min-height: 20px;
        }

    </style>
@endsection
@section('js')


    <script src="{{url('/')}}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js"
            type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{url('/')}}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{url('/')}}/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->

    @push('js')
        <script>

            $(function () {

                var form = $('#user-edit-frm');
                var success = $('.alert-success');
                var error_danger = $('.alert-danger');
                form.submit(function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: $(form).serializeArray(),
                        dataType: 'json',
                        headers: {
                            'Accept-Language': 'ar'
                        },
                        success: function (response) {
                            $("#wait_msg,#overlay").hide();

                            if (response.status) {

                                success.show();
                                error_danger.hide();
                                success.html('<strong>' + response.message + '!</strong>');

                                toastr["success"](response.message);
                            } else {
                                success.hide();
                                error_danger.show();
                                error_danger.html('<strong>' + response.message + '!</strong>');

                                var error = '<ul>';
                                $.each(response.errors, function (i, v) {
                                    error += '<li>' + v.message + '</li>';
                                });
                                error += '</ul>';
                                error_danger.find('strong').append(error);
                                toastr["error"](response.message);
                            }

                            $("html, body").animate({ scrollTop: 0 }, "slow");


                        }, error: function (xhr) {
                            if (xhr.status === 403) {
                                toastr["error"]('{{message_unauthorize()}}');

                            }
                        }
                    });

                });

            });
        </script>
    @endpush
@stop