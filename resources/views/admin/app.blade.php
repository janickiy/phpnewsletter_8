<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('frontend.str.admin_panel') }} | @yield('title')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ url('favicon.ico') }}" type="image/x-icon">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome7/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte4/css/adminlte.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/flag-icons/css/flag-icons.min.css') }}">

    <!-- Custom style -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">

    @yield('css')

    <script type="text/javascript">
        let SITE_URL = "{{ URL::to('/') }}";
    </script>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<!-- Site wrapper -->
<div class="app-wrapper">
    <!-- Navbar -->
    <nav class="app-header navbar navbar-expand bg-body">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="fullscreen" title="{{ __('frontend.str.expand_full_screen') }}"
                   href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">

                @php
                    $currentLocale = app()->getLocale();
                    $flags = config('app.flags', []);
                    $languages = config('app.languages', []);
                    $currentFlag = $flags[$currentLocale] ?? 'us';
                @endphp

                <a class="nav-link" data-bs-toggle="dropdown" href="javascript:void(0);">
                    <i class="fi fi-{{ $currentFlag }}"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-end p-0">
                    @foreach($languages as $code => $languageName)
                        <a data-id="{{ $code }}" href="javascript:void(0);" class="dropdown-item select-lang">
                            <i class="fi fi-{{ $flags[$code] ?? 'us' }} me-2"></i> {{ $languageName }}
                        </a>
                    @endforeach
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users.edit', ['id' => Auth::user()->id]) }}"
                   title="{{ Auth::user()->login }}">
                    <i class="fas fa-user-circle me-1"></i>
                    <span>{{ Auth::user()->login }}</span>
                </a>
            </li>

            <!-- Notifications Dropdown Menu -->
            <li class="nav-item">
                <a class="nav-link" title="{{ __('frontend.str.signout') }}" href="{{ route('logout') }}"
                   role="button">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!-- Brand Logo -->
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard.index') }}" class="brand-link">
                <img src="{{ url('/assets/img/logo-sidebar-icon.png') }}?v={{ filemtime(public_path('assets/img/logo-sidebar-icon.png')) }}" alt="PHP Newsletter" class="brand-icon">
                <span class="brand-wordmark">
                    <span class="brand-wordmark-main"><span>PHP</span>Newsletter</span>
                    <span class="brand-wordmark-sub">Mailing System</span>
                </span>
            </a>
        </div>

        <!-- Sidebar -->
        <div class="sidebar-wrapper">
            <!-- Sidebar user (optional) -->


            <!-- Sidebar Menu -->
            <nav class="mt-2">
                @php
                    $isModerator = Auth::user()?->role === \App\Enums\UserRole::Moderator->value;
                    $isModeratorProjectPage = $isModerator && Request::is('organizations/*/projects*');
                @endphp

                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->

                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard.index') }}" class="nav-link{{ Request::is('/') ? ' active' : '' }}"
                           title="{{ __('frontend.menu.dashboard') }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>{{ __('frontend.menu.dashboard') }}</p>
                        </a>
                    </li>

                    @if(!$isModerator && PermissionsHelper::has_permission('admin|organization_admin|project_admin|moderator'))
                        <li class="nav-item">
                            <a href="{{ route('admin.organizations.index') }}" class="nav-link{{ Request::is('organizations*') && !$isModeratorProjectPage ? ' active' : '' }}"
                               title="{{ __('frontend.menu.organizations') }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>{{ __('frontend.menu.organizations') }}</p>
                            </a>
                        </li>
                    @endif

                    @if($isModerator)
                        <li class="nav-item">
                            <a href="{{ route('admin.projects.index') }}" class="nav-link{{ Request::is('projects*') || $isModeratorProjectPage ? ' active' : '' }}"
                               title="{{ __('frontend.menu.projects') }}">
                                <i class="nav-icon fas fa-folder-open"></i>
                                <p>{{ __('frontend.menu.projects') }}</p>
                            </a>
                        </li>
                    @endif

                    @if(PermissionsHelper::has_permission('admin'))
                        <li class="nav-item">
                            <a href="{{ route('admin.templates.index') }}" class="nav-link{{ Request::is('templates*') || Request::is('template*') ? ' active' : '' }}"
                               title="{{ __('frontend.menu.templates') }}">
                                <i class="nav-icon fas fa-envelope"></i>
                                <p>{{ __('frontend.menu.templates') }}</p>
                            </a>
                        </li>
                    @endif

                    @if(PermissionsHelper::has_permission('admin|organization_admin|project_admin|moderator'))

                        <li class="nav-item">
                            <a href="{{ route('admin.subscribers.index') }}" class="nav-link{{ Request::is('subscribers*') ? ' active' : '' }}"
                               title="{{ __('frontend.menu.subscribers') }}">
                                <i class="nav-icon fas fa-user-friends"></i>
                                <p>{{ __('frontend.menu.subscribers') }}</p>
                            </a>
                        </li>

                    @endif

                    @if(PermissionsHelper::has_permission('admin'))
                        <li class="nav-item">
                            <a href="{{ route('admin.macros.index') }}" class="nav-link{{ Request::is('macros*') ? ' active' : '' }}"
                               title="{{ __('frontend.menu.macros') }}">
                                <i class="nav-icon fas fa-scroll"></i>
                                <p>{{ __('frontend.menu.macros') }}</p>
                            </a>
                        </li>
                    @endif

                    @if(PermissionsHelper::has_permission('admin|organization_admin|project_admin'))
                        <li class="nav-item">
                            <a href="{{ route('admin.schedule.index') }}" class="nav-link{{ Request::is('schedule*') ? ' active' : '' }}"
                               title="{{ __('frontend.menu.schedule') }}">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>{{ __('frontend.menu.schedule') }}</p>
                            </a>
                        </li>
                    @endif

                    @if(PermissionsHelper::has_permission('admin'))

                        <li class="nav-item">
                            <a href="{{ route('admin.category.index') }}" class="nav-link{{ Request::is('category*') ? ' active' : '' }}"
                               title="{{ __('frontend.menu.category') }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>{{ __('frontend.menu.category') }}</p>
                            </a>
                        </li>

                    @endif

                    @if(PermissionsHelper::has_permission('admin'))

                        <li class="nav-item">
                            <a href="{{ route('admin.smtp.index') }}" class="nav-link{{ Request::is('smtp*') ? ' active' : '' }}" title="{{ __('frontend.str.smtp_server') }}">
                                <i class="nav-icon fas fa-inbox"></i>
                                <p>{{ __('frontend.str.smtp_server') }}</p>
                            </a>
                        </li>

                    @endif

                    @if(PermissionsHelper::has_permission('admin|organization_admin|project_admin|moderator'))
                        <li class="nav-item{{ Request::is('log*') || Request::is('redirect*') ? ' menu-open' : '' }}">
                            <a href="#" class="nav-link{{ Request::is('log*') || Request::is('redirect*') ? ' active' : '' }}" title="{{ __('frontend.menu.logs') }}">
                                <i class="nav-icon fas fa-chart-area"></i>
                                <p>
                                    {{ __('frontend.menu.logs') }}
                                    <i class="nav-arrow fas fa-angle-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ route('admin.log.index') }}" class="nav-link{{ Request::is('log*') ? ' active' : '' }}"
                                       title="{{ __('frontend.menu.mailing_log') }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('frontend.menu.mailing_log') }}</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('admin.redirect.index') }}" class="nav-link{{ Request::is('redirect*') ? ' active' : '' }}"
                                       title="{{ __('frontend.menu.referrens_log') }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('frontend.menu.referrens_log') }}</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endif

                    @if(PermissionsHelper::has_permission('admin'))

                        <li class="nav-item">
                            <a href="{{ route('admin.settings.index') }}" class="nav-link{{  Request::is('settings*') ? ' active' : '' }}"
                               title="{{ __('frontend.menu.settings') }}">
                                <i class="nav-icon fa fa-cogs"></i>
                                <p>{{ __('frontend.menu.settings') }}</p>
                            </a>
                        </li>

                    @endif

                    @if(PermissionsHelper::has_permission('admin'))

                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link{{ Request::is('users*') ? ' active' : '' }}"
                               title="{{ __('frontend.menu.users') }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>{{ __('frontend.menu.users') }}</p>
                            </a>
                        </li>

                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.faq') }}" class="nav-link{{ Request::is('faq*') ? ' active' : '' }}" title="FAQ">
                            <i class="nav-icon fas fa-question-circle"></i>
                            <p>FAQ</p>
                        </a>
                    </li>

                    <li class="nav-item{{ Request::is('pages*') ? ' menu-open' : '' }}">
                        <a href="#" class="nav-link{{ Request::is('pages*') ? ' active' : '' }}" title="{{ __('frontend.menu.miscellaneous') }}">
                            <i class="nav-icon fas fa-bookmark"></i>
                            <p>
                                {{ __('frontend.menu.miscellaneous') }}
                                <i class="nav-arrow fas fa-angle-right"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{ route('admin.pages.subscription_form') }}" class="nav-link{{ Request::is('pages/subscription-form*') ? ' active' : '' }}"
                                   title="{{ __('frontend.menu.subscription_form') }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __('frontend.menu.subscription_form') }}</p>
                                </a>
                            </li>

                            @if(PermissionsHelper::has_permission(Auth::user()->role,'admin|moderator'))

                                <li class="nav-item">
                                    <a href="{{ route('admin.pages.cron_job_list') }}" class="nav-link{{ Request::is('pages/cron-job-list*') ? ' active' : '' }}"
                                       title="{{ __('frontend.menu.cron_job_list') }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('frontend.menu.cron_job_list') }}</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('admin.pages.phpinfo') }}" class="nav-link{{ Request::is('pages/phpinfo*') ? ' active' : '' }}" title="PHP Info">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>PHP Info</p>
                                    </a>
                                </li>

                            @endif

                        </ul>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <main class="app-main">
        <!-- Content Header (Page header) -->
        <section class="app-content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        @hasSection('breadcrumbs')
                            @yield('breadcrumbs')
                        @else
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('frontend.str.admin_panel') }}</a></li>
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            </ol>
                        @endif
                    </div>
                </div>

                @include('admin.notifications')

            </div><!-- /.container-fluid -->
        </section>

        <div class="app-content">
            @yield('content')
        </div>

    </main>
    <!-- /.content-wrapper -->

    <footer class="app-footer">

            &copy; 2006-{{ date('Y') }}
            <a href="https://janickiy.com" class="text-decoration-none">PHP Newsletter</a>.

        {{ __('frontend.str.author') }}.
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 5 -->
<script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

<!-- Cookie -->
<script src="{{ asset('plugins/cookie/jquery.cookie.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('vendor/adminlte4/js/adminlte.min.js') }}"></script>

<script>

    $(function () {
        $('a.select-lang').on('click', function () {
          //  $(this).parent().find('li.active').removeClass('active');
          //  $(this).addClass('active');

            let Lng = $(this).attr('data-id');

            let request = $.ajax({
                url: '{{ route('admin.ajax.action') }}',
                method: "POST",
                data: {
                    action: "change_lng",
                    locale: Lng,
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json"
            });

            request.done(function (data) {
                if (data.result != null && data.result === true) {
                    location.reload();
                }
            });
        });

        $(document).on("click", "a.opislink:not(.active)", function () {
            $(this).addClass('active');
            $(this).parent().find('div.opis').slideDown(760);
            return false;
        });

        $(document).on("click", "a.opislink.active", function () {
            $(this).removeClass('active');
            $(this).parent().find('div.opis').slideUp(760);
            return false;
        });

        setTimeout(function () {
            setTimeout(function () {
                $('.alert-success').fadeOut('700')
            }, 5000);
        });
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.js-live-search-select').forEach((select) => {
            if (select.dataset.liveSearchReady === '1') {
                return;
            }

            select.dataset.liveSearchReady = '1';
            select.classList.add('d-none');

            const wrapper = document.createElement('div');
            wrapper.className = 'live-search-select';

            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'form-select live-search-select-toggle';
            toggle.setAttribute('aria-haspopup', 'listbox');
            toggle.setAttribute('aria-expanded', 'false');

            const menu = document.createElement('div');
            menu.className = 'live-search-select-menu';

            const search = document.createElement('input');
            search.type = 'search';
            search.className = 'form-control form-control-sm live-search-select-input';
            search.placeholder = select.dataset.searchPlaceholder || '';
            search.autocomplete = 'off';

            const list = document.createElement('div');
            list.className = 'live-search-select-options';
            list.setAttribute('role', 'listbox');

            menu.append(search, list);
            wrapper.append(toggle, menu);
            select.after(wrapper);

            const options = Array.from(select.options).map((option) => ({
                value: option.value,
                label: option.textContent.trim(),
                disabled: option.disabled,
            }));

            const normalize = (value) => value.toLocaleLowerCase().trim();

            const closeMenu = () => {
                wrapper.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            };

            const updateToggle = () => {
                const selected = select.options[select.selectedIndex];
                toggle.textContent = selected ? selected.textContent.trim() : '';
            };

            const selectOption = (option) => {
                if (option.disabled) {
                    return;
                }

                select.value = option.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                updateToggle();
                closeMenu();
                toggle.focus();
            };

            const renderOptions = (query = '') => {
                const needle = normalize(query);
                const matchedOptions = options.filter((option) => normalize(option.label).includes(needle));

                list.innerHTML = '';

                if (matchedOptions.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'live-search-select-empty';
                    empty.textContent = select.dataset.noResults || '';
                    list.append(empty);
                    return;
                }

                matchedOptions.forEach((option) => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'live-search-select-option';
                    item.textContent = option.label;
                    item.setAttribute('role', 'option');
                    item.setAttribute('aria-selected', option.value === select.value ? 'true' : 'false');
                    item.disabled = option.disabled;

                    item.addEventListener('click', () => selectOption(option));
                    list.append(item);
                });
            };

            const openMenu = () => {
                wrapper.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
                search.value = '';
                renderOptions();
                search.focus();
            };

            toggle.addEventListener('click', () => {
                if (wrapper.classList.contains('is-open')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            search.addEventListener('input', () => renderOptions(search.value));
            search.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMenu();
                    toggle.focus();
                }
            });

            document.addEventListener('click', (event) => {
                if (!wrapper.contains(event.target)) {
                    closeMenu();
                }
            });

            updateToggle();
        });
    });
</script>

@yield('js')

</body>
</html>
