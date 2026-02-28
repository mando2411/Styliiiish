<?php
/**
 * Theme functions and definitions
 *
 * @package eKart
 */

/**
 * After setup theme hook
 */
function ekart_theme_setup(){
    /*
     * Make child theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_child_theme_textdomain( 'ekart' );	
}
add_action( 'after_setup_theme', 'ekart_theme_setup' );

/**
 * Load assets.
 */

function ekart_theme_css() {
	wp_enqueue_style( 'ekart-parent-theme-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'ekart_theme_css', 99);

require get_stylesheet_directory() . '/theme-functions/controls/class-customize.php';

/**
 * Import Options From Parent Theme
 *
 */
function ekart_parent_theme_options() {
	$ekart_mods = get_option( 'theme_mods_shopire' );
	if ( ! empty( $ekart_mods ) ) {
		foreach ( $ekart_mods as $ekart_mod_k => $ekart_mod_v ) {
			set_theme_mod( $ekart_mod_k, $ekart_mod_v );
		}
	}
}
add_action( 'after_switch_theme', 'ekart_parent_theme_options' );

/**
 * Fix Arabic my-account endpoint 404s by redirecting endpoint tails
 * to WooCommerce my-account endpoint paths while preserving query args.
 */
function ekart_fix_arabic_myaccount_endpoints_404() {
	if ( is_admin() ) {
		return;
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/';
	$request_path = parse_url( $request_uri, PHP_URL_PATH );
	$request_path = is_string( $request_path ) ? $request_path : '/';
	$normalized   = rawurldecode( strtolower( rtrim( $request_path, '/' ) ) );
	if ( $normalized === '' ) {
		$normalized = '/';
	}

	$arabic_account_bases = [
		'/ar/حسابي',
		'/ara/حسابي',
		'/حسابي',
	];

	$endpoint_tail = '';
	foreach ( $arabic_account_bases as $account_base ) {
		if ( $normalized === $account_base ) {
			return;
		}

		$prefix = $account_base . '/';
		if ( str_starts_with( $normalized, $prefix ) ) {
			$endpoint_tail = ltrim( substr( $normalized, strlen( $prefix ) ), '/' );
			break;
		}
	}

	if ( $endpoint_tail === '' ) {
		return;
	}

	$my_account_permalink = function_exists( 'wc_get_page_permalink' )
		? (string) wc_get_page_permalink( 'myaccount' )
		: (string) home_url( '/my-account/' );

	$my_account_path = wp_parse_url( $my_account_permalink, PHP_URL_PATH );
	$my_account_path = is_string( $my_account_path ) ? rtrim( $my_account_path, '/' ) : '/my-account';
	if ( $my_account_path === '' ) {
		$my_account_path = '/my-account';
	}

	$target = home_url( $my_account_path . '/' . $endpoint_tail . '/' );

	if ( ! empty( $_GET ) ) {
		$sanitized_query = [];
		foreach ( $_GET as $key => $value ) {
			$clean_key = sanitize_key( (string) $key );
			if ( $clean_key === '' ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$sanitized_query[ $clean_key ] = array_map(
					static function ( $item ) {
						return sanitize_text_field( wp_unslash( (string) $item ) );
					},
					$value
				);
			} else {
				$sanitized_query[ $clean_key ] = sanitize_text_field( wp_unslash( (string) $value ) );
			}
		}

		if ( ! empty( $sanitized_query ) ) {
			$target = add_query_arg( $sanitized_query, $target );
		}
	}

	wp_safe_redirect( $target, 302 );
	exit;
}
add_action( 'template_redirect', 'ekart_fix_arabic_myaccount_endpoints_404', 1 );

/**
 * Replace broken legacy asset host on my-account pages.
 */
function ekart_rewrite_legacy_assets_host_for_account_pages() {
	if ( is_admin() ) {
		return;
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/';
	$request_path = parse_url( $request_uri, PHP_URL_PATH );
	$request_path = is_string( $request_path ) ? $request_path : '/';
	$normalized   = rawurldecode( strtolower( rtrim( $request_path, '/' ) ) );

	$account_prefixes = [
		'/my-account',
		'/en/my-account',
		'/ar/حسابي',
		'/ara/حسابي',
		'/حسابي',
	];

	$is_account_request = false;
	foreach ( $account_prefixes as $prefix ) {
		if ( $normalized === $prefix || str_starts_with( $normalized, $prefix . '/' ) ) {
			$is_account_request = true;
			break;
		}
	}

	if ( ! $is_account_request ) {
		return;
	}

	ob_start(
		static function ( $html ) {
			if ( ! is_string( $html ) || $html === '' ) {
				return $html;
			}

			$replacements = [
				'https://styliiiish.com/' => 'https://styliiiish.com/',
				'http://l.styliiiish.com/'  => 'https://styliiiish.com/',
				'//l.styliiiish.com/'       => '//styliiiish.com/',
			];

			return strtr( $html, $replacements );
		}
	);
}
add_action( 'template_redirect', 'ekart_rewrite_legacy_assets_host_for_account_pages', 2 );

function ekart_output_no_translation_guard_script() {
	if ( is_admin() ) {
		return;
	}
	?>
	<script id="ekart-no-translation-guard">
	(function(){
		var markerSelector='[data-no-translation]';
		var arabicPattern=/[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF]/;
		var skipTagMap={SCRIPT:1,STYLE:1,NOSCRIPT:1,TEXTAREA:1,INPUT:1,OPTION:1};
		var arabicTextLocks=new WeakMap();
		var arabicElementLocks=new WeakMap();
		var markNode=function(node){
			if(!node||node.nodeType!==1){return;}
			if(node.closest&&node.closest('[data-allow-ar-translation]')){return;}
			node.setAttribute('translate','no');
			node.classList.add('notranslate','trp-no-translate');
		};
		var lockElementArabicText=function(element){
			if(!element||element.nodeType!==1){return;}
			if(skipTagMap[element.tagName]){return;}
			if(element.closest&&element.closest('[data-allow-ar-translation]')){return;}
			if(element.children&&element.children.length===0){
				var textValue=String(element.textContent||'').trim();
				if(textValue!==''&&arabicPattern.test(textValue)&&!arabicElementLocks.has(element)){
					arabicElementLocks.set(element,textValue);
					markNode(element);
				}
			}
		};
		var restoreLockedElement=function(element){
			if(!element||element.nodeType!==1){return;}
			var original=arabicElementLocks.get(element);
			if(typeof original==='string'){
				var current=String(element.textContent||'').trim();
				if(current!==original){
					element.textContent=original;
				}
				markNode(element);
			}
		};
		var markArabicTextContainers=function(root){
			if(!root){return;}
			if(root.nodeType===3){
				var textValue=String(root.nodeValue||'').trim();
				if(textValue!==''&&arabicPattern.test(textValue)&&root.parentElement){
					if(!arabicTextLocks.has(root)){
						arabicTextLocks.set(root,String(root.nodeValue||''));
					}
					lockElementArabicText(root.parentElement);
					markNode(root.parentElement);
				}
				return;
			}
			if(root.nodeType!==1){return;}
			var startNode=root;
			var walker=document.createTreeWalker(startNode,NodeFilter.SHOW_TEXT,null);
			var current;
			while((current=walker.nextNode())){
				var parent=current.parentElement;
				if(!parent||skipTagMap[parent.tagName]){continue;}
				var value=String(current.nodeValue||'').trim();
				if(value!==''&&arabicPattern.test(value)){
					if(!arabicTextLocks.has(current)){
						arabicTextLocks.set(current,String(current.nodeValue||''));
					}
					lockElementArabicText(parent);
					markNode(parent);
				}
			}
		};
		var markTree=function(root){
			if(!root||root.nodeType!==1){return;}
			if(root.matches&&root.matches(markerSelector)){markNode(root);}
			if(root.querySelectorAll){
				root.querySelectorAll(markerSelector).forEach(markNode);
			}
			markArabicTextContainers(root);
		};
		markTree(document.documentElement||document.body);
		markArabicTextContainers(document.body||document.documentElement);
		if(!window.MutationObserver){return;}
		var observer=new MutationObserver(function(mutations){
			mutations.forEach(function(mutation){
				if(mutation.type==='characterData'){
					var textNode=mutation.target;
					var lockedText=arabicTextLocks.get(textNode);
					if(typeof lockedText==='string'){
						if(String(textNode.nodeValue||'')!==lockedText){
							textNode.nodeValue=lockedText;
						}
						if(textNode.parentElement){
							restoreLockedElement(textNode.parentElement);
						}
						return;
					}
					if(arabicPattern.test(String(textNode.nodeValue||''))){
						arabicTextLocks.set(textNode,String(textNode.nodeValue||''));
						if(textNode.parentElement){
							lockElementArabicText(textNode.parentElement);
							markNode(textNode.parentElement);
						}
					}
					return;
				}
				restoreLockedElement(mutation.target&&mutation.target.nodeType===1?mutation.target:null);
				if(!mutation.addedNodes||!mutation.addedNodes.length){return;}
				mutation.addedNodes.forEach(function(added){
					markTree(added);
					markArabicTextContainers(added);
					if(added&&added.nodeType===1){
						restoreLockedElement(added);
					}
				});
			});
		});
		observer.observe(document.documentElement,{childList:true,subtree:true,characterData:true});
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'ekart_output_no_translation_guard_script', 0 );