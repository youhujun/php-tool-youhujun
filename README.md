# youhujun/php-tool-youhujun

<div align="center">
   <a target="_blank" href="https://www.youhu.club/">📑 阅读文档</a>|  <a target="_blank" href="https://www.youhujun.com/">🌐 参考博客</a> 
</div>

## 项目简介

[php-tool-youhujun](https://gitcode.com/youhujun/) 是为了提高php开发效率,无需关乎基础通用功能的实现,将精力聚焦于业务逻辑的开发而催生的.

## 项目功能

目前仅实现了excel表格导入和导出,后续会逐步增加微信支付,加密解密等常用功能.

## 安装

```bash
compoer require youhujun/php-tool-youhujun
```

## 使用

### Excel门面使用

- 导出

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

ExcelFacade::exportExcelData($columns, $data, $title, $saveDirectory);
```
- 导入

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

ExcelFacade::initReadExcel($this->tempFile);
ExcelFacade::setWorkSheet(0);
$result = ExcelFacade::getDataByRow();

// 移除表头后再进行断言，这更符合真实业务场景
array_shift($result);

//数据库操作
```

**更多文档请查看[文档地址](https://www.youhu.club/)**

## 测试

### 安装
```bash
composer install --dev
```

### 测试

- 单独测试Excel

```bash
composer test --testsuite Excel
```
- 单独测试Calendar

```bash
composer test --testsuite Calendar
```
- 全部测试
  
```bash
composer test
```