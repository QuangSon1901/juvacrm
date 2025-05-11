<div class="scrollable-x-auto">
    <table class="table table-border" data-datatable-table="true" id="products_table">
        <thead>
            <tr>
                <th class="text-gray-700 font-normal w-[100px]">#</th>
                <th class="text-gray-700 font-normal min-w-[250px]">Tên sản phẩm</th>
                <th class="text-gray-700 font-normal min-w-[120px]">Trạng thái</th>
                <th class="text-gray-700 font-normal min-w-[150px]">Ngày tạo</th>
                <th class="w-[60px]"></th>
            </tr>
        </thead>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" data-datatable-spinner="true" style="display: none;">
            <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-2sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-light">
                <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </div>
        </div>
        <tbody>
            @if(isset($data) && count($data) > 0)
                @foreach($data as $product)
                <tr>
                    <td class="text-gray-800 font-normal">{{ $product['index'] }}</td>
                    <td class="text-sm text-gray-800 font-normal">{{ $product['name'] }}</td>
                    <td class="text-sm text-gray-800 font-normal">
                        @if(hasPermission('manage-assets'))
                        <label class="switch switch-sm">
                            <input class="product-status-toggle" data-id="{{ $product['id'] }}" {{ $product['is_active'] ? 'checked' : '' }} type="checkbox" value="1">
                        </label>
                        @else
                        <span class="badge badge-{{ $product['is_active'] ? 'success' : 'danger' }}">
                            {{ $product['is_active'] ? 'Hoạt động' : 'Vô hiệu' }}
                        </span>
                        @endif
                    </td>
                    <td class="text-sm text-gray-800 font-normal">
                        {{ date('d/m/Y', strtotime($product['created_at'])) }}
                    </td>
                    <td class="w-[60px]">
                        @if(hasPermission('manage-assets'))
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical"></i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <button class="menu-link btn-edit-product" data-id="{{ $product['id'] }}" data-name="{{ $product['name'] }}">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-pencil"></i>
                                            </span>
                                            <span class="menu-title">
                                                Chỉnh sửa
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">Không có dữ liệu sản phẩm</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
    <div class="flex items-center gap-2 order-2 md:order-1">
        Hiển thị
        <select class="select select-sm w-16" data-datatable-size="true" name="perpage">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="50">50</option>
        </select>
        mỗi trang
    </div>
    <div class="flex items-center gap-4 order-1 md:order-2">
        <span data-datatable-info="true">
            {{ $data->count() > 0 ? ($sorter['offset'] + 1) : 0 }}-{{ $sorter['offset'] + $data->count() }} trong {{ $sorter['sorterrecords'] }}
        </span>
        <div class="pagination" data-datatable-pagination="true">
            <div class="pagination">
                @if($sorter['sorterpage'] > 1)
                    <button class="btn" onclick="loadPage({{ $sorter['sorterpage'] - 1 }})">
                        <i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i>
                    </button>
                @else
                    <button class="btn disabled" disabled>
                        <i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i>
                    </button>
                @endif
                
                @for($i = 1; $i <= $sorter['totalpages']; $i++)
                    @if($i == $sorter['sorterpage'])
                        <button class="btn active disabled" disabled>{{ $i }}</button>
                    @else
                        <button class="btn" onclick="loadPage({{ $i }})">{{ $i }}</button>
                    @endif
                @endfor
                
                @if($sorter['sorterpage'] < $sorter['totalpages'])
                    <button class="btn" onclick="loadPage({{ $sorter['sorterpage'] + 1 }})">
                        <i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i>
                    </button>
                @else
                    <button class="btn disabled" disabled>
                        <i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function loadPage(page) {
        const statusFilter = $('#filter-status').val();
        
        let method = "get",
            url = "/product/data",
            params = {
                filter: {
                    status: statusFilter
                },
                page: page
            },
            data = null;
        
        axiosTemplate(method, url, params, data)
            .then(res => {
                if (res.data.status === 200) {
                    $('#products-container').html(res.data.content);
                    
                    // Initialize datatable again if needed
                    if (typeof initDatatable === 'function') {
                        initDatatable();
                    }
                }
            })
            .catch(error => {
                console.error('Error loading page:', error);
            });
    }
</script>