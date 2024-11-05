<!-- Sidebar -->
<div class="fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]" data-drawer="true" data-drawer-class="drawer drawer-start flex" data-drawer-enable="true|lg:false" id="sidebar">
    <div class="hidden lg:flex items-center justify-center shrink-0 pt-8 pb-3.5" id="sidebar_header">
        <a href="/metronic/tailwind/demo8/">
            <img class="dark:hidden h-[42px]" src="{{asset('assets/images/logo/favicon.png')}}" />
            <img class="hidden dark:block h-[42px]" src="{{asset('assets/images/logo/favicon.png')}}" />
        </a>
    </div>
    <div class="scrollable-y-hover grow gap-2.5 shrink-0 flex items-center pt-5 lg:pt-0 ps-3 pe-3 lg:pe-0 flex-col" data-scrollable="true" data-scrollable-dependencies="#sidebar_header,#sidebar_footer" data-scrollable-height="auto" data-scrollable-offset="80px" data-scrollable-wrappers="#sidebar_menu_wrapper" id="sidebar_menu_wrapper">
        <!-- Sidebar Menu -->
        <div class="menu flex flex-col gap-2.5 grow" data-menu="true" id="sidebar_menu">
            <div class="menu-item">
                <a class="menu-link rounded-[9px] border border-transparent menu-item-active:border-gray-200 menu-item-active:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2" href="/metronic/tailwind/demo8/">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-chart-line-star text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title text-xs menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600 font-medium">
                        Boards
                    </span>
                </a>
            </div>
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-profile-circle text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600">
                        Profiles
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Profiles
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/default">
                                    <span class="menu-title">
                                        Default
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/creator">
                                    <span class="menu-title">
                                        Creator
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/company">
                                    <span class="menu-title">
                                        Company
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/nft">
                                    <span class="menu-title">
                                        NFT
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/blogger">
                                    <span class="menu-title">
                                        Blogger
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/crm">
                                    <span class="menu-title">
                                        CRM
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                                <div class="menu-link grow cursor-pointer">
                                    <span class="menu-title">
                                        More
                                    </span>
                                    <span class="menu-arrow">
                                        <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                        </i>
                                    </span>
                                </div>
                                <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                                    <div class="menu-item">
                                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/gamer">
                                            <span class="menu-title">
                                                Gamer
                                            </span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/feeds">
                                            <span class="menu-title">
                                                Feeds
                                            </span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/plain">
                                            <span class="menu-title">
                                                Plain
                                            </span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/modal">
                                            <span class="menu-title">
                                                Modal
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Projects
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/projects/3-columns">
                                    <span class="menu-title">
                                        3 Columns
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/projects/2-columns">
                                    <span class="menu-title">
                                        2 Columns
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/works">
                            <span class="menu-title">
                                Works
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/teams">
                            <span class="menu-title">
                                Teams
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/network">
                            <span class="menu-title">
                                Network
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/activity">
                            <span class="menu-title">
                                Activity
                            </span>
                        </a>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                More
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/campaigns/card">
                                    <span class="menu-title">
                                        Campaigns - Card
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/campaigns/list">
                                    <span class="menu-title">
                                        Campaigns - List
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/empty">
                                    <span class="menu-title">
                                        Empty
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-item here" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-setting-2 text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600">
                        Account
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Account Home
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/home/get-started">
                                    <span class="menu-title">
                                        Get Started
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/home/user-profile">
                                    <span class="menu-title">
                                        User Profile
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/home/company-profile">
                                    <span class="menu-title">
                                        Company Profile
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/home/settings-sidebar">
                                    <span class="menu-title">
                                        Settings - With Sidebar
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/home/settings-enterprise">
                                    <span class="menu-title">
                                        Settings - Enterprise
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/home/settings-plain">
                                    <span class="menu-title">
                                        Settings - Plain
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/home/settings-modal">
                                    <span class="menu-title">
                                        Settings - Modal
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item here" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Billing
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item active">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/billing/basic">
                                    <span class="menu-title">
                                        Billing - Basic
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/billing/enterprise">
                                    <span class="menu-title">
                                        Billing - Enterprise
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/billing/plans">
                                    <span class="menu-title">
                                        Plans
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/billing/history">
                                    <span class="menu-title">
                                        Billing History
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Security
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/get-started">
                                    <span class="menu-title">
                                        Get Started
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/overview">
                                    <span class="menu-title">
                                        Security Overview
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/allowed-ip-addresses">
                                    <span class="menu-title">
                                        Allowed IP Addresses
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/privacy-settings">
                                    <span class="menu-title">
                                        Privacy Settings
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/device-management">
                                    <span class="menu-title">
                                        Device Management
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/backup-and-recovery">
                                    <span class="menu-title">
                                        Backup &amp; Recovery
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/current-sessions">
                                    <span class="menu-title">
                                        Current Sessions
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/security/security-log">
                                    <span class="menu-title">
                                        Security Log
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Members &amp; Roles
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/team-starter">
                                    <span class="menu-title">
                                        Teams Starter
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/teams">
                                    <span class="menu-title">
                                        Teams
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/team-info">
                                    <span class="menu-title">
                                        Team Info
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/members-starter">
                                    <span class="menu-title">
                                        Members Starter
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/team-members">
                                    <span class="menu-title">
                                        Team Members
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/import-members">
                                    <span class="menu-title">
                                        Import Members
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/roles">
                                    <span class="menu-title">
                                        Roles
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/permissions-toggle">
                                    <span class="menu-title">
                                        Permissions - Toggler
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/members/permissions-check">
                                    <span class="menu-title">
                                        Permissions - Check
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/account/integrations">
                            <span class="menu-title">
                                Integrations
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/account/notifications">
                            <span class="menu-title">
                                Notifications
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/account/api-keys">
                            <span class="menu-title">
                                API Keys
                            </span>
                        </a>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                More
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/appearance">
                                    <span class="menu-title">
                                        Appearance
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/invite-a-friend">
                                    <span class="menu-title">
                                        Invite a Friend
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/account/activity">
                                    <span class="menu-title">
                                        Activity
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-users text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600">
                        Network
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item">
                        <a class="menu-link" href="/metronic/tailwind/demo8/network/get-started">
                            <span class="menu-title">
                                Get Started
                            </span>
                        </a>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                User Cards
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-cards/mini-cards">
                                    <span class="menu-title">
                                        Mini Cards
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-cards/team-crew">
                                    <span class="menu-title">
                                        Team Crew
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-cards/author">
                                    <span class="menu-title">
                                        Author
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-cards/nft">
                                    <span class="menu-title">
                                        NFT
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-cards/social">
                                    <span class="menu-title">
                                        Social
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                User Table
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-table/team-crew">
                                    <span class="menu-title">
                                        Team Crew
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-table/app-roster">
                                    <span class="menu-title">
                                        App Roster
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-table/market-authors">
                                    <span class="menu-title">
                                        Market Authors
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-table/saas-users">
                                    <span class="menu-title">
                                        SaaS Users
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-table/store-clients">
                                    <span class="menu-title">
                                        Store Clients
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/metronic/tailwind/demo8/network/user-table/visitors">
                                    <span class="menu-title">
                                        Visitors
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-share text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600">
                        Help
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item">
                        <a class="menu-link" href="https://keenthemes.com/metronic/tailwind/docs/getting-started/installation">
                            <span class="menu-title">
                                Getting Started
                            </span>
                        </a>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Support Forum
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="https://devs.keenthemes.com">
                                    <span class="menu-title">
                                        All Questions
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="https://devs.keenthemes.com/popular">
                                    <span class="menu-title">
                                        Popular Questions
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="https://devs.keenthemes.com/question/create">
                                    <span class="menu-title">
                                        Ask Question
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="https://keenthemes.com/metronic/tailwind/docs/getting-started/license">
                            <span class="menu-title">
                                Licenses &amp; FAQ
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="https://keenthemes.com/metronic/tailwind/docs">
                            <span class="menu-title">
                                Documentation
                            </span>
                        </a>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="https://keenthemes.com/contact">
                            <span class="menu-title">
                                Contact Us
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Sidebar Menu -->
    </div>
    <div class="flex flex-col gap-5 items-center shrink-0 pb-4" id="sidebar_footer">
        <div class="flex flex-col gap-1.5">
            <div class="dropdown" data-dropdown="true" data-dropdown-offset="110px, 30px" data-dropdown-placement="right-end" data-dropdown-trigger="click|lg:click">
                <button class="dropdown-toggle btn btn-icon btn-icon-xl relative rounded-md size-9 border border-transparent hover:bg-light hover:text-primary hover:border-gray-200 dropdown-open:bg-gray-200 text-gray-600">
                    <span class="menu-icon">
                        <i class="ki-filled ki-messages">
                        </i>
                    </span>
                </button>
                <div class="dropdown-content light:border-gray-300 w-screen max-w-[450px]">
                    <div>
                        <div class="flex items-center justify-between gap-2.5 text-sm text-gray-900 font-semibold px-5 py-2.5">
                            Chat
                            <button class="btn btn-sm btn-icon btn-light btn-clear shrink-0" data-dropdown-dismiss="true">
                                <i class="ki-filled ki-cross">
                                </i>
                            </button>
                        </div>
                        <div class="border-b border-b-gray-200">
                        </div>
                        <div class="shadow-card border-b border-gray-200 py-2.5">
                            <div class="flex items-center justify-between flex-wrap gap-2 px-5">
                                <div class="flex items-center flex-wrap gap-2">
                                    <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-200 size-11">
                                        <img alt="" class="size-7" src="/static/metronic/tailwind/dist/assets/media/brand-logos/gitlab.svg" />
                                    </div>
                                    <div class="flex flex-col">
                                        <a class="text-2sm font-semibold text-gray-900 hover:text-primary-active" href="#">
                                            HR Team
                                        </a>
                                        <span class="text-2xs font-medium italic text-gray-500">
                                            Jessy is typing..
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="flex -space-x-2">
                                        <div class="flex">
                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-[30px]" src="/static/metronic/tailwind/dist/assets/media/avatars/300-4.png" />
                                        </div>
                                        <div class="flex">
                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-[30px]" src="/static/metronic/tailwind/dist/assets/media/avatars/300-1.png" />
                                        </div>
                                        <div class="flex">
                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-[30px]" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png" />
                                        </div>
                                        <div class="flex">
                                            <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-3xs size-[30px] text-success-inverse size-6 ring-success-light bg-success">
                                                +10
                                            </span>
                                        </div>
                                    </div>
                                    <div class="menu" data-menu="true">
                                        <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical">
                                                </i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                <div class="menu-item">
                                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/members/teams">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-users">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Invite Users
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-item" data-menu-item-offset="-15px, 0" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                                                    <div class="menu-link">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-people">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Team
                                                        </span>
                                                        <span class="menu-arrow">
                                                            <i class="ki-filled ki-right text-3xs rtl:transform rtl:rotate-180">
                                                            </i>
                                                        </span>
                                                    </div>
                                                    <div class="menu-dropdown menu-default w-full max-w-[175px]">
                                                        <div class="menu-item">
                                                            <a class="menu-link" href="/metronic/tailwind/demo8/account/members/import-members">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-shield-search">
                                                                    </i>
                                                                </span>
                                                                <span class="menu-title">
                                                                    Find Members
                                                                </span>
                                                            </a>
                                                        </div>
                                                        <div class="menu-item">
                                                            <a class="menu-link" href="/metronic/tailwind/demo8/account/members/import-members">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-calendar">
                                                                    </i>
                                                                </span>
                                                                <span class="menu-title">
                                                                    Meetings
                                                                </span>
                                                            </a>
                                                        </div>
                                                        <div class="menu-item">
                                                            <a class="menu-link" href="/metronic/tailwind/demo8/account/members/import-members">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-filter-edit">
                                                                    </i>
                                                                </span>
                                                                <span class="menu-title">
                                                                    Group Settings
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="menu-item">
                                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/security/privacy-settings">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-setting-3">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Settings
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="scrollable-y-auto" data-scrollable="true" data-scrollable-dependencies="#header" data-scrollable-max-height="auto" data-scrollable-offset="280px">
                        <div class="flex flex-col gap-5 py-5">
                            <div class="flex items-end gap-3.5 px-5">
                                <img alt="" class="rounded-full size-9" src="/static/metronic/tailwind/dist/assets/media/avatars/300-5.png" />
                                <div class="flex flex-col gap-1.5">
                                    <div class="card shadow-none flex flex-col bg-gray-100 gap-2.5 p-3 rounded-bl-none">
                                        <p class="text-2sm font-medium text-gray-700">
                                            Hello!
                                            <br />
                                            Next week we are closing the project. Do You have questions?
                                        </p>
                                    </div>
                                    <span class="text-2xs font-medium text-gray-500">
                                        14:04
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-end justify-end gap-3.5 px-5">
                                <div class="flex flex-col gap-1.5">
                                    <div class="card shadow-none flex bg-primary flex-col gap-2.5 p-3 rounded-br-none">
                                        <p class="text-2sm font-medium text-light">
                                            This is excellent news!
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-end relative">
                                        <span class="text-2xs font-medium text-gray-600 me-6">
                                            14:08
                                        </span>
                                        <i class="ki-filled ki-double-check text-lg absolute text-success">
                                        </i>
                                    </div>
                                </div>
                                <div class="relative shrink-0">
                                    <img alt="" class="rounded-full size-9" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png" />
                                    <span class="size-[4.8px] badge badge-circle badge-success absolute top-7 end-0 transform -translate-y-1/2">
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-end gap-3.5 px-5">
                                <img alt="" class="rounded-full size-9" src="/static/metronic/tailwind/dist/assets/media/avatars/300-4.png" />
                                <div class="flex flex-col gap-1.5">
                                    <div class="card shadow-none flex flex-col bg-gray-100 gap-2.5 p-3 rounded-bl-none">
                                        <p class="text-2sm font-medium text-gray-700">
                                            I have checked the features, can not wait to demo them!
                                        </p>
                                    </div>
                                    <span class="text-2xs font-medium text-gray-500">
                                        14:26
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-end gap-3.5 px-5">
                                <img alt="" class="rounded-full size-9" src="/static/metronic/tailwind/dist/assets/media/avatars/300-1.png" />
                                <div class="flex flex-col gap-1.5">
                                    <div class="card shadow-none flex flex-col bg-gray-100 gap-2.5 p-3 rounded-bl-none">
                                        <p class="text-2sm font-medium text-gray-700">
                                            I have looked over the rollout plan, and everything seems spot on. I am ready on my end and can not wait for the user feedback.
                                        </p>
                                    </div>
                                    <span class="text-2xs font-medium text-gray-500">
                                        15:09
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-end justify-end gap-3.5 px-5">
                                <div class="flex flex-col gap-1.5">
                                    <div class="card shadow-none flex bg-primary flex-col gap-2.5 p-3 rounded-br-none">
                                        <p class="text-2sm font-medium text-light">
                                            Haven't seen the build yet, I'll look now.
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-end relative">
                                        <span class="text-2xs font-medium text-gray-600 me-6">
                                            15:52
                                        </span>
                                        <i class="ki-filled ki-double-check text-lg absolute text-gray-400">
                                        </i>
                                    </div>
                                </div>
                                <div class="relative shrink-0">
                                    <img alt="" class="rounded-full size-9" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png" />
                                    <span class="size-[4.8px] badge badge-circle badge-success absolute top-7 end-0 transform -translate-y-1/2">
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-end justify-end gap-3.5 px-5">
                                <div class="flex flex-col gap-1.5">
                                    <div class="card shadow-none flex bg-primary flex-col gap-2.5 p-3 rounded-br-none">
                                        <p class="text-2sm font-medium text-light">
                                            Checking the build now
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-end relative">
                                        <span class="text-2xs font-medium text-gray-600 me-6">
                                            15:52
                                        </span>
                                        <i class="ki-filled ki-double-check text-lg absolute text-gray-400">
                                        </i>
                                    </div>
                                </div>
                                <div class="relative shrink-0">
                                    <img alt="" class="rounded-full size-9" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png" />
                                    <span class="size-[4.8px] badge badge-circle badge-success absolute top-7 end-0 transform -translate-y-1/2">
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-end gap-3.5 px-5">
                                <img alt="" class="rounded-full size-9" src="/static/metronic/tailwind/dist/assets/media/avatars/300-4.png" />
                                <div class="flex flex-col gap-1.5">
                                    <div class="card shadow-none flex flex-col bg-gray-100 gap-2.5 p-3 rounded-bl-none">
                                        <p class="text-2sm font-medium text-gray-700">
                                            Tomorrow, I will send the link for the meeting
                                        </p>
                                    </div>
                                    <span class="text-2xs font-medium text-gray-500">
                                        17:40
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2.5">
                        <div class="flex grow gap-2 p-5 bg-gray-100 mb-2.5" id="join_request">
                            <div class="relative shrink-0">
                                <img alt="" class="rounded-full size-8" src="/static/metronic/tailwind/dist/assets/media/avatars/300-14.png" />
                                <span class="size-1.5 badge badge-circle bg-gray-400 absolute top-7 end-0.5 ring-1 ring-light transform -translate-y-1/2">
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-3 grow">
                                <div class="flex flex-col">
                                    <div class="text-2sm mb-px">
                                        <a class="hover:text-primary-active font-semibold text-gray-900" href="#">
                                            Jane Perez
                                        </a>
                                        <span class="text-gray-600">
                                            wants to join chat
                                        </span>
                                    </div>
                                    <span class="flex items-center text-2xs font-medium text-gray-500">
                                        1 day ago
                                        <span class="badge badge-circle bg-gray-500 size-1 mx-1.5">
                                        </span>
                                        Design Team
                                    </span>
                                </div>
                                <div class="flex gap-2.5">
                                    <button class="btn btn-light btn-xs" data-dismiss="#join_request">
                                        Decline
                                    </button>
                                    <button class="btn btn-dark btn-xs">
                                        Accept
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="relative grow mx-5">
                            <img alt="" class="rounded-full size-[30px] absolute start-0 top-2/4 -translate-y-2/4 ms-2.5" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png" />
                            <input class="input h-auto py-4 ps-12 bg-transparent" placeholder="Write a message..." type="text" value="" />
                            <div class="flex items-center gap-2.5 absolute end-3 top-1/2 -translate-y-1/2">
                                <button class="btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-exit-up">
                                    </i>
                                </button>
                                <a class="btn btn-dark btn-sm" href="#">
                                    Send
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dropdown" data-dropdown="true" data-dropdown-offset="-20px, 30px" data-dropdown-placement="right-end" data-dropdown-trigger="click|lg:click">
                <button class="dropdown-toggle btn btn-icon btn-icon-xl relative rounded-md size-9 border border-transparent hover:bg-light hover:text-primary hover:border-gray-200 dropdown-open:bg-gray-200 text-gray-600">
                    <span class="menu-icon">
                        <i class="ki-filled ki-setting-2">
                        </i>
                    </span>
                </button>
                <div class="dropdown-content light:border-gray-300 w-screen max-w-[320px]">
                    <div class="flex items-center justify-between gap-2.5 text-2xs text-gray-600 font-medium px-5 py-3 border-b border-b-gray-200">
                        <span>
                            Apps
                        </span>
                        <span>
                            Enabled
                        </span>
                    </div>
                    <div class="flex flex-col scrollable-y-auto max-h-[400px] divide-y divide-gray-200">
                        <div class="flex items-center justify-between flex-wrap gap-2 px-5 py-3.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-200 size-10">
                                    <img alt="" class="size-6" src="/static/metronic/tailwind/dist/assets/media/brand-logos/jira.svg" />
                                </div>
                                <div class="flex flex-col">
                                    <a class="text-2sm font-semibold text-gray-900 hover:text-primary-active" href="#">
                                        Jira
                                    </a>
                                    <span class="text-2xs font-medium text-gray-600">
                                        Project management
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 lg:gap-5">
                                <label class="switch switch-sm">
                                    <input type="checkbox" value="2" />
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-between flex-wrap gap-2 px-5 py-3.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-200 size-10">
                                    <img alt="" class="size-6" src="/static/metronic/tailwind/dist/assets/media/brand-logos/inferno.svg" />
                                </div>
                                <div class="flex flex-col">
                                    <a class="text-2sm font-semibold text-gray-900 hover:text-primary-active" href="#">
                                        Inferno
                                    </a>
                                    <span class="text-2xs font-medium text-gray-600">
                                        Ensures healthcare app
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 lg:gap-5">
                                <label class="switch switch-sm">
                                    <input checked="" type="checkbox" value="1" />
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-between flex-wrap gap-2 px-5 py-3.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-200 size-10">
                                    <img alt="" class="size-6" src="/static/metronic/tailwind/dist/assets/media/brand-logos/evernote.svg" />
                                </div>
                                <div class="flex flex-col">
                                    <a class="text-2sm font-semibold text-gray-900 hover:text-primary-active" href="#">
                                        Evernote
                                    </a>
                                    <span class="text-2xs font-medium text-gray-600">
                                        Notes management app
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 lg:gap-5">
                                <label class="switch switch-sm">
                                    <input checked="" type="checkbox" value="1" />
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-between flex-wrap gap-2 px-5 py-3.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-200 size-10">
                                    <img alt="" class="size-6" src="/static/metronic/tailwind/dist/assets/media/brand-logos/gitlab.svg" />
                                </div>
                                <div class="flex flex-col">
                                    <a class="text-2sm font-semibold text-gray-900 hover:text-primary-active" href="#">
                                        Gitlab
                                    </a>
                                    <span class="text-2xs font-medium text-gray-600">
                                        DevOps platform
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 lg:gap-5">
                                <label class="switch switch-sm">
                                    <input type="checkbox" value="2" />
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-between flex-wrap gap-2 px-5 py-3.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-200 size-10">
                                    <img alt="" class="size-6" src="/static/metronic/tailwind/dist/assets/media/brand-logos/google-webdev.svg" />
                                </div>
                                <div class="flex flex-col">
                                    <a class="text-2sm font-semibold text-gray-900 hover:text-primary-active" href="#">
                                        Google webdev
                                    </a>
                                    <span class="text-2xs font-medium text-gray-600">
                                        Building web expierences
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 lg:gap-5">
                                <label class="switch switch-sm">
                                    <input checked="" type="checkbox" value="1" />
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="grid p-5 border-t border-t-gray-200">
                        <a class="btn btn-sm btn-light justify-center" href="/metronic/tailwind/demo8/account/api-keys">
                            Go to Apps
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="menu" data-menu="true">
            <div class="menu-item" data-menu-item-offset="-20px, 28px" data-menu-item-overflow="true" data-menu-item-placement="right-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <div class="menu-toggle btn btn-icon">
                    <img alt="" class="size-8 justify-center rounded-lg border border-gray-500 shrink-0" src="/static/metronic/tailwind/dist/assets/media/avatars/gray/5.png">
                    </img>
                </div>
                <div class="menu-dropdown menu-default light:border-gray-300 w-screen max-w-[250px]">
                    <div class="flex items-center justify-between px-5 py-1.5 gap-1.5">
                        <div class="flex items-center gap-2">
                            <img alt="" class="size-9 rounded-full border-2 border-success" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png">
                            <div class="flex flex-col gap-1.5">
                                <span class="text-sm text-gray-800 font-semibold leading-none">
                                    Cody Fisher
                                </span>
                                <a class="text-xs text-gray-600 hover:text-primary font-medium leading-none" href="/metronic/tailwind/demo8/account/home/get-started">
                                    c.fisher@gmail.com
                                </a>
                            </div>
                            </img>
                        </div>
                        <span class="badge badge-xs badge-primary badge-outline">
                            Pro
                        </span>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="flex flex-col">
                        <div class="menu-item">
                            <a class="menu-link" href="/metronic/tailwind/demo8/public-profile/profiles/default">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-badge">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    Public Profile
                                </span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="/metronic/tailwind/demo8/account/home/user-profile">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-profile-circle">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    My Profile
                                </span>
                            </a>
                        </div>
                        <div class="menu-item" data-menu-item-offset="-50px, 0" data-menu-item-placement="left-start" data-menu-item-placement-rtl="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                            <div class="menu-link">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-setting-2">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    My Account
                                </span>
                                <span class="menu-arrow">
                                    <i class="ki-filled ki-right text-3xs rtl:transform rtl:rotate-180">
                                    </i>
                                </span>
                            </div>
                            <div class="menu-dropdown menu-default light:border-gray-300 w-full max-w-[220px]">
                                <div class="menu-item">
                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/home/get-started">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-coffee">
                                            </i>
                                        </span>
                                        <span class="menu-title">
                                            Get Started
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/home/user-profile">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-some-files">
                                            </i>
                                        </span>
                                        <span class="menu-title">
                                            My Profile
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link" href="#">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-icon">
                                            </i>
                                        </span>
                                        <span class="menu-title">
                                            Billing
                                        </span>
                                        <span class="menu-badge" data-tooltip="true" data-tooltip-placement="top">
                                            <i class="ki-filled ki-information-2 text-md text-gray-500">
                                            </i>
                                            <span class="tooltip" data-tooltip-content="true">
                                                Payment and subscription info
                                            </span>
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/security/overview">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-medal-star">
                                            </i>
                                        </span>
                                        <span class="menu-title">
                                            Security
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/members/teams">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-setting">
                                            </i>
                                        </span>
                                        <span class="menu-title">
                                            Members &amp; Roles
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/integrations">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-switch">
                                            </i>
                                        </span>
                                        <span class="menu-title">
                                            Integrations
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-separator">
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link" href="/metronic/tailwind/demo8/account/security/overview">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-shield-tick">
                                            </i>
                                        </span>
                                        <span class="menu-title">
                                            Notifications
                                        </span>
                                        <label class="switch switch-sm">
                                            <input checked="" name="check" type="checkbox" value="1">
                                            </input>
                                        </label>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="https://devs.keenthemes.com">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-message-programming">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    Dev Forum
                                </span>
                            </a>
                        </div>
                        <div class="menu-item" data-menu-item-offset="-10px, 0" data-menu-item-placement="left-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                            <div class="menu-link">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-icon">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    Language
                                </span>
                                <div class="flex items-center gap-1.5 rounded-md border border-gray-300 text-gray-600 p-1.5 text-2xs font-medium shrink-0">
                                    English
                                    <img alt="" class="inline-block size-3.5 rounded-full" src="/static/metronic/tailwind/dist/assets/media/flags/united-states.svg" />
                                </div>
                            </div>
                            <div class="menu-dropdown menu-default light:border-gray-300 w-full max-w-[170px]">
                                <div class="menu-item active">
                                    <a class="menu-link h-10" href="?dir=ltr">
                                        <span class="menu-icon">
                                            <img alt="" class="inline-block size-4 rounded-full" src="/static/metronic/tailwind/dist/assets/media/flags/united-states.svg" />
                                        </span>
                                        <span class="menu-title">
                                            English
                                        </span>
                                        <span class="menu-badge">
                                            <i class="ki-solid ki-check-circle text-success text-base">
                                            </i>
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link h-10" href="?dir=rtl">
                                        <span class="menu-icon">
                                            <img alt="" class="inline-block size-4 rounded-full" src="/static/metronic/tailwind/dist/assets/media/flags/saudi-arabia.svg" />
                                        </span>
                                        <span class="menu-title">
                                            Arabic(Saudi)
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link h-10" href="?dir=ltr">
                                        <span class="menu-icon">
                                            <img alt="" class="inline-block size-4 rounded-full" src="/static/metronic/tailwind/dist/assets/media/flags/spain.svg" />
                                        </span>
                                        <span class="menu-title">
                                            Spanish
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link h-10" href="?dir=ltr">
                                        <span class="menu-icon">
                                            <img alt="" class="inline-block size-4 rounded-full" src="/static/metronic/tailwind/dist/assets/media/flags/germany.svg" />
                                        </span>
                                        <span class="menu-title">
                                            German
                                        </span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link h-10" href="?dir=ltr">
                                        <span class="menu-icon">
                                            <img alt="" class="inline-block size-4 rounded-full" src="/static/metronic/tailwind/dist/assets/media/flags/japan.svg" />
                                        </span>
                                        <span class="menu-title">
                                            Japanese
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="flex flex-col">
                        <div class="menu-item mb-0.5">
                            <div class="menu-link">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-moon">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    Dark Mode
                                </span>
                                <label class="switch switch-sm">
                                    <input data-theme-state="dark" data-theme-toggle="true" name="check" type="checkbox" value="1">
                                    </input>
                                </label>
                            </div>
                        </div>
                        <div class="menu-item px-4 py-1.5">
                            <a class="btn btn-sm btn-light justify-center" href="/metronic/tailwind/demo8/authentication/classic/sign-in">
                                Log out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Sidebar -->