<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{url('/')}}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
<link href="{{url('/')}}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL PLUGINS -->
<div class="modal fade" id="{{$modal_id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">{!! $modal_title !!}</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet-body form">
                            {!! Form::open(['method'=>$form['method'],'id'=>$form['form_id'],'class'=>'form-horizontal form','url'=>$form['url'],'files'=>true]) !!}
                            <div class="alert alert-danger" role="alert" style="display: none;">
                                <strong>Oh snap!</strong>
                                <span>Change a few things down and try submitting again.</span>
                                <div class="errors"></div>
                            </div>
                            <div class="form-body">
                                @foreach($form['fields'] as $key=> $fields)
                                    @if($fields == 'text')
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <input type="text" name="{{$key}}" id="{{$key}}" class="form-control"
                                                       @if(isset($form['values'])) value="{{$form['values'][$key]}}" @endif>
                                            </div>
                                        </div>
                                    @endif
                                    @if($fields == 'file')
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <input type="file" name="{{$key}}" id="{{$key}}" class="form-control"
                                                       @if(isset($form['values'])) value="{{$form['values'][$key]}}" @endif>
                                            </div>
                                        </div>
                                    @endif
                                    @if($fields == 'button')
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <div class="btn-group" style="    margin-right: 20px;">
                                                    <a href="#full" data-toggle="modal"
                                                       class="btn btn-icon-only btn-circle green">

                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                </div>
                                                <input type="hidden" name="lat" id="lat"
                                                       class="form-control">
                                                <input type="hidden" name="lng" id="lng"
                                                       class="form-control">
                                            </div>
                                        </div>

                                    @endif
                                    @if($fields == 'data-time')
                                        <div class="form-group">
                                            <label class="control-label col-md-3">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <div class="input-group date form_datetime">
                                                    <input type="text" size="16" name="{{$key}}" id="{{$key}}"
                                                           data-date-format="dd-mm-yyyy"
                                                           @if(isset($form['values'])) value="{{$form['values'][$key]}}"
                                                           @endif readonly class="timepicker form-control ">
                                                    <span class="input-group-btn">
                                                            <button class="btn default date-set" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                </div>
                                            </div>
                                        </div>

                                    @endif
                                    @if($fields == 'date-picker')

                                        <div class="form-group">
                                            <label class="control-label col-md-3">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">

                                                <div class="input-group input-medium date date-picker"
                                                     data-date="12-02-2012" data-date-format="dd-mm-yyyy"
                                                     data-date-viewmode="years">
                                                    <input type="text" class="form-control" readonly="">
                                                    <span class="input-group-btn">
                                                                        <button class="btn default" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                </div>
                                                {{--<div class="input-group date form_datetime">--}}
                                                {{--<input type="text" size="16" name="{{$key}}" id="{{$key}}"--}}
                                                {{--@if(isset($form['values'])) value="{{$form['values'][$key]}}"--}}
                                                {{--@endif readonly class="timepicker form-control ">--}}
                                                {{--<span class="input-group-btn">--}}
                                                {{--<button class="btn default date-set" type="button">--}}
                                                {{--<i class="fa fa-calendar"></i>--}}
                                                {{--</button>--}}
                                                {{--</span>--}}
                                                {{--</div>--}}
                                            </div>
                                        </div>

                                    @endif

                                    @if($fields == 'password')
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <input type="password" name="{{$key}}" id="{{$key}}"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    @endif
                                    @if($fields == 'textarea')
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <textarea name="{{$key}}" id="{{$key}}" rows="5"
                                                          class="form-control">@if(isset($form['values'])){{$form['values'][$key]}}@endif</textarea>
                                            </div>
                                        </div>
                                    @endif

                                    @if(is_array($fields) && !isset($fields['is_multiple']))
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <div class="input-icon">
                                                    <select class="form-control select2-multiple {{$key}}"
                                                            name="{{$key}}" id="{{$key}}"
                                                            style="    padding: 0;">
                                                        @foreach($fields as $k=>$field)
                                                            <option value="{{$k}}"
                                                                    @if(isset($form['values']) && $form['values'][$key] == $k) selected @endif>{{ucfirst($field)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(is_array($fields) && isset($fields['is_multiple']) && $fields['is_multiple'])

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <div class="input-icon">

                                                    <select class="form-control select2-multiple select2-hidden-accessible {{$key}}"
                                                            multiple="" tabindex="-1" aria-hidden="true"
                                                            name="{{$key}}[]" id="{{$key}}"
                                                            style="    padding: 0;">
                                                        @foreach($fields[$key] as $k=>$field)
                                                            <option value="{{$k}}"
                                                                    @if(isset($form['values']) && $form['values'][$key] == $k) selected @endif>{{ucfirst($field)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    @endif
                                    @if(is_object($fields))
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">{{$form['fields_ar'][$key]}}</label>
                                            <div class="col-md-9">
                                                <div class="input-icon">
                                                    <select class="form-control {{$key}}" name="{{$key}}" id="{{$key}}"
                                                            style="    padding: 0;">
                                                        <option></option>
                                                        @foreach($fields as $field)
                                                            <option value="{{$field->id}}"
                                                                    @if(isset($form['values']) && $form['values'][$key] == $field->id) selected @endif>{{ucfirst($field->name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" class="btn green">{{$action}}</button>
                                            <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">
                                                اغلاق
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
<script src="{{url('/')}}/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="{{url('/')}}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{url('/')}}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{url('/')}}/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>