<?php

namespace YouHuJun\Tool\App\Exceptions;

/**
 * Class CommonException
 *
 * 这是一个通用的业务异常类，用于在应用程序中表示预期的业务逻辑错误。
 * 它继承自 PHP 的基础 Exception 类，以便可以被标准的 try-catch 块捕获。
 *
 * @package YouHuJun\Tool\Exceptions
 */
class CommonException extends \Exception
{
    // 你可以在这里添加自定义的属性或方法，例如错误码、错误级别等。
    // 例如，一个 $code 属性来存储业务错误码。

    /**
     * CommonException constructor.
     *
     * @param string         $message 异常消息
     * @param int            $code    异常代码
     * @param \Throwable|null $previous 先前的异常
     */
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    // 例如，你可以添加一个方法来获取一个格式化的错误信息数组
    public function getErrorDetails(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}