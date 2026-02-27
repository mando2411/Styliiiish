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
	$is_english = in_array($normalized_path, ['/my-account', '/en/my-account'], true);
	$locale_prefix = $is_english ? '/en' : '/ar';
	$wp_base_url = rtrim((string) get_option('home'), '/');
	if ($wp_base_url === '') {
		$wp_base_url = rtrim(home_url('/'), '/');
	}

	$build_localized_url = static function (string $path = '') use ($wp_base_url, $locale_prefix): string {
		$clean_path = trim($path);
		$clean_path = $clean_path === '' ? '' : ('/' . ltrim($clean_path, '/'));
		$clean_path = preg_replace('#^/(ar|en)(?=/|$)#i', '', (string) $clean_path);
		$clean_path = $clean_path === null ? '' : $clean_path;

		return rtrim($wp_base_url, '/') . $locale_prefix . $clean_path;
	};
	$wp_logo = $wp_base_url . '/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
	$my_dresses_url = $is_english ? 'https://styliiiish.com/my-dresses/' : 'https://styliiiish.com/ar/%d9%81%d8%b3%d8%a7%d8%aa%d9%8a%d9%86%d9%8a/';
	$ar_switch_url = 'https://styliiiish.com/ar/%d8%ad%d8%b3%d8%a7%d8%a8%d9%8a/';
	$en_switch_url = 'https://styliiiish.com/my-account/';
?>

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
	.icon-wrap{position:relative;display:inline-flex;align-items:center}
	.icon-btn{height:38px;min-width:38px;border:1px solid var(--line);border-radius:10px;background:#fff;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;color:var(--secondary);font-size:16px}
	.icon-plus-one{position:absolute;top:-7px;right:-8px;background:var(--primary);color:#fff;border-radius:999px;padding:1px 6px;font-size:11px;font-weight:800;line-height:1}
	.header-cta{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:10px;font-size:14px;font-weight:700;background:var(--primary);color:#fff;text-decoration:none}
	.promo{background:linear-gradient(90deg,var(--secondary),#24384f);color:#fff;text-align:center;padding:10px 16px;font-size:14px;font-weight:600}
	.header-categories-strip{background:#fff;border-bottom:1px solid var(--line)}
	.categories-strip-inner{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:10px 0}
	.category-strip-chip{display:inline-flex;align-items:center;justify-content:center;padding:7px 12px;border-radius:999px;background:#f6f8fb;color:var(--secondary);font-size:13px;font-weight:700;text-decoration:none;border:1px solid var(--line)}
	.category-strip-chip:hover{background:#fff4f5;color:var(--primary)}
	@media (max-width:900px){.main-header-inner{grid-template-columns:1fr;gap:10px;padding:12px 0}.main-nav{justify-content:flex-start}.brand-logo{height:46px!important;max-height:46px!important;max-width:min(220px,70vw)!important}.search-input{min-width:140px}}
</style>

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ekart' ); ?></a>

		<div class="topbar">
			<div class="container topbar-inner">
				<div class="topbar-right">
					<strong>Call us anytime:</strong>
					<a href="tel:+201050874255" dir="ltr" lang="en">+20 010 5087 4255</a>
				</div>
				<div class="topbar-left">
					<div class="lang-switch <?php echo $is_english ? 'is-en' : 'is-ar'; ?>" aria-label="Language Switcher" data-no-translation data-en-url="<?php echo esc_attr($en_switch_url); ?>" data-ar-url="<?php echo esc_attr($ar_switch_url); ?>">
						<span class="lang-indicator" aria-hidden="true"></span>
						<a class="<?php echo !$is_english ? 'active' : ''; ?>" href="<?php echo esc_url($ar_switch_url); ?>" onclick="var sw=this.parentElement;sw.classList.remove('is-en');sw.classList.add('is-ar');this.classList.add('active');this.nextElementSibling&&this.nextElementSibling.classList.remove('active');window.location.href='<?php echo esc_js($ar_switch_url); ?>';return false;">AR</a>
						<a class="<?php echo $is_english ? 'active' : ''; ?>" href="<?php echo esc_url($en_switch_url); ?>" onclick="var sw=this.parentElement;sw.classList.remove('is-ar');sw.classList.add('is-en');this.classList.add('active');this.previousElementSibling&&this.previousElementSibling.classList.remove('active');window.location.href='<?php echo esc_js($en_switch_url); ?>';return false;">EN</a>
					</div>
					<script>
					(function(){
						var sw = document.currentScript && document.currentScript.previousElementSibling;
						if(!sw || !sw.classList || !sw.classList.contains('lang-switch')) return;
						var current = (window.location.pathname || '').replace(/\/+$/,'').toLowerCase();
						var isEnglishPath = (current === '/my-account' || current === '/en/my-account');
						var links = sw.querySelectorAll('a');
						if(links.length !== 2) return;
						var arLink = links[0];
						var enLink = links[1];
						if(isEnglishPath){
							sw.classList.remove('is-ar');
							sw.classList.add('is-en');
							enLink.classList.add('active');
							arLink.classList.remove('active');
						}else{
							sw.classList.remove('is-en');
							sw.classList.add('is-ar');
							arLink.classList.add('active');
							enLink.classList.remove('active');
						}
					})();
					</script>
					<span class="topbar-note">⚡ Daily Deals</span>
					<a href="https://www.facebook.com/Styliiish.Egypt/" target="_blank" rel="noopener">Facebook</a>
					<a href="https://www.instagram.com/styliiiish.egypt/" target="_blank" rel="noopener">Instagram</a>
					<a href="https://g.page/styliish" target="_blank" rel="noopener">Google</a>
				</div>
			</div>
		</div>
		<header class="main-header">
			<div class="container main-header-inner">
				<a class="brand" href="<?php echo esc_url($build_localized_url('/')); ?>">
					<img class="brand-logo" src="<?php echo esc_url($wp_logo); ?>" alt="Styliiiish">
					<span class="brand-tag">Because every woman deserves to shine</span>
				</a>
				<nav class="main-nav" aria-label="Main Navigation">
					<a href="<?php echo esc_url($build_localized_url('/')); ?>">Home</a>
					<a href="<?php echo esc_url($build_localized_url('/shop')); ?>">Shop</a>
					<a href="<?php echo esc_url($build_localized_url('/blog')); ?>">Blog</a>
					<a href="<?php echo esc_url($build_localized_url('/about-us')); ?>">About</a>
					<a href="<?php echo esc_url($build_localized_url('/contact-us')); ?>">Contact</a>
				</nav>
				<div class="header-actions">
					<form class="search-form" action="<?php echo esc_url($build_localized_url('/shop')); ?>" method="get">
						<input class="search-input" type="search" name="q" required placeholder="<?php echo esc_attr('Search for your dress...'); ?>" aria-label="<?php echo esc_attr('Search for your dress...'); ?>">
						<button class="search-btn" type="submit">Search</button>
					</form>
					<a class="icon-btn" href="<?php echo esc_url($is_english ? 'https://styliiiish.com/my-account/' : 'https://styliiiish.com/ar/%d8%ad%d8%b3%d8%a7%d8%a8%d9%8a/'); ?>" aria-label="<?php echo esc_attr('My Account'); ?>" title="<?php echo esc_attr('My Account'); ?>">👤</a>
					<span class="icon-wrap">
						<a class="icon-btn" href="<?php echo esc_url($build_localized_url('/wishlist')); ?>" aria-label="<?php echo esc_attr('Wishlist'); ?>" title="<?php echo esc_attr('Wishlist'); ?>">❤</a>
						<span class="icon-plus-one">+1</span>
					</span>
					<span class="icon-wrap">
						<a class="icon-btn" href="<?php echo esc_url($build_localized_url('/cart')); ?>" aria-label="<?php echo esc_attr('Cart'); ?>" title="<?php echo esc_attr('Cart'); ?>">🛒</a>
						<span class="icon-plus-one">+1</span>
					</span>
					<a class="header-cta" href="<?php echo esc_url($my_dresses_url); ?>" target="_blank" rel="noopener">Start Selling</a>
				</div>
			</div>
		</header>
		<div class="promo">Because every woman deserves to shine • Up to 50% OFF • Delivery across Egypt in 2–10 business days</div>
		<div class="header-categories-strip">
			<div class="container categories-strip-inner">
				<a class="category-strip-chip" href="<?php echo esc_url($build_localized_url('/shop?category=dress')); ?>">Dress</a>
				<a class="category-strip-chip" href="<?php echo esc_url($build_localized_url('/shop?category=bridesmaid-dresses')); ?>">Bridesmaid Dresses</a>
				<a class="category-strip-chip" href="<?php echo esc_url($build_localized_url('/shop?category=evening-dresses')); ?>">Evening Dresses</a>
				<a class="category-strip-chip" href="<?php echo esc_url($build_localized_url('/shop?category=final-clearance-dresses')); ?>">Final Clearance Dresses</a>
				<a class="category-strip-chip" href="<?php echo esc_url($build_localized_url('/shop?category=plus-size-dresses')); ?>">Plus Size Dresses</a>
				<a class="category-strip-chip" href="<?php echo esc_url($build_localized_url('/shop?category=mothers-dresses')); ?>">Mother of the Bride Dresses</a>
				<a class="category-strip-chip" href="<?php echo esc_url($build_localized_url('/shop?category=pre-loved-dresses')); ?>">Pre-Loved Dresses</a>
			</div>
		</div>
	
	<div id="content" class="site-content">
	