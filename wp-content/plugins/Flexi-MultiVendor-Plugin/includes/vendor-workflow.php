<?php
/**
 * Aswaq Taj – Vendor Roles & Workflow
 */
error_log( 'TAJ VENDOR WORKFLOW LOADED' );

/*--------------------------------------------------------------
# 1️⃣ Register Roles (صلاحيات صحيحة)
--------------------------------------------------------------*/
add_action( 'init', function () {

    // Vendor ACTIVE
    if ( ! get_role( 'taj_vendor' ) ) {
        add_role(
            'taj_vendor',
            'Vendor',
            [
                'read' => true,

                // WooCommerce
                'edit_products'            => true,
                'publish_products'        => true,
                'edit_published_products' => true,
                'delete_products'         => true,

                'read_shop_orders'        => true,
                'edit_shop_orders'        => true,
            ]
        );
    }

    // Vendor PENDING (عرض فقط)
    if ( ! get_role( 'taj_vendor_pending' ) ) {
        add_role(
            'taj_vendor_pending',
            'Vendor (Pending)',
            [
                'read' => true,
            ]
        );
    }

    // Vendor SUSPENDED (عرض فقط)
    if ( ! get_role( 'taj_vendor_suspended' ) ) {
        add_role(
            'taj_vendor_suspended',
            'Vendor (Suspended)',
            [
                'read' => true,
            ]
        );
    }

});

/*--------------------------------------------------------------
# 2️⃣ Register Endpoints
--------------------------------------------------------------*/
add_action( 'init', function () {

    add_rewrite_endpoint( 'register-vendor', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'vendor-dashboard', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'vendor-status', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'vendor-suspended', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'moderate-site', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'vendor_orders', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'store-profile', EP_ROOT | EP_PAGES );

});

/*--------------------------------------------------------------
# 3️⃣ Tabs Logic (My Account)
--------------------------------------------------------------*/
add_filter( 'woocommerce_account_menu_items', function ( $items ) {

    if ( ! is_user_logged_in() ) {
        return $items;
    }

    $roles = (array) wp_get_current_user()->roles;

    // Labels (Translation Ready)
    // 'vendor_details' removed
    $labels = [
        'store-profile'   => __( 'Store Profile', 'website-flexi' ),
        'vendor_orders'   => __( 'Store Orders', 'website-flexi' ),
        'vendor_dashboard' => __( 'My Products', 'website-flexi' ),
        'vendor_status'    => __( 'Application Under Review', 'website-flexi' ),
        'vendor_suspended' => __( 'Store Suspended', 'website-flexi' ),
        'register_vendor'  => __( 'Register as Vendor', 'website-flexi' ),
        'moderate_site'    => __( 'Moderate Your Site', 'website-flexi' ),
    ];

    // Admin override
    if ( in_array('administrator', $roles, true) ) {
        $vendor_tabs = [
            'moderate-site' => $labels['moderate_site'],
        ];
    } else {
        // Vendor tabs
        $vendor_tabs = [];

        if ( in_array( 'taj_vendor', $roles, true ) ) {

            $vendor_tabs = [
                'store-profile'    => $labels['store-profile'],
                'vendor_orders'    => $labels['vendor_orders'],
                'vendor-dashboard' => $labels['vendor_dashboard'],

            ];

        } elseif ( in_array( 'taj_vendor_pending', $roles, true ) ) {

            $vendor_tabs = [
                'vendor-status' => $labels['vendor_status'],
            ];

        } elseif ( in_array( 'taj_vendor_suspended', $roles, true ) ) {

            $vendor_tabs = [
                'vendor-suspended' => $labels['vendor_suspended'],
            ];

        } else {

            $vendor_tabs = [
                'register-vendor' => $labels['register_vendor'],
            ];
        }
    }

   // Rebuild menu without breaking logout
        $new_items = [];
        
        // Get wallet if exists
        $wallet = [];
        
        if ( isset( $items['woo-wallet'] ) ) {
            $wallet['woo-wallet'] = $items['woo-wallet'];
            unset( $items['woo-wallet'] );
        }
        
        foreach ( $items as $key => $label ) {
        
            // Before logout
            if ( $key === 'customer-logout' ) {
        
                // Add vendor tabs first
                foreach ( $vendor_tabs as $v_key => $v_label ) {
                    $new_items[ $v_key ] = $v_label;
                }
        
                // Then wallet
                if ( ! empty( $wallet ) ) {
                    $new_items = array_merge( $new_items, $wallet );
                }
            }
        
            // Keep original item
            $new_items[ $key ] = $label;
        }
        
        return $new_items;



}, 99 );


// Hide Downloads tab based on setting
add_filter( 'woocommerce_account_menu_items', 'wf_hide_downloads_by_setting', 999 );

function wf_hide_downloads_by_setting( $items ) {

    if ( get_option( 'websiteflexi_disable_downloads', 'no' ) === 'yes' ) {

        unset( $items['downloads'] );

    }

    return $items;
}


/*--------------------------------------------------------------
# 4️⃣ Endpoint Protection (ACL حقيقي)
--------------------------------------------------------------*/
add_action( 'template_redirect', function () {

    if ( ! is_user_logged_in() || ! is_account_page() ) {
        return;
    }

    $roles = (array) wp_get_current_user()->roles;

    $is_vendor    = in_array( 'taj_vendor', $roles, true );
    $is_pending   = in_array( 'taj_vendor_pending', $roles, true );
    $is_suspended = in_array( 'taj_vendor_suspended', $roles, true );

    $deny = function () {
        wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
        exit;
    };

    if ( get_query_var( 'vendor-dashboard', false ) !== false
      || get_query_var( 'wallet', false ) !== false
      || get_query_var( 'withdraw', false ) !== false
    ) {
        if ( ! $is_vendor ) $deny();
    }

    if ( get_query_var( 'vendor-status', false ) !== false ) {
        if ( ! $is_pending ) $deny();
    }

    if ( get_query_var( 'vendor-suspended', false ) !== false ) {
        if ( ! $is_suspended ) $deny();
    }

    if ( get_query_var( 'register-vendor', false ) !== false ) {
        if ( $is_vendor || $is_pending || $is_suspended ) $deny();
    }

}, 1 );








/*--------------------------------------------------------------
# 5️⃣ Endpoint Content
--------------------------------------------------------------*/
add_action( 'woocommerce_account_vendor-status_endpoint', function () {
    echo '<p>' . __( 'Your vendor application is under review. You will be contacted soon.', 'website-flexi' ) . '</p>';
});

add_action( 'woocommerce_account_vendor-suspended_endpoint', function () {
    echo '<p>' . __( 'Your store has been temporarily suspended. Please contact support.', 'website-flexi' ) . '</p>';
});


add_action(
    'woocommerce_account_vendor-dashboard_endpoint',
    'aswaq_taj_vendor_dashboard_content'
);

function aswaq_taj_vendor_dashboard_content() {

    // حماية
    $user = wp_get_current_user();

    if (!in_array('taj_vendor', (array) $user->roles, true)) {
        wp_safe_redirect(wc_get_page_permalink('myaccount'));
        exit;
    }



    /* ===============================
       المحتوى
    =============================== */

    echo '<div class="taj-vendor-dashboard">';

    echo do_shortcode('[styliiiish_user_manage_products]');

    echo '</div>';
}



// 2️⃣ محتوى التبويب Admin
add_action('woocommerce_account_moderate-site_endpoint', function() {
    $user = wp_get_current_user();
    if (!in_array('administrator', (array)$user->roles, true)) {
        wp_safe_redirect(wc_get_page_permalink('myaccount'));
        exit;
    }
    echo do_shortcode('[owner_dashboard]');
});



add_action( 'woocommerce_account_register-vendor_endpoint', function () {

    $user = wp_get_current_user();

    if ( array_intersect( ['taj_vendor','taj_vendor_pending','taj_vendor_suspended'], $user->roles ) ) {
        echo '<p>' . __( 'You cannot apply again.', 'website-flexi' ) . '</p>';
        return;
    }

    $kyc_enabled = get_option( 'wf_enable_kyc', 'yes' ) === 'yes';

    if ( ! $kyc_enabled ) {
        ?>
        <h2 class="taj-page-title"><?php _e( 'Become a Vendor', 'website-flexi' ); ?></h2>
        <p class="taj-page-subtitle"><?php _e( 'KYC verification is currently disabled. You can activate your vendor account directly.', 'website-flexi' ); ?></p>

        <div class="taj-card">
            <form method="post" action="<?php echo esc_url( wc_get_account_endpoint_url( 'register-vendor' ) ); ?>" class="taj-vendor-form">
                <?php wp_nonce_field( 'taj_vendor_quick_apply', 'taj_vendor_quick_apply_nonce' ); ?>
                <input type="hidden" name="taj_vendor_quick_apply" value="1">

                <div class="taj-actions">
                    <button type="submit" class="button button-primary taj-submit">
                        <?php _e( 'Activate Vendor Account', 'website-flexi' ); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
        return;
    }
    ?>

<h2 class="taj-page-title"><?php _e( 'Register as a Vendor', 'website-flexi' ); ?></h2>
<p class="taj-page-subtitle"><?php _e( 'Fill in the details below. Your application will be reviewed before approval.', 'website-flexi' ); ?></p>

<div class="taj-card">
  <form method="post"
        action="<?php echo esc_url( wc_get_account_endpoint_url( 'register-vendor' ) ); ?>"
        enctype="multipart/form-data"
        class="taj-vendor-form">

      <?php wp_nonce_field( 'taj_vendor_apply', 'taj_vendor_nonce' ); ?>
      <input type="hidden" name="taj_vendor_apply" value="1">

      <!-- =======================
           SECTION: Basic Info
      ======================== -->
      <div class="taj-section">
        <div class="taj-section-head">
          <h3><?php _e( 'Basic Information', 'website-flexi' ); ?></h3>
          <p><?php _e( 'Store details shown to customers after approval.', 'website-flexi' ); ?></p>
        </div>

        <div class="taj-grid">
          <div class="taj-field">
            <label><?php _e( 'Store Name', 'website-flexi' ); ?></label>
            <input type="text" name="store_name" required>
          </div>

          <div class="taj-field taj-field-full">
            <label><?php _e( 'Store Description', 'website-flexi' ); ?></label>
            <textarea name="store_description" rows="4" required></textarea>
          </div>

          <div class="taj-field">
            <label><?php _e( 'Phone Number (Call)', 'website-flexi' ); ?></label>
            <input type="text" name="phone_call" required>
          </div>

          <div class="taj-field">
            <label><?php _e( 'WhatsApp Number', 'website-flexi' ); ?></label>
            <input type="text" name="phone_whatsapp" required>
          </div>

          <div class="taj-field taj-field-full">
            <label><?php _e( 'Current Working Address', 'website-flexi' ); ?></label>
            <textarea name="current_address" rows="3" required></textarea>
          </div>
        </div>
      </div>

      <!-- =======================
           SECTION: Business Type
      ======================== -->
      <div class="taj-section">
        <div class="taj-section-head">
          <h3><?php _e( 'Business Type', 'website-flexi' ); ?></h3>
          <p><?php _e( 'This helps us verify your business and set pickup details.', 'website-flexi' ); ?></p>
        </div>

        <div class="taj-grid">
          <div class="taj-field">
            <label><?php _e( 'Select Business Type', 'website-flexi' ); ?></label>
            <select name="business_type" id="business_type" required>
              <option value=""><?php _e( 'Select Business Type', 'website-flexi' ); ?></option>
              <option value="home"><?php _e( 'Work From Home', 'website-flexi' ); ?></option>
              <option value="shop"><?php _e( 'I Have a Shop', 'website-flexi' ); ?></option>
              <option value="company"><?php _e( 'I Have a Company', 'website-flexi' ); ?></option>
              <option value="office"><?php _e( 'I Have an Office', 'website-flexi' ); ?></option>
            </select>
          </div>
        </div>

        <div id="home_notice" class="taj-alert" style="display:none;">
          <strong>⚠️</strong>
          <span><?php _e( 'This working address will be used as the pickup location for shipping orders.', 'website-flexi' ); ?></span>
        </div>

        <div id="business_fields" style="display:none;">
          <div class="taj-divider"></div>

          <div class="taj-section-mini">
            <h4><?php _e( 'Business Documents', 'website-flexi' ); ?></h4>
            <p><?php _e( 'Optional for home businesses. Required for shops/companies/offices.', 'website-flexi' ); ?></p>
          </div>

          <div class="taj-grid">
            <!-- Commercial Register -->
            <div class="taj-field taj-file-field">
              <label><?php _e( 'Commercial Register', 'website-flexi' ); ?></label>

              <input type="file" name="commercial_register" id="commercial_register" accept="image/*" hidden>

              <div class="taj-file-ui">
                <button type="button" class="button taj-file-btn" data-target="commercial_register">
                  <?php _e( 'Choose File', 'website-flexi' ); ?>
                </button>

                <span class="taj-file-name"
                      data-empty="<?php echo esc_attr__( 'No file chosen', 'website-flexi' ); ?>">
                  <?php _e( 'No file chosen', 'website-flexi' ); ?>
                </span>

                <button type="button" class="button taj-file-remove" data-target="commercial_register">
                  <?php _e( 'Remove', 'website-flexi' ); ?>
                </button>
              </div>

              <div class="taj-file-preview"></div>
            </div>

            <!-- Tax Card -->
            <div class="taj-field taj-file-field">
              <label><?php _e( 'Tax Card', 'website-flexi' ); ?></label>

              <input type="file" name="tax_card" id="tax_card" accept="image/*" hidden>

              <div class="taj-file-ui">
                <button type="button" class="button taj-file-btn" data-target="tax_card">
                  <?php _e( 'Choose File', 'website-flexi' ); ?>
                </button>

                <span class="taj-file-name"
                      data-empty="<?php echo esc_attr__( 'No file chosen', 'website-flexi' ); ?>">
                  <?php _e( 'No file chosen', 'website-flexi' ); ?>
                </span>

                <button type="button" class="button taj-file-remove" data-target="tax_card">
                  <?php _e( 'Remove', 'website-flexi' ); ?>
                </button>
              </div>

              <div class="taj-file-preview"></div>
            </div>

            <div class="taj-field taj-field-full">
              <label><?php _e( 'Business Address', 'website-flexi' ); ?></label>
              <textarea name="business_address" rows="3"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- =======================
           SECTION: Identity Verification
      ======================== -->
      <div class="taj-section">
        <div class="taj-section-head">
          <h3><?php _e( 'Identity Verification', 'website-flexi' ); ?></h3>
          <p><?php _e( 'Upload clear images. Make sure all details are readable.', 'website-flexi' ); ?></p>
        </div>

        <div class="taj-grid">
          <!-- ID FRONT -->
          <div class="taj-field taj-file-field">
            <label><?php _e( 'ID Card (Front)', 'website-flexi' ); ?></label>

            <input type="file" name="id_front" id="id_front" accept="image/*" hidden required>

            <div class="taj-file-ui">
              <button type="button" class="button taj-file-btn" data-target="id_front">
                <?php _e( 'Choose File', 'website-flexi' ); ?>
              </button>

              <span class="taj-file-name"
                    data-empty="<?php echo esc_attr__( 'No file chosen', 'website-flexi' ); ?>">
                <?php _e( 'No file chosen', 'website-flexi' ); ?>
              </span>

              <button type="button" class="button taj-file-remove" data-target="id_front">
                <?php _e( 'Remove', 'website-flexi' ); ?>
              </button>
            </div>

            <div class="taj-file-preview"></div>
          </div>

          <!-- ID BACK -->
          <div class="taj-field taj-file-field">
            <label><?php _e( 'ID Card (Back)', 'website-flexi' ); ?></label>

            <input type="file" name="id_back" id="id_back" accept="image/*" hidden required>

            <div class="taj-file-ui">
              <button type="button" class="button taj-file-btn" data-target="id_back">
                <?php _e( 'Choose File', 'website-flexi' ); ?>
              </button>

              <span class="taj-file-name"
                    data-empty="<?php echo esc_attr__( 'No file chosen', 'website-flexi' ); ?>">
                <?php _e( 'No file chosen', 'website-flexi' ); ?>
              </span>

              <button type="button" class="button taj-file-remove" data-target="id_back">
                <?php _e( 'Remove', 'website-flexi' ); ?>
              </button>
            </div>

            <div class="taj-file-preview"></div>
          </div>

          <!-- UTILITY BILL -->
          <div class="taj-field taj-file-field">
            <label><?php _e( 'Latest Electricity or Gas Bill', 'website-flexi' ); ?></label>

            <input type="file" name="utility_bill" id="utility_bill" accept="image/*" hidden required>

            <div class="taj-file-ui">
              <button type="button" class="button taj-file-btn" data-target="utility_bill">
                <?php _e( 'Choose File', 'website-flexi' ); ?>
              </button>

              <span class="taj-file-name"
                    data-empty="<?php echo esc_attr__( 'No file chosen', 'website-flexi' ); ?>">
                <?php _e( 'No file chosen', 'website-flexi' ); ?>
              </span>

              <button type="button" class="button taj-file-remove" data-target="utility_bill">
                <?php _e( 'Remove', 'website-flexi' ); ?>
              </button>
            </div>

            <div class="taj-file-preview"></div>
          </div>
        </div>
      </div>

      <div class="taj-actions">
        <button type="submit" class="button button-primary taj-submit">
          <?php _e( 'Submit Vendor Application', 'website-flexi' ); ?>
        </button>
        <p class="taj-note">
          <?php _e( 'By submitting, you confirm the information is correct. We may contact you for verification.', 'website-flexi' ); ?>
        </p>
      </div>

  </form>
</div>


    <script>
    document.getElementById('business_type').addEventListener('change', function () {
        const businessFields = document.getElementById('business_fields');
        const homeNotice = document.getElementById('home_notice');

        if (this.value === 'home') {
            homeNotice.style.display = 'block';
            businessFields.style.display = 'none';
        } else if (this.value) {
            homeNotice.style.display = 'none';
            businessFields.style.display = 'block';
        } else {
            homeNotice.style.display = 'none';
            businessFields.style.display = 'none';
        }
    });
    </script>

<?php
});








add_action( 'wp_loaded', 'taj_handle_vendor_application' );
function taj_handle_vendor_application() {

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['taj_vendor_quick_apply'] ) ) {

        if (
            ! isset( $_POST['taj_vendor_quick_apply_nonce'] ) ||
            ! wp_verify_nonce( $_POST['taj_vendor_quick_apply_nonce'], 'taj_vendor_quick_apply' )
        ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        $kyc_enabled = get_option( 'wf_enable_kyc', 'yes' ) === 'yes';
        if ( $kyc_enabled ) {
            return;
        }

        $user_id = get_current_user_id();
        $user    = wp_get_current_user();

        if ( array_intersect( [ 'taj_vendor', 'taj_vendor_pending', 'taj_vendor_suspended' ], (array) $user->roles ) ) {
            wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        }

        ( new WP_User( $user_id ) )->set_role( 'taj_vendor' );
        update_user_meta( $user_id, 'taj_kyc_status', 'approved' );
        update_user_meta( $user_id, 'taj_vendor_approved_at', current_time( 'mysql' ) );

        wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
        exit;
    }

    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) return;
    if ( empty( $_POST['taj_vendor_apply'] ) ) return;

    error_log( 'TAJ FORM SUBMITTED' );

    if (
        ! isset( $_POST['taj_vendor_nonce'] ) ||
        ! wp_verify_nonce( $_POST['taj_vendor_nonce'], 'taj_vendor_apply' )
    ) {
        error_log( 'TAJ NONCE FAILED' );
        return;
    }

    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();

    /* ===== حفظ البيانات ===== */
    update_user_meta( $user_id, 'taj_store_name', sanitize_text_field( $_POST['store_name'] ) );
    update_user_meta( $user_id, 'taj_store_description', sanitize_textarea_field($_POST['store_description'] ?? ''));
    update_user_meta( $user_id, 'taj_phone_call', sanitize_text_field( $_POST['phone_call'] ) );
    update_user_meta( $user_id, 'taj_phone_whatsapp', sanitize_text_field( $_POST['phone_whatsapp'] ) );
    update_user_meta( $user_id, 'taj_current_address', sanitize_textarea_field( $_POST['current_address'] ) );
    update_user_meta( $user_id, 'taj_business_type', sanitize_text_field( $_POST['business_type'] ) );
    update_user_meta(
        $user_id,
        'taj_business_address',
        sanitize_textarea_field( $_POST['business_address'] ?? '' )
    );

    /* ===== رفع الملفات ===== */
    require_once ABSPATH . 'wp-admin/includes/file.php';

    foreach ( [
        'id_front',
        'id_back',
        'utility_bill',
        'commercial_register',
        'tax_card'
    ] as $file_key ) {

        if ( empty( $_FILES[ $file_key ]['name'] ) ) continue;

        $upload = wp_handle_upload(
            $_FILES[ $file_key ],
            [ 'test_form' => false ]
        );

        if ( empty( $upload['error'] ) ) {
            update_user_meta(
                $user_id,
                'taj_' . $file_key,
                esc_url_raw( $upload['url'] )
            );
        }
    }

    /* ===== تحويل الدور ===== */
    ( new WP_User( $user_id ) )->set_role( 'taj_vendor_pending' );
    update_user_meta( $user_id, 'taj_vendor_applied_at', current_time( 'mysql' ) );

    wp_mail(
        get_option( 'admin_email' ),
        'Vendor Application Submitted – Aswaq Taj',
        'New vendor application awaiting review.'
    );

    wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
    exit;
}
























































add_action( 'admin_menu', function () {
    add_users_page(
        'Vendor Applications',
        'Vendor Applications',
        'manage_options',
        'taj-vendor-applications',
        'taj_render_vendor_applications_page'
    );
});
function taj_render_vendor_applications_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $vendors = get_users([
        'role' => 'taj_vendor_pending',
        'orderby' => 'registered',
        'order' => 'DESC',
    ]);
    ?>

    <div class="wrap">
        <h1>Vendor Applications (KYC Review)</h1>

        <?php if ( empty( $vendors ) ) : ?>
            <p>No pending applications.</p>
        <?php else : ?>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Store Name</th>
                    <th>Business Type</th>
                    <th>Phone</th>
                    <th>Applied At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ( $vendors as $vendor ) : 
                $uid = $vendor->ID;
            ?>
                <tr>
                    <td><?php echo esc_html( $vendor->user_email ); ?></td>
                    <td><?php echo esc_html( get_user_meta( $uid, 'taj_store_name', true ) ); ?></td>
                    <td><?php echo esc_html( get_user_meta( $uid, 'taj_business_type', true ) ); ?></td>
                    <td><?php echo esc_html( get_user_meta( $uid, 'taj_phone_call', true ) ); ?></td>
                    <td><?php echo esc_html( get_user_meta( $uid, 'taj_vendor_applied_at', true ) ); ?></td>
                    <td>
                        <a href="<?php echo admin_url( 'users.php?page=taj-vendor-applications&view=' . $uid ); ?>" class="button">Review</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <?php endif; ?>
    </div>
    <?php

    // عرض صفحة المراجعة الفردية
    if ( isset( $_GET['view'] ) ) {
        taj_render_vendor_single_review( intval( $_GET['view'] ) );
    }
}
function taj_render_vendor_single_review( $user_id ) {

    $user = get_user_by( 'id', $user_id );
    if ( ! $user ) return;

    $fields = [
        'taj_store_name'        => 'Store Name',
        'taj_phone_call'        => 'Phone (Call)',
        'taj_phone_whatsapp'    => 'WhatsApp',
        'taj_current_address'   => 'Current Address',
        'taj_business_type'     => 'Business Type',
        'taj_business_address'  => 'Business Address',
    ];

    $files = [
        'taj_id_front'           => 'ID Front',
        'taj_id_back'            => 'ID Back',
        'taj_utility_bill'       => 'Utility Bill',
        'taj_commercial_register'=> 'Commercial Register',
        'taj_tax_card'           => 'Tax Card',
    ];
    ?>

    <hr>
    <h2>Review Vendor Application</h2>

    <h3>Basic Info</h3>
    <table class="widefat">
        <?php foreach ( $fields as $key => $label ) : ?>
            <tr>
                <th><?php echo esc_html( $label ); ?></th>
                <td><?php echo esc_html( get_user_meta( $user_id, $key, true ) ); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>KYC Documents</h3>
    <table class="widefat">
        <?php foreach ( $files as $key => $label ) :
            $url = get_user_meta( $user_id, $key, true );
        ?>
            <tr>
                <th><?php echo esc_html( $label ); ?></th>
                <td>
                    <?php if ( $url ) : ?>
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank">View</a>
                    <?php else : ?>
                        Not provided
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <form method="post" style="margin-top:20px;">
        <?php wp_nonce_field( 'taj_vendor_review', 'taj_review_nonce' ); ?>
        <input type="hidden" name="vendor_id" value="<?php echo esc_attr( $user_id ); ?>">

        <textarea name="review_note" placeholder="Admin note (optional)" style="width:100%;margin-bottom:10px;"></textarea>

        <button name="taj_approve_vendor" class="button button-primary">Approve</button>
        <button name="taj_reject_vendor" class="button button-secondary">Reject</button>
    </form>
    <?php
}
add_action( 'admin_init', function () {

    if ( empty( $_POST['vendor_id'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['taj_review_nonce'], 'taj_vendor_review' ) ) {
        return;
    }

    $user_id = intval( $_POST['vendor_id'] );
    $note = sanitize_textarea_field( $_POST['review_note'] ?? '' );

    if ( isset( $_POST['taj_approve_vendor'] ) ) {

        ( new WP_User( $user_id ) )->set_role( 'taj_vendor' );
        update_user_meta( $user_id, 'taj_kyc_status', 'approved' );
        update_user_meta( $user_id, 'taj_kyc_note', $note );
        update_user_meta( $user_id, 'taj_vendor_approved_at', current_time( 'mysql' ) );

    } elseif ( isset( $_POST['taj_reject_vendor'] ) ) {

        ( new WP_User( $user_id ) )->set_role( 'taj_vendor_suspended' );
        update_user_meta( $user_id, 'taj_kyc_status', 'rejected' );
        update_user_meta( $user_id, 'taj_kyc_note', $note );
        update_user_meta( $user_id, 'taj_vendor_rejected_at', current_time( 'mysql' ) );
    }

    wp_redirect( admin_url( 'users.php?page=taj-vendor-applications' ) );
    exit;
});
function taj_log_kyc_action( $user_id, $action ) {
    add_user_meta( $user_id, 'taj_kyc_log', [
        'action' => $action,
        'admin'  => get_current_user_id(),
        'time'   => current_time( 'mysql' ),
    ] );
}







































add_action( 'woocommerce_account_vendor_orders_endpoint', 'wf_vendor_orders_content' );

function wf_vendor_orders_content() {

    if ( ! is_user_logged_in() ) {
        return;
    }

    echo do_shortcode( '[websiteflexi_vendor_orders]' );
}






