<?php
/*
 * @Descripttion: 二维码生成服务
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-08 00:01:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-08 00:01:25
 * @FilePath: App.Service.V1.Qrcode.QrcodeFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\Tool\App\Service\V1\Qrcode;

use YouHuJun\Tool\App\Exceptions\CommonException;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * 二维码生成服务类
 *
 * 提供二维码生成功能,支持多种输出模式,去除框架和模型依赖
 */
class QrcodeFacadeService
{
    /**
     * 生成二维码
     *
     * @param array $config 配置参数
     * @param array $params 二维码参数
     * @param int $mode 输出模式: 1-保存到文件, 2-直接输出, 3-生成Data URI
     * @return mixed 根据mode不同返回不同结果
     * @throws CommonException
     */
    public function makeQrcode(array $config, array $params, int $mode = 1)
    {
        // 验证必填参数
        if (!isset($params['data']) || empty($params['data'])) {
            throw new CommonException('QrcodeDataRequired');
        }

        // 获取配置参数,设置默认值
        $redirectUrl = $params['data'];
        $logoPath = $config['logoPath'] ?? '';
        $noticeInfo = $config['noticeInfo'] ?? '二维码';
        $qrcodePath = $config['qrcodePath'] ?? '';
        $size = $config['size'] ?? 300;
        $margin = $config['margin'] ?? 10;
        $logoResizeToWidth = $config['logoResizeToWidth'] ?? 50;

        // 如果是保存模式,验证保存路径
        if ($mode === 1 && empty($qrcodePath)) {
            throw new CommonException('QrcodeSavePathRequired');
        }

        // 构建二维码
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $redirectUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: $margin,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: $logoPath,
            logoResizeToWidth: $logoResizeToWidth,
            logoPunchoutBackground: true,
            labelText: $noticeInfo,
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );

        $result = $builder->build();

        // 根据模式处理结果
        if ($mode === 1) {
            // 保存到文件
            $result->saveToFile($qrcodePath);

            // 检查文件是否保存成功
            if (!file_exists($qrcodePath)) {
                throw new CommonException('QrcodeSaveError');
            }

            return $qrcodePath;
        } elseif ($mode === 2) {
            // 直接输出
            header('Content-Type: ' . $result->getMimeType());
            echo $result->getString();
            exit;
        } elseif ($mode === 3) {
            // 生成Data URI
            return $result->getDataUri();
        } else {
            throw new CommonException('QrcodeModeError');
        }
    }
}
