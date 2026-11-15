<?php
/**
 * Plugin Name: حساب کاربری سیمرغ سایا
 * Description: طراحی راست‌چین حساب مشتری روی مسیرها و فرم‌های اصلی ووکامرس.
 * Version: 1.0.2
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 */
namespace SimorghSaya\Account;
defined('ABSPATH') || exit;

function icon($name, $size = 22) {
    $paths = [
        'dashboard'=>'<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'orders'=>'<path d="m12 2 9 5v10l-9 5-9-5V7zM3 7l9 5 9-5m-9 5v10M7.5 4.5l9 5"/>',
        'downloads'=>'<path d="M12 3v12m-5-5 5 5 5-5M4 16v4h16v-4"/>',
        'edit-address'=>'<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'edit-account'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 6 0 0 1 16 0v2"/>',
        'customer-logout'=>'<path d="M10 3H4v18h6m-1-9h12m-4-4 4 4-4 4"/>',
        'payment-methods'=>'<rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20M6 15h4"/>',
        'arrow'=>'<path d="M20 12H4m6-6-6 6 6 6"/>',
        'bag'=>'<path d="M4 8h16l1 13H3L4 8Z"/><path d="M8 9V6a4 4 0 0 1 8 0v3"/>',
        'help'=>'<path d="M4 13v-2a8 8 0 0 1 16 0v5a4 4 0 0 1-4 4h-3"/><rect x="2.5" y="10" width="4" height="7" rx="2"/><rect x="17.5" y="10" width="4" height="7" rx="2"/>',
    ];
    return '<svg class="ssa-icon" width="'.(int)$size.'" height="'.(int)$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.($paths[$name] ?? $paths['dashboard']).'</svg>';
}
function root_url(){return wc_get_page_permalink('myaccount');}
function shop_url(){return wc_get_page_permalink('shop');}
function account_page(){return function_exists('is_account_page') && is_account_page();}
function labels(){return ['dashboard'=>'پیشخوان','orders'=>'سفارش‌ها','downloads'=>'دانلودها','edit-address'=>'نشانی‌ها','edit-account'=>'مشخصات حساب','payment-methods'=>'روش‌های پرداخت','customer-logout'=>'خروج از حساب'];}
function greeting($user){$name=trim((string)$user->first_name);return $name !== '' ? 'سلام، '.$name : 'به حسابت خوش آمدی';}
add_action('wp_enqueue_scripts',function(){
    if (!function_exists('WC')) return;
    wp_enqueue_style('ssa-entry',plugins_url('assets/entry.css',__FILE__),[],'1.0.2');
    if(account_page()) wp_enqueue_style('ssa-account',plugins_url('assets/account.css',__FILE__),[],'1.0.2');
},100);

// Leave WooCommerce in control of nonces, customer isolation, redirects and form handlers.
add_filter('wc_get_template',function($path,$name){
    if (!account_page()) return $path;
    if($name==='myaccount/dashboard.php') return __DIR__.'/dashboard.php';
    if($name==='myaccount/navigation.php') return __DIR__.'/navigation.php';
    return $path;
},20,2);

add_filter('do_shortcode_tag', function($html,$tag){
    if ($tag!=='woocommerce_my_account' || !account_page() || is_admin() || is_feed() || (defined('REST_REQUEST') && REST_REQUEST)) return $html;
    $member=is_user_logged_in(); $ep=WC()->query->get_current_endpoint();
    $names=labels(); $names+=['lost-password'=>'بازیابی گذرواژه','view-order'=>'جزئیات سفارش','add-payment-method'=>'افزودن روش پرداخت','order-withdrawal'=>'درخواست انصراف'];
    $title=$member ? ($ep ? ($names[$ep] ?? wp_strip_all_tags(WC()->query->get_endpoint_title($ep))) : 'حساب کاربری من') : ($ep==='lost-password'?'بازیابی گذرواژه':'ورود به حساب کاربری');
    if (!$title) $title='حساب کاربری من';
    ob_start(); ?>
    <section id="ss-account" dir="rtl" class="<?php echo $member?'ssa-member':'ssa-guest'; ?>" aria-labelledby="ssa-title">
      <div class="ssa-head"><div><p class="ssa-kicker">فروشگاه سیمرغ سایا</p><h1 id="ssa-title"><?php echo esc_html($title); ?></h1></div>
        <a class="ssa-shop" href="<?php echo esc_url(shop_url()); ?>"><?php echo icon('bag',18); ?> فروشگاه <?php echo icon('arrow',16); ?></a></div>
      <?php if(!$member): ?><div class="ssa-auth"><div class="ssa-formbox"><?php echo $html; // Original shortcode markup. ?>
      <?php if($ep==='lost-password'): ?><a class="ssa-back" href="<?php echo esc_url(root_url()); ?>">بازگشت به ورود <?php echo icon('arrow',16); ?></a><?php endif; ?>
      </div><aside class="ssa-pass" aria-label="خدمات حساب کاربری"><div class="ssa-passmark" dir="ltr" aria-hidden="true"><i></i> SIMORGH SAYA</div><div class="ssa-passcopy"><small>حساب ورزشکار</small><h2>همراهِ هر تمرین</h2><p>سفارش‌هایت را پیگیری کن و نشانی‌ها و اطلاعات حسابت را یک‌جا در دسترس داشته باش.</p></div><div class="ssa-passfoot" aria-hidden="true"><span>تجهیزات ورزش‌های قدرتی</span><b dir="ltr">S / S</b></div></aside></div>
      <?php else: echo $html; endif; ?>
      <div class="ssa-support"><?php echo icon('help',18); ?><span>برای پیگیری خرید به کمک نیاز داری؟</span><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">تماس با پشتیبانی <?php echo icon('arrow',15); ?></a></div>
    </section><?php return ob_get_clean();
},20,2);

// Turn the existing footer label into a real link; add an account entry beside the existing cart.
add_filter('elementor/widget/render_content',function($content,$widget){
    if(!function_exists('wc_get_page_permalink')) return $content;
    $id=$widget->get_id(); $url=esc_url(root_url());
    if($id==='6752ca1' && strpos($content,'حساب کاربری')!==false)
        return preg_replace('~(<li\b[^>]*>\s*)(<span class="elementor-icon-list-text">حساب کاربری</span>)(\s*</li>)~u','$1<a href="'.$url.'">$2</a>$3',$content,1);
    if($id!=='caa2257' || strpos($content,'ssa-entry-link')!==false) return $content;
    $needle='<div class="elementor-menu-cart__toggle elementor-button-wrapper">';
    $entry='<a class="ssa-entry-link" href="'.$url.'" aria-label="حساب کاربری" title="حساب کاربری">'.icon('edit-account',20).'</a>';
    return str_replace($needle,$needle.$entry,$content);
},20,2);
