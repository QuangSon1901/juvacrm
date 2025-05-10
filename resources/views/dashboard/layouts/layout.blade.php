<!DOCTYPE html>
<html class="h-full" data-theme="true" data-theme-mode="light" dir="ltr" lang="en">
@include('dashboard.layouts.head')

<body class="antialiased flex h-full text-base text-gray-700 [--tw-page-bg:#F6F6F9] [--tw-page-bg-dark:var(--tw-coal-200)] [--tw-content-bg:var(--tw-light)] [--tw-content-bg-dark:var(--tw-coal-500)] [--tw-content-scrollbar-color:#e8e8e8] [--tw-header-height:60px] [--tw-sidebar-width:90px] bg-custom-gradient">
    @include('dashboard.layouts.loader')
    <div class="flex grow">
        @include('dashboard.layouts.header')
        <div class="flex flex-col lg:flex-row grow pt-[--tw-header-height] lg:pt-0">
            @include('dashboard.layouts.sidebar')
            <div class="flex flex-col grow rounded-xl bg-[--tw-content-bg] dark:bg-[--tw-content-bg-dark] border border-gray-300 dark:border-gray-200 lg:ms-[--tw-sidebar-width] mt-0 lg:mt-5 m-5">
                <div class="flex flex-col grow lg:scrollable-y-auto lg:[scrollbar-width:auto] lg:light:[--tw-scrollbar-thumb-color:var(--tw-content-scrollbar-color)] pt-5" id="scrollable_content">
                    <main class="grow" role="content">
                        @yield('dashboard_content')
                    </main>
                </div>
                @include('dashboard.layouts.footer')
            </div>
        </div>
    </div>
    <div class="absolute hidden group-hover:block bg-gray-800 text-white text-sm px-4 py-2 rounded shadow-lg 
                transform -translate-x-1/2 translate-y-2 z-50"
        style="min-width: 150px;"
        id="tooltip-content">
        Tooltip nội dung
    </div>

    <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>

    <!-- Image Viewer Modal Component -->
    <div id="image-viewer" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 opacity-0 transition-opacity duration-300">
        <div class="relative max-h-[90vh] max-w-[90vw] transform scale-95 transition-transform duration-300">
            <!-- Close Button -->
            <button id="close-image-viewer" class="absolute -top-4 -right-4 flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-800 shadow-lg transition-all hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:-top-5 sm:-right-5 sm:h-12 sm:w-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            </button>
            
            <!-- Image Container with Loading State -->
            <div class="relative min-h-52 min-w-52 rounded-lg bg-white p-1 shadow-2xl sm:p-2">
                <!-- Loading Spinner -->
                <div id="image-loading" class="absolute inset-0 flex items-center justify-center bg-white/80">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                </div>
                
                <!-- Actual Image -->
                <img id="viewer-image" src="" alt="Enlarged view" class="h-auto max-h-[85vh] w-auto max-w-full rounded object-contain" onerror="this.src='/assets/images/default.svg'"/>
                
                <!-- Optional Caption -->
                <div id="image-caption" class="mt-2 px-2 text-center text-sm text-gray-700 sm:text-base"></div>
            </div>
        </div>
    </div>
    @include('dashboard.layouts.script')
    @stack('scripts')

    <!-- jQuery-based Image Viewer with Event Delegation -->
<script>
   $(document).ready(function() {
    // Initialize the image viewer
    window.ImageViewer = {
      // Cache jQuery elements
      $elements: {
        viewer: $('#image-viewer'),
        image: $('#viewer-image'),
        caption: $('#image-caption'),
        loading: $('#image-loading'),
        closeBtn: $('#close-image-viewer')
      },
      
      // Initialize the image viewer
      init: function() {
        // Set up event listeners
        this.setupEvents();
      },
      
      // Set up all event listeners (using event delegation for all images)
      setupEvents: function() {
        const self = this;
        const $elements = this.$elements;
        
        // Use event delegation for all images except those with .no-zoom class
        $(document).on('click', 'img:not(.no-zoom)', function() {
            if ($(this).closest('a').length == 0)
          self.open($(this).attr('src'), $(this).attr('alt'));
        });
        
        // Close button click
        $elements.closeBtn.on('click', function() {
          self.close();
        });
        
        // Click outside image
        $elements.viewer.on('click', function(e) {
          if (e.target === this) {
            self.close();
          }
        });
        
        // ESC key press
        $(document).on('keydown', function(e) {
          if (e.key === 'Escape' && $elements.viewer.hasClass('image-viewer-active')) {
            self.close();
          }
        });
      },
      
      transformCloudinaryUrl: function(url) {
        if (url && url.includes('cloudinary.com')) {
          return url.replace(/w_\d+,h_\d+,|h_\d+,w_\d+,|w_\d+,|h_\d+,/g, '');
        }
        
        // If not a Cloudinary URL, return original URL
        return url;
      },
      
      // Open the image viewer
      open: function(src, alt) {
        const $elements = this.$elements;
        
        // Transform URL if it's a Cloudinary URL
        const transformedSrc = this.transformCloudinaryUrl(src);
        
        $elements.loading.show();
        $elements.image.attr('src', transformedSrc);
        $elements.caption.text(alt || '');
        $elements.viewer.addClass('image-viewer-active');
        $('body').css('overflow', 'hidden');
        
        $elements.image.on('load', function() {
          $elements.loading.hide();
        });
      },
      
      // Close the image viewer
      close: function() {
        const $elements = this.$elements;
        
        $elements.viewer.removeClass('image-viewer-active');
        
        setTimeout(function() {
          $elements.image.attr('src', '');
          $('body').css('overflow', '');
        }, 300);
      }
    };
    
    // Initialize the image viewer
    window.ImageViewer.init();
});
  
  // Global API for opening images from anywhere
  window.openImage = function(src, caption) {
    window.ImageViewer.open(src, caption || '');
  };
</script>
<!-- Thêm vào phần script của resources/views/dashboard/layouts/layout.blade.php -->
<script>
    // Hàm cập nhật số lượng thông báo chưa đọc
    function updateNotificationBadge(count) {
        if (count > 0) {
            $('.notification-badge').text(count).removeClass('d-none');
        } else {
            $('.notification-badge').text('0').addClass('d-none');
        }
    }
    
    // Hàm load danh sách thông báo
    async function loadNotifications() {
        try {
            const res = await axiosTemplate('get', '{{ route("dashboard.notification.unread") }}');
            
            if (res.data.status === 200) {
                $('#notifications-list').html(res.data.html);
                updateNotificationBadge(res.data.unreadCount);
            }
        } catch (error) {
            console.error('Lỗi khi tải thông báo:', error);
            $('#notifications-list').html(`
                <div class="px-5 py-3 text-center">
                    <div class="text-gray-600">Không thể tải thông báo</div>
                </div>
            `);
        }
    }
    
    $(function() {
        // Load thông báo ban đầu
        loadNotifications();
        
        // Cập nhật thông báo mỗi 1 phút
        setInterval(loadNotifications, 60000);
        
        // Đánh dấu đã đọc khi click vào nút mark-read
        $(document).on('click', '.mark-read-btn', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = $(this).data('id');
            
            try {
                const res = await axiosTemplate('post', '{{ route("dashboard.notification.mark-read") }}', null, {
                    id: id
                });
                
                if (res.data.status === 200) {
                    // Tải lại thông báo sau khi đánh dấu đã đọc
                    loadNotifications();
                }
            } catch (error) {
                console.error('Lỗi khi đánh dấu đã đọc:', error);
            }
        });
        
        // Đánh dấu tất cả đã đọc
        $('#mark-all-read-btn').on('click', async function(e) {
            e.preventDefault();
            
            try {
                const res = await axiosTemplate('post', '{{ route("dashboard.notification.mark-read") }}');
                
                if (res.data.status === 200) {
                    // Tải lại thông báo sau khi đánh dấu tất cả đã đọc
                    loadNotifications();
                }
            } catch (error) {
                console.error('Lỗi khi đánh dấu tất cả đã đọc:', error);
            }
        });
        
        // Mở trang chi tiết khi click vào thông báo
        $(document).on('click', '.notification-content', function() {
            const url = $(this).data('url');
            if (url) {
                window.location.href = url;
            }
        });
        
        // Xử lý khi mở dropdown thông báo
        $('#notification-dropdown-toggle').on('click', function() {
            // Tải lại thông báo khi mở dropdown
            loadNotifications();
        });
    });
</script>
</body>

</html>