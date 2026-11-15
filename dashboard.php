<?php
namespace SimorghSaya\Account;
defined('ABSPATH') || exit;
$uid=get_current_user_id();
if(!$uid) return;
$order_list=wc_get_orders(['customer_id'=>$uid,'limit'=>1,'orderby'=>'date','order'=>'DESC','type'=>'shop_order','status'=>array_keys(wc_get_order_statuses())]);
$last=$order_list[0] ?? null;
$items=wc_get_account_menu_items(); $shortcuts=[
 'orders'=>['سفارش‌های من','مشاهده و پیگیری خریدها'],
 'edit-address'=>['دفتر نشانی‌ها','مدیریت نشانی‌های تحویل'],
 'edit-account'=>['مشخصات حساب','ویرایش اطلاعات و گذرواژه'],
];
?>
<div class="ssa-dash">
 <div class="ssa-welcome"><div><span class="ssa-eyebrow">پیشخوان شخصی</span><h2><?php echo esc_html(greeting(wp_get_current_user())); ?></h2><p>از اینجا خریدها و اطلاعات حسابت را مدیریت کن.</p></div><span class="ssa-welcome-art" aria-hidden="true"></span></div>
 <div class="ssa-shortcuts"><?php foreach($shortcuts as $key=>$copy): if(!isset($items[$key])) continue; ?>
    <a class="ssa-shortcut" href="<?php echo esc_url(wc_get_account_endpoint_url($key)); ?>"><i><?php echo icon($key); ?></i><strong><?php echo esc_html($copy[0]); ?></strong><span><?php echo esc_html($copy[1]); ?></span><?php echo icon('arrow',17); ?></a>
 <?php endforeach; ?></div>
 <section class="ssa-order"><div class="ssa-sectionhead"><h3>آخرین سفارش</h3><?php if(isset($items['orders'])): ?><a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">همهٔ سفارش‌ها <?php echo icon('arrow',15); ?></a><?php endif; ?></div>
 <?php if($last && (int)$last->get_customer_id()===$uid): ?>
 <div class="ssa-orderline"><div><strong>سفارش <bdi>#<?php echo esc_html($last->get_order_number()); ?></bdi></strong><?php if($last->get_date_created()): ?><small><?php echo esc_html(wc_format_datetime($last->get_date_created(),wc_date_format())); ?></small><?php endif; ?></div><span class="ssa-status"><?php echo esc_html(wc_get_order_status_name($last->get_status())); ?></span><span><?php echo wp_kses_post($last->get_formatted_order_total()); ?></span><a class="ssa-textlink" href="<?php echo esc_url($last->get_view_order_url()); ?>">جزئیات سفارش <?php echo icon('arrow',15); ?></a></div>
 <?php else: ?><div class="ssa-empty"><i><?php echo icon('bag',28); ?></i><div><strong>هنوز سفارشی ثبت نکرده‌ای</strong><p>بعد از اولین خرید، وضعیت سفارشت را همین‌جا می‌بینی.</p></div><a class="ssa-textlink" href="<?php echo esc_url(shop_url()); ?>">دیدن محصولات <?php echo icon('arrow',15); ?></a></div><?php endif; ?></section>
</div>
<?php do_action('woocommerce_account_dashboard'); do_action('woocommerce_before_my_account'); do_action('woocommerce_after_my_account'); ?>
