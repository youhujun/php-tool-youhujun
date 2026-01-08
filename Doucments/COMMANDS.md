# YouHuJun PHP Tool 命令行工具

## 命令前缀

可以使用以下几种方式调用生成命令:

### 1. 使用 `youhujun` 前缀 (推荐)

```bash
php youhujun make:facade <路径> [描述]
php youhujun make:service <路径> [描述]
php youhujun call:facade <路径> [描述]
```

### 2. 使用 `tool` 前缀

```bash
php tool make:facade <路径> [描述]
php tool make:service <路径> [描述]
php tool call:facade <路径> [描述]
```

## 命令说明

| 命令 | 说明 |
|------|------|
| `make:facade` | 只生成 Facade 文件 |
| `make:service` | 只生成 Service 文件 |
| `call:facade` | 同时生成 Facade 和 Service 文件 |

## 路径格式

| 路径格式 | 生成内容 | 基础路径 |
|----------|----------|-----------|
| `Facade/V1/模块/类名` | 只生成 Facade | `App/Facade` |
| `Service/V1/模块/类名` | 只生成 Service | `App/Service` |
| `V1/模块/类名` | 生成 Facade + Service | `App/Facade` 和 `App/Service` |

**说明:**
- Facade 基础路径是: `App/Facade`
- Service 基础路径是: `App/Service`
- V1 是版本号,在基础路径之后

## 使用示例

### 生成 Facade

```bash
# 使用 youhujun 前缀
php youhujun make:facade Facade/V1/Wechat/Official/WechatOfficialWebAuth

# 使用 tool 前缀
php tool make:facade Facade/V1/Wechat/Official/WechatOfficialWebAuth
```

生成文件:
```
src/App/Facade/V1/Wechat/Official/WechatOfficialWebAuthFacade.php
```

### 生成 Service

```bash
# 使用 youhujun 前缀
php youhujun make:service Service/V1/Wechat/Official/WechatOfficialWebAuth "微信公众号网页授权服务"

# 使用 tool 前缀
php tool make:service Service/V1/Wechat/Official/WechatOfficialWebAuth "微信公众号网页授权服务"
```

生成文件:
```
src/App/Service/V1/Wechat/Official/WechatOfficialWebAuthFacadeService.php
```

### 同时生成 Facade 和 Service

```bash
# 使用 call:facade 命令
php youhujun call:facade V1/Calendar/CalendarConverter "日历转换服务"

# 使用 tool 前缀
php tool call:facade V1/Calendar/CalendarConverter "日历转换服务"
```

生成文件:
```
src/App/Facade/V1/Calendar/CalendarConverterFacade.php
src/App/Service/V1/Calendar/CalendarConverterFacadeService.php
```

### 查看帮助

```bash
php youhujun
php youhujun help
php tool
```

## 生成的文件结构总结

| 命令 | 输入路径 | 生成文件 |
|------|----------|----------|
| `make:facade` | `Facade/V1/Wechat/Official/WechatOfficialWebAuth` | `src/App/Facade/V1/Wechat/Official/WechatOfficialWebAuthFacade.php` |
| `make:service` | `Service/V1/Wechat/Official/WechatOfficialWebAuth` | `src/App/Service/V1/Wechat/Official/WechatOfficialWebAuthFacadeService.php` |
| `call:facade` | `V1/Calendar/CalendarConverter` | `src/App/Facade/V1/Calendar/CalendarConverterFacade.php`<br>`src/App/Service/V1/Calendar/CalendarConverterFacadeService.php` |

## Facade 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Wechat\Official\WechatOfficialWebAuthFacade;

// 静态调用
$result = WechatOfficialWebAuthFacade::someMethod($param);

// 测试时注入实例
$service = new WechatOfficialWebAuthFacadeService();
WechatOfficialWebAuthFacade::setInstance($service);
```
