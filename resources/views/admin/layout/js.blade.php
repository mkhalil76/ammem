
<!-- BEGIN CORE PLUGINS -->
<script src="{{url('/')}}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<script src="{{url('/')}}/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
@yield('js')

<script src="{{url('/')}}/assets/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{url('/')}}/assets/layouts/layout2/scripts/layout.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/layouts/layout2/scripts/demo.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->
{!! HTML::script('assets/global/plugins/bootbox/bootbox.min.js') !!}

@stack('js')