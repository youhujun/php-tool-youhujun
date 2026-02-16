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
| `V1/模块/类名` | 只生成 Facade | `src/App/Facades` |
| `V1/模块/类名` | 只生成 Service | `src/App/Services` |
| `V1/模块/类名` | 生成 Facade + Service | `src/App/Facades` 和 `src/App/Services` |

**说明:**
- Facade 基础路径是: `src/App/Facades`
- Service 基础路径是: `src/App/Services`
- V1 是版本号,在基础路径之后

## 使用示例

### 生成 Facade

```bash
# 使用 youhujun 前缀
php youhujun make:facade V1/Wechat/Official/WechatOfficialWebAuth

# 使用 tool 前缀
php tool make:facade V1/Wechat/Official/WechatOfficialWebAuth
```

生成文件:
```
src/App/Facades/V1/Wechat/Official/WechatOfficialWebAuthFacade.php
```

### 生成 Service

```bash
# 使用 youhujun 前缀
php youhujun make:service V1/Wechat/Official/WechatOfficialWebAuth "微信公众号网页授权服务"

# 使用 tool 前缀
php tool make:service V1/Wechat/Official/WechatOfficialWebAuth "微信公众号网页授权服务"
```

生成文件:
```
src/App/Services/V1/Wechat/Official/WechatOfficialWebAuthService.php
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
src/App/Facades/V1/Calendar/CalendarConverterFacade.php
src/App/Services/V1/Calendar/CalendarConverterService.php
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
| `make:facade` | `V1/Wechat/Official/WechatOfficialWebAuth` | `src/App/Facades/V1/Wechat/Official/WechatOfficialWebAuthFacade.php` |
| `make:service` | `V1/Wechat/Official/WechatOfficialWebAuth` | `src/App/Services/V1/Wechat/Official/WechatOfficialWebAuthService.php` |
| `call:facade` | `V1/Calendar/CalendarConverter` | `src/App/Facades/V1/Calendar/CalendarConverterFacade.php`<br>`src/App/Services/V1/Calendar/CalendarConverterService.php` |

## Facade 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Wechat\Official\WechatOfficialWebAuthFacade;

// 静态调用
$result = WechatOfficialWebAuthFacade::someMethod($param);

// 测试时注入实例
$service = new WechatOfficialWebAuthService();
WechatOfficialWebAuthFacade::setInstance($service);
```
