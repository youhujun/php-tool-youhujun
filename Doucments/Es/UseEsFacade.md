# Elasticsearch服务使用文档

## 概述

Elasticsearch服务提供了对 Elasticsearch 的完整操作支持，包括索引管理、文档CRUD、搜索查询以及数据同步等功能。

## 安装

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
```

## 初始化

### 使用默认配置

```php
// 默认连接 http://127.0.0.1:9200 (无认证)
EsFacade::init();
```

### 自定义ES地址

```php
// 连接指定ES服务 (无认证)
EsFacade::init('http://192.168.1.100:9200');
```

### 使用账户密码认证

```php
// 连接需要认证的ES服务
EsFacade::init(
    'http://192.168.1.100:9200',
    'elastic',      // ES用户名
    'password123'   // ES密码
);
```

### 生产环境配置建议

```php
// 使用环境变量配置 (推荐)
EsFacade::init(
    env('ES_HOST', 'http://127.0.0.1:9200'),
    env('ES_USER'),
    env('ES_PASSWORD')
);
```

## 功能方法

### 一、索引管理

索引操作相当于 MySQL 的建表操作，需要先创建索引后才能进行文档操作。

#### 1. 检查索引是否存在 - `indexExists`

检查指定索引是否已存在。

##### 方法签名

```php
EsFacade::indexExists(string $index): bool
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |

##### 返回值

返回 `true` 表示索引存在，`false` 表示不存在。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

if (EsFacade::indexExists('users')) {
    echo "索引存在";
} else {
    echo "索引不存在";
}
```

---

#### 2. 创建索引 - `createIndex`

创建新索引，可选择是否定义字段映射。

##### 方法签名

```php
EsFacade::createIndex(string $index, array $mapping = []): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| mapping | array | 否 | 索引映射配置(字段定义) |

##### 返回值

返回 ES 响应结果数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    // 创建空索引(使用ES默认映射)
    EsFacade::createIndex('users');

    // 创建带字段映射的索引
    EsFacade::createIndex('users', [
        'properties' => [
            'name' => [
                'type' => 'text',
                'analyzer' => 'ik_max_word'
            ],
            'age' => ['type' => 'integer'],
            'email' => ['type' => 'keyword'],
            'status' => ['type' => 'keyword'],
            'created_at' => ['type' => 'date'],
            'updated_at' => ['type' => 'date']
        ]
    ]);
    echo "索引创建成功";

} catch (\Exception $e) {
    echo "创建失败: " . $e->getMessage();
}
```

**常用字段类型说明:**

| ES类型 | 说明 | 示例 |
|--------|------|------|
| text | 全文检索字段，会分词 | 文章内容、描述 |
| keyword | 精确匹配，不分词 | ID、状态码、标签 |
| integer | 整数 | 年龄、数量 |
| float | 浮点数 | 价格、分数 |
| date | 日期时间 | 创建时间 |
| boolean | 布尔值 | 是否启用 |

---

#### 3. 删除索引 - `deleteIndex`

删除指定索引及其所有文档。

##### 方法签名

```php
EsFacade::deleteIndex(string $index): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |

##### 返回值

返回 ES 响应结果数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    $result = EsFacade::deleteIndex('users');
    echo "索引删除成功";

} catch (\Exception $e) {
    echo "删除失败: " . $e->getMessage();
}
```

**⚠️ 警告:** 删除索引会永久删除所有数据，请谨慎操作！

---

### 二、文档操作

文档操作相当于 MySQL 的增删改查，操作前需确保索引已存在。

#### 1. 创建文档 - `createDoc`

创建或新增一个文档。

##### 方法签名

```php
EsFacade::createDoc(string $index, array $data, string $docId = null): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| data | array | 是 | 文档数据 |
| docId | string|null | 否 | 文档ID，不传则ES自动生成 |

##### 返回值

返回 ES 响应结果数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    // 自动生成ID创建
    $docId = EsFacade::createDoc('users', [
        'name' => '游鹄君',
        'age' => 25,
        'email' => 'youhu8888@163.com'
    ]);
    echo "文档ID: " . json_encode($docId);

    // 指定ID创建
    $result = EsFacade::createDoc('users', [
        'name' => '雪儿',
        'age' => 18,
        'email' => 'xueer@youhujun.com'
    ], 'user_001');
    echo "创建结果: " . json_encode($result);

} catch (\Exception $e) {
    echo "创建失败: " . $e->getMessage();
}
```

---

#### 2. 获取文档 - `getDoc`

根据文档ID获取单个文档。

##### 方法签名

```php
EsFacade::getDoc(string $index, string $docId): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| docId | string | 是 | 文档ID |

##### 返回值

返回文档数据数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    $doc = EsFacade::getDoc('users', 'user_001');
    echo "文档数据: " . json_encode($doc, JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    echo "获取失败: " . $e->getMessage();
}
```

---

#### 3. 更新文档 - `updateDoc`

更新文档，支持全量更新和局部更新。

##### 方法签名

```php
EsFacade::updateDoc(string $index, string $docId, array $data, bool $isPartial = true): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| docId | string | 是 | 文档ID |
| data | array | 是 | 更新数据 |
| isPartial | bool | 否 | 是否局部更新，默认true |

##### 返回值

返回 ES 响应结果数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    // 局部更新(推荐)
    $result = EsFacade::updateDoc('users', 'user_001', [
        'age' => 26,
        'email' => 'newemail@example.com'
    ], true);
    echo "局部更新成功";

    // 全量替换
    $result = EsFacade::updateDoc('users', 'user_001', [
        'name' => '游鹄君',
        'age' => 26,
        'email' => 'newemail@example.com',
        'status' => 'active'
    ], false);
    echo "全量替换成功";

} catch (\Exception $e) {
    echo "更新失败: " . $e->getMessage();
}
```

---

#### 4. 删除文档 - `deleteDoc`

删除单个文档。

##### 方法签名

```php
EsFacade::deleteDoc(string $index, string $docId): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| docId | string | 是 | 文档ID |

##### 返回值

返回 ES 响应结果数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    $result = EsFacade::deleteDoc('users', 'user_001');
    echo "删除成功";

} catch (\Exception $e) {
    echo "删除失败: " . $e->getMessage();
}
```

---

#### 5. 搜索文档 - `searchDoc`

根据查询条件搜索文档。

##### 方法签名

```php
EsFacade::searchDoc(string $index, array $query, int $from = 0, int $size = 10): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名(多个索引用逗号分隔) |
| query | array | 是 | ES查询条件 |
| from | int | 否 | 起始位置(分页)，默认0 |
| size | int | 否 | 返回数量，默认10 |

##### 返回值

返回搜索结果数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    // 精确匹配搜索
    $result = EsFacade::searchDoc('users', [
        'match' => ['name' => '游鹄君']
    ]);
    echo "搜索结果: " . json_encode($result, JSON_UNESCAPED_UNICODE);

    // 分页搜索
    $result = EsFacade::searchDoc('users', [
        'match_all' => (object)[]
    ], 10, 20); // 从第10条开始，返回20条

    // 多索引搜索
    $result = EsFacade::searchDoc('users,orders', [
        'match' => ['name' => '游鹄君']
    ]);

} catch (\Exception $e) {
    echo "搜索失败: " . $e->getMessage();
}
```

---

#### 6. 按条件删除 - `deleteByQuery`

根据查询条件删除文档。

##### 方法签名

```php
EsFacade::deleteByQuery(string $index, array $query): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| query | array | 是 | 删除条件 |

##### 返回值

返回 ES 响应结果数组。

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    $result = EsFacade::deleteByQuery('users', [
        'term' => ['status' => 'deleted']
    ]);
    echo "删除成功";

} catch (\Exception $e) {
    echo "删除失败: " . $e->getMessage();
}
```

---

### 三、高级功能

#### 1. 自定义请求 - `customRequest`

对于特殊操作，可以使用自定义请求方法。

##### 方法签名

```php
EsFacade::customRequest(string $method, string $path, array $data = []): array
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

try {
    // 获取所有索引列表
    $result = EsFacade::customRequest('GET', '/_cat/indices?v');

    // 获取集群健康状态
    $result = EsFacade::customRequest('GET', '/_cluster/health');

    // 批量操作
    $result = EsFacade::customRequest('POST', '/_bulk', $bulkData);

} catch (\Exception $e) {
    echo "请求失败: " . $e->getMessage();
}
```

---

#### 2. 数据同步

支持通过闭包或接口方式实现数据同步。

##### 方式一: 使用闭包钩子

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

// 注册数据同步钩子
EsFacade::registerSyncHook(function(string $type, string $index, array $data, ?string $docId = null) {
    // 自定义同步逻辑
    switch ($type) {
        case 'single':
            // 单条同步
            return EsFacade::createDoc($index, $data, $docId);
        case 'batch':
            // 批量同步
            foreach ($data as $item) {
                EsFacade::createDoc($index, $item, $item['_id'] ?? null);
            }
            return ['success' => true];
        case 'delete':
            // 删除同步
            return EsFacade::deleteDoc($index, $docId);
    }
});

// 执行同步
$result = EsFacade::syncData('single', 'users', [
    'name' => '游鹄君',
    'age' => 25
], 'user_001');
```

##### 方式二: 使用接口实现

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Services\V1\Es\Contracts\EsDataSyncContract;

class UserEsSync implements EsDataSyncContract
{
    public function syncSingle(string $index, array $data, ?string $docId = null): array
    {
        // 自定义单条同步逻辑
        return EsFacade::createDoc($index, $data, $docId);
    }

    public function syncBatch(string $index, array $data): array
    {
        // 自定义批量同步逻辑
        return EsFacade::customRequest('POST', '/_bulk', $data);
    }

    public function syncDelete(string $index, string|array $docId): array
    {
        // 自定义删除同步逻辑
        return EsFacade::deleteDoc($index, $docId);
    }
}

// 注册接口实现
$userSync = new UserEsSync();
EsFacade::registerSyncContract($userSync);

// 执行同步
$result = EsFacade::syncData('single', 'users', [
    'name' => '雪儿',
    'age' => 18
], 'user_002');
```

---

## 完整示例

### 示例1: 完整的CRUD操作

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

// 初始化
EsFacade::init('http://127.0.0.1:9200');

try {
    // 1. 创建索引
    if (!EsFacade::indexExists('products')) {
        EsFacade::createIndex('products', [
            'properties' => [
                'name' => ['type' => 'text'],
                'price' => ['type' => 'float'],
                'category' => ['type' => 'keyword']
            ]
        ]);
        echo "索引创建成功" . PHP_EOL;
    }

    // 2. 创建文档
    $result = EsFacade::createDoc('products', [
        'name' => '游鹄生态会员',
        'price' => 99.99,
        'category' => 'service'
    ], 'prod_001');
    echo "文档创建成功: " . json_encode($result) . PHP_EOL;

    // 3. 获取文档
    $doc = EsFacade::getDoc('products', 'prod_001');
    echo "文档数据: " . json_encode($doc, JSON_UNESCAPED_UNICODE) . PHP_EOL;

    // 4. 更新文档
    $result = EsFacade::updateDoc('products', 'prod_001', [
        'price' => 88.88
    ]);
    echo "文档更新成功" . PHP_EOL;

    // 5. 搜索文档
    $result = EsFacade::searchDoc('products', [
        'match' => ['name' => '游鹄']
    ]);
    echo "搜索结果: " . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;

    // 6. 删除文档
    $result = EsFacade::deleteDoc('products', 'prod_001');
    echo "文档删除成功" . PHP_EOL;

} catch (\Exception $e) {
    echo "操作失败: " . $e->getMessage() . PHP_EOL;
}
```

### 示例2: 用户搜索功能

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * 搜索用户
 */
function searchUsers(string $keyword, int $page = 1, int $pageSize = 10): array
{
    try {
        $from = ($page - 1) * $pageSize;

        // 多字段搜索
        $result = EsFacade::searchDoc('users', [
            'multi_match' => [
                'query' => $keyword,
                'fields' => ['name', 'email', 'nickname']
            ]
        ], $from, $pageSize);

        return [
            'success' => true,
            'data' => $result['hits']['hits'] ?? [],
            'total' => $result['hits']['total']['value'] ?? 0
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

// 使用示例
$result = searchUsers('游鹄', 1, 10);
print_r($result);
```

### 示例3: 数据同步到ES

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * 用户模型数据同步到ES
 */
class UserSyncHandler implements EsDataSyncContract
{
    public function syncSingle(string $index, array $data, ?string $docId = null): array
    {
        try {
            return EsFacade::createDoc($index, $data, $docId);
        } catch (\Exception $e) {
            throw new CommonException("用户同步失败: " . $e->getMessage());
        }
    }

    public function syncBatch(string $index, array $data): array
    {
        try {
            // 批量操作
            $bulkData = '';
            foreach ($data as $item) {
                $id = $item['id'] ?? null;
                unset($item['id']);

                $bulkData .= json_encode(['index' => ['_index' => $index, '_id' => $id]]) . "\n";
                $bulkData .= json_encode($item, JSON_UNESCAPED_UNICODE) . "\n";
            }

            return EsFacade::customRequest('POST', '/_bulk', $bulkData);
        } catch (\Exception $e) {
            throw new CommonException("批量同步失败: " . $e->getMessage());
        }
    }

    public function syncDelete(string $index, string|array $docId): array
    {
        try {
            return EsFacade::deleteDoc($index, $docId);
        } catch (\Exception $e) {
            throw new CommonException("删除同步失败: " . $e->getMessage());
        }
    }
}

// 使用示例
$userSync = new UserSyncHandler();
EsFacade::registerSyncContract($userSync);

// 同步单个用户
EsFacade::syncData('single', 'users', [
    'id' => 'user_001',
    'name' => '游鹄君',
    'age' => 25
], 'user_001');
```

---

## 错误处理

所有方法在失败时都会抛出异常，建议使用 try-catch 捕获。

### 错误处理示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Exceptions\CommonException;

try {
    $result = EsFacade::createDoc('users', $data);
} catch (CommonException $e) {
    error_log("ES操作失败: " . $e->getMessage());
    // 处理错误
}
```

---

## 注意事项

### 1. 连接配置

- 默认连接地址: `http://127.0.0.1:9200`
- 支持账户密码认证
- 生产环境建议使用环境变量配置ES连接信息
- 确保ES服务已启动且可访问

### 2. 认证配置

**基本认证方式:**

```php
// 无认证 (仅开发环境使用)
EsFacade::init('http://127.0.0.1:9200');

// 基本认证 (用户名 + 密码)
EsFacade::init(
    'http://es.yourdomain.com:9200',
    'elastic',
    'your-password-here'
);
```

**安全建议:**

1. 生产环境必须启用认证
2. 使用HTTPS协议传输
3. 密码使用强密码策略
4. 定期更换账户密码
5. 使用环境变量存储敏感信息

**环境变量配置示例:**

```env
# .env 文件
ES_HOST=http://your-es-server.com:9200
ES_USER=elastic
ES_PASSWORD=your-strong-password-here
```

```php
// Laravel 示例
EsFacade::init(
    env('ES_HOST', 'http://127.0.0.1:9200'),
    env('ES_USER'),
    env('ES_PASSWORD')
);
```

### 3. 性能优化

- **批量操作**: 使用 `_bulk` API 进行批量操作，性能更好
- **分页查询**: 合理设置 `from` 和 `size` 参数，避免返回过多数据
- **索引优化**: 根据业务需求合理设计索引映射

### 4. 数据一致性

- 更新操作建议使用局部更新(`isPartial = true`)
- 批量操作注意事务处理
- 数据同步时建议使用队列异步处理

### 5. 安全建议

- **认证**: 生产环境必须启用账户密码认证
- **传输**: 使用HTTPS协议加密传输
- **密码**: 使用强密码策略，定期更换
- **存储**: 敏感数据建议加密存储
- **备份**: 定期备份ES数据
- **访问控制**: 配置IP白名单或网络隔离

---

## 依赖要求

本服务依赖 `curl_helper.php` 辅助函数，确保已正确加载:

```php
// 确保包含 curl_helper.php
require_once 'src/config/curl_helper.php';
```

---

## 更多帮助

- Elasticsearch官方文档: https://www.elastic.co/guide/
- 如有问题，请查看 `EsFacadeService.php` 源码
