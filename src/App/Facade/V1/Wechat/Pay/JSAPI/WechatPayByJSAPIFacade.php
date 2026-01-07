<?php
namespace YouHuJun\Tool\App\Facade\V1\Wechat\Pay\JSAPI;

use YouHuJun\Tool\App\Service\V1\Wechat\Pay\JSAPI\WechatPayByJSAPIFacadeService;
use BadMethodCallException;

/**
 * 微信JSAPI支付门面类
 *
 * @see \YouHuJun\Tool\App\Service\V1\Wechat\Pay\JSAPI\WechatPayByJSAPIFacadeService
 */
class WechatPayByJSAPIFacade
{
    protected static $instance;

    private function __construct() {}
    private function __clone() {}

    /**
     * 设置服务实例(用于依赖注入或测试)
     *
     * @param WechatPayByJSAPIFacadeService $instance 服务实例
     * @return void
     */
    public static function setInstance(WechatPayByJSAPIFacadeService $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * 清除服务实例
     *
     * @return void
     */
    public static function clearInstance(): void
    {
        static::$instance = null;
    }

    /**
     * 获取服务实例
     *
     * @return WechatPayByJSAPIFacadeService 服务实例
     */
    protected static function getInstance(): WechatPayByJSAPIFacadeService
    {
        if (static::$instance === null) {
            static::$instance = new WechatPayByJSAPIFacadeService();
        }
        return static::$instance;
    }

    /**
     * JSAPI下单
     *
     * @param array $config 支付配置,包含merchantId、merchantSerialNumber、merchantPrivateKeyPath、wechatpayCertificatePath、officialAppid、notifyUrl
     * @param array $orderData 订单数据
     * @return array 返回预支付ID和签名信息,包含prepay_id、appId、timeStamp、nonceStr、paySign
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function prePayOrder(array $config, array $orderData): array
    {
        return static::getInstance()->prePayOrder($config, $orderData);
    }

    /**
     * 查询订单
     *
     * @param array $config 支付配置
     * @param string $outTradeNo 商户订单号
     * @return array 订单信息
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function queryOrder(array $config, string $outTradeNo): array
    {
        return static::getInstance()->queryOrder($config, $outTradeNo);
    }

    /**
     * 关闭订单
     *
     * @param array $config 支付配置
     * @param string $outTradeNo 商户订单号
     * @return bool
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function closeOrder(array $config, string $outTradeNo): bool
    {
        return static::getInstance()->closeOrder($config, $outTradeNo);
    }

    /**
     * 申请退款
     *
     * @param array $config 支付配置
     * @param array $refundData 退款数据
     * @return array 退款结果
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function refund(array $config, array $refundData): array
    {
        return static::getInstance()->refund($config, $refundData);
    }

    /**
     * 查询退款
     *
     * @param array $config 支付配置
     * @param string $outRefundNo 商户退款单号
     * @return array 退款信息
     * @throws \YouHuJun\Tool\App\Exceptions\CommonException
     */
    public static function queryRefund(array $config, string $outRefundNo): array
    {
        return static::getInstance()->queryRefund($config, $outRefundNo);
    }

    /**
     * 魔术方法调用(兼容未显式声明的方法)
     *
     * @param string $method 方法名
     * @param array $parameters 参数数组
     * @return mixed
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $instance = static::getInstance();
        if (!method_exists($instance, $method)) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s()', get_class($instance), $method)
            );
        }
        return $instance->$method(...$parameters);
    }
}
