<?php

namespace YouHuJun\Tool\App\Exceptions;

/**
 * Class CommonException
 *
 * 这是一个通用的业务异常类，用于在应用程序中表示预期的业务逻辑错误。
 * 抛出异常时返回标准格式的数组：['code'=>错误码, 'error'=>错误标识, 'msg'=>错误信息]
 *
 * @package YouHuJun\Tool\Exceptions
 */
class CommonException extends \Exception
{
    /**
     * 错误标识
     * @var string
     */
    protected $errorKey;

    /**
     * 错误信息
     * @var string
     */
    protected $message;

    /**
     * 错误码映射表
     * 从配置文件加载,支持运行时扩展
     * @var array
     */
    protected static $errorMapping = [];

    /**
     * 配置文件路径
     * @var string
     */
    protected static $configFile = __DIR__ . '/../../config/error-codes.php';

    /**
     * 是否已加载配置文件
     * @var bool
     */
    protected static $configLoaded = false;

    /**
     * 初始化加载配置文件
     *
     * @return void
     */
    protected static function loadConfig(): void
    {
        if (self::$configLoaded) {
            return;
        }

        if (file_exists(self::$configFile)) {
            self::$errorMapping = require self::$configFile;
        }

        self::$configLoaded = true;
    }

    /**
     * 设置配置文件路径
     * 用于自定义错误码配置文件位置
     *
     * @param string $configFile 配置文件绝对路径
     * @return void
     */
    public static function setConfigFile(string $configFile): void
    {
        self::$configFile = $configFile;
        self::$configLoaded = false; // 重置加载状态
    }

    /**
     * 注册自定义错误码
     * 用于在运行时动态添加错误码
     *
     * @param array $mapping 错误码映射,格式为 ['errorKey' => ['code'=>..., 'error'=>..., 'msg'=>...], ...]
     * @param bool $merge 是否合并到现有配置,默认 true
     * @return void
     */
    public static function registerErrorMapping(array $mapping, bool $merge = true): void
    {
        self::loadConfig();

        if ($merge) {
            self::$errorMapping = array_merge(self::$errorMapping, $mapping);
        } else {
            self::$errorMapping = $mapping;
        }
    }

    /**
     * 获取所有错误码映射
     *
     * @return array
     */
    public static function getErrorMapping(): array
    {
        self::loadConfig();
        return self::$errorMapping;
    }

    /**
     * 获取错误码配置
     *
     * @param string $errorKey 错误标识
     * @return array|null 返回错误配置数组,如果不存在返回 null
     */
    protected function getErrorConfig(string $errorKey): ?array
    {
        self::loadConfig();

        return self::$errorMapping[$errorKey] ?? null;
    }

    /**
     * 构造函数
     *
     * @param string|null $errorKey 错误标识
     * @param \Throwable|null $previous 先前的异常
     */
    public function __construct(string|null $errorKey = null, \Throwable|null $previous = null)
    {
        if ($errorKey) {
            $this->errorKey = $errorKey;
            $errorConfig = $this->getErrorConfig($errorKey);

            if ($errorConfig) {
                $this->code = $errorConfig['code'] ?? 0;
                $this->message = $errorConfig['msg'] ?? '未知错误';
                // 如果配置中有 error 字段,使用配置的,否则使用传入的 errorKey
                $this->errorKey = $errorConfig['error'] ?? $errorKey;
            } else {
                // 错误码不存在,使用默认值
                $this->errorKey = 'CodeError';
                $this->message = '错误码不存在';
                $this->code = 10001;
            }
        } else {
            // 默认服务器异常
            $this->errorKey = 'ServerError';
            $this->message = '服务器异常';
            $this->code = 10000;
        }

        parent::__construct($this->message, $this->code, $previous);
    }

    /**
     * 获取错误标识
     *
     * @return string
     */
    public function getErrorKey(): string
    {
        return $this->errorKey;
    }

    /**
     * 获取格式化的错误信息(用于返回给客户端)
     *
     * @return array 返回格式: ['code'=>错误码, 'error'=>错误标识, 'msg'=>错误信息]
     */
    public function getErrorResponse(): array
    {
        // 确保UTF-8编码,防止中文乱码
        $response = [
            'code' => $this->code,
            'error' => $this->errorKey,
            'msg' => $this->message
        ];

        // 对字符串进行UTF-8编码检查和转换
        array_walk_recursive($response, function(&$value) {
            if (is_string($value)) {
                // 确保字符串是UTF-8编码
                if (!mb_check_encoding($value, 'UTF-8')) {
                    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
                }
            }
        });

        return $response;
    }

    /**
     * 获取JSON格式的错误响应(带UTF-8编码保护)
     *
     * @return string 返回JSON字符串
     */
    public function getErrorResponseJson(): string
    {
        $response = $this->getErrorResponse();
        // 使用 JSON_UNESCAPED_UNICODE 选项保持中文字符不被转义
        return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 输出JSON格式的错误响应并设置正确的响应头
     *
     * @return void
     */
    public function sendErrorResponse(): void
    {
        // 设置响应头,指定UTF-8编码
        header('Content-Type: application/json; charset=utf-8');
        echo $this->getErrorResponseJson();
        exit;
    }

    /**
     * 获取详细的错误信息(用于日志记录)
     *
     * @return array
     */
    public function getErrorDetails(): array
    {
        return [
            'code' => $this->code,
            'error' => $this->errorKey,
            'message' => $this->message,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}