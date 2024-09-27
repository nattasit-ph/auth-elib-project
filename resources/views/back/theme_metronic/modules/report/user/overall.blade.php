@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'User Statistics')
@section('page_title', 'User Statistics')
@section('topbar_button')
<div>
    <form id="frm_export" method="get" action="{{ route('admin.report.user.exportToExcel') }}">
        <input type="hidden" id="hd_lang" name="hd_lang" value="">
        <a id="btn_export_to_excel" href="javascript:void(0);" class="btn btn-success">
            <i class="fa fa-file-excel"></i> Export to excel
        </a>
    </form>
</div>
@endsection

@push('additional_css')
<style type="text/css">
</style>
@endpush

@section('content')
<div id="main_content" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

    {{-- Display Success Message Area --}}
    @if(session()->get('success'))
    <div class="alert alert-solid-success alert-bold alert-dismissible fade show" role="alert" dismissable="true">
        <div class="alert-text">{{ session()->get('success') }}</div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
    @endif

    {{-- Display Error Area --}}
    @if ($errors->any())
    <div class="alert alert-solid-danger alert-bold" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- User chart area -->

    <div class="row">

        <!-- User login by device -->
        <div class="col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            Login Device
                        </h3>
                        <span class="kt-widget14__desc">
                            อุปกรณ์ที่ใช้เข้าสู่ระบบ
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="login_device" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End: User login by device -->

        <!-- User browser -->
        <div class="col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            Usually Use Browser
                        </h3>
                        <span class="kt-widget14__desc">
                            เบราว์เซอร์ที่เข้าใช้งาน
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="use_browser" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End: browser -->

    </div>

    <div class="row">

        <!-- User gender -->
        <div class="col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            User Gender
                        </h3>
                        <span class="kt-widget14__desc">
                            สถิติของผู้ใช้งานตามเพศ
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="user_gender_chart" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End: User gender -->

        <!-- User age -->
        <div class="col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            User Range Age
                        </h3>
                        <span class="kt-widget14__desc">
                            สถิติของผู้ใช้งานตามช่วงอายุ
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="user_range_age_chart" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End: User age -->

    </div>
    <!-- End: User chart area -->

    <!-- interest topic -->
    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            User Interest Topic
                        </h3>
                        <span class="kt-widget14__desc">
                            หมวดหมู่ที่ผู้ใช้งานสนใจ
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="intesrest_topic" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End: interest topic -->

    <!-- dummy chart -->
    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            User Career
                        </h3>
                        <span class="kt-widget14__desc">
                            ผู้ใช้งานตามสายอาชีพ
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="user_position" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            User Education
                        </h3>
                        <span class="kt-widget14__desc">
                            ผู้ใช้งานตามการศึกษา
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="user_education" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-widget14">
                    <div class="kt-widget14__header kt-margin-b-30">
                        <h3 class="kt-widget14__title">
                            User Area
                        </h3>
                        <span class="kt-widget14__desc">
                            ผู้ใช้งานตามที่อยู่อาศัย
                        </span>
                    </div>
                    <div class="kt-widget14__chart" style="height:295px;">
                        <div class="" id="user_area" style="width: 100%; height: 295px; margin: 0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End: dummy chart -->

    <!--begin::Portlet-->



    <!--end::Portlet-->
</div>
@endsection

@push('additional_js')
<script src="additional/highcharts-3.0.7/js/highcharts.js"></script>
<script src="additional/highcharts-3.0.7/js/themes/bookdose.js"></script>
<script type="text/javascript">
    function getPieChartData(FullUrl, elementId) {
        jQuery.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            url: FullUrl,
            beforeSend: function() {
                jQuery('#' + elementId).html('Generating data...');
            },
            success: function(data) {
                //Draw Chart
                if (data.data_chart) {
                    chart_1 = new Highcharts.Chart({
                        chart: {
                            renderTo: elementId
                        },
                        tooltip: {
                            formatter: function() {
                                return '<b>' + this.point.name + '</b><br/>' +
                                    this.series.name + ': ' + this.y;
                            }
                        },
                        legend: {
                            enabled: true
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                }
                            }
                        },
                        series: data.data_chart
                    });
                } else {
                    jQuery('#' + elementId).html('No data available.');
                }
            }
        });
    }

    function get_interset_topic() {
        jQuery.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            url: "{{route('admin.report.user.interestTopic')}}",
            beforeSend: function() {
                jQuery('#intesrest_topic').html('Generating data...');
            },
            success: function(data) {
                //Draw Chart
                if (data.data_chart) {
                    chart_1 = new Highcharts.Chart({
                        colors: ["#20c997"],
                        chart: {
                            renderTo: 'intesrest_topic'
                        },
                        tooltip: {
                            formatter: function() {
                                return '<b>' + this.x + '</b><br/>' +
                                    this.series.name + ': ' + this.y;
                            }
                        },
                        xAxis: {
                            lineColor: '#20c997',
                            categories: data.task_title,
                            labels: {
                                rotation: -45,
                                align: 'right',
                                formatter: function() {
                                    return (this.value).toString();
                                }
                            }
                        },
                        yAxis: [{ // Primary yAxis
                            gridLineColor: '#20c997',
                            min: 0,
                            minRange: 5,
                            labels: {
                                formatter: function() {
                                    return this.value;
                                }
                            },
                            title: {
                                text: ''
                            }
                        }],
                        legend: {
                            enabled: false
                        },
                        series: data.data_chart
                    });
                } else {
                    jQuery('#intesrest_topic').html('No data available.');
                }
            }
        });
    }

    $(function() {
        getPieChartData("{{route('admin.report.user.UsuallyBrowser')}}", 'use_browser');
        getPieChartData("{{route('admin.report.user.loginDevice')}}", 'login_device');
        getPieChartData("{{route('admin.report.user.gender')}}", 'user_gender_chart');
        getPieChartData("{{route('admin.report.user.rangeAge')}}", 'user_range_age_chart');
        get_interset_topic();

        $('#btn_export_to_excel').on('click', function(e) {
            e.preventDefault();
            $('#hd_lang').val("{{ $lang ?? config('bookdose.frontend_default_lang') }}");
            $('#frm_export').submit();
        });
    })
</script>
@endpush