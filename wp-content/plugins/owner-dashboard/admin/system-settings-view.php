<?php
if ( ! defined('ABSPATH') ) {
    exit;
}
?>

<div class="wrap">
    <h1>WebsiteFlexi Owner Dashboard & Marketplace – Settings</h1>

    <?php if ( $message_key ): ?>
        <div class="notice notice-success is-dismissible" style="margin-top:15px;">
            <p>
                <?php
                switch ($message_key) {
                    case 'marketplace_saved':
                        echo 'Marketplace settings saved successfully.';
                        break;
                    case 'add_mode_saved':
                        echo 'Add Product Mode saved successfully.';
                        break;
                    case 'manager_added':
                        echo 'Manager added successfully.';
                        break;
                    case 'manager_removed':
                        echo 'Manager removed successfully.';
                        break;
                    case 'dashboard_user_added':
                        echo 'Dashboard user added successfully.';
                        break;
                    case 'dashboard_user_removed':
                        echo 'Dashboard user removed successfully.';
                        break;
                    default:
                        echo 'Settings saved.';
                        break;
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <h2 class="nav-tab-wrapper" style="margin-top:20px;">
        <a href="<?php echo esc_url( add_query_arg( array('page' => 'websiteflexi-system-settings', 'tab' => 'marketplace'), admin_url('plugins.php') ) ); ?>"
           class="nav-tab <?php echo ($active_tab === 'marketplace') ? 'nav-tab-active' : ''; ?>">
            Marketplace
        </a>

        <a href="<?php echo esc_url( add_query_arg( array('page' => 'websiteflexi-system-settings', 'tab' => 'add_product'), admin_url('plugins.php') ) ); ?>"
           class="nav-tab <?php echo ($active_tab === 'add_product') ? 'nav-tab-active' : ''; ?>">
            Add Product
        </a>

        <a href="<?php echo esc_url( add_query_arg( array('page' => 'websiteflexi-system-settings', 'tab' => 'managers'), admin_url('plugins.php') ) ); ?>"
           class="nav-tab <?php echo ($active_tab === 'managers') ? 'nav-tab-active' : ''; ?>">
            Managers
        </a>

        <a href="<?php echo esc_url( add_query_arg( array('page' => 'websiteflexi-system-settings', 'tab' => 'dashboard_access'), admin_url('plugins.php') ) ); ?>"
           class="nav-tab <?php echo ($active_tab === 'dashboard_access') ? 'nav-tab-active' : ''; ?>">
            Dashboard Access
        </a>
    </h2>

    <?php if ($active_tab === 'marketplace'): ?>

        <form method="post" style="margin-top:20px;">
            <?php wp_nonce_field('wf_save_marketplace_settings'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Enable Customer Marketplace</th>
                    <td>
                        <label>
                            <input type="checkbox"
                                   name="sty_mp_enable_marketplace"
                                   value="1"
                                   <?php checked( $marketplace_enabled, true ); ?> />
                            Allow customers to sell & manage their own dresses (Sell Your Dress system).
                        </label>
                        <p class="description">
                            When enabled, logged-in customers will see custom marketplace features (Sell Your Dress / Manage Products)
                            based on your implementation.
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="wf_save_marketplace_settings" class="button button-primary">
                    Save Marketplace Settings
                </button>
            </p>
        </form>

    <?php elseif ($active_tab === 'add_product'): ?>

        <form method="post" style="margin-top:20px;">
            <?php wp_nonce_field('styliiiish_save_add_product_mode'); ?>

            <table class="form-table" style="max-width:500px;">
                <tr>
                    <th scope="row"><label for="styliiiish_add_product_mode">Add Product Mode</label></th>
                    <td>
                        <select name="styliiiish_add_product_mode" id="styliiiish_add_product_mode">
                            <option value="ajax" <?php selected($add_product_mode, 'ajax'); ?>>
                                AJAX – Duplicate Template (Recommended)
                            </option>
                            <option value="old" <?php selected($add_product_mode, 'old'); ?>>
                                OLD Method – admin_post (Legacy)
                            </option>
                        </select>
                        <p class="description">
                            اختر طريقة إنشاء المنتج الجديد فى Owner Dashboard.
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="styliiiish_save_add_product_mode_btn" class="button button-primary">
                    Save Add Product Settings
                </button>
            </p>
        </form>

    <?php elseif ($active_tab === 'managers'): ?>

        <h2 style="margin-top:25px;">Current Managers</h2>

        <table class="widefat striped" style="max-width:700px;margin-top:15px;">
            <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>ID</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($manager_users)): ?>
                <tr><td colspan="4">No managers added.</td></tr>
            <?php else: ?>
                <?php foreach ($manager_users as $user): ?>
                    <tr>
                        <td><?php echo esc_html($user->display_name . " (" . $user->user_email . ")"); ?></td>
                        <td><?php echo esc_html(implode(', ', $user->roles)); ?></td>
                        <td><?php echo esc_html($user->ID); ?></td>
                        <td>
                            <a href="<?php echo esc_url( add_query_arg(array(
                                'page' => 'websiteflexi-system-settings',
                                'wf_remove_manager' => $user->ID,
                                'tab' => 'managers'
                            ), admin_url('plugins.php')) ); ?>"
                               onclick="return confirm('Remove this manager?');"
                               style="color:#dc3545;">
                                Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <h3 style="margin-top:25px;">Add Manager</h3>

        <form method="post">
            <?php wp_nonce_field('styliiiish_add_manager_action'); ?>

            <table class="form-table" style="max-width:500px;">
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="manager_email" required style="width:100%;"></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input type="text" name="manager_password" placeholder="Auto-generate if empty" style="width:100%;"></td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="styliiiish_add_manager" class="button button-primary">
                    Add Manager
                </button>
            </p>
        </form>

    <?php elseif ($active_tab === 'dashboard_access'): ?>

        <h2 style="margin-top:25px;">Owner Dashboard Access</h2>

        <table class="widefat striped" style="max-width:700px;margin-top:15px;">
            <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>ID</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($dashboard_users)): ?>
                <tr><td colspan="4">No dashboard users yet.</td></tr>
            <?php else: ?>
                <?php foreach ($dashboard_users as $user): ?>
                    <tr>
                        <td><?php echo esc_html($user->display_name . " (" . $user->user_email . ")"); ?></td>
                        <td><?php echo esc_html(implode(', ', $user->roles)); ?></td>
                        <td><?php echo esc_html($user->ID); ?></td>
                        <td>
                            <a href="<?php echo esc_url( add_query_arg(array(
                                'page' => 'websiteflexi-system-settings',
                                'wf_remove_dashboard_user' => $user->ID,
                                'tab' => 'dashboard_access'
                            ), admin_url('plugins.php')) ); ?>"
                               onclick="return confirm('Remove this user?');"
                               style="color:#dc3545;">
                                Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <h3 style="margin-top:25px;">Add Dashboard User</h3>

        <form method="post">
            <?php wp_nonce_field('styliiiish_add_dashboard_action'); ?>

            <table class="form-table" style="max-width:500px;">
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="dashboard_email" required style="width:100%;"></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input type="text" name="dashboard_password" placeholder="Auto-generate if empty" style="width:100%;"></td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="styliiiish_add_dashboard_user" class="button button-primary">
                    Add Dashboard User
                </button>
            </p>
        </form>

    <?php endif; ?>

</div>
