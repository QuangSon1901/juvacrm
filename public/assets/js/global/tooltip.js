$(function () {
    let tooltip; // Biến để lưu tooltip

    $(document).on('mouseenter', '[data-tooltip]', function (e) {
        const tooltipContent = $(this).attr('data-tooltip'); // Lấy nội dung tooltip
        const position = $(this).attr('data-position') || 'top'; // Lấy vị trí (mặc định là 'bottom')

        // Tạo tooltip nếu chưa có
        tooltip = $('<div>')
            .addClass('absolute bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50')
            .css({
                display: 'none',
                position: 'absolute'
            })
            .text(tooltipContent)
            .appendTo('body');

        const tooltipPos = $(this).offset();
        const elementHeight = $(this).outerHeight();
        const elementWidth = $(this).outerWidth();

        if (position === 'top') {
            tooltip.css({
                top: tooltipPos.top - tooltip.outerHeight() - 8,
                left: tooltipPos.left + elementWidth / 2 - tooltip.outerWidth() / 2,
            });
        } else if (position === 'bottom') {
            tooltip.css({
                top: tooltipPos.top + elementHeight + 8,
                left: tooltipPos.left + elementWidth / 2 - tooltip.outerWidth() / 2,
            });
        } else if (position === 'left') {
            tooltip.css({
                top: tooltipPos.top + elementHeight / 2 - tooltip.outerHeight() / 2,
                left: tooltipPos.left - tooltip.outerWidth() - 8
            });
        } else if (position === 'right') {
            tooltip.css({
                top: tooltipPos.top + elementHeight / 2 - tooltip.outerHeight() / 2,
                left: tooltipPos.left + elementWidth + 8,
            });
        }

        tooltip.fadeIn(300);
    });

    $(document).on('mouseleave', '[data-tooltip]', function () {
        if (tooltip) tooltip.remove();
    });
});
