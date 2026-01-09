<?php
/**
 * 测试文件编码问题
 */

// 加载composer自动加载
require __DIR__ . '/vendor/autoload.php';

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 检查当前文件的编码
echo "=== 检查文件编码 ===\n";
$errorCodesFile = __DIR__ . '/src/config/error-codes.php';
$lines = file($errorCodesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// 读取错误码配置
$errorMapping = require $errorCodesFile;

// 检查中文信息
echo "\n=== 检查错误码中文信息 ===\n";
$errorKey = 'GetLocationRegionByH5TencentMapError';
if (isset($errorMapping[$errorKey])) {
    $msg = $errorMapping[$errorKey]['msg'];
    echo "错误码: {$errorKey}\n";
    echo "原始消息: {$msg}\n";
    echo "二进制长度: " . strlen($msg) . "\n";
    echo "字符长度: " . mb_strlen($msg) . "\n";
    echo "编码检测: " . mb_detect_encoding($msg, 'UTF-8,GBK,GB2312,BIG5') . "\n";
    echo "是否UTF-8: " . (mb_check_encoding($msg, 'UTF-8') ? '是' : '否') . "\n";

    // 尝试转换
    if (!mb_check_encoding($msg, 'UTF-8')) {
        $converted = mb_convert_encoding($msg, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        echo "\n转换后消息: {$converted}\n";
        echo "转换后编码: " . mb_detect_encoding($converted, 'UTF-8,GBK,GB2312,BIG5') . "\n";
    }
}

// 测试JSON编码
echo "\n=== 测试JSON编码 ===\n";
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'code' => 60030,
    'error' => $errorKey,
    'msg' => $errorMapping[$errorKey]['msg']
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
