@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý thông báo
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="card">
        <div class="card-header py-5 flex-wrap gap-2">
            <h3 class="card-title">
                Danh sách thông báo
            </h3>
            <div class="flex flex-wrap gap-2 ml-auto">
                <div class="dropdown" data-dropdown="true">
                    <button class="dropdown-toggle btn btn-sm btn-light" data-dropdown-trigger="click" id="filter-dropdown">
                        <i class="ki-filled ki-filter me-2"></i>Lọc
                    </button>
                    <div class="dropdown-content light:border-gray-300 w-48">
                        <div class="menu-item">
                            <button class="menu-link notification-filter active" data-filter="all">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-notification"></i>
                                </span>
                                <span class="menu-title">Tất cả thông báo</span>
                            </button>
                        </div>
                        <div class="menu-item">
                            <button class="menu-link notification-filter" data-filter="unread">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-abstract-26"></i>
                                </span>
                                <span class="menu-title">Chưa đọc</span>
                            </button>
                        </div>
                        <div class="menu-item">
                            <button class="menu-link notification-filter" data-filter="read">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-check-square"></i>
                                </span>
                                <span class="menu-title">Đã đọc</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown" data-dropdown="true">
                    <button class="dropdown-toggle btn btn-sm btn-light" data-dropdown-trigger="click" id="action-dropdown">
                        <i class="ki-filled ki-gear me-2"></i>Thao tác
                    </button>
                    <div class="dropdown-content light:border-gray-300 w-56">
                        <div class="menu-item">
                            <button class="menu-link" id="mark-all-read-btn">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-check-square"></i>
                                </span>
                                <span class="menu-title">Đánh dấu tất cả đã đọc</span>
                            </button>
                        </div>
                        <div class="menu-item">
                            <button class="menu-link" id="delete-read-btn">
                                <span class="menu-icon text-danger">
                                    <i class="ki-filled ki-trash"></i>
                                </span>
                                <span class="menu-title text-danger">Xóa đã đọc</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div id="notifications-container" class="divide-y divide-gray-200">
                <!-- Nội dung thông báo sẽ được load thông qua AJAX -->
                <div class="flex justify-center items-center py-10">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="text-gray-600">Đang tải thông báo...</span>
                </div>
            </div>
            
            <div id="no-notifications" class="text-center py-20 hidden">
                <div class="mb-4">
                    <i class="ki-filled ki-notification text-gray-400 text-5xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-1">Không có thông báo</h3>
                <p class="text-gray-500">Bạn chưa có thông báo nào</p>
            </div>
        </div>
        
        <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
            <div class="flex items-center gap-2 order-2 md:order-1" id="pagination-info">
                Hiển thị <span id="current-page-size">0</span> thông báo
            </div>
            <div class="flex items-center gap-4 order-1 md:order-2">
                <div id="pagination" class="pagination"></div>
            </div>
        </div>
    </div>
</div>

<!-- Template cho thông báo riêng lẻ -->
<template id="notification-item-template">
    <div class="notification-item flex items-start p-5 hover:bg-light group/item" data-id="">
        <div class="relative shrink-0 mt-1 me-4">
            <div class="rounded-full size-10 flex items-center justify-center notification-icon">
                <i class="notification-icon-class text-xl"></i>
            </div>
            <span class="size-2 badge badge-circle absolute top-0 right-0 ring-1 ring-light transform translate-x-1/2 -translate-y-1/2 notification-status"></span>
        </div>
        
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start mb-1">
                <h4 class="notification-title text-base font-medium text-gray-900 mb-1 group-hover/item:text-primary"></h4>
                <span class="notification-time text-xs text-gray-500"></span>
            </div>
            <p class="notification-content text-sm text-gray-700 mb-2"></p>
            <div class="flex items-center gap-2">
                <div class="notification-actions flex items-center gap-2">
                    <button class="mark-read-btn btn btn-icon btn-xs btn-light-primary" title="Đánh dấu đã đọc">
                        <i class="ki-outline ki-check text-xs"></i>
                    </button>
                    <button class="delete-btn btn btn-icon btn-xs btn-light-danger" title="Xóa thông báo">
                        <i class="ki-outline ki-trash text-xs"></i>
                    </button>
                </div>
                <a href="#" class="ms-auto notification-link text-xs text-primary hover:text-primary-active flex items-center">
                    <span>Xem chi tiết</span>
                    <i class="ki-filled ki-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Khởi tạo dữ liệu
        let currentPage = 1;
        let currentFilter = 'all';
        
        // Load thông báo lần đầu
        loadNotifications();
        
        // Xử lý sự kiện bộ lọc
        $('.notification-filter').on('click', function() {
            $('.notification-filter').removeClass('active');
            $(this).addClass('active');
            
            currentFilter = $(this).data('filter');
            currentPage = 1;
            loadNotifications();
        });
        
        // Sự kiện đánh dấu tất cả là đã đọc
        $('#mark-all-read-btn').on('click', function() {
            Notiflix.Confirm.show(
                'Đánh dấu đã đọc',
                'Bạn có chắc muốn đánh dấu tất cả thông báo là đã đọc?',
                'Đồng ý',
                'Hủy',
                function() {
                    markAllAsRead();
                }
            );
        });
        
        // Sự kiện xóa tất cả thông báo đã đọc
        $('#delete-read-btn').on('click', function() {
            Notiflix.Confirm.show(
                'Xóa thông báo',
                'Bạn có chắc muốn xóa tất cả thông báo đã đọc?',
                'Đồng ý',
                'Hủy',
                function() {
                    deleteAllRead();
                }
            );
        });
        
        // Xử lý đánh dấu thông báo đã đọc (delegate cho các nút được tạo động)
        $(document).on('click', '.mark-read-btn', function() {
            const notificationId = $(this).closest('.notification-item').data('id');
            markAsRead(notificationId);
        });
        
        // Xử lý xóa thông báo (delegate cho các nút được tạo động)
        $(document).on('click', '.delete-btn', function() {
            const notificationId = $(this).closest('.notification-item').data('id');
            
            Notiflix.Confirm.show(
                'Xóa thông báo',
                'Bạn có chắc muốn xóa thông báo này?',
                'Đồng ý',
                'Hủy',
                function() {
                    deleteNotification(notificationId);
                }
            );
        });
        
        // Xử lý click vào thông báo để chuyển đến trang chi tiết
        $(document).on('click', '.notification-item', function(e) {
            // Không xử lý nếu click vào nút
            if ($(e.target).closest('button').length || $(e.target).closest('a').length) {
                return;
            }
            
            const notificationId = $(this).data('id');
            const url = $(this).find('.notification-link').attr('href');
            
            // Đánh dấu đã đọc và chuyển trang nếu có URL
            if (url && url !== '#') {
                markAsRead(notificationId, function() {
                    window.location.href = url;
                });
            }
        });
        
        // Sự kiện phân trang (sẽ được tạo động)
        $(document).on('click', '.pagination-item', function() {
            currentPage = $(this).data('page');
            loadNotifications();
        });
        
        // Hàm tải danh sách thông báo
        function loadNotifications() {
            let url = '/notifications/data';
            let params = {
                page: currentPage
            };
            
            // Thêm tham số bộ lọc
            if (currentFilter === 'unread') {
                params.is_read = 'false';
            } else if (currentFilter === 'read') {
                params.is_read = 'true';
            }
            
            // Hiển thị loading
            $('#notifications-container').html(`
                <div class="flex justify-center items-center py-10">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="text-gray-600">Đang tải thông báo...</span>
                </div>
            `);
            
            // Gọi API để lấy dữ liệu
            $.get(url, params, function(response) {
                if (response.status === 200) {
                    renderNotifications(response);
                } else {
                    showAlert('warning', 'Không thể tải thông báo');
                }
            }).fail(function() {
                showAlert('error', 'Có lỗi xảy ra khi tải thông báo');
            });
        }
        
        // Hàm hiển thị danh sách thông báo
        function renderNotifications(response) {
            const notifications = response.html;
            const pagination = response.pagination;
            
            // Nếu không có thông báo
            if (!notifications || notifications.trim() === '') {
                $('#notifications-container').addClass('hidden');
                $('#no-notifications').removeClass('hidden');
                return;
            }
            
            // Hiển thị dữ liệu
            $('#notifications-container').removeClass('hidden').html('');
            $('#no-notifications').addClass('hidden');
            
            // Tạo các phần tử thông báo
            const template = $('#notification-item-template').html();
            
            // Phân tích HTML từ response
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = notifications;
            
            // Lấy tất cả các phần tử thông báo từ HTML
            const notificationElements = $(tempDiv).find('.notification-item');
            
            // Tạo các phần tử thông báo
            notificationElements.each(function() {
                const notification = $(this);
                const isRead = notification.hasClass('is-read');
                const notificationId = notification.data('id');
                const title = notification.find('.notification-title').text();
                const content = notification.find('.notification-content').text();
                const iconClass = notification.find('.notification-icon-class').attr('class');
                const iconBgClass = notification.find('.notification-icon').attr('class');
                const time = notification.find('.notification-time').text();
                const link = notification.data('url') || '#';
                
                // Tạo phần tử thông báo từ template
                const notificationElement = $(template);
                notificationElement.attr('data-id', notificationId);
                notificationElement.find('.notification-title').text(title);
                notificationElement.find('.notification-content').text(content);
                notificationElement.find('.notification-icon-class').attr('class', iconClass);
                notificationElement.find('.notification-icon').addClass(iconBgClass);
                notificationElement.find('.notification-time').text(time);
                notificationElement.find('.notification-link').attr('href', link);
                
                // Cập nhật trạng thái đã đọc/chưa đọc
                if (isRead) {
                    notificationElement.addClass('bg-gray-50');
                    notificationElement.find('.notification-status').addClass('bg-gray-300');
                    notificationElement.find('.mark-read-btn').addClass('hidden');
                } else {
                    notificationElement.find('.notification-status').addClass('bg-primary');
                }
                
                // Thêm vào container
                $('#notifications-container').append(notificationElement);
            });
            
            // Cập nhật thông tin phân trang
            $('#current-page-size').text(notificationElements.length);
            $('#pagination').html(pagination);
        }
        
        // Đánh dấu thông báo đã đọc
        function markAsRead(notificationId, callback) {
            $.post('/notifications/mark-read', { id: notificationId }, function(response) {
                if (response.status === 200) {
                    const notification = $(`.notification-item[data-id="${notificationId}"]`);
                    notification.addClass('bg-gray-50');
                    notification.find('.notification-status').removeClass('bg-primary').addClass('bg-gray-300');
                    notification.find('.mark-read-btn').addClass('hidden');
                    
                    // Callback nếu cần
                    if (typeof callback === 'function') {
                        callback();
                    }
                } else {
                    showAlert('warning', response.message || 'Không thể đánh dấu thông báo đã đọc');
                }
            }).fail(function() {
                showAlert('error', 'Có lỗi xảy ra khi đánh dấu thông báo đã đọc');
            });
        }
        
        // Đánh dấu tất cả thông báo đã đọc
        function markAllAsRead() {
            $.post('/notifications/mark-read', {}, function(response) {
                if (response.status === 200) {
                    showAlert('success', 'Đã đánh dấu tất cả thông báo là đã đọc');
                    loadNotifications();
                } else {
                    showAlert('warning', response.message || 'Không thể đánh dấu tất cả thông báo là đã đọc');
                }
            }).fail(function() {
                showAlert('error', 'Có lỗi xảy ra khi đánh dấu tất cả thông báo là đã đọc');
            });
        }
        
        // Xóa một thông báo
        function deleteNotification(notificationId) {
            $.post('/notifications/delete', { id: notificationId }, function(response) {
                if (response.status === 200) {
                    $(`.notification-item[data-id="${notificationId}"]`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Kiểm tra nếu không còn thông báo nào
                        if ($('.notification-item').length === 0) {
                            $('#notifications-container').addClass('hidden');
                            $('#no-notifications').removeClass('hidden');
                        }
                    });
                    
                    showAlert('success', 'Đã xóa thông báo');
                } else {
                    showAlert('warning', response.message || 'Không thể xóa thông báo');
                }
            }).fail(function() {
                showAlert('error', 'Có lỗi xảy ra khi xóa thông báo');
            });
        }
        
        // Xóa tất cả thông báo đã đọc
        function deleteAllRead() {
            $.post('/notifications/delete', {}, function(response) {
                if (response.status === 200) {
                    showAlert('success', 'Đã xóa tất cả thông báo đã đọc');
                    loadNotifications();
                } else {
                    showAlert('warning', response.message || 'Không thể xóa thông báo đã đọc');
                }
            }).fail(function() {
                showAlert('error', 'Có lỗi xảy ra khi xóa thông báo đã đọc');
            });
        }
    });
</script>
@endpush