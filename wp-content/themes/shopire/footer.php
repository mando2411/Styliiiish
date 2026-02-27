</div></div>
<?php
	$request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
	$request_path = parse_url($request_uri, PHP_URL_PATH);
	$request_path = is_string($request_path) ? $request_path : '/';
	$is_english = preg_match('#^/en(?:/|$)#i', $request_path) === 1;
	$locale_prefix = $is_english ? '/en' : '/ar';
	$account_url = $is_english ? home_url('/my-account/') : home_url('/ar/%d8%ad%d8%b3%d8%a7%d8%a8%d9%8a/');
	$is_account_path = preg_match('#^/(?:my-account|en/my-account|ar/حسابي|ara/حسابي)(?:/|$)#u', $request_path) === 1;
	$is_account_layout = $is_account_path || (function_exists('is_account_page') && is_account_page());
?>

<?php if ($is_account_layout) : ?>
<style>
	.laravel-account-footer{margin-top:8px;background:#0f1a2a;color:#fff;border-top:4px solid #d51522}
	.laravel-account-wrap{width:min(1180px,92%);margin:0 auto}
	.laravel-account-footer-grid{display:grid;grid-template-columns:1.2fr 1fr 1fr;gap:18px;padding:34px 0 22px}
	.laravel-account-footer-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:16px}
	.laravel-account-footer h4,.laravel-account-footer h5{margin:0 0 10px;color:#fff}
	.laravel-account-footer p,.laravel-account-footer a{color:#b8c2d1;text-decoration:none}
	.laravel-account-footer a:hover{color:#fff}
	.laravel-account-footer-links{list-style:none;margin:0;padding:0;display:grid;gap:7px}
	.laravel-account-footer-bottom{border-top:1px solid rgba(255,255,255,.14);padding:12px 0 20px;display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;color:#b8c2d1;font-size:13px}
	@media (max-width:900px){.laravel-account-footer-grid{grid-template-columns:1fr}}
</style>
<footer class="laravel-account-footer">
	<div class="laravel-account-wrap laravel-account-footer-grid">
		<div class="laravel-account-footer-card">
			<img src="https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png" alt="Styliiiish" style="height:56px;width:auto;max-width:100%;margin-bottom:10px;">
			<h4><?php echo $is_english ? 'Styliiiish Fashion House' : 'ستيليش فاشون هاوس'; ?></h4>
			<p><?php echo $is_english ? 'We are passionate about offering the latest dress designs for every special occasion.' : 'نعمل بشغف على تقديم أحدث تصاميم الفساتين لتناسب كل مناسبة خاصة بك.'; ?></p>
		</div>
		<div class="laravel-account-footer-card">
			<h5><?php echo $is_english ? 'Quick Links' : 'روابط سريعة'; ?></h5>
			<ul class="laravel-account-footer-links">
				<li><a href="<?php echo esc_url(home_url($locale_prefix)); ?>"><?php echo $is_english ? 'Home' : 'الرئيسية'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/shop')); ?>"><?php echo $is_english ? 'Shop' : 'المتجر'; ?></a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/blog')); ?>"><?php echo $is_english ? 'Blog' : 'المدونة'; ?></a></li>
				<li><a href="<?php echo esc_url($account_url); ?>"><?php echo $is_english ? 'My Account' : 'حسابي'; ?></a></li>
			</ul>
		</div>
		<div class="laravel-account-footer-card">
			<h5><?php echo $is_english ? 'Contact' : 'تواصل معنا'; ?></h5>
			<ul class="laravel-account-footer-links">
				<li><a href="tel:+201050874255">+20 010 5087 4255</a></li>
				<li><a href="<?php echo esc_url(home_url($locale_prefix . '/contact-us')); ?>"><?php echo $is_english ? 'Contact Us' : 'تواصل معنا'; ?></a></li>
			</ul>
		</div>
	</div>
	<div class="laravel-account-wrap laravel-account-footer-bottom">
		<span><?php echo esc_html(date('Y')); ?> © Styliiiish</span>
		<span><a href="<?php echo esc_url(home_url($locale_prefix)); ?>">styliiiish.com</a></span>
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
