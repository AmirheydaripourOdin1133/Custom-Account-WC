<?php
namespace SimorghSaya\Account;
defined('ABSPATH') || exit;
do_action('woocommerce_before_account_navigation'); $local=labels();
?>
<nav class="woocommerce-MyAccount-navigation ssa-nav" aria-label="بخش‌های حساب کاربری">
  <div class="ssa-nav-title"><?php echo icon('edit-account',18); ?> <span>میز کاربری</span></div>
  <ul><?php foreach(wc_get_account_menu_items() as $key=>$label): $active=wc_is_current_account_menu_item($key); ?>
    <li class="<?php echo esc_attr(wc_get_account_menu_item_classes($key)); ?>"><a href="<?php echo esc_url(wc_get_account_endpoint_url($key)); ?>" <?php if($active) echo 'aria-current="page"'; ?>><?php echo icon($key,19); ?><span><?php echo esc_html($local[$key] ?? $label); ?></span></a></li>
  <?php endforeach; ?></ul>
</nav>
<?php do_action('woocommerce_after_account_navigation'); ?>
