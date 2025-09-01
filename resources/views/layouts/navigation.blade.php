<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    {{-- Modified Logo Link for Admin --}}
                    @if(Auth::user()->accessRole === 'Admin')
                        <a href="{{ route('admin.dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Hide Dashboard for Admin --}}
                    @if(Auth::user()->accessRole !== 'Admin')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    {{-- Regular User Links --}}
                    @if(Auth::user()->accessRole !== 'Admin')
                        <x-nav-link :href="route('request.create')" :active="request()->routeIs('request.create')">
                            {{ __('Submit Form') }}
                        </x-nav-link>
                        <x-nav-link :href="route('approvals.index')" :active="request()->routeIs('approvals.index')" class="relative inline-flex items-center">
                            {{ __('Approvals') }}
                            @if(isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                                <span class="notification-badge absolute top-[15%] right-0 translate-x-3/4 inline-flex items-center justify-center h-4 w-4 text-xs font-semibold text-white bg-red-500 rounded-full">
                                    {{ $pendingApprovalCount }}
                                </span>
                            @endif
                        </x-nav-link>

                        @if((Auth::user()->position === 'Head' || Auth::user()->position === 'VPAA') && Auth::user()->accessRole === 'Approver')
                            <x-nav-link :href="route('approver-assignments.index')" :active="request()->routeIs('approver-assignments.index')">
                                {{ __('Manage Approvers') }}
                            </x-nav-link>
                        @endif

                        {{-- PFMO Job Orders Link --}}
                        @if(Auth::user()->department && Auth::user()->department->dept_code === 'PFMO')
                            <x-nav-link :href="route('job-orders.index')" :active="request()->routeIs('job-orders.index') || request()->routeIs('job-orders.show')">
                                {{ __('Job Orders') }}
                            </x-nav-link>
                            @if(Auth::user()->position === 'Head')
                                <x-nav-link :href="route('job-orders.analytics')" :active="request()->routeIs('job-orders.analytics')">
                                    {{ __('Analytics') }}
                                </x-nav-link>
                            @endif
                        @endif
                    @endif

                    {{-- Admin Links --}}
                    @if(Auth::user()->accessRole === 'Admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.employee_list')" :active="request()->routeIs('admin.employee_list') || request()->routeIs('admin.employee_import_form')">
                            {{ __('Employee Information') }}
                        </x-nav-link>
                        {{-- Placeholder for User Management - We will create this route and page later --}}
                        {{-- <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')"> --}}
                        {{--     {{ __('User Management') }} --}}
                        {{-- </x-nav-link> --}}
                        {{-- Placeholder for Department Management - We will create this route and page later --}}
                        {{-- <x-nav-link :href="route('admin.departments.index')" :active="request()->routeIs('admin.departments.index')"> --}}
                        {{--     {{ __('Department Management') }} --}}
                        {{-- </x-nav-link> --}}
                    @endif
                </div>
            </div>

            <!-- Employee Name and Settings Dropdown Container -->
            <div class="hidden sm:flex sm:items-center">
                <!-- Employee Name Display -->
                <div class="flex items-center pr-2 mr-2 border-r">
                    <div class="text-sm font-semibold text-gray-700">
                        {{ Auth::user()->employeeInfo->Titles }} {{ Auth::user()->employeeInfo->FirstName }} {{ Auth::user()->employeeInfo->LastName }} {{ Auth::user()->employeeInfo->Suffix }}
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="flex items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                {{-- Display Username or Full Name in Dropdown Trigger --}}
                                <div>{{ Auth::user()->username ?? Auth::user()->employeeInfo->FirstName }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Hide Dashboard for Admin in Responsive Menu --}}
            @if(Auth::user()->accessRole !== 'Admin')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

            {{-- Regular User Responsive Links --}}
            @if(Auth::user()->accessRole !== 'Admin')
                <x-responsive-nav-link :href="route('request.create')" :active="request()->routeIs('request.create')">
                    {{ __('Make a New Request') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('approvals.index')" :active="request()->routeIs('approvals.index')" class="relative inline-flex items-center">
                    {{ __('Approvals') }}
                    @if(isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                        <span class="notification-badge inline-flex items-center justify-center h-4 w-4 text-xs font-semibold text-white bg-red-500 rounded-full -ml-1">
                            {{ $pendingApprovalCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>
                 @if((Auth::user()->position === 'Head' || Auth::user()->position === 'VPAA') && Auth::user()->accessRole === 'Approver')
                    <x-responsive-nav-link :href="route('approver-assignments.index')" :active="request()->routeIs('approver-assignments.index')">
                        {{ __('Manage Approvers') }}
                    </x-responsive-nav-link>
                @endif

                {{-- PFMO Job Orders Link --}}
                @if(Auth::user()->department && Auth::user()->department->dept_code === 'PFMO')
                    <x-responsive-nav-link :href="route('job-orders.index')" :active="request()->routeIs('job-orders.*')">
                        {{ __('Job Orders') }}
                    </x-responsive-nav-link>
                @endif

                {{-- Job Order Feedback Link (for all users) --}}
            @endif
            {{-- Admin Responsive Links --}}
            @if(Auth::user()->accessRole === 'Admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Admin Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.employee_list')" :active="request()->routeIs('admin.employee_list') || request()->routeIs('admin.employee_import_form')">
                    {{ __('Employee Information') }}
                </x-responsive-nav-link>
                {{-- Placeholder for User Management - We will create this route and page later --}}
                {{-- <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')"> --}}
                {{--     {{ __('User Management') }} --}}
                {{-- </x-responsive-nav-link> --}}
                {{-- Placeholder for Department Management - We will create this route and page later --}}
                {{-- <x-responsive-nav-link :href="route('admin.departments.index')" :active="request()->routeIs('admin.departments.index')"> --}}
                {{--     {{ __('Department Management') }} --}}
                {{-- </x-responsive-nav-link> --}}
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                 {{-- Display Username or Full Name in Responsive Dropdown --}}
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->username ?? Auth::user()->employeeInfo->FirstName }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->employeeInfo->Email ?? 'No email' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
