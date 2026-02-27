</div></div>
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
	$account_url = $is_english ? home_url('/my-account/') : home_url('/ar/حسابي/');
	$is_account_layout = in_array($normalized_path, ['/my-account', '/en/my-account', '/ar/حسابي', '/ara/حسابي', '/حسابي'], true);
	$hour_now = (int) current_time('G');
	$is_open_now = ($hour_now >= 11 && $hour_now < 19);
?>

<?php if ($is_account_layout) : ?>
<style>
	.site-footer{margin-top:8px;background:#0f1a2a;color:#fff;border-top:4px solid #d51522}
	.footer-wrap{width:min(1180px,92%);margin:0 auto}
	.footer-grid{display:grid;grid-template-columns:1.2fr 1fr 1fr 1fr;gap:18px;padding:34px 0 22px}
	.footer-brand,.footer-col{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:16px}
	.footer-brand h4,.footer-col h5{margin:0 0 10px;font-size:18px;color:#fff}
	.footer-brand p{margin:0 0 10px;color:#b8c2d1;font-size:14px}
	.footer-status,.footer-open-hours{margin:0 0 10px;color:#b8c2d1;font-size:14px}
	.status-pill{display:inline-flex;align-items:center;justify-content:center;padding:3px 9px;border-radius:999px;font-size:12px;font-weight:800;border:1px solid transparent;line-height:1.2}
	.status-pill.is-open{color:#0a8f5b;border-color:rgba(10,143,91,.45);background:rgba(10,143,91,.14)}
	.status-pill.is-closed{color:#d51522;border-color:rgba(213,21,34,.45);background:rgba(213,21,34,.14)}
	.footer-links{list-style:none;margin:0;padding:0;display:grid;gap:7px}
	.footer-links a{color:#b8c2d1;font-size:14px;text-decoration:none;transition:.2s ease}
	.footer-links a:hover{color:#fff}
	.footer-contact-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
	.footer-contact-row a{color:#fff;background:rgba(213,21,34,.16);border:1px solid rgba(213,21,34,.35);border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;text-decoration:none}
	.footer-bottom{border-top:1px solid rgba(255,255,255,.14);padding:12px 0 20px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;color:#b8c2d1;font-size:13px}
	.footer-bottom a{color:#fff;text-decoration:none}
	.footer-mini-nav{display:flex;gap:12px;flex-wrap:wrap;justify-content:center;padding-bottom:18px}
	.footer-mini-nav a{color:#b8c2d1;font-size:13px;text-decoration:none}
	.footer-mini-nav a:hover{color:#fff}
	@media (max-width:900px){.footer-grid{grid-template-columns:1fr}}
</style>
<footer class="site-footer notranslate" translate="no">
	<div class="footer-wrap footer-grid">
		<div class="footer-brand">
			<img src="<?php echo esc_url($wp_logo); ?>" alt="Styliiiish" style="height:62px;width:auto;max-width:min(360px,100%);margin-bottom:10px;">
			<h4><?php echo $is_english ? 'Styliiiish Fashion House' : 'ستيليش فاشون هاوس'; ?></h4>
			<p><?php echo $is_english ? 'We are passionate about offering the latest dress designs for every special occasion.' : 'نعمل بشغف على تقديم أحدث تصاميم الفساتين لتناسب كل مناسبة خاصة بك.'; ?></p>
			<p class="footer-status"><?php echo $is_english ? 'Status' : 'الحالة'; ?> : <span class="status-pill <?php echo $is_open_now ? 'is-open' : 'is-closed'; ?>"><?php echo $is_open_now ? ($is_english ? 'Open' : 'مفتوح') : ($is_english ? 'Closed' : 'مغلق'); ?></span></p>
			<p class="footer-open-hours"><strong><?php echo $is_english ? 'Working Hours' : 'ساعات العمل'; ?>:</strong> <?php echo $is_english ? 'Saturday – Friday: 11:00 AM – 7:00 PM' : 'السبت – الجمعة: 11:00 ص – 7:00 م'; ?></p>
			<div class="footer-contact-row">
				<a href="<?php echo esc_url(home_url($locale_prefix . '/contact-us')); ?>"><?php echo $is_english ? 'Contact Us' : 'تواصلي معنا'; ?></a>
				<a href="tel:+201050874255"><?php echo $is_english ? 'Direct Call' : 'اتصال مباشر'; ?></a>
			</div>
		</div>
		<div class="footer-col">
			<h5><?php echo $is_english ? 'Quick Links' : 'روابط سريعة'; ?></h5>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url(home_url($locale_prefix)); ?>"><?php echo $is_english ? 'Home' : 'الرئيسية'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/blog')); ?>"><?php echo $is_english ? 'Blog' : 'المدونة'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>"><?php echo $is_english ? 'Shop Dresses Now' : 'تسوقي الفساتين الآن'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>"><?php echo $is_english ? 'Shop' : 'المتجر'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/marketplace')); ?>"><?php echo $is_english ? 'Marketplace' : 'الماركت بليس'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/categories')); ?>"><?php echo $is_english ? 'Categories' : 'الأقسام'; ?></a></li>
				<li><a href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener"><?php echo $is_english ? 'Sell Your Dress' : 'بيعي فستانك'; ?></a></li>
				<li><a href="<?php echo esc_url($account_url); ?>" target="_blank" rel="noopener"><?php echo $is_english ? 'My Account' : 'حسابي'; ?></a></li>
			</ul>
		</div>
		<div class="footer-col">
			<h5><?php echo $is_english ? 'Official Info' : 'معلومات رسمية'; ?></h5>
			<ul class="footer-links">
				<li><a href="https://maps.app.goo.gl/MCdcFEcFoR4tEjpT8" target="_blank" rel="noopener"><?php echo $is_english ? '1 Nabil Khalil St, Nasr City, Cairo, Egypt' : '1 شارع نبيل خليل، مدينة نصر، القاهرة، مصر'; ?></a></li>
				<li><a href="tel:+201050874255">+2 010-5087-4255</a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/contact-us')); ?>"><?php echo $is_english ? 'Contact Us' : 'تواصل معنا'; ?></a></li>
			</ul>
		</div>
		<div class="footer-col">
			<h5><?php echo $is_english ? 'Policies & Legal' : 'سياسات وقوانين'; ?></h5>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/about-us')); ?>"><?php echo $is_english ? 'About Us' : 'من نحن'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/privacy-policy')); ?>"><?php echo $is_english ? 'Privacy Policy' : 'سياسة الخصوصية'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/terms-conditions')); ?>"><?php echo $is_english ? 'Terms & Conditions' : 'الشروط والأحكام'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/marketplace-policy')); ?>"><?php echo $is_english ? 'Marketplace Policy' : 'سياسة الماركت بليس'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/refund-return-policy')); ?>"><?php echo $is_english ? 'Refund & Return Policy' : 'سياسة الاسترجاع والاستبدال'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/faq')); ?>"><?php echo $is_english ? 'FAQ' : 'الأسئلة الشائعة'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/shipping-delivery-policy')); ?>"><?php echo $is_english ? 'Shipping & Delivery Policy' : 'سياسة الشحن والتوصيل'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/cookie-policy')); ?>"><?php echo $is_english ? 'Cookie Policy' : 'سياسة ملفات الارتباط'; ?></a></li>
			</ul>
		</div>
	</div>
	<div class="footer-wrap footer-bottom">
		<span><?php echo $is_english ? ('All rights reserved © ' . esc_html(date('Y')) . ' Styliiiish | Powered by ') : ('جميع الحقوق محفوظة © ' . esc_html(date('Y')) . ' Styliiiish | تشغيل وتطوير '); ?><a href="https://websiteflexi.com/" target="_blank" rel="noopener">Website Flexi</a></span>
		<span><a href="<?php echo esc_url(home_url($locale_prefix)); ?>">styliiiish.com</a></span>
	</div>
	<div class="footer-wrap footer-mini-nav">
		<a href="<?php echo esc_url(home_url($locale_prefix)); ?>"><?php echo $is_english ? 'Home' : 'الرئيسية'; ?></a>
		<a href="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>"><?php echo $is_english ? 'Shop' : 'المتجر'; ?></a>
		<a href="<?php echo esc_url(home_url($locale_prefix . '/cart')); ?>"><?php echo $is_english ? 'Cart' : 'السلة'; ?></a>
		<a href="<?php echo esc_url($account_url); ?>" target="_blank" rel="noopener"><?php echo $is_english ? 'Account' : 'حسابي'; ?></a>
		<a href="<?php echo esc_url(home_url($locale_prefix . '/wishlist')); ?>"><?php echo $is_english ? 'Wishlist' : 'المفضلة'; ?></a>
	</div>
</footer>
<?php else : ?>
<footer id="wf_footer" class="wf_footer wf_footer--one clearfix">
	<div class="footer-shape">
		<img src="<?php echo esc_url(get_template_directory_uri());?>/assets/images/footer-shape.png" alt="" class="wow fadeInLeft" data-wow-delay="200ms" data-wow-duration="1500ms">
	</div>
	<?php 
		// Footer Top
		do_action('shopire_footer_top'); 
		
		// Footer Widget
		do_action('shopire_footer_widget');

		// Footer Copyright
		do_action('shopire_footer_bottom'); 	
	?>
</footer>
<?php endif; ?>
<?php 
	// Top Scroller
	do_action('shopire_top_scroller');
	do_action('shopire_footer_mobile_menu');
?>
<?php 
wp_footer(); ?>
</body>
</html>
