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
                        <i class="fa fa-bell-o"></i> {{$title}}
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-advance table-hover" id="users_tbl">
                            <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    الاسم
                                </th>
                                <th>
                                    المنطقة
                                </th>
                                <th>
                                    المجموعات
                                </th>
                                <th>
                                    الجنس
                                </th>
                                <th>
                                    رقم الهاتف
                                </th>
                                <th>
                                    البريد الالكتروني
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
                var table = $('#users_tbl').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": baseURL + '/admin/users-data',
                    columns: [
                        {data: 'num', name: 'num', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'region', name: 'region'},
                        {data: 'groups', name: 'groups'},
                        {data: 'gender', name: 'gender'},
                        {data: 'mobile', name: 'mobile'},
                        {data: 'email', name: 'email'},
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

                $(document).on('click', '.status', function (e) {

                    e.preventDefault();
                    $.ajax({
                        url: $(this).attr('href'),
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {

                            if (data.status) {
                                table.ajax.reload();
                            }
                        }
                    })
                });

                $(document).on('click', '.expand_groups', function () {
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
                                url:"{{url('admin/user-groups')}}/" + id,
                                type:'GET',
                                success:function (data) {

                                    td.find("img").remove();
                                    $(data).insertAfter(row);

                                    $('.expand_group_table').DataTable(
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