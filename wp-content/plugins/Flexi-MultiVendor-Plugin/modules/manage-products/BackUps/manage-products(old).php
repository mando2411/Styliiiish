<?php
/*
    MANAGE PRODUCTS PAGE (Owner Dashboard)
    SPA Style • AJAX • SweetAlert2 • Inline Editing • Filters • Bulk • Image Modal
*/







/* ===============================
   MAIN PAGE RENDER
================================== */
function styliiiish_render_manage_products()
{
    if (!current_user_can('manage_woocommerce')) {
        echo 'No permission';
        return;
    }

    $paged = 1;
    ?>
                    </br>
           <button type="button" class="button button-primary" id="styliiiish-add-product">
                + Add New Product
            </button>
                        </br></br>





    <div class="styliiiish-toolbar">
        <div class="styliiiish-filters-group">
            <input type="text" id="styliiiish-search" class="regular-text" placeholder="Search products...">
            <?php
            wp_dropdown_categories([
                'show_option_all' => 'All categories',
                'taxonomy'        => 'product_cat',
                'name'            => 'styliiiish_filter_cat',
                'id'              => 'styliiiish-filter-cat',
                'hide_empty'      => 0,
                'value_field'     => 'term_id',
            ]);
            ?>
            <select id="styliiiish-filter-status">
                <option value="">All statuses</option>
                <option value="publish">Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>

        <div class="styliiiish-bulk-group">
            <select id="styliiiish-bulk-action">
                <option value="">Bulk actions</option>
                <option value="delete">Delete</option>
                <option value="publish">Set to Published</option>
                <option value="draft">Set to Draft</option>

            </select>
            <button type="button" class="button" id="styliiiish-bulk-apply">Apply</button>
        </div>
    </div>

    <div id="styliiiish-manage-products-content">
        <?php echo styliiiish_build_manage_products_content($paged); ?>
    </div>

    <!-- Categories Modal -->
    <div id="editCatsModal">
        <div class="cats-modal-box">
            <h3>Edit Categories</h3>
            <div id="cats-checkboxes"></div>
            <div class="cats-btn-row">
                <button id="saveCatsBtn" class="button button-primary">Save</button>
                <button id="closeCatsBtn" class="button">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="styliiiishImageModal">
        <div class="image-modal-box">
            <h3>Manage Images</h3>
            <div id="styliiiish-images-list" style="flex:1;overflow:auto;max-height:65vh;"></div>
            <div class="image-btn-row">
                <button id="styliiiish-add-image" class="button button-primary">Add / Change Image</button>
                <button id="styliiiish-close-image-modal" class="button">Close</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <?php
}














?>
