# 变更日志

本文档记录 [youhujun/php-tool-youhujun](https://gitcode.com/youhujun/) 的所有重要变更。

格式基于 [Keep a Changelog](https://keepachangelog.com/zh-CN/1.0.0/)，
版本号遵循 [语义化版本](https://semver.org/lang/zh-CN/)。

---

## [1.1.6] - 2026-03-16

### 新增

#### 功能模块
- **Elasticsearch 服务**: 提供完整的 ES 操作支持，包括索引管理、文档 CRUD、搜索查询等功能
- **ES 认证支持**: 支持 ES 账户密码认证，增强生产环境安全性
- **数据同步钩子**: 支持通过闭包或接口实现数据同步

#### 新增服务类
- `YouHuJun\Tool\App\Facades\V1\Es\EsFacade` - ES 门面代理
- `YouHuJun\Tool\App\Services\V1\Es\EsFacadeService` - ES 服务实现
- `YouHuJun\Tool\App\Services\V1\Es\Contracts\EsDataSyncContract` - 数据同步接口

#### 文档
- 新增 `Doucments/Es/UseEsFacade.md` - ES 服务使用文档

### 优化
- 为所有 Facade 类添加 `__callStatic` 魔术方法，完善门面静态代理功能
- 统一 Facade 类结构，明确方法声明保持 IDE 代码提示
- 优化 EsFacade 文档结构，按照"索引管理 → 文档操作 → 高级功能"的逻辑顺序组织

### 修复
- 修复部分 Facade 类缺少 `__callStatic` 魔术方法的问题
- 修复 Facade 静态调用无代码提示的问题

---

## [1.1.4] - 2026-02-17

### 新增

#### 功能模块
- **密钥管理服务**: 提供安全密钥生成功能,支持自定义字符类型和长度
- **认证签名服务**: 提供 HMAC-SHA256 签名生成功能,用于API接口认证和数据完整性校验

#### 命令行工具
- 新增 `php youhujun make:facade` 命令 - 生成 Facade 文件
- 新增 `php youhujun make:service` 命令 - 生成 Service 文件
- 新增 `php youhujun call:facade` 命令 - 同时生成 Facade 和 Service 文件
- 支持自定义路径和版本号,快速生成门面代理类

#### 文档
- 新增 `Doucments/Utils/Sign/UseKeyManagerFacade.md` - 密钥管理服务使用文档
- 新增 `Doucments/Utils/Sign/UseAuthSignFacade.md` - 认证签名服务使用文档
- 新增 `Doucments/COMMANDS.md` - 命令行工具使用文档

### 优化
- 统一 Facade 类命名规范,使用单数形式(如 `KeyManagerFacade` 而非 `KeyManagerFacades`)
- 统一 Service 类命名规范,移除 `Facade` 前缀(如 `KeyManagerService` 而非 `KeyManagerFacadeService`)
- 优化命令行工具生成逻辑,修正文件路径和类名生成问题
- 完善代码注释,统一文档格式和风格

### 新增服务类
- `YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade` - 密钥管理门面
- `YouHuJun\Tool\App\Service\V1\Utils\Sign\KeyManagerFacadeService` - 密钥管理服务
- `YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade` - 认证签名门面
- `YouHuJun\Tool\App\Service\V1\Utils\Sign\AuthSignFacadeService` - 认证签名服务

---

## [1.1.5] - 2026-02-17

### 新增

#### 功能优化
- **分片工具多配置支持**: ShardFacade 支持多配置模式,适配分库分表+微服务场景
- **映射表分离**: 支持业务表和映射表使用不同分片配置
- **微服务适配**: 每个微服务可配置独立的分片规则

### 优化
- 重构 ShardFacade 为多配置模式,所有方法强制传入 `configKey` 参数
- 统一命名规范: `setConfig` → `setMultiConfig`, `getConfig` → `getMultiConfig`
- 新增业务表与映射表分离的完整使用示例
- 新增微服务场景多配置示例
- 更新最佳实践部分,所有示例适配多配置模式

### 变更
- `ShardFacade::setConfig()` → `ShardFacade::setMultiConfig($configKey, $config)`
- `ShardFacade::getConfig($key)` → `ShardFacade::getMultiConfig($configKey, $key)`
- `ShardFacade::calc($uid)` → `ShardFacade::calc($uid, $configKey)`
- `ShardFacade::getTableName($uid, $baseTable)` → `ShardFacade::getTableName($uid, $baseTable, $configKey)`
- `ShardFacade::getDbName($uid)` → `ShardFacade::getDbName($uid, $configKey)`
- `ShardFacade::getShardKey($uid)` → `ShardFacade::getShardKey($uid, $configKey)`

#### 文档
- 更新 `Doucments/Utils/UseShardFacade.md` - 完整适配多配置模式

### 重要提示
**破坏性变更**: 所有 ShardFacade 方法现在必须传入 `configKey` 参数,无默认值。请更新所有调用代码。

---

## [1.1.3] - 2026-01-14

### 新增

#### 功能模块
- **雪花算法 ID 生成**: 基于雪花算法的分布式唯一 ID 生成功能,支持自定义机器 ID 和起始时间
- **数据库分片工具**: 提供分库分表计算功能,支持灵活的分片策略配置

#### 文档
- 新增 `Doucments/Utils/UseSnowflakeFacade.md` - 雪花算法使用文档
- 新增 `Doucments/Utils/UseShardFacade.md` - 分片工具使用文档

---

## [1.1.0] - 2025-01-08

### 新增

#### 功能模块
- **Excel 处理**: Excel 文件的导入和导出功能
- **日历转换**: 公历与农历互转
- **二维码生成**: 支持多种格式的二维码生成
- **腾讯云短信**: 短信发送功能
- **腾讯地图**: 地图相关功能
- **微信小程序**: 微信小程序登录
- **微信公众号**: 微信公众号网页授权
- **微信支付**: 微信 JSAPI 支付、支付回调解密
- **抖音登录**: 抖音小程序和小游戏登录
- **AES 加密**: AES 加密解密功能
- **RSA 加密**: RSA 公钥加密私钥解密功能
- **七牛云存储**: 文件上传、删除、获取文件信息等

#### 错误码系统
- 系统级错误码 (10000-19999)
- Excel 相关错误码 (20000-29999)
- 日历相关错误码 (30000-39999)
- 微信相关错误码 (40000-49999)
- 地图相关错误码 (50000-50999)
- 短信相关错误码 (51000-51999)
- 二维码相关错误码 (80000-80999)
- 抖音相关错误码 (53000-53999)
- 加密解密相关错误码 (90000-91999)
- 七牛云相关错误码 (92000-92999)

### 变更
- 更新 README.md,新增所有功能模块说明
- 修复安装命令拼写错误 (compoer → composer)
- 添加环境要求和扩展说明
- 新增 CHANGELOG.md 变更日志文件

### 新增

#### 功能模块
- **Excel 处理**: Excel 文件的导入和导出功能
- **日历转换**: 公历与农历互转
- **二维码生成**: 支持多种格式的二维码生成
- **腾讯云短信**: 短信发送功能
- **腾讯地图**: 地图相关功能
- **微信小程序**: 微信小程序登录
- **微信公众号**: 微信公众号网页授权
- **微信支付**: 微信 JSAPI 支付、支付回调解密
- **抖音登录**: 抖音小程序和小游戏登录
- **AES 加密**: AES 加密解密功能
- **RSA 加密**: RSA 公钥加密私钥解密功能
- **七牛云存储**: 文件上传、删除、获取文件信息等

#### 错误码系统
- 系统级错误码 (10000-19999)
- Excel 相关错误码 (20000-29999)
- 日历相关错误码 (30000-39999)
- 微信相关错误码 (40000-49999)
- 地图相关错误码 (50000-50999)
- 短信相关错误码 (51000-51999)
- 二维码相关错误码 (80000-80999)
- 抖音相关错误码 (53000-53999)
- 加密解密相关错误码 (90000-91999)
- 七牛云相关错误码 (92000-92999)

---

## [1.0.5] - 2024-12-24

### 修复
- 修复了部分已知问题

---

## [1.0.0] - 2024-XX-XX

### 新增
- 初始版本发布
- Excel 导入导出基础功能
