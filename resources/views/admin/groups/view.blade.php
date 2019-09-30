@extends(admin_layout_vw().'.index')

@section('css')


    <link href="{{url('/')}}/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css">
    <link href="{{url('/')}}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap-rtl.css"
          rel="stylesheet" type="text/css">


    <link href="{{url('/')}}/assets/global/css/components-md-rtl.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="{{url('/')}}/assets/global/css/plugins-md-rtl.min.css" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bell-o"></i> {{$title}}
                    </div>
                </div>


                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12" align="center">
                                <div class="btn-group">
                                    <a href="{{url(admin_vw().'/group-create')}}" class="btn sbold green add">
                                        <i class="fa fa-plus"></i>

                                        اضافة
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-advance table-hover" id="groups_tbl">
                            <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    الاسم
                                </th>
                                <th>
                                    نوع المجموعة
                                </th>
                                <th>
                                    الاعضاء
                                </th>
                                <th>
                                    الحالة
                                </th>
                                <th>
                                    مدير المجموعة
                                </th>
                                <th>
                                    حالة الصلاحية
                                </th>
                                <th>
                                    المدة الصلاحية (يوم)
                                </th>
                                <th>
                                    العمليات
                                </th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
@endsection
@section('js')

    <script src="{{url('/')}}/assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js"
            type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
            type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"
            type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
            type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
            type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="{{url('/')}}/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>


    @push('js')
        <script>
            {{--SELECT `id`, `name`, `image`, `description`, `type`, `status`, `user_id`, `deleted_at`, `created_at`, `updated_at` FROM `groups` WHERE 1--}}
            $(document).ready(function () {

                var table = $('#groups_tbl').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": baseURL + '/admin/groups-data',
                    columns: [
                        {data: 'num', name: 'num', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'type', name: 'type'},
                        {data: 'members', name: 'members'},
                        {data: 'status', name: 'status'},
                        {data: 'admin', name: 'admin'},
                        {data: 'admin_status', name: 'admin_status'},
                        {data: 'duration', name: 'duration'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    "language": {
                        "sProcessing": "<img src='{{loader_icon()}}' />",
                        "sLengthMenu": "أظهر _MENU_ مدخلات",
                        "sZeroRecords": "لم يعثر على أية سجلات",
                        "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                        "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                        "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                        "sInfoPostFix": "",
                        "sSearch": "ابحث:",
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": "الأول",
                            "sPrevious": "السابق",
                            "sNext": "التالي",
                            "sLast": "الأخير"
                        }
                    }
                });
                var success = $('.alert.success');
                var errors = $('.alert.errors');

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

                $(document).on('click', '.add', function (e) {

                    e.preventDefault();
                    var action = $(this).attr('href');
                    $.ajax({
                        url: action,
                        type: 'GET',
                        success: function (data) {
                            $('#results-modals').html(data);
                            $('#addGroup').modal('show');
                        }
                    });
                });

                $(document).on('change', '.admin_status', function (e) {

                    e.preventDefault();
                    var action = $(this).attr('href');
                    $.ajax({
                        url: action,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{csrf_token()}}',
                            admin_status: $(this).val()
                        },
                        success: function (response) {
                            if (response.status) {
                                table.ajax.reload();
                                toastr["success"](response.message);
                            } else {
                                toastr["error"](response.message);

                            }

                            $("#wait_msg,#overlay").hide();

                        }
                    });
                });

                $(document).on('change', '.type', function (e) {

                    e.preventDefault();
                    var action = $(this).attr('href');
                    $.ajax({
                        url: action,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{csrf_token()}}',
                            type_id: $(this).val()
                        },
                        success: function (response) {
                            if (response.status) {
                                table.ajax.reload();
                                toastr["success"](response.message);
                            } else {
                                toastr["error"](response.message);

                            }

                            $("#wait_msg,#overlay").hide();

                        }
                    });
                });

                $(document).on('click', '.edit', function (e) {

                    e.preventDefault();
                    var action = $(this).attr('href');
                    $.ajax({
                        url: action,
                        type: 'GET',
                        success: function (data) {
                            $('#results-modals').html(data);
                            $('#editGroup').modal('show');
                        }, error: function (xhr) {
                            if (xhr.status === 403) {
                                toastr["error"]('{{message_unauthorize()}}');
                            }
                        }
                    });
                });

                $(document).on('click', '.delete', function (e) {

                    e.preventDefault();
                    var action = $(this).attr('href');
                    bootbox.confirm("هل متاكد من هذه العملية ؟!<a class='blind-alert round'>انتبه: لا يمكنك الرجوع..!!<a/> ", function (result) {
                        if (result) {
                            $("#wait_msg,#overlay").show();

                            $.ajax({
                                url: action,
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    _token: '{{csrf_token()}}',
                                },
                                success: function (response) {
                                    if (response.status) {
                                        table.ajax.reload();
                                        toastr["success"](response.message);
                                    } else {
                                        toastr["error"](response.message);

                                    }

                                    $("#wait_msg,#overlay").hide();

                                }
                            });
                        }
                    });

                });

                $(document).on('click', '.expand_group_members', function () {
                    $(this).find("span").addClass("glyphicon-chevron-up");
                    $(this).find("span").removeClass("glyphicon-chevron-down");
                    var td = $(this).parent();
                    var row = $(this).closest("tr");
                    var id = $(this).data('id');
                    td.find("img").remove();
                    $.fn.isAfter = function (sel) {
                        return this.prevAll(sel).length !== 0;
                    }
                    if (!$(".subgrid.m" + id + "").is(":visible")) {
                        if (!$(".subgrid.m" + id + "").isAfter(row)) {
                            td.append("<img src='{{url(admin_assets_vw().'/apps/img/preloader.gif')}}'/>");

                            $.ajax({
                                url: "{{url('admin/group-members')}}/" + id,
                                type: 'GET',
                                success: function (data) {

                                    td.find("img").remove();
                                    $(data).insertAfter(row);

                                    $('.expand_group_members_table').DataTable(
                                        {
                                            "bDestroy": true,
                                            "bFilter": false,
                                            "searching": true,
                                            "sDom": 'rtpi',
//                                    "oLanguage": {"sEmptyTable": "لا يوجد نتائج للبحث!"},
                                            "bScrollCollapse": true,
                                            "bLengthChange": true,
                                            "bPaginate": true,
                                            "bInfo": false,
                                            "columnDefs": [
                                                {"targets": [0], "orderable": false},
                                            ], "displayStart": 0,
                                            "language": {
                                                "sProcessing": "<img src='{{loader_icon()}}' />",
                                                "sLengthMenu": "أظهر _MENU_ مدخلات",
                                                "sZeroRecords": "لم يعثر على أية سجلات",
                                                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                                                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                                                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                                                "sInfoPostFix": "",
                                                "sSearch": "ابحث:",
                                                "sUrl": "",
                                                "oPaginate": {
                                                    "sFirst": "الأول",
                                                    "sPrevious": "السابق",
                                                    "sNext": "التالي",
                                                    "sLast": "الأخير"
                                                }
                                            }
                                        }
                                    );
                                }
                            });

                        }
                        else {
                        }
                        $(".subgrid.m" + id + "").removeClass("hide");

                    } else {
                        $(this).find("span").removeClass("glyphicon-chevron-up");
                        $(this).find("span").addClass("glyphicon-chevron-down");
                        $(".subgrid.m" + id + "").addClass("hide");
                    }
                });

            });
        </script>
    @endpush
@stop