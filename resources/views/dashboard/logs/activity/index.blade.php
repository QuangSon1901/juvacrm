@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <!-- Container -->
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch sử hoạt động
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
        </div>
    </div>
    <!-- End of Container -->
</div>
<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- begin: activity -->
        <div class="flex gap-5 lg:gap-7.5">
            <div class="card grow" id="activity_2024">
                <div class="card-header">
                    <h3 class="card-title">
                        Tất cả hoạt động
                    </h3>
                </div>
                <div class="card-body">
                    <div class="flex flex-col">
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-people text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        Posted a new article
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/blogger">
                                            Top 10 Tech Trends
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        Today, 9:00 AM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-entrance-left text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        I had the privilege of interviewing an industry expert for an
                                        <a class="text-sm link" href="/metronic/tailwind/demo1/public-profile/profiles/blogger">
                                            upcoming blog post
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        2 days ago, 4:07 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-code text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col pb-2.5">
                                    <span class="text-sm text-gray-800">
                                        Jenny attended a Nature Photography Immersion workshop
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        3 days ago, 11:45 AM
                                    </span>
                                </div>
                                <div class="card shadow-none">
                                    <div class="card-body">
                                        <div class="grid gap-4">
                                            <div class="flex flex-col md:flex-row md:items-center gap-5">
                                                <div class="flex items-center gap-5 shrink-0">
                                                    <div class="border border-brand-clarity rounded-lg max-h-20">
                                                        <div class="flex items-center justify-center border-b border-b-brand-clarity bg-brand-light rounded-t-lg">
                                                            <span class="text-2sm text-brand font-medium p-2">
                                                                Apr
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center justify-center size-12">
                                                            <span class="font-medium text-gray-800 text-1.5xl tracking-tight">
                                                                02
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <img alt="" class="rounded-lg max-h-20 max-w-full" src="/static/metronic/tailwind/dist/assets/media/images/600x400/8.jpg">

                                                </div>
                                                <div class="flex flex-col gap-2">
                                                    <a class="text-xs text-brand leading-[14px] hover:text-primary-active mb-px" href="#">
                                                        Photo Workshop
                                                    </a>
                                                    <a class="text-md font-medium hover:text-primary text-gray-900 leading-4" href="#">
                                                        Nature Photography Immersion
                                                    </a>
                                                    <p class="text-xs text-gray-800 leading-[22px]">
                                                        Enhance your nature photography skills in a hands-on workshop guided by experienced photographers.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-share text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        I couldn't resist sharing a sneak peek of our
                                        <a class="text-sm link" href="/metronic/tailwind/demo1/public-profile/profiles/blogger">
                                            upcoming content
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        5 days ago, 4:07 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-calendar-tick text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col pb-2.5">
                                    <span class="text-sm text-gray-800">
                                        Jenny attended a webinar on new product features.
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        3 days ago, 11:45 AM
                                    </span>
                                </div>
                                <div class="card shadow-none p-4">
                                    <div class="flex flex-wrap gap-2.5">
                                        <i class="ki-filled ki-code text-lg text-info">
                                        </i>
                                        <div class="flex flex-col gap-5 grow">
                                            <div class="flex flex-wrap items-center justify-between">
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="text-md font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                                        Leadership Development Series: Part 1
                                                    </span>
                                                    <span class="text-xs text-gray-600">
                                                        The first installment of a leadership development series.
                                                    </span>
                                                </div>
                                                <a class="btn btn-link" href="/metronic/tailwind/demo1/account/members/teams">
                                                    View
                                                </a>
                                            </div>
                                            <div class="flex flex-wrap gap-7.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Code:
                                                    </span>
                                                    <a class="text-2sm text-primary" href="#">
                                                        #leaderdev-1
                                                    </a>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm text-gray-600">
                                                        Progress:
                                                    </span>
                                                    <div class="progress progress-success min-w-[120px]">
                                                        <div class="progress-bar" style="width: 80%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1.5 lg:min-w-24 shrink-0 max-w-auto">
                                                    <span class="text-2sm text-gray-600">
                                                        Guests:
                                                    </span>
                                                    <div class="flex -space-x-2">
                                                        <div class="flex">
                                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-4.png">
                                                        </div>
                                                        <div class="flex">
                                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-1.png">
                                                        </div>
                                                        <div class="flex">
                                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png">
                                                        </div>
                                                        <div class="flex">
                                                            <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-3xs size-7 text-primary-inverse ring-primary-light bg-primary">
                                                                +24
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-coffee text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-900">
                                        Reaching the milestone of
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/feeds">
                                            10,000 followers
                                        </a>
                                        on the blog was a dream come true
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        5 days ago, 4:07 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-rocket text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-900">
                                        Completed phase one of client project ahead of schedule.
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        6 days ago, 10:45 AM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-directbox-default text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col pb-2.5">
                                    <span class="text-sm text-gray-800">
                                        Attending the virtual blogging conference was an enriching experience
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        2 days ago, 4:07 PM
                                    </span>
                                </div>
                                <div class="card shadow-none">
                                    <div class="card-body lg:py-4">
                                        <div class="flex justify-center py-4">
                                            <img alt="image" class="dark:hidden max-h-[160px]" src="/static/metronic/tailwind/dist/assets/media/illustrations/28.svg">
                                            <img alt="image" class="light:hidden max-h-[160px]" src="/static/metronic/tailwind/dist/assets/media/illustrations/28-dark.svg">
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <div class="text-md font-medium text-gray-900 text-center">
                                                Blogging Conference
                                            </div>
                                            <div class="flex items-center justify-center gap-1">
                                                <a class="text-2sm font-semibold link" href="/metronic/tailwind/demo1/public-profile/profiles/company">
                                                    Axio new release
                                                </a>
                                                <span class="text-2sm text-gray-700 me-2">
                                                    email campaign
                                                </span>
                                                <span class="badge badge-sm badge-success badge-outline">
                                                    Public
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-design-1 text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        Onboarded a talented designer to our creative team, adding valuable expertise to upcoming projects.
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        2 weeks ago, 10:45 AM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-code text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="grow">
                                    <div class="flex flex-col pb-2.5">
                                        <div class="text-sm text-gray-800">
                                            A new team
                                            <a class="text-sm font-medium text-gray-900 hover:text-primary-active" href="#">
                                                Market Mavericks
                                            </a>
                                            joined community
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            1 month ago, 11:45 AM
                                        </span>
                                    </div>
                                    <div class="card shadow-none p-4">
                                        <div class="flex flex-wrap justify-between items-center gap-7">
                                            <div class="flex items-center gap-4">
                                                <div class="relative size-[50px] shrink-0">
                                                    <svg class="w-full h-full stroke-primary-clarity fill-primary-light" fill="none" height="48" viewBox="0 0 44 48" width="44" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16 2.4641C19.7128 0.320509 24.2872 0.320508 28 2.4641L37.6506 8.0359C41.3634 10.1795 43.6506 14.141 43.6506
              18.4282V29.5718C43.6506 33.859 41.3634 37.8205 37.6506 39.9641L28 45.5359C24.2872 47.6795 19.7128 47.6795 16 45.5359L6.34937
              39.9641C2.63655 37.8205 0.349365 33.859 0.349365 29.5718V18.4282C0.349365 14.141 2.63655 10.1795 6.34937 8.0359L16 2.4641Z" fill="none">
                                                        </path>
                                                        <path d="M16.25 2.89711C19.8081 0.842838 24.1919 0.842837 27.75 2.89711L37.4006 8.46891C40.9587 10.5232 43.1506 14.3196 43.1506
              18.4282V29.5718C43.1506 33.6804 40.9587 37.4768 37.4006 39.5311L27.75 45.1029C24.1919 47.1572 19.8081 47.1572 16.25 45.1029L6.59937
              39.5311C3.04125 37.4768 0.849365 33.6803 0.849365 29.5718V18.4282C0.849365 14.3196 3.04125 10.5232 6.59937 8.46891L16.25 2.89711Z" stroke="none" stroke-opacity="1">
                                                        </path>
                                                    </svg>
                                                    <div class="absolute leading-none start-2/4 top-2/4 -translate-y-2/4 -translate-x-2/4 rtl:translate-x-2/4">
                                                        <i class="ki-filled ki-abstract-39 text-xl ps-px text-primary">
                                                        </i>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-1.5">
                                                    <a class="text-base font-medium hover:text-primary text-gray-900" href="#">
                                                        Market Mavericks
                                                    </a>
                                                    <p class="text-2sm text-gray-700">
                                                        Navigating markets with strategic solutions
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-6 lg:gap-12">
                                                <div class="flex flex-col items-end gap-5">
                                                    <span class="text-2xs text-gray-600 uppercase">
                                                        rating
                                                    </span>
                                                    <div class="rating">
                                                        <div class="rating-label checked">
                                                            <i class="rating-on ki-solid ki-star text-base leading-none">
                                                            </i>
                                                            <i class="rating-off ki-outline ki-star text-base leading-none">
                                                            </i>
                                                        </div>
                                                        <div class="rating-label checked">
                                                            <i class="rating-on ki-solid ki-star text-base leading-none">
                                                            </i>
                                                            <i class="rating-off ki-outline ki-star text-base leading-none">
                                                            </i>
                                                        </div>
                                                        <div class="rating-label checked">
                                                            <i class="rating-on ki-solid ki-star text-base leading-none">
                                                            </i>
                                                            <i class="rating-off ki-outline ki-star text-base leading-none">
                                                            </i>
                                                        </div>
                                                        <div class="rating-label checked">
                                                            <i class="rating-on ki-solid ki-star text-base leading-none">
                                                            </i>
                                                            <i class="rating-off ki-outline ki-star text-base leading-none">
                                                            </i>
                                                        </div>
                                                        <div class="rating-label indeterminate">
                                                            <i class="rating-on ki-solid ki-star text-base leading-none" style="width: 50.0%">
                                                            </i>
                                                            <i class="rating-off ki-outline ki-star text-base leading-none">
                                                            </i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-end gap-3 lg:min-w-24 shrink-0 max-w-auto">
                                                    <span class="text-2xs text-gray-600 uppercase">
                                                        memebers
                                                    </span>
                                                    <div class="flex -space-x-2">
                                                        <div class="flex">
                                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-4.png">
                                                        </div>
                                                        <div class="flex">
                                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-1.png">
                                                        </div>
                                                        <div class="flex">
                                                            <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png">
                                                        </div>
                                                        <div class="flex">
                                                            <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-3xs size-7 text-primary-inverse ring-primary-light bg-primary">
                                                                S
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grid justify-end min-w-20">
                                                    <a class="btn btn-sm btn-primary">
                                                        <i class="ki-filled ki-people">
                                                        </i>
                                                        Join
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-like-shapes text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-800">
                                        Hosted a virtual
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/creator">
                                            team-building event
                                        </a>
                                        , fostering collaboration and strengthening bonds among team members.
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">
                                        1 month ago, 13:56 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-cup text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        We recently
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/nft">
                                            celebrated
                                        </a>
                                        the blog's 1-year anniversary
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        3 months ago, 4:07 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-center">
                    <a class="btn btn-link" href="#">
                        All-time Activity
                    </a>
                </div>
            </div>
            <div class="card grow hidden" id="activity_2023">
                <div class="card-header">
                    <h3 class="card-title">
                        Tất cả hoạt động
                    </h3>
                </div>
                <div class="card-body">
                    <div class="flex flex-col">
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-people text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        Posted a new article
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/blogger">
                                            Top 10 Tech Trends
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        Today, 9:00 AM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-share text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        I couldn't resist sharing a sneak peek of our
                                        <a class="text-sm link" href="/metronic/tailwind/demo1/public-profile/profiles/blogger">
                                            upcoming content
                                        </a>
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        5 days ago, 4:07 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-coffee text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-900">
                                        Reaching the milestone of
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/feeds">
                                            10,000 followers
                                        </a>
                                        on the blog was a dream come true
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        5 days ago, 4:07 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-design-1 text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        Onboarded a talented designer to our creative team, adding valuable expertise to upcoming projects.
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        2 weeks ago, 10:45 AM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                            </div>
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-like-shapes text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 mb-7 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-800">
                                        Hosted a virtual
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/creator">
                                            team-building event
                                        </a>
                                        , fostering collaboration and strengthening bonds among team members.
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">
                                        1 month ago, 13:56 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start relative">
                            <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                <i class="ki-filled ki-cup text-base">
                                </i>
                            </div>
                            <div class="ps-2.5 text-md grow">
                                <div class="flex flex-col">
                                    <div class="text-sm text-gray-800">
                                        We recently
                                        <a class="text-sm font-medium link" href="/metronic/tailwind/demo1/public-profile/profiles/nft">
                                            celebrated
                                        </a>
                                        the blog's 1-year anniversary
                                    </div>
                                    <span class="text-xs text-gray-600">
                                        3 months ago, 4:07 PM
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-center">
                    <a class="btn btn-link" href="#">
                        All-time Activity
                    </a>
                </div>
            </div>
            <div class="flex flex-col gap-2.5" data-tabs="true">
                <a class="btn btn-sm text-gray-600 hover:text-primary tab-active:bg-primary-light tab-active:text-primary active" data-tab-toggle="#activity_2024" href="#">
                    2024
                </a>
                <a class="btn btn-sm text-gray-600 hover:text-primary tab-active:bg-primary-light tab-active:text-primary" data-tab-toggle="#activity_2023" href="#">
                    2023
                </a>
            </div>
        </div>
        <!-- end: activity -->
    </div>
</div>
@endsection