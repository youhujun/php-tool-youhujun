# youhujun/php-tool-youhujun

<div align="center">
   <a target="_blank" href="https://www.youhu.club/">📑 阅读文档</a>|  <a target="_blank" href="https://www.youhujun.com/">🌐 参考博客</a> 
</div>

## 项目简介

[php-tool-youhujun](https://gitcode.com/youhujun/) 是为了提高php开发效率,无需关乎基础通用功能的实现,将精力聚焦于业务逻辑的开发而催生的.

## 项目功能

本工具包提供了丰富的 PHP 开发常用功能,涵盖以下模块:

### 📊 数据处理
- **Excel**: Excel 文件的导入和导出功能

### 📅 日期时间
- **日历**: 公历与农历互转

### 🏷️ 二维码
- **二维码生成**: 支持多种格式的二维码生成

### 📨 消息服务
- **腾讯云短信**: 短信发送功能

### 🗺️ 地图服务
- **腾讯地图**: 地图相关功能

### 🔐 微信生态
- **微信小程序**: 微信小程序登录
- **微信公众号**: 微信公众号网页授权
- **微信支付**: 微信 JSAPI 支付、支付回调解密

### 🎵 抖音生态
- **抖音登录**: 抖音小程序和小游戏登录

### 🔒 加密解密
- **AES 加密**: AES 加密解密功能
- **RSA 加密**: RSA 公钥加密私钥解密功能

### ☁️ 云存储
- **七牛云存储**: 文件上传、删除、获取文件信息等

### 🔧 工具类
- **雪花算法 ID**: 基于雪花算法的分布式唯一 ID 生成,支持自定义机器 ID 和起始时间
- **数据库分片**: 提供分库分表计算功能,支持灵活的分片策略配置

## 安装

```bash
composer require youhujun/php-tool-youhujun
```

### 环境要求

- PHP >= 8.2
- 扩展: openssl, json, gd
- 依赖: godruoyi/php-snowflake (用于雪花算法 ID 生成)

## 使用

**更多文档请查看[文档地址](https://www.youhujun.com/open-02/php-tool-youhujun/)**

## 测试

### 安装测试依赖
```bash
composer install --dev
```

### 运行测试

- 单独测试 Excel

```bash
composer test --testsuite Excel
```

- 单独测试 Calendar

```bash
composer test --testsuite Calendar
```

- 全部测试

```bash
composer test
```

> 注意: 部分功能模块(如微信支付、短信服务等)需要第三方服务的 Key 或 AppID,因此暂时不包含在自动测试中。

## 变更日志

查看详细的版本变更记录,请访问 [CHANGELOG.md](CHANGELOG.md)

## 文档

**更多详细文档请查看[文档地址](https://www.youhu.club/)**

## 许可证

MIT License