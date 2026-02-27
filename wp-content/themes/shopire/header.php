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
    $account_url = $is_english ? home_url('/my-account/') : home_url('/ar/%d8%ad%d8%b3%d8%a7%d8%a8%d9%8a/');
	$is_account_path = in_array($normalized_path, ['/my-account', '/en/my-account', '/ar/حسابي', '/ara/حسابي', '/حسابي'], true);
	$is_account_layout = $is_account_path || (function_exists('is_account_page') && is_account_page());
?>

<?php if ($is_account_layout) : ?>
<style>
    .laravel-account-topbar{background:#17273B;color:#fff;font-size:13px;border-bottom:1px solid rgba(255,255,255,.12)}
    .laravel-account-wrap{width:min(1180px,92%);margin:0 auto}
    .laravel-account-topbar-inner{min-height:42px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
    .laravel-account-topbar a{color:#fff;text-decoration:none}
    .laravel-account-main-header{background:#fff;border-bottom:1px solid rgba(189,189,189,.4);position:sticky;top:0;z-index:40;box-shadow:0 8px 24px rgba(23,39,59,.06)}
    .laravel-account-main-header-inner{min-height:88px;display:flex;align-items:center;justify-content:space-between;gap:16px}
    .laravel-account-brand{display:flex;align-items:center;gap:10px;text-decoration:none}
    .laravel-account-brand img{height:56px;width:auto;max-width:min(320px,52vw);object-fit:contain}
    .laravel-account-nav{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .laravel-account-nav a{color:#17273B;font-size:14px;font-weight:700;padding:8px 12px;border-radius:8px;text-decoration:none;background:#f9fbff;border:1px solid rgba(189,189,189,.35)}
    .laravel-account-nav a:hover{color:#d51522;background:#fff4f5}
    .laravel-account-cta{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:10px;font-size:14px;font-weight:700;background:#d51522;color:#fff;text-decoration:none}
    .laravel-account-promo{background:linear-gradient(90deg,#17273B,#24384f);color:#fff;text-align:center;padding:10px 16px;font-size:14px;font-weight:600}
    @media (max-width:900px){.laravel-account-main-header-inner{flex-wrap:wrap;padding:10px 0}.laravel-account-nav{width:100%}}
</style>
<?php endif; ?>

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'shopire' ); ?></a>
	
	<?php if ($is_account_layout) : ?>
		<div class="laravel-account-topbar">
			<div class="laravel-account-wrap laravel-account-topbar-inner">
				<div><?php echo $is_english ? 'Call us anytime:' : 'اتصلي بنا في أي وقت:'; ?> <a href="tel:+201050874255" dir="ltr" lang="en">+20 010 5087 4255</a></div>
				<div>
					<a href="<?php echo esc_url(home_url('/ar/%d8%ad%d8%b3%d8%a7%d8%a8%d9%8a/')); ?>">AR</a>
					&nbsp;|&nbsp;
					<a href="<?php echo esc_url(home_url('/my-account/')); ?>">EN</a>
				</div>
			</div>
		</div>
		<header class="laravel-account-main-header">
			<div class="laravel-account-wrap laravel-account-main-header-inner">
				<a class="laravel-account-brand" href="<?php echo esc_url(home_url($locale_prefix)); ?>">
					<img src="https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png" alt="Styliiiish">
				</a>
				<nav class="laravel-account-nav" aria-label="Main Navigation">
					<a href="<?php echo esc_url(home_url($locale_prefix)); ?>"><?php echo $is_english ? 'Home' : 'الرئيسية'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>"><?php echo $is_english ? 'Shop' : 'المتجر'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/blog')); ?>"><?php echo $is_english ? 'Blog' : 'المدونة'; ?></a>
					<a href="<?php echo esc_url(home_url($locale_prefix . '/contact-us')); ?>"><?php echo $is_english ? 'Contact' : 'تواصل معنا'; ?></a>
				</nav>
				<a class="laravel-account-cta" href="<?php echo esc_url($account_url); ?>"><?php echo $is_english ? 'My Account' : 'حسابي'; ?></a>
			</div>
		</header>
		<div class="laravel-account-promo"><?php echo $is_english ? 'Because every woman deserves to shine • Up to 50% OFF • Delivery across Egypt in 2–10 business days' : 'لأن كل امرأة تستحق أن تتألق • خصومات تصل إلى 50% • توصيل داخل مصر خلال 2–10 أيام عمل'; ?></div>
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
	