<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
        background-color: #edf2f7;
        font-size: 16px;
    }
    .text-center {
        text-align: center;
    }
    .button {
        padding: 8px 15px;
        -webkit-text-size-adjust: none; 
        border-radius: 4px; color: #fff; 
        display: inline-block; 
        overflow: hidden;
        text-decoration: none;
    }
    .mt {
        margin-top: 30px;
    }
    .mb {
        margin-bottom: 30px;
    }
    .button-primary {
        background-color: #2d3748;
        color: #fff;
    }
    .text-gray {
        color: #b0adc5;
    }
    @media  only screen and (max-width: 600px) {
    .inner-body {
        width: 100% !important;
    }

    .footer {
        width: 100% !important;
    }
    }

    @media  only screen and (max-width: 500px) {
    .button {
        width: 100% !important;
    }
    }
    </style>
    </head>
    <body>
        <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; width: 100%;">
            <tr>
                <td align="center" style="">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0; width: 100%;">
                    <tr>
                        <td class="header" style="padding: 25px 0; text-align: center;">
                            <a href="{{ config('bookdose.app.main_product_redirect') }}" style="color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none; display: inline-block;">{{ config('bookdose.app.name') }}</a>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="background-color: #edf2f7;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="-premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;">
                            <!-- Body content -->
                                <tr>
                                    <td class="content-cell" style="max-width: 100vw; padding: 32px;">
                                        <h1 class="mb" style="font-size: 18px; font-weight: bold;">{{ __('mail.dear')." ".($name ?? __('mail.user')) }}</h1>

                                        @yield('content')

                                        <p class="mt">{{ __('mail.regards') }}<br>{{ config('bookdose.app.name') }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-gray text-center" style="font-size: 12px;">© {{ config('bookdose.app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </tr>
        </table>
    </body>
</html>