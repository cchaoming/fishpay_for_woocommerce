<?php
/*
Plugin Name: 鱼码支付Plugin
Description: 鱼码支付插件WordPress，官网:https://grant.damicms.com
Version: 1.0.0
*/
function fishpay_plugin_init() {
    // 检查 WooCommerce 是否已安装并激活
    if (!class_exists('WooCommerce')) {
        // 如果 WooCommerce 未安装，则显示错误消息
        add_action('admin_notices', 'fishpay_plugin_missing_wc_notice');
    }else{
        // 加载 WooCommerce 支付网关
        include_once(dirname(__FILE__).'/woocommerce/fishpay-wx-gateway.php');
        include_once(dirname(__FILE__).'/woocommerce/fishpay-alipay-gateway.php');
    }
}

function fishpay_plugin_missing_wc_notice() {
    echo '<div class="error"><p>' . __('fishpay插件需要安装并激活WooCommerce.', 'fishpay') . '</p></div>';
}

// 注册自定义支付网关
function fishpay_add_to_gateways($gateways)
{
    $gateways[] = 'Fishpay_Wx_Gateway';
    $gateways[] = 'Fishpay_Alipay_Gateway';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'fishpay_add_to_gateways');
// 初始化函数
add_action('plugins_loaded', 'fishpay_plugin_init',0);
//block支持
add_action( 'woocommerce_blocks_loaded', 'fishpay_gateway_block_support' );
function fishpay_gateway_block_support() {
    require_once __DIR__ . '/woocommerce/fishpay-alipay-gateway-blocks-support.php';
    require_once __DIR__ . '/woocommerce/fishpay-wx-gateway-blocks-support.php';
    // 注册我们刚才引入的 PHP 类
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
            $payment_method_registry->register( new Fishpay_Alipay_Gateway_Blocks_Support() );
            $payment_method_registry->register( new Fishpay_Wx_Gateway_Blocks_Support() );
        }
    );
}


