<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.1
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
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
    <title>تسجيل الدخول</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="Preview page of Metronic Admin RTL Theme #3 for " name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    {{--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />--}}
    <link href="{{url('/')}}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/bootstrap-switch/css/bootstrap-switch-rtl.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{url('/')}}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{url('/')}}/assets/global/css/components-md-rtl.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/css/plugins-md-rtl.min.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{url('/')}}/assets/pages/css/login-2-rtl.min.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->

<body class=" login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="javascript:;">

        <img src="{{url('/')}}/assets/pages/img/ammem.png" alt="" width="15%"/> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    {{--<form class="login-form" action="index.blade.php" method="post">--}}
    {!! Form::open(['method'=>'POST','url'=>url('login'),'class'=>"mobile-form"]) !!}
    <h3 class="form-title">تسجيل الدخول</h3>
    <div class="alert alert-danger display-hide">
        <button class="close" data-close="alert"></button>
        <span> ادخل رقم الهاتف الخاص بك. </span>
    </div>
    <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">رقم الهاتف</label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off"
                   placeholder="رقم الهاتف"
                   name="mobile" id="mobile" value="{{ old('mobile') }}"/></div>
        @if ($errors->has('mobile'))
            <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
        @endif
    </div>

    <div class="form-actions">
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn green pull-right confirm-activation-code" style="width: 100%"> تسجيل الدخول</button>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn default pull-left sign-up" style="width: 100%"> تسجيل مستخدم جديد</button>
            </div>
        </div>
    </div>

    {{--<div class="form-actions">--}}
    {{--</div>--}}
    {{--<div class="form-actions">--}}
    {{--</div>--}}

{{--<div class="forget-password">--}}
{{--<h4>نسيت كلمة المرور؟</h4>--}}
{{--<p> لا تقلق, انقر--}}
{{--<a href="javascript:;" id="forget-password"> هنا </a> لاسترجاع كلمة المرور </p>--}}
{{--</div>--}}
{!! Form::close() !!}
{{--</form>--}}
<!-- END LOGIN FORM -->
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <form class="activation-code-form login-form" id="activation-code-form" action="{{url(user_vw().'/login')}}"
          method="post"
          style="display: none">
        {{csrf_field()}}
        <h3>تاكيد رمز التحقق</h3>
        <p> ادخل رمز التحقق لدخول الي الحساب الخاص بك. </p>

        <input class="form-control mobileNo placeholder-no-fix" type="hidden" autocomplete="off"
               placeholder="رقم الهاتف"
               name="mobile"/>

        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="رمز التحقق"
                       name="activation_code"/></div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn red back-btn">عودة</button>

            <button type="submit" class="btn green pull-right"> تاكيد</button>
        </div>
        <div class="text-center">

            @if(!session()->has('is_sent_activation_code'))
                <a href="javascript:;" class="send-activation btn-link" style="color: darkred;">ارسال رمز التحقق؟</a>
            @endif
        </div>
    </form>
    <!-- END FORGOT PASSWORD FORM -->
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <form class="activation-code-form sign-up-form" id="sign-up-form" action="{{url(user_vw().'/sign-up')}}"
          method="post"
          style="display: none">
        {{csrf_field()}}
        <h3>تسجيل مستخدم جديد</h3>
        <p> سيتم ارسال كود التحقق الخاص بك على هاتفك. </p>

        {{--'country' => 'required',--}}
        {{--'mobile' => 'required|unique:users,mobile',--}}
        {{--'email' => 'required|email|unique:users,email',--}}
        {{--'name' => 'required',--}}
        {{--'region' => 'required',--}}
        {{--'activity_id' => 'required|exists:activities,id',--}}
        {{--'organization_id' => 'required|exists:organizations,id',--}}
        {{--'interest_id' => 'required|exists:interests,id',--}}
        {{--'gender' => 'required|in:male,female',--}}
        {{--'job_id' => 'required|exists:jobs,id',--}}
        <div class="alert alert-danger" role="alert" style="display: none;">
            <ul class="error_list"></ul>
        </div>

        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-user"></i>
                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="اسم المستخدم"
                       name="name"/></div>
        </div>

        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-phone"></i>
                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="00966"
                       name="mobile"/></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input class="form-control placeholder-no-fix" type="text" autocomplete="off"
                       placeholder="البريد الالكتروني"
                       name="email"/></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <select class="form-control edited " id="gender" name="gender">
                    <option value="" disabled selected>اختيار الجنس</option>

                    <option value="male">ذكر
                    </option>
                    <option value="female">انثى
                    </option>
                </select></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-flag"></i>
                <input id="country" class="form-control placeholder-no-fix" type="text" autocomplete="off"
                       placeholder="الدولة"
                       name="country"/></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-globe"></i>
                <input id="region" class="form-control placeholder-no-fix" type="text" autocomplete="off"
                       placeholder="المنطقة"
                       name="region"/></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <select class="form-control edited " id="activity_id" name="activity_id">
                    <option value="" disabled selected>اختيار النشاط</option>
                    @foreach($activities as $activity)
                        <option value="{{$activity->id}}">{{$activity->name}}</option>
                    @endforeach
                </select></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <select class="form-control edited " id="interest_id" name="interest_id">
                    <option value="" disabled selected>اختيار الاهتمامات</option>
                    @foreach($interests as $interest)
                        <option value="{{$interest->id}}">{{$interest->name}}</option>
                    @endforeach

                </select></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <select class="form-control edited " id="job_id" name="job_id">
                    <option value="" disabled selected>اختيار الوظيفة</option>
                    @foreach($jobs as $job)
                        <option value="{{$job->id}}">{{$job->name}}</option>
                    @endforeach
                </select></div>
        </div>
        <div class="form-group">
            <div class="input-icon">
                <select class="form-control edited " id="organization_id" name="organization_id">
                    <option value="" disabled selected>اختيار الجهة</option>
                    @foreach($organizations as $organization)
                        <option value="{{$organization->id}}">{{$organization->name}}</option>
                    @endforeach
                </select></div>
        </div>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn green pull-right" style="width: 100%"><i class="fa fa-check"></i> تسجيل</button>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn red back-btn" style="width: 100%"> <i class="fa fa-reply"></i>عودة</button>
                </div>
            </div>
        </div>
    </form>
    <!-- END FORGOT PASSWORD FORM -->

</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="row">
    <div class="col-md-12 copyright">2018 © 3mmem.com</div>
</div>
{{--<div class="copyright"> 2018 © 3mmem.com</div>--}}
<!-- END COPYRIGHT -->

<!-- BEGIN CORE PLUGINS -->
<script src="{{url('/')}}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
        type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{url('/')}}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/jquery-validation/js/additional-methods.min.js"
        type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{url('/')}}/assets/pages/scripts/login-2.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script src="{{url('/')}}/assets/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>

<link href="{{url('/')}}/assets/fonts/droid-arabic-kufi.css" rel="stylesheet" type="text/css"/>

<style>
    body, h3, h4 {
        font-family: 'DroidArabicKufiRegular' !important;
        font-weight: normal;
        font-style: normal;
    }

    .help-block {
        font-size: 12px !important;
    }
</style>
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->

<script>
    $(document).ready(function () {
        $(document).on('click', '.confirm-activation-code', function () {

            if ($('#mobile').val().length === 0) {
                alert('add mobile number');
                return;
            }

            $('.mobileNo').val($('#mobile').val());
            $('.activation-code-form').show();
            $('.mobile-form').hide();
            $('.sign-up-form').hide();
        });
        $(document).on('click', '.sign-up', function () {

            $('.sign-up-form').show();
            $('.mobile-form').hide();
//            $('.activation-code-form').hide();
        });
        $(document).on('click', '.back-btn', function () {
            $('.activation-code-form').hide();
            $('.mobile-form').show();
        });

        $(document).on('submit', '#activation-code-form', function () {

            var action = $(this).attr('action');
            var dataForm = $(this).serializeArray();
            $.ajax({
                url: action,
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                headers: {
                    'Accept-Language': 'ar'
                },
                success: function (data) {

                    if (data.status) {
                        $(this).hide();
                    }
                }
            });
        });
        $(document).on('submit', '#sign-up-form', function (e) {
            e.preventDefault();
            var _this = $(this);
            var action = $(this).attr('action');
            var dataForm = $(this).serializeArray();
            $.ajax({
                url: action,
                type: 'POST',
                dataType: 'json',
                data: dataForm,
                headers: {
                    'Accept-Language': 'ar'
                },
                success: function (data) {

                    if (data.status) {

//                        toastr["success"](data.message);
                        $('#sign-up-form').hide();
                        $('.activation-code-form').show();
                        $('.mobileNo').val($('.mobile').val());

                        $('.alert-danger').hide();


                    } else {
//                        toastr["error"](data.message);

                        var error = '';
                        $.each(data.errors, function (i, v) {

                            error += '<li>' + v.message + '</li>';
                        });

                        $('.alert-danger').find('.error_list').html(error);

                        $('.alert-danger').show();

                    }
                }
            });
        });
        $(document).on('click', '.send-activation', function () {
            $.ajax({
                url: '{{url(user_vw().'/send-activation')}}',
                type: 'POST',
                dataType: 'json',
                data: {'mobile': $('.mobileNo').val(), '_token': '{{csrf_token()}}'},
                headers: {
                    'Accept-Language': 'ar'
                },
                success: function (data) {

                    if (data.status) {
                        $(this).hide();
                    }
                }
            });
        });
    });

    var placeSearch, country;

    var componentForm = {
        street_number: 'short_name'
    };

    function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        country = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('country')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        country.addListener('place_changed', fillInAddress);
    }


    function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = country.getPlace();

        for (var component in componentForm) {
            document.getElementById(component).value = '';
            document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
            }
        }
    }

    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }


</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWvg7pVxiwybFKR89SiHPCIXGdgG808FU&libraries=places&callback=initAutocomplete"
        async defer></script>
</body>

</html>