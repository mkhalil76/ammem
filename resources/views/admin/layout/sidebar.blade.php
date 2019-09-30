<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-hover-submenu " data-keep-expanded="false"
            data-auto-scroll="true" data-slide-speed="200">
            @if(auth()->user()->type == 'admin')

            <li class="nav-item @if(request()->segment(2) == 'home') start active open @endif">
                <a href="{{url(admin_vw().'/home')}}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">الصفحة الرئيسية</span>
                    @if(request()->segment(2) == 'home')
                        <span class="selected"></span>
                    @endif
                </a>
            </li>
            @endif

            @if(auth()->user()->type == 'admin')
                <li class="nav-item @if(request()->segment(2) == 'users' || request()->segment(2) == 'send-public-notification') start active open @endif">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-user"></i>
                        <span class="title">المستخدمين</span>
                        <span class="arrow"></span>

                    </a>

                    <ul class="sub-menu">
                        <li class="nav-item @if(request()->segment(2) == 'users') start active open @endif">
                            <a href="{{url(admin_vw().'/users')}}" class="nav-link ">
                                <span class="title">ادارة المستخدمين</span>
                            </a>
                        </li>
                        <li class="nav-item @if(request()->segment(2) == 'send-public-notification') start active open @endif">
                            <a href="{{url(admin_vw().'/send-public-notification')}}" class="nav-link ">
                                <span class="title">ارسال تعميم عام</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item @if(request()->segment(2) == 'groups') start active open @endif">
                    <a href="{{url(admin_vw().'/groups')}}" class="nav-link nav-toggle">
                        <i class="fa fa-users"></i>
                        <span class="title">المجموعات</span>
                        @if(request()->segment(2) == 'groups')
                            <span class="selected"></span>
                        @endif
                    </a>
                </li>
                <li class="nav-item @if(request()->segment(2) == 'messages') start active open @endif">
                    <a href="{{url(admin_vw().'/messages')}}" class="nav-link nav-toggle">
                        <i class="fa fa-send"></i>
                        <span class="title">التعميمات</span>
                    </a>
                </li>

                <li class="nav-item @if(request()->segment(2) == 'payments') start active open @endif">
                    <a href="{{url(admin_vw().'/payments')}}" class="nav-link nav-toggle">
                        <i class="fa fa-money"></i>
                        <span class="title">حركة الدفعات</span>
                    </a>
                </li>

                <li class="nav-item @if(request()->segment(2) == 'activities' || request()->segment(2) == 'organizations' || request()->segment(2) == 'interests'|| request()->segment(2) == 'jobs'|| request()->segment(2) == 'group-types') start active open @endif">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-cogs"></i>
                        <span class="title">الثوابت</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item @if(request()->segment(2) == 'activities' ) start active open @endif">
                            <a href="{{url(admin_vw().'/activities')}}" class="nav-link ">
                                <span class="title">النشاطات</span>
                            </a>
                        </li>
                        <li class="nav-item @if( request()->segment(2) == 'organizations' ) start active open @endif">
                            <a href="{{url(admin_vw().'/organizations')}}" class="nav-link ">
                                <span class="title">الجهات</span>
                            </a>
                        </li>
                        <li class="nav-item @if( request()->segment(2) == 'interests') start active open @endif">
                            <a href="{{url(admin_vw().'/interests')}}" class="nav-link ">
                                <span class="title">الاهتمامات</span>
                            </a>
                        </li>
                        <li class="nav-item @if( request()->segment(2) == 'jobs') start active open @endif">
                            <a href="{{url(admin_vw().'/jobs')}}" class="nav-link ">
                                <span class="title">الوظائف</span>
                            </a>
                        </li>
                        <li class="nav-item @if( request()->segment(2) == 'group-types') start active open @endif">
                            <a href="{{url(admin_vw().'/group-types')}}" class="nav-link ">
                                <span class="title">انواع المجموعات</span>
                            </a>
                        </li>
                    </ul>
                </li>

            @endif

            @if(auth()->user()->type == 'user')

                <li class="nav-item @if(request()->segment(2) == 'my-groups' || request()->segment(2) == 'conversation') start active open @endif">
                    <a href="{{url(user_vw().'/my-groups')}}" class="nav-link nav-toggle">
                        <i class="fa fa-group"></i>
                        <span class="title">مجموعاتي</span>
                        @if(request()->segment(2) == 'my-groups' || request()->segment(2) == 'conversation')
                            <span class="selected"></span>
                        @endif

                    </a>
                </li>

                <li class="nav-item @if(request()->segment(2) == 'contacts') start active open @endif">
                    <a href="{{url(user_vw().'/contacts')}}" class="nav-link nav-toggle">
                        <i class="fa fa-user"></i>
                        <span class="title">جهات الاتصال</span>
                        @if(request()->segment(2) == 'contacts')
                            <span class="selected"></span>
                        @endif

                    </a>
                </li>
                <li class="nav-item @if(request()->segment(2) == 'messages') start active open @endif">
                    <a href="{{url(user_vw().'/messages')}}" class="nav-link nav-toggle">
                        <i class="fa fa-send"></i>
                        <span class="title">التعاميم</span>
                        @if(request()->segment(2) == 'messages')
                            <span class="selected"></span>
                        @endif

                    </a>
                </li>
            @endif
        </ul>

        <!-- END SIDEBAR MENU -->
    </div>
</div>
