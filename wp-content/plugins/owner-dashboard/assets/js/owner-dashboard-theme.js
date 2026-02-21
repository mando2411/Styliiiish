
// متغيرات عامة نستخدمها فى كل الكود
//window.currentFilters = { search:'', cat:'', status:'', page:1 };
//window.currentProduct = 0;
//window.currentImageProd = 0;
//window.searchTimer = null;
//window.imageFrame = null;

// Override functions placeholder
//function scrollToManageProducts() {}
//window.loadManageProductsPage = function(page){}

// Run first load
//setTimeout(() => loadManageProductsPage(1), 300);
// 🚫 Stop this script completely outside dashboard pages




// متغيرات عامة نستخدمها فى كل الكود
window.currentFilters   = { search:'', cat:'', status:'', page:1 };
window.currentProduct   = 0;
window.currentImageProd = 0;
window.searchTimer      = null;
window.imageFrame       = null;

// مجرد placeholders – التعريف الحقيقي جوه jQuery(function($){...})
function scrollToManageProducts() {}
window.loadManageProductsPage = function(page){};


 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/









 jQuery(function($){




/*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of   // Skeleton Loader          ##################                              */
 /**************************** // Function Of   // Skeleton Loader  *******************************************/
   /**************************** // Function Of // Skeleton Loader  *******************************************/  
   function showLoadingSkeleton() {

    let mode = jQuery('#styliiiish-manage-products-content').data('mode');

    let rowsCount = 10;
    let rows = "";
    for (let i = 0; i < rowsCount; i++) {
        rows += `
            <tr class="skeleton-row">
                <td><div class="sk-box sk-check"></div></td>
                <td><div class="sk-box sk-img"></div></td>
                <td><div class="sk-line sk-w-80"></div></td>
                <td><div class="sk-line sk-w-90"></div></td>
                <td><div class="sk-line sk-w-80"></div></td>
                <td><div class="sk-line sk-w-50"></div></td>
                <td>
                    <div class="sk-line sk-w-90"></div>
                    <div class="sk-line sk-w-60 mt-4"></div>
                </td>
                <td><div class="sk-dropdown"></div></td>
                <td>
                    <div class="sk-btn"></div>
                    <div class="sk-btn"></div>
                    <div class="sk-btn"></div>
                </td>
            </tr>
        `;
    }

    let headHtml = $('#styliiiish-manage-products-content table thead').html() || `
        <tr>
            <th></th>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Attributes</th>
            <th>Price</th>
            <th>Categories</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>`;

    /* ============================
       USER MODE – NEW SKELETON
    ============================ */
    let userStatsSkeleton = `
        <div class="pretty-stats skeleton-stats">
            <div class="pretty-stat-box skeleton"></div>
            <div class="pretty-stat-box skeleton"></div>
            <div class="pretty-stat-box skeleton"></div>
            <div class="pretty-stat-box skeleton"></div>
        </div>
    `;

    /* ============================
       OWNER MODE – OLD SKELETON
    ============================ */
    let ownerStatsSkeleton = `
        <div class="styliiiish-stats-bar">
            <div class="styliiiish-stat-box"><span class="label">Total</span><span class="value">...</span></div>
            <div class="styliiiish-stat-box"><span class="label">Published</span><span class="value">...</span></div>
            <div class="styliiiish-stat-box"><span class="label">Draft</span><span class="value">...</span></div>
        </div>
    `;

    $('#styliiiish-manage-products-content').html(`
        ${ mode === 'user' ? userStatsSkeleton : ownerStatsSkeleton }
        <table class="owner-products-table skeleton-mode">
            <thead>${headHtml}</thead>
            <tbody>${rows}</tbody>
        </table>
    `);
}

        
        
        
        
        
        
        
        
        

window.scrollToManageProducts = function() {
    let offset = $('#styliiiish-manage-products-content').offset();
    if (!offset) return;
    $('html, body').animate({
        scrollTop: offset.top - 80
    }, 250);
}

window.loadManageProductsPage = function(page) {
    currentFilters.page = page || 1;
    showLoadingSkeleton();



    


    $.post(ajax_object.ajax_url, {
        action: 'styliiiish_manage_products_list',
        page: currentFilters.page,
        search: currentFilters.search,
        cat: currentFilters.cat,
        status: currentFilters.status,
        mode: jQuery('#styliiiish-manage-products-content').data('mode')
    }, function (html) {
        $('#styliiiish-manage-products-content')
            .hide()
            .html(html)
            .fadeIn(180);
    });
};


/** ⬅️⬅️⬅️⬅️ هنا بالضبط تحط السطر */
// أول لود بعد ما الـ DOM يجهز
if ($('#styliiiish-manage-products-content').length) {
    setTimeout(function(){
        loadManageProductsPage(1);
    }, 200);
}









/*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of   Pagination           ##################                              */
 /**************************** // Function Of   Pagination  *******************************************/
   /**************************** // Function Of   Pagination  *******************************************/
        $(document).on('click', '.styliiiish-page-link', function (e) {
    e.preventDefault();
    var page = $(this).data('page');
    if (!page) return;
    scrollToManageProducts();
    loadManageProductsPage(page);
});












/*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of  // Filters Products          ##################                              */
 /**************************** // Function Of  // Filters Products *******************************************/
   /**************************** // Function Of  // Filters Products *******************************************/
        $('#styliiiish-search').on('keyup', function () {
            clearTimeout(searchTimer);
            let val = $(this).val();
            searchTimer = setTimeout(function () {
                currentFilters.search = val;
                loadManageProductsPage(1);
            }, 300);
        });

        $('#styliiiish-filter-cat').on('change', function () {
            currentFilters.cat = $(this).val();
            loadManageProductsPage(1);
        });

        $('#styliiiish-filter-status').on('change', function () {
            currentFilters.status = $(this).val();
            loadManageProductsPage(1);
        });







/*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of  Select all Products          ##################                              */
 /**************************** // Function Of  Select all Products *******************************************/
   /**************************** // Function Of  Select all Products *******************************************/
        
        $(document).on('change', '#styliiiish-select-all', function(){
            let checked = $(this).is(':checked');
            $('#styliiiish-manage-products-content .styliiiish-row-check').prop('checked', checked);
        });







 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of Bulk apply            ##################                              */
 /**************************** // Function Of Bulk apply *******************************************/
   /**************************** // Function Of Bulk apply *******************************************/
        // 
        $('#styliiiish-bulk-apply').on('click', function(){
            let action = $('#styliiiish-bulk-action').val();
            if (!action) {
                Swal.fire('Notice', 'Please select a bulk action.', 'info');
                return;
            }

            let ids = [];
            $('#styliiiish-manage-products-content .styliiiish-row-check:checked').each(function () {
                ids.push($(this).val());
            });

            if (!ids.length) {
                Swal.fire('Notice', 'No products selected.', 'info');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'Apply bulk action to selected products?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, apply',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.post(ajax_object.ajax_url, {
                    action: 'styliiiish_bulk_action',
                    bulk_action: action,
                    ids: ids
                }, function(response){
                    if (!response || typeof response.success === 'undefined') {
                        Swal.fire('Error', 'Unexpected response.', 'error');
                        return;
                    }
                    if (!response.success) {
                        Swal.fire('Error', response.data && response.data.message ? response.data.message : 'Bulk action failed.', 'error');
                        return;
                    }

                    Swal.fire('Done', response.data.message, 'success');
                    loadManageProductsPage(currentFilters.page);
                }, 'json').fail(function(){
                    Swal.fire('Error', 'Error communicating with server.', 'error');
                });
            });
        });
    







 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of attributes Edit And Save           ##################                              */
 /**************************** // Function Of attributes Edit And Save *******************************************/
   /**************************** // Function Of attributes Edit And Save *******************************************/
            

    let currentProductID = 0;
    let lastScrollY = 0;

    // قفل / فتح الاسكرول بطريقة بسيطة ومتوافقة مع Ekart
    function lockScroll() {
        lastScrollY = window.scrollY || window.pageYOffset || 0;
        $('html, body').addClass('attr-modal-open');
    }

    function unlockScroll() {
        $('html, body').removeClass('attr-modal-open');
        window.scrollTo(0, lastScrollY);
    }

    // ====== دالة الغلق الموحدة ======
    function closeAttrModal() {
        $('#attrModal').fadeOut(150, function () {
            unlockScroll();
            
            $('html').removeClass('attr-modal-open');
            document.documentElement.style.setProperty('--scrollbar-compensation', '0px');


        });
    }

    // ====== فتح المودال ======
    $(document).on('click', '.btn-edit-attrs', function (e) {
        e.preventDefault();

        currentProductID = $(this).data('id');
        if (!currentProductID) return;



        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.documentElement.style.setProperty('--scrollbar-compensation', scrollbarWidth + 'px');
        
        $('html').addClass('attr-modal-open');




        // قفل الاسكرول
        lockScroll();

        // عرض المودال
        $('#attrModal')
            .css('display', 'flex')
            .hide()
            .fadeIn(150);

        // تحميل البيانات
        $('#attrSelectorWrap')
            .empty()
            .append('<p style="margin:10px 0;">Loading attributes...</p>');

        $.post(ajax_object.ajax_url, {
            action: 'styliiiish_get_attributes',
            product_id: currentProductID
        }, function (res) {

            $('#attrSelectorWrap').empty();

            if (!res || !res.success) {
                $('#attrSelectorWrap').append('<p>Error loading attributes.</p>');
                return;
            }

            let attributes = res.data;

            if (!attributes.length) {
                $('#attrSelectorWrap').append('<p>No attributes registered for this product.</p>');
                return;
            }

            attributes.forEach(function (attr) {

                let html = `
                    <label style="font-weight:bold;margin-top:10px;display:block;">
                        ${attr.label}
                    </label>
                    <select class="single-attr"
                            data-tax="${attr.taxonomy}"
                            style="width:100%;">
                        <option value="">— Select —</option>
                `;

                attr.options.forEach(function (opt) {

                    // 🔥 إخفاء بعض الخيارات عن الـ User فقط
                    if (!ajax_object.is_manager) {
                
                        let forbidden = [
                            'new',
                            'used-very-good-styliiiish-certified',
                            'used'
                        ];
                
                        if (forbidden.includes(opt.value)) {
                            return; // Skip
                        }
                    }
                
                    let sel = (opt.value === attr.selected) ? 'selected' : '';
                    html += `<option value="${opt.value}" ${sel}>${opt.label}</option>`;
                });



                html += `</select>`;

                $('#attrSelectorWrap').append(html);
            });

            if ($.fn.select2) {
                $('.single-attr').select2({
                    width: '100%',
                    dropdownParent: $('#attrModal .attr-modal-content')
                });
            }

        }, 'json');

    });

    // ====== زر Save ======
    $(document).on('click', '#saveAttrChanges', function (e) {
        e.preventDefault();

        if (!currentProductID) return;

        let items = {};

        $('.single-attr').each(function () {
            let tax = $(this).data('tax');
            let val = $(this).val();
            items[tax] = val;
        });

        $.post(ajax_object.ajax_url, {
            action: 'styliiiish_save_attributes',
            product_id: currentProductID,
            items: items
        }, function (res) {

            if (!res || !res.success) {
                Swal.fire('Error', 'Error saving attributes.', 'error');
                return;
            }

            Swal.fire('Saved!', 'Attributes updated successfully.', 'success');

            closeAttrModal();
            
            // 🔥 بعد الحفظ — اعمل Pending Check
                $.post(ajax_object.ajax_url, {
                    action: "styliiiish_force_pending_check",
                    product_id: currentProductID
                });

            if (typeof loadManageProductsPage === 'function') {
                loadManageProductsPage(1);
            }

        }, 'json');
    });

    // ====== زر Close ======
    $(document).on('click', '#closeAttrModal', function (e) {
        e.preventDefault();
        closeAttrModal();
    });

    // ====== الضغط على الخلفية ======
    $(document).on("click", ".attr-modal", function(e) {
        if ($(e.target).hasClass("attr-modal")) {
            closeAttrModal();
        }
    });







 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of Duplicate Products            ##################                              */
 /**************************** // Function Of Duplicate Products *******************************************/
   /**************************** // Function Of Duplicate Products *******************************************/
            
        // Duplicate
$(document).on("click", ".btn-duplicate", function (e) {
    e.preventDefault();

    let product_id = $(this).data("id");
    if (!product_id) return;

    Swal.fire({
        title: "Duplicate product?",
        text: "A draft copy will be created.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, duplicate",
        cancelButtonText: "Cancel"
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.post(ajax_object.ajax_url, {
            action: "styliiiish_duplicate_product",
            product_id: product_id
        }, function(response){

            if (!response || typeof response.success === 'undefined') {
                Swal.fire("Error", "Unexpected response.", "error");
                return;
            }

            if (!response.success) {
                Swal.fire("Error", response.data && response.data.message ? response.data.message : "Failed to duplicate.", "error");
                return;
            }

            Swal.fire("Done", "Product duplicated successfully.", "success");

            setTimeout(function () {
                loadManageProductsPage(1);
                scrollToManageProducts();
            }, 300);

        }, 'json').fail(function(){
            Swal.fire("Error", "Error communicating with server.", "error");
        });

    });
});

 








 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of Delete Products            ##################                              */
 /**************************** // Function Of Delete Products *******************************************/
   /**************************** // Function Of Delete Products *******************************************/
            
        // Delete
        $(document).on("click", ".btn-delete", function (e) {
            e.preventDefault();

            let product_id = $(this).data("id");
            let $row = $(this).closest("tr");

            if (!product_id) return;

            Swal.fire({
                title: "Are you sure?",
                text: "This product will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.post(ajax_object.ajax_url, {
                        action: "styliiiish_delete_product",
                        product_id: product_id
                    }, function(response) {

                        if (!response || typeof response.success === 'undefined') {
                            Swal.fire("Error", "Unexpected response from server.", "error");
                            return;
                        }

                        if (!response.success) {
                            var msg = response.data && response.data.message ? response.data.message : "Failed to delete product.";
                            Swal.fire("Error", msg, "error");
                            return;
                        }

                        $row.fadeOut(200, function() {
                            $(this).remove();
                        });

                        Swal.fire("Deleted!", "Product removed successfully", "success");

                    }, 'json').fail(function () {
                        Swal.fire("Error", "Error communicating with server.", "error");
                    });
                }
            });
        });






 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of Status Of Products            ##################                              */
 /**************************** // Function Of Status Of Products *******************************************/
   /**************************** // Function Of Status Of Products *******************************************/

      // Inline Status
        $(document).on('change', '.inline-status', function () {
            var $select    = $(this);
            var product_id = $select.data('id');
            var status     = $select.val();
            var $row       = $select.closest('tr');

            $select.prop('disabled', true);

            $.post(ajax_object.ajax_url, {
                action: 'styliiiish_update_status',
                product_id: product_id,
                status: status
            }, function (response) {

                $select.prop('disabled', false);

                if (!response || typeof response.success === 'undefined') {
                    Swal.fire('Error', 'Unexpected response from server.', 'error');
                    return;
                }

                if (!response.success) {
                    var msg = response.data && response.data.message ? response.data.message : 'Error updating status.';
                    Swal.fire('Error', msg, 'error');
                    return;
                }

                $row.addClass('row-highlight');
                setTimeout(function(){ $row.removeClass('row-highlight'); }, 1200);

            }, 'json').fail(function () {
                $select.prop('disabled', false);
                Swal.fire('Error', 'Error communicating with server.', 'error');
            });
        });









 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of Categories Of Products            ##################                              */
 /**************************** // Function Of Categories Of Products *******************************************/
   /**************************** // Function Of Categories Of Products *******************************************/

        // Edit Categories Modal
        $(document).on("click", ".edit-cats-btn", function () {
            currentProduct = $(this).data("product");

            $.post(ajax_object.ajax_url, {
                action: "styliiiish_get_cats",
                product_id: currentProduct
            }, function (res) {
                $("#cats-checkboxes").html(res);
                $("#editCatsModal").css("display", "flex");
            });
        });

    // ====== زر Save ======
        $("#saveCatsBtn").on("click", function () {
            var selected = [];
            $('#cats-checkboxes input[type="checkbox"]:checked').each(function () {
                selected.push($(this).val());
            });

            $.post(ajax_object.ajax_url, {
                action: "styliiiish_save_cats",
                product_id: currentProduct,
                cats: selected
            }, function (newCats) {
                $("#cat-display-" + currentProduct).html(newCats);
                $("#editCatsModal").hide();
            });
        });

    // ====== زر Close ======
        $("#closeCatsBtn").on("click", function () {
            $("#editCatsModal").hide();
        });









/*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
/*         ############             Function Of Add New product           ##################                              */
 /**************************** // Function Of Add New product *******************************************/
   /**************************** // Function Of Add New product *******************************************/
            jQuery(document).on('click', '#styliiiish-add-product', function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Create new product?",
        text: "A new product will be created from the template.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Create",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (!result.isConfirmed) return;

        jQuery.post(ajax_object.ajax_url, {
            action: 'styliiiish_add_new_product'
        }, function(response){

            if (!response || !response.success) {
                Swal.fire("Error", response?.data?.message || "Failed!", "error");
                return;
            }

            Swal.fire("Done!", "Product created successfully.", "success");

            // 💥 Reload table
            setTimeout(() => {
                loadManageProductsPage(1);
            }, 200);

        }, 'json')
        .fail(function(xhr){
            Swal.fire("Error", "AJAX request failed!", "error");
        });
    });
});

	








/*         ############             IMAGE MODAL + MEDIA LIBRARY           ##################                              */
 /**************************** // IMAGE MODAL + MEDIA LIBRARY *******************************************/
   /**************************** // IMAGE MODAL + MEDIA LIBRARY *******************************************/

// تحديث صورة المنتج داخل الجدول
function updateImageRow(prodID, mainHTML) {
    let row = $(`tr[data-row-id="${prodID}"] .styliiiish-image-wrapper`);
    if (!row.length) return;

    const overlay = `<div class="styliiiish-image-overlay">Edit image</div>`;

    if (mainHTML) {
        row.html(mainHTML + overlay);
    } else {
        row.html(`<div class="no-image">No image</div>${overlay}`);
    }
}

// API Helper
function sendRequest(action, data, onSuccess) {
    $.post(ajax_object.ajax_url, { action, ...data }, function(response) {
        if (!response || response.success !== true) {
            Swal.fire("Error", response?.data?.message || "Error occurred.", "error");
            return;
        }
        onSuccess(response);
    }, "json").fail(function() {
        Swal.fire("Error", "Error communicating with server.", "error");
    });
}

/* ============================================================
   ADD / CHANGE IMAGE BUTTON
============================================================ */

$("#styliiiish-add-image").on("click", function (e) {
    e.preventDefault();

    if (!currentImageProd) return;

    // OWNER → افتح مكتبة الوسائط
    if (ajax_object.mode === "owner") {
        if (typeof wp !== "undefined" && wp.media) {

            if (!window.styImageFrame) {
                window.styImageFrame = wp.media({
                    title: "Select Image",
                    button: { text: "Use Image" },
                    multiple: false
                });

                window.styImageFrame.on("select", function () {

                    let attachment = window.styImageFrame.state().get("selection").first().toJSON();

                    sendRequest(
                        "styliiiish_add_image_to_product",
                        {
                            product_id: currentImageProd,
                            attachment_id: attachment.id
                        },
                        function(response) {

                            $("#styliiiish-images-list").html(response.data.html);
                            updateImageRow(currentImageProd, response.data.main);

                            // OWNER فقط → لا نعمل أي pending هنا
                        }
                    );
                });
            }

            window.styImageFrame.open();
            return;
        }
    }

    // USER → رفع ملف مباشر
    $("#styliiiish-upload-input").click();
});




/* ============================================================
   USER IMAGE UPLOAD HANDLER
============================================================ */

$("#styliiiish-upload-input").on("change", function () {
    const file = this.files[0];
    if (!file) return;

    let formData = new FormData();
    formData.append("action", "styliiiish_upload_image_custom");
    formData.append("product_id", currentImageProd);
    formData.append("file", file);

    // Show Lottie Loader
    $("#styliiiish-lottie-loader").show();
    $("#styliiiish-images-list").css("opacity", "0.3");
    $("#styliiiish-upload-percent").text("Uploading… 0%");

    $.ajax({
        url: ajax_object.ajax_url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        xhr: function () {
            let xhr = new window.XMLHttpRequest();

            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    let percent = Math.round((evt.loaded / evt.total) * 100);
                    $("#styliiiish-upload-percent").text(`Uploading… ${percent}%`);
                }
            }, false);

            return xhr;
        },
        success: function (response) {
            if (!response.success) {
                Swal.fire("Error", response.data.message || "Upload failed", "error");
                $("#styliiiish-lottie-loader").hide();
                $("#styliiiish-images-list").css("opacity", "1");
                return;
            }

            $("#styliiiish-upload-percent").text("Processing…");

            $("#styliiiish-images-list").html(response.data.html);
            updateImageRow(currentImageProd, response.data.main);


            /* ------------------------------------------------
               ⭐ USER ONLY — التطبيق الفعلي للشرطين
            ------------------------------------------------ */
            if (ajax_object.mode === "user") {

                let $row    = $(`tr[data-row-id="${currentImageProd}"]`);
                let status  = $row.find(".sty-status").text().trim().toLowerCase();

                /* ----------------------------------------------
                   ✔ الشرط الأول:
                     Active → Pending مباشرة عند إضافة صورة
                -----------------------------------------------*/
                if (status === "active") {
                    changeProductStatus(currentImageProd, "pending");
                }

                /* ----------------------------------------------
                   ✔ الشرط الثاني:
                     Uncomplete → Pending إذا أصبح مكتمل الشروط
                -----------------------------------------------*/
                if (status === "uncomplete") {

                    // قراءة البيانات من الأعمدة الموجودة بالفعل
                    let name   = $row.find(".sty-name").text().trim();
                    let desc   = $row.find(".sty-desc").attr("data-full") || "";
                    let price  = parseFloat($row.find(".sty-price").text().replace(/[^\d.]/g, ""));
                    
                    // حساب الكلمات
                    let wordCount = desc.trim().split(/\s+/).length;

                    // تحقق من وجود Attributes
                    let hasColor    = $row.find(".attr-color").data("has") === "1";
                    let hasSize     = $row.find(".attr-size").data("has") === "1";
                    let hasWeight   = $row.find(".attr-weight").data("has") === "1";
                    let hasCond     = $row.find(".attr-condition").data("has") === "1";

                    let attributesOK = hasColor && hasSize && hasWeight && hasCond;

                    // لو الشروط كلها أصبحت مكتملة → Pending
                    if (
                        name.length >= 3 &&
                        wordCount >= 20 &&
                        price > 0 &&
                        attributesOK
                    ) {
                        changeProductStatus(currentImageProd, "pending");
                    }
                }
            }

            // Fade out loader
            setTimeout(() => {
                $("#styliiiish-lottie-loader").fadeOut(200);
                $("#styliiiish-images-list").css("opacity", "1");
            }, 500);
        },
        error: function () {
            Swal.fire("Error", "Upload request failed", "error");
            $("#styliiiish-lottie-loader").hide();
            $("#styliiiish-images-list").css("opacity", "1");
        }
    });
});



/* ============================================================
   OPEN IMAGE MODAL
============================================================ */

$(document).on("click", ".styliiiish-image-cell", function () {
    currentImageProd = $(this).data("id");
    if (!currentImageProd) return;

    sendRequest(
        "styliiiish_get_images",
        { product_id: currentImageProd },
        function(response) {
            $("#styliiiish-images-list").html(response.data.html);
            $("#styliiiishImageModal").css("display", "flex");
        }
    );
});

$("#styliiiish-close-image-modal").on("click", function () {
    $("#styliiiishImageModal").hide();
});

/* ============================================================
   SET MAIN IMAGE
============================================================ */

$(document).on("click", ".styliiiish-set-main", function (e) {
    e.preventDefault();
    const attachID = $(this).data("attachment");
    if (!currentImageProd || !attachID) return;

    sendRequest(
        "styliiiish_set_featured_image",
        {
            product_id: currentImageProd,
            attachment_id: attachID
        },
        function(response) {
            $("#styliiiish-images-list").html(response.data.html);
            updateImageRow(currentImageProd, response.data.main);
        }
    );
});

/* ============================================================
   REMOVE IMAGE
============================================================ */

$(document).on("click", ".styliiiish-remove-image", function (e) {
    e.preventDefault();
    const attachID = $(this).data("attachment");
    if (!currentImageProd || !attachID) return;

    sendRequest(
        "styliiiish_remove_image",
        {
            product_id: currentImageProd,
            attachment_id: attachID
        },
        function(response) {

            // تحديث قائمة الصور في المودال
            $("#styliiiish-images-list").html(response.data.html);

            // تحديث الصورة الرئيسية في الجدول
            updateImageRow(currentImageProd, response.data.main);

            // ⭐⭐⭐ الشرط الصحيح:
            // لو response.data.main = null أو فاضي → مفيش صورة رئيسية
            if (!response.data.main || response.data.main.trim() === "") {

                // فقط للمستخدم العادي، وليس الـ Owner
                if (ajax_object.mode === "user") {
                    changeProductStatus(currentImageProd, "uncomplete");
                }
            }
        }
    );
});








// Hide 'Media Library' tab for users
// Force-hide media library tab for users












































function checkBeforeSave(field, value, el) {

    // نشتغل فقط على المنتجات Active
    let statusText = el.closest("tr").find(".sty-status").text().trim();
    if (!statusText.includes("Active")) {
        return Promise.resolve(true);
    }

    // =========================
    // 1) DESCRIPTION CHECK
    // =========================
    if (field === "post_content" || field === "description") {
        let wordCount = value.trim().split(/\s+/).length;

        if (wordCount < 20) {
            return Swal.fire({
               title: "Description Too Short",
                title: "Description Too Short",
                html: `
                    To keep your product active and visible to buyers, the description must be at least <b>20 words</b>.<br><br>
                    If you continue, the product will switch to <b>Incomplete</b> status, be hidden from customers, and will require review again once updated.
                `,
                icon: "warning",
                customClass: {
                popup: "sty-alert",
                confirmButton: "sty-btn-primary",
                cancelButton: "sty-btn-secondary",
            },
                showCancelButton: true,
                confirmButtonText: "Continue Anyway",
                cancelButtonText: "Continue Editing"
            }).then(r => r.isConfirmed);
        }
    }

    // =========================
    // 2) NAME CHECK
    // =========================
    if (field === "title" && value.trim().length < 3) {
        return Swal.fire({
            title: "Name Too Short",
                html: `
                    To keep your product active and visible to buyers, the name must be clear and contain at least <b>3 characters</b>.<br><br>
                    If you continue, the product will switch to <b>Incomplete</b> status, be hidden from customers, and will require review again once the name is properly updated.
                `,
            icon: "warning",
            customClass: {
            popup: "sty-alert",
            confirmButton: "sty-btn-primary",
            cancelButton: "sty-btn-secondary",
        },
            showCancelButton: true,
            confirmButtonText: "Continue Anyway",
            cancelButtonText: "Continue Editing"
        }).then(r => r.isConfirmed);
    }

    // =========================
    // 3) PRICE CHECK
    // =========================
    let numericPrice = parseFloat(value.replace(/[^\d.]/g, ''));

    if (field === "price" && (!numericPrice || numericPrice <= 0)) {
        return Swal.fire({
            title: "Invalid Price",
                html: `
                    To remain active and visible to buyers, your product must have a valid price greater than <b>0</b>.<br><br>
                    If you proceed, the product will be marked as <b>Incomplete</b>, removed from customer view, and will need to be reviewed again after you update the price.
                `,
            icon: "warning",
            customClass: {
            popup: "sty-alert",
            confirmButton: "sty-btn-primary",
            cancelButton: "sty-btn-secondary",
        },
            showCancelButton: true,
            confirmButtonText: "Continue Anyway",
            cancelButtonText: "Continue Editing"
        }).then(r => r.isConfirmed);
    }

    // =========================
    // 4) IMAGE CHECK (if removed)
    // =========================
    if (field === "image_removed") {  // لو هتعملها لاحقاً
        return Swal.fire({
            title: "Main Image Removed",
                html: `
                    To keep your product active and visible to buyers, a clear main image is required.<br><br>
                    If you continue, the product will switch to <b>Incomplete</b> status, be hidden from customers, and will require review again once a new main image is added.
                `,
            icon: "warning",
            customClass: {
            popup: "sty-alert",
            confirmButton: "sty-btn-primary",
            cancelButton: "sty-btn-secondary",
        },
            showCancelButton: true,
            confirmButtonText: "Continue Anyway",
            cancelButtonText: "Upload Image Now"
        }).then(r => r.isConfirmed);
    }

    // Passed ✓
    return Promise.resolve(true);
}




/* ============================
   INLINE EDIT — TITLE / PRICE / DESCRIPTION
============================ */

$(document).on("blur", ".inline-edit", function () {

    let el     = $(this);
    let id     = el.data("id");
    let field  = el.data("field");
    let value  = el.text().trim();

    if (!id || !field) return;

    // ========== PRE-SAVE VALIDATION ==========
    checkBeforeSave(field, value, el).then(allowed => {

        if (!allowed) {
            el.focus();
            return;
        }

        // ========== AJAX SAVE ==========
        $.post(ajax_object.ajax_url, {
            action: "styliiiish_quick_update_product",
            product_id: id,
            field: field,
            value: value
        }, function (response) {

            if (!response || response.success !== true) {
                Swal.fire("Error", (response?.data?.message || "Update failed"), "error");
                return;
            }

            // DESCRIPTION FIX
            if (field === "post_content" || field === "description") {
                el.text(response.short || response.data?.short);
                el.attr("data-full", response.full || response.data?.full);
            } else {
                el.text(response.value || response.data?.value || value);
            }

            // Highlight save
            el.css({
                background: "#d4ffd4",
                transition: "0.3s"
            });

            setTimeout(() => {
                el.css("background", "transparent");
            }, 500);

            // Auto pending check
            $.post(ajax_object.ajax_url, {
                action: "styliiiish_trigger_pending_check",
                product_id: id
            });

        }, "json");

    });

});



























































$(document).on("change", "#styliiiish-filter-status-user", function () {

    let chosen = $(this).val();

    if (chosen === "publish") {
        currentFilters.status = "active";
    }
    else if (chosen === "pending") {
        currentFilters.status = "pending";
    }
    else if (chosen === "draft") {
        currentFilters.status = "uncomplete";
    }
    else if (chosen === "deactivated") {
        currentFilters.status = "deactivated";
    }
    else {
        currentFilters.status = "";
    }

    loadManageProductsPage(1);  // 🔥 نرسل page فقط
});








// User Deactivate Product
$(document).on('click', '.btn-deactivate-user', function (e) {
    e.preventDefault();

    const $btn = $(this);
    const id   = $btn.data('id');
    if (!id) return;

    Swal.fire({
        title: 'Deactivate this dress?',
        text: 'It will stop appearing to customers until you activate it again.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, deactivate',
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.post(ajax_object.ajax_url, {
            action: 'styliiiish_user_deactivate_product',
            product_id: id
        }, function (res) {

            if (!res || !res.success) {
                Swal.fire('Error', res?.data?.message || 'Deactivation failed.', 'error');
                return;
            }

            const $row = $('tr[data-row-id="' + id + '"]');
            $row.find('td[data-label="Status"] span').first().replaceWith(res.data.status_html);

            // حوّل الزر لـ Activate
            $btn
                .removeClass('btn-deactivate-user')
                .addClass('btn-activate-user')
                .text('Activate');

            Swal.fire('Done', 'Dress deactivated.', 'success');

        }, 'json');
    });
});





// User Activate Product
$(document).on('click', '.btn-activate-user', function (e) {
    e.preventDefault();

    const $btn = $(this);
    const id   = $btn.data('id');
    if (!id) return;

    Swal.fire({
        title: 'Send for review?',
        text: 'Your dress will be checked. If complete → Pending, if not → Uncomplete.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, send',
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.post(ajax_object.ajax_url, {
            action: 'styliiiish_user_activate_product',
            product_id: id
        }, function (res) {

            if (!res || !res.success) {
                Swal.fire('Error', res?.data?.message || 'Activation failed.', 'error');
                return;
            }

            const $row = $('tr[data-row-id="' + id + '"]');
            
            // ✔ تحديث شكل الحالة
            $row.find('td[data-label="Status"] span')
                .first()
                .replaceWith(res.data.status_html);

            // ✔ لو المنتج جه Pending
            if (res.data.status === 'pending') {

                Swal.fire('Sent!', 'Your dress is now Pending review.', 'success');

                // بقى Active؟ نخلي الزر Deactivate
                $btn
                    .removeClass('btn-activate-user')
                    .addClass('btn-deactivate-user')
                    .text('Deactivate');

            } 
            // ✔ لو رجع Draft → ناقص حاجة
            else if (res.data.status === 'draft') {
                
                Swal.fire('Incomplete', 'Some required fields are missing. Please complete them first.', 'warning');
                
                // ما نخليش الزر يروح Deactivate، لأنه مش Active أصلاً
                // نخليه زي ما هو (Activate)
            }

        }, 'json');

    });
});




























function changeProductStatus(productID, newStatus) {


    console.log("Changing status for:", productID, "→", newStatus);
    $.post(ajax_object.ajax_url, {
        action: "styliiiish_update_status",
        product_id: productID,
        status: newStatus
    }, function(response) {

        if (response && response.success) {

            // تحديث الحالة داخل الجدول
            let row = $(`tr[data-row-id="${productID}"]`);
            let label = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

            row.find(".sty-status").text(label);

            // تحديث CSS class
            row.removeClass("status-active status-pending status-uncomplete")
               .addClass("status-" + newStatus);

        } else {
            Swal.fire("Error", response?.data?.message || "Unable to update status.", "error");
        }

    }, "json");
}























    });
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    