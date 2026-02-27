<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php echo esc_url(get_bloginfo( 'pingback_url' )); ?>">
		<?php endif; ?>

		<?php wp_head(); ?>
	</head>
<body <?php body_class('section-title-one btn--effect-six menu__active-one'); ?>>
<?php wp_body_open(); ?>

<?php
	$request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
	$request_path = parse_url($request_uri, PHP_URL_PATH);
	$request_path = is_string($request_path) ? $request_path : '/';
	$normalized_path = rawurldecode(strtolower(rtrim($request_path, '/')));
	if ($normalized_path === '') {
		$normalized_path = '/';
	}
	$is_english = preg_match('#^/en(?:/|$)#i', $request_path) === 1;
	$locale_prefix = $is_english ? '/en' : '/ar';
	$wp_base_url = rtrim(home_url('/'), '/');
	$wp_logo = $wp_base_url . '/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
	$is_account_layout = in_array($normalized_path, ['/my-account', '/en/my-account', '/ar/حسابي', '/ara/حسابي', '/حسابي'], true);
?>

<?php if ($is_account_layout) : ?>
<style>
	:root{--wf-main-rgb:213,21,34;--wf-main-color:rgb(var(--wf-main-rgb));--wf-secondary-color:#17273B;--line:rgba(189,189,189,.4);--primary:var(--wf-main-color);--secondary:var(--wf-secondary-color);--muted:#5a6678}
	.container{width:min(1180px,92%);margin:0 auto}
	.topbar{background:var(--secondary);color:#fff;font-size:13px;border-bottom:1px solid rgba(255,255,255,.12)}
	.topbar-inner{min-height:42px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
	.topbar-left,.topbar-right{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
	.topbar a{color:#fff;opacity:.92;text-decoration:none}.topbar a:hover{opacity:1}
	.topbar-note{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.14);border-radius:999px;padding:4px 10px;font-weight:700;font-size:12px}
	.lang-switch{position:relative;display:inline-grid;grid-template-columns:1fr 1fr;align-items:center;direction:ltr;width:110px;height:34px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.28);border-radius:999px;padding:3px;overflow:hidden}
	.lang-switch .lang-indicator{position:absolute;top:3px;width:calc(50% - 3px);height:calc(100% - 6px);background:#fff;border-radius:999px;transition:.25s ease;z-index:1}
	.lang-switch.is-ar .lang-indicator{left:3px}
	.lang-switch.is-en .lang-indicator{right:3px}
	.lang-switch a{position:relative;z-index:2;text-align:center;font-size:12px;font-weight:800;opacity:.95;color:#fff;padding:5px 0;text-decoration:none}
	.lang-switch a.active{color:var(--secondary);opacity:1}
	.main-header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:40;box-shadow:0 8px 24px rgba(23,39,59,.06)}
	.main-header-inner{min-height:96px;display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:16px}
	.brand{display:flex;flex-direction:column;gap:2px;text-decoration:none}
	.brand-logo{height:56px!important;max-height:56px!important;width:auto!important;max-width:min(260px,42vw)!important;object-fit:contain!important;display:block}
	.brand-tag{color:var(--muted);font-size:12px;font-weight:600}
	.main-nav{display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap;background:#f9fbff;border:1px solid var(--line);border-radius:12px;padding:6px}
	.main-nav a{color:var(--secondary);font-size:14px;font-weight:700;padding:8px 12px;border-radius:8px;transition:.2s ease;text-decoration:none}
	.main-nav a:hover{color:var(--primary);background:#fff4f5}
	.header-actions{display:flex;align-items:center;gap:8px;justify-content:flex-end;flex-wrap:wrap}
	.search-form{display:flex;align-items:center;gap:6px}
	.search-input{height:38px;min-width:190px;border:1px solid var(--line);border-radius:10px;padding:0 12px;font-size:13px;outline:none}
	.search-btn{height:38px;border:none;border-radius:10px;background:#17273B;color:#fff;padding:0 12px;font-size:13px;font-weight:700;cursor:pointer}
	.icon-btn{height:38px;min-width:38px;border:1px solid var(--line);border-radius:10px;background:#fff;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;color:var(--secondary);font-size:16px}
	.header-cta{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:10px;font-size:14px;font-weight:700;background:var(--primary);color:#fff;text-decoration:none}
	.promo{background:linear-gradient(90deg,var(--secondary),#24384f);color:#fff;text-align:center;padding:10px 16px;font-size:14px;font-weight:600}
	@media (max-width:900px){.main-header-inner{grid-template-columns:1fr;gap:10px;padding:12px 0}.main-nav{justify-content:flex-start}.brand-logo{height:46px!important;max-height:46px!important;max-width:min(220px,70vw)!important}.search-input{min-width:140px}}
</style>
<?php endif; ?>

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ekart' ); ?></a>

	<?php if ($is_account_layout) : ?>
		<div class="topbar">
			<div class="container topbar-inner">
				<div class="topbar-right">
					<strong><?php echo $is_english ? 'Call us anytime:' : 'اتصلي بنا في أي وقت:'; ?></strong>
					<a href="tel:+201050874255" dir="ltr" lang="en">+20 010 5087 4255</a>
				</div>
				<div class="topbar-left">
					<div class="lang-switch <?php echo $is_english ? 'is-en' : 'is-ar'; ?>" aria-label="Language Switcher">
						<span class="lang-indicator" aria-hidden="true"></span>
						<a class="<?php echo !$is_english ? 'active' : ''; ?>" href="<?php echo esc_url(home_url('/ar/حسابي/')); ?>">AR</a>
						<a class="<?php echo $is_english ? 'active' : ''; ?>" href="<?php echo esc_url(home_url('/my-account/')); ?>">EN</a>
					</div>
					<span class="topbar-note"><?php echo $is_english ? '⚡ Daily Deals' : '⚡ خصومات يومية'; ?></span>
					<a href="https://www.facebook.com/Styliiish.Egypt/" target="_blank" rel="noopener">Facebook</a>
					<a href="https://www.instagram.com/styliiiish.egypt/" target="_blank" rel="noopener">Instagram</a>
					<a href="https://g.page/styliish" target="_blank" rel="noopener">Google</a>
				</div>
			</div>
		</div>
		<header class="main-header">
			<div class="container main-header-inner">
				<a class="brand" href="<?php echo esc_url(home_url($locale_prefix)); ?>">
					<img class="brand-logo" src="<?php echo esc_url($wp_logo); ?>" alt="Styliiiish">
					<span class="brand-tag"><?php echo $is_english ? 'Because every woman deserves to shine' : 'لأن كل امرأة تستحق أن تتألق'; ?></span>
				</a>
				<nav class="main-nav" aria-label="Main Navigation">
					<a href="<?php echo esc_url(home_url($locale_prefix)); ?>"><?php echo $is_english ? 'Home' : 'الرئيسية'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>"><?php echo $is_english ? 'Shop' : 'المتجر'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/blog')); ?>"><?php echo $is_english ? 'Blog' : 'المدونة'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/about-us')); ?>"><?php echo $is_english ? 'About' : 'من نحن'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/contact-us')); ?>"><?php echo $is_english ? 'Contact' : 'تواصل معنا'; ?></a>
				</nav>
				<div class="header-actions">
					<form class="search-form" action="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>" method="get">
						<input class="search-input" type="search" name="q" required placeholder="<?php echo esc_attr($is_english ? 'Search for your dress...' : 'ابحثي عن فستانك...'); ?>" aria-label="<?php echo esc_attr($is_english ? 'Search for your dress...' : 'ابحثي عن فستانك...'); ?>">
						<button class="search-btn" type="submit"><?php echo $is_english ? 'Search' : 'بحث'; ?></button>
					</form>
					<a class="icon-btn" href="<?php echo esc_url($is_english ? home_url('/my-account/') : home_url('/ar/حسابي/')); ?>" aria-label="<?php echo esc_attr($is_english ? 'My Account' : 'حسابي'); ?>" title="<?php echo esc_attr($is_english ? 'My Account' : 'حسابي'); ?>">👤</a>
					<a class="icon-btn" href="<?php echo esc_url(home_url($locale_prefix . '/wishlist')); ?>" aria-label="<?php echo esc_attr($is_english ? 'Wishlist' : 'المفضلة'); ?>" title="<?php echo esc_attr($is_english ? 'Wishlist' : 'المفضلة'); ?>">❤</a>
					<a class="icon-btn" href="<?php echo esc_url(home_url($locale_prefix . '/cart')); ?>" aria-label="<?php echo esc_attr($is_english ? 'Cart' : 'السلة'); ?>" title="<?php echo esc_attr($is_english ? 'Cart' : 'السلة'); ?>">🛒</a>
					<a class="header-cta" href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener"><?php echo $is_english ? 'Start Selling' : 'ابدئي البيع'; ?></a>
				</div>
			</div>
		</header>
		<div class="promo"><?php echo $is_english ? 'Because every woman deserves to shine • Up to 50% OFF • Delivery across Egypt in 2–10 business days' : 'لأن كل امرأة تستحق أن تتألق • خصومات تصل إلى 50% • توصيل داخل مصر خلال 2–10 أيام عمل'; ?></div>
	<?php else : ?>
		<?php 
			// Theme Header
			do_action('shopire_site_main_header'); 
			
			// Theme Breadcrumb
			if ( !is_page_template( 'page-templates/frontpage.php' )) {
					get_template_part('/template-parts/site','breadcrumb');
			}
		?>
	<?php endif; ?>
	
	<div id="content" class="site-content">
	