@extends(admin_layout_vw().'.index')

@section('css')
    <link href="{{url('/')}}/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css">
    <link href="{{url('/')}}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap-rtl.css"
          rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bell-o"></i> {{$sub_title}}
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-12" align="center">
                                <div class="btn-group">
                                    <a href="{{url(admin_vw().'/constant/'.$type)}}" class="btn sbold green add">
                                        <i class="fa fa-plus"></i>

                                        اضافة
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-advance table-hover" id="constants_tbl">
                            <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    الاسم
                                </th><th>
                                    الحد الاقصى
                                </th><th>
                                    التكلفة
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

    <script src="{{url('/')}}/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
            type="text/javascript"></script>
    <script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>

    @push('js')
        <script>
            $(document).ready(function () {
                var table = $('#constants_tbl').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": baseURL + '/admin/constant-data/{{$type}}',
                    columns: [
                        {data: 'num', name: 'num', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'max_num_member', name: 'max_num_member'},
                        {data: 'cost', name: 'cost'},
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
                            $('#addConstant').modal('show');
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
                            $('#editConstant').modal('show');
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
                    bootbox.confirm("هل متاكد من هذه العملية ؟! <a class='blind-alert round'>انتبه: لا يمكنك الرجوع..!!<a/> ", function (result) {
                        if (result) {
                            $("#wait_msg,#overlay").show();

                            $.ajax({
                                url: action,
                                type: 'DELETE',
                                dataType: 'json',
                                data: {
                                    _token: '{{csrf_token()}}',
                                    type: '{{$type}}',
                                },
                                success: function (response) {
                                    if (response.status) {
                                        table.ajax.reload();
                                        toastr["success"](response.message);
                                    }else{
                                        toastr["error"](response.message);

                                    }

                                    $("#wait_msg,#overlay").hide();

                                }
                            });
                        }
                    });

                });
            });

        </script>
    @endpush
@stop