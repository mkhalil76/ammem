<?php
/**
 * Created by PhpStorm.
 * User: mohammedsobhei
 * Date: 11/21/17
 * Time: 10:12 PM
 */


function dashboard()
{
    return 'Dashboard';
}

function admin_vw()
{
    return 'admin';
}


function admin_layout_vw()
{
    return 'admin.layout';
}

function admin_error_vw()
{
    return 'admin.errors';
}

function admin_middleware()
{
    return 'admin';
}

function admin_url()
{
    return 'admin';
}

function admin_users_vw()
{
    return 'admin.users';
}

function admin_groups_vw()
{
    return 'admin.groups';
}

function admin_messages_vw()
{
    return 'admin.messages';
}

function admin_public_notification_vw()
{
    return 'admin.public_notification';
}

function admin_payment_vw()
{
    return 'admin.payments';
}

function admin_assets_vw()
{
    return 'assets/';
}

function version_api()
{
    return '/v1';
}

function namespace_api()
{
    return 'Api\V1';
}

function public_url()
{
    return url('public/');
}

function upload_url()
{
    return base_path() . '/assets/upload';
}

function loader_icon()
{
    return url('assets/apps/img/preloader.gif');
}

function user_vw()
{
    return 'user';
}

function google_api_key()
{
    return 'AIzaSyBxxFm7dwG-LHInOuNjy8uwMVF5bFFi8FA';
}

function message_unauthorize()
{
    return 'للاسف: لا يوجد لك صلاحية لعمل ذلك';
}

function site_vw()
{
    return 'site';
}


function page_count($num_object)
{
    return ceil($num_object / max_pagination());
}
