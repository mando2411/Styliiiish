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

    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
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
