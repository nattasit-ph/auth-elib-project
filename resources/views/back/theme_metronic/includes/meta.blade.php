<meta charset="utf-8" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="Admin System">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Global Core Vendors -->
<link href="vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
<link href="vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />

<!--Global Optional Vendors -->
<link href="vendors/general/tether/dist/css/tether.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-datetime-picker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css" rel="stylesheet" type="text/css" />
<!-- <link href="vendors/general/select2/dist/css/select2.css" rel="stylesheet" type="text/css" /> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link href="vendors/general/ion-rangeslider/css/ion.rangeSlider.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/nouislider/distribute/nouislider.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/owl.carousel/dist/assets/owl.carousel.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/owl.carousel/dist/assets/owl.theme.default.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/summernote/dist/summernote.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/animate.css/animate.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/toastr/build/toastr.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/morris.js/morris.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/sweetalert2/dist/sweetalert2.css" rel="stylesheet" type="text/css" />
<link href="vendors/general/socicon/css/socicon.css" rel="stylesheet" type="text/css" />
<link href="vendors/custom/vendors/line-awesome/css/line-awesome.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<link href="vendors/custom/vendors/flaticon/flaticon.css" rel="stylesheet" type="text/css" />
<link href="vendors/custom/vendors/flaticon2/flaticon.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.3/css/all.css">
<link href="vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.min.css">

<!-- Theme Core -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
<link href="css/style.bundle.css" rel="stylesheet" type="text/css" />
<link href="css/datatables.custom.css" rel="stylesheet" type="text/css" />

<!-- Layout Skins (used by all pages) -->
<link href="css/skins/header/base/light.css" rel="stylesheet" type="text/css" />
<link href="css/skins/header/menu/light.css" rel="stylesheet" type="text/css" />
<link href="css/skins/brand/dark.css" rel="stylesheet" type="text/css" />
<link href="css/skins/aside/dark.css" rel="stylesheet" type="text/css" />

<!-- Custom -->
<link type="text/css" href="{{ asset('/back/'.config('bookdose.theme_back').'/css/color/main.css') }}" rel="stylesheet">
<link type="text/css" href="{{ asset('/back/'.config('bookdose.theme_back').'/css/lang/lang_'.app()->getLocale().'.css') }}" rel="stylesheet">
<link href="css/main.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="{{ asset(config('bookdose.app.project').'/images/favicons/favicon.ico') }}" type="image/x-icon">

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script> -->

@if(in_array(strtoupper(config('bookdose.app.name')), ['LABOUR']))
<!-- color css -->
<link type="text/css" href="{{ asset('/back/'.strtolower(config('bookdose.app.name')).'/css/color.css') }}" rel="stylesheet">
<!-- custom project css -->
<link type="text/css" href="{{ asset('/back/'.strtolower(config('bookdose.app.name')).'/css/style.css') }}" rel="stylesheet">
@endif
