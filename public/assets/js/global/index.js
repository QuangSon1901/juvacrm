$(function() {
    $(document).on('click', '.pagination button', function() {
        let _updater = $(this).closest('.card').find('.updater');
        let page = $(this).attr('data-page');

        $(this).closest('.card').find('.currentpage').val(page);
        callAjaxDataTable(_updater);
    })

    $(document).on('change', 'input[type=checkbox][data-filter], select[data-filter]', function() {
        let _updater = $(this).closest('.card').find('.updater');
        callAjaxDataTable(_updater);
    })

    $(document).on('keyup', 'input[type=text][data-filter], input[type=password][data-filter]', function() {
        if (event.keyCode === 13) {
            let _updater = $(this).closest('.card').find('.updater');
            callAjaxDataTable(_updater);
        }
    })

    loadDataTable();
})

function loadDataTable() {
    const _updater = $(".updater");

    if (_updater.length == 0) return;

    $.each(_updater, async (_, _this) => {
        await callAjaxDataTable($(_this));
    })
}

async function callAjaxDataTable(_this) {
    let _table = _this.closest('.card');
    let page = _table.find('.currentpage').val()
    let filter = {};

    _table.find('[data-filter]').each((_, item) => {
        let $item = $(item);
        filter[$item.attr('data-filter')] = $item.is(':checkbox') ? ($item.is(':checked') ? 1 : 0) : $item.val() ;
    });

    _table.find('.table-loader').removeClass("hidden");
    let method = "get",
        url = _table.find('.currentlist').val(),
        params = {
            page,
            filter
        },
        data = null;
    let res = await axiosTemplate(method, url, params, data);
    _table.find('.table-loader').addClass("hidden");
    
    _this.html(res.data.content);

    let perpage = res.data.sorter.perpage,
        sorterrecords = res.data.sorter.sorterrecords;

    page--;

    _table.find('.sorterlow').text(page * perpage + 1);
    _table.find('.sorterhigh').text(Math.min((page + 1) * perpage, sorterrecords));
    _table.find('.sorterrecords').text(sorterrecords);

    _table.find('.pagination').html(renderPagination(res.data.sorter));

}

function renderPagination(data) {
    const $paginationContainer = $("<div class='pagination'></div>");

    if (data.sorterpage === 1) {
        $paginationContainer.append("<button class='btn disabled' disabled><i class='ki-outline ki-black-left rtl:transform rtl:rotate-180'></i></button>");
    } else {
        $paginationContainer.append(`<button class='btn' data-page='${data.sorterpage - 1}'><i class='ki-outline ki-black-left rtl:transform rtl:rotate-180'></i></button>`);
    }

    for (let i = 1; i <= data.totalpages; i++) {
        if (i === data.sorterpage) {
            $paginationContainer.append(`<button class='btn active disabled' disabled>${i}</button>`);
        } else {
            $paginationContainer.append(`<button class='btn' data-page='${i}'>${i}</button>`);
        }
    }

    if (data.sorterpage === data.totalpages) {
        $paginationContainer.append("<button class='btn disabled' disabled><i class='ki-outline ki-black-right rtl:transform rtl:rotate-180'></i></button>");
    } else {
        $paginationContainer.append(`<button class='btn' data-page='${data.sorterpage + 1}'><i class='ki-outline ki-black-right rtl:transform rtl:rotate-180'></i></button>`);
    }

    return $paginationContainer;
}