<style>
    .promo {
        background: linear-gradient(90deg, var(--secondary), #24384f);
        color: #fff;
        text-align: center;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 600;
    }

    .topbar {
        background: var(--secondary);
        color: #fff;
        font-size: 13px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .topbar-inner {
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .topbar-left,
    .topbar-right {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .topbar a {
        color: #fff;
        opacity: .92;
    }

    .topbar a:hover {
        opacity: 1;
    }

    .topbar-phone {
        direction: ltr;
        unicode-bidi: isolate;
        display: inline-block;
        letter-spacing: .2px;
        font-variant-numeric: tabular-nums;
    }

    .topbar-note {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.14);
        border-radius: 999px;
        padding: 4px 10px;
        font-weight: 700;
        font-size: 12px;
    }

    .lang-switch {
        position: relative;
        display: inline-grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        direction: ltr;
        width: 110px;
        height: 34px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 999px;
        padding: 3px;
        overflow: hidden;
    }

    .lang-switch .lang-indicator {
        position: absolute;
        top: 3px;
        width: calc(50% - 3px);
        height: calc(100% - 6px);
        background: #fff;
        border-radius: 999px;
        transition: .25s ease;
        z-index: 1;
    }

    .lang-switch.is-ar .lang-indicator {
        left: 3px;
    }

    .lang-switch.is-en .lang-indicator {
        right: 3px;
    }

    .lang-switch a {
        position: relative;
        z-index: 2;
        text-align: center;
        font-size: 12px;
        font-weight: 800;
        opacity: .95;
        color: #fff;
        padding: 5px 0;
    }

    .lang-switch a.active {
        color: var(--secondary);
        opacity: 1;
    }

    .main-header {
        background: #fff;
        border-bottom: 1px solid var(--line);
        position: sticky;
        top: 0;
        z-index: 40;
        box-shadow: 0 8px 24px rgba(23, 39, 59, 0.06);
    }

    .main-header-inner {
        min-height: 84px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 16px;
    }

    .brand {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .brand-logo {
        height: 40px;
        width: auto;
        max-width: min(220px, 38vw);
        object-fit: contain;
    }

    .brand-tag {
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
    }

    .main-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        background: #f9fbff;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 6px;
    }

    .main-nav a {
        color: var(--secondary);
        font-size: 14px;
        font-weight: 700;
        padding: 8px 12px;
        border-radius: 8px;
        transition: .2s ease;
        text-decoration: none;
    }

    .main-nav a:hover {
        color: var(--primary);
        background: #fff4f5;
    }

    .main-nav a.active {
        background: #fff4f5;
        color: var(--primary);
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .action-account,
    .action-wishlist,
    .action-cart,
    .action-sell {
        white-space: nowrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background: var(--primary-2);
        border-color: var(--primary-2);
    }

    .search-form {
        display: flex;
        align-items: center;
        height: 40px;
        border: 1px solid var(--line);
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .search-input {
        border: 0;
        outline: 0;
        width: 190px;
        padding: 0 12px;
        color: var(--secondary);
        font-size: 13px;
        font-family: inherit;
    }

    .search-btn {
        height: 100%;
        border: 0;
        background: var(--secondary);
        color: #fff;
        padding: 0 12px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
    }

    .search-btn:hover {
        background: #24384f;
    }

    .icon-btn {
        border: 1px solid var(--line);
        background: #fff;
        color: var(--secondary);
        border-radius: 10px;
        min-width: 38px;
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
        font-size: 13px;
        font-weight: 700;
    }

    .icon-btn .icon {
        font-size: 16px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .icon-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .wishlist-trigger-wrap,
    .cart-trigger-wrap,
    .account-trigger-wrap {
        position: relative;
    }

    .wishlist-count,
    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        background: var(--primary);
        color: #fff;
        font-size: 11px;
        line-height: 18px;
        text-align: center;
        font-weight: 800;
        padding: 0 4px;
        display: none;
    }

    .wishlist-plus-one,
    .cart-plus-one {
        position: absolute;
        top: -24px;
        right: -4px;
        font-size: 12px;
        font-weight: 900;
        color: var(--primary);
        opacity: 0;
        transform: translateY(0);
        pointer-events: none;
    }

    .wishlist-dropdown,
    .account-mini-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(23, 39, 59, .14);
        padding: 10px;
        display: none;
        z-index: 90;
    }

    .wishlist-dropdown { width: min(360px, 82vw); }
    .account-mini-menu { width: min(320px, 86vw); }

    [dir="rtl"] .wishlist-dropdown,
    [dir="rtl"] .account-mini-menu { right: auto; left: 0; }

    [dir="ltr"] .wishlist-dropdown,
    [dir="ltr"] .account-mini-menu { left: auto; right: 0; }

    .wishlist-dropdown.is-open,
    .account-mini-menu.is-open { display: block; }

    .account-mini-head {
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #fbfcff;
        padding: 10px;
        display: grid;
        gap: 4px;
        margin-bottom: 10px;
    }

    .account-mini-name {
        font-size: 14px;
        font-weight: 900;
        color: var(--secondary);
    }

    .account-mini-meta {
        font-size: 12px;
        color: var(--muted);
    }

    .account-mini-actions {
        display: grid;
        gap: 8px;
    }

    .account-mini-actions a {
        min-height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
    }

    .account-manage-link {
        border: 1px solid var(--line);
        background: #fff;
        color: var(--secondary);
    }

    .account-logout-link {
        border: 1px solid rgba(var(--wf-main-rgb), .25);
        background: #fff7f8;
        color: var(--primary);
    }

    .wishlist-dropdown-list {
        display: grid;
        gap: 8px;
        max-height: 360px;
        overflow: auto;
    }

    .wishlist-dropdown-item {
        display: grid;
        grid-template-columns: 56px 1fr;
        gap: 10px;
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 8px;
        background: #fff;
    }

    .wishlist-dropdown-item img {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 8px;
        background: #f2f2f5;
    }

    .wishlist-dropdown-name {
        font-size: 13px;
        font-weight: 800;
        color: var(--secondary);
        margin: 0 0 6px;
        line-height: 1.35;
    }

    .wishlist-dropdown-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 0 10px;
        border-radius: 8px;
        background: var(--primary);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
    }

    .wishlist-dropdown-empty {
        margin: 0;
        font-size: 13px;
        color: var(--muted);
        text-align: center;
        padding: 12px 8px;
        border: 1px dashed var(--line);
        border-radius: 10px;
        background: #fbfcff;
    }

    .wishlist-dropdown-footer {
        display: flex;
        justify-content: center;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--line);
    }

    .wishlist-dropdown-all {
        font-size: 13px;
        color: var(--primary);
        font-weight: 800;
    }

    .mini-cart {
        position: fixed;
        inset: 0;
        z-index: 110;
        pointer-events: none;
    }

    .mini-cart.is-open { pointer-events: auto; }

    .mini-cart-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 26, 42, 0.52);
        opacity: 0;
        transition: .2s ease;
    }

    .mini-cart.is-open .mini-cart-backdrop { opacity: 1; }

    .mini-cart-panel {
        position: absolute;
        top: 0;
        right: 0;
        width: min(430px, 92vw);
        height: 100%;
        background: #fff;
        border-inline-start: 1px solid var(--line);
        display: grid;
        grid-template-rows: auto 1fr auto;
        transform: translateX(100%);
        transition: .24s ease;
        box-shadow: -10px 0 30px rgba(23, 39, 59, .14);
    }

    .mini-cart.is-open .mini-cart-panel { transform: translateX(0); }
    [dir="rtl"] .mini-cart-panel { right: auto; left: 0; border-inline-start: 0; border-inline-end: 1px solid var(--line); transform: translateX(-100%); }
    [dir="rtl"] .mini-cart.is-open .mini-cart-panel { transform: translateX(0); }

    .mini-cart-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 12px;
        border-bottom: 1px solid var(--line);
    }

    .mini-cart-head h3 { margin: 0; font-size: 17px; color: var(--secondary); }

    .mini-cart-close {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
        color: var(--secondary);
        padding: 6px 10px;
        cursor: pointer;
        font-family: inherit;
        font-weight: 700;
    }

    .mini-cart-list {
        overflow: auto;
        padding: 12px;
        display: grid;
        gap: 10px;
        align-content: start;
    }

    .mini-cart-item {
        display: grid;
        grid-template-columns: 70px 1fr auto;
        gap: 10px;
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 9px;
        align-items: center;
    }

    .mini-cart-item img {
        width: 70px;
        height: 92px;
        object-fit: cover;
        border-radius: 9px;
        background: #f2f2f5;
    }

    .mini-cart-item h4 {
        margin: 0 0 4px;
        font-size: 13px;
        line-height: 1.35;
        color: var(--secondary);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .mini-cart-meta { font-size: 12px; color: var(--muted); display: flex; gap: 6px; align-items: center; }
    .mini-cart-price { font-size: 12px; color: var(--primary); font-weight: 800; margin-top: 4px; }

    .mini-cart-remove {
        border: 1px solid rgba(var(--wf-main-rgb), .2);
        background: #fff;
        color: var(--primary);
        border-radius: 8px;
        min-width: 34px;
        min-height: 34px;
        font-weight: 800;
        cursor: pointer;
    }

    .mini-cart-empty { color: var(--muted); font-size: 14px; padding: 8px 0; text-align: center; }
    .mini-cart-loading { color: var(--muted); font-size: 13px; text-align: center; padding: 10px 0; }

    .mini-cart-foot { border-top: 1px solid var(--line); padding: 12px; display: grid; gap: 8px; }
    .mini-cart-subtotal { font-size: 13px; color: var(--secondary); display: flex; justify-content: space-between; }
    .mini-cart-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .mini-cart-actions a { min-height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; text-decoration: none; }
    .mini-cart-view { border: 1px solid var(--line); background: #fff; color: var(--secondary); }
    .mini-cart-checkout { background: var(--primary); color: #fff; }

    .auth-modal { position: fixed; inset: 0; z-index: 120; pointer-events: none; }
    .auth-modal.is-open { pointer-events: auto; }
    .auth-modal-backdrop { position: absolute; inset: 0; background: rgba(15, 26, 42, .54); opacity: 0; transition: .22s ease; }
    .auth-modal.is-open .auth-modal-backdrop { opacity: 1; }

    .auth-modal-panel {
        position: absolute;
        top: 50%;
        left: 50%;
        width: min(450px, 94vw);
        max-height: min(88vh, 760px);
        overflow: auto;
        transform: translate(-50%, calc(-50% + 16px));
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 22px 50px rgba(23, 39, 59, .24);
        padding: 18px;
        transition: .22s ease;
        opacity: 0;
    }

    .auth-modal.is-open .auth-modal-panel { transform: translate(-50%, -50%); opacity: 1; }
    .auth-head { display: flex; align-items: start; justify-content: space-between; gap: 10px; margin-bottom: 14px; }
    .auth-title-wrap h3 { margin: 0; font-size: 22px; line-height: 1.2; color: var(--secondary); }
    .auth-title-wrap p { margin: 7px 0 0; font-size: 13px; color: var(--muted); }
    .auth-close { border: 1px solid var(--line); border-radius: 10px; min-width: 36px; min-height: 36px; background: #fff; color: var(--secondary); font-size: 18px; cursor: pointer; }
    .auth-form { display: grid; gap: 10px; }
    .auth-field { display: grid; gap: 6px; }
    .auth-field label { font-size: 12px; font-weight: 800; color: var(--secondary); }
    .auth-field input[type="text"], .auth-field input[type="password"] { min-height: 44px; border: 1px solid var(--line); border-radius: 10px; padding: 0 12px; font-size: 14px; color: var(--secondary); outline: none; font-family: inherit; background: #fff; }
    .auth-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .auth-remember { display: inline-flex; align-items: center; gap: 8px; color: var(--secondary); font-size: 13px; font-weight: 700; }
    .auth-forgot { color: var(--primary); font-size: 13px; font-weight: 700; }
    .auth-submit { min-height: 44px; border: 0; border-radius: 10px; background: var(--primary); color: #fff; font-size: 14px; font-weight: 800; font-family: inherit; cursor: pointer; }
    .auth-divider { display: flex; align-items: center; gap: 10px; color: var(--muted); font-size: 12px; margin: 2px 0; }
    .auth-divider::before, .auth-divider::after { content: ""; flex: 1; height: 1px; background: var(--line); }
    .auth-google-wrap { display: grid; gap: 8px; }
    .auth-google-wrap .googlesitekit-sign-in-with-google__frontend-output-button { width: 100%; min-height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--line); border-radius: 10px; background: #fff; overflow: hidden; }
    .auth-google-fallback { min-height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; font-weight: 800; border: 1px solid var(--line); color: var(--secondary); background: #fff; text-decoration: none; }
    .auth-register { min-height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; font-weight: 800; border: 1px dashed rgba(var(--wf-main-rgb), .38); color: var(--primary); background: #fff8f9; text-decoration: none; }

    .header-cta {
        height: 40px;
        padding: 0 14px;
        border-radius: 10px;
        font-size: 13px;
    }

    @media (max-width: 980px) {
        .main-header-inner {
            grid-template-columns: 1fr;
            padding: 12px 0;
        }

        .brand,
        .main-nav,
        .header-actions {
            justify-content: center;
            text-align: center;
        }

        .main-nav {
            overflow-x: auto;
            flex-wrap: nowrap;
            justify-content: flex-start;
            scrollbar-width: thin;
        }

        .main-nav a {
            white-space: nowrap;
        }
    }

    @media (max-width: 640px) {
        .promo {
            font-size: 12px;
            line-height: 1.45;
            padding: 8px 12px;
        }

        .topbar-inner {
            justify-content: center;
            min-height: 36px;
        }

        .topbar-left {
            display: none;
        }

        .topbar-right {
            width: 100%;
            justify-content: center;
            gap: 8px;
        }

        .main-header-inner {
            grid-template-columns: 1fr auto;
            gap: 8px;
            min-height: auto;
            padding: 10px 0;
        }

        .brand {
            align-items: center;
            text-align: center;
        }

        .brand-logo {
            height: 34px;
            max-width: 190px;
        }

        .brand-tag {
            font-size: 11px;
        }

        .main-nav {
            grid-column: 1 / -1;
            margin-top: 4px;
            border-radius: 10px;
            padding: 5px;
            gap: 6px;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x proximity;
            scrollbar-width: none;
        }

        .main-nav::-webkit-scrollbar {
            display: none;
        }

        .main-nav a {
            font-size: 12px;
            padding: 7px 10px;
            scroll-snap-align: start;
        }

        .header-actions {
            justify-content: flex-end;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .search-form {
            display: none;
        }

        .icon-btn {
            min-width: 34px;
            min-height: 34px;
            padding: 0 8px;
            font-size: 12px;
            border-radius: 8px;
        }

        .icon-btn .icon {
            font-size: 15px;
        }

        .action-wishlist,
        .action-sell {
            display: none;
        }

        .action-account,
        .action-cart {
            min-width: 46px;
            justify-content: center;
        }
    }

    @media (max-width: 390px) {
        .action-account,
        .action-cart {
            min-width: 42px;
            font-size: 11px;
        }

        .brand-logo {
            height: 30px;
            max-width: 165px;
        }

        .main-nav a {
            font-size: 11px;
            padding: 6px 9px;
        }
    }
</style>
