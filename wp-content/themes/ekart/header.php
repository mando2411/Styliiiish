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
	.laravel-wrap{width:min(1180px,92%);margin:0 auto}
	.topbar{background:var(--secondary);color:#fff;font-size:13px;border-bottom:1px solid rgba(255,255,255,.12)}
	.topbar-inner{min-height:42px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
	.topbar-left,.topbar-right{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
	.topbar a{color:#fff;opacity:.92;text-decoration:none}.topbar a:hover{opacity:1}
	.main-header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:40;box-shadow:0 8px 24px rgba(23,39,59,.06)}
	.main-header-inner{min-height:96px;display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:16px}
	.brand{display:flex;flex-direction:column;gap:2px;text-decoration:none}
	.brand-logo{height:60px;width:auto;max-width:min(320px,52vw);object-fit:contain}
	.brand-tag{color:var(--muted);font-size:12px;font-weight:600}
	.main-nav{display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap;background:#f9fbff;border:1px solid var(--line);border-radius:12px;padding:6px}
	.main-nav a{color:var(--secondary);font-size:14px;font-weight:700;padding:8px 12px;border-radius:8px;transition:.2s ease;text-decoration:none}
	.main-nav a:hover{color:var(--primary);background:#fff4f5}
	.header-cta{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:10px;font-size:14px;font-weight:700;background:var(--primary);color:#fff;text-decoration:none}
	.promo{background:linear-gradient(90deg,var(--secondary),#24384f);color:#fff;text-align:center;padding:10px 16px;font-size:14px;font-weight:600}
	@media (max-width:900px){.main-header-inner{grid-template-columns:1fr;gap:10px;padding:12px 0}.main-nav{justify-content:flex-start}}
</style>
<?php endif; ?>

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ekart' ); ?></a>

	<?php if ($is_account_layout) : ?>
		<div class="topbar">
			<div class="laravel-wrap topbar-inner">
				<div class="topbar-right">
					<strong><?php echo $is_english ? 'Call us anytime:' : 'اتصلي بنا في أي وقت:'; ?></strong>
					<a href="tel:+201050874255" dir="ltr" lang="en">+20 010 5087 4255</a>
				</div>
				<div class="topbar-left">
					<a href="<?php echo esc_url(home_url('/ar/حسابي/')); ?>">AR</a>
					<a href="<?php echo esc_url(home_url('/my-account/')); ?>">EN</a>
					<a href="https://www.facebook.com/Styliiish.Egypt/" target="_blank" rel="noopener">Facebook</a>
					<a href="https://www.instagram.com/styliiiish.egypt/" target="_blank" rel="noopener">Instagram</a>
				</div>
			</div>
		</div>
		<header class="main-header">
			<div class="laravel-wrap main-header-inner">
				<a class="brand" href="<?php echo esc_url(home_url($locale_prefix)); ?>">
					<img class="brand-logo" src="<?php echo esc_url($wp_logo); ?>" alt="Styliiiish">
					<span class="brand-tag"><?php echo $is_english ? 'Because every woman deserves to shine' : 'لأن كل امرأة تستحق أن تتألق'; ?></span>
				</a>
				<nav class="main-nav" aria-label="Main Navigation">
					<a href="<?php echo esc_url(home_url($locale_prefix)); ?>"><?php echo $is_english ? 'Home' : 'الرئيسية'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>"><?php echo $is_english ? 'Shop' : 'المتجر'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/blog')); ?>"><?php echo $is_english ? 'Blog' : 'المدونة'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/marketplace')); ?>"><?php echo $is_english ? 'Marketplace' : 'الماركت بليس'; ?></a>
				</nav>
				<a class="header-cta" href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener"><?php echo $is_english ? 'Start Selling' : 'ابدئي البيع'; ?></a>
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
	