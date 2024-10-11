<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
final class Fishpay_Alipay_Gateway_Blocks_Support extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'fishpay_alipay_gateway'; // payment gateway id
    public function initialize() {
        // get payment gateway settings
        $this->settings = get_option( "woocommerce_{$this->name}_settings", array() );
        // you can also initialize your payment gateway here
        // $gateways = WC()->payment_gateways->payment_gateways();
        // $this->gateway  = $gateways[ $this->name ];
    }
    public function is_active() {
        return ! empty( $this->settings[ 'enabled' ] ) && 'yes' === $this->settings[ 'enabled' ];
    }
    public function get_payment_method_script_handles() {
        wp_register_script(
            'wc-fishpay-alipay-blocks-integration',
            plugin_dir_url( __DIR__ ) . 'build/fishpay.js',
            array(
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
            ),
            null, // or time() or filemtime( ... ) to skip caching
            true
        );
        return array( 'wc-fishpay-alipay-blocks-integration' );
    }
    public function get_payment_method_data() {
        return array(
            'title'        => $this->get_setting( 'title' ),
            'description'  => $this->get_setting( 'description' ),
            //if $this->gateway was initialized on line 15
            // 'supports'  => $this->get_setting( 'supports' ),
            // example of getting a public key
            // 'publicKey' => $this->get_publishable_key(),
        );
    }
}