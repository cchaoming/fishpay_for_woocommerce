<?php
if (!class_exists('WC_Payment_Gateway')) {
    return;
}

/**
 * Fishpay Wx Gateway.
 *
 * Provides a Cash on Delivery Payment Gateway.
 *
 * @class       Fishpay_Wx_Gateway
 * @extends     WC_Payment_Gateway
 * @version     1.0.0
 * @package     WooCommerce\Classes\Payment
 */
class Fishpay_Wx_Gateway extends WC_Payment_Gateway
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->id = 'fishpay_wx_gateway';
        $this->method_title = __('鱼码-微信扫码', 'fishpay');
        $this->icon = apply_filters('woocommerce_fishpay_wx_icon', '');
        $this->method_description = __('鱼码微信插件为WooCommerce.', 'fishpay');
        $this->has_fields = false;
        $this->init_form_fields();
        $this->init_settings();
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description') ?: __('鱼码微信插件为WooCommerce.', 'fishpay');
        $this->app_id = $this->get_option('app_id');
        $this->app_key = $this->get_option('app_key');
        // 添加支付网关设置保存的钩子
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));
        add_action('woocommerce_api_' . $this->id, array($this, 'fishpay_wx_gateway_callback'));
        //同步
        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
    }

    // 支付网关异步通知处理
    function fishpay_wx_gateway_callback()
    {
        // 处理异步通知逻辑
        $arr = $_POST;
        $order_no = $arr["order_no"];
        if (!$order_no) {
            die('fail no order no');
        }
        $subject = $arr["subject"];
        $pay_type = $arr["pay_type"];
        $total_fee = $arr["money"];
        $realmoney = $arr["realmoney"];
        $yuma_order = $arr["user_order_id"];
        $extra = urldecode($arr["extra"]);
        $sign = $arr["sign"];
        $app_id = $this->app_id;
        $app_secret = $this->app_key;
        //计算签名
        $mysign_forstr = "order_no=" . $order_no . "&subject=" . $subject . "&pay_type=" . $pay_type . "&money=" . $total_fee . "&realmoney=" . $realmoney . "&yuma_order=" . $yuma_order . "&app_id=" . $app_id . "&extra=" . $extra . "&" . $app_secret;
        $mysign = strtoupper(md5($mysign_forstr));
        if ($sign == $mysign) {
            //更新订单状态
            $order = wc_get_order($order_no);
            if ($order->get_status() != 'processing') {
                $order->update_status('processing', __('Fishpay Wechat payment success.', 'fishpay'));
            }
            $order->payment_complete();
            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page()
    {
        // 处理异步通知逻辑
        $arr = $_GET;
        $order_no = $arr["order_no"] ?? '';
        if (!$order_no) {
            return;
        }
        $subject = $arr["subject"];
        $pay_type = $arr["pay_type"];
        $total_fee = $arr["money"];
        $realmoney = $arr["realmoney"];
        $yuma_order = $arr["user_order_id"];
        $extra = urldecode($arr["extra"]);
        $sign = $arr["sign"];
        $app_id = $this->app_id;
        $app_secret = $this->app_key;
        //计算签名
        $mysign_forstr = "order_no=" . $order_no . "&subject=" . $subject . "&pay_type=" . $pay_type . "&money=" . $total_fee . "&realmoney=" . $realmoney . "&yuma_order=" . $yuma_order . "&app_id=" . $app_id . "&extra=" . $extra . "&" . $app_secret;
        $mysign = strtoupper(md5($mysign_forstr));
        if ($sign == $mysign) {
            // Remove cart.
            WC()->cart->empty_cart();
            echo wp_kses_post(wpautop(wptexturize('您的订单ID:' . $order_no . '鱼码-微信扫码支付成功！')));
        }
    }

    /**
     * 初始化支付网关设置字段
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'label' => __('启用/停用鱼码-微信扫码支付', 'fishpay'),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'safe_text',
                'description' => __('Payment method description that the customer will see on your checkout.', 'fishpay'),
                'default' => __('鱼码-微信扫码', 'fishpay'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('Payment method description that the customer will see on your website.', 'woocommerce'),
                'default' => __('鱼码-微信扫码支付', 'fishpay'),
                'desc_tip' => true,
            ),
            'app_id' => array(
                'title' => __('App Id', 'fishpay'),
                'type' => 'safe_text',
                'description' => __('your fishpay app id', 'fishpay'),
                'default' => '',
                'desc_tip' => true,
            ),
            'app_key' => array(
                'title' => __('App Secret Key', 'fishpay'),
                'type' => 'safe_text',
                'description' => __('your fishpay app key', 'fishpay'),
                'default' => '',
                'desc_tip' => true,
            )
        );


    }

    public function is_available()
    {
        if (!$this->app_id || !$this->app_key) {
            return false;
        }
        return parent::is_available();
    }

    /**
     * 处理支付
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        $total_amount = $order->get_total();
        if ($total_amount < 0.01) {
            echo "<script>alert('订单金额不能小于0.01');window.history.go(-1);</script>";
            return;
        }
        $total_amount = number_format($total_amount, 2, ".", "");
        $config = array(
            'gateway_url' => 'https://grant.damicms.com/pay/index',
            'app_id' => $this->app_id,
            'app_key' => $this->app_key,
            'pay_type' => 'wx',
            'notify_url' => WC()->api_request_url($this->id),
            'return_url' => $this->get_return_url($order)
        );
        $out_trade_no = $order_id; //$order->get_order_number();
        $subject = get_bloginfo('name');
        $extra = '';
        $unsign = "order_no=" . $out_trade_no . "&subject=" . $subject . "&pay_type=" . $config['pay_type'] . "&money=" . $total_amount . "&app_id=" . $config['app_id'] . "&extra=" . $extra . "&" . $config['app_key'];
        $sign = md5($unsign);
        $payRequestBuilder = array(
            'order_no' => $out_trade_no,
            'subject' => $subject,
            'pay_type' => $config['pay_type'],
            'money' => $total_amount,
            'app_id' => $config['app_id'],
            'extra' => $extra,
            'notify_url' => urlencode($config['notify_url']),
            'return_url' => urlencode($config['return_url']),
            'sign' => $sign
        );
        return array(
            'result' => 'success',
            'redirect' => $config['gateway_url'] . '?' . http_build_query($payRequestBuilder),
        );
    }
}
