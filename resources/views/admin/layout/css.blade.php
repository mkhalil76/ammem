<!-- BEGIN GLOBAL MANDATORY STYLES -->
{{--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />--}}
<link href="{{url('/')}}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('/')}}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('/')}}/assets/global/plugins/bootstrap/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('/')}}/assets/global/plugins/bootstrap-switch/css/bootstrap-switch-rtl.min.css" rel="stylesheet" type="text/css" />
<!-- END GLOBAL MANDATORY STYLES -->
<link href="{{url('/')}}/assets/global/plugins/bootstrap-toastr/toastr-rtl.min.css" rel="stylesheet" type="text/css" />

<!-- BEGIN PAGE LEVEL PLUGINS -->
@yield('css')
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL STYLES -->
<link href="{{url('/')}}/assets/global/css/components-md-rtl.min.css" rel="stylesheet" id="style_components" type="text/css" />
<link href="{{url('/')}}/assets/global/css/plugins-md-rtl.min.css" rel="stylesheet" type="text/css" />
<!-- END THEME GLOBAL STYLES -->
<!-- BEGIN THEME LAYOUT STYLES -->
<link href="{{url('/')}}/assets/layouts/layout2/css/layout-rtl.min.css" rel="stylesheet" type="text/css" />
<link href="{{url('/')}}/assets/layouts/layout2/css/themes/blue-rtl.min.css" rel="stylesheet" type="text/css" id="style_color" />
<link href="{{url('/')}}/assets/layouts/layout2/css/custom-rtl.min.css" rel="stylesheet" type="text/css" />
<!-- END THEME LAYOUT STYLES -->
<link rel="shortcut icon" href="favicon.ico" />

<link href="{{url('/')}}/assets/fonts/droid-arabic-kufi.css" rel="stylesheet" type="text/css" />

<style>
    body,li,h1,h2,h3,h4,h5,h6,.select2-selection__rendered{
        font-family: 'DroidArabicKufiRegular' !important;
        font-weight: normal;
        font-style: normal;
    }
    .table {
        text-align: center;
    }
    thead, th {text-align: center;}
    .dataTables_wrapper .dataTables_processing {
        width: 200px;
        display: inline-block;
        padding: 7px;
        right: 50%;
        margin-right: -100px;
        margin-top: 10px;
        text-align: center;

         border: none !important;
         background: none !important;
         vertical-align: middle;
         -webkit-box-shadow: none !important;
        -moz-box-shadow: none !important;
         box-shadow: none !important;
    }

</style>
@stack('css')