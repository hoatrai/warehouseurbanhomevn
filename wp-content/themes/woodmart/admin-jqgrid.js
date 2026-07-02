
document.addEventListener("DOMContentLoaded", function () {
    // Chỉ chạy nếu là mobile
    if (window.innerWidth > 768) return;

    const filterContainer = document.getElementById("filter-container");
    const toggleBtn = document.getElementById("toggle-filter-btn");

    let isToggled = false;

    toggleBtn.style.display = "inline-block";
    filterContainer.style.display = "none"; // Mặc định ẩn

    toggleBtn.addEventListener("click", function () {
        if (filterContainer.style.display === "none" || filterContainer.style.display === "") {
            filterContainer.style.display = "flex";
            isToggled = true;
        } else {
            filterContainer.style.display = "none";
            isToggled = false;
        }
    });

    // Không cần lắng nghe resize vì chỉ cần xử lý khi load trang mobile thôi
});


jQuery(document).ready(function ($) {
    window.justAdded = false;

    function setupAutoCalc() {
        const $ngang = $('#dtd_ngang');
        const $dai = $('#dtd_dai');
        const $congNhan = $('#dtd_congnhan');

        function tinhDienTich() {
            const ngang = parseFloat($ngang.val()) || 0;
            const dai = parseFloat($dai.val()) || 0;
            const dienTich = ngang * dai;
            $congNhan.val(dienTich);
        }

        $ngang.on('input', tinhDienTich);
        $dai.on('input', tinhDienTich);

        // $congNhan.prop('readonly', true); // Không cho sửa tay
    }


    let isTrashView = false;
    // Fetch table list
// Function to get query parameters from the URL
    function getParameterByName(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }


    function loadDistricts(provinceId, selectedDistrictId = null) {
        $('#wp_district').prop('disabled', true).html('<option value="">Đang tải...</option>');
        $('#wp_wards').prop('disabled', true).html('<option value="">Chọn thông tin</option>');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            cache: false, // JQuery tự thêm tham số _=timestamp
            data: {
                action: 'load_wp_districts',
                wp_province_id: provinceId
            },
            success: function(response) {
                $('#wp_district').prop('disabled', false).html('<option value="">Chọn thông tin</option>');
                var districts = response.data.data || [];

                if (districts.length > 0) {
                    $.each(districts, function(index, item) {
                        $('#wp_district').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                }

                if (selectedDistrictId) {
                    $('#wp_district').val(selectedDistrictId).trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error('Load districts error:', error);
                alert('Không thể tải danh sách quận/huyện.');
            }
        });
    }

    function loadWards(districtId, selectedWardId = null) {
        $('#wp_wards').prop('disabled', true).html('<option value="">Đang tải...</option>');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            cache: false, // JQuery tự thêm tham số _=timestamp
            data: {
                action: 'load_wp_wards',
                wp_district_id: districtId
            },
            success: function(response) {
                $('#wp_wards').prop('disabled', false).html('<option value="">Chọn thông tin</option>');
                var wards = response.data.data || [];

                if (wards.length > 0) {
                    $.each(wards, function(index, item) {
                        $('#wp_wards').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                }

                if (selectedWardId) {
                    $('#wp_wards').val(selectedWardId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Load wards error:', error);
                alert('Không thể tải danh sách phường/xã.');
            }
        });
    }

    $.ajax({
        url: ajaxurl,
        method: "POST",
        dataType: "json",
        cache: false, // JQuery tự thêm tham số _=timestamp
        data: { action: "get_jqgrid_tables" },
        success: function (response) {
            if (!response.success || !response.tables) {
                console.error("Error: No table list received");
                return;
            }

            var $tableSelector = $("#tableSelector");
            $tableSelector.empty(); // Clear existing options

            // Populate dropdown
            $.each(response.tables, function (index, table) {
                $tableSelector.append($("<option>", {
                    value: table,
                    text: table
                }));

            });

            // Load first table by default
            /* if (response.tables.length > 0) {
             loadGrid(response.tables[0]);
             }*/
            // Get table name from URL
            var tableFromURL = getParameterByName("page");
            console.log(tableFromURL);

            // Select table from URL if available, otherwise load first table
            var selectedTable = tableFromURL && response.tables.includes(tableFromURL) ? tableFromURL : response.tables[0];

            $tableSelector.val(selectedTable);
            loadGrid(selectedTable); // Load the selected table

            // Change event for table selection
            $tableSelector.on("change", function () {
                loadGrid($(this).val());
            });

            $('#btn-danggiaodich').on('click', function () {
                $(this).css({
                    'background-color': '#1e7e34',  // màu xanh đậm hơn
                    'color': 'white',
                    'opacity': '1'
                });
                const statusValue = $(this).val(); // lấy value của button, vd: "657"
                window.customStatusFilter = statusValue;
                loadGrid(selectedTable);
            });

            $(document).on('click', '.statusBtn', function () {
                // Reset tất cả button
                $('.statusBtn').css({
                    'background-color': '#e5e5e5',
                    'color': '#555',
                    'opacity': '0.9',
                    'box-shadow': '0 2px 5px rgba(0,0,0,0.05)'
                });

                // Highlight button được chọn
                $(this).css({
                    'background-color': '#28a745',
                    'color': '#fff',
                    'opacity': '1',
                    'box-shadow': '0 4px 10px rgba(40, 167, 69, 0.3)'
                });

                const statusValue = $(this).val();
                window.customStatusFilter = statusValue;
                loadGrid(selectedTable);
            });
            $(document).on('click', '.sanphamhotBtn', function () {
                // Reset tất cả button
                $('.sanphamhotBtn').css({
                    'background-color': '#e5e5e5',
                    'color': '#555',
                    'opacity': '0.9',
                    'box-shadow': '0 2px 5px rgba(0,0,0,0.05)'
                });

                // Highlight button được chọn
                $(this).css({
                    'background-color': '#28a745',
                    'color': '#fff',
                    'opacity': '1',
                    'box-shadow': '0 4px 10px rgba(40, 167, 69, 0.3)'
                });

                const statusValueHot = $(this).val();
                window.customStatusFilterHot = statusValueHot;
                loadGrid(selectedTable);
            });
            $(document).on('click', '.thangmayBtn', function () {
                // Reset tất cả button
                $('.thangmayBtn').css({
                    'background-color': '#e5e5e5',
                    'color': '#555',
                    'opacity': '0.9',
                    'box-shadow': '0 2px 5px rgba(0,0,0,0.05)'
                });

                // Highlight button được chọn
                $(this).css({
                    'background-color': '#28a745',
                    'color': '#fff',
                    'opacity': '1',
                    'box-shadow': '0 4px 10px rgba(40, 167, 69, 0.3)'
                });

                const statusValueThangmay = $(this).val();
                window.customStatusFilterThangmay = statusValueThangmay;
                loadGrid(selectedTable);
            });
            $(document).on('click', '.sanphammoiBtn', function () {
                // Reset tất cả button
                $('.sanphammoiBtn').css({
                    'background-color': '#e5e5e5',
                    'color': '#555',
                    'opacity': '0.9',
                    'box-shadow': '0 2px 5px rgba(0,0,0,0.05)'
                });

                // Highlight button được chọn
                $(this).css({
                    'background-color': '#28a745',
                    'color': '#fff',
                    'opacity': '1',
                    'box-shadow': '0 4px 10px rgba(40, 167, 69, 0.3)'
                });

                const statusValueMoi = $(this).val();
                window.customStatusFilterMoi = statusValueMoi;
                loadGrid(selectedTable);
            });



            $('.statusGiatang, .statusGiagiam').on('click', function () {
                // Reset style tất cả nút sort (nếu có nhiều)
                $('.statusGiatang, .statusGiagiam').css({
                    'background-color': '',
                    'color': '',
                    'opacity': '0.95'
                });

                // Đổi style cho nút đang chọn
                $(this).css({
                    'background-color': '#1e7e34', // xanh đậm cho cả 2, bạn có thể chia ra nếu thích
                    'color': 'white',
                    'opacity': '1'
                });

                const sortOrder = $(this).val(); // "asc" hoặc "desc"
                window.customSortField = 'giaban';       // tên cột cần sort
                window.customSortOrder = sortOrder;      // hướng sort

                loadGrid(selectedTable);
            });





            // handle when filter custom
            $('#btn-filter').on('click', function () {
                loadGrid(selectedTable);
            });
            //console.log(selectedTable);

            $('#btn-reset').on('click', function () {
                if(selectedTable == 'wp_dulieunhadat'){
                    $('#filter-all').val('');
                    $('#filter-soto').val('');
                    $('#filter-sothua').val('');
                    $('#filter-province').val(null).trigger('change');
                    $('#filter-vitri').val(null).trigger('change');
                    $('#filter-ttk_huong').val(null).trigger('change');
                    $('#filter-producttype').val(null).trigger('change');
                    $('#filter-usernhanvien').val('');
                    $('#filter-tenduan').val(null).trigger('change');
                    $('#filter-width-from').val('');
                    $('#filter-width-to').val('');
                    $('#filter-long-from').val('');
                    $('#filter-long-to').val('');

                    $('#filter-giaban-from').val('');
                    $('#filter-giaban-to').val('');
                }
                if(selectedTable == 'wp_khachhang'){
                    $('#filter-ten').val('');
                    $('#filter-sodt').val('');
                    $('#filter-ghichu').val('');
                    $('#filter-loai').val(null).trigger('change');
                    $('#filter-trangthai').val(null).trigger('change');
                    $('#filter-user').val(null).trigger('change');
                }

                loadGrid(selectedTable);
            });

            // handle tinh thanh pho filter
            // Load tỉnh/thành phố từ PHP và gắn vào select2

            $('#filter-province').select2({
                placeholder: 'Chọn tỉnh / thành',
                allowClear: true,
                width: '200px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_province_list',
                            keyword: params.term || '' // <-- gửi từ khóa tìm kiếm
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });


// Sau khi khởi tạo, áp CSS trực tiếp để fix chiều cao
            setTimeout(function() {
                const $container = $('#filter-province').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);


            // Select con category parent_id = 557
            $('#filter-vitri').select2({
                placeholder: 'Chọn vị trí',
                allowClear: true,
                width: '200px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_child_category_list',
                            parent_id: 557,
                            q: params.term || ''
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });



// Fix CSS chiều cao select2 category 557
            setTimeout(function() {
                const $container = $('#filter-vitri').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);


            // Select con category parent_id = 567
            $('#filter-ttk_huong').select2({
                placeholder: 'Chọn hướng',
                allowClear: true,
                width: '200px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_child_category_list',
                            parent_id: 567,
                            q: params.term || ''
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });
            // Fix CSS chiều cao select2 category 567
            setTimeout(function() {
                const $container = $('#filter-ttk_huong').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);



// Select con category parent_id = 558
            $('#filter-producttype').select2({
                placeholder: 'Chọn loại sản phẩm',
                allowClear: true,
                width: '200px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_child_category_list',
                            parent_id: 513,
                            q: params.term || ''
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });
            setTimeout(function() {
                const $container = $('#filter-producttype').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);


            $('#filter-propertytype').select2({
                placeholder: 'Chọn loại BĐS',
                allowClear: true,
                width: '200px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_child_category_list',
                            parent_id: 519,
                            q: params.term || ''
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });
            setTimeout(function() {
                const $container = $('#filter-propertytype').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);



            $('#filter-usernhanvien').select2({
                placeholder: 'Sản phẩm của bạn',
                allowClear: true,
                width: '250px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_user_list_khohang',   // đổi action thành get_user_list
                            q: params.term || '',      // search term
                            // Nếu muốn lọc theo role, thêm dòng này, ví dụ role: 'subscriber'
                            // role: 'subscriber',
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });
            // Khi chọn giá trị, tự động click nút filter
            $('#filter-usernhanvien').on('change', function() {
                $('#btn-filter').trigger('click');
            });
            $('#filter-all').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // chặn submit form mặc định
                    $('#btn-filter').trigger('click');
                }
            });



// Fix CSS chiều cao select2 nhanvien
            setTimeout(function() {
                const $container = $('#filter-usernhanvien').next('.select2-container');

                $container.find('.select2-selection--single').css({
                    'height': '38px',
                    'line-height': '38px',
                    'padding': '0 16px',
                    'border-radius': '6px',
                    'border': 'none',
                    'box-shadow': '0 4px 8px rgba(44, 123, 229, 0.4)', // shadow xanh dịu
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px',
                    'font-weight': 'bold',
                    'background-color': '#FFA500', // nền xanh button
                    'color': '#fff',
                    'cursor': 'pointer',
                    'transition': 'background-color 0.3s ease'
                });
                $container.find('.select2-selection__placeholder').css({
                    'color': '#fff',
                    'opacity': '1'
                });


                // Khi mở dropdown, đổi nền đậm hơn
                $container.find('.select2-selection--single').on('click', function() {
                    $(this).css('background-color', '#FFA500');
                });
                // Khi dropdown đóng, trả về nền ban đầu
                $container.find('.select2-selection--single').on('blur', function() {
                    $(this).css('background-color', '#FFA500');
                });

                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%',
                    'color': '#fff'
                });

                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center',
                    'color': '#fff'
                });
            }, 10);


                $('#filter-tenduan').select2({
                    placeholder: 'Chọn tên dự án',
                    allowClear: true,
                    width: '200px',
                    multiple: true,          // ✅ cho phép chọn nhiều
                    closeOnSelect: false,    // ✅ không đóng dropdown khi chọn
                    ajax: {
                        url: ajaxurl,
                        method: "POST",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                action: 'get_duan_list',
                                keyword: params.term || '' // Gửi từ khóa tìm kiếm
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: response.data || []
                            };
                        },
                        cache: false
                    }
                });

                setTimeout(function () {
                    const $container = $('#filter-tenduan').next('.select2-container');

                    // Selection multiple (khung ngoài)
                    $container.find('.select2-selection--multiple').css({
                        'min-height': '22px',        // ⬇ thấp hơn nữa
                        'padding': '1px 3px',        // ⬇ giảm padding
                        'border-radius': '4px',
                        'border': '1px solid #ccc',
                        'display': 'flex',
                        'align-items': 'center',
                        'box-sizing': 'border-box'
                    });

                    // Rendered wrapper (chạy ngang)
                    $container.find('.select2-selection__rendered').css({
                        'display': 'flex',
                        'flex-direction': 'row',
                        'flex-wrap': 'nowrap',
                        'align-items': 'center',
                        'gap': '3px',
                        'padding': '0',
                        'margin': '0',
                        'overflow-x': 'auto'
                    });

                    // Choice tag
                    $container.find('.select2-selection__choice').css({
                        'display': 'inline-flex',
                        'width': 'auto',
                        'margin': '0',
                        'padding': '5px 5px',          // ⬇ mỏng lại
                        'font-size': '6px',       // ⬇ nhỏ font
                        'line-height': '14px',       // ⬇ thấp
                        'white-space': 'nowrap',
                        'border-radius': '3px'
                    });

                    // Input search
                    $container.find('.select2-search__field').css({
                        'margin': '0',
                        'height': '14px',
                        'line-height': '14px',
                        'font-size': '14px',
                        'min-width': '30px',
                        'padding-left': '8px',    // ✅ thêm dòng này
                        'padding-top': '4px'     // ✅ thêm dòng này
                    });


                }, 10);
            // 👉 Thu nhỏ font khi đã select
            function forceCompactFont() {
                const $container = $('#filter-tenduan').next('.select2-container');

                // TAG text
                $container.find('.select2-selection__choice').each(function () {
                    this.style.setProperty('font-size', '9px', 'important');
                    this.style.setProperty('line-height', '12px', 'important');
                    this.style.setProperty('padding', '2px 4px', 'important');
                });

                // Dấu X remove
                $container.find('.select2-selection__choice__remove').each(function () {
                    this.style.setProperty('font-size', '10px', 'important');
                    this.style.setProperty('line-height', '12px', 'important');
                });


            }


// Apply lần đầu
            setTimeout(forceCompactFont, 50);

// Apply lại mỗi khi select / unselect (vì select2 render lại DOM)
            $('#filter-tenduan').on('select2:select select2:unselect', function () {
                setTimeout(forceCompactFont, 10);
            });

            // Bắt Enter trong Select2 => trigger nút filter
            $(document).on('keydown', function (e) {
                if (e.key !== 'Enter') return;

                const $select2 = $('#filter-tenduan');
                const s2 = $select2.data('select2');

                if (!s2) return;

                // Chỉ khi select2 đang mở hoặc đang focus
                if (s2.isOpen() || $(document.activeElement).hasClass('select2-search__field')) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('ENTER DETECTED -> CLICK FILTER'); // debug
                    $('#btn-filter')[0].click();
                }
            });








// Sau khi khởi tạo, áp CSS trực tiếp để fix chiều cao
            /*setTimeout(function() {
                const $container = $('#filter-tenduan').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);*/


            // phan khach hang
            $('#filter-loai').select2({
                placeholder: 'Chọn loại',
                allowClear: true,
                width: '320px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_child_category_list',
                            parent_id: 688,
                            q: params.term || ''
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });


// Sau khi khởi tạo, áp CSS trực tiếp để fix chiều cao
            setTimeout(function() {
                const $container = $('#filter-loai').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);

            $('#filter-trangthai').select2({
                placeholder: 'Chọn trạng thái',
                allowClear: true,
                width: '320px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_child_category_list',
                            parent_id: 692,
                            q: params.term || ''
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });


// Sau khi khởi tạo, áp CSS trực tiếp để fix chiều cao
            setTimeout(function() {
                const $container = $('#filter-trangthai').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);

            $('#filter-user').select2({
                placeholder: 'Chọn môi giới',
                allowClear: true,
                width: '320px',
                ajax: {
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_user_list',   // đổi action thành get_user_list
                            q: params.term || '',      // search term
                            // Nếu muốn lọc theo role, thêm dòng này, ví dụ role: 'subscriber'
                            // role: 'subscriber',
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: false
                }
            });


// Sau khi khởi tạo, áp CSS trực tiếp để fix chiều cao
            setTimeout(function() {
                const $container = $('#filter-user').next('.select2-container');
                $container.find('.select2-selection--single').css({
                    'height': '40px',
                    'line-height': '40px',
                    'padding': '0 12px',
                    'border-radius': '6px',
                    'border': '1px solid #ccc',
                    'box-shadow': '1px 1px 4px rgba(0,0,0,0.1)',
                    'display': 'flex',
                    'align-items': 'center',
                    'box-sizing': 'border-box',
                    'font-size': '14px'
                });
                $container.find('.select2-selection__rendered').css({
                    'line-height': '40px',
                    'padding-left': '0',
                    'margin': '0',
                    'display': 'flex',
                    'align-items': 'center',
                    'height': '100%'
                });
                $container.find('.select2-selection__arrow').css({
                    'height': '40px',
                    'top': '0',
                    'right': '8px',
                    'display': 'flex',
                    'align-items': 'center'
                });
            }, 10);




        },
        error: function (){
            console.error("Failed to fetch table list");
        }

});

    // Function to load
    function loadGrid(selectedTable) {
        const keyword = $('#filter-all').val(); // Lấy từ input filter
        const soto = $('#filter-soto').val();   // Lấy giá trị Số Tờ
        const sothua = $('#filter-sothua').val(); // Lấy giá trị Số Thửa
        const province_id = $('#filter-province').val();
        const vitri = $('#filter-vitri').val();
        const ttk_huong = $('#filter-ttk_huong').val();
        const product_type = $('#filter-producttype').val();
        const property_type = $('#filter-propertytype').val();
        const usernhanvien = $('#filter-usernhanvien').val();
        const tenduan = $('#filter-tenduan').val();
        const widthfrom = $('#filter-width-from').val();
        const widthto = $('#filter-width-to').val();
        const longfrom = $('#filter-long-from').val();
        const longto = $('#filter-long-to').val();
        const giabanfrom = $('#filter-giaban-from').val();
        const giabanto = $('#filter-giaban-to').val();
        const dtdcongnhanfrom = $('#filter-dtdcongnhan-from').val();
        const dtdcongnhanto = $('#filter-dtdcongnhan-to').val();

        const trangthaigiaodich = window.customStatusFilter || '';
        const sanphamhot = window.customStatusFilterHot || '';
        const thangmay = window.customStatusFilterThangmay || '';
        const sanphammoi = window.customStatusFilterMoi || '';

        // 🔥 thêm sort field
        const sort_field = window.customSortField || 'id';
        const sort_order = window.customSortOrder || 'desc';

        var requestData = {
            action: "get_jqgrid_colmodel",
            table: selectedTable,
            keyword: keyword,
            soto: soto,
            sothua: sothua,
            province_id: province_id,
            vitri: vitri,
            ttk_huong: ttk_huong,
            product_type: product_type,
            property_type: property_type,
            usernhanvien: usernhanvien,
            tenduan: tenduan,
            widthfrom: widthfrom,
            widthto: widthto,
            longfrom: longfrom,
            longto: longto,
            giabanfrom: giabanfrom,
            giabanto: giabanto,
            dtdcongnhanfrom: dtdcongnhanfrom,
            dtdcongnhanto: dtdcongnhanto,
            trangthaigiaodich: trangthaigiaodich,
            sanphamhot: sanphamhot,
            thangmay: thangmay,
            sanphammoi: sanphammoi
        };

// Nếu bảng là wp_khachhang thì thêm loai
        const khten = $('#filter-ten').val();
        const khsodt = $('#filter-sodt').val();
        const khghichu = $('#filter-ghichu').val();
        const khloai = $('#filter-loai').val();
        const khtrangthai = $('#filter-trangthai').val();
        const khuser = $('#filter-user').val();
        if (selectedTable === "wp_khachhang") {
            requestData.khten = khten;
            requestData.khsodt = khsodt;
            requestData.khghichu = khghichu;
            requestData.khloai = khloai;
            requestData.khtrangthai = khtrangthai;
            requestData.khuser = khuser;
        }


        $.ajax({
            url: ajaxurl,
            method: "POST",
            dataType: "json",
            cache: false, // JQuery tự thêm tham số _=timestamp
            data: requestData,
            success: function (response) {
                if (!response.success || !response.colModel) {
                    console.error("Error: No colModel data received");
                    return;
                }

                var colModel = response.colModel;
                colModel.push({
                    name: 'actions',
                    label: '',
                    width: 90,
                    sortable: false,
                    align: 'center',
                    formatter: function (cellValue, options, rowObject) {
                        const createdTime = new Date(rowObject.datecreate);
                        createdTime.setHours(createdTime.getHours() + 7); // giờ VN

                        const now = new Date();
                        const diffMs = now - createdTime;

                        // Nếu đang ở bảng tạm (thùng rác) → hiện nút PHỤC HỒI
                        if (isTrashView) {
                            return '<button class="btn-restore" data-id="' + rowObject.id + '" '
                                + 'style="background: #e8f5e9; border: 1px solid #66bb6a; border-radius: 8px; padding: 6px; cursor: pointer;" '
                                + 'title="Khôi phục">'
                                + '<svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="#2e7d32" viewBox="0 0 24 24">'
                                + '<path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6a6 6 0 0 1-6 6 6 6 0 0 1-5.66-4H5.08c.5 4.02 3.9 7 7.92 7 4.42 0 8-3.58 8-8s-3.58-8-8-8z"/>'
                                + '</svg>'
                                + '</button>';
                        }

                        // Nếu KHÔNG có quyền sửa & đã quá 5 phút → ẩn nút
                        /* if (!ajax_object.can_edit && diffMs > 5 * 60 * 1000) {
                         return '';
                         }*/

                        // Ngược lại hiển thị nút CHỈNH SỬA
                        return '<button class="btn-edit" title="Bổ sung thông tin" '
                            + 'style="background: #e3f2fd; border: 1px solid #64b5f6; border-radius: 8px; padding: 6px; cursor: pointer;" '
                            + 'onmousedown="this.style.transform=\'scale(0.95)\'" '
                            + 'onmouseup="this.style.transform=\'scale(1)\'">'
                            + '<svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="#1976d2" viewBox="0 0 24 24">'
                            + '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>'
                            + '<path d="M14.06 8.94l1 1L5 20H4v-1L14.06 8.94zM15.5 7.5l1 1 1.06-1.06-1-1L15.5 7.5zM20.71 5.63a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0l-1.08 1.08 2.75 2.75 1.08-1.08z"/>'
                            + '</svg>'
                            + '</button>';
                    }
                });

                const urlParams = new URLSearchParams(window.location.search);
                const filterId = urlParams.get('id');
                $.jgrid.defaults.locale = "vi";

                // Tạo đối tượng postData với các tham số hiện có
                let postData = {
                    action: "load_jqgrid_data",
                    table: selectedTable,
                    keyword: keyword,
                    soto: soto,
                    sothua: sothua,
                    province_id: province_id,
                    vitri: vitri,
                    ttk_huong: ttk_huong,
                    product_type: product_type,
                    property_type: property_type,
                    usernhanvien: usernhanvien,
                    tenduan: tenduan,
                    widthfrom: widthfrom,
                    widthto: widthto,
                    longfrom: longfrom,
                    longto: longto,
                    giabanfrom: giabanfrom,
                    giabanto: giabanto,
                    dtdcongnhanfrom: dtdcongnhanfrom,
                    dtdcongnhanto: dtdcongnhanto,
                    trangthaigiaodich: trangthaigiaodich,
                    sanphamhot: sanphamhot,
                    thangmay: thangmay,
                    sanphammoi: sanphammoi,
                    // 🔥 Thêm vào đây
                    sort_field: sort_field,
                    sort_order: sort_order
                };
                // Nếu là bảng wp_khachhang thì thêm các trường khách hàng
                if (selectedTable === "wp_khachhang") {
                    postData.khten = khten;
                    postData.khsodt = khsodt;
                    postData.khghichu = khghichu;
                    postData.khloai = khloai;
                    postData.khtrangthai = khtrangthai;
                    postData.khuser = khuser;
                }

                // Nếu có id trong URL thì thêm filter_id vào postData
                if (filterId) {
                    postData.filter_id = filterId;
                }


                $grid = $("#jqGrid");
                $("#jqGrid").jqGrid("GridUnload"); // Unload previous grid
                $("#jqGrid").jqGrid({
                    styleUI: 'Bootstrap4',
                    url: ajaxurl,
                    datatype: "json",
                    mtype: "POST",
                    postData: postData,
                    colModel: colModel,
                    jsonReader: {
                        root: "rows",
                        repeatitems: false,
                        id: "id"
                    },
                    viewrecords: true,
                    sortorder: "desc",
                    sortable: true,
                    rowNum: 20,
                    autowidth: true,
                    shrinkToFit: true,
                    loadOnce: false,
                    multiselect: false,
                    forceFit: false,  // Allow full width of columns
                    height: "100%",
                    pager: "#jqGridPager",
                   // rowList: [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 100000000],
                    // recordtext: "Hiển thị {0} - {1} của {2}",
                    recordtext: "urbanhome.vn",
                    pgtext: "Trang {0}",
                    emptyrecords: "Không có dữ liệu",
                    selectable: true, // tuỳ phiên bản
                    loadError: function (xhr, status, error) {
                        console.log("Error:", status, error);
                        console.log("Response Text:", xhr.responseText);
                    },//formview
                    //ondblClickRow
                    onCellSelect: function(rowId) {
                        if (selectedTable === "wp_khachhang") {
                            return; // ❌ Không chạy gì nữa
                        }
                        // alert("Đang chạy JS mới");
                        if (!ajax_object.can_view) {
                            alert("Bạn không có quyền xem chi tiết.");
                            return;
                        }
                        if (rowId) {
//formview
                            $("#jqGrid").jqGrid('viewGridRow', rowId, {
                                recreateForm: true,
                                closeOnEscape: true,
                                closeAfterView: true,
                                modal: true,
                                width: 1000,
                                viewPagerButtons: false,
                                beforeShowForm: function(form) {
                                    form.closest('.ui-jqdialog').find('.ui-jqdialog-title').text("Thông tin chi tiết");
                                    form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').css({
                                        'height': '40px',
                                        'line-height': '30px',
                                        'padding': '4px 6px',
                                        'font-size': '14px'
                                    });

                                    // Gọi AJAX check/update contact_all
                                   /* $.ajax({
                                        url: ajax_object.ajaxurl,
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            action: "check_update_contact_all",
                                            row_id: rowId
                                        },
                                        success: function(res) {
                                            if (res.success) {
                                                console.log("✅ contact_all đã được check/update");
                                                // reload lại dữ liệu từ server
                                                $("#jqGrid").trigger("reloadGrid");
                                            } else {
                                                console.warn("⚠ Không update contact_all:", res.data);
                                            }
                                        }
                                    });*/



                                    form.hide();  // Ẩn form ngay khi mở
                                    form.find("tr").each(function() {
                                        // Kiểm tra label hoặc bạn có thể kiểm tra theo id hoặc name input
                                        if ($(this).attr("id") === "trv_wp_district" || $(this).attr("id") === "trv_wp_wards"
                                            || $(this).attr("id") === "trv_product_type" || $(this).attr("id") === "trv_transaction_type"
                                            || $(this).attr("id") === "trv_property_type" || $(this).attr("id") === "trv_wp_province"
                                            || $(this).attr("id") === "trv_vitri" || $(this).attr("id") === "trv_donvi_giaban"
                                            || $(this).attr("id") === "trv_vaitro" || $(this).attr("id") === "trv_loaitaisan"
                                            || $(this).attr("id") === "trv_tinhtranggiaodich" || $(this).attr("id") === "trv_gioitinh"
                                            || $(this).attr("id") === "trv_ttk_loaidat" || $(this).attr("id") === "trv_ttk_huong"
                                            || $(this).attr("id") === "trv_loaihoahong" || $(this).attr("id") === "trv_donvi_thoigianthue"
                                            || $(this).attr("id") === "trv_sohong_image_id" || $(this).attr("id") === "trv_bosung_image_id") {
                                            $(this).hide();
                                        }
                                    });
                                    // Hinh so hong
                                    /* var imgRow = form.find("#trv_sohong_image_id_link");
                                     if (imgRow.length > 0) {
                                     var val = imgRow.find("td.DataTD").text().trim();
                                     if (val && val.startsWith("http")) {
                                     imgRow.find("td.DataTD").html('<img src="' + val + '" style="max-width: 200px; border-radius: 6px;">');
                                     }
                                     }
                                     var imgRow = form.find("#trv_bosung_image_id_link");
                                     if (imgRow.length > 0) {
                                     var val = imgRow.find("td.DataTD").text().trim();
                                     if (val && val.startsWith("http")) {
                                     imgRow.find("td.DataTD").html('<img src="' + val + '" style="max-width: 200px; border-radius: 6px;">');
                                     }
                                     }*/
                                    form.closest(".ui-jqdialog").css({
                                        "font-family": "'Segoe UI', sans-serif",
                                        "font-size": "14px"
                                    });
                                    form.find(".CaptionTD").css({
                                        "font-weight": "bold",
                                        "color": "#555",
                                        "text-align": "right",
                                        "width": "30%",
                                        "padding-right": "10px"
                                    });
                                    form.find(".DataTD").css({
                                        "background": "#f7f7f7",
                                        "border-radius": "4px",
                                        "padding": "6px 10px"
                                    });



                                    // form: jQuery object của form popup

// Lấy tất cả các row (field) trong form
                                    var $rows = form.find("tr");

// Tạo container 2 cột
                                    var $container = $("<div style='display:flex; gap:20px;'></div>");

// Tạo 2 cột riêng biệt
                                    var $col1 = $("<div style='flex:1;'></div>");
                                    var $col2 = $("<div style='flex:1;'></div>");

// Phân chia các row vào 2 cột (ví dụ: chẵn sang col1, lẻ sang col2)
                                    $rows.each(function(index, row) {
                                        if (index % 2 === 0) {
                                            //$col1.append(row);
                                            $col1.append('');
                                        } else {
                                            //$col2.append(row);
                                            $col2.append('');
                                        }
                                    });

                                    // Hàm parse chuỗi datetime theo múi giờ VN (giờ VN = UTC +7)
                                    function parseDateTimeVN(dateTimeStr) {
                                        // dateTimeStr dạng "YYYY-MM-DD HH:mm:ss" hoặc "YYYY-MM-DD HH:mm"
                                        if (!dateTimeStr) return null;
                                        const [datePart, timePart = "00:00:00"] = dateTimeStr.split(' ');
                                        const [year, month, day] = datePart.split('-').map(Number);
                                        const [hour = 0, minute = 0, second = 0] = timePart.split(':').map(Number);
                                        // Tạo Date UTC trừ đi 7h để đúng múi giờ VN
                                        return new Date(Date.UTC(year, month - 1, day, hour - 7, minute, second));
                                    }

// Hàm thêm số 0 cho số nhỏ hơn 10
                                    function pad(num) {
                                        return num.toString().padStart(2, '0');
                                    }

// Hàm tính số tháng chênh lệch giữa 2 ngày
                                    function monthDiff(d1, d2) {
                                        let months = (d2.getFullYear() - d1.getFullYear()) * 12;
                                        months += d2.getMonth() - d1.getMonth();
                                        if (d2.getDate() < d1.getDate()) {
                                            months--; // chưa qua ngày trong tháng thì tính lùi lại
                                        }
                                        return months;
                                    }

// Hàm tính số ngày chênh lệch giữa 2 ngày
                                    function dayDiff(d1, d2) {
                                        const date1 = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
                                        const date2 = new Date(d2.getFullYear(), d2.getMonth(), d2.getDate());
                                        const diffTime = date2 - date1;
                                        return Math.round(diffTime / (1000 * 60 * 60 * 24));
                                    }

// --- Bắt đầu lấy dữ liệu và xử lý ---

// Lấy ID dòng đang được xem
                                    const rowData = $("#jqGrid").jqGrid('getRowData', rowId);
                                    console.log(rowData);
                                    const dateStr = rowData.datecreate;
                                    let displayText = '';

                                    if (!dateStr || dateStr.trim() === '' || dateStr === 'null') {
                                        displayText = '🕒 Không có thông tin ngày tạo';
                                    } else {
                                        // Parse ngày tạo theo múi giờ VN
                                        const createdDateUTC = parseDateTimeVN(dateStr);
                                        if (!createdDateUTC) {
                                            displayText = '🕒 Không có thông tin ngày tạo';
                                        } else {
                                            // Chuyển sang giờ VN (cộng 7 tiếng)
                                            const createdDateVN = new Date(createdDateUTC.getTime() + 7 * 60 * 60 * 1000);
                                            // Lấy thời gian hiện tại theo múi giờ VN
                                            const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Ho_Chi_Minh' }));

                                            const day = pad(createdDateVN.getDate());
                                            const month = pad(createdDateVN.getMonth() + 1);
                                            const hour = pad(createdDateVN.getHours());
                                            const minute = pad(createdDateVN.getMinutes());

                                            let timeNote = `(${day}-${month}) lúc ${hour}:${minute}`;

                                            const diffMonths = monthDiff(createdDateVN, now);
                                            if (diffMonths >= 1) {
                                                displayText = `🕒 Hơn ${diffMonths} tháng trước ${timeNote}`;
                                            } else {
                                                const days = dayDiff(createdDateVN, now);
                                                if (days <= 0) {
                                                    displayText = `🕒 Hôm nay ${timeNote}`;
                                                } else {
                                                    displayText = `🕒 ${days} ngày trước ${timeNote}`;
                                                }
                                            }
                                        }
                                    }



                                    function parseGia(giaStr) {
                                        // Loại bỏ dấu cách, chữ 'tỷ', chuyển thành triệu
                                        giaStr = giaStr.replace(/\s/g, '').toLowerCase();
                                        if (giaStr.includes('tỷ')) {
                                            const so = parseFloat(giaStr);
                                            return so * 1000; // triệu
                                        } else if (giaStr.includes('triệu')) {
                                            return parseFloat(giaStr);
                                        }
                                        return 0;
                                    }
                                    function formatVietnamCurrency(number) {
                                        if (number >= 1_000_000_000) {
                                            return (number / 1_000_000_000).toFixed((number % 1_000_000_000 === 0) ? 0 : 2) + ' tỷ';
                                        } else if (number >= 1_000_000) {
                                            return (number / 1_000_000).toFixed((number % 1_000_000 === 0) ? 0 : 2) + ' triệu';
                                        } else if (number >= 1_000) {
                                            return (number / 1_000).toFixed((number % 1_000 === 0) ? 0 : 2) + ' nghìn';
                                        } else {
                                            return number.toString();
                                        }
                                    }

                                    function parseDienTich(dtStr) {
                                        const match = dtStr.match(/[\d\.]+/); // lấy số đầu tiên trong chuỗi
                                        return match ? parseFloat(match[0]) : 0;
                                    }

                                    function formatCreatedDateCustom(rowData) {
                                        // Ưu tiên lấy dateupdate, nếu không có thì dùng datecreate
                                        let dateStr = rowData.dateupdate && rowData.dateupdate.trim() !== '' && rowData.dateupdate !== 'null'
                                            ? rowData.dateupdate
                                            : rowData.datecreate;

                                        if (!dateStr || dateStr.trim() === '' || dateStr === 'null') {
                                            return '🕒 Không có thông tin ngày tạo';
                                        }

                                        // Parse datetime (hàm này bạn phải có sẵn)
                                        const createdDateUTC = parseDateTimeVN(dateStr);
                                        if (!createdDateUTC) {
                                            return '🕒 Không có thông tin ngày tạo';
                                        }

                                        // Chuyển UTC → giờ VN
                                        const createdDateVN = new Date(createdDateUTC.getTime() + 7 * 60 * 60 * 1000);
                                        const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Ho_Chi_Minh' }));

                                        const day = pad(createdDateVN.getDate());
                                        const month = pad(createdDateVN.getMonth() + 1);
                                        const hour = pad(createdDateVN.getHours());
                                        const minute = pad(createdDateVN.getMinutes());

                                        const timeNote = `(${day}-${month}) cập nhật lúc ${hour}:${minute}`;

                                        // Tính chênh lệch tháng
                                        const diffMonths = monthDiff(createdDateVN, now);
                                        if (diffMonths >= 1) {
                                            return `🕒 Hơn ${diffMonths} tháng trước ${timeNote}`;
                                        }

                                        // Tính chênh lệch ngày
                                        const days = dayDiff(createdDateVN, now);
                                        if (days <= 0) {
                                            return `🕒 Hôm nay ${timeNote}`;
                                        } else if (days === 1) {
                                            return `🕒 Hôm qua ${timeNote}`;
                                        } else {
                                            return `🕒 ${days} ngày trước ${timeNote}`;
                                        }
                                    }





// Tính giá triệu / m²

                                    function tinhGiaTren1m2(giaStr, dtStr) {
                                        const giaTrieu = giaStr;
                                        const dienTich = parseDienTich(dtStr);

                                        if (giaTrieu > 0 && dienTich > 0) {
                                            const gia = giaTrieu / dienTich;
                                            return formatCurrency(gia) + ' triệu/m²';
                                        }
                                        return 'Không xác định';
                                    }

                                    function formatCurrency(number) {
                                        return Number(number).toLocaleString('vi-VN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }

                                    function formatPhoneNumber(phone) {
                                        // Xóa khoảng trắng, dấu gạch, hoặc dấu chấm
                                        phone = phone.replace(/[\s.-]/g, '');

                                        // Bỏ đầu +84 hoặc 0084, thay bằng 0
                                        if (phone.startsWith('+84')) {
                                            phone = '0' + phone.slice(3);
                                        } else if (phone.startsWith('0084')) {
                                            phone = '0' + phone.slice(4);
                                        }

                                        // Đảm bảo chỉ lấy số, loại bỏ ký tự không phải số
                                        phone = phone.replace(/\D/g, '');

                                        // Format lại thành 3-3-4 (ví dụ: 091 234 5678)
                                        if (phone.length === 10) {
                                            return phone.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
                                        } else if (phone.length === 11) {
                                            return phone.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
                                        }

                                        // Trả lại nguyên bản nếu không đúng độ dài
                                        return phone;
                                    }
                                    function formatHiddenPhoneNumber(phone) {
                                        // Chỉ lấy số (loại bỏ mọi ký tự không phải số)
                                        phone = phone.replace(/\D/g, '');

                                        // Lấy 3 số cuối
                                        const lastThree = phone.slice(-3);

                                        // Trả về định dạng ***.***.456
                                        return `**.***.${lastThree}`;
                                    }



                                    $.ajax({
                                        url: ajax_object.ajaxurl,
                                        method: 'POST',
                                        dataType: 'json',
                                        cache: false, // JQuery tự thêm tham số _=timestamp
                                        data: {
                                            action: 'get_dulieunhadat_goc_by_id',
                                            id: rowId
                                        },
                                        success: function(response) {
                                            if(response.success) {
                                                let rowData2 = response.data;
                                                let history = rowData2.history || []; // lấy mảng history từ data
                                                console.log('History data:', history);






                                                let multiImagesHtmlMain = `
<div style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
`;

                                                if (rowData.sohong_image_multi_urls && rowData.sohong_image_multi_urls.length > 0) {
                                                    let urls = [];

                                                    if (typeof rowData.sohong_image_multi_urls === 'string') {
                                                        urls = rowData.sohong_image_multi_urls.split(',');
                                                    } else if (Array.isArray(rowData.sohong_image_multi_urls)) {
                                                        urls = rowData.sohong_image_multi_urls;
                                                    }

                                                    if (urls.length > 0) {
                                                        urls.forEach((url) => {
                                                            url = url.trim();
                                                        const ext = url.split('.').pop().toLowerCase();
                                                        const isVideo = ['mp4', 'webm', 'ogg'].includes(ext);

                                                        if (isVideo) {
                                                            multiImagesHtmlMain += `
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <video src="${url}" controls
                        style="width: 120px; height: 90px; border-radius: 6px;
                               object-fit: cover; box-shadow: 0 2px 6px rgba(0,0,0,0.2); background: #000;"
                        preload="metadata" playsinline>
                    </video>
                    <a href="${url}" download
                        style="font-size: 12px; color: #007bff; margin-top: 4px; text-decoration: underline;">Tải video</a>
                </div>
                `;
                                                        } else {
                                                            multiImagesHtmlMain += `
                <a href="${url}" data-lightbox="sohong"
                    data-title='<a href="${url}" download style="color: white; text-decoration: underline;">Tải ảnh</a>'>
                    <img src="${url}" alt="Ảnh sổ hồng"
                        style="width: 120px; height: 90px; object-fit: cover; border-radius: 6px;
                               box-shadow: 0 2px 6px rgba(0,0,0,0.2);" />
                </a>
                `;
                                                        }
                                                    });
                                                    } else {
                                                        multiImagesHtmlMain += `<p style="color: #666;">Không có ảnh/video sổ hồng</p>`;
                                                    }
                                                } else {
                                                    multiImagesHtmlMain += `<p style="color: #666;">Không có ảnh/video sổ hồng</p>`;
                                                }

                                                multiImagesHtmlMain += '</div>';



                                                // fix new phan user
                                                history.forEach(item => {
                                                    const user = item.data || {};

                                                const infoThem =
                                                    '<div style="font-size:12px; color:#555; margin-top:4px; line-height:1.4; display:flex; gap:15px; flex-wrap:wrap;">' +
                                                    '<div>' + (user.user_login || '-') + '</div>' +
                                                    '<div>NV' + (user.userupdate || '-') + '</div>' +
                                                    '<div>' + formatCreatedDateCustom(user.dateupdate) + '</div>' +
                                                    '</div>';

                                                console.log(infoThem); // test in ra
                                            });

                                                // end fix new




                                                let ghiChuHtml = '';
                                                if (rowData.ghichu && rowData.ghichu.trim() !== '') {
                                                    // Xóa số điện thoại trong ghi chú bằng regex
                                                    let cleanedNote = rowData.ghichu
                                                    // Xoá số điện thoại
                                                        .replace(/(?:\+84|0)(?:\s?\d){8,11}/g, '')
                                                        // Xoá thẻ <img ...> kể cả emoji
                                                        .replace(/<img[^>]*>/gi, '')
                                                        // Xoá các đoạn escape của src="...svg"
                                                        .replace(/\\*"?\s*src=\\*"?https:\/\/s\.w\.org\/images\/core\/emoji[^ >]*>/gi, '');


                                                    ghiChuHtml =
                                                        '<div class="GroupNote" style="position:relative; margin-top:10px; padding:12px 50px 12px 40px; border:1px solid #ddd; border-radius:6px; background:#fff9e6; font-family:Arial, sans-serif; font-size:14px; color:#444; line-height:1.4; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">' +
                                                        '<i class="IconComment fas fa-comment-alt" style="position:absolute; left:12px; top:12px; font-size:20px; color:#f0a500;"></i>' +
                                                        '<div class="jInfoCopy" style="margin-right:50px; white-space: pre-line;">' +
                                                        cleanedNote +
                                                        '</div>' +
                                                        '<a href="javascript:void(0)" class="jCopyTextTarget" data-target=".jInfoCopy" data-noti="Nội dung ghi chú"' +
                                                        ' style="position:absolute; right:2px; top:2px; background:#ffffffbd; border-top-right-radius:5px; padding:5px; font-size:.9rem; cursor:pointer; color:#555; text-decoration:none;">' +
                                                        '<i class="fas fa-copy"></i> Copy' +
                                                        '</a>' +
                                                        '</div>';
                                                }

                                                function isValidPrice(value) {
                                                    return value && parseFloat(value) > 0;
                                                }
                                                console.log(rowData);
                                                const canViewPhone = rowData.can_view_phone === true || rowData.can_view_phone === 'true';

                                                // Tạo html chi tiết dài, kết hợp nhiều trường
                                                $col1.append(
                                                    '<section style="font-family: Arial, sans-serif; color: #333; background: #f9f9f9; padding: 15px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">' +

                                                    '<div style="display: flex; justify-content: space-between; margin-bottom: 15px;">' +
                                                    '<div style="flex: 1; margin-right: 20px;">' +
                                                    '<div style="font-weight: 600; font-size: 14px; margin-bottom: 5px;">Mã BĐS: ' +
                                                    '<a href="javascript:void(0)" copytext="12879" style="color: #007bff; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">' +
                                                    '<i class="far fa-copy" style="font-size: 16px;"></i> ' + rowData.id +
                                                    '</a>' +
                                                    '</div>' +
                                                    '<div style="font-size: 13px; color: #666;">Ngày Nhập: ' +
                                                    '<div style="display: inline-block;">' +
                                                    '<p style="margin: 0;">'+displayText+'</p>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>' +

                                                    '<div style="display: flex; align-items: center; gap: 15px; margin: 0px 13px;">' +
                                                    '<div class="avatar-wrapper" style="width: 60px !important; aspect-ratio: 1 / 1; border-radius: 50%; overflow: hidden; flex-shrink: 0;">' +
                                                    '<img src="' + rowData.user_avatar_url + '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">' +
                                                    '</div>' +
                                                    '</div>'+
                                                    '<div style="font-size: 14px; line-height: 1.5; color: darkgreen; font-weight: bold;">' +
                                                    '<div>Người Nhập: NV'+rowData.user+'</div>' +
                                                    '<div>'+rowData.user_first_name+' <b>'+rowData.user_last_name+'</b> ' +
                                                    '<span style="margin-left: 5px; cursor: pointer; color: #007bff;" title="Ngày vào làm: Gần 1 Năm (26-08-2024)">' +

                                                    '</span>' +
                                                    '</div>' +
                                                    '<div>'+formatPhoneNumber(rowData.user_phone)+'</div>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>' +

                                                    '<div style="border-top: 1px solid #ddd; padding-top: 15px;">' +
                                                    '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">' +
                                                    '<div style="font-size: 18px; font-weight: 700; color: #007bff; width:100%;">' +
                                                    '<div style="display: flex; gap: 8px;">' +
                                                    (rowData.getname_column_first
                                                        ? '<p style="text-decoration: underline; margin: 0; background: green;padding: 7px;border-radius: 4px;text-decoration: none;color: #FEE200;text-transform: uppercase;">' + rowData.getname_column_first + '</p><p style="text-align: right;color: #444;margin-left: 70px;">Số nhà / MC: </p><p style="color:red;font-size: 13px;">' + rowData.sonha +'('+rowData.code_product+')'+ '</p>'
                                                        : '') +
                                                    '</div>' +
                                                    (rowData.tinhtranggiaodich_name
                                                        ? '<div style="color: green; font-weight: 600; margin-top: 4px;">' + rowData.tinhtranggiaodich_name + '</div>'
                                                        : '') +
                                                    (rowData.khuvuc
                                                        ? '<div style="color: sienna; font-weight: 600; margin-top: 4px;">Khu vực ' + rowData.khuvuc + '</div>'
                                                        : '') +
                                                    '</div>' +


                                                    '</div>' +
                                                    '</div>'+
                                                    // Tạo địa chỉ
                                                    ((rowData.product_type == 517 || rowData.product_type == 515) &&
                                                    (rowData.name_area_name || rowData.sonha || rowData.road || rowData.wp_ward_name || rowData.wp_district_name || rowData.wp_province_name)
                                                        ? (function () {
                                                            const addressParts = [];

                                                            if (rowData.product_type == 517 && rowData.name_area_name) {
                                                                addressParts.push(rowData.name_area_name);
                                                            } else if (rowData.product_type == 515 && rowData.sonha) {
                                                                addressParts.push(rowData.sonha);
                                                            }

                                                            // Thêm các phần còn lại
                                                            addressParts.push(
                                                                rowData.road,
                                                                rowData.wp_ward_name,
                                                                rowData.wp_district_name,
                                                                rowData.wp_province_name
                                                            );

                                                            const address = addressParts.filter(Boolean).join(', ');

                                                            /*return (
                                                             '<a href="https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(address) + '" target="_blank" ' +
                                                             'style="display: flex; align-items: center; gap: 8px; color: #007bff; text-decoration: none; font-size: 14px;">' +
                                                             '<span style="color:red;font-weight: bold;text-align: right; font-size:12px;">' + address + '</span>' +
                                                             '<i class="fa fa-map-marker" style="color:red; margin-right:5px;"></i>' +
                                                             '</a>'
                                                             );*/
                                                            return (
                                                                '<a href="' + rowData['link_googlemap'] + '" target="_blank" ' +
                                                                'style="padding:5px; text-transform:uppercase; display: flex; align-items: center; gap: 8px; color: #007bff; text-decoration: none; font-size: 14px;">' +
                                                                '<span style="color:red; font-weight: bold; font-size:12px;">' + address + '</span>' +
                                                                '<div style="position: relative; width: 24px; height: 24px;">' +
                                                                '<i class="fas fa-map" style="font-size: 24px; color: #ccc;"></i>' +
                                                                '<i class="fas fa-map-marker-alt" style="color:#e60000; font-size:12px; position: absolute; top: 6px; left: 6px;"></i>' +
                                                                '</div>' +
                                                                '</a>'
                                                            );

                                                        })()
                                                        : '')+

                                                    '<div style="display: flex; gap: 30px; font-size: 14px; color: #444;">' +

                                                    // PHẦN GIÁ
                                                    (
                                                        (isValidPrice(rowData.giaban) || isValidPrice(rowData.giathue))
                                                            ? (
                                                            '<div>' +
                                                            '<p style="margin: 0 0 5px 0; font-weight: 600;">Giá</p>' +

                                                            (
                                                                (isValidPrice(rowData.giaban) && isValidPrice(rowData.giathue))
                                                                    ? (
                                                                    // ✅ Có cả giá bán và thuê
                                                                    '<p style="margin: 0; font-size: 16px; font-weight: 700; color:red;">' +
                                                                    formatVietnamCurrency(rowData.giaban) +
                                                                    ' <span style="font-weight: 400;">' +
                                                                    (rowData.donvi_giaban_name ? rowData.donvi_giaban_name : 'VNĐ') +
                                                                    '</span>' +
                                                                    '</p>' +
                                                                    '<p style="margin: 0; font-size: 13px; color: #666;">~ ' +
                                                                    tinhGiaTren1m2(rowData.giaban, rowData.dtd_congnhan) +
                                                                    '</p>' +
                                                                    '<p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 700; color:green;">' +
                                                                    'HĐ thuê: ' + formatVietnamCurrency(rowData.giathue) +
                                                                    ' <span style="font-weight: 400;">' +
                                                                    (rowData.donvi_giathue_name ? rowData.donvi_giathue_name : 'VNĐ') +
                                                                    '</span>' +
                                                                    '</p>'
                                                                )
                                                                    : isValidPrice(rowData.giathue)
                                                                    ? (
                                                                        // ✅ Chỉ có giá thuê
                                                                        '<p style="margin: 0; font-size: 16px; font-weight: 700; color:green;">' +
                                                                        formatVietnamCurrency(rowData.giathue) +
                                                                        ' <span style="font-weight: 400;">' +
                                                                        (rowData.donvi_giathue_name ? rowData.donvi_giathue_name : 'VNĐ') +
                                                                        '</span>' +
                                                                        '</p>'
                                                                    )
                                                                    : (
                                                                        // ✅ Chỉ có giá bán
                                                                        '<p style="margin: 0; font-size: 16px; font-weight: 700; color:red;">' +
                                                                        formatVietnamCurrency(rowData.giaban) +
                                                                        ' <span style="font-weight: 400;">' +
                                                                        (rowData.donvi_giaban_name ? rowData.donvi_giaban_name : 'VNĐ') +
                                                                        '</span>' +
                                                                        '</p>' +
                                                                        '<p style="margin: 0; font-size: 13px; color: #666;">~ ' +
                                                                        tinhGiaTren1m2(rowData.giaban, rowData.dtd_congnhan) +
                                                                        '</p>'
                                                                    )
                                                            ) +

                                                            '</div>'
                                                        )
                                                            : ''
                                                    )+

                                                    // Khối diện tích (luôn hiển thị)
                                                    '<div>' +
                                                    '<p style="margin: 0 0 5px 0; font-weight: 600;">Diện tích</p>' +
                                                    '<div>' +
                                                    '<p style="margin: 0; font-size: 16px; font-weight: 700;">' + rowData.dtd_congnhan + ' m2</p>' +
                                                    '<p style="margin: 0; font-size: 13px; color: #666;">(' + rowData.dtd_ngang + ' x ' + rowData.dtd_dai + ')</p>' +
                                                    '</div>' +
                                                    '</div>' +

                                                    '</div>'

                                                    +
                                                    '<div class="wrapcontact" style="max-height:300px;overflow-y:auto;padding:3px;' +
                                                    'border:1px solid #e5e7eb;border-radius:10px;-webkit-overflow-scrolling:touch;' +
                                                    'scrollbar-width:thin;scrollbar-color:#8b5cf6 #f3f4f6;">' +


                                                    (function () {
                                                        var html = '';
                                                        var renderContacts = [];

                                                        // Map trạng thái & vai trò
                                                        var statusMap = {
                                                            "681": { name: "Đã cọc", color: "#42a795" },
                                                            "677": { name: "Đã giao dịch", color: "#1621dc" },
                                                            "675": { name: "Đang giao dịch", color: "#28a745" },
                                                            "676": { name: "Ngưng giao dịch", color: "#6c757d" }
                                                        };
                                                        var roleMap = {
                                                            "627": "Chủ Nhà",
                                                            "639": "Đại Diện Chủ Nhà",
                                                            "637": "Đại Diện Công Ty",
                                                            "629": "Độc Quyền",
                                                            "631": "Môi Giới Hợp Tác",
                                                            "633": "Người Thân Chủ Nhà",
                                                            "635": "Trợ Lý Chủ Nhà"
                                                        };

                                                        function safeParse(json) {
                                                            if (!json) return null;
                                                            if (typeof json === 'object') return json;
                                                            try { return JSON.parse(json); }
                                                            catch (e) { console.warn("Parse lỗi:", e, json); return null; }
                                                        }

                                                        function pushContacts(source, sourceType, targetArr) {
                                                            if (!source || !source.contact_all) return;
                                                            var ca = safeParse(source.contact_all);
                                                            if (!ca) return;

                                                            // 👉 MAIN: luôn lấy contacts_primary
                                                            if (sourceType === "main" && ca.contacts_primary) {
                                                                targetArr.push({
                                                                    name: ca.contacts_primary.name,
                                                                    phone: ca.contacts_primary.dienthoaididong,
                                                                    vaitro: ca.contacts_primary.vaitro,
                                                                    status: source.tinhtranggiaodich || '',
                                                                    date: source.dateupdate || '',
                                                                    user_login: source.user_login || '',
                                                                    userupdate: rowData.userupdate || '',
                                                                    isPrimary: true,
                                                                    source: sourceType
                                                                });
                                                            }

                                                            // 👉 INFO: chỉ lấy cho main
                                                            if (sourceType === "main" && Array.isArray(ca.contacts_info)) {
                                                                ca.contacts_info.forEach(c => {
                                                                    targetArr.push({
                                                                    name: c.name_more,
                                                                    phone: c.phone_more,
                                                                    vaitro: c.vaitro_more,
                                                                    status: source.tinhtranggiaodich || '',
                                                                    date: source.dateupdate || '',
                                                                    user_login: source.user_login || '',
                                                                    userupdate: source.userupdate || '',
                                                                    isPrimary: false,
                                                                    source: sourceType
                                                                });
                                                            });
                                                            }
                                                        }

                                                        // =========================
                                                        // 👉 MAIN CONTACTS ONLY
                                                        // =========================
                                                        var mainArr = [];
                                                        pushContacts(rowData, "main", mainArr);

                                                        renderContacts = mainArr;

                                                        // =========================
                                                        // 👉 RENDER HTML
                                                        // =========================
                                                        renderContacts.forEach(c => {
                                                            var statusObj = statusMap[c.status] || {};
                                                        var statusName = statusObj.name || '';
                                                        var bgColor = statusObj.color || '#28a745';
                                                        var roleName = roleMap[c.vaitro] || '';
                                                        var primaryBadge = c.isPrimary
                                                            ? `<span style="font-size:11px; background:#ffc107; color:#222; padding:1px 4px; border-radius:4px; font-weight:600;">Chính</span>`
                                                            : '';

                                                        html += `
<div style="margin-top:12px; display:flex; flex-direction:column; gap:6px; padding:8px; border-radius:8px; background:#f9f9f9; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <div style="font-weight:600; font-size:14px; color:#222;">${c.name || ''}</div>
        ${canViewPhone ? `
        <a href="javascript:void(0)" class="show-phone-popup"
            data-phone="${c.phone || ''}"
            data-nhadat-id="${rowData.id || ''}"
            data-user="${rowData.user || ''}"
            data-name="${c.name || ''}"
            data-role="${roleName}"
            style="color:#007bff; text-decoration:none; font-size:13px;">
            <i class="fas fa-phone"></i>
            <span>${typeof formatHiddenPhoneNumber === 'function' ? formatHiddenPhoneNumber(c.phone) : c.phone}</span>
        </a>` : '<span style="color:#aaa; font-size:13px; font-style:italic;">Đã hết lượt xem hôm nay</span>'}
        ${roleName ? `<span style="font-size:11px; background:#eee; padding:1px 4px; border-radius:4px; font-weight:500; color:#555;">${roleName}</span>` : ''}
        ${statusName ? `<span style="font-size:11px; background:${bgColor}; color:#fff; padding:1px 4px; border-radius:4px; font-weight:500;">${statusName}</span>` : ''}
        ${primaryBadge}
    </div>
    ${c.user_login ? `
    <div style="font-size:12px; color:#555; margin-top:4px; line-height:1.4; display:flex; gap:10px; flex-wrap:wrap; border-top:1px solid #ddd; padding-top:4px;">
        <div style="font-weight:600; min-width:80px;">${c.user_login}</div>
        <div style="min-width:50px;">NV${c.userupdate}</div>
        <div style="color:#888;">${formatCreatedDateCustom({dateupdate: c.date})}</div>
    </div>` : ''}
</div>`;
                                                    });

                                                        return html;
                                                    })()








                                                +

                                                    '</div></div>'+

                                                    '<div class="GroupProducts" style="padding:10px; margin-top:10px;border:1px solid #ddd; border-radius:6px; background:#fafafa; font-family:Arial, sans-serif; font-size:14px; color:#333;">' +
                                                    '<div class="List">' +

                                                    (rowData.sonha
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-home"></i></div>' +
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số nhà</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.sonha + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData.solo
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-th-list"></i></div>' + // icon cho Số lô
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số lô</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.solo + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData.sothua
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-map-marker-alt"></i></div>' + // icon cho Số thửa
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số thửa</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.sothua + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData.soto
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-layer-group"></i></div>' + // icon cho Số tờ
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số tờ</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.soto + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData.tenduan
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-city"></i></div>' + // icon cho Tên dự án
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Tên dự án</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.tenduan + '</div>' +
                                                        '</div>'
                                                        : '')+



                                                    (rowData.vitri_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-map-pin"></i></div>' +
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Vị Trí</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.vitri_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.loaitaisan_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' + // thêm margin-bottom
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-building"></i></div>' +
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Loại Tài Sản</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.loaitaisan_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (
                                                        rowData.loaihoahong == 651 && rowData.hoahong_tyle
                                                            ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                            '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;">' +
                                                            '<i class="fas fa-percent"></i>' +
                                                            '</div>' +
                                                            '<div class="iLabel" style="flex:1; font-weight:600;">Hoa hồng</div>' +
                                                            '<div class="iValue" style="color:#222;">' + rowData.hoahong_tyle + ' %</div>' +
                                                            '</div>'
                                                            : rowData.loaihoahong == 653 && rowData.hoahong_thang
                                                            ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                            '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;">' +
                                                            '<i class="fas fa-calendar-alt"></i>' +
                                                            '</div>' +
                                                            '<div class="iLabel" style="flex:1; font-weight:600;">Hoa hồng</div>' +
                                                            '<div class="iValue" style="color:#222;">' + rowData.hoahong_thang + ' tháng</div>' +
                                                            '</div>'
                                                            : rowData.loaihoahong == 649 && rowData.hoahong_sotien
                                                                ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                                '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;">' +
                                                                '<i class="fas fa-money-bill-wave"></i>' +
                                                                '</div>' +
                                                                '<div class="iLabel" style="flex:1; font-weight:600;">Hoa hồng</div>' +
                                                                '<div class="iValue" style="color:#222;">' + Number(rowData.hoahong_sotien).toLocaleString('vi-VN') + ' ₫</div>' +
                                                                '</div>'
                                                                : ''
                                                    )+


                                                    '</div>' +
                                                    '</div>'+


                                                    '<div class="GroupProducts" style="padding:10px; margin-top:10px;border:1px solid #ddd; border-radius:6px; background:#fafafa; font-family:Arial, sans-serif; font-size:14px; color:#333;">' +
                                                    '<div class="List" style="font-weight:bold; color:green; padding:10px;"><strong>🔎 Thông tin khác: </strong></div>' +


                                                    (rowData.ttk_loaidat_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-tree"></i></div>' + // icon đất đai
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Loại đất</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_loaidat_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.ttk_sophongngu
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-bed"></i></div>' + // icon phòng ngủ
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số phòng ngủ</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_sophongngu + ' phòng</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.ttk_sotang
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-building"></i></div>' + // icon số tầng
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số tầng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_sotang + ' tầng</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.ttk_sotangham
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-warehouse"></i></div>' + // icon tầng hầm
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số tầng hầm</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_sotangham + ' hầm</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.ttk_swc
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-toilet"></i></div>' + // icon toilet
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số toilet</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_swc + ' phòng</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.ttk_dtsd
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-ruler-combined"></i></div>' + // icon diện tích
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Diện tích sử dụng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_dtsd + ' m2</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.ttk_huong_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-compass"></i></div>' + // icon hướng
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Hướng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_huong_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData.ttk_duongrong
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-road"></i></div>' + // icon hẻm rộng
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Hẻm Rộng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData.ttk_duongrong + ' <span class="iUnit">m</span></div>' +
                                                        '</div>'
                                                        : '')+

                                                    '</div>' +
                                                    '</div>'+

                                                    ghiChuHtml +
                                                    '<a href="javascript:void(0)" class="jCopyTextTarget" data-target=".jInfoCopy" data-noti="Nội dung ghi chú"' +
                                                    ' style="position:absolute; right:2px; top:2px; background:#ffffffbd; border-top-right-radius:5px; padding:5px; font-size:.9rem; cursor:pointer; color:#555; text-decoration:none;">' +

                                                    '</a>' +
                                                    '</div>'
                                                    /* + '<div style="margin-top: 15px; text-align: center;">' +
                                                     '<a href="'+rowData.sohong_image_id_link+'" download target="_blank" style="display: inline-block;">' +
                                                     '<img src="'+rowData.sohong_image_id_link+'" alt="Sổ Hồng" style="width: 160px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">' +
                                                     '<div style="margin-top: 5px; color: #007bff; font-size: 14px;">Click vào hình để tải Sổ Hồng</div>' +
                                                     '</a>' +
                                                     '</div>'*/ +
                                                    multiImagesHtmlMain +
                                                    '</section>'
                                                );






                                                console.log('history:', history);
                                                console.log('typeof history:', typeof history);
                                                console.log('isArray:', Array.isArray(history));

                                                // 1. Append từng phần history lên trước
// 1. Lấy dateupdate mới nhất trong history (nếu có)
                                                // Hàm parse chuỗi datetime theo múi giờ VN (giờ VN = UTC +7)
                                                function parseDateTimeVN(dateTimeStr) {
                                                    // dateTimeStr dạng "YYYY-MM-DD HH:mm:ss" hoặc "YYYY-MM-DD HH:mm"
                                                    const [datePart, timePart = "00:00:00"] = dateTimeStr.split(' ');
                                                    const [year, month, day] = datePart.split('-').map(Number);
                                                    const [hour = 0, minute = 0, second = 0] = timePart.split(':').map(Number);
                                                    // Tạo Date UTC trừ đi 7h để đúng múi giờ VN
                                                    return new Date(Date.UTC(year, month - 1, day, hour - 7, minute, second));
                                                }

// Hàm thêm số 0 cho số nhỏ hơn 10
                                                function pad(n) {
                                                    return n < 10 ? '0' + n : n;
                                                }

// Hàm tính số tháng chênh lệch giữa 2 ngày
                                                function monthDiff(d1, d2) {
                                                    let months = (d2.getFullYear() - d1.getFullYear()) * 12;
                                                    months += d2.getMonth() - d1.getMonth();
                                                    if (d2.getDate() < d1.getDate()) {
                                                        months--; // chưa qua ngày trong tháng thì tính lùi lại
                                                    }
                                                    return months;
                                                }

// Hàm tính số ngày chênh lệch giữa 2 ngày
                                                function dayDiff(d1, d2) {
                                                    const date1 = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
                                                    const date2 = new Date(d2.getFullYear(), d2.getMonth(), d2.getDate());
                                                    const diffTime = date2 - date1;
                                                    return Math.round(diffTime / (1000 * 60 * 60 * 24));
                                                }

// Biến chứa text hiển thị ngày cập nhật
                                                let displayText2 = '🕒 Không có thông tin ngày cập nhật';

                                                if (history.length > 0) {
                                                    // Lọc ra các phần tử có dateupdate hợp lệ
                                                    const validDates = history
                                                            .map(item => item.dateupdate)
                                                .filter(d => d && d.trim() !== '' && d !== 'null');

                                                    if (validDates.length > 0) {
                                                        // Sắp xếp để lấy ngày cập nhật mới nhất (theo múi giờ VN)
                                                        validDates.sort((a, b) => parseDateTimeVN(b) - parseDateTimeVN(a));
                                                        const latestUpdateStr = validDates[0];
                                                        const updatedDateUTC = parseDateTimeVN(latestUpdateStr);

                                                        // Chuyển updatedDate từ UTC sang giờ VN (cộng 7 tiếng)
                                                        const updatedDateVN = new Date(updatedDateUTC.getTime() + 7 * 60 * 60 * 1000);

                                                        // Lấy giờ hiện tại theo múi giờ VN
                                                        const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Ho_Chi_Minh' }));

                                                        // Định dạng ngày giờ để hiển thị
                                                        const day = pad(updatedDateVN.getDate());
                                                        const month = pad(updatedDateVN.getMonth() + 1);
                                                        const hour = pad(updatedDateVN.getHours());
                                                        const minute = pad(updatedDateVN.getMinutes());

                                                        let timeNote = `(${hour}:${minute} ${day}-${month})`;

                                                        // Tính khoảng cách thời gian
                                                        const diffMonths = monthDiff(updatedDateVN, now);
                                                        if (diffMonths >= 1) {
                                                            displayText2 = `🕒 Cập nhật cách đây hơn ${diffMonths} tháng ${timeNote}`;
                                                        } else {
                                                            const days = dayDiff(updatedDateVN, now);
                                                            if (days <= 0) {
                                                                displayText2 = `🕒 Cập nhật hôm nay ${timeNote}`;
                                                            } else {
                                                                displayText2 = `🕒 Cập nhật ${days} ngày trước ${timeNote}`;
                                                            }
                                                        }
                                                    }
                                                }



// 2. Sau đó mới chạy vòng lặp history.forEach, dùng displayText2 này ngoài vòng forEach

                                                history.forEach(item => {
                                                    let changedFields = [];
                                                if (Array.isArray(item.changed_fields)) {
                                                    changedFields = item.changed_fields;
                                                } else if (typeof item.changed_fields === 'string') {
                                                    changedFields = item.changed_fields.split(',');
                                                }

                                                const fieldDisplayNames = {
                                                    giathue: 'Giá thuê',
                                                    donvi_giaban_text:'Đơn vị giá bán',
                                                    dtd_congnhan:'Diện tích đất công nhận',
                                                    dtd_dai:'Diện tích đất dài',
                                                    dtd_ngang:'Diện tích đất ngang',
                                                    dtd_mathau:'Diện tích đất mặt hậu',
                                                    dtd_mathau:'Diện tích đất mặt hậu',
                                                    dtqh_dai:'Diện tích quy hoach dài',
                                                    dtqh_ngang:'Diện tích quy hoach ngang',
                                                    dtqh_mathau:'Diện tích quy hoach mặt hậu',
                                                    dtqh_xaydung:'Diện tích quy hoach xây dựng',
                                                    giaban:'Giá bán',
                                                    giachot:'Giá chốt',
                                                    gioitinh:'Giới tính',
                                                    khuvuc:'Khu vực',
                                                    loaitaisan:'Loại tài sản',
                                                    name:'Tên chủ nhà',
                                                    product_type:'Loại sản phẩm',
                                                    property_type:'Loại BĐS',
                                                    road:'Đường xá',
                                                    solo:'Số lô',
                                                    tinhtranggiaodich:'Tình trạng giao dịch',
                                                    transaction_type:'Loại giao dịch',
                                                    ttk_duongrong:'Thông tin khác đường rộng',
                                                    ttk_huong:'Thông tin khác hướng',
                                                    ttk_loaidat:'Thông tin khác loại đất',
                                                    ttk_duongrong:'Thông tin khác đường rộng',
                                                    ttk_dtsd:'Thông tin khác dtsd',
                                                    ttk_sophongngu:'Thông tin khác số phòng ngủ',
                                                    ttk_sotang:'Thông tin khác số tầng',
                                                    ttk_sotangham:'Thông tin khác số tầng hầm',
                                                    ttk_swc:'Thông tin khác số tollet',
                                                    vaitro: 'Vai trò',
                                                    ghichu: 'Ghi chú',
                                                    sanphamhot: 'Sản phẩm hot',
                                                    thangmay: 'Thang máy',
                                                    wp_district: 'Quận/huyện',
                                                    wp_province: 'Tỉnh/thành',
                                                    wp_wards: 'Phường/xã',
                                                    name_area: 'Tên khu vực',
                                                    vitri: 'Vị trí',
                                                    code_product: 'Mã căn',
                                                    donvi_giaban: 'Đơn vị giá bán',
                                                    hoahong_tyle: 'Loại hoa hồng',
                                                    hoahong_tyle: 'Loại hoa hồng',
                                                    dienthoaididong: 'Điện thoại chủ nhà',
                                                    donvi_thoigiangia: 'Đơn vị thời gian bán',
                                                    donvi_thoigianthue: 'Đơn vị thời gian thuê',
                                                    sohong_image_multi: 'Hình ảnh sổ hồng',
                                                    donvi_giathue: 'Đơn vị giá thuê',
                                                    donvi_giaban: 'Đơn vị giá bán',
                                                    thoigianthue: 'Thời gian thuê',
                                                    hethanthue: 'Hết hạn thuê',
                                                    sonha: 'Số nhà',
                                                    sothua: 'Số thửa',
                                                    soto: 'Số tờ',
                                                    loaihoahong: 'Loại hoa hồng',
                                                    hoahong_thang: 'Hoa hồng tháng',
                                                    hoahong_thang: 'Hoa hồng tháng',

                                                    // Thêm các field khác nếu cần
                                                };

                                                const moneyFields = ['giaban', 'giathue','giachot'];

                                                const user = item.data; // đây là mảng từ PHP

                                                const avatar = user.user_avatar_url || 'https://via.placeholder.com/60';
                                                const firstName = user.first_name || '';
                                                const lastName = user.last_name || '';
                                                const userId = user.userupdate || '';
                                                const phone = user.phone || '';





                                                var userHtml =
                                                    '<div style="display: flex; align-items: center; gap: 15px; margin: 0px 13px;">' +
                                                    '<div class="avatar-wrapper" style="width: 60px !important; aspect-ratio: 1 / 1; border-radius: 50%; overflow: hidden; flex-shrink: 0;">' +
                                                    '<img src="' + avatar + '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '<div style="font-size: 14px; line-height: 1.5; color: darkgreen; font-weight: bold;">' +
                                                    '<div>Người Sửa: NV' + userId + '</div>' +
                                                    '<div>' + firstName + ' <b>' + lastName + '</b> ' +
                                                    '<span style="margin-left: 5px; cursor: pointer; color: #007bff;">' +
                                                    '</span>' +
                                                    '</div>' +
                                                    '<div>' + phone + '</div>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>';





                                                let changedHtml = changedFields.map(field => {
                                                        if (field === "contact_info" || field ==="contact_all") return '';
                                                        let textField = field + '_text';
                                                let value = 'N/A';

                                                if (item.data) {
                                                    if (item.data.hasOwnProperty(textField) && item.data[textField]) {
                                                        value = item.data[textField];
                                                    } else if (item.data.hasOwnProperty(field) && item.data[field]) {
                                                        value = item.data[field];
                                                    }
                                                }

                                                // Format tiền nếu là field tiền
                                                if (moneyFields.includes(field.toLowerCase()) && value !== 'N/A') {
                                                    value = formatVietnamCurrency(value);
                                                }

                                                let displayName = fieldDisplayNames[field.toLowerCase()] || field;

                                                // ✅ Nếu là ảnh sổ hồng đa, render ảnh thay vì text
                                                if (field === 'sohong_image_multi' && Array.isArray(item.data['sohong_image_multi_urls'])) {
                                                    let imagesHtml = item.data['sohong_image_multi_urls'].map(url => `
    <a href="${url}" data-lightbox="sohong" data-title='<a href="${url}" download style="color:white; text-decoration:underline;">Tải ảnh</a>' style="display: inline-block;">
        <img src="${url}" alt="Ảnh sổ hồng" style="width: 100px; margin: 5px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);" />
    </a>
`).join('');
                                                    return `
            <div style="
                margin: 8px 0;
                padding: 10px 15px;
                background: #f9faff;
                border-left: 4px solid #2980b9;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                color: #2c3e50;
                font-weight: 600;
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                border-radius: 4px;
            ">
                <span style="color:#2980b9;">${displayName}:</span><br/>
                ${imagesHtml}
            </div>
        `;
                                                }

                                                // Trường thông thường
                                                return `
        <p style="
            margin: 8px 0;
            padding: 10px 15px;
            background: #f9faff;
            border-left: 4px solid #2980b9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            border-radius: 4px;
        ">
            <span style="color:#2980b9;">${displayName}:</span> <span style="font-weight:400;">${value}</span>
        </p>
    `;
                                            }).join('');





                                                var html =
                                                    '<section style="font-family: Arial, sans-serif; color: #333; background: #f9f9f9; padding: 10px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); opacity:0.4; transition: opacity 0.3s ease;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.4">' +



                                                    '<div style="display: flex; justify-content: space-between; margin-bottom: 15px;">' +
                                                    '<div style="flex: 1; margin-right: 20px;">' +
                                                    '<div style="font-weight: 600; font-size: 14px; margin-bottom: 5px;">Mã BĐS: ' +
                                                    '<a href="javascript:void(0)" copytext="12879" style="color: #007bff; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">' +
                                                    '<i class="far fa-copy" style="font-size: 16px;"></i> ' + rowData2.id +
                                                    '</a>' +
                                                    '</div>' +
                                                    '<div style="font-size: 13px; color: #666;">Ngày Nhập: ' +
                                                    '<div style="display: inline-block;">' +
                                                    '<p style="margin: 0;">' + displayText2 + '</p>' +  // Dùng displayText2 chung ngoài vòng forEach
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>' +

                                                    userHtml +
                                                    // Phần history hoặc nội dung khác của bạn ở đây
                                                    '<div style="border: 1px solid #ddd; border-radius: 6px; padding: 10px; margin-bottom: 10px; display: flex; flex-direction: column; gap: 8px;">' +
                                                    '<div style="display: flex; align-items: center; gap: 10px; color:#ff0000">' +
                                                    'Dữ liệu đã thay đổi:' +
                                                    '</div>' +
                                                    '<div>' +
                                                    (item.label || '') +
                                                    '</div>' +
                                                    '<div>' +
                                                    changedHtml +
                                                    '</div>' +
                                                    '<div style="display: flex; justify-content: flex-end; align-items: center; gap: 5px; color: #666; font-size: 13px;">' +
                                                    (item.verified ? '<span style="font-size:18px; color: ' + (item.icon_color || '#000') + ';">✅</span>' : '') +
                                                    (item.verified ? 'Xác minh bởi <b style="color:' + (item.icon_color || '#000') + ';">' + (item.verified_by || '') + '</b>' : '') +
                                                    '<img src="' + (item.avatar_url || 'https://app.urbanhome.vn/refer/ast/img/Avatar/men.png') + '" alt="Avatar" style="width: 20px; height: 20px; border-radius: 50%;">' +
                                                    '</div>' +
                                                    '</div>';

                                                '</section>';

                                                $col2.append(html);
                                            });
                                                console.log(rowData2);
                                                // get sohong_multi
                                                let multiImagesHtml = '<div style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">';

                                                if (Array.isArray(rowData2.sohong_image_multi_urls) && rowData2.sohong_image_multi_urls.length > 0) {
                                                    rowData2.sohong_image_multi_urls.forEach((url) => {
                                                        url = url.trim();
                                                    const ext = url.split('.').pop().toLowerCase();
                                                    const isVideo = ['mp4', 'webm', 'ogg'].includes(ext);

                                                    if (isVideo) {
                                                        multiImagesHtml += `
            <div style="display: flex; flex-direction: column; align-items: center;">
                <video src="${url}" controls
                    style="width: 120px; height: 90px; border-radius: 6px;
                           object-fit: cover; box-shadow: 0 2px 6px rgba(0,0,0,0.2); background: #000;"
                    preload="metadata" playsinline>
                </video>
                <a href="${url}" download
                    style="font-size: 12px; color: #007bff; margin-top: 4px; text-decoration: underline;">Tải video</a>
            </div>
            `;
                                                    } else {
                                                        multiImagesHtml += `
            <a href="${url}" data-lightbox="sohong"
                data-title='<a href="${url}" download style="color:white; text-decoration:underline;">Tải ảnh</a>'>
                <img src="${url}" alt="Ảnh sổ hồng"
                     style="width: 120px; height: 90px; object-fit: cover; border-radius: 6px;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.2);" />
            </a>
            `;
                                                    }
                                                });
                                                } else {
                                                    multiImagesHtml += '<p style="color: #666;">Không có ảnh/video sổ hồng</p>';
                                                }

                                                multiImagesHtml += '</div>';


                                                let ghiChuHtml2 = '';
                                                if (rowData2.ghichu && rowData2.ghichu.trim() !== '') {
                                                    // Regex xoá số điện thoại
                                                    let cleanedNote2 = rowData2.ghichu
                                                    // Xoá số điện thoại
                                                        .replace(/(?:\+84|0)(?:\s?\d){8,11}/g, '')
                                                        // Xoá thẻ <img ...> kể cả emoji
                                                        .replace(/<img[^>]*>/gi, '')
                                                        // Xoá các đoạn escape của src="...svg"
                                                        .replace(/\\*"?\s*src=\\*"?https:\/\/s\.w\.org\/images\/core\/emoji[^ >]*>/gi, '');


                                                    ghiChuHtml2 =
                                                        '<div class="GroupNote" style="position:relative; margin-top:10px; padding:12px 50px 12px 40px; border:1px solid #ddd; border-radius:6px; background:#fff9e6; font-family:Arial, sans-serif; font-size:14px; color:#444; line-height:1.4; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">' +
                                                        '<i class="IconComment fas fa-comment-alt" style="position:absolute; left:12px; top:12px; font-size:20px; color:#f0a500;"></i>' +
                                                        '<div class="jInfoCopy" style="margin-right:50px; white-space: pre-line;">' +
                                                        cleanedNote2 +
                                                        '</div>' +
                                                        '<a href="javascript:void(0)" class="jCopyTextTarget" data-target=".jInfoCopy" data-noti="Nội dung ghi chú"' +
                                                        ' style="position:absolute; right:2px; top:2px; background:#ffffffbd; border-top-right-radius:5px; padding:5px; font-size:.9rem; cursor:pointer; color:#555; text-decoration:none;">' +
                                                        '<i class="fas fa-copy"></i> Copy' +
                                                        '</a>' +
                                                        '</div>';
                                                }


                                                const mainHtml =
                                                    // Card 3
                                                    '<section style="font-family: Arial, sans-serif; color: #333; background: #f9f9f9; padding: 10px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); opacity:0.4; transition: opacity 0.3s ease;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.4">' +


                                                    '<p style="color: #FF0000;text-decoration: none;border: solid 1px #ff0000;padding: 6px;background: #fff;border-radius: 5px;"><b>TIN GỐC</b> được đăng đầu tiên</p>'+
                                                    '<div style="display: flex; justify-content: space-between; margin-bottom: 15px;">' +
                                                    '<div style="flex: 1; margin-right: 20px;">' +
                                                    '<div style="font-weight: 600; font-size: 14px; margin-bottom: 5px;">Mã BĐS: ' +
                                                    '<a href="javascript:void(0)" copytext="12879" style="color: #007bff; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">' +
                                                    '<i class="far fa-copy" style="font-size: 16px;"></i> ' + rowData2.id +
                                                    '</a>' +
                                                    '</div>' +
                                                    '<div style="font-size: 13px; color: #666;">Ngày Nhập: ' +
                                                    '<div style="display: inline-block;">' +
                                                    '<p style="margin: 0;">'+displayText+'</p>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>' +

                                                    '<div style="display: flex; align-items: center; gap: 15px; margin: 0px 13px;">' +
                                                    '<div class="avatar-wrapper" style="width: 60px !important; aspect-ratio: 1 / 1; border-radius: 50%; overflow: hidden; flex-shrink: 0;">' +
                                                    '<img src="' + rowData2.user_avatar_url + '" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">' +
                                                    '</div>' +
                                                    '</div>'+
                                                    '<div style="font-size: 14px; line-height: 1.5; color: darkgreen; font-weight: bold;">' +
                                                    '<div>Người Nhập: NV'+rowData2.user+'</div>' +
                                                    '<div>'+rowData2.user_first_name+' <b>'+rowData2.user_last_name+'</b> ' +
                                                    '<span style="margin-left: 5px; cursor: pointer; color: #007bff;" title="Ngày vào làm: Gần 1 Năm (26-08-2024)">' +

                                                    '</span>' +
                                                    '</div>' +
                                                    '<div>'+formatPhoneNumber(rowData2.user_phone)+'</div>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>' +

                                                    '<div style="border-top: 1px solid #ddd; padding-top: 15px;">' +
                                                    '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">' +
                                                    '<div style="font-size: 18px; font-weight: 700; color: #007bff; width:70%;">' +
                                                    '<div style="display: flex; gap: 8px;">' +
                                                    ((rowData2.transaction_type_name || rowData2.property_type_name)
                                                        ? '<p style="text-decoration: underline; margin: 0; background: green; padding: 7px; border-radius: 4px; text-decoration: none; color: #FEE200; text-transform: uppercase;">' +
                                                        (rowData2.transaction_type_name || '') + ' ' + (rowData2.property_type_name || '') +
                                                        '</p>'
                                                        : '') +
                                                    '</div>' +
                                                    (rowData2.tinhtranggiaodich_name
                                                        ? '<div style="color: green; font-weight: 600; margin-top: 4px;">' + rowData2.tinhtranggiaodich_name + '</div>'
                                                        : '') +
                                                    (rowData2.khuvuc
                                                        ? '<div style="color: sienna; font-weight: 600; margin-top: 4px;">Khu vực ' + rowData2.khuvuc + '</div>'
                                                        : '') +
                                                    '</div>' +


                                                    '</div>' +
                                                    '</div>'+

                                                    // Xử lý địa chỉ Google Maps
                                                    ((rowData2.product_type == 517 || rowData2.product_type == 515) &&
                                                    (rowData2.khuvuc || rowData2.sonha || rowData2.road || rowData2.wp_ward_name || rowData2.wp_district_name || rowData2.wp_province_name)
                                                        ? (function () {
                                                            const addressParts = [];

                                                            if (rowData2.product_type == 517 && rowData2.khuvuc) {
                                                                addressParts.push(rowData2.khuvuc);
                                                            } else if (rowData2.product_type == 515 && rowData2.sonha) {
                                                                addressParts.push(rowData2.sonha);
                                                            }

                                                            // Thêm các phần còn lại
                                                            addressParts.push(
                                                                rowData2.road,
                                                                rowData2.wp_ward_name,
                                                                rowData2.wp_district_name,
                                                                rowData2.wp_province_name
                                                            );

                                                            const address = addressParts.filter(Boolean).join(', ');

                                                            /*return (
                                                             '<a href="https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(address) + '" target="_blank" ' +
                                                             'style="display: flex; align-items: center; gap: 8px; color: #007bff; text-decoration: none; font-size: 14px;">' +
                                                             '<span style="color:red;font-weight: bold;text-align: right; font-size:12px;">' + address + '</span>' +
                                                             '<i class="fa fa-map-marker" style="color:red; margin-right:5px;"></i>' +
                                                             '</a>'
                                                             );*/
                                                            return (
                                                                '<a href="' + rowData2['link_googlemap'] + '" target="_blank" ' +
                                                                'style="padding:5px; text-transform:uppercase;display: flex; align-items: center; gap: 8px; color: #007bff; text-decoration: none; font-size: 14px;">' +
                                                                '<span style="color:red; font-weight: bold; font-size:12px;">' + address + '</span>' +
                                                                '<div style="position: relative; width: 24px; height: 24px;">' +
                                                                '<i class="fas fa-map" style="font-size: 24px; color: #ccc;"></i>' +
                                                                '<i class="fas fa-map-marker-alt" style="color:#e60000; font-size:12px; position: absolute; top: 6px; left: 6px;"></i>' +
                                                                '</div>' +
                                                                '</a>'
                                                            );


                                                        })()
                                                        : '') +

                                                    '<div style="display: flex; gap: 30px; font-size: 14px; color: #444;">' +

                                                    // PHẦN GIÁ
                                                    (
                                                        (isValidPrice(rowData2.giaban) || isValidPrice(rowData2.giathue))
                                                            ? (
                                                            '<div>' +
                                                            '<p style="margin: 0 0 5px 0; font-weight: 600;">Giá</p>' +

                                                            (
                                                                (isValidPrice(rowData2.giaban) && isValidPrice(rowData2.giathue))
                                                                    ? (
                                                                    // ✅ Có cả giá bán và thuê
                                                                    '<p style="margin: 0; font-size: 16px; font-weight: 700; color:red;">' +
                                                                    formatVietnamCurrency(rowData2.giaban) +
                                                                    ' <span style="font-weight: 400;">' +
                                                                    (rowData2.donvi_giaban_name ? rowData2.donvi_giaban_name : 'VNĐ') +
                                                                    '</span>' +
                                                                    '</p>' +
                                                                    '<p style="margin: 0; font-size: 13px; color: #666;">~ ' +
                                                                    tinhGiaTren1m2(rowData2.giaban, rowData2.dtd_congnhan) +
                                                                    '</p>' +
                                                                    '<p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 700; color:green;">' +
                                                                    'HĐ thuê: ' + formatVietnamCurrency(rowData2.giathue) +
                                                                    ' <span style="font-weight: 400;">' +
                                                                    (rowData2.donvi_giathue_name ? rowData2.donvi_giathue_name : 'VNĐ') +
                                                                    '</span>' +
                                                                    '</p>'
                                                                )
                                                                    : isValidPrice(rowData2.giathue)
                                                                    ? (
                                                                        // ✅ Chỉ có giá thuê
                                                                        '<p style="margin: 0; font-size: 16px; font-weight: 700; color:green;">' +
                                                                        formatVietnamCurrency(rowData2.giathue) +
                                                                        ' <span style="font-weight: 400;">' +
                                                                        (rowData2.donvi_giathue_name ? rowData2.donvi_giathue_name : 'VNĐ') +
                                                                        '</span>' +
                                                                        '</p>'
                                                                    )
                                                                    : (
                                                                        // ✅ Chỉ có giá bán
                                                                        '<p style="margin: 0; font-size: 16px; font-weight: 700; color:red;">' +
                                                                        formatVietnamCurrency(rowData2.giaban) +
                                                                        ' <span style="font-weight: 400;">' +
                                                                        (rowData2.donvi_giaban_name ? rowData2.donvi_giaban_name : 'VNĐ') +
                                                                        '</span>' +
                                                                        '</p>' +
                                                                        '<p style="margin: 0; font-size: 13px; color: #666;">~ ' +
                                                                        tinhGiaTren1m2(rowData2.giaban, rowData2.dtd_congnhan) +
                                                                        '</p>'
                                                                    )
                                                            ) +

                                                            '</div>'
                                                        )
                                                            : ''
                                                    )+

                                                    // Khối diện tích (luôn hiển thị)
                                                    '<div>' +
                                                    '<p style="margin: 0 0 5px 0; font-weight: 600;">Diện tích</p>' +
                                                    '<div>' +
                                                    '<p style="margin: 0; font-size: 16px; font-weight: 700;">' + rowData.dtd_congnhan + ' m2</p>' +
                                                    '<p style="margin: 0; font-size: 13px; color: #666;">(' + rowData.dtd_ngang + ' x ' + rowData.dtd_dai + ')</p>' +
                                                    '</div>' +
                                                    '</div>' +

                                                    '</div>'+

                                                    '<div style="margin-top: 15px; font-size: 14px; color: #444; display: flex; align-items: center; gap: 10px;">' +
                                                    (rowData2.gioitinh == 645
                                                        ? '<i class="fas fa-female" style="font-size: 20px; color: #e83e8c; margin-right:6px;"></i>'
                                                        : '<i class="fas fa-male" style="font-size: 20px; color: #007bff; margin-right:6px;"></i>') +
                                                    '<div style="font-weight: 600;">' + rowData2.name + '</div>' +
                                                    (rowData2.can_view_phone
                                                            ? '<a href="javascript:void(0)" class="show-phone-popup" ' +
                                                            'data-phone="' + rowData2.dienthoaididong + '" ' +
                                                            'data-nhadat-id="' + rowData2.id + '" ' +
                                                            'data-user="' + rowData2.user + '" ' +
                                                            'data-name="' + rowData2.name + '" ' +
                                                            'data-role="' + (rowData2.vaitro_name || '') + '" ' +
                                                            'style="color: #007bff; text-decoration: none; font-size: 14px; display: inline-flex; gap: 4px;">' +
                                                            '<i class="fas fa-star-of-life" style="font-size: 14px;"></i>' +
                                                            '<span>' + formatHiddenPhoneNumber(rowData2.dienthoaididong) + '</span>' +
                                                            '<span style="font-size: 14px;background: #ccc; padding: 4px;border-radius: 5px;font-weight: normal;">' + rowData2.vaitro_name + '</span>' +
                                                            '</a>'
                                                            : '<span style="color: #aaa; font-size: 14px; font-style: italic;">Đã hết lượt xem hôm nay</span>'
                                                    )
                                                    +
                                                    '</div>'

                                                    +

                                                    ((function () {
                                                        var htmlContact = '';
                                                        if (rowData2.contact_info) {
                                                            try {
                                                                var contactList = typeof rowData2.contact_info === 'string'
                                                                    ? JSON.parse(rowData2.contact_info)
                                                                    : rowData2.contact_info;

                                                                contactList.forEach(function (item) {
                                                                    htmlContact += '<div style="margin-top: 8px; display: flex; align-items: center; gap: 17px; margin-left: 0px;">' +
                                                                        (item.gender_more == 645
                                                                            ? '<i class="fas fa-female" style="font-size: 16px; color: #e83e8c;"></i>'
                                                                            : '<i class="fas fa-male" style="font-size: 16px; color: #007bff;"></i>') +
                                                                        '<div style="font-weight: 600;">' + item.name_more + '</div>' +
                                                                        (
                                                                            rowData2.can_view_phone
                                                                                ? '<a href="javascript:void(0)" class="show-phone-popup" ' +
                                                                                'data-phone="' + item.phone_more + '" ' +
                                                                                'data-nhadat-id="' + rowData2.id + '" ' +
                                                                                'data-user="' + rowData2.user + '" ' +
                                                                                'data-name="' + item.name_more + '" ' +
                                                                                'data-role="' + (item.vaitro_more_name || '') + '" ' +
                                                                                'style="color: #007bff; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">' +
                                                                                '<i class="fas fa-star-of-life" style="font-size: 12px;"></i>' +
                                                                                '<span>' + formatHiddenPhoneNumber(item.phone_more) + '</span>' +
                                                                                '<span style="font-size: 14px;background: #ccc; padding: 4px;border-radius: 5px;font-weight: normal;">' +
                                                                                (item.vaitro_more_name || '') +
                                                                                '</span>' +
                                                                                '</a>'
                                                                                : '<span style="color: #aaa; font-size: 14px; font-style: italic;">Đã hết lượt xem hôm nay</span>'
                                                                        )

                                                                        +
                                                                        '</div>';
                                                                });
                                                            } catch (e) {
                                                                console.warn("Lỗi parse contact_info:", e);
                                                            }
                                                        }
                                                        return htmlContact;
                                                    })())
                                                    +

                                                    '</div>'+
                                                    '<div class="GroupProducts" style="padding:10px; margin-top:10px;border:1px solid #ddd; border-radius:6px; background:#fafafa; font-family:Arial, sans-serif; font-size:14px; color:#333;">' +
                                                    '<div class="List">' +

                                                    (rowData2.sonha
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-home"></i></div>' +
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số nhà</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.sonha + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData2.solo
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-th-list"></i></div>' + // icon cho Số lô
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số lô</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.solo + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData2.sothua
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-map-marker-alt"></i></div>' + // icon cho Số thửa
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số thửa</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.sothua + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData2.soto
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-layer-group"></i></div>' + // icon cho Số tờ
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số tờ</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.soto + '</div>' +
                                                        '</div>'
                                                        : '') +
                                                    (rowData2.tenduan
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-city"></i></div>' + // icon cho Tên dự án
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Tên dự án</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.tenduan + '</div>' +
                                                        '</div>'
                                                        : '')+



                                                    (rowData2.vitri_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-map-pin"></i></div>' +
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Vị Trí</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.vitri_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.loaitaisan_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' + // thêm margin-bottom
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-building"></i></div>' +
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Loại Tài Sản</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.loaitaisan_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (
                                                        rowData2.loaihoahong == 651 && rowData2.hoahong_tyle
                                                            ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                            '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;">' +
                                                            '<i class="fas fa-percent"></i>' +
                                                            '</div>' +
                                                            '<div class="iLabel" style="flex:1; font-weight:600;">Hoa hồng</div>' +
                                                            '<div class="iValue" style="color:#222;">' + rowData2.hoahong_tyle + ' %</div>' +
                                                            '</div>'
                                                            : rowData2.loaihoahong == 653 && rowData2.hoahong_thang
                                                            ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                            '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;">' +
                                                            '<i class="fas fa-calendar-alt"></i>' +
                                                            '</div>' +
                                                            '<div class="iLabel" style="flex:1; font-weight:600;">Hoa hồng</div>' +
                                                            '<div class="iValue" style="color:#222;">' + rowData2.hoahong_thang + ' tháng</div>' +
                                                            '</div>'
                                                            : rowData2.loaihoahong == 649 && rowData2.hoahong_sotien
                                                                ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                                '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;">' +
                                                                '<i class="fas fa-money-bill-wave"></i>' +
                                                                '</div>' +
                                                                '<div class="iLabel" style="flex:1; font-weight:600;">Hoa hồng</div>' +
                                                                '<div class="iValue" style="color:#222;">' + Number(rowData2.hoahong_sotien).toLocaleString('vi-VN') + ' ₫</div>' +
                                                                '</div>'
                                                                : ''
                                                    )+


                                                    '</div>' +
                                                    '</div>'+


                                                    '<div class="GroupProducts" style="padding:10px; margin-top:10px;border:1px solid #ddd; border-radius:6px; background:#fafafa; font-family:Arial, sans-serif; font-size:14px; color:#333;">' +
                                                    '<div class="List" style="font-weight:bold; color:green; padding:10px;"><strong>🔎 Thông tin khác: </strong></div>' +


                                                    (rowData2.ttk_loaidat_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-tree"></i></div>' + // icon đất đai
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Loại đất</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_loaidat_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.ttk_sophongngu
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-bed"></i></div>' + // icon phòng ngủ
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số phòng ngủ</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_sophongngu + ' phòng</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.ttk_sotang
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-building"></i></div>' + // icon số tầng
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số tầng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_sotang + ' tầng</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.ttk_sotangham
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-warehouse"></i></div>' + // icon tầng hầm
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số tầng hầm</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_sotangham + ' hầm</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.ttk_swc
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-toilet"></i></div>' + // icon toilet
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Số toilet</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_swc + ' phòng</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.ttk_dtsd
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-ruler-combined"></i></div>' + // icon diện tích
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Diện tích sử dụng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_dtsd + ' m2</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.ttk_huong_name
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-compass"></i></div>' + // icon hướng
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Hướng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_huong_name + '</div>' +
                                                        '</div>'
                                                        : '') +

                                                    (rowData2.ttk_duongrong
                                                        ? '<div class="iRows" style="display:flex; align-items:center; margin-bottom:8px;">' +
                                                        '<div class="iIcon" style="width:24px; text-align:center; color:#5a5a5a; margin-right:8px;"><i class="fas fa-road"></i></div>' + // icon hẻm rộng
                                                        '<div class="iLabel" style="flex:1; font-weight:600;">Hẻm Rộng</div>' +
                                                        '<div class="iValue" style="color:#222;">' + rowData2.ttk_duongrong + ' <span class="iUnit">m</span></div>' +
                                                        '</div>'
                                                        : '')+

                                                    '</div>' +
                                                    '</div>'+

                                                    ghiChuHtml2 +
                                                    '<a href="javascript:void(0)" class="jCopyTextTarget" data-target=".jInfoCopy" data-noti="Nội dung ghi chú"' +
                                                    ' style="position:absolute; right:2px; top:2px; background:#ffffffbd; border-top-right-radius:5px; padding:5px; font-size:.9rem; cursor:pointer; color:#555; text-decoration:none;">' +

                                                    '</a>' +
                                                    '</div>'
                                                    /* + '<div style="margin-top: 15px; text-align: center;">' +
                                                     '<a href="'+rowData2.sohong_image_id_url+'" download target="_blank" style="display: inline-block;">' +
                                                     '<img src="'+rowData2.sohong_image_id_url+'" alt="Sổ Hồng" style="width: 160px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">' +
                                                     '<div style="margin-top: 5px; color: #007bff; font-size: 14px;">Click vào hình để tải Sổ Hồng</div>' +
                                                     '</a>'*/ +
                                                    multiImagesHtml +
                                                    '</div>' +
                                                    '</section>';
                                                $col2.append(mainHtml);



                                                // Xóa hết nội dung cũ trong form, thêm container, rồi thêm col1, col2
                                                form.empty().append($container.append($col1).append($col2));
                                                form.fadeIn();  // Hiện lại form khi đã có dữ liệu



                                                // Tạo popup con (nếu chưa có)
                                                if (!$('#phone-popup-child').length) {
                                                    $(form).append(
                                                        '<div id="phone-popup-child" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; ' +
                                                        'padding:40px; max-width:900px; box-shadow:0 2px 8px rgba(0,0,0,0.2); z-index:10000; border-radius:8px; ' +
                                                        'font-family:sans-serif;">' +
                                                        '<div class="popup-flex-container" style="display:flex; gap:20px; flex-wrap:wrap;">' +

                                                        // Bên trái: form chính
                                                        '<div style="flex:1; min-width:300px;">' +

                                                        '<span style="position:absolute; top:10px; right:10px; cursor:pointer; font-size:18px; color:#888;" id="close-popup-child">&times;</span>' +

                                                        '<div class="popup-title" style="text-transform: uppercase;font-weight:bold; margin-bottom:8px; font-size:18px;"></div>' +

                                                        '<div id="popup-phone-number-child" style="font-weight:bold;font-size:16px; margin-bottom:15px; color:#ff0000;"></div>' +

                                                        '<form id="phone-status-form" method="post" accept-charset="utf-8">' +
                                                        '<input type="hidden" name="nhadat_id" value="">' +
                                                        '<input type="hidden" name="nguoidung_id" value="">' +

                                                        '<div style="margin-bottom:15px;">' +
                                                        '<label style="font-weight:bold; display:block; margin-bottom:5px;">Tình Trạng Số Điện Thoại</label>' +
                                                        '<div style="display:flex; flex-wrap:wrap; gap:10px;">' +
                                                        '<label style="flex:1; min-width:45%; display:flex; align-items:center; padding:8px; border:1px solid #ccc; border-radius:6px; cursor:pointer;">' +
                                                        '<input type="radio" name="phonestatus" value="1" style="margin-right:8px;"> Đúng Thông Tin' +
                                                        '</label>' +
                                                        '<label style="flex:1; min-width:45%; display:flex; align-items:center; padding:8px; border:1px solid #ccc; border-radius:6px; cursor:pointer;">' +
                                                        '<input type="radio" name="phonestatus" value="2" style="margin-right:8px;"> Sai Thông Tin' +
                                                        '</label>' +
                                                        '<label style="flex:1; min-width:45%; display:flex; align-items:center; padding:8px; border:1px solid #ccc; border-radius:6px; cursor:pointer;">' +
                                                        '<input type="radio" name="phonestatus" value="3" style="margin-right:8px;"> Không Liên Lạc Được' +
                                                        '</label>' +
                                                        '<label style="flex:1; min-width:45%; display:flex; align-items:center; padding:8px; border:1px solid #ccc; border-radius:6px; cursor:pointer;">' +
                                                        '<input type="radio" name="phonestatus" value="4" style="margin-right:8px;"> Số Môi Giới' +
                                                        '</label>' +
                                                        '</div>' +
                                                        '</div>' +

                                                        '<div style="margin-bottom:15px;">' +
                                                        '<label style="font-weight:bold; display:block; margin-bottom:5px;">Ghi chú</label>' +
                                                        '<textarea name="note" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc; resize:vertical; min-height:80px;"></textarea>' +

                                                        '<div class="GroupCheckPhone" style="margin-top:10px;">' +
                                                        '<a href="#" target="_blank" style="margin-right:15px; display:inline-flex; align-items:center;">' +
                                                        '<img src="https://cdn-icons-png.flaticon.com/512/300/300221.png" style="width:20px; height:20px; margin-right:5px;">' +
                                                        '<span>Check Số Với Google</span>' +
                                                        '</a>' +
                                                        '<a href="#" target="_blank" style="display:inline-flex; align-items:center;">' +
                                                        '<img src="https://warehouse.urbanhome.vn/wp-content/uploads/2025/07/zalo-seeklogo.webp" style="width:20px; height:20px; margin-right:5px;">' +
                                                        '<span>Check Số Với Zalo</span>' +
                                                        '</a>' +
                                                        '</div>' +
                                                        '</div>' +

                                                        '<button type="submit" style="background:#e74c3c; color:#fff; padding:10px 20px; border:none; border-radius:6px; font-size:16px; cursor:pointer; width:100%; margin-bottom:10px;">Cập Nhật</button>' +
                                                        '</form>' +

                                                        '<div id="bds-cung-sdt" class="GroupBox GroupListProperty" style="margin-top:20px;">' +
                                                        '<p class="gTitle" style="color:#e74c3c; font-weight:700; font-size:18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.15); margin-bottom:12px;">BĐS cùng số ĐT <b style="color:#c0392b;"></b>:</p>' +
                                                        '<div class="property-list"></div>' +
                                                        '</div>' +

                                                        '</div>'
                                                        +

                                                        '<div style="min-width:300px; width:350px; max-height:800px; overflow-y:auto; border-left:1px solid #ddd; padding-left:15px; font-family:sans-serif; ">' +

                                                        // --- Report Stats ---
                                                        '<div id="popup-report-container">' +
                                                        '</div>' +

                                                        // --- Switch + Danh sách ---
                                                        '<div style="border-top:1px solid #ccc; padding-top:15px; max-height:300px; overflow-y:auto;">' +

                                                        // switch bật/tắt
                                                        '<div style="margin-bottom:15px;">' +

                                                        '<div class="fGroupRadioSwitch jfGroupRadioSwitch active" style="display:flex; align-items:center;">' +
                                                        '<div class="frsGroup jRadioSwitch-frsGroup" style="width:50px; height:26px; background:#2ecc71; border-radius:13px; position:relative; cursor:pointer;">' +
                                                        '<div class="fTog" style="width:26px; height:26px; background:#fff; border-radius:50%; position:absolute; left:24px; top:0; transition:all 0.3s;"><span></span></div>' +
                                                        '</div>' +
                                                        '<input class="jfrs-no" name="ViewPhone" type="radio" value="0" style="display:none;">' +
                                                        '<input class="jfrs-yes" name="ViewPhone" type="radio" checked value="1" style="display:none;">' +
                                                        '</div>' +
                                                        '</div>' +

                                                        // --- Comment Stats ---
                                                        '<div id="popup-comment-container">' +
                                                        '</div>' +

                                                        '</div>' +
                                                        '</div>'+


                                                        '</div>' +
                                                        '</div>' // kết thúc popup chính
                                                    );

                                                    if (!document.getElementById('popup-flex-responsive-style')) {
                                                        $('head').append(`
                                                            <style id="popup-flex-responsive-style">
                                                              @media (max-width: 768px) {
                                                                .popup-flex-container {
                                                                  flex-direction: column !important;
                                                                }
                                                        
                                                                .popup-flex-container > div {
                                                                  width: 100% !important;
                                                                  min-width: unset !important;
                                                                }
                                                              }
                                                            </style>
                                                          `);
                                                    }



                                                }




                                                $.post(ajaxurl, {
                                                    action: 'get_report_html',
                                                    nhadat_id: rowData.id
                                                }, function(res) {
                                                    if (res.success) {
                                                        $('#popup-report-container').html(res.data.html);
                                                    }
                                                });

                                                const $switchGroup = $(form).find('.jfGroupRadioSwitch');

                                                // Kiểm tra ban đầu: có class active không?
                                                const $radioYes = $switchGroup.find('.jfrs-yes');
                                                const $radioNo = $switchGroup.find('.jfrs-no');
                                                const $knob = $switchGroup.find('.fTog');

                                                if ($radioYes.is(':checked')) {
                                                    $switchGroup.addClass('active');
                                                    $knob.css('left', '24px');
                                                } else {
                                                    $switchGroup.removeClass('active');
                                                    $knob.css('left', '0');
                                                }

                                                // Gắn lại sự kiện khi click
                                                $switchGroup.off('click.switchToggle').on('click.switchToggle', '.jRadioSwitch-frsGroup', function () {
                                                    const parent = $(this).closest('.jfGroupRadioSwitch');
                                                    const knob = parent.find('.fTog');

                                                    if (parent.hasClass('active')) {
                                                        parent.removeClass('active');
                                                        knob.css('left', '0');
                                                        parent.find('.jfrs-no').prop('checked', true);
                                                    } else {
                                                        parent.addClass('active');
                                                        knob.css('left', '24px');
                                                        parent.find('.jfrs-yes').prop('checked', true);
                                                    }
                                                });



                                                var $innerForm = $(form).find('#phone-status-form');

                                                // Debug xem form có tồn tại không
                                                console.log('Form found:', $innerForm.length);

                                                $innerForm.off('submit').on('submit', function(e) {
                                                    e.preventDefault();
                                                    console.log('Submit event triggered');

                                                    var $submitBtn = $innerForm.find('button[type="submit"]');

                                                    var phonestatus = $innerForm.find('input[name="phonestatus"]:checked').val();
                                                    var note = $innerForm.find('textarea[name="note"]').val().trim();

                                                    if (!phonestatus) {
                                                        alert('Vui lòng chọn tình trạng số điện thoại!');
                                                        return false;
                                                    }
                                                    if (note.length === 0) {
                                                        alert('Vui lòng nhập ghi chú!');
                                                        return false;
                                                    }

                                                    // Disable nút submit để tránh nhấn nhiều lần
                                                    $submitBtn.prop('disabled', true)
                                                        .css({
                                                            'background-color': '#ccc',  // màu xám nhạt khi disable
                                                            'color': '#666',             // màu chữ tối nhạt
                                                            'cursor': 'not-allowed'     // con trỏ chuột đổi thành không được phép click
                                                        });

                                                    var data = $innerForm.serialize() + '&action=save_phone_status';

                                                    $.ajax({
                                                        url: ajaxurl,
                                                        type: 'POST',
                                                        data: data,
                                                        cache: false, // JQuery tự thêm tham số _=timestamp
                                                        success: function(response) {
                                                            alert(response.data.message);
                                                            location.reload();
                                                        },
                                                        error: function() {
                                                            alert('Lỗi khi gửi dữ liệu!');
                                                            // Bật lại nút submit khi có lỗi
                                                            $submitBtn.prop('disabled', false).css({
                                                                'background-color': '#e74c3c',  // màu xám nhạt khi disable
                                                                'color': '#fff',             // màu chữ tối nhạt
                                                                'cursor': 'not-allowed'     // con trỏ chuột đổi thành không được phép click
                                                            });
                                                        }
                                                    });

                                                    return false;
                                                });


                                                function renderPhoneComments(comments = [], showAll = false) {
                                                    let html = '';

                                                    comments.forEach(item => {
                                                        // Mặc định ẩn comment chưa xác định, chỉ show khi showAll bật
                                                        if (!showAll && (item.phone_status === null || item.phone_status === undefined)) {
                                                        return; // bỏ qua
                                                    }

                                                    let statusText = '';
                                                    let icon = '';
                                                    let color = '';

                                                    switch (item.phone_status) {
                                                        case 1:
                                                            statusText = 'Đúng Thông Tin';
                                                            icon = 'fa-check-circle';
                                                            color = '#27ae60';
                                                            break;
                                                        case 2:
                                                            statusText = 'Sai Thông Tin';
                                                            icon = 'fa-phone-slash';
                                                            color = '#e74c3c';
                                                            break;
                                                        case 3:
                                                            statusText = 'Không Liên Lạc Được';
                                                            icon = 'fa-phone-slash';
                                                            color = '#f39c12';
                                                            break;
                                                        case 4:
                                                            statusText = 'Số Môi Giới';
                                                            icon = 'fa-user-tie';
                                                            color = '#8e44ad';
                                                            break;
                                                        default:
                                                            statusText = 'Chưa xác định';
                                                            icon = 'fa-question-circle';
                                                            color = '#bdc3c7';
                                                    }

                                                    html += `
    <div style="margin-bottom:15px; display:flex; gap:10px;">
        <img src="${item.avatar}" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
        <div style="flex:1;">
            <div style="font-weight:bold; font-size:14px;">
                ${item.user_name} <span style="color:#999; font-size:12px;">${item.user_code}</span>
            </div>
            <div style="font-size:12px; color:#888;">${item.time_label}</div>
            ${
                                                        !showAll
                                                            ? `<div style="margin-top:5px;">
                            <span style="color:${color}; font-weight:600;">
                                <i class="fas ${icon}"></i> ${statusText}
                            </span>
                       </div>
                       <div style="font-size:13px; color:#555;">${item.note}</div>`
                                                            : ''
                                                        }
        </div>
    </div>
`;

                                                });

                                                    return `
        <div style="border-top:1px solid #ccc; padding-top:15px; max-height:300px; overflow-y:auto;">
            ${html}
        </div>
    `;
                                                }
                                                function loadComments(nhadat_id, showAll = null) {
                                                    // Nếu không truyền showAll → tự lấy theo class active
                                                    if (showAll === null) {
                                                        showAll = $('.fGroupRadioSwitch').hasClass('active') ? 1 : 0;
                                                    }

                                                    $.post(ajaxurl, {
                                                        action: 'get_phone_comments',
                                                        nhadat_id: nhadat_id,
                                                        show_all: showAll
                                                    }, function(res) {
                                                        if (res.success) {
                                                            const html = renderPhoneComments(res.data, !!showAll);
                                                            $('#popup-comment-container').html(html);
                                                        }
                                                    });
                                                }



                                                loadComments(rowData.id);
                                                // Gán sự kiện toggle sau khi đã append DOM
                                                $('#phone-popup-child').find('.fGroupRadioSwitch').off('click').on('click', function () {
                                                    const $this = $(this);

                                                    // Toggle class
                                                    $this.toggleClass('active');

                                                    // Move nút
                                                    const $tog = $this.find('.fTog');
                                                    if ($this.hasClass('active')) {
                                                        $tog.css('left', '24px');
                                                        $this.find('.jfrs-yes').prop('checked', true);
                                                    } else {
                                                        $tog.css('left', '0');
                                                        $this.find('.jfrs-no').prop('checked', true);
                                                    }

                                                    // Gọi lại load comment
                                                    const nhadatID = rowData.id;
                                                    const showAll = $this.hasClass('active') ? 1 : 0;
                                                    loadComments(nhadatID, showAll);
                                                });








                                                // Gọi AJAX để lấy danh sách BĐS cùng số điện thoại
                                                $.ajax({
                                                    url: ajaxurl,
                                                    type: 'POST',
                                                    cache: false, // JQuery tự thêm tham số _=timestamp
                                                    data: {
                                                        action: 'get_properties_by_phone',
                                                        phone: rowData.dienthoaididong
                                                    },
                                                    success: function(response) {
                                                        if (response.success) {
                                                            var html = '';
                                                            response.data.forEach(function(item) {
                                                                // Gắn link admin với ID sản phẩm
                                                                var productUrl = 'https://warehouse.urbanhome.vn/wp-admin/admin.php?page=wp_dulieunhadat&id=' + item.id;

                                                                html += '<a href="' + productUrl + '" target="_blank" style="text-decoration:none; display:block; color:inherit;">';

                                                                html += '<div style="margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:10px;">';

                                                                // Hàng 1: icon mũi tên + loại BĐS + hình thức GD
                                                                html += '<div style="font-weight:bold; color:#27ae60; text-transform:uppercase;">';
                                                                html += '→ ' + item.property_type + ' - ' + item.transaction_type;
                                                                html += '</div>';

                                                                // Hàng 2: khu vực + địa chỉ
                                                                html += '<div>';
                                                                html += item.khuvuc + ' – ' + item.ward + ', ' + item.district + ', ' + item.province;
                                                                html += '</div>';

                                                                html += '</div>';
                                                                html += '</a>';
                                                            });

                                                            $('#bds-cung-sdt .property-list').html(html);
                                                        }
                                                        else {
                                                            $('#bds-cung-sdt .property-list').html('<p>Không có dữ liệu.</p>');
                                                        }
                                                    },
                                                    error: function() {
                                                        $('#bds-cung-sdt .property-list').html('<p>Lỗi khi tải dữ liệu.</p>');
                                                    }
                                                });


                                                // Bắt sự kiện click vào số điện thoại để mở popup con
                                                // Bắt sự kiện click vào số điện thoại để mở popup con
                                                $(form).off('click', '.show-phone-popup').on('click', '.show-phone-popup', function (e) {
                                                    e.stopPropagation();

                                                    var $this = $(this);

                                                    var phoneNumber = $this.data('phone');
                                                    var nhadatId = $this.data('nhadat-id');
                                                    var userId = $this.data('user');
                                                    var name = $this.data('name');
                                                    var role = $this.data('role');

                                                    // Hiển thị số điện thoại
                                                    $('.popup-title').html(name + ' <span style="...">' + role + '</span>');
                                                    $('#popup-phone-number-child').text(phoneNumber);
                                                    $('#phone-status-form input[name="nhadat_id"]').val(nhadatId);
                                                    $('#phone-status-form input[name="nguoidung_id"]').val(userId);
                                                    $('#bds-cung-sdt .gTitle b').text(phoneNumber);
                                                    $('.GroupCheckPhone a').eq(0).attr('href', 'https://www.google.com.vn/search?q=' + phoneNumber);
                                                    $('.GroupCheckPhone a').eq(1).attr('href', 'https://zalo.me/' + phoneNumber);







                                                    // Cập nhật tên + vai trò ở phần tiêu đề popup
                                                    $('#phone-popup-child .popup-title').html(
                                                        name + ' <span style="font-size: 14px;background: #ccc; padding: 4px;border-radius: 5px;font-weight: normal;">' + (role || '') + '</span>'
                                                    );

                                                    // Cập nhật hidden form
                                                    $('#phone-status-form input[name="nhadat_id"]').val(nhadatId);
                                                    $('#phone-status-form input[name="nguoidung_id"]').val(userId);

                                                    // Cập nhật link tìm kiếm Google & Zalo
                                                    $('#phone-popup-child .GroupCheckPhone a').eq(0).attr('href', 'https://www.google.com.vn/search?q=' + phoneNumber);
                                                    $('#phone-popup-child .GroupCheckPhone a').eq(1).attr('href', 'https://zalo.me/' + phoneNumber);

                                                    // Cập nhật title BĐS cùng số
                                                    $('#bds-cung-sdt .gTitle b').text(phoneNumber);

                                                    // Hiển thị popup dưới nút được click
                                                    var pos = $this.position();
                                                    $('#phone-popup-child').css({
                                                        top: pos.top + $this.outerHeight() + 5 + 'px',
                                                        left: pos.left - 50 + 'px'
                                                    }).fadeIn(200);

                                                    // Gửi Ajax lưu lượt xem số điện thoại
                                                    $.ajax({
                                                        url: ajaxurl,
                                                        method: 'POST',
                                                        cache: false, // JQuery tự thêm tham số _=timestamp
                                                        data: {
                                                            action: 'luu_luot_xem_sdt',
                                                            nhadat_id: nhadatId
                                                        }
                                                    });
                                                });



                                                // Đóng popup con khi bấm nút Đóng
                                                $(form).off('click', '#close-popup-child').on('click', '#close-popup-child', function() {
                                                    $('#phone-popup-child').fadeOut(200);
                                                });

                                                // Đóng popup con khi click ra ngoài popup con hoặc popup chính
                                                $(form).off('click.popupClose').on('click.popupClose', function(e) {
                                                    // Nếu click không phải trên popup con hay số điện thoại, đóng popup con
                                                    if (!$(e.target).closest('#phone-popup-child, .show-phone-popup').length) {
                                                        $('#phone-popup-child').fadeOut(200);
                                                    }
                                                });


                                                // Gắn sự kiện copy vào nút sau khi đã render xong
                                                $(form).find('.jCopyTextTarget').off('click').on('click', async function () {
                                                    console.log('===> Đã click nút copy');

                                                    const selector = $(this).data('target');
                                                    const targetEl = $(form).find(selector)[0];
                                                    if (!targetEl) {
                                                        console.warn('Không tìm thấy phần tử để copy');
                                                        return;
                                                    }

                                                    const text = targetEl.innerText.trim();
                                                    try {
                                                        await navigator.clipboard.writeText(text);
                                                        const msg = 'Đã sao chép!';
                                                        alert(msg);
                                                    } catch (err) {
                                                        alert('Trình duyệt không hỗ trợ sao chép.');
                                                    }
                                                });







                                            } else {
                                                console.log('Lỗi: ' + response.data);
                                                form.fadeIn();  // Hiện lại form khi đã có dữ liệu
                                            }
                                        },
                                        error: function(err) {
                                            console.error('Lỗi AJAX:', err);
                                            form.fadeIn();  // Hiện lại form khi đã có dữ liệu
                                        }
                                    });



// phan css view mobile

// Chỉ áp dụng mobile
                                    if (window.innerWidth < 768) {
                                        // 1. Scroll lên đầu
                                        setTimeout(() => {
                                            window.scrollTo({ top: 0, behavior: "auto" }); // cuộn tức thì lên đầu trang
                                    }, 50);

                                        // 2. Áp dụng CSS nếu chưa có
                                        const styleId = "jqgrid-mobile-style";
                                        if (!document.getElementById(styleId)) {
                                            const css = `
        @media (max-width: 768px) {
          #viewmodjqGrid {
            width: 98vw !important;
            left: 1vw !important;
            top: 10vh !important;
          }

          #viewmodjqGrid .FormGrid {
            display: block !important;
          }

          #viewmodjqGrid .FormGrid > div[style*="display:flex"] {
            flex-direction: column !important;
          }

          #viewmodjqGrid .FormGrid > div[style*="display:flex"] > div {
            width: 100% !important;
          }
        }
      `;
                                            const style = document.createElement("style");
                                            style.id = styleId;
                                            style.textContent = css;
                                            document.head.appendChild(style);
                                        }
                                    }
                                    //==== moi sua







                                }

                            });
                        }
                    },
                    loadComplete: function () {
                        if(selectedTable == "wp_khachhang"){
                            const isAdmin = ajax_object.is_admin === true || ajax_object.is_admin === '1';
                            if (!isAdmin) {

                                $("#jqGrid").jqGrid('hideCol', 'user_name');
                            }
                        }

                        if (window.justAdded) {
                            // Đợi 1 chút để DOM chắc chắn render xong
                            setTimeout(() => {
                                let $firstRow = $("#jqGrid tr.jqgrow:first");
                            if ($firstRow.length) {
                                $firstRow.css({
                                    "background-color": "",
                                    "transition": "background-color 1s ease"
                                });

                                requestAnimationFrame(() => {
                                    $firstRow.css("background-color", "#b3ff13");
                                setTimeout(() => {
                                    $firstRow.css("background-color", "");
                            }, 2000);
                            });
                            } else {
                                console.warn("⚠️ Không tìm thấy dòng đầu để highlight");
                            }
                            window.justAdded = false; // reset cờ
                        }, 100);
                        }



                        $(".ui-pg-selbox").css({
                            "width": "50px",
                            "padding": "10px",
                            "height": "35px"
                        });

                        $(".ui-search-toolbar select").select2({
                            width: 'resolve',
                            placeholder: "Chọn giá trị",
                            allowClear: true,
                        }).next('.select2-container').find('.select2-selection--single').css({
                            'height': '31px',
                            'line-height': '31px'
                        });
                        console.log("Data loaded successfully.");
                        $("option[value=100000000]").text('All');
                        $("tr.jqgrow:odd").css({ "backgroundColor": "#f0f2f6" });
                        $(".ui-jqgrid .ui-jqgrid-htable th").css({
                            "border": "1px solid #cfd8e3",
                            "background": "linear-gradient(to bottom, #f0f2f6, #d9e0ea)",
                            "box-shadow": "0 3px 5px rgba(0, 0, 0, 0.2)",
                            "text-shadow": "1px 1px 0px #fff",
                            "font-weight": "bold",
                            "padding": "3px"
                        });
                        $(".ui-jqgrid-sortable").css({ "font-weight": "bold", "font-size": "14px" });
                        $("input[type=checkbox], input[type=radio]").css({ "visibility": "inherit", "display": "inline" });
                        $(".ui-jqgrid-htable").css({ "height": "50px" });
                        $(".ui-jqgrid tr.jqgrow td").css({ "height": "40px", "border": "1px solid #FFF", "font-size": "13px" });
                        $(".ui-jqgrid .ui-jqgrid-hdiv").css({ "border-radius": "6px 6px 0 0" });
                    }
                });
                $(window).on("resize", function () {
                    var newWidth = $("#jqGrid").closest(".ui-jqgrid").parent().width();
                    $("#jqGrid").jqGrid("setGridWidth", newWidth, true);
                }).trigger('resize');


                // ✅ Lắng nghe sự kiện nút phục hồi
                $("#jqGrid").on('click', '.btn-restore', function () {
                    const id = $(this).data('id');
                    if (!confirm("Bạn có chắc muốn khôi phục bản ghi này?")) return;

                    $.post(ajax_object.ajaxurl, {
                        action: 'restore_jqgrid_data',
                        table: selectedTable,
                        id: id
                    }, function (response) {
                        if (response.success) {
                            alert("Khôi phục thành công");
                            $("#jqGrid").trigger("reloadGrid");
                        } else {
                            alert("Khôi phục thất bại: " + response.data);
                        }
                    });
                });

//batdaunutedit
                $("#jqGrid").on('click', '.btn-edit', function (e) {
                    e.preventDefault();       // Ngăn hành vi mặc định (nếu là thẻ <a>)
                    e.stopPropagation();      // ✅ Ngăn sự kiện lan lên grid (onCellSelect)
                    var rowId = $(this).closest('tr.jqgrow').attr('id');
                    $("#jqGrid").jqGrid("editGridRow", rowId, {
                        // Edit options
                        url: ajaxurl,
                        mtype: "POST",
                        editData: {
                            action: "edit_jqgrid_data",
                            table: selectedTable,
                            id: rowId
                        },
                        beforeSubmit: function (postdata, formid) {
                            if(selectedTable == 'wp_dulieunhadat'){

                                // Remove commas before saving
                                postdata.giaban = postdata.giaban.replace(/,/g, ''); // Remove commas
                                postdata.giathue = postdata.giathue.replace(/,/g, ''); // Remove commas
                                postdata.giachot = postdata.giachot.replace(/,/g, ''); // Remove commas

                                var product_type = $("#product_type").val(); // hoặc lấy từ formid nếu cần

                                if (product_type === "517") {
                                    if (!postdata.code_product || postdata.code_product.trim() === "") {
                                        return [false, "Bạn phải nhập Mã căn khi chọn loại Dự án!"];
                                    }
                                }
                                if (product_type === "515") {
                                    if (!postdata.sonha || postdata.sonha.trim() === "") {
                                        return [false, "Bạn phải nhập Số Nhà khi chọn loại Nhà ở riêng lẻ!"];
                                    }
                                }

                                var transaction_type = $("#transaction_type").val(); // hoặc lấy từ formid nếu cần

                                if (transaction_type === "509") {
                                    if (!postdata.giaban || postdata.giaban.trim() === "") {
                                        return [false, "Bạn phải nhập Giá bán khi chọn loại Bán!"];
                                    }
                                }
                                if (transaction_type === "511") {
                                    if (!postdata.giathue || postdata.giathue.trim() === "") {
                                        return [false, "Bạn phải nhập Giá thuê khi chọn loại Thuê!"];
                                    }
                                }

                                return [true, '', postdata];
                            }
                        },

                        afterShowForm: function($form) {
                            setupAutoCalc();
                            setTimeout(function () {
                                // Tìm <tr> chứa ô input name="contact_info"
                                const contactInputRow = $form.find('tr').has('input[name="contact_info"]');
                                contactInputRow.hide();
                                // Tìm <tr> chứa ô input name="contact_all"
                                const contactAllInputRow = $form.find('tr').has('input[name="contact_all"]');
                                contactAllInputRow.hide();
                            }, 500);
                            // Chỉ gắn dấu * cho #tr_code_product và #tr_giaban thôi, bỏ #tr_name_area
                            const requiredRows = ["#tr_sonha","#tr_code_product", "#tr_giaban","#tr_name_area"];

                            requiredRows.forEach(function(rowId) {
                                const labelTd = $form.find(rowId + " .CaptionTD");
                                const currentText = labelTd.text().trim();

                                if (!currentText.includes("*") && labelTd.find("i.fa-asterisk").length === 0) {
                                    labelTd.html('<i class="fa fa-asterisk" style="color:red; margin-right:5px;"></i>' + currentText);
                                }
                            });
                            //dangcanchonay
                            //const rowId =  $("#jqGrid").jqGrid('getGridParam', 'selrow');
                            const rowData =  $("#jqGrid").jqGrid('getRowData', rowId);


                            // Gán lại input hidden nếu đã có trên form
                            $('#sohong_image_id').val(rowData.sohong_image_id || '');
                            $('#bosung_image_id').val(rowData.bosung_image_id || '');

                            // Gọi ajax để hiển thị ảnh preview
                            if (rowData.sohong_image_id) {
                                jQuery.post(ajax_object.ajaxurl, {
                                    action: 'get_image_url',
                                    image_id: rowData.sohong_image_id
                                }, function(res) {
                                    if (res.success) {
                                        $('#sohong_image_preview').attr('src', res.data.url).show();
                                    }
                                });
                            }

                            if (rowData.bosung_image_id) {
                                jQuery.post(ajax_object.ajaxurl, {
                                    action: 'get_image_url',
                                    image_id: rowData.bosung_image_id
                                }, function(res) {
                                    if (res.success) {
                                        $('#bosung_image_preview').attr('src', res.data.url).show();
                                    }
                                });
                            }

                        },
                        closeAfterEdit:true,
                        closeOnEscape: true,
                        onClose: function () {
                            $(".select2-dropdown").hide();
                            return true;
                        },
                        width:1000,
                        recreateForm: true,
                        beforeShowForm: function ($form) {


                            setTimeout(function () {
                                // Inject CSS đẹp
                                const style = document.createElement("style");
                                style.innerHTML = `
        .group-title-row {
            margin-top: 16px !important;
        }

        .dynamic-contact-row {
            background-color: #f9fafb;
            box-shadow: none !important;
        }

        .dynamic-contact-row td {
            padding: 10px 14px !important;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .dynamic-contact-row input,
        .dynamic-contact-row select {
            flex: 1 1 140px;
            min-width: 140px;
            padding: 6px 10px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: #fff;
            box-sizing: border-box;
        }

        .dynamic-contact-row button.remove-contact-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        .add-contact-btn {
            margin-top: 8px;
            margin-left: 14px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
    `;
                                document.head.appendChild(style);

                                const tbodies = Array.from(document.querySelectorAll("#TblGrid_jqGrid tbody"));

                                const groupConfigs = [
                                    { from: 1, to: 27, title: "🔶 Thông tin chính", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 28, to: 34, title: "🔷 Thông tin liên hệ", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 35, to: 38, title: "🟢 Diện tích", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 38, to: 60, title: "🔸 Thông tin khác", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" }
                                ];

                                groupConfigs.forEach((config, groupIndex) => {
                                    let group = tbodies
                                            .map(tbody => {
                                            const tr = tbody.querySelector("tr");
                                const rowpos = parseInt(tr?.getAttribute("data-rowpos"), 10);
                                return { tbody, rowpos };
                            })
                                .filter(item => !isNaN(item.rowpos) && item.rowpos >= config.from && item.rowpos <= config.to)
                                .sort((a, b) => a.rowpos - b.rowpos)
                                .map(item => item.tbody);

                                if (group.length === 0) return;

                                const tableBody = group[0].parentElement;

                                if (groupIndex !== 0) {
                                    const spacerBody = document.createElement("tbody");
                                    spacerBody.classList.add("group-spacer-body");
                                    spacerBody.innerHTML = `<tr><td colspan="2" style="height: 16px; padding:0; border: none;"></td></tr>`;
                                    tableBody.insertBefore(spacerBody, group[0]);
                                }

                                const titleRow = document.createElement("tr");
                                titleRow.classList.add("group-title-row");
                                titleRow.innerHTML = `
            <td colspan="2" style="
                font-weight:600;
                padding:8px 14px;
                background-color:${config.color};
                border:1px solid ${config.border};
                border-bottom:none;
                border-radius:8px 8px 0 0;
                color:${config.text};
                font-size:15px;
                letter-spacing:0.3px;
            ">${config.title}</td>`;
                                tableBody.insertBefore(titleRow, group[0]);

                                group.forEach((tbody, index) => {
                                    tbody.classList.add("group-middle");
                                tbody.style.backgroundColor = config.color;
                                tbody.style.border = "none";
                                tbody.style.boxShadow = "0 1px 4px rgba(0, 0, 0, 0.06)";

                                if (index === group.length - 1) {
                                    tbody.style.border = `1px solid ${config.border}`;
                                    tbody.style.borderTop = "none";
                                    tbody.style.borderRadius = "0 0 8px 8px";
                                } else {
                                    tbody.style.borderLeft = `1px solid ${config.border}`;
                                    tbody.style.borderRight = `1px solid ${config.border}`;
                                }
                            });
                            });

                                // Load contact_info
                                const contactRaw = $("#contact_info").val();
                                if (!contactRaw) return;

                                let contactList = [];
                                try {
                                    contactList = JSON.parse(contactRaw);
                                } catch (err) {
                                    console.error("Lỗi parse contact_info:", err);
                                    return;
                                }

                                const lienHeGroup = groupConfigs.find(g => g.title.includes("liên hệ"));
                                if (!lienHeGroup) return;

                                const lienHeTbodies = tbodies
                                        .map(tbody => {
                                        const tr = tbody.querySelector("tr");
                                const rowpos = parseInt(tr?.getAttribute("data-rowpos"), 10);
                                return { tbody, rowpos };
                            })
                                .filter(item => !isNaN(item.rowpos) && item.rowpos >= lienHeGroup.from && item.rowpos <= lienHeGroup.to)
                                .sort((a, b) => a.rowpos - b.rowpos)
                                .map(item => item.tbody);

                                const lastTbody = lienHeTbodies[lienHeTbodies.length - 1];
                                if (!lastTbody) return;

                                // Hàm tạo dòng liên hệ
                                function createContactRow(data = {}) {
                                    const tr = document.createElement("tr");
                                    tr.classList.add("dynamic-contact-row");
                                    tr.innerHTML = `
            <td class="DataTD">
                <select name="vaitro_more[]">
                    <option value="627">Chủ Nhà</option>
                    <option value="629">Độc Quyền</option>
                    <option value="631">Môi Giới Hợp Tác</option>
                    <option value="633">Người Thân Chủ Nhà</option>
                    <option value="635">Trợ Lý Chủ Nhà</option>
                    <option value="637">Đại Diện Công Ty</option>
                    <option value="639">Đại Diện Chủ Nhà</option>
                </select>
                <input type="text" name="name_more[]" placeholder="Tên" value="${data.name_more || ''}">
                <input type="text" name="dienthoaididong_more[]" placeholder="Điện thoại" value="${data.phone_more || ''}">
                <select name="gioitinh_more[]">
                    <option value="643">Nam</option>
                    <option value="645">Nữ</option>
                </select>
                <button type="button" class="remove-contact-btn">❌</button>
            </td>
        `;

                                    // Gán lại value cho select nếu có dữ liệu
                                    tr.querySelector('select[name="vaitro_more[]"]').value = data.vaitro_more || "";
                                    tr.querySelector('select[name="gioitinh_more[]"]').value = data.gender_more || "";

                                    // Nút xoá
                                    tr.querySelector(".remove-contact-btn").addEventListener("click", () => {
                                        tr.remove();
                                });

                                    return tr;
                                }

                                // Render lại các dòng contact
                                if (!isAdmin && tooLate) {}else{
                                    contactList.forEach(contact => {
                                        const row = createContactRow(contact);
                                    lastTbody.appendChild(row);
                                });
                                }


                                // Nút thêm mới
                                const addBtn = document.createElement("button");
                                addBtn.textContent = "+ Thêm liên hệ";
                                addBtn.type = "button";
                                addBtn.className = "add-contact-btn";
                                addBtn.addEventListener("click", () => {
                                    const newRow = createContactRow();
                                lastTbody.appendChild(newRow);
                            });

                                // Chèn nút thêm vào sau nhóm liên hệ
                                const lastRow = lastTbody.querySelector("tr:last-child");
                                if (lastRow) {
                                    const newTr = document.createElement("tr");
                                    const newTd = document.createElement("td");
                                    newTd.colSpan = 2;
                                    newTd.appendChild(addBtn);
                                    newTr.appendChild(newTd);
                                    lastTbody.appendChild(newTr);
                                }
                            }, 300);



                            setTimeout(() => {
                                $form.find("tbody").each(function () {
                                const currentStyle = this.getAttribute("style")?.replace(/\s+/g, '').toLowerCase();
                                if (currentStyle === "width:100%;display:block;") {
                                    this.style.setProperty("height", "20px", "important");
                                }
                            });
                        }, 50);

//paddyedit
                            // Lấy giá trị dropdown hiện tại

                            $("#sData").click(function() {
                                var formValid = true;  // Flag to check if form is valid
                                var firstInvalidField = null;  // To store the first invalid field

                                // Loop through all input fields and select2 fields to check for emptiness or invalidity
                                $("form input, form select").each(function() {
                                    // Check if the field is empty
                                    if ($(this).val() === "" || $(this).hasClass('invalid')) {
                                        if (!firstInvalidField) {
                                            firstInvalidField = $(this);  // Store the first invalid field
                                        }
                                        formValid = false;  // Set the form as invalid
                                    }

                                    // For select2 fields, check if the select2 dropdown has an empty value
                                    if ($(this).is("select") && $(this).hasClass("select2-hidden-accessible") && $(this).val() === null) {
                                        if (!firstInvalidField) {
                                            firstInvalidField = $(this);  // Store the first invalid select2 field
                                        }
                                        formValid = false;  // Set the form as invalid
                                    }
                                });

                                // If form is invalid, scroll to the first invalid field
                                if (!formValid && firstInvalidField) {
                                    // Check if the invalid field is a select2 field and scroll to its container
                                    if (firstInvalidField.hasClass("select2-hidden-accessible")) {
                                        firstInvalidField = firstInvalidField.siblings(".select2-container");  // Get the select2 container
                                    }

                                    // Scroll to the field
                                    $('html, body').animate({
                                        scrollTop: firstInvalidField.offset().top - 500  // Scroll to the field
                                    }, 500); // 500 ms duration for scrolling
                                }

                                return formValid;  // Return the validation result (true if valid, false otherwise)
                            });

                            // Select the input field for the specific column (replace 'column_name' with your column identifier)




                            // fix template table
                            //=========================
                            //=========================
                            // --- jQuery chỉnh style bảng ---
                            var $table = $form.find("table");

// Style tổng thể cho table
                            $table.css({
                                "width": "100%",
                                "border-collapse": "collapse",
                                "display": "flex",
                                "flex-wrap": "wrap",
                                "gap": "0px",
                                "overflow": "hidden",
                                "padding": "16px",
                                "background-color": "#f9fafb",   // nền nhẹ nhàng
                                "border-radius": "8px",
                                "box-shadow": "0 4px 8px rgba(0,0,0,0.05)"
                            });

// Lấy toàn bộ tr để xử lý nhóm lại
                            var $rows = $table.find("tr");

// Xóa các tbody cũ
                            $table.find("tbody").remove();

// Tạo lại tbody đầu (2 hàng đầu giữ nguyên)
                            var $firstTbody = $("<tbody></tbody>").css({
                                "width": "100%",
                                "display": "block"
                            });
                            $firstTbody.append($rows.slice(0, 2));
                            $table.append($firstTbody);

// Nhóm các row còn lại theo từng 2 row
                            var groupSize = 2;
                            var restRows = $rows.slice(2);

                            for (var i = 0; i < restRows.length; i += groupSize) {
                                var $groupTbody = $("<tbody></tbody>").css({
                                    "display": "flex",
                                    "flex-wrap": "wrap",
                                    "gap": "8px",
                                    "width": "100%"
                                });

                                var group = restRows.slice(i, i + groupSize);
                                $groupTbody.append(group);
                                $table.append($groupTbody);
                            }

// Style từng dòng tr
                            $table.find("tr").css({
                                "display": "flex",
                                "flex-direction": "column",
                                "flex": "1 1 calc(50% - 12px)",  // 2 cột
                                "margin": "10px 0",
                                "max-width": "100%",
                                "box-sizing": "border-box",
                                "position": "relative"
                            });

// Style td và th
                            $table.find("td, th").css({
                                "padding": "6px 10px"
                            });

// Thêm icon Dashicons trước label
                            $table.find("label").each(function() {
                                var $label = $(this);
                                // Nếu chưa có icon thì thêm
                                if (!$label.find("span.dashicons").length) {
                                    $label.prepend('<span class="dashicons dashicons-admin-home" style="color:#3b82f6; margin-right:6px;"></span>');
                                }
                            });

// Style label: cho icon + text nằm ngang, căn giữa, khoảng cách đẹp
                            $table.find("label").css({
                                "display": "flex",
                                "align-items": "center",
                                "gap": "6px",
                                "font-weight": "600",
                                "color": "#333",
                                "margin-bottom": "4px",
                                "font-size": "14px"
                            });

// Style input, select, textarea cho gọn, đẹp + hiệu ứng bóng, hover, focus
                            $table.find("input, select, textarea").css({
                                "padding": "8px 12px",
                                "font-size": "15px",
                                "border": "1.5px solid #ccc",
                                "border-radius": "6px",
                                "width": "100%",
                                "box-sizing": "border-box",
                                "outline": "none",
                                "background-color": "#fafafa",
                                "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)",
                                "transition": "border-color 0.3s, box-shadow 0.3s, background-color 0.3s"
                            }).on("focus", function () {
                                $(this).css({
                                    "border-color": "#2563eb",
                                    "box-shadow": "0 0 12px rgba(37,99,235,0.6)",
                                    "background-color": "#fff"
                                });
                            }).on("blur", function () {
                                $(this).css({
                                    "border-color": "#ccc",
                                    "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)",
                                    "background-color": "#fafafa"
                                });
                            }).on("mouseenter", function () {
                                $(this).css({
                                    "border-color": "#60a5fa",
                                    "box-shadow": "0 0 8px rgba(59,130,246,0.3)"
                                });
                            }).on("mouseleave", function () {
                                if (!$(this).is(":focus")) {
                                    $(this).css({
                                        "border-color": "#ccc",
                                        "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)"
                                    });
                                }
                            });

// Style cho .form-row trong table để các input nằm ngang, đẹp hơn
                            $table.find(".form-row").css({
                                "display": "flex",
                                "align-items": "center",
                                "justify-content": "space-between",
                                "gap": "8px",
                                "width": "100%",
                                "overflow": "hidden",
                                "padding": "8px 0",
                                "box-sizing": "border-box",
                                "flex-wrap": "wrap"
                            });

// Bố cục label + input trong .form-row để label cố định width, input tự động giãn
                            $table.find(".form-row label").css({
                                "flex": "0 0 140px",
                                "white-space": "nowrap"
                            });

                            $table.find(".form-row input, .form-row select, .form-row textarea").css({
                                "flex": "1 1 auto"
                            });

// Reinitialize Select2 on the select elements after DOM manipulation


//==========================



                            //select2
                            $form.find("select").each(function () {
                                $(this).select2({
                                    width: '100%',
                                    placeholder: 'Chọn thông tin',
                                    allowClear: false,
                                    minimumResultsForSearch: 0 // Enable search functionality
                                })
                            });

                            // Apply Select2 styling
                            applySelect2Styles();


                            /* var $thoigianthue = $('input[name="thoigianthue"]');
                             if ($thoigianthue.length > 0) {
                             $thoigianthue.datepicker({
                             dateFormat: 'yy-mm-dd',
                             changeMonth: true,
                             changeYear: true
                             });
                             };*/
                            var $hethanthue = $('input[name="hethanthue"]');
                            if ($hethanthue.length > 0) {
                                $hethanthue.datepicker({
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: true,
                                    changeYear: true
                                });
                            }




                            $form.addClass("custom-jqgrid-form");
                            // Modify the form header style
                            $form.parent().find('.ui-jqdialog-titlebar').css({
                                'background': '#FFA500', // Orange background
                                'color': 'white',
                                'font-size': '16px',
                                'font-weight': 'bold',
                                'padding': '10px',
                                'border-radius': '8px 8px 0 0'
                            });

                            // Style input fields
                            $form.find('input, select, textarea').css({
                                'width': '100%',
                                'padding': '8px',
                                'border': '1px solid #ccc',
                                'border-radius': '5px',
                                'font-size': '13px',
                            });

                            // Style the form buttons
                            $form.parent().find('.ui-jqdialog-buttonset button').css({
                                'background': '#4CAF50',
                                'color': 'white',
                                'font-size': '14px',
                                'padding': '8px 12px',
                                'border-radius': '5px',
                                'border': 'none',
                                'cursor': 'pointer',
                                'transition': '0.3s'
                            }).hover(
                                function () {
                                    $(this).css('background', '#388E3C');
                                },
                                function () {
                                    $(this).css('background', '#4CAF50');
                                }
                            );

                            // Change title text
                            if(selectedTable == 'wp_duan') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Cập Nhật Dự Án');
                            }else if(selectedTable == 'wp_dulieunhadat') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Cập Nhật Sản Phẩm');
                            }else if(selectedTable == 'wp_khachhang') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Cập Nhật Khách Hàng');
                            }

                            // Apply modern styling to the header
                            $(".ui-jqdialog-titlebar").css({
                                "color": "#ffa500",          // White text
                                "font-size": "18px",
                                "font-weight": "600",
                                "padding": "15px",
                                "border-radius": "2px 2px 0 0",
                                "border-bottom": "3px solid #4caf50" // Darker green border bottom
                            });

                            // Style the close button for better UX
                            $(".ui-jqdialog-titlebar-close").css({
                                "background": "transparent",
                                "border": "none",
                                "cursor": "pointer",
                                "padding": "4px",
                                "transition": "0.3s"
                            });

                            // Hover effect for close button
                            $(".ui-jqdialog-titlebar-close span").css({
                                "color": "#fff",
                                "font-size": "16px"
                            });

                            $(".ui-jqdialog-titlebar-close").hover(
                                function () { $(this).css("opacity", "0.7"); },
                                function () { $(this).css("opacity", "1"); }
                            );


                            var dlgDiv = $("#editmod" + $grid[0].id);
                            var parentDiv = dlgDiv.parent();
                            var dlgWidth = dlgDiv.width();
                            var parentWidth = parentDiv.width();

                            // Đặt modal lên sát mép trên màn hình
                            dlgDiv.css({
                                "top": "-70px",  // Đẩy modal lên sát viền trên màn hình
                                "left": Math.round((parentWidth - dlgWidth) / 2) + "px",
                                /* "z-index": "9999", // Luôn nằm trên các phần tử khác
                                 "position": "fixed" // Giữ nguyên vị trí khi cuộn trang*/
                            });



                            // Fix the main dialog to avoid horizontal scrolling
                            /*var $dialog = $form.closest(".ui-jqdialog");
                             $dialog.css({
                             "border-radius": "10px",
                             "background": "#fff",
                             "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                             //"max-width": "800px",
                             "width": "70%",  // Prevents form from being too wide
                             "overflow": "hidden", // Prevents horizontal scrolling,
                             "padding" :"0px",
                             });*/
                            // Fix the main dialog to avoid horizontal scrolling
                            var $dialog = $form.closest(".ui-jqdialog");
                            const winWidth = $(window).width();
                            if (winWidth <= 768) {

                                const dialogWidth = Math.min(winWidth * 0.9, 400); // tối đa 400px hoặc 90% màn hình


                                $dialog.css({
                                    width: dialogWidth + "px",
                                    left: ((winWidth - dialogWidth) / 2) + "px",
                                    top: "60px",
                                    //"z-index": 9999,
                                    "border-radius": "10px",
                                    "background": "#fff",
                                    "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                                    //"max-width": "800px",
                                    "width": "85%",  // Prevents form from being too wide
                                    "overflow": "hidden", // Prevents horizontal scrolling,
                                    "padding": "0px",
                                });
                            }else{
                                $dialog.css({
                                    "border-radius": "10px",
                                    "background": "#fff",
                                    "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                                    //"max-width": "800px",
                                    "overflow": "hidden", // Prevents horizontal scrolling,
                                    "padding": "0px",
                                });
                            }

                            // Style labels
                            $form.find("td.CaptionTD").css({
                                "font-weight": "bold",
                                "color": "#333",
                                //"width": "100%",
                                "margin-bottom": "5px",
                                "text-align": "left",
                                "position":"absolute",
                                "top": "-10px",
                                "z-index": "1",
                                "left": "21px",
                                "height": "30px",
                                "background" : "linear-gradient(to bottom, transparent 50%, rgb(255, 255, 255) 50%, rgb(255, 255, 255) 51%, transparent 65%)",

                            });

                            // Style input fields
                            $form.find("td.DataTD input, td.DataTD select").css({
                                "width": "100%",
                                "padding": "8px",
                                "border": "1px solid #ccc",
                                "border-radius": "5px",
                                "font-size": "14px",
                                "box-sizing": "border-box",
                                "height": "40px",
                                "transition": "all 0.3s ease-in-out" // Hiệu ứng mượt khi thay đổi trạng thái
                            });

                            // Hover effect
                            $form.find("td.DataTD input, td.DataTD select").hover(
                                function () {
                                    $(this).css("border-color", "#007bff"); // Khi hover, viền đổi màu xanh
                                },
                                function () {
                                    $(this).css("border-color", "#ccc"); // Khi không hover, trở lại màu cũ
                                }
                            );

                            // Focus effect
                            $form.find("td.DataTD input, td.DataTD select").on("focus", function () {
                                $(this).css({
                                    "border-color": "#007bff",
                                    "box-shadow": "0 0 5px rgba(0, 123, 255, 0.5)"
                                });
                            }).on("blur", function () {
                                $(this).css({
                                    "border-color": "#ccc",
                                    "box-shadow": "none"
                                });
                            });

                            // Fix button alignment
                            $(".EditButton").css({
                                "display": "flex",
                                "justify-content": "center",
                                "gap": "15px",
                                "margin-top": "20px"
                            });

                            // Style Submit button
                            $("#sData").html('<span style="color: #fff;">✔ Tiếp Tục</span>').css({
                                "background": "#4CAF50",
                                "border-radius": "5px",
                                "padding": "10px 20px",
                                "font-weight": "bold",
                                "box-shadow": "0 2px 4px rgba(0,0,0,0.2)",
                                "border": "none"
                            });

                            // Style Cancel button
                            $("#cData").html('<span style="color: #fff;">✖ Hủy</span>').css({
                                "background": "#d9534f",
                                "border-radius": "5px",
                                "padding": "10px 20px",
                                "font-weight": "bold",
                                "box-shadow": "0 2px 4px rgba(0,0,0,0.2)",
                                "border": "none"
                            });



                            // Hide form error message
                            $("#FormError").hide();
                            $('[value="_empty"]').hide();



                            // handle ajax wp_province, wp_district, wp_wards
                            //var rowId = $(this).jqGrid('getGridParam', 'selrow');
                            var rowData = $(this).jqGrid('getRowData', rowId);



                            // check phan sau 5 phut thì admin toàn quyền sửa, user chỉnh sửa những field cho phép

                            const createdTime = new Date(rowData.datecreate); // hoặc dùng field khác nếu cần
                            const now = new Date();
                            const diffMs = now - createdTime;

                            const isAdmin = ajax_object.is_admin === true || ajax_object.is_admin === '1';
                            const tooLate = diffMs > 5 * 60 * 1000;

                            if (!isAdmin && tooLate) {
                                // Ẩn hoặc disable field sau 5 phút nếu KHÔNG phải admin
                                $form.find('#tr_name').hide();
                                $form.find('#tr_vaitro').hide();
                                $form.find('#tr_dienthoaididong').hide();
                                $form.find('#tr_gioitinh').hide();
                            }



                            var provinceId = rowData.wp_province;
                            var districtId = rowData.wp_district;
                            var wardId = rowData.wp_wards;

                            // Reset các event tránh bị chồng event
                            $('#wp_province').off('change').on('change', function() {
                                var selectedProvince = $(this).val();
                                loadDistricts(selectedProvince);
                            });

                            $('#wp_district').off('change').on('change', function() {
                                var selectedDistrict = $(this).val();
                                loadWards(selectedDistrict);
                            });

                            // Set sẵn province
                            $('#wp_province').val(provinceId);

                            // Gọi load districts và tự set district + ward
                            if (provinceId) {
                                loadDistricts(provinceId, districtId);

                                // Vì loadDistricts sẽ tự trigger loadWards(selectedDistrictId)
                                // Nên gắn thêm loadWards khi loadDistrict xong
                                $(document).one('ajaxStop', function() {
                                    // Đợi tất cả ajax hoàn thành
                                    if (districtId) {
                                        loadWards(districtId, wardId);
                                    }
                                });
                            }



                            // hide show input
                            // Event listener for select dropdown change
                            $("#tr_code_product, #tr_name_area,#tr_sonha, #tr_sothua, #tr_soto").fadeOut();
                            /*$("#product_type").change(function () {
                             var selectedValue = $(this).val(); // Lấy giá trị đã chọn

                             if (selectedValue === "517") {
                             $("#tr_code_product, #tr_name_area").show(); // Hiển thị các input liên quan đến sản phẩm
                             $("#tr_sonha, #tr_sothua, #tr_soto").hide(); // Ẩn các input không liên quan
                             } else if (selectedValue === "515") {
                             $("#tr_code_product, #tr_name_area").hide(); // Ẩn các input sản phẩm
                             $("#tr_sonha, #tr_sothua, #tr_soto").show(); // Hiển thị các input liên quan đến địa chỉ
                             } else {
                             $("#tr_code_product, #tr_name_area, #tr_sonha, #tr_sothua, #tr_soto").hide(); // Ẩn tất cả các input khi không có lựa chọn nào phù hợp
                             }
                             });*/
                            setTimeout(function () {
                                updateFields();

                                // Gắn sự kiện change với namespace để tránh lỗi gắn trùng
                                $("#product_type", $form).off("change.producttype").on("change.producttype", function () {
                                    updateFields();
                                });

                            }, 1000); // delay để chờ DOM trong form jqGrid render xong

                            function updateFields() {
                                var selectedValue = $("#product_type", $form).val();

                                if (selectedValue === "517") {
                                    $("#tr_code_product, #tr_name_area", $form).show();
                                    $("#tr_sonha, #tr_sothua, #tr_soto", $form).hide();
                                } else if (selectedValue === "515") {
                                    $("#tr_code_product, #tr_name_area", $form).hide();
                                    $("#tr_sonha, #tr_sothua, #tr_soto", $form).show();
                                } else {
                                    $("#tr_code_product, #tr_name_area, #tr_sonha, #tr_sothua, #tr_soto", $form).hide();
                                }
                            }




                            // Event listener for select dropdown change
                            // Ẩn tất cả các phần liên quan ban đầu
                            $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_donvi_giaban,#tr_giathue,#tr_thoigianthue,#tr_hethanthue,#tr_giaban,#tr_giachot").hide();

// Gán event change cho dropdown
                            $form.find("#transaction_type").change(function () {
                                var selectedValue = $(this).val();
                                console.log(selectedValue);

                                if (selectedValue === "509") {
                                    $("#tr_donvi_giaban,#tr_giaban,#tr_giachot").show();
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_giathue,#tr_thoigianthue,#tr_hethanthue").show();
                                    $('#tr_giathue .CaptionTD').text('HĐ thuê');
                                    $('#tr_donvi_giathue .CaptionTD').text('Đơn vị HĐ thuê');
                                    $('#tr_donvi_thoigiangia .CaptionTD').text('HĐ thuê theo');
                                } else if (selectedValue === "511") {
                                    $("#tr_donvi_giaban,#tr_giaban,#tr_giachot").hide();
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_giathue,#tr_thoigianthue,#tr_hethanthue").show();
                                    $('#tr_giathue .CaptionTD').html('<i class="fa fa-asterisk" style="color:red; margin-right:5px;"></i> Giá thuê');
                                    $('#tr_donvi_giathue .CaptionTD').text('Đơn vị giá thuê');
                                    $('#tr_donvi_thoigiangia .CaptionTD').text('Giá thuê theo');
                                } else {
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_donvi_giaban,#tr_giathue,#tr_thoigianthue,#tr_hethanthue,#tr_giaban,#tr_giachot").hide();
                                }
                            });

// 🔥 Gọi sự kiện change thủ công khi trang load để cập nhật giao diện ban đầu
                            $form.find("#transaction_type").trigger("change");

                            // Event listener for select dropdown change
                            $("#tr_hoahong_sotien,#tr_hoahong_thang, #tr_hoahong_tyle").hide();
                            $form.find("#loaihoahong").change(function () {
                                var selectedValue = $(this).val();
                                console.log(selectedValue);
                                if (selectedValue === "651") {
                                    $("#tr_hoahong_tyle").show(); // Show input1 and input2
                                    $("#tr_hoahong_sotien, #tr_hoahong_thang").hide(); // Hide input3 and input4
                                } else if (selectedValue === "653") {
                                    $("#tr_hoahong_thang").show(); // Show input1 and input2
                                    $("#tr_hoahong_sotien, #tr_hoahong_tyle").hide(); // Hide input3 and input4
                                } else if (selectedValue === "649") {
                                    $("#tr_hoahong_sotien").show(); // Show input1 and input2
                                    $("#tr_hoahong_thang, #tr_hoahong_tyle").hide(); // Hide input3 and input4
                                }
                                else {
                                    $("#tr_hoahong_sotien,#tr_hoahong_thang, #tr_hoahong_tyle").hide();
                                }
                            });
                            // 🔥 Gọi thủ công để khởi tạo giao diện đúng theo giá trị ban đầu
                            $form.find("#loaihoahong").trigger("change");

//format number
                            $("input[name='thoigianthue'],input[name='dtd_ngang'], input[name='dtd_dai'],input[name='dtd_mathau'], input[name='dtd_congnhan'],input[name='dtqh_ngang'], input[name='dtqh_dai'], input[name='dtqh_mathau'], input[name='dtqh_xaydung'], input[name=''],input[name=''], input[name=''], input[name='ttk_duongrong'], input[name='ttk_dtsd'], input[name='dienthoaididong']").on("input", function(e) {
                                var value = $(this).val();
                                var sanitizedValue = value.replace(/[^0-9.]/g, '');
                                if ((sanitizedValue.match(/\./g) || []).length > 1) {
                                    sanitizedValue = sanitizedValue.replace(/\.+$/, '');
                                }
                                $(this).val(sanitizedValue);
                            });

                            // format a thousand separator
                            $("input[name='giathue'],input[name='giaban'],input[name='giachot']").on("input", function(e) {
                                var value = $(this).val();

                                // Remove any non-numeric characters except for a decimal point
                                var sanitizedValue = value.replace(/[^0-9.]/g, '');

                                // Ensure that only one decimal point is allowed
                                if ((sanitizedValue.match(/\./g) || []).length > 1) {
                                    sanitizedValue = sanitizedValue.replace(/\.+$/, '');  // Remove extra decimal points
                                }

                                // Format the number with comma as thousand separator
                                var parts = sanitizedValue.split('.');
                                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add comma separator every three digits

                                // Combine the integer and decimal parts
                                sanitizedValue = parts.join('.');

                                // Update the input field with the sanitized and formatted value
                                $(this).val(sanitizedValue);
                            });
//paddyeditnutonrow

                            if (selectedTable == 'wp_dulieunhadat') {
                                setTimeout(function() {
                                    const $container = $('#filter-usernhanvien').next('.select2-container');

                                    $container.find('.select2-selection--single').css({
                                        'height': '38px',
                                        'line-height': '38px',
                                        'padding': '0 16px',
                                        'border-radius': '6px',
                                        'border': 'none',
                                        'box-shadow': '0 4px 8px rgba(44, 123, 229, 0.4)', // shadow xanh dịu
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'box-sizing': 'border-box',
                                        'font-size': '14px',
                                        'font-weight': 'bold',
                                        'background-color': '#FFA500', // nền xanh button
                                        'color': '#fff',
                                        'cursor': 'pointer',
                                        'transition': 'background-color 0.3s ease'
                                    });
                                    $container.find('.select2-selection__placeholder').css({
                                        'color': '#fff',
                                        'opacity': '1'
                                    });


                                    // Khi mở dropdown, đổi nền đậm hơn
                                    $container.find('.select2-selection--single').on('click', function() {
                                        $(this).css('background-color', '#FFA500');
                                    });
                                    // Khi dropdown đóng, trả về nền ban đầu
                                    $container.find('.select2-selection--single').on('blur', function() {
                                        $(this).css('background-color', '#FFA500');
                                    });

                                    $container.find('.select2-selection__rendered').css({
                                        'line-height': '40px',
                                        'padding-left': '0',
                                        'margin': '0',
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'height': '100%',
                                        'color': '#fff'
                                    });

                                    $container.find('.select2-selection__arrow').css({
                                        'height': '40px',
                                        'top': '0',
                                        'right': '8px',
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'color': '#fff'
                                    });
                                }, 10);
                                // Thêm nút upload + input ẩn + ảnh preview (1 ảnh) cho sổ hồng và ảnh bổ sung
                                // và upload nhiều ảnh cho sohong_image_multi nếu chưa có
                                if (!$('#upload_sohong_btn').length && !$('#upload_bosung_btn').length && !$('#upload_sohong_multi_btn').length) {
                                    var html = '' +
                                        '<div style="display: flex; justify-content: center; gap: 40px; margin-bottom: 20px;">' +

                                        // Ảnh Sổ Hồng (1 ảnh)
                                        /* '<div style="text-align: center;">' +
                                         '<label><strong>Ảnh Sổ Hồng:</strong></label><br>' +
                                         '<button type="button" id="upload_sohong_btn" style="' +
                                         'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                         'background-color: #c82333; color: white; border: none; margin-top: 6px;">' +
                                         '<i class="fas fa-upload" style="margin-right: 6px;"></i> Chọn Sổ Hồng' +
                                         '</button>' +
                                         '<input type="hidden" id="sohong_image_id" name="sohong_image_id" value="">' +
                                         '<br>' +
                                         '<img id="sohong_image_preview" src="" alt="Ảnh Sổ Hồng" style="' +
                                         'max-width: 150px; border-radius: 5px; box-shadow: 0 0 8px rgba(0,0,0,0.15);' +
                                         'display: none; margin-top: 10px;">' +
                                         '</div>' +

                                         // Ảnh bổ sung (1 ảnh)
                                         '<div style="text-align: center;">' +
                                         '<label><strong>Hình ảnh bổ sung:</strong></label><br>' +
                                         '<button type="button" id="upload_bosung_btn" style="' +
                                         'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                         'background-color: #007bff; color: white; border: none; margin-top: 6px;">' +
                                         '<i class="fas fa-upload" style="margin-right: 6px;"></i> Chọn ảnh bổ sung' +
                                         '</button>' +
                                         '<input type="hidden" id="bosung_image_id" name="bosung_image_id" value="">' +
                                         '<br>' +
                                         '<img id="bosung_image_preview" src="" alt="Ảnh bổ sung" style="' +
                                         'max-width: 150px; border-radius: 5px; box-shadow: 0 0 8px rgba(0,0,0,0.15);' +
                                         'display: none; margin-top: 10px;">' +
                                         '</div>' +*/

                                        // Upload nhiều ảnh Sổ Hồng (multi)
                                        '<div style="text-align:center; margin-bottom:20px;">' +
                                        '<label><strong>Ảnh sổ hồng/Video (Nhiều ảnh):</strong></label><br>' +
                                        '<button type="button" id="upload_sohong_multi_btn" style="' +
                                        'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                        'background-color: #28a745; color: white; border: none; margin-top: 6px;">' +
                                        '<i class="fas fa-upload" style="margin-right:6px;"></i> Chọn ảnh/video' +
                                        '</button>' +
                                        '<input type="hidden" id="sohong_image_multi" name="sohong_image_multi" value="">' +
                                        '<div id="sohong_multi_preview" style="margin-top: 10px; display:flex; gap:10px; flex-wrap: wrap;"></div>' +
                                        '</div>' +

                                        '</div>';

                                    $form.append(html);



                                    // Hàm xử lý upload nhiều ảnh (multi) với watermark và lưu ID
                                    // Hàm xử lý upload nhiều ảnh (multi) với watermark tại 3 vị trí và lưu ID
                                    function handleUploadMulti(buttonId, hiddenInputId, previewContainerId) {
                                        let frame;

                                        $(buttonId).on('click', function (e) {
                                            e.preventDefault();

                                            if (frame) {
                                                frame.open();
                                                return;
                                            }

                                            frame = wp.media({
                                                title: 'Chọn ảnh hoặc video',
                                                button: { text: 'Chọn file' },
                                                multiple: true
                                            });

                                            frame.on('select', function () {
                                                const selection = frame.state().get('selection').toArray();
                                                const previewContainer = $(previewContainerId);
                                                const hiddenInput = $(hiddenInputId);
                                                previewContainer.empty();
                                                const ids = [];

                                                // Vẽ watermark
                                                const drawWatermark3Positions = (ctx, canvas, text) => {
                                                    ctx.font = "22px Arial";
                                                    ctx.fillStyle = "rgba(255, 0, 0, 0.15)";
                                                    ctx.textAlign = "left";
                                                    ctx.textBaseline = "top";
                                                    ctx.shadowColor = "rgba(0,0,0,0.3)";
                                                    ctx.shadowOffsetX = 1;
                                                    ctx.shadowOffsetY = 1;
                                                    ctx.shadowBlur = 1;

                                                    const positions = [
                                                        { x: 40, y: 40 },
                                                        { x: canvas.width / 2 - 80, y: canvas.height / 2 - 10 },
                                                        { x: canvas.width - 240, y: canvas.height - 80 }
                                                    ];

                                                    for (const pos of positions) {
                                                        ctx.save();
                                                        ctx.translate(pos.x, pos.y);
                                                        ctx.rotate(-Math.PI / 10);
                                                        ctx.fillText(text, 0, 0);
                                                        ctx.restore();
                                                    }
                                                };

                                                // Hàm xử lý ảnh (có watermark)
                                                const processImage = (attachment) => {
                                                    return new Promise((resolve, reject) => {
                                                            const img = new Image();
                                                    img.crossOrigin = 'anonymous';
                                                    img.src = attachment.url;

                                                    img.onload = () => {
                                                        const canvas = document.createElement('canvas');
                                                        canvas.width = img.width;
                                                        canvas.height = img.height;
                                                        const ctx = canvas.getContext('2d');
                                                        ctx.drawImage(img, 0, 0);

                                                        drawWatermark3Positions(ctx, canvas, "Urbanhome.vn");

                                                        canvas.toBlob((blob) => {
                                                            if (!blob) return reject("Không tạo được blob.");

                                                        const formData = new FormData();
                                                        formData.append('action', 'upload_watermarked_image');
                                                        formData.append('file', blob, 'watermarked.jpg');

                                                        fetch(ajaxurl, {
                                                            method: 'POST',
                                                            body: formData
                                                        })
                                                            .then(res => res.json())
                                                    .then(res => {
                                                            if (res.success && res.data.url) {
                                                            const wrapper = $('<div>', {
                                                                style: 'position: relative; display: inline-block; margin: 6px;'
                                                            });

                                                            const imgEl = $('<img>', {
                                                                src: res.data.url,
                                                                style: 'max-width:120px; border-radius:5px; box-shadow:0 0 8px rgba(0,0,0,0.15); display: block;'
                                                            });

                                                            const closeBtn = $('<button>', {
                                                                    text: 'X',
                                                                    style: 'position: absolute; top: 2px; right: 2px; background: rgba(255,0,0,0.7); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer;'
                                                                }).on('click', () => {
                                                                    wrapper.remove();
                                                            ids.splice(ids.indexOf(res.data.id), 1);
                                                            hiddenInput.val(ids.join(','));
                                                        });

                                                            wrapper.append(imgEl, closeBtn);
                                                            previewContainer.append(wrapper);
                                                            ids.push(res.data.id);
                                                            resolve();
                                                        } else {
                                                            alert('Lỗi upload: ' + (res.data?.message || 'Không rõ lỗi'));
                                                            reject();
                                                        }
                                                    })
                                                    .catch(err => {
                                                            alert('Lỗi mạng: ' + err.message);
                                                        reject(err);
                                                    });
                                                    }, 'image/jpeg', 0.92);
                                                    };

                                                    img.onerror = () => {
                                                        alert('Không tải được ảnh: ' + attachment.url);
                                                        reject();
                                                    };
                                                });
                                                };

                                                // Xử lý file
                                                (async () => {
                                                    for (const att of selection) {
                                                    const attachment = att.toJSON();
                                                    const url = attachment.url || '';
                                                    const ext = url.split('.').pop().toLowerCase();

                                                    try {
                                                        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                                                            await processImage(attachment);
                                                        } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
                                                            const wrapper = $('<div>', {
                                                                style: 'position: relative; display: inline-block; margin: 6px;'
                                                            });

                                                            const video = document.createElement('video');
                                                            video.src = attachment.url;
                                                            video.controls = true;
                                                            video.style.maxWidth = '120px';
                                                            video.style.borderRadius = '5px';
                                                            video.style.boxShadow = '0 0 8px rgba(0,0,0,0.15)';
                                                            video.style.display = 'block';
                                                            video.style.background = '#000';

                                                            const closeBtn = $('<button>', {
                                                                    text: 'X',
                                                                    style: 'position: absolute; top: 2px; right: 2px; background: rgba(255,0,0,0.7); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer;'
                                                                }).on('click', () => {
                                                                    wrapper.remove();
                                                            ids.splice(ids.indexOf(attachment.id), 1);
                                                            hiddenInput.val(ids.join(','));
                                                        });

                                                            wrapper.append(video, closeBtn);
                                                            previewContainer.append(wrapper);
                                                            ids.push(attachment.id);
                                                        } else {
                                                            alert(`Không hỗ trợ định dạng: ${ext}`);
                                                        }
                                                    } catch (err) {
                                                        console.error('Lỗi xử lý file:', err);
                                                    }
                                                }

                                                // Gán danh sách ID
                                                hiddenInput.val(ids.join(','));
                                            })();
                                            });

                                            frame.open();
                                        });
                                    }

// Gọi
                                    handleUploadMulti('#upload_sohong_multi_btn', '#sohong_image_multi', '#sohong_multi_preview');




                                    // Hàm xử lý upload ảnh đơn với watermark
                                    function handleUpload(buttonId, imageId, previewId) {
                                        var frame;
                                        $(buttonId).on('click', function (e) {
                                            e.preventDefault();
                                            if (frame) return frame.open();

                                            frame = wp.media({
                                                title: 'Chọn ảnh',
                                                button: { text: 'Chọn ảnh' },
                                                multiple: false
                                            });

                                            frame.on('select', function () {
                                                var attachment = frame.state().get('selection').first().toJSON();

                                                // Hiển thị ảnh tạm thời
                                                $(previewId).attr('src', attachment.url).show();

                                                // Tạo watermark rồi upload ảnh đã watermark
                                                var img = new Image();
                                                img.crossOrigin = 'Anonymous';
                                                img.src = attachment.url;
                                                img.onload = function () {
                                                    var canvas = document.createElement('canvas');
                                                    canvas.width = img.width;
                                                    canvas.height = img.height;
                                                    var ctx = canvas.getContext('2d');
                                                    ctx.drawImage(img, 0, 0);

                                                    ctx.font = "24px Arial";
                                                    ctx.fillStyle = "rgba(255,0,0,0.6)";
                                                    ctx.textAlign = "right";
                                                    ctx.fillText("Urbanhome.vn", canvas.width - 20, canvas.height - 20);

                                                    canvas.toBlob(function (blob) {
                                                        var formData = new FormData();
                                                        formData.append('action', 'upload_watermarked_image');
                                                        formData.append('file', blob, 'watermarked.jpg');

                                                        fetch(ajaxurl, {
                                                            method: 'POST',
                                                            body: formData
                                                        })
                                                            .then(response => response.json())
                                                        .then(res => {
                                                            if (res.success) {
                                                            // Gán URL ảnh đã upload thành công
                                                            $(previewId).attr('src', res.data.url).show();
                                                            // Gán ID ảnh (giả sử backend trả về ID trong res.data.id)
                                                            $(imageId).val(res.data.id);
                                                        } else {
                                                            alert('Lỗi upload watermark');
                                                        }
                                                    })
                                                        .catch(err => alert('Lỗi mạng: ' + err.message));
                                                    }, 'image/jpeg', 0.92);
                                                };
                                            });

                                            frame.open();
                                        });
                                    }

                                    handleUpload('#upload_sohong_btn', '#sohong_image_id', '#sohong_image_preview');
                                    handleUpload('#upload_bosung_btn', '#bosung_image_id', '#bosung_image_preview');





                                }

                                // Hàm show ảnh từ ID (đơn)
                                function showImageFromId(imageId, $previewEl) {
                                    if (!imageId) {
                                        $previewEl.hide();
                                        return;
                                    }
                                    wp.media.attachment(imageId).fetch().then(function (attachment) {
                                        if (attachment && attachment.url) {
                                            $previewEl.attr('src', attachment.url).show();
                                        } else {
                                            $previewEl.hide();
                                        }
                                    }).catch(() => {
                                        $previewEl.hide();
                                });
                                }

                                // Thêm nút xóa cho ảnh Sổ Hồng (1 ảnh)
                                $('#sohong_image_preview').after('<button type="button" id="delete_sohong_btn" style="margin-top:6px; display:none;">Xóa ảnh</button>');
                                // Thêm nút xóa cho ảnh bổ sung (1 ảnh)
                                $('#bosung_image_preview').after('<button type="button" id="delete_bosung_btn" style="margin-top:6px; display:none;">Xóa ảnh</button>');

                                // Hiển thị nút xóa nếu có ảnh
                                function toggleDeleteButtons() {
                                    if ($('#sohong_image_preview').attr('src')) {
                                        $('#delete_sohong_btn').show();
                                    } else {
                                        $('#delete_sohong_btn').hide();
                                    }
                                    if ($('#bosung_image_preview').attr('src')) {
                                        $('#delete_bosung_btn').show();
                                    } else {
                                        $('#delete_bosung_btn').hide();
                                    }
                                }

                                toggleDeleteButtons();

                                // Xử lý xóa ảnh đơn
                                $('#delete_sohong_btn').on('click', function () {
                                    $('#sohong_image_preview').attr('src', '').hide();
                                    $('#sohong_image_id').val('');
                                    $(this).hide();
                                });

                                $('#delete_bosung_btn').on('click', function () {
                                    $('#bosung_image_preview').attr('src', '').hide();
                                    $('#bosung_image_id').val('');
                                    $(this).hide();
                                });

                                // Xóa ảnh nhiều: thêm nút xóa cho từng ảnh
                                // Cập nhật lại hàm showImagesFromIds để có nút xóa cho từng ảnh multi
                                function showImagesFromIds(idsString, $previewContainer) {
                                    if (!idsString) {
                                        $previewContainer.empty();
                                        return;
                                    }

                                    let ids = idsString.split(',');
                                    $previewContainer.empty();

                                    ids.forEach(function (id, index) {
                                        wp.media.attachment(id).fetch().then(function (attachment) {
                                            if (attachment && attachment.url) {
                                                const url = attachment.url;
                                                const ext = url.split('.').pop().toLowerCase();
                                                const isVideo = ['mp4', 'webm', 'ogg'].includes(ext);

                                                const wrapper = $('<div>', {
                                                    style: `
                        position: relative;
                        display: inline-block;
                        width: 120px;
                        height: 90px;
                        margin-right: 6px;
                        margin-bottom: 6px;
                        border-radius: 5px;
                        overflow: hidden;
                        box-shadow: 0 0 8px rgba(0,0,0,0.15);
                        background: #000;
                    `
                                                });

                                                let mediaEl;
                                                if (isVideo) {
                                                    mediaEl = $('<video>', {
                                                        src: url,
                                                        controls: true,
                                                        preload: 'metadata',
                                                        playsinline: true,
                                                        style: `
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            display: block;
                            background: #000;
                        `
                                                    });
                                                } else {
                                                    mediaEl = $('<img>', {
                                                        src: url,
                                                        alt: 'Media',
                                                        style: `
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            display: block;
                        `
                                                    });
                                                }

                                                const delBtn = $('<button>', {
                                                    text: 'X',
                                                    style: `
                        position: absolute;
                        top: 2px;
                        right: 2px;
                        background: rgba(255,0,0,0.7);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 24px;
                        height: 24px;
                        font-size: 14px;
                        cursor: pointer;
                    `
                                                }).on('click', function () {
                                                    wrapper.remove();
                                                    ids = ids.filter(i => i !== id);
                                                    $('#sohong_image_multi').val(ids.join(','));
                                                });

                                                wrapper.append(mediaEl).append(delBtn);
                                                $previewContainer.append(wrapper);
                                            }
                                        }).catch(() => {
                                            // Bỏ qua file lỗi
                                        });
                                    });
                                }



                                // Giữ nguyên phần setTimeout gọi showImagesFromIds để hiển thị ảnh và nút xóa
                                setTimeout(() => {
                                    console.log('IDs ảnh:', rowData.sohong_image_id, rowData.bosung_image_id, rowData.sohong_image_multi);

                                $('#sohong_image_id').val(rowData.sohong_image_id || '');
                                $('#bosung_image_id').val(rowData.bosung_image_id || '');
                                $('#sohong_image_multi').val(rowData.sohong_image_multi || '');

                                showImageFromId(rowData.sohong_image_id, $('#sohong_image_preview'));
                                showImageFromId(rowData.bosung_image_id, $('#bosung_image_preview'));

                                toggleDeleteButtons();

                                if (rowData.sohong_image_multi) {
                                    showImagesFromIds(rowData.sohong_image_multi, $('#sohong_multi_preview'));
                                } else {
                                    $('#sohong_multi_preview').empty();
                                }
                            }, 700);

                            }


                            $('#id_g').closest('tr').hide();


                            // phanfixformedit
                            const styleId = "jqgrid-mobile-style";
                            if (!document.getElementById(styleId)) {
                                const css = `
    @media (max-width: 768px) {
      #viewmodjqGrid,
      #editmodjqGrid {
        width: 98vw !important;
        left: 1vw !important;
        top: 10vh !important;
      }

      #viewmodjqGrid .FormGrid,
      #editmodjqGrid .FormGrid {
        display: block !important;
      }

      #viewmodjqGrid .FormGrid > div[style*="display:flex"],
      #editmodjqGrid .FormGrid > div[style*="display:flex"] {
        flex-direction: column !important;
      }

      #viewmodjqGrid .FormGrid > div[style*="display:flex"] > div,
      #editmodjqGrid .FormGrid > div[style*="display:flex"] > div {
        width: 100% !important;
      }
    }
  `;
                                const style = document.createElement("style");
                                style.id = styleId;
                                style.textContent = css;
                                document.head.appendChild(style);
                            }

                            //





                        },
                        serializeEditData: function(postData) {
                            var rowId = $("#jqGrid").jqGrid('getGridParam', 'selrow');
                            var oldData = $("#jqGrid").jqGrid('getRowData', rowId);

                            var changedFields = [];

                            for (var key in postData) {
                                if (!postData.hasOwnProperty(key)) continue;

                                if (['action', 'id', 'oper', 'table'].includes(key)) continue;

                                var oldVal = oldData[key] !== undefined ? String(oldData[key]) : '';
                                var newVal = postData[key] !== undefined ? String(postData[key]) : '';

                                if (newVal !== oldVal) {
                                    changedFields.push(key);
                                }
                            }

                            // Gán các trường ảnh từ input ẩn (luôn lấy giá trị mới nhất)
                            postData.sohong_image_id = $('#sohong_image_id').val() || '';
                            postData.bosung_image_id = $('#bosung_image_id').val() || '';
                            postData.sohong_image_multi = $('#sohong_image_multi').val() || '';

                            // Nếu các trường ảnh không có trong changedFields thì thêm vào để backend nhận biết thay đổi
                            ['sohong_image_id', 'bosung_image_id', 'sohong_image_multi'].forEach(function(field) {
                                if (!changedFields.includes(field)) {
                                    changedFields.push(field);
                                }
                            });

                            postData.changed_fields = changedFields.join(',');

                            return postData;
                        },
                        onclickSubmit: function (params, postdata) {
                            const vaitroArr = [];
                            const nameArr = [];
                            const dienthoaiArr = [];
                            const gioitinhArr = [];

                            // Quét qua tất cả các dòng liên hệ (kể cả dòng tạo động)
                            document.querySelectorAll("select[name='vaitro_more[]']").forEach(el => vaitroArr.push(el.value));
                            document.querySelectorAll("input[name='name_more[]']").forEach(el => nameArr.push(el.value));
                            document.querySelectorAll("input[name='dienthoaididong_more[]']").forEach(el => dienthoaiArr.push(el.value));
                            document.querySelectorAll("select[name='gioitinh_more[]']").forEach(el => gioitinhArr.push(el.value));

                            // Gán vào postdata
                            postdata.vaitro_more = vaitroArr;
                            postdata.name_more = nameArr;
                            postdata.dienthoaididong_more = dienthoaiArr;
                            postdata.gioitinh_more = gioitinhArr;

                            return postdata;
                        },
                        afterSubmit: function (response, postdata) {
                            try {
                                var res = JSON.parse(response.responseText);

                                if (res.success) {
                                    alert("✅ Cập nhật thành công!");
                                    return [true, "", res.id]; // success
                                } else {
                                    alert("❌ Lỗi: " + (res.data || "Không rõ lỗi"));
                                    return [false, res.data || "Lỗi không xác định"];
                                }
                            } catch (e) {
                                alert("❌ Không thể phân tích phản hồi từ server.");
                                return [false, "Lỗi phản hồi từ server"];
                            }
                        },

                        onInitializeForm: function ($form) {
                            $form.css({height: "auto"});
                            $form.closest(".ui-jqdialog").css({height: "auto"});
                        },
                        viewPagerButtons:false, //hide next and pre in form
                        drag:true
                    });
                });




                // tao thanh cuon ngang khi cot qua dai
                // Resize grid when window resizes
                /* $(window).on("resize", function () {
                 $("#jqGrid").setGridWidth($(window).width() - 30);
                 }).trigger("resize");*/
//createNewBtn
                $("#createNewBtn").click(function () {
                    $("#jqGrid").jqGrid("editGridRow", "new", {
                        // Add options
                        url: ajaxurl,
                        mtype: "POST",
                        editData: { action: "add_jqgrid_data", table: selectedTable},
                        closeAfterAdd: true,
                        closeOnEscape: true,
                        reloadAfterSubmit: true,
                        onClose: function () {
                            $(".select2-dropdown").hide();
                            $('.btn-check-duplicate-floating').remove();
                            return true;
                        },
                        width:1000,
                        beforeInitData: function () {
                            selRowId = $grid.jqGrid('getGridParam', 'selrow');
                        },
                        recreateForm: true,
                        beforeSubmit: function (postdata, formid) {
                            if (selectedTable === 'wp_dulieunhadat') {

                                postdata.giaban = postdata.giaban.replace(/,/g, '');
                                postdata.giathue = postdata.giathue.replace(/,/g, '');
                                postdata.giachot = postdata.giachot.replace(/,/g, '');

                                const product_type = $("#product_type").val();
                                const transaction_type = $("#transaction_type").val();

                                if (product_type === "517") {
                                    if (!postdata.code_product || postdata.code_product.trim() === "") {
                                        return [false, "Bạn phải nhập Mã căn khi chọn loại Dự án!"];
                                    }
                                    if (!postdata.name_area || postdata.name_area.trim() === "") {
                                        return [false, "Bạn phải nhập Tên Dự Án khi chọn loại Dự án!"];
                                    }
                                }
                                if (product_type === "515") {
                                    if (!postdata.sonha || postdata.sonha.trim() === "") {
                                        return [false, "Bạn phải nhập Số Nhà khi chọn loại Nhà ở riêng lẻ!"];
                                    }
                                }

                                if (transaction_type === "509") {
                                    if (!postdata.giaban || postdata.giaban.trim() === "") {
                                        return [false, "Bạn phải nhập Giá bán khi chọn loại Bán!"];
                                    }
                                }
                                if (transaction_type === "511") {
                                    if (!postdata.giathue || postdata.giathue.trim() === "") {
                                        return [false, "Bạn phải nhập Giá thuê khi chọn loại Thuê!"];
                                    }
                                }

                                // --- Gửi AJAX kiểm tra trùng ---
                                let isDuplicate = false;
                                let message = "";
                                $.ajax({
                                    url: ajaxurl, // hoặc API riêng của bạn
                                    type: "POST",
                                    cache: false, // JQuery tự thêm tham số _=timestamp
                                    data: {
                                        action: "check_duplicate_property", // hook trong WordPress
                                        data: postdata
                                    },
                                    async: false, // BẮT BUỘC phải đồng bộ nếu muốn ngăn submit
                                    success: function (res) {
                                        if (res.duplicate) {
                                            isDuplicate = true;
                                            message = res.message || "Dữ liệu bị trùng!";
                                        }
                                    },
                                    error: function () {
                                        isDuplicate = true;
                                        message = "Lỗi khi kiểm tra trùng. Vui lòng thử lại.";
                                    }
                                });

                                if (isDuplicate) {
                                    return [false, message];
                                }

                                return [true, '', postdata];
                            }
                        },
                        afterShowForm: function ($form) {
                            setupAutoCalc();
                            setTimeout(function () {
                                // Tìm <tr> chứa ô input name="contact_info"
                                const contactInputRow = $form.find('tr').has('input[name="contact_info"]');
                                contactInputRow.hide();
                                // Tìm <tr> chứa ô input name="contact_all"
                                const contactAllInputRow = $form.find('tr').has('input[name="contact_all"]');
                                contactAllInputRow.hide();
                            }, 1000);

                            var $ngang = $("#dtd_ngang", $form);
                            var $dai = $("#dtd_dai", $form);
                            var $congnhan = $("#dtd_congnhan", $form);
                            console.log('$ngang:', $ngang.length, '$dai:', $dai.length, '$congnhan:', $congnhan.length);


                            function tinhCongNhan() {
                                var ngang = parseFloat($ngang.val()) || 0;
                                var dai = parseFloat($dai.val()) || 0;
                                var result = ngang * dai;
                                $congnhan.val(result ? result.toFixed(2) : "");
                            }

                            $ngang.on('input', function() {
                                console.log('input ngang', $ngang.val());
                                tinhCongNhan();
                            });
                            $dai.on('input', function() {
                                console.log('input dai', $dai.val());
                                tinhCongNhan();
                            });




                            // Chỉ gắn dấu * cho #tr_code_product và #tr_giaban thôi, bỏ #tr_name_area
                            const requiredRows = ["#tr_sonha","#tr_code_product", "#tr_giaban","#tr_name_area"];

                            requiredRows.forEach(function(rowId) {
                                const labelTd = $form.find(rowId + " .CaptionTD");
                                const currentText = labelTd.text().trim();

                                if (!currentText.includes("*") && labelTd.find("i.fa-asterisk").length === 0) {
                                    labelTd.html('<i class="fa fa-asterisk" style="color:red; margin-right:5px;"></i>' + currentText);
                                }
                            });



                        },
                        beforeShowForm: function ($form) {

                            // gan button check trung
                            if (selectedTable === 'wp_dulieunhadat') {
                                $form = $($form); // đảm bảo là jQuery object

                                // Xóa nút cũ nếu đã tồn tại
                                $('.btn-check-duplicate-floating').remove();

                                // Tạo nút lơ lửng giữa màn hình
                                const checkButton = $('<button type="button">Kiểm tra trùng sản phẩm</button>')
                                    .addClass('btn-check-duplicate-floating')
                                    .css({
                                        position: 'fixed',
                                        top: '40%',                // vị trí dọc (có thể điều chỉnh)
                                        left: '98%',               // giữa ngang
                                        transform: 'translateX(-50%)',
                                        zIndex: 9999,
                                        padding: '12px 20px',
                                        backgroundColor: '#ff0000',
                                        color: '#fff',
                                        border: 'none',
                                        borderRadius: '25px',
                                        fontSize: '14px',
                                        fontWeight: 'bold',
                                        boxShadow: '0 4px 12px rgba(0,0,0,0.2)',
                                        cursor: 'pointer',
                                        transition: 'background-color 0.3s ease'
                                    })
                                    .hover(
                                        function () { $(this).css('backgroundColor', '#ff0000'); },
                                        function () { $(this).css('backgroundColor', '#ff0000'); }
                                    )
                                    .click(function () {
                                        const postdata = {};
                                        $form.find(':input[name]').each(function () {
                                            postdata[this.name] = $(this).val();
                                        });

                                        $.ajax({
                                            url: ajaxurl,
                                            type: "POST",
                                            cache: false, // JQuery tự thêm tham số _=timestamp
                                            data: {
                                                action: "check_duplicate_property",
                                                data: postdata
                                            },
                                            success: function (res) {
                                                if (res.duplicate) {
                                                    alert("⚠️ " + (res.message || "Dữ liệu bị trùng!"));
                                                } else {
                                                    alert("✅ Không bị trùng!");
                                                }
                                            },
                                            error: function () {
                                                alert("❌ Lỗi khi kiểm tra trùng.");
                                            }
                                        });
                                    });

                                // Thêm nút vào body (nằm giữa màn hình)
                                $('body').append(checkButton);


                                // phanfixformadd
                                const styleId = "jqgrid-mobile-style";
                                if (!document.getElementById(styleId)) {
                                    const css = `
    @media (max-width: 768px) {
      #viewmodjqGrid,
      #editmodjqGrid {
        width: 98vw !important;
        left: 1vw !important;
        top: 10vh !important;
      }

      #viewmodjqGrid .FormGrid,
      #editmodjqGrid .FormGrid {
        display: block !important;
      }

      #viewmodjqGrid .FormGrid > div[style*="display:flex"],
      #editmodjqGrid .FormGrid > div[style*="display:flex"] {
        flex-direction: column !important;
      }

      #viewmodjqGrid .FormGrid > div[style*="display:flex"] > div,
      #editmodjqGrid .FormGrid > div[style*="display:flex"] > div {
        width: 100% !important;
      }
    }
  `;
                                    const style = document.createElement("style");
                                    style.id = styleId;
                                    style.textContent = css;
                                    document.head.appendChild(style);
                                }

                                //






                            }




//form add
                            setTimeout(function () {
                                // Thêm CSS riêng cho 4 field động và 2 nút + -
                                const style = document.createElement("style");
                                style.innerHTML = `
        /* 4 field input/select trong dòng động */
        .dynamic-contact-row td.DataTD {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .dynamic-contact-row td.DataTD select,
        .dynamic-contact-row td.DataTD input {
            flex: 1 1 130px;  /* chiều rộng tối thiểu */
            min-width: 130px;
            padding: 6px 8px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        .dynamic-contact-row td.DataTD select:focus,
        .dynamic-contact-row td.DataTD input:focus {
            border-color: #4caf50;
            outline: none;
            box-shadow: 0 0 6px rgba(76, 175, 80, 0.5);
        }

        /* Nút thêm bớt + - */
        #btn-add-fields, #btn-remove-fields {
            background-color: #4caf50;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            margin-left: 8px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            user-select: none;
        }
        #btn-remove-fields {
            background-color: #e53935;
            margin-left: 12px;
        }
        #btn-add-fields:hover {
            background-color: #45a049;
            box-shadow: 0 3px 8px rgba(69,160,73,0.6);
        }
        #btn-remove-fields:hover {
            background-color: #d32f2f;
            box-shadow: 0 3px 8px rgba(211,47,47,0.6);
        }
    `;
                                document.head.appendChild(style);

                                // ... phần còn lại giữ nguyên, không thay đổi ...
                            }, 300);


                            setTimeout(function () {
                                // Thêm CSS margin-top cho .group-title-row
                                const style = document.createElement("style");
                                style.innerHTML = `
        .group-title-row {
            margin-top: 16px !important;
        }
        /* Style cho các input động trong nhóm liên hệ */
        .dynamic-contact-row {
            background-color: rgb(249, 250, 251);
            box-shadow: none !important;
        }
        .dynamic-contact-row td {
            padding: 6px 14px !important;
        }
        .dynamic-contact-row input, .dynamic-contact-row select {
            width: 90%;
            padding: 4px 6px;
            font-size: 13px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        #btn-add-fields, #btn-remove-fields {
            background-color: #4caf50;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            margin-right: 8px;
        }
        #btn-remove-fields {
            background-color: #e53935;
        }
    `;
                                document.head.appendChild(style);

                                const tbodies = Array.from(document.querySelectorAll("#TblGrid_jqGrid tbody"));

                                const groupConfigs = [
                                    { from: 1, to: 27, title: "🔶 Thông tin chính", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 28, to: 34, title: "🔷 Thông tin liên hệ", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 35, to: 38, title: "🟢 Diện tích", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 38, to: 60, title: "🔸 Thông tin khác", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" }
                                ];

                                // Duyệt từng nhóm như cũ
                                groupConfigs.forEach((config, groupIndex) => {
                                    let group = tbodies
                                            .map(tbody => {
                                            const tr = tbody.querySelector("tr");
                                const rowpos = parseInt(tr?.getAttribute("data-rowpos"), 10);
                                return { tbody, rowpos };
                            })
                                .filter(item => !isNaN(item.rowpos) && item.rowpos >= config.from && item.rowpos <= config.to)
                                .sort((a, b) => a.rowpos - b.rowpos)
                                .map(item => item.tbody);

                                if (group.length === 0) return;

                                const tableBody = group[0].parentElement;

                                if (groupIndex !== 0) {
                                    const spacerBody = document.createElement("tbody");
                                    spacerBody.classList.add("group-spacer-body");
                                    spacerBody.innerHTML = `<tr><td colspan="2" style="height: 16px; padding:0; border: none;"></td></tr>`;
                                    tableBody.insertBefore(spacerBody, group[0]);
                                }

                                const titleRow = document.createElement("tr");
                                titleRow.classList.add("group-title-row");
                                titleRow.innerHTML = `
            <td colspan="2" style="
                font-weight:600;
                padding:8px 14px;
                background-color:${config.color};
                border:1px solid ${config.border};
                border-bottom:none;
                border-radius:8px 8px 0 0;
                color:${config.text};
                font-size:15px;
                letter-spacing:0.3px;
            ">${config.title}</td>`;
                                tableBody.insertBefore(titleRow, group[0]);

                                group.forEach((tbody, index) => {
                                    tbody.classList.add("group-middle");
                                tbody.style.backgroundColor = config.color;
                                tbody.style.border = "none";
                                tbody.style.boxShadow = "0 1px 4px rgba(0, 0, 0, 0.06)";

                                if (index === group.length - 1) {
                                    tbody.style.border = `1px solid ${config.border}`;
                                    tbody.style.borderTop = "none";
                                    tbody.style.borderRadius = "0 0 8px 8px";
                                } else {
                                    tbody.style.borderLeft = `1px solid ${config.border}`;
                                    tbody.style.borderRight = `1px solid ${config.border}`;
                                }
                            });
                            });

                                // --- Thêm input động cho nhóm "Thông tin liên hệ" ngay trong nhóm này ---
                                const lienHeGroup = groupConfigs.find(g => g.title.includes("liên hệ"));
                                if (lienHeGroup) {
                                    const lienHeTbodies = tbodies
                                            .map(tbody => {
                                            const tr = tbody.querySelector("tr");
                                    const rowpos = parseInt(tr?.getAttribute("data-rowpos"), 10);
                                    return { tbody, rowpos };
                                })
                                .filter(item => !isNaN(item.rowpos) && item.rowpos >= lienHeGroup.from && item.rowpos <= lienHeGroup.to)
                                .sort((a, b) => a.rowpos - b.rowpos)
                                .map(item => item.tbody);

                                    // Lấy tbody cuối cùng trong nhóm liên hệ để chèn thêm
                                    const lastTbody = lienHeTbodies[lienHeTbodies.length - 1];
                                    if (!lastTbody) return;

                                    const tableBody = lastTbody.parentElement;

                                    // Nút điều khiển thêm bớt (chèn dưới cùng của tbody cuối)
                                    const controlRow = document.createElement("tr");
                                    controlRow.innerHTML = `
            <td colspan="2" style="padding: 10px 14px; text-align: right;">
                <button type="button" id="btn-add-fields">+</button>
                <button type="button" id="btn-remove-fields">–</button>
            </td>`;
                                    lastTbody.appendChild(controlRow);

                                    // Mảng lưu các row động đã tạo
                                    const addedRows = [];

                                    // Dữ liệu mẫu cho select
                                    const vaitroOptions = {
                                        627: "Chủ Nhà",
                                        639: "Đại Diện Chủ Nhà",
                                        637: "Đại Diện Công Ty",
                                        629: "Độc Quyền",
                                        631: "Môi Giới Hợp Tác",
                                        633: "Người Thân Chủ Nhà",
                                        635: "Trợ Lý Chủ Nhà",
                                    };
                                    const gioitinhOptions = {
                                        643: "Nam",
                                        645: "Nữ"
                                    };

                                    function createDynamicRow() {
                                        const tr = document.createElement("tr");
                                        tr.classList.add("dynamic-contact-row");
                                        tr.innerHTML = `
                <td class="DataTD" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <select name="vaitro_more[]">
                        ${Object.entries(vaitroOptions).map(([val, txt]) =>
                                        `<option value="${val}">${txt}</option>`).join('')}
                    </select>
                    <input type="text" name="name_more[]" placeholder="Tên" />
                    <input type="text" name="dienthoaididong_more[]" placeholder="Điện thoại" />
                    <select name="gioitinh_more[]">
                        ${Object.entries(gioitinhOptions).map(([val, txt]) =>
                                        `<option value="${val}">${txt}</option>`).join('')}
                    </select>
                </td>
            `;
                                        return tr;
                                    }

                                    document.getElementById("btn-add-fields").addEventListener("click", () => {
                                        const newRow = createDynamicRow();
                                    lastTbody.appendChild(newRow);
                                    addedRows.push(newRow);
                                });

                                    document.getElementById("btn-remove-fields").addEventListener("click", () => {
                                        if (addedRows.length > 0) {
                                        const lastRow = addedRows.pop();
                                        lastRow.remove();
                                    }
                                });
                                }
                            }, 300);



                            setTimeout(() => {
                                $form.find("tbody").each(function () {
                                const currentStyle = this.getAttribute("style")?.replace(/\s+/g, '').toLowerCase();
                                if (currentStyle === "width:100%;display:block;") {
                                    this.style.setProperty("height", "20px", "important");
                                }
                            });
                        }, 50);





                            $("#sData").click(function() {
                                var formValid = true;  // Flag to check if form is valid
                                var firstInvalidField = null;  // To store the first invalid field

                                // Loop through all input fields and select2 fields to check for emptiness or invalidity
                                $("form input, form select").each(function() {
                                    // Check if the field is empty
                                    if ($(this).val() === "" || $(this).hasClass('invalid')) {
                                        if (!firstInvalidField) {
                                            firstInvalidField = $(this);  // Store the first invalid field
                                        }
                                        formValid = false;  // Set the form as invalid
                                    }

                                    // For select2 fields, check if the select2 dropdown has an empty value
                                    if ($(this).is("select") && $(this).hasClass("select2-hidden-accessible") && $(this).val() === null) {
                                        if (!firstInvalidField) {
                                            firstInvalidField = $(this);  // Store the first invalid select2 field
                                        }
                                        formValid = false;  // Set the form as invalid
                                    }
                                });

                                // If form is invalid, scroll to the first invalid field
                                if (!formValid && firstInvalidField) {
                                    // Check if the invalid field is a select2 field and scroll to its container
                                    if (firstInvalidField.hasClass("select2-hidden-accessible")) {
                                        firstInvalidField = firstInvalidField.siblings(".select2-container");  // Get the select2 container
                                    }

                                    // Scroll to the field
                                    $('html, body').animate({
                                        scrollTop: firstInvalidField.offset().top - 500  // Scroll to the field
                                    }, 500); // 500 ms duration for scrolling
                                }

                                return formValid;  // Return the validation result (true if valid, false otherwise)
                            });

                            // Select the input field for the specific column (replace 'column_name' with your column identifier)




                            // fix template table
                            //=========================
                            // --- jQuery chỉnh style bảng ---
                            var $table = $form.find("table");

// Style tổng thể cho table
                            $table.css({
                                "width": "100%",
                                "border-collapse": "collapse",
                                "display": "flex",
                                "flex-wrap": "wrap",
                                "gap": "0px",
                                "overflow": "hidden",
                                "padding": "16px",
                                "background-color": "#f9fafb",   // nền nhẹ nhàng
                                "border-radius": "8px",
                                "box-shadow": "0 4px 8px rgba(0,0,0,0.05)"
                            });

// Lấy toàn bộ tr để xử lý nhóm lại
                            var $rows = $table.find("tr");

// Xóa các tbody cũ
                            $table.find("tbody").remove();

// Tạo lại tbody đầu (2 hàng đầu giữ nguyên)
                            var $firstTbody = $("<tbody></tbody>").css({
                                "width": "100%",
                                "display": "block"
                            });
                            $firstTbody.append($rows.slice(0, 2));
                            $table.append($firstTbody);

// Nhóm các row còn lại theo từng 2 row
                            var groupSize = 2;
                            var restRows = $rows.slice(2);

                            for (var i = 0; i < restRows.length; i += groupSize) {
                                var $groupTbody = $("<tbody></tbody>").css({
                                    "display": "flex",
                                    "flex-wrap": "wrap",
                                    "gap": "8px",
                                    "width": "100%"
                                });

                                var group = restRows.slice(i, i + groupSize);
                                $groupTbody.append(group);
                                $table.append($groupTbody);
                            }

// Style từng dòng tr
                            $table.find("tr").css({
                                "display": "flex",
                                "flex-direction": "column",
                                "flex": "1 1 calc(50% - 12px)",  // 2 cột
                                "margin": "10px 0",
                                "max-width": "100%",
                                "box-sizing": "border-box",
                                "position": "relative"
                            });

// Style td và th
                            $table.find("td, th").css({
                                "padding": "6px 10px"
                            });

// Thêm icon Dashicons trước label
                            $table.find("label").each(function() {
                                var $label = $(this);
                                // Nếu chưa có icon thì thêm
                                if (!$label.find("span.dashicons").length) {
                                    $label.prepend('<span class="dashicons dashicons-admin-home" style="color:#3b82f6; margin-right:6px;"></span>');
                                }
                            });

// Style label: cho icon + text nằm ngang, căn giữa, khoảng cách đẹp
                            $table.find("label").css({
                                "display": "flex",
                                "align-items": "center",
                                "gap": "6px",
                                "font-weight": "600",
                                "color": "#333",
                                "margin-bottom": "4px",
                                "font-size": "14px"
                            });

// Style input, select, textarea cho gọn, đẹp + hiệu ứng bóng, hover, focus
                            $table.find("input, select, textarea").css({
                                "padding": "8px 12px",
                                "font-size": "15px",
                                "border": "1.5px solid #ccc",
                                "border-radius": "6px",
                                "width": "100%",
                                "box-sizing": "border-box",
                                "outline": "none",
                                "background-color": "#fafafa",
                                "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)",
                                "transition": "border-color 0.3s, box-shadow 0.3s, background-color 0.3s"
                            }).on("focus", function () {
                                $(this).css({
                                    "border-color": "#2563eb",
                                    "box-shadow": "0 0 12px rgba(37,99,235,0.6)",
                                    "background-color": "#fff"
                                });
                            }).on("blur", function () {
                                $(this).css({
                                    "border-color": "#ccc",
                                    "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)",
                                    "background-color": "#fafafa"
                                });
                            }).on("mouseenter", function () {
                                $(this).css({
                                    "border-color": "#60a5fa",
                                    "box-shadow": "0 0 8px rgba(59,130,246,0.3)"
                                });
                            }).on("mouseleave", function () {
                                if (!$(this).is(":focus")) {
                                    $(this).css({
                                        "border-color": "#ccc",
                                        "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)"
                                    });
                                }
                            });

// Style cho .form-row trong table để các input nằm ngang, đẹp hơn
                            $table.find(".form-row").css({
                                "display": "flex",
                                "align-items": "center",
                                "justify-content": "space-between",
                                "gap": "8px",
                                "width": "100%",
                                "overflow": "hidden",
                                "padding": "8px 0",
                                "box-sizing": "border-box",
                                "flex-wrap": "wrap"
                            });

// Bố cục label + input trong .form-row để label cố định width, input tự động giãn
                            $table.find(".form-row label").css({
                                "flex": "0 0 140px",
                                "white-space": "nowrap"
                            });

                            $table.find(".form-row input, .form-row select, .form-row textarea").css({
                                "flex": "1 1 auto"
                            });





// Reinitialize Select2 on the select elements after DOM manipulation


//==========================



                            //select2
                            $form.find("select").each(function () {
                                $(this).select2({
                                    width: '100%',
                                    placeholder: 'Chọn thông tin',
                                    allowClear: false,
                                    minimumResultsForSearch: 0 // Enable search functionality
                                })
                            });

                            // Apply Select2 styling
                            applySelect2Styles();


                            /* var $thoigianthue = $('input[name="thoigianthue"]');
                             if ($thoigianthue.length > 0) {
                             $thoigianthue.datepicker({
                             dateFormat: 'yy-mm-dd',
                             changeMonth: true,
                             changeYear: true
                             });
                             };*/
                            var $hethanthue = $('input[name="hethanthue"]');
                            if ($hethanthue.length > 0) {
                                $hethanthue.datepicker({
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: true,
                                    changeYear: true
                                });
                            }




                            $form.addClass("custom-jqgrid-form");
                            // Modify the form header style
                            $form.parent().find('.ui-jqdialog-titlebar').css({
                                'background': '#FFA500', // Orange background
                                'color': 'white',
                                'font-size': '16px',
                                'font-weight': 'bold',
                                'padding': '10px',
                                'border-radius': '8px 8px 0 0',
                                'height':"200px"
                            });

                            // Style input fields
                            $form.find('input, select, textarea').css({
                                'width': '100%',
                                'padding': '8px',
                                'border': '1px solid #ccc',
                                'border-radius': '5px',
                                'font-size': '13px',
                            });

                            // Style the form buttons
                            $form.parent().find('.ui-jqdialog-buttonset button').css({
                                'background': '#4CAF50',
                                'color': 'white',
                                'font-size': '14px',
                                'padding': '8px 12px',
                                'border-radius': '5px',
                                'border': 'none',
                                'cursor': 'pointer',
                                'transition': '0.3s'
                            }).hover(
                                function () {
                                    $(this).css('background', '#388E3C');
                                },
                                function () {
                                    $(this).css('background', '#4CAF50');
                                }
                            );


                            // Change title text
                            if(selectedTable == 'wp_duan') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Dự Án');
                            }else if(selectedTable == 'wp_dulieunhadat') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Sản Phẩm');
                            }else if(selectedTable == 'wp_khachhang') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Khách Hàng');
                            }
                            // Apply modern styling to the header
                            $(".ui-jqdialog-titlebar").css({
                                "color": "#ffa500",          // White text
                                "font-size": "18px",
                                "font-weight": "600",
                                "padding": "15px",
                                "border-radius": "2px 2px 0 0",
                                "border-bottom": "3px solid #4caf50" // Darker green border bottom
                            });

                            // Style the close button for better UX
                            $(".ui-jqdialog-titlebar-close").css({
                                "background": "transparent",
                                "border": "none",
                                "cursor": "pointer",
                                "padding": "4px",
                                "transition": "0.3s"
                            });

                            // Hover effect for close button
                            $(".ui-jqdialog-titlebar-close span").css({
                                "color": "#fff",
                                "font-size": "16px"
                            });

                            $(".ui-jqdialog-titlebar-close").hover(
                                function () { $(this).css("opacity", "0.7"); },
                                function () { $(this).css("opacity", "1"); }
                            );


                            var dlgDiv = $("#editmod" + $grid[0].id);
                            var parentDiv = dlgDiv.parent();
                            var dlgWidth = dlgDiv.width();
                            var parentWidth = parentDiv.width();

                            // Đặt modal lên sát mép trên màn hình
                            if(selectedTable == 'wp_dulieunhadat') {
                                dlgDiv.css({
                                    "top": "-220px",  // Đẩy modal lên sát viền trên màn hình
                                    "left": Math.round((parentWidth - dlgWidth) / 2) + "px",
                                    /* "z-index": "9999", // Luôn nằm trên các phần tử khác
                                     "position": "fixed" // Giữ nguyên vị trí khi cuộn trang*/
                                });
                            }



                            // Fix the main dialog to avoid horizontal scrolling
                            var $dialog = $form.closest(".ui-jqdialog");
                            const winWidth = $(window).width();
                            if (winWidth <= 768) {

                                const dialogWidth = Math.min(winWidth * 0.9, 400); // tối đa 400px hoặc 90% màn hình


                                $dialog.css({
                                    width: dialogWidth + "px",
                                    left: ((winWidth - dialogWidth) / 2) + "px",
                                    top: "60px",
                                    //"z-index": 9999,
                                    "border-radius": "10px",
                                    "background": "#fff",
                                    "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                                    //"max-width": "800px",
                                    "width": "85%",  // Prevents form from being too wide
                                    "overflow": "hidden", // Prevents horizontal scrolling,
                                    "padding": "0px",
                                });
                            }else{
                                $dialog.css({
                                    "border-radius": "10px",
                                    "background": "#fff",
                                    "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                                    //"max-width": "800px",
                                    "overflow": "hidden", // Prevents horizontal scrolling,
                                    "padding": "0px",
                                });
                            }

                            // Style labels
                            $form.find("td.CaptionTD").css({
                                "font-weight": "bold",
                                "color": "#333",
                                //"width": "100%",
                                "margin-bottom": "5px",
                                "text-align": "left",
                                "position":"absolute",
                                "top": "-10px",
                                "z-index": "1",
                                "left": "21px",
                                "height": "30px",
                                "background" : "linear-gradient(to bottom, transparent 50%, rgb(255, 255, 255) 50%, rgb(255, 255, 255) 51%, transparent 65%)",

                            });

                            // Style input fields
                            $form.find("td.DataTD input, td.DataTD select").css({
                                "width": "100%",
                                "padding": "8px",
                                "border": "1px solid #ccc",
                                "border-radius": "5px",
                                "font-size": "14px",
                                "box-sizing": "border-box",
                                "height": "40px",
                                "transition": "all 0.3s ease-in-out" // Hiệu ứng mượt khi thay đổi trạng thái
                            });

                            // Hover effect
                            $form.find("td.DataTD input, td.DataTD select").hover(
                                function () {
                                    $(this).css("border-color", "#007bff"); // Khi hover, viền đổi màu xanh
                                },
                                function () {
                                    $(this).css("border-color", "#ccc"); // Khi không hover, trở lại màu cũ
                                }
                            );

                            // Focus effect
                            $form.find("td.DataTD input, td.DataTD select").on("focus", function () {
                                $(this).css({
                                    "border-color": "#007bff",
                                    "box-shadow": "0 0 5px rgba(0, 123, 255, 0.5)"
                                });
                            }).on("blur", function () {
                                $(this).css({
                                    "border-color": "#ccc",
                                    "box-shadow": "none"
                                });
                            });

                            // Fix button alignment
                            $(".EditButton").css({
                                "display": "flex",
                                "justify-content": "center",
                                "gap": "15px",
                                "margin-top": "20px"
                            });

                            // Style Submit button
                            $("#sData").html('<span style="color: #fff;">✔ Tiếp Tục</span>').css({
                                "background": "#4CAF50",
                                "border-radius": "5px",
                                "padding": "10px 20px",
                                "font-weight": "bold",
                                "box-shadow": "0 2px 4px rgba(0,0,0,0.2)",
                                "border": "none"
                            });

                            // Style Cancel button
                            $("#cData").html('<span style="color: #fff;">✖ Hủy</span>').css({
                                "background": "#d9534f",
                                "border-radius": "5px",
                                "padding": "10px 20px",
                                "font-weight": "bold",
                                "box-shadow": "0 2px 4px rgba(0,0,0,0.2)",
                                "border": "none"
                            });



                            // Hide form error message
                            $("#FormError").hide();
                            $('[value="_empty"]').hide();

                            // handle ajax wp_province, wp_district, wp_wards
                            $('#wp_province').on('change', function() {
                                $('#wp_district').prop('disabled', true);  // Disable the wp_district dropdown
                                $('#wp_district').empty().append('<option value="">Tải thông tin</option>');
                                var wp_province_id = $(this).val(); // Get selected wp_province ID
                                if (wp_province_id) {
                                    // If a wp_province is selected, make an AJAX request
                                    $.ajax({
                                        url: ajaxurl, // WordPress AJAX URL
                                        method: 'POST',
                                        cache: false, // JQuery tự thêm tham số _=timestamp
                                        data: {
                                            action: 'load_wp_districts', // Custom action to load wp_districts
                                            wp_province_id: wp_province_id
                                        },
                                        success: function(response) {
                                            // console.log(response); // Check the response in the browser console to see if it has the data
                                            $('#wp_district').prop('disabled', false);  // Enable the wp_district dropdown
                                            // Empty the wp_district dropdown first
                                            $('#wp_district').empty().append('<option value="">Chọn thông tin</option>');
                                            var wp_districts = response.data.data || []; // Get actual wp_districts array

                                            if (Array.isArray(wp_districts) && wp_districts.length > 0) {
                                                $('#wp_district').empty().append('<option value="">Chọn thông tin</option>');

                                                $.each(wp_districts, function(index, wp_district) {
                                                    $('#wp_district').append('<option value="' + wp_district.id + '">' + wp_district.name + '</option>');
                                                });

                                                $('#wp_district').trigger('change'); // Refresh Select2
                                            } else {
                                                console.warn("No wp_districts found:", response);
                                                $('#wp_district').empty().append('<option value="">No wp_districts available</option>');
                                                $('#wp_district').trigger('change');
                                            }
                                        }
                                    });
                                } else {
                                    // Clear the wp_district select dropdown if no wp_province is selected
                                    $('#wp_district').empty().append('<option value="">Chọn thông tin</option>');
                                }
                            });

                            $('#wp_district').on('change', function() {
                                $('#wp_wards').prop('disabled', true);  // Disable the wp_district dropdown
                                $('#wp_wards').empty().append('<option value="">Tải thông tin</option>');
                                var wp_district_id = $(this).val(); // Get selected wp_district ID
                                if (wp_district_id) {
                                    // If a wp_district is selected, make an AJAX request
                                    $.ajax({
                                        url: ajaxurl, // WordPress AJAX URL
                                        method: 'POST',
                                        cache: false, // JQuery tự thêm tham số _=timestamp
                                        data: {
                                            action: 'load_wp_wards', // Custom action to load wp_districts
                                            wp_district_id: wp_district_id
                                        },
                                        success: function(response) {
                                            // console.log(response); // Check the response in the browser console to see if it has the data
                                            $('#wp_wards').prop('disabled', false);  // Enable the wp_district dropdown
                                            // Empty the wp_district dropdown first
                                            $('#wp_wards').empty().append('<option value="">Chọn thông tin</option>');
                                            var wp_wards = response.data.data || []; // Get actual wp_districts array

                                            if (Array.isArray(wp_wards) && wp_wards.length > 0) {
                                                $('#wp_wards').empty().append('<option value="">Chọn thông tin</option>');

                                                $.each(wp_wards, function(index, ward) {
                                                    $('#wp_wards').append('<option value="' + ward.id + '">' + ward.name + '</option>');
                                                });

                                                $('#wp_wards').trigger('change'); // Refresh Select2
                                            } else {
                                                console.warn("No wp_districts found:", response);
                                                $('#wp_wards').empty().append('<option value="">No wp_wards available</option>');
                                                $('#wp_wards').trigger('change');
                                            }
                                        }
                                    });
                                } else {
                                    // Clear the wp_district select dropdown if no wp_province is selected
                                    $('#ward').empty().append('<option value="">Chọn thông tin</option>');
                                }
                            });



                            // hide show input
                            // Event listener for select dropdown change
                            $("#tr_code_product, #tr_name_area,#tr_sonha, #tr_sothua, #tr_soto").fadeOut();
                            /* $("#product_type").change(function () {
                             var selectedValue = $(this).val(); // Lấy giá trị đã chọn

                             if (selectedValue === "517") {
                             $("#tr_code_product, #tr_name_area").show(); // Hiển thị các input liên quan đến sản phẩm
                             $("#tr_sonha, #tr_sothua, #tr_soto").hide(); // Ẩn các input không liên quan
                             } else if (selectedValue === "515") {
                             $("#tr_code_product, #tr_name_area").hide(); // Ẩn các input sản phẩm
                             $("#tr_sonha, #tr_sothua, #tr_soto").show(); // Hiển thị các input liên quan đến địa chỉ
                             } else {
                             $("#tr_code_product, #tr_name_area, #tr_sonha, #tr_sothua, #tr_soto").hide(); // Ẩn tất cả các input khi không có lựa chọn nào phù hợp
                             }
                             });*/
                            setTimeout(function () {
                                updateFields();

                                // Gắn sự kiện change với namespace để tránh lỗi gắn trùng
                                $("#product_type", $form).off("change.producttype").on("change.producttype", function () {
                                    updateFields();
                                });

                            }, 1000); // delay để chờ DOM trong form jqGrid render xong

                            function updateFields() {
                                var selectedValue = $("#product_type", $form).val();

                                if (selectedValue === "517") {
                                    $("#tr_code_product, #tr_name_area", $form).show();
                                    $("#tr_sonha, #tr_sothua, #tr_soto", $form).hide();
                                } else if (selectedValue === "515") {
                                    $("#tr_code_product, #tr_name_area", $form).hide();
                                    $("#tr_sonha, #tr_sothua, #tr_soto", $form).show();
                                } else {
                                    $("#tr_code_product, #tr_name_area, #tr_sonha, #tr_sothua, #tr_soto", $form).hide();
                                }
                            }


                            // Event listener for select dropdown change
                            $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue, #tr_donvi_giaban ,#tr_giathue,#tr_thoigianthue, #tr_hethanthue, #tr_giaban, #tr_giachot").hide();
                            $form.find("#transaction_type").change(function () {
                                var selectedValue = $(this).val();
                                console.log(selectedValue);
                                if (selectedValue === "509") {
                                    $("#tr_donvi_giaban,#tr_giaban, #tr_giachot").show(); // Show input1 and input2
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_giathue, #tr_thoigianthue, #tr_hethanthue").show(); // Hide input3 and input4
                                    $('#tr_giathue .CaptionTD').text('HĐ thuê');
                                    $('#tr_donvi_giathue .CaptionTD').text('Đơn vị HĐ thuê');
                                    $('#tr_donvi_thoigiangia .CaptionTD').text('HĐ thuê theo');
                                } else if (selectedValue === "511") {
                                    $("#tr_donvi_giaban, #tr_giaban, #tr_giachot").hide();
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue, #tr_giathue, #tr_thoigianthue, #tr_hethanthue").show();
                                    $('#tr_giathue .CaptionTD').html('<i class="fa fa-asterisk" style="color:red; margin-right:5px;"></i> Giá thuê');
                                    $('#tr_donvi_giathue .CaptionTD').text('Đơn vị giá thuê');
                                    $('#tr_donvi_thoigiangia .CaptionTD').text('Giá thuê theo');
                                } else {
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue, #tr_donvi_giaban ,#tr_giathue,#tr_thoigianthue, #tr_hethanthue, #tr_giaban, #tr_giachot").hide();
                                }
                            });

                            // Event listener for select dropdown change
                            $("#tr_hoahong_sotien,#tr_hoahong_thang, #tr_hoahong_tyle").hide();
                            $form.find("#loaihoahong").change(function () {
                                var selectedValue = $(this).val();
                                console.log(selectedValue);
                                if (selectedValue === "651") {
                                    $("#tr_hoahong_tyle").show(); // Show input1 and input2
                                    $("#tr_hoahong_sotien, #tr_hoahong_thang").hide(); // Hide input3 and input4
                                } else if (selectedValue === "653") {
                                    $("#tr_hoahong_thang").show(); // Show input1 and input2
                                    $("#tr_hoahong_sotien, #tr_hoahong_tyle").hide(); // Hide input3 and input4
                                } else if (selectedValue === "649") {
                                    $("#tr_hoahong_sotien").show(); // Show input1 and input2
                                    $("#tr_hoahong_thang, #tr_hoahong_tyle").hide(); // Hide input3 and input4
                                }
                                else {
                                    $("#tr_hoahong_sotien,#tr_hoahong_thang, #tr_hoahong_tyle").hide();
                                }
                            });

//format number
                            $("input[name='thoigianthue'],input[name='dtd_ngang'], input[name='dtd_dai'],input[name='dtd_mathau'], input[name='dtd_congnhan'],input[name='dtqh_ngang'], input[name='dtqh_dai'], input[name='dtqh_mathau'], input[name='dtqh_xaydung'], input[name=''],input[name=''], input[name=''], input[name='ttk_duongrong'], input[name='ttk_dtsd'], input[name='dienthoaididong']").on("input", function(e) {
                                var value = $(this).val();
                                var sanitizedValue = value.replace(/[^0-9.]/g, '');
                                if ((sanitizedValue.match(/\./g) || []).length > 1) {
                                    sanitizedValue = sanitizedValue.replace(/\.+$/, '');
                                }
                                $(this).val(sanitizedValue);
                            });

                            // format a thousand separator
                            $("input[name='giathue'],input[name='giaban'],input[name='giachot']").on("input", function(e) {
                                var value = $(this).val();

                                // Remove any non-numeric characters except for a decimal point
                                var sanitizedValue = value.replace(/[^0-9.]/g, '');

                                // Ensure that only one decimal point is allowed
                                if ((sanitizedValue.match(/\./g) || []).length > 1) {
                                    sanitizedValue = sanitizedValue.replace(/\.+$/, '');  // Remove extra decimal points
                                }

                                // Format the number with comma as thousand separator
                                var parts = sanitizedValue.split('.');
                                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add comma separator every three digits

                                // Combine the integer and decimal parts
                                sanitizedValue = parts.join('.');

                                // Update the input field with the sanitized and formatted value
                                $(this).val(sanitizedValue);
                            });



// addform

                            if (selectedTable == 'wp_dulieunhadat') {
                                setTimeout(function() {
                                    const $container = $('#filter-usernhanvien').next('.select2-container');

                                    $container.find('.select2-selection--single').css({
                                        'height': '38px',
                                        'line-height': '38px',
                                        'padding': '0 16px',
                                        'border-radius': '6px',
                                        'border': 'none',
                                        'box-shadow': '0 4px 8px rgba(44, 123, 229, 0.4)', // shadow xanh dịu
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'box-sizing': 'border-box',
                                        'font-size': '14px',
                                        'font-weight': 'bold',
                                        'background-color': '#FFA500', // nền xanh button
                                        'color': '#fff',
                                        'cursor': 'pointer',
                                        'transition': 'background-color 0.3s ease'
                                    });
                                    $container.find('.select2-selection__placeholder').css({
                                        'color': '#fff',
                                        'opacity': '1'
                                    });


                                    // Khi mở dropdown, đổi nền đậm hơn
                                    $container.find('.select2-selection--single').on('click', function() {
                                        $(this).css('background-color', '#FFA500');
                                    });
                                    // Khi dropdown đóng, trả về nền ban đầu
                                    $container.find('.select2-selection--single').on('blur', function() {
                                        $(this).css('background-color', '#FFA500');
                                    });

                                    $container.find('.select2-selection__rendered').css({
                                        'line-height': '40px',
                                        'padding-left': '0',
                                        'margin': '0',
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'height': '100%',
                                        'color': '#fff'
                                    });

                                    $container.find('.select2-selection__arrow').css({
                                        'height': '40px',
                                        'top': '0',
                                        'right': '8px',
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'color': '#fff'
                                    });
                                }, 10);
                                // Thêm nút upload + input ẩn + ảnh preview vào form nếu chưa có
                                if (!$('#upload_sohong_btn').length && !$('#upload_bosung_btn').length && !$('#upload_sohong_multi_btn').length) {
                                    var html = '' +
                                        '<div style="display: flex; justify-content: center; gap: 40px; margin-bottom: 20px;">' +

                                        // Ảnh Sổ Hồng đơn
                                        /*  '<div style="text-align: center;">' +
                                         '<label><strong>Ảnh Sổ Hồng:</strong></label><br>' +
                                         '<button type="button" id="upload_sohong_btn" style="' +
                                         'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                         'background-color: #c82333; color: white; border: none; margin-top: 6px;">' +
                                         '<i class="fas fa-upload" style="margin-right: 6px;"></i> Chọn Sổ Hồng' +
                                         '</button>' +
                                         '<input type="hidden" id="sohong_image_id" name="sohong_image_id" value="">' +
                                         '<br>' +
                                         '<img id="sohong_image_preview" src="" alt="Ảnh Sổ Hồng" style="' +
                                         'max-width: 150px; border-radius: 5px; box-shadow: 0 0 8px rgba(0,0,0,0.15);' +
                                         'display: none; margin-top: 10px;">' +
                                         '</div>' +

                                         // Ảnh bổ sung đơn
                                         '<div style="text-align: center;">' +
                                         '<label><strong>Hình ảnh bổ sung:</strong></label><br>' +
                                         '<button type="button" id="upload_bosung_btn" style="' +
                                         'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                         'background-color: #007bff; color: white; border: none; margin-top: 6px;">' +
                                         '<i class="fas fa-upload" style="margin-right: 6px;"></i> Chọn ảnh bổ sung' +
                                         '</button>' +
                                         '<input type="hidden" id="bosung_image_id" name="bosung_image_id" value="">' +
                                         '<br>' +
                                         '<img id="bosung_image_preview" src="" alt="Ảnh bổ sung" style="' +
                                         'max-width: 150px; border-radius: 5px; box-shadow: 0 0 8px rgba(0,0,0,0.15);' +
                                         'display: none; margin-top: 10px;">' +
                                         '</div>' +*/

                                        // Ảnh Sổ Hồng nhiều (mới thêm)
                                        '<div style="text-align: center;">' +
                                        '<label><strong>Ảnh Sổ Hồng/Video (nhiều):</strong></label><br>' +
                                        '<button type="button" id="upload_sohong_multi_btn" style="' +
                                        'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                        'background-color: #28a745; color: white; border: none; margin-top: 6px;">' +
                                        '<i class="fas fa-upload" style="margin-right: 6px;"></i> Chọn ảnh/video' +
                                        '</button>' +
                                        '<input type="hidden" id="sohong_image_multi" name="sohong_image_multi" value="">' +
                                        '<br>' +
                                        '<div id="sohong_image_multi_preview" style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;"></div>' +
                                        '</div>' +

                                        '</div>';

                                    $form.append(html);

                                    // Hàm xử lý upload ảnh đơn
                                    function handleUpload(buttonId, imageId, previewId) {
                                        var frame;
                                        $(buttonId).on('click', function (e) {
                                            e.preventDefault();
                                            if (frame) return frame.open();
                                            frame = wp.media({
                                                title: 'Chọn ảnh',
                                                button: { text: 'Chọn ảnh' },
                                                multiple: false
                                            });
                                            frame.on('select', function () {
                                                var attachment = frame.state().get('selection').first().toJSON();

                                                // Hiển thị ảnh tạm trước
                                                $(previewId).attr('src', attachment.url).show();

                                                // Load ảnh gốc rồi vẽ watermark
                                                var img = new Image();
                                                img.crossOrigin = 'Anonymous';
                                                img.src = attachment.url;
                                                img.onload = function () {
                                                    var canvas = document.createElement('canvas');
                                                    canvas.width = img.width;
                                                    canvas.height = img.height;
                                                    var ctx = canvas.getContext('2d');
                                                    ctx.drawImage(img, 0, 0);

                                                    // Thêm watermark
                                                    ctx.font = "24px Arial";
                                                    ctx.fillStyle = "rgba(255,0,0,0.6)";
                                                    ctx.textAlign = "right";
                                                    ctx.fillText("Urbanhome.vn", canvas.width - 20, canvas.height - 20);

                                                    canvas.toBlob(function (blob) {
                                                        var formData = new FormData();
                                                        formData.append('action', 'upload_watermarked_image');
                                                        formData.append('file', blob, 'watermarked.jpg');

                                                        fetch(ajaxurl, {
                                                            method: 'POST',
                                                            body: formData
                                                        })
                                                            .then(response => response.json())
                                                        .then(res => {
                                                            if (res.success) {
                                                            $(previewId).attr('src', res.data.url).show();
                                                            $(imageId).val(res.data.id); // Lưu ID attachment vào input ẩn
                                                        } else {
                                                            alert('Lỗi upload watermark');
                                                        }
                                                    })
                                                        .catch(err => alert('Lỗi mạng: ' + err.message));
                                                    }, 'image/jpeg', 0.92);
                                                };
                                            });
                                            frame.open();
                                        });
                                    }

                                    // Upload ảnh đơn cho 2 phần cũ
                                    handleUpload('#upload_sohong_btn', '#sohong_image_id', '#sohong_image_preview');
                                    handleUpload('#upload_bosung_btn', '#bosung_image_id', '#bosung_image_preview');



                                    $('#upload_sohong_multi_btn').on('click', function (e) {
                                        e.preventDefault();

                                        if (window.frameMulti) {
                                            window.frameMulti.open();
                                            return;
                                        }

                                        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                                            alert('wp.media chưa được nạp. Hãy gọi wp_enqueue_media() trong PHP!');
                                            return;
                                        }

                                        const mediaFrame = wp.media({
                                            title: 'Chọn ảnh hoặc video',
                                            button: { text: 'Chọn file' },
                                            multiple: true
                                        });

                                        window.frameMulti = mediaFrame;

                                        mediaFrame.on('select', function () {
                                            const selection = mediaFrame.state().get('selection').toArray();
                                            const previewContainer = $('#sohong_image_multi_preview');
                                            const hiddenInput = $('#sohong_image_multi');
                                            previewContainer.empty();
                                            const ids = [];

                                            const drawWatermarkGrid = (ctx, canvas, text) => {
                                                ctx.font = "60px Arial";
                                                ctx.fillStyle = "rgba(255, 0, 0, 0.15)";
                                                ctx.textAlign = "left";
                                                ctx.textBaseline = "top";
                                                ctx.shadowColor = "rgba(0,0,0,0.2)";
                                                ctx.shadowOffsetX = 1;
                                                ctx.shadowOffsetY = 1;
                                                ctx.shadowBlur = 1;

                                                const positions = [
                                                    { x: 40, y: 40 },
                                                    { x: canvas.width / 2 - 80, y: canvas.height / 2 - 10 },
                                                    { x: canvas.width - 240, y: canvas.height - 80 }
                                                ];

                                                for (const pos of positions) {
                                                    ctx.save();
                                                    ctx.translate(pos.x, pos.y);
                                                    ctx.rotate(-Math.PI / 10);
                                                    ctx.fillText(text, 0, 0);
                                                    ctx.restore();
                                                }
                                            };

                                            const processImage = (attachment) => {
                                                return new Promise((resolve, reject) => {
                                                        const img = new Image();
                                                img.crossOrigin = 'anonymous';
                                                img.src = attachment.url;

                                                img.onload = () => {
                                                    const canvas = document.createElement('canvas');
                                                    canvas.width = img.width;
                                                    canvas.height = img.height;
                                                    const ctx = canvas.getContext('2d');
                                                    ctx.drawImage(img, 0, 0);

                                                    drawWatermarkGrid(ctx, canvas, "Urbanhome.vn");

                                                    canvas.toBlob((blob) => {
                                                        if (!blob) return reject("Không tạo được blob từ ảnh.");

                                                    const formData = new FormData();
                                                    formData.append('action', 'upload_watermarked_image');
                                                    formData.append('file', blob, 'watermarked.jpg');

                                                    fetch(ajaxurl, {
                                                        method: 'POST',
                                                        body: formData
                                                    })
                                                        .then(res => res.json())
                                                .then(res => {
                                                        if (res.success && res.data.url) {
                                                        const imgEl = $('<img>', {
                                                            src: res.data.url,
                                                            style: 'max-width:100px; margin:5px; border-radius:5px; box-shadow:0 0 8px rgba(0,0,0,0.15);'
                                                        });
                                                        previewContainer.append(imgEl);
                                                        ids.push(res.data.id);
                                                        resolve();
                                                    } else {
                                                        alert('Upload thất bại: ' + (res.data?.message || 'Không rõ lỗi'));
                                                        reject();
                                                    }
                                                })
                                                .catch(err => {
                                                        alert('Lỗi mạng: ' + err.message);
                                                    reject(err);
                                                });
                                                }, 'image/jpeg', 0.92);
                                                };

                                                img.onerror = () => {
                                                    alert('Không tải được ảnh: ' + attachment.url);
                                                    reject();
                                                };
                                            });
                                            };

                                            (async () => {
                                                for (const att of selection) {
                                                const attachment = att.toJSON();
                                                const mime = attachment.mime || '';

                                                try {
                                                    if (mime.startsWith('image/')) {
                                                        await processImage(attachment);
                                                    } else if (mime.startsWith('video/')) {
                                                        const videoEl = $('<video>', {
                                                            src: attachment.url,
                                                            controls: true,
                                                            style: 'max-width:100px; margin:5px; border-radius:5px; box-shadow:0 0 8px rgba(0,0,0,0.15);'
                                                        });
                                                        previewContainer.append(videoEl);
                                                        ids.push(attachment.id);
                                                    }
                                                } catch (e) {
                                                    console.error('Lỗi xử lý file:', e);
                                                }
                                            }

                                            hiddenInput.val(ids.join(','));
                                        })();
                                        });

                                        mediaFrame.open();
                                    });





                                }

                                // Reset giá trị input ẩn và ẩn preview khi mở form lại
                                $('#sohong_image_id').val('');
                                $('#bosung_image_id').val('');
                                $('#sohong_image_preview').hide();
                                $('#bosung_image_preview').hide();
                                $('#sohong_image_multi').val('');
                                $('#sohong_image_multi_preview').empty();
                            }







                        },
                        serializeEditData: function (postData) {
                            postData.sohong_image_id = $('#sohong_image_id').val();
                            postData.bosung_image_id = $('#bosung_image_id').val();
                            postData.sohong_image_multi = $('#sohong_image_multi').val();
                            return postData;
                        },
                        afterShowForm: function ($form) {
                            setupAutoCalc();
                            setTimeout(function () {
                                // Tìm <tr> chứa ô input name="contact_info"
                                const contactInputRow = $form.find('tr').has('input[name="contact_info"]');
                                contactInputRow.hide();
                                // Tìm <tr> chứa ô input name="contact_all"
                                const contactAllInputRow = $form.find('tr').has('input[name="contact_all"]');
                                contactAllInputRow.hide();
                            }, 500);
                            // Chỉ gắn dấu * cho #tr_code_product và #tr_giaban thôi, bỏ #tr_name_area
                            const requiredRows = ["#tr_sonha","#tr_code_product", "#tr_giaban","#tr_name_area"];

                            requiredRows.forEach(function(rowId) {
                                const labelTd = $form.find(rowId + " .CaptionTD");
                                const currentText = labelTd.text().trim();

                                if (!currentText.includes("*") && labelTd.find("i.fa-asterisk").length === 0) {
                                    labelTd.html('<i class="fa fa-asterisk" style="color:red; margin-right:5px;"></i>' + currentText);
                                }
                            });


                        },
                        afterComplete: function() {
                            // Khi form đóng hoặc thêm xong
                            $('.ui-widget-overlay').remove(); // Dọn sạch lớp mờ
                        },
                        afterSubmit: function (response, postdata) {
                            try {
                                var res = JSON.parse(response.responseText);

                                if (res.success) {
                                    alert("✅ Cập nhật thành công!");
                                    return [true, "", res.id]; // success
                                } else {
                                    alert("❌ Lỗi: " + (res.data || "Không rõ lỗi"));
                                    return [false, res.data || "Lỗi không xác định"];
                                }
                            } catch (e) {
                                alert("❌ Không thể phân tích phản hồi từ server.");
                                return [false, "Lỗi phản hồi từ server"];
                            }
                        },
                        onclickSubmit: function (params, postdata) {
                            const vaitroArr = [];
                            const nameArr = [];
                            const dienthoaiArr = [];
                            const gioitinhArr = [];

                            // Quét qua tất cả các dòng liên hệ (kể cả dòng tạo động)
                            document.querySelectorAll("select[name='vaitro_more[]']").forEach(el => vaitroArr.push(el.value));
                            document.querySelectorAll("input[name='name_more[]']").forEach(el => nameArr.push(el.value));
                            document.querySelectorAll("input[name='dienthoaididong_more[]']").forEach(el => dienthoaiArr.push(el.value));
                            document.querySelectorAll("select[name='gioitinh_more[]']").forEach(el => gioitinhArr.push(el.value));

                            // Gán vào postdata
                            postdata.vaitro_more = vaitroArr;
                            postdata.name_more = nameArr;
                            postdata.dienthoaididong_more = dienthoaiArr;
                            postdata.gioitinh_more = gioitinhArr;

                            return postdata;
                        },
                        //$("#TblGrid_jqGrid tbody").append('<tr style="display: none;" class="FormData" id="tr_tablemain"><td class="CaptionTD"></td><td class="DataTD"><input type="text" role="textbox" id="tablemain" name="tablemain" value="'+selectedTable+'" rowid="" module="form" checkupdate="false" size="20" class="FormElement ui-widget-content ui-corner-all"></td></tr>')
                        onInitializeForm: function ($form) {
                            $form.css({height: "auto"});
                            $form.closest(".ui-jqdialog").css({height: "auto"});
                        },
                        viewPagerButtons:false, //hide next and pre in form
                        drag:true
                    });
                });

                function applySelect2Styles() {

                    $(".select2-container .select2-selection--single").css({
                        "height": "40px",  // Height of the select box
                        "line-height": "40px",  // Vertically align text
                        "border": "1px solid #ccc",  // Border color
                        "border-radius": "5px",  // Rounded corners
                        "font-size": "14px",  // Font size
                        "background-color": "#fff"  // Background color
                    });

                    $(".select2-container .select2-selection--single .select2-selection__rendered").css({
                        "padding-left": "10px",
                        "padding-right": "10px",
                        "line-height":"38px"
                    });

                    $(".select2-container--default .select2-results__options").css({
                        "max-height": "200px",  // Max height of dropdown options
                        "overflow-y": "auto"  // Enable vertical scrolling if the options exceed max height
                    });

                    $(".select2-container--default .select2-results__option").css({
                        "height": "30px",  // Height of individual options
                        "line-height": "30px"  // Align text vertically
                    });
                    $(".select2-container--default .select2-selection--single .select2-selection__arrow").css({
                        "height": "38px",  // Height of individual options
                        "width": "27px"  // Align text vertically
                    });

                    $(".select2-container--default .select2-results__option--highlighted").css({
                        "background-color": "#4CAF50",  // Highlight option on hover
                        "color": "white"  // White text on hover
                    });

                }

                // Enable filtering with select options for specific fields
                $("#jqGrid").jqGrid("filterToolbar", {
                    searchOperators: false, // Disable advanced operators
                    stringResult: true,
                    defaultSearch: "cn", // Default search contains (cn)
                    autosearch: true
                });

                // Apply styles for select filters
                $(".ui-search-toolbar select").css({
                    "height": "30px",
                    "border": "1px solid #ccc",
                    "border-radius": "4px",
                    "padding": "5px",
                    "text-align": "left",  // Ensures text inside dropdown is left-aligned
                    "display": "block",  // Ensures proper alignment
                    "width": "100%",  // Makes it match the column width
                });

                // add edit delete
                // Enable CRUD operations
                if (!ajax_object.can_edit) {
                    var canedit = false;
                }else{
                    var canedit = true;
                }
                if (!ajax_object.can_delete) {
                    var candelete = false;
                }else{
                    var candelete = true;
                }
                $("#jqGrid").navGrid("#jqGridPager", {
                        edit: canedit,
                        add: false,
                        del: candelete,
                        search: false,
                        view: false
                    },
                    {
                        // Edit options
                        url: ajaxurl,
                        mtype: "POST",
                        editData: {
                            action: "edit_jqgrid_data",
                            table: selectedTable,
                            id: function () {
                                var selRowId = $("#jqGrid").jqGrid('getGridParam', 'selrow');
                                console.log("Sending ID:", selRowId);  // Debugging
                                return selRowId ? selRowId : 0;
                            }
                        },
                        beforeSubmit: function (postdata, formid) {
                            if(selectedTable == 'wp_dulieunhadat'){

                                // Remove commas before saving
                                postdata.giaban = postdata.giaban.replace(/,/g, ''); // Remove commas
                                postdata.giathue = postdata.giathue.replace(/,/g, ''); // Remove commas
                                postdata.giachot = postdata.giachot.replace(/,/g, ''); // Remove commas

                                var product_type = $("#product_type").val(); // hoặc lấy từ formid nếu cần

                                if (product_type === "517") {
                                    if (!postdata.code_product || postdata.code_product.trim() === "") {
                                        return [false, "Bạn phải nhập Mã căn khi chọn loại Dự án!"];
                                    }
                                }
                                if (product_type === "515") {
                                    if (!postdata.sonha || postdata.sonha.trim() === "") {
                                        return [false, "Bạn phải nhập Số Nhà khi chọn loại Nhà ở riêng lẻ!"];
                                    }
                                }

                                var transaction_type = $("#transaction_type").val(); // hoặc lấy từ formid nếu cần

                                if (transaction_type === "509") {
                                    if (!postdata.giaban || postdata.giaban.trim() === "") {
                                        return [false, "Bạn phải nhập Giá bán khi chọn loại Bán!"];
                                    }
                                }
                                if (transaction_type === "511") {
                                    if (!postdata.giathue || postdata.giathue.trim() === "") {
                                        return [false, "Bạn phải nhập Giá thuê khi chọn loại Thuê!"];
                                    }
                                }

                                return [true, '', postdata];
                            }
                        },

                        afterShowForm: function($form) {
                            setupAutoCalc();
                            setTimeout(function () {
                                // Tìm <tr> chứa ô input name="contact_info"
                                const contactInputRow = $form.find('tr').has('input[name="contact_info"]');
                                contactInputRow.hide();
                                // Tìm <tr> chứa ô input name="contact_all"
                                const contactAllInputRow = $form.find('tr').has('input[name="contact_all"]');
                                contactAllInputRow.hide();
                            }, 500);
                            // Chỉ gắn dấu * cho #tr_code_product và #tr_giaban thôi, bỏ #tr_name_area
                            const requiredRows = ["#tr_sonha","#tr_code_product", "#tr_giaban","#tr_name_area"];

                            requiredRows.forEach(function(rowId) {
                                const labelTd = $form.find(rowId + " .CaptionTD");
                                const currentText = labelTd.text().trim();

                                if (!currentText.includes("*") && labelTd.find("i.fa-asterisk").length === 0) {
                                    labelTd.html('<i class="fa fa-asterisk" style="color:red; margin-right:5px;"></i>' + currentText);
                                }
                            });
                            //dangcanchonay
                            const rowId =  $("#jqGrid").jqGrid('getGridParam', 'selrow');
                            const rowData =  $("#jqGrid").jqGrid('getRowData', rowId);

                            // Gán lại input hidden nếu đã có trên form
                            $('#sohong_image_id').val(rowData.sohong_image_id || '');
                            $('#bosung_image_id').val(rowData.bosung_image_id || '');

                            // Gọi ajax để hiển thị ảnh preview
                            if (rowData.sohong_image_id) {
                                jQuery.post(ajax_object.ajaxurl, {
                                    action: 'get_image_url',
                                    image_id: rowData.sohong_image_id
                                }, function(res) {
                                    if (res.success) {
                                        $('#sohong_image_preview').attr('src', res.data.url).show();
                                    }
                                });
                            }

                            if (rowData.bosung_image_id) {
                                jQuery.post(ajax_object.ajaxurl, {
                                    action: 'get_image_url',
                                    image_id: rowData.bosung_image_id
                                }, function(res) {
                                    if (res.success) {
                                        $('#bosung_image_preview').attr('src', res.data.url).show();
                                    }
                                });
                            }
                        },
                        closeAfterEdit:true,
                        closeOnEscape: true,
                        onClose: function () {
                            $(".select2-dropdown").hide();
                            return true;
                        },
                        width:1000,
                        beforeInitData: function () {
                            var selRowId = $grid.jqGrid('getGridParam', 'selrow');

                            if (!selRowId) {
                                alert("Please select a row before editing.");
                                return false; // Prevents further execution
                            }
                        },
                        recreateForm: true,
                        beforeShowForm: function ($form) {

                            setTimeout(function () {
                                // Thêm CSS margin-top cho .group-title-row
                                const style = document.createElement("style");
                                style.innerHTML = `
        .group-title-row {
            margin-top: 16px !important;
        }
    `;
                                document.head.appendChild(style);

                                const tbodies = Array.from(document.querySelectorAll("#TblGrid_jqGrid tbody"));
//chiacot
                                const groupConfigs = [
                                    { from: 1, to: 27, title: "🔶 Thông tin chính", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 28, to: 34, title: "🔷 Thông tin liên hệ", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 35, to: 38, title: "🟢 Diện tích", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 38, to: 60, title: "🔸 Thông tin khác", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" }
                                ];

                                // Duyệt từng nhóm
                                groupConfigs.forEach((config, groupIndex) => {
                                    let group = tbodies
                                            .map(tbody => {
                                            const tr = tbody.querySelector("tr");
                                const rowpos = parseInt(tr?.getAttribute("data-rowpos"), 10);
                                return { tbody, rowpos };
                            })
                                .filter(item => !isNaN(item.rowpos) && item.rowpos >= config.from && item.rowpos <= config.to)
                                .sort((a, b) => a.rowpos - b.rowpos)
                                .map(item => item.tbody);

                                if (group.length === 0) return;

                                const tableBody = group[0].parentElement;

                                if (groupIndex !== 0) {
                                    const spacerBody = document.createElement("tbody");
                                    spacerBody.classList.add("group-spacer-body");
                                    spacerBody.innerHTML = `<tr><td colspan="2" style="height: 16px; padding:0; border: none;"></td></tr>`;
                                    tableBody.insertBefore(spacerBody, group[0]);
                                }

                                const titleRow = document.createElement("tr");
                                titleRow.classList.add("group-title-row");
                                titleRow.innerHTML = `
            <td colspan="2" style="
                font-weight:600;
                padding:8px 14px;
                background-color:${config.color};
                border:1px solid ${config.border};
                border-bottom:none;
                border-radius:8px 8px 0 0;
                color:${config.text};
                font-size:15px;
                letter-spacing:0.3px;
            ">${config.title}</td>`;
                                tableBody.insertBefore(titleRow, group[0]);

                                group.forEach((tbody, index) => {
                                    tbody.classList.add("group-middle");
                                tbody.style.backgroundColor = config.color;
                                tbody.style.border = "none";
                                tbody.style.boxShadow = "0 1px 4px rgba(0, 0, 0, 0.06)";

                                if (index === group.length - 1) {
                                    tbody.style.border = `1px solid ${config.border}`;
                                    tbody.style.borderTop = "none";
                                    tbody.style.borderRadius = "0 0 8px 8px";
                                } else {
                                    tbody.style.borderLeft = `1px solid ${config.border}`;
                                    tbody.style.borderRight = `1px solid ${config.border}`;
                                }
                            });

                            });
                            }, 300);

                            setTimeout(() => {
                                $form.find("tbody").each(function () {
                                const currentStyle = this.getAttribute("style")?.replace(/\s+/g, '').toLowerCase();
                                if (currentStyle === "width:100%;display:block;") {
                                    this.style.setProperty("height", "20px", "important");
                                }
                            });
                        }, 50);


//paddyedit
                            $("#sData").click(function() {
                                var formValid = true;  // Flag to check if form is valid
                                var firstInvalidField = null;  // To store the first invalid field

                                // Loop through all input fields and select2 fields to check for emptiness or invalidity
                                $("form input, form select").each(function() {
                                    // Check if the field is empty
                                    if ($(this).val() === "" || $(this).hasClass('invalid')) {
                                        if (!firstInvalidField) {
                                            firstInvalidField = $(this);  // Store the first invalid field
                                        }
                                        formValid = false;  // Set the form as invalid
                                    }

                                    // For select2 fields, check if the select2 dropdown has an empty value
                                    if ($(this).is("select") && $(this).hasClass("select2-hidden-accessible") && $(this).val() === null) {
                                        if (!firstInvalidField) {
                                            firstInvalidField = $(this);  // Store the first invalid select2 field
                                        }
                                        formValid = false;  // Set the form as invalid
                                    }
                                });

                                // If form is invalid, scroll to the first invalid field
                                if (!formValid && firstInvalidField) {
                                    // Check if the invalid field is a select2 field and scroll to its container
                                    if (firstInvalidField.hasClass("select2-hidden-accessible")) {
                                        firstInvalidField = firstInvalidField.siblings(".select2-container");  // Get the select2 container
                                    }

                                    // Scroll to the field
                                    $('html, body').animate({
                                        scrollTop: firstInvalidField.offset().top - 500  // Scroll to the field
                                    }, 500); // 500 ms duration for scrolling
                                }

                                return formValid;  // Return the validation result (true if valid, false otherwise)
                            });

                            // Select the input field for the specific column (replace 'column_name' with your column identifier)




                            // fix template table
                            //=========================
                            // --- jQuery chỉnh style bảng ---
                            var $table = $form.find("table");

// Style tổng thể cho table
                            $table.css({
                                "width": "100%",
                                "border-collapse": "collapse",
                                "display": "flex",
                                "flex-wrap": "wrap",
                                "gap": "0px",
                                "overflow": "hidden",
                                "padding": "16px",
                                "background-color": "#f9fafb",   // nền nhẹ nhàng
                                "border-radius": "8px",
                                "box-shadow": "0 4px 8px rgba(0,0,0,0.05)"
                            });

// Lấy toàn bộ tr để xử lý nhóm lại
                            var $rows = $table.find("tr");

// Xóa các tbody cũ
                            $table.find("tbody").remove();

// Tạo lại tbody đầu (2 hàng đầu giữ nguyên)
                            var $firstTbody = $("<tbody></tbody>").css({
                                "width": "100%",
                                "display": "block"
                            });
                            $firstTbody.append($rows.slice(0, 2));
                            $table.append($firstTbody);

// Nhóm các row còn lại theo từng 2 row
                            var groupSize = 2;
                            var restRows = $rows.slice(2);

                            for (var i = 0; i < restRows.length; i += groupSize) {
                                var $groupTbody = $("<tbody></tbody>").css({
                                    "display": "flex",
                                    "flex-wrap": "wrap",
                                    "gap": "8px",
                                    "width": "100%"
                                });

                                var group = restRows.slice(i, i + groupSize);
                                $groupTbody.append(group);
                                $table.append($groupTbody);
                            }

// Style từng dòng tr
                            $table.find("tr").css({
                                "display": "flex",
                                "flex-direction": "column",
                                "flex": "1 1 calc(50% - 12px)",  // 2 cột
                                "margin": "10px 0",
                                "max-width": "100%",
                                "box-sizing": "border-box",
                                "position": "relative"
                            });

// Style td và th
                            $table.find("td, th").css({
                                "padding": "6px 10px"
                            });

// Thêm icon Dashicons trước label
                            $table.find("label").each(function() {
                                var $label = $(this);
                                // Nếu chưa có icon thì thêm
                                if (!$label.find("span.dashicons").length) {
                                    $label.prepend('<span class="dashicons dashicons-admin-home" style="color:#3b82f6; margin-right:6px;"></span>');
                                }
                            });

// Style label: cho icon + text nằm ngang, căn giữa, khoảng cách đẹp
                            $table.find("label").css({
                                "display": "flex",
                                "align-items": "center",
                                "gap": "6px",
                                "font-weight": "600",
                                "color": "#333",
                                "margin-bottom": "4px",
                                "font-size": "14px"
                            });

// Style input, select, textarea cho gọn, đẹp + hiệu ứng bóng, hover, focus
                            $table.find("input, select, textarea").css({
                                "padding": "8px 12px",
                                "font-size": "15px",
                                "border": "1.5px solid #ccc",
                                "border-radius": "6px",
                                "width": "100%",
                                "box-sizing": "border-box",
                                "outline": "none",
                                "background-color": "#fafafa",
                                "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)",
                                "transition": "border-color 0.3s, box-shadow 0.3s, background-color 0.3s"
                            }).on("focus", function () {
                                $(this).css({
                                    "border-color": "#2563eb",
                                    "box-shadow": "0 0 12px rgba(37,99,235,0.6)",
                                    "background-color": "#fff"
                                });
                            }).on("blur", function () {
                                $(this).css({
                                    "border-color": "#ccc",
                                    "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)",
                                    "background-color": "#fafafa"
                                });
                            }).on("mouseenter", function () {
                                $(this).css({
                                    "border-color": "#60a5fa",
                                    "box-shadow": "0 0 8px rgba(59,130,246,0.3)"
                                });
                            }).on("mouseleave", function () {
                                if (!$(this).is(":focus")) {
                                    $(this).css({
                                        "border-color": "#ccc",
                                        "box-shadow": "inset 0 1px 3px rgba(0,0,0,0.1)"
                                    });
                                }
                            });

// Style cho .form-row trong table để các input nằm ngang, đẹp hơn
                            $table.find(".form-row").css({
                                "display": "flex",
                                "align-items": "center",
                                "justify-content": "space-between",
                                "gap": "8px",
                                "width": "100%",
                                "overflow": "hidden",
                                "padding": "8px 0",
                                "box-sizing": "border-box",
                                "flex-wrap": "wrap"
                            });

// Bố cục label + input trong .form-row để label cố định width, input tự động giãn
                            $table.find(".form-row label").css({
                                "flex": "0 0 140px",
                                "white-space": "nowrap"
                            });

                            $table.find(".form-row input, .form-row select, .form-row textarea").css({
                                "flex": "1 1 auto"
                            });
// Reinitialize Select2 on the select elements after DOM manipulation


//==========================



                            //select2
                            $form.find("select").each(function () {
                                $(this).select2({
                                    width: '100%',
                                    placeholder: 'Chọn thông tin',
                                    allowClear: false,
                                    minimumResultsForSearch: 0 // Enable search functionality
                                })
                            });

                            // Apply Select2 styling
                            applySelect2Styles();


                            /* var $thoigianthue = $('input[name="thoigianthue"]');
                             if ($thoigianthue.length > 0) {
                             $thoigianthue.datepicker({
                             dateFormat: 'yy-mm-dd',
                             changeMonth: true,
                             changeYear: true
                             });
                             };*/
                            var $hethanthue = $('input[name="hethanthue"]');
                            if ($hethanthue.length > 0) {
                                $hethanthue.datepicker({
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: true,
                                    changeYear: true
                                });
                            }




                            $form.addClass("custom-jqgrid-form");
                            // Modify the form header style
                            $form.parent().find('.ui-jqdialog-titlebar').css({
                                'background': '#FFA500', // Orange background
                                'color': 'white',
                                'font-size': '16px',
                                'font-weight': 'bold',
                                'padding': '10px',
                                'border-radius': '8px 8px 0 0'
                            });

                            // Style input fields
                            $form.find('input, select, textarea').css({
                                'width': '100%',
                                'padding': '8px',
                                'border': '1px solid #ccc',
                                'border-radius': '5px',
                                'font-size': '13px',
                            });

                            // Style the form buttons
                            $form.parent().find('.ui-jqdialog-buttonset button').css({
                                'background': '#4CAF50',
                                'color': 'white',
                                'font-size': '14px',
                                'padding': '8px 12px',
                                'border-radius': '5px',
                                'border': 'none',
                                'cursor': 'pointer',
                                'transition': '0.3s'
                            }).hover(
                                function () {
                                    $(this).css('background', '#388E3C');
                                },
                                function () {
                                    $(this).css('background', '#4CAF50');
                                }
                            );

                            // Change title text
                            if(selectedTable == 'wp_duan') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Dự Án');
                            }else if(selectedTable == 'wp_dulieunhadat') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Sản Phẩm');
                            }else if(selectedTable == 'wp_khachhang') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Khách Hàng');
                            }
                            // Apply modern styling to the header
                            $(".ui-jqdialog-titlebar").css({
                                "color": "#ffa500",          // White text
                                "font-size": "18px",
                                "font-weight": "600",
                                "padding": "5px",
                                "border-radius": "2px 2px 0 0",
                                "border-bottom": "3px solid #4caf50" // Darker green border bottom
                            });

                            // Style the close button for better UX
                            $(".ui-jqdialog-titlebar-close").css({
                                "background": "transparent",
                                "border": "none",
                                "cursor": "pointer",
                                "padding": "4px",
                                "transition": "0.3s"
                            });

                            // Hover effect for close button
                            $(".ui-jqdialog-titlebar-close span").css({
                                "color": "#fff",
                                "font-size": "16px"
                            });

                            $(".ui-jqdialog-titlebar-close").hover(
                                function () { $(this).css("opacity", "0.7"); },
                                function () { $(this).css("opacity", "1"); }
                            );


                            var dlgDiv = $("#editmod" + $grid[0].id);
                            var parentDiv = dlgDiv.parent();
                            var dlgWidth = dlgDiv.width();
                            var parentWidth = parentDiv.width();

                            // Đặt modal lên sát mép trên màn hình
                            dlgDiv.css({
                                "top": "-70px",  // Đẩy modal lên sát viền trên màn hình
                                "left": Math.round((parentWidth - dlgWidth) / 2) + "px",
                                /* "z-index": "9999", // Luôn nằm trên các phần tử khác
                                 "position": "fixed" // Giữ nguyên vị trí khi cuộn trang*/
                            });



                            // Fix the main dialog to avoid horizontal scrolling
                            /* var $dialog = $form.closest(".ui-jqdialog");
                             $dialog.css({
                             "border-radius": "10px",
                             "background": "#fff",
                             "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                             //"max-width": "800px",
                             "width": "70%",  // Prevents form from being too wide
                             "overflow": "hidden", // Prevents horizontal scrolling,
                             "padding" :"0px",
                             });*/
                            // Fix the main dialog to avoid horizontal scrolling
                            var $dialog = $form.closest(".ui-jqdialog");
                            const winWidth = $(window).width();
                            if (winWidth <= 768) {

                                const dialogWidth = Math.min(winWidth * 0.9, 400); // tối đa 400px hoặc 90% màn hình


                                $dialog.css({
                                    width: dialogWidth + "px",
                                    left: ((winWidth - dialogWidth) / 2) + "px",
                                    top: "60px",
                                    //"z-index": 9999,
                                    "border-radius": "10px",
                                    "background": "#fff",
                                    "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                                    //"max-width": "800px",
                                    "width": "85%",  // Prevents form from being too wide
                                    "overflow": "hidden", // Prevents horizontal scrolling,
                                    "padding": "0px",
                                });
                            }else{
                                $dialog.css({
                                    "border-radius": "10px",
                                    "background": "#fff",
                                    "box-shadow": "0px 4px 10px rgba(0,0,0,0.2)",
                                    //"max-width": "800px",
                                    "overflow": "hidden", // Prevents horizontal scrolling,
                                    "padding": "0px",
                                });
                            }

                            // Style labels
                            $form.find("td.CaptionTD").css({
                                "font-weight": "bold",
                                "color": "#333",
                                //"width": "100%",
                                "margin-bottom": "5px",
                                "text-align": "left",
                                "position":"absolute",
                                "top": "-10px",
                                "z-index": "1",
                                "left": "21px",
                                "height": "30px",
                                "background" : "linear-gradient(to bottom, transparent 50%, rgb(255, 255, 255) 50%, rgb(255, 255, 255) 51%, transparent 65%)",

                            });

                            // Style input fields
                            $form.find("td.DataTD input, td.DataTD select").css({
                                "width": "100%",
                                "padding": "8px",
                                "border": "1px solid #ccc",
                                "border-radius": "5px",
                                "font-size": "14px",
                                "box-sizing": "border-box",
                                "height": "40px",
                                "transition": "all 0.3s ease-in-out" // Hiệu ứng mượt khi thay đổi trạng thái
                            });

                            // Hover effect
                            $form.find("td.DataTD input, td.DataTD select").hover(
                                function () {
                                    $(this).css("border-color", "#007bff"); // Khi hover, viền đổi màu xanh
                                },
                                function () {
                                    $(this).css("border-color", "#ccc"); // Khi không hover, trở lại màu cũ
                                }
                            );

                            // Focus effect
                            $form.find("td.DataTD input, td.DataTD select").on("focus", function () {
                                $(this).css({
                                    "border-color": "#007bff",
                                    "box-shadow": "0 0 5px rgba(0, 123, 255, 0.5)"
                                });
                            }).on("blur", function () {
                                $(this).css({
                                    "border-color": "#ccc",
                                    "box-shadow": "none"
                                });
                            });

                            // Fix button alignment
                            $(".EditButton").css({
                                "display": "flex",
                                "justify-content": "center",
                                "gap": "15px",
                                "margin-top": "20px"
                            });

                            // Style Submit button
                            $("#sData").html('<span style="color: #fff;">✔ Tiếp Tục</span>').css({
                                "background": "#4CAF50",
                                "border-radius": "5px",
                                "padding": "10px 20px",
                                "font-weight": "bold",
                                "box-shadow": "0 2px 4px rgba(0,0,0,0.2)",
                                "border": "none"
                            });

                            // Style Cancel button
                            $("#cData").html('<span style="color: #fff;">✖ Hủy</span>').css({
                                "background": "#d9534f",
                                "border-radius": "5px",
                                "padding": "10px 20px",
                                "font-weight": "bold",
                                "box-shadow": "0 2px 4px rgba(0,0,0,0.2)",
                                "border": "none"
                            });



                            // Hide form error message
                            $("#FormError").hide();
                            $('[value="_empty"]').hide();



                            // handle ajax wp_province, wp_district, wp_wards
                            var rowId = $(this).jqGrid('getGridParam', 'selrow');
                            var rowData = $(this).jqGrid('getRowData', rowId);

                            var provinceId = rowData.wp_province;
                            var districtId = rowData.wp_district;
                            var wardId = rowData.wp_wards;

                            // Reset các event tránh bị chồng event
                            $('#wp_province').off('change').on('change', function() {
                                var selectedProvince = $(this).val();
                                loadDistricts(selectedProvince);
                            });

                            $('#wp_district').off('change').on('change', function() {
                                var selectedDistrict = $(this).val();
                                loadWards(selectedDistrict);
                            });

                            // Set sẵn province
                            $('#wp_province').val(provinceId);

                            // Gọi load districts và tự set district + ward
                            if (provinceId) {
                                loadDistricts(provinceId, districtId);

                                // Vì loadDistricts sẽ tự trigger loadWards(selectedDistrictId)
                                // Nên gắn thêm loadWards khi loadDistrict xong
                                $(document).one('ajaxStop', function() {
                                    // Đợi tất cả ajax hoàn thành
                                    if (districtId) {
                                        loadWards(districtId, wardId);
                                    }
                                });
                            }






                            // hide show input
                            // Event listener for select dropdown change
                            $("#tr_code_product, #tr_name_area,#tr_sonha, #tr_sothua, #tr_soto").fadeOut();
                            /* $("#product_type").change(function () {
                             var selectedValue = $(this).val(); // Lấy giá trị đã chọn

                             if (selectedValue === "517") {
                             $("#tr_code_product, #tr_name_area").show(); // Hiển thị các input liên quan đến sản phẩm
                             $("#tr_sonha, #tr_sothua, #tr_soto").hide(); // Ẩn các input không liên quan
                             } else if (selectedValue === "515") {
                             $("#tr_code_product, #tr_name_area").hide(); // Ẩn các input sản phẩm
                             $("#tr_sonha, #tr_sothua, #tr_soto").show(); // Hiển thị các input liên quan đến địa chỉ
                             } else {
                             $("#tr_code_product, #tr_name_area, #tr_sonha, #tr_sothua, #tr_soto").hide(); // Ẩn tất cả các input khi không có lựa chọn nào phù hợp
                             }
                             });*/
                            setTimeout(function () {
                                updateFields();

                                // Gắn sự kiện change với namespace để tránh lỗi gắn trùng
                                $("#product_type", $form).off("change.producttype").on("change.producttype", function () {
                                    updateFields();
                                });

                            }, 1000); // delay để chờ DOM trong form jqGrid render xong

                            function updateFields() {
                                var selectedValue = $("#product_type", $form).val();

                                if (selectedValue === "517") {
                                    $("#tr_code_product, #tr_name_area", $form).show();
                                    $("#tr_sonha, #tr_sothua, #tr_soto", $form).hide();
                                } else if (selectedValue === "515") {
                                    $("#tr_code_product, #tr_name_area", $form).hide();
                                    $("#tr_sonha, #tr_sothua, #tr_soto", $form).show();
                                } else {
                                    $("#tr_code_product, #tr_name_area, #tr_sonha, #tr_sothua, #tr_soto", $form).hide();
                                }
                            }


                            // Ẩn tất cả các phần liên quan ban đầu
                            $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_donvi_giaban,#tr_giathue,#tr_thoigianthue,#tr_hethanthue,#tr_giaban,#tr_giachot").hide();

// Gán event change cho dropdown
                            $form.find("#transaction_type").change(function () {
                                var selectedValue = $(this).val();
                                console.log(selectedValue);

                                if (selectedValue === "509") {
                                    $("#tr_donvi_giaban,#tr_giaban,#tr_giachot").show();
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_giathue,#tr_thoigianthue,#tr_hethanthue").show();
                                    $('#tr_giathue .CaptionTD').text('HĐ thuê');
                                    $('#tr_donvi_giathue .CaptionTD').text('Đơn vị HĐ thuê');
                                    $('#tr_donvi_thoigiangia .CaptionTD').text('HĐ thuê theo');
                                } else if (selectedValue === "511") {
                                    $("#tr_donvi_giaban,#tr_giaban,#tr_giachot").hide();
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_giathue,#tr_thoigianthue,#tr_hethanthue").show();
                                    $('#tr_giathue .CaptionTD').html('<i class="fa fa-asterisk" style="color:red; margin-right:5px;"></i> Giá thuê');
                                    $('#tr_donvi_giathue .CaptionTD').text('Đơn vị giá thuê');
                                    $('#tr_donvi_thoigiangia .CaptionTD').text('Giá thuê theo');
                                } else {
                                    $("#tr_donvi_thoigiangia,#tr_donvi_thoigianthue,#tr_donvi_giathue,#tr_donvi_giaban,#tr_giathue,#tr_thoigianthue,#tr_hethanthue,#tr_giaban,#tr_giachot").hide();
                                }
                            });

// 🔥 Gọi sự kiện change thủ công khi trang load để cập nhật giao diện ban đầu
                            $form.find("#transaction_type").trigger("change");

                            // Event listener for select dropdown change
                            $("#tr_hoahong_sotien,#tr_hoahong_thang, #tr_hoahong_tyle").hide();
                            $form.find("#loaihoahong").change(function () {
                                var selectedValue = $(this).val();
                                console.log(selectedValue);
                                if (selectedValue === "651") {
                                    $("#tr_hoahong_tyle").show(); // Show input1 and input2
                                    $("#tr_hoahong_sotien, #tr_hoahong_thang").hide(); // Hide input3 and input4
                                } else if (selectedValue === "653") {
                                    $("#tr_hoahong_thang").show(); // Show input1 and input2
                                    $("#tr_hoahong_sotien, #tr_hoahong_tyle").hide(); // Hide input3 and input4
                                } else if (selectedValue === "649") {
                                    $("#tr_hoahong_sotien").show(); // Show input1 and input2
                                    $("#tr_hoahong_thang, #tr_hoahong_tyle").hide(); // Hide input3 and input4
                                }
                                else {
                                    $("#tr_hoahong_sotien,#tr_hoahong_thang, #tr_hoahong_tyle").hide();
                                }
                            });
                            // 🔥 Gọi thủ công để khởi tạo giao diện đúng theo giá trị ban đầu
                            $form.find("#loaihoahong").trigger("change");

//format number
                            $("input[name='thoigianthue'],input[name='dtd_ngang'], input[name='dtd_dai'],input[name='dtd_mathau'], input[name='dtd_congnhan'],input[name='dtqh_ngang'], input[name='dtqh_dai'], input[name='dtqh_mathau'], input[name='dtqh_xaydung'], input[name=''],input[name=''], input[name=''], input[name='ttk_duongrong'], input[name='ttk_dtsd'], input[name='dienthoaididong']").on("input", function(e) {
                                var value = $(this).val();
                                var sanitizedValue = value.replace(/[^0-9.]/g, '');
                                if ((sanitizedValue.match(/\./g) || []).length > 1) {
                                    sanitizedValue = sanitizedValue.replace(/\.+$/, '');
                                }
                                $(this).val(sanitizedValue);
                            });

                            // format a thousand separator
                            $("input[name='giathue'],input[name='giaban'],input[name='giachot']").on("input", function(e) {
                                var value = $(this).val();

                                // Remove any non-numeric characters except for a decimal point
                                var sanitizedValue = value.replace(/[^0-9.]/g, '');

                                // Ensure that only one decimal point is allowed
                                if ((sanitizedValue.match(/\./g) || []).length > 1) {
                                    sanitizedValue = sanitizedValue.replace(/\.+$/, '');  // Remove extra decimal points
                                }

                                // Format the number with comma as thousand separator
                                var parts = sanitizedValue.split('.');
                                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add comma separator every three digits

                                // Combine the integer and decimal parts
                                sanitizedValue = parts.join('.');

                                // Update the input field with the sanitized and formatted value
                                $(this).val(sanitizedValue);
                            });




                            if (selectedTable == 'wp_dulieunhadat') {
                                setTimeout(function() {
                                    const $container = $('#filter-usernhanvien').next('.select2-container');

                                    $container.find('.select2-selection--single').css({
                                        'height': '38px',
                                        'line-height': '38px',
                                        'padding': '0 16px',
                                        'border-radius': '6px',
                                        'border': 'none',
                                        'box-shadow': '0 4px 8px rgba(44, 123, 229, 0.4)', // shadow xanh dịu
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'box-sizing': 'border-box',
                                        'font-size': '14px',
                                        'font-weight': 'bold',
                                        'background-color': '#FFA500', // nền xanh button
                                        'color': '#fff',
                                        'cursor': 'pointer',
                                        'transition': 'background-color 0.3s ease'
                                    });
                                    $container.find('.select2-selection__placeholder').css({
                                        'color': '#fff',
                                        'opacity': '1'
                                    });


                                    // Khi mở dropdown, đổi nền đậm hơn
                                    $container.find('.select2-selection--single').on('click', function() {
                                        $(this).css('background-color', '#FFA500');
                                    });
                                    // Khi dropdown đóng, trả về nền ban đầu
                                    $container.find('.select2-selection--single').on('blur', function() {
                                        $(this).css('background-color', '#FFA500');
                                    });

                                    $container.find('.select2-selection__rendered').css({
                                        'line-height': '40px',
                                        'padding-left': '0',
                                        'margin': '0',
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'height': '100%',
                                        'color': '#fff'
                                    });

                                    $container.find('.select2-selection__arrow').css({
                                        'height': '40px',
                                        'top': '0',
                                        'right': '8px',
                                        'display': 'flex',
                                        'align-items': 'center',
                                        'color': '#fff'
                                    });
                                }, 10);
                                // Thêm nút upload + input ẩn + ảnh preview vào form nếu chưa có
                                if (!$('#upload_sohong_btn').length && !$('#upload_bosung_btn').length) {
                                    var html = '' +
                                        '<div style="display: flex; justify-content: center; gap: 40px; margin-bottom: 20px;">' +

                                        '<div style="text-align: center;">' +
                                        '<label><strong>Ảnh Sổ Hồng:</strong></label><br>' +
                                        '<button type="button" id="upload_sohong_btn" style="' +
                                        'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                        'background-color: #c82333; color: white; border: none; margin-top: 6px;">' +
                                        '<i class="fas fa-upload" style="margin-right: 6px;"></i> Chọn Sổ Hồng' +
                                        '</button>' +
                                        '<input type="hidden" id="sohong_image_id" name="sohong_image_id" value="">' +
                                        '<br>' +
                                        '<img id="sohong_image_preview" src="" alt="Ảnh Sổ Hồng" style="' +
                                        'max-width: 150px; border-radius: 5px; box-shadow: 0 0 8px rgba(0,0,0,0.15);' +
                                        'display: none; margin-top: 10px;">' +
                                        '</div>' +

                                        '<div style="text-align: center;">' +
                                        '<label><strong>Hình ảnh bổ sung:</strong></label><br>' +
                                        '<button type="button" id="upload_bosung_btn" style="' +
                                        'padding: 6px 12px; cursor: pointer; border-radius: 5px;' +
                                        'background-color: #007bff; color: white; border: none; margin-top: 6px;">' +
                                        '<i class="fas fa-upload" style="margin-right: 6px;"></i> Chọn ảnh bổ sung' +
                                        '</button>' +
                                        '<input type="hidden" id="bosung_image_id" name="bosung_image_id" value="">' +
                                        '<br>' +
                                        '<img id="bosung_image_preview" src="" alt="Ảnh bổ sung" style="' +
                                        'max-width: 150px; border-radius: 5px; box-shadow: 0 0 8px rgba(0,0,0,0.15);' +
                                        'display: none; margin-top: 10px;">' +
                                        '</div>' +

                                        '</div>';

                                    $form.append(html);

                                    // Hàm xử lý upload ảnh, watermark và set preview + input hidden
                                    function handleUpload(buttonId, imageId, previewId) {
                                        var frame;
                                        $(buttonId).on('click', function (e) {
                                            e.preventDefault();
                                            if (frame) return frame.open();
                                            frame = wp.media({
                                                title: 'Chọn ảnh',
                                                button: { text: 'Chọn ảnh' },
                                                multiple: false
                                            });
                                            frame.on('select', function () {
                                                var attachment = frame.state().get('selection').first().toJSON();

                                                // Hiển thị ảnh tạm thời
                                                $(previewId).attr('src', attachment.url).show();

                                                // Tạo watermark rồi upload ảnh đã watermark
                                                var img = new Image();
                                                img.crossOrigin = 'Anonymous';
                                                img.src = attachment.url;
                                                img.onload = function () {
                                                    var canvas = document.createElement('canvas');
                                                    canvas.width = img.width;
                                                    canvas.height = img.height;
                                                    var ctx = canvas.getContext('2d');
                                                    ctx.drawImage(img, 0, 0);

                                                    ctx.font = "60px Arial";
                                                    ctx.fillStyle = "rgba(255,0,0,0.6)";
                                                    ctx.textAlign = "right";
                                                    ctx.fillText("Urbanhome.vn", canvas.width - 20, canvas.height - 20);

                                                    canvas.toBlob(function (blob) {
                                                        var formData = new FormData();
                                                        formData.append('action', 'upload_watermarked_image');
                                                        formData.append('file', blob, 'watermarked.jpg');

                                                        fetch(ajaxurl, {
                                                            method: 'POST',
                                                            body: formData
                                                        })
                                                            .then(response => response.json())
                                                        .then(res => {
                                                            if (res.success) {
                                                            // Gán URL ảnh đã upload thành công
                                                            $(previewId).attr('src', res.data.url).show();
                                                            // Gán ID ảnh (giả sử backend trả về ID trong res.data.id)
                                                            $(imageId).val(res.data.id);
                                                        } else {
                                                            alert('Lỗi upload watermark');
                                                        }
                                                    })
                                                        .catch(err => alert('Lỗi mạng: ' + err.message));
                                                    }, 'image/jpeg', 0.92);
                                                };
                                            });
                                            frame.open();
                                        });
                                    }

                                    handleUpload('#upload_sohong_btn', '#sohong_image_id', '#sohong_image_preview');
                                    handleUpload('#upload_bosung_btn', '#bosung_image_id', '#bosung_image_preview');
                                }

                                // Hàm show ảnh từ attachment ID trong WP Media
                                function showImageFromId(imageId, $previewEl) {
                                    if (!imageId) return;
                                    wp.media.attachment(imageId).fetch().then(function (attachment) {
                                        if (attachment && attachment.url) {
                                            $previewEl.attr('src', attachment.url).show();
                                        }
                                    });
                                }



                                // Trường hợp chỉnh sửa: load dữ liệu đã có
                                $('#sohong_image_id').val(rowData.sohong_image_id || '');
                                $('#bosung_image_id').val(rowData.bosung_image_id || '');

                                showImageFromId(rowData.sohong_image_id, $('#sohong_image_preview'));
                                showImageFromId(rowData.bosung_image_id, $('#bosung_image_preview'));

                            }





                            $('#id_g').closest('tr').hide();




                        },
                        serializeEditData: function (postData) {
                            postData.sohong_image_id = $('#sohong_image_id').val();
                            postData.bosung_image_id = $('#bosung_image_id').val();
                            return postData;
                        },
                        afterSubmit: function (response, postdata) {
                            try {
                                var res = JSON.parse(response.responseText);

                                if (res.success) {
                                    alert("✅ Cập nhật thành công!");
                                    return [true, "", res.id]; // success
                                } else {
                                    alert("❌ Lỗi: " + (res.data || "Không rõ lỗi"));
                                    return [false, res.data || "Lỗi không xác định"];
                                }
                            } catch (e) {
                                alert("❌ Không thể phân tích phản hồi từ server.");
                                return [false, "Lỗi phản hồi từ server"];
                            }
                        },
                        onInitializeForm: function ($form) {
                            $form.css({height: "auto"});
                            $form.closest(".ui-jqdialog").css({height: "auto"});
                        },
                        viewPagerButtons:false, //hide next and pre in form
                        drag:true
                    },
                    {
                        // Add options
                        url: ajaxurl,
                        mtype: "POST",
                        editData: { action: "add_jqgrid_data", table: selectedTable},
                        closeAfterAdd: true,
                        closeOnEscape: true,
                        reloadAfterSubmit: true,
                        onClose: function () {
                            $(".select2-dropdown").hide();
                            return true;
                        },
                        width:1000,
                        beforeInitData: function () {
                            selRowId = $grid.jqGrid('getGridParam', 'selrow');
                        },
                        recreateForm: true,
                        beforeShowForm: function ($form) {
                            setTimeout(function () {
                                // Thêm CSS margin-top cho .group-title-row
                                const style = document.createElement("style");
                                style.innerHTML = `
        .group-title-row {
            margin-top: 16px !important;
        }
    `;
                                document.head.appendChild(style);

                                const tbodies = Array.from(document.querySelectorAll("#TblGrid_jqGrid tbody"));

                                const groupConfigs = [
                                    { from: 1, to: 27, title: "🔶 Thông tin chính", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 28, to: 33, title: "🔷 Thông tin liên hệ", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 34, to: 37, title: "🟢 Diện tích", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" },
                                    { from: 38, to: 60, title: "🔸 Thông tin khác", color: "rgb(249, 250, 251)", border: "#cbd5e0", text: "rgb(76, 175, 80)" }
                                ];
                                // Duyệt từng nhóm
                                groupConfigs.forEach((config, groupIndex) => {
                                    let group = tbodies
                                            .map(tbody => {
                                            const tr = tbody.querySelector("tr");
                                const rowpos = parseInt(tr?.getAttribute("data-rowpos"), 10);
                                return { tbody, rowpos };
                            })
                                .filter(item => !isNaN(item.rowpos) && item.rowpos >= config.from && item.rowpos <= config.to)
                                .sort((a, b) => a.rowpos - b.rowpos)
                                .map(item => item.tbody);

                                if (group.length === 0) return;

                                const tableBody = group[0].parentElement;

                                if (groupIndex !== 0) {
                                    const spacerBody = document.createElement("tbody");
                                    spacerBody.classList.add("group-spacer-body");
                                    spacerBody.innerHTML = `<tr><td colspan="2" style="height: 16px; padding:0; border: none;"></td></tr>`;
                                    tableBody.insertBefore(spacerBody, group[0]);
                                }

                                const titleRow = document.createElement("tr");
                                titleRow.classList.add("group-title-row");
                                titleRow.innerHTML = `
            <td colspan="2" style="
                font-weight:600;
                padding:8px 14px;
                background-color:${config.color};
                border:1px solid ${config.border};
                border-bottom:none;
                border-radius:8px 8px 0 0;
                color:${config.text};
                font-size:15px;
                letter-spacing:0.3px;
            ">${config.title}</td>`;
                                tableBody.insertBefore(titleRow, group[0]);

                                group.forEach((tbody, index) => {
                                    tbody.classList.add("group-middle");
                                tbody.style.backgroundColor = config.color;
                                tbody.style.border = "none";
                                tbody.style.boxShadow = "0 1px 4px rgba(0, 0, 0, 0.06)";

                                if (index === group.length - 1) {
                                    tbody.style.border = `1px solid ${config.border}`;
                                    tbody.style.borderTop = "none";
                                    tbody.style.borderRadius = "0 0 8px 8px";
                                } else {
                                    tbody.style.borderLeft = `1px solid ${config.border}`;
                                    tbody.style.borderRight = `1px solid ${config.border}`;
                                }
                            });

                            });
                            }, 300);

                            setTimeout(() => {
                                $form.find("tbody").each(function () {
                                const currentStyle = this.getAttribute("style")?.replace(/\s+/g, '').toLowerCase();
                                if (currentStyle === "width:100%;display:block;") {
                                    this.style.setProperty("height", "20px", "important");
                                }
                            });
                        }, 50);

                            // remove US$ pap status
                            /* if(jq_table == 'papmall_pap'){
                             var priceOrigin = $("#pap_price").val();
                             var priceCut = priceOrigin.split("US$ ");
                             $("#pap_price").val(parseFloat(priceCut[1]));
                             }*/
                            /*var dlgDiv = $("#editmod" + $grid[0].id);
                             var parentDiv = dlgDiv.parent();
                             var dlgWidth = dlgDiv.width();
                             var parentWidth = parentDiv.width();
                             var dlgHeight = dlgDiv.height();
                             var parentHeight = parentDiv.height();
                             dlgDiv[0].style.top = Math.round((parentHeight-dlgHeight)/2) + "px";
                             dlgDiv[0].style.left = Math.round((parentWidth-dlgWidth)) + "px";*/
                            // Add custom class for styling
                            $form.addClass("custom-jqgrid-form");

                            // Modify the form header style
                            $form.parent().find('.ui-jqdialog-titlebar').css({
                                'background': '#FFA500', // Orange background
                                'color': 'white',
                                'font-size': '16px',
                                'font-weight': 'bold',
                                'padding': '10px',
                                'border-radius': '8px 8px 0 0'
                            });

                            // Style input fields
                            $form.find('input, select, textarea').css({
                                'width': '100%',
                                'padding': '8px',
                                'border': '1px solid #ccc',
                                'border-radius': '5px',
                            });

                            // Style the form buttons
                            $form.parent().find('.ui-jqdialog-buttonset button').css({
                                'background': '#4CAF50',
                                'color': 'white',
                                'font-size': '14px',
                                'padding': '8px 12px',
                                'border-radius': '5px',
                                'border': 'none',
                                'cursor': 'pointer',
                                'transition': '0.3s'
                            }).hover(
                                function () {
                                    $(this).css('background', '#388E3C');
                                },
                                function () {
                                    $(this).css('background', '#4CAF50');
                                }
                            );

                            // Change title text
                            if(selectedTable == 'wp_duan') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Dự Án');
                            }else if(selectedTable == 'wp_dulieunhadat') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Sản Phẩm');
                            }else if(selectedTable == 'wp_khachhang') {
                                $(".ui-jqdialog-title").html('<img src="https://s.w.org/images/core/emoji/15.0.3/svg/2795.svg" style="width: 18px; height: 18px; filter: invert(40%) sepia(90%) saturate(3000%) hue-rotate(90deg);"> Thêm Khách Hàng');
                            }
                            // Apply modern styling to the header
                            $(".ui-jqdialog-titlebar").css({
                                "color": "#ffa500",          // White text
                                "font-size": "18px",
                                "font-weight": "600",
                                "padding": "5px",
                                "border-radius": "2px 2px 0 0",
                                "border-bottom": "3px solid #4caf50" // Darker green border bottom
                            });

                            // Style the close button for better UX
                            $(".ui-jqdialog-titlebar-close").css({
                                "background": "transparent",
                                "border": "none",
                                "cursor": "pointer",
                                "padding": "4px",
                                "transition": "0.3s"
                            });

                            // Hover effect for close button
                            $(".ui-jqdialog-titlebar-close span").css({
                                "color": "#fff",
                                "font-size": "16px"
                            });

                            $(".ui-jqdialog-titlebar-close").hover(
                                function () { $(this).css("opacity", "0.7"); },
                                function () { $(this).css("opacity", "1"); }
                            );

                            setTimeout(function () {
                                $("#sData .fm-button-text").text("Lưu");  // Change "Submit" to "Lưu"
                                $("#cData .fm-button-text").text("Hủy bỏ");  // Change "Cancel" to "Hủy bỏ"
                            }, 100); // Ensure the form is fully rendered



                            var dlgDiv = $("#editmod" + $grid[0].id);
                            var parentDiv = dlgDiv.parent();
                            var dlgWidth = dlgDiv.width();
                            var parentWidth = parentDiv.width();

                            // Đặt modal lên sát mép trên màn hình
                            dlgDiv.css({
                                "top": "-50px",  // Đẩy modal lên sát viền trên màn hình
                                "left": Math.round((parentWidth - dlgWidth) / 2) + "px",
                                "z-index": "9999", // Luôn nằm trên các phần tử khác
                                "position": "fixed" // Giữ nguyên vị trí khi cuộn trang
                            });


                            //$("#TblGrid_jqGrid tbody").append('<tr style="display: none;" class="FormData" id="tr_tablemain"><td class="CaptionTD"></td><td class="DataTD"><input type="text" role="textbox" id="tablemain" name="tablemain" value="'+selectedTable+'" rowid="" module="form" checkupdate="false" size="20" class="FormElement ui-widget-content ui-corner-all"></td></tr>');
                        },
                        afterSubmit: function (response, postdata) {
                            try {
                                var res = JSON.parse(response.responseText);

                                if (res.success) {
                                    alert("✅ Cập nhật thành công!");
                                    return [true, "", res.id]; // success
                                } else {
                                    alert("❌ Lỗi: " + (res.data || "Không rõ lỗi"));
                                    return [false, res.data || "Lỗi không xác định"];
                                }
                            } catch (e) {
                                alert("❌ Không thể phân tích phản hồi từ server.");
                                return [false, "Lỗi phản hồi từ server"];
                            }
                        },
                        //$("#TblGrid_jqGrid tbody").append('<tr style="display: none;" class="FormData" id="tr_tablemain"><td class="CaptionTD"></td><td class="DataTD"><input type="text" role="textbox" id="tablemain" name="tablemain" value="'+selectedTable+'" rowid="" module="form" checkupdate="false" size="20" class="FormElement ui-widget-content ui-corner-all"></td></tr>')
                        onInitializeForm: function ($form) {
                            $form.css({height: "auto"});
                            $form.closest(".ui-jqdialog").css({height: "auto"});
                        },
                        viewPagerButtons:false, //hide next and pre in form
                        drag:true
                    },
                    /*  {
                     url: ajaxurl, // This must be set to admin-ajax.php
                     mtype: "POST",
                     reloadAfterSubmit: true,
                     onclickSubmit: function (params, postdata) {
                     const selRowId = $("#jqGrid").jqGrid('getGridParam', 'selrow');
                     if (!selRowId) {
                     alert("Please select a row");
                     return false;
                     }

                     return {
                     action: "delete_jqgrid_data", // 🟢 MUST HAVE THIS!
                     table: selectedTable,         // your custom table name
                     id: selRowId,
                     oper: "del"
                     };
                     }
                     },*/
                    {
                        //delete option
                        url: ajaxurl,
                        mtype: "POST",
                        reloadAfterSubmit: true,
                        onclickSubmit: function (params, postdata) {
                            const selRowId = $("#jqGrid").jqGrid('getGridParam', 'selrow');
                            if (!selRowId) {
                                alert("Vui lòng chọn dòng cần xóa");
                                return false;
                            }
                            return {
                                action: "delete_jqgrid_data",
                                table: selectedTable,
                                id: selRowId,
                                oper: "del",
                                force_delete: isTrashView ? 1 : 0 // 🟢 Nếu đang ở thùng rác => xóa vĩnh viễn
                            };
                        },
                        afterShowForm: function ($form) {
                            // 🟢 Scroll đến popup khi nó hiển thị
                            $("html, body").animate({
                                scrollTop: $form.offset().top - 100
                            }, 300);
                        }

                    },
                    {} // search options

                );

            },
            error: function () {
                console.error("Failed to fetch colModel");
            }
        });
    }



    // Xử lý khi bấm nút Thùng rác
    $("#deleteBtn").on("click", function () {
        isTrashView = true;

        // Lấy dữ liệu post hiện tại
        let currentPostData = $("#jqGrid").jqGrid("getGridParam", "postData");

        // Gán lại dữ liệu post, thêm deleted: 1
        $("#jqGrid").jqGrid("setGridParam", {
            postData: {
                deleted: 1
            },
            page: 1 // reset về trang đầu tiên
        }).trigger("reloadGrid");
    });


});