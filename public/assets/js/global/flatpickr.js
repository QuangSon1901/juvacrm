function flatpickrMake(_element, type = 'date', options = {}) {
    let dateFormat = 'd-m-Y';
    switch (type) {
        case 'date':
            dateFormat = 'd-m-Y';
            break;
        case 'datetime':
            dateFormat = 'd-m-Y H:i:m';
            break;
        case 'time':
            dateFormat = 'H:i:m';
            break;
        default:
            dateFormat = type;
            break;
    }
    _element.flatpickr({
        dateFormat,
        enableTime: type != 'date',
        noCalendar: type == 'time',
        time_24hr: true,
        prevArrow: '<i class="ki-outline ki-left"></i>',
        nextArrow: '<i class="ki-outline ki-right"></i>',
        ...options
    });
}