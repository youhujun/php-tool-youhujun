# Elasticsearch服务使用文档

## 概述

Elasticsearch服务提供了对 Elasticsearch 的完整操作支持，包括索引管理、文档CRUD、搜索查询以及批量操作等功能。

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
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);
```

## 返回值格式

所有方法统一返回格式：

```php
[
    'code' => 0,           // 0=成功, 10000=失败
    'msg' => '操作成功',   // 提示信息
    'status' => 1,         // 1=成功, 0=失败, 其他值见各方法说明
    'error' => null,       // ES原始响应(调试用)
    'data' => []           // 返回的数据(部分方法有此字段)
]
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

// 初始化ES
EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

if (EsFacade::indexExists($indexName)) {
    echo "索引存在";
} else {
    echo "索引不存在";
}
```

---

#### 2. 创建索引 - `createIndex`

创建新索引，支持设置分片数、副本数和字段映射。

##### 方法签名

```php
EsFacade::createIndex(string $index, array $body = []): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| body | array | 否 | 索引配置(settings和mappings) |

##### 返回值

```php
[
    'code' => 0,
    'msg' => 'es索引创建成功',
    'status' => 1,
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

// 创建带完整配置的索引
$result = EsFacade::createIndex($indexName, [
    'settings' => [
        // 单节点测试设1，生产可按数据量调
        'number_of_shards' => config('common_es.setting.shard_number'),
        // 测试环境不用副本，生产建议设1
        'number_of_replicas' => config('common_es.setting.replicas_number'),
        'analysis' => [
            'analyzer' => [
                // 全局默认细粒度分词
                'default' => ['type' => 'ik_max_word'],
                // 搜索时粗粒度分词
                'default_search' => ['type' => 'ik_smart']
            ]
        ]
    ],
    'mappings' => [
        'properties' => [
            'phone' => ['type' => 'keyword', 'ignore_above' => 256],
            'id_number' => ['type' => 'keyword', 'ignore_above' => 256],
            'account_name' => ['type' => 'keyword', 'ignore_above' => 256],
            'email' => ['type' => 'keyword', 'ignore_above' => 256],
            'nick_name' => [
                'type' => 'text',
                'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]
            ],
            'real_name' => [
                'type' => 'text',
                'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]
            ],
            'account_status' => ['type' => 'integer'],
            'real_auth_status' => ['type' => 'integer'],
            'created_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'],
            'sex' => ['type' => 'integer'],
            'solar_birthday_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis', 'index' => false],
            'chinese_birthday_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis', 'index' => false],
            'introduction' => ['type' => 'keyword', 'ignore_above' => 512, 'index' => false],
            'avatar' => ['type' => 'keyword', 'ignore_above' => 512, 'index' => false],
            'ablum_uid' => ['type' => 'long', 'index' => false],
            'qrcode' => ['type' => 'keyword', 'ignore_above' => 256, 'index' => false],
        ]
    ]
]);

if ($result['code'] === 0) {
    echo "索引创建成功";
}
```

**常用字段类型说明:**

| ES类型 | 说明 | 示例 | 常用配置 |
|--------|------|------|----------|
| text | 全文检索字段，会分词 | 文章内容、昵称、真实姓名 | analyzer: ik_max_word |
| keyword | 精确匹配，不分词 | ID、状态码、手机号、身份证号 | ignore_above: 256 |
| integer | 整数 | 年龄、状态码 | - |
| long | 长整型 | 用户ID、相册UID | index: false(不需要搜索) |
| date | 日期时间 | 创建时间、生日 | format: 'yyyy-MM-dd HH:mm:ss' |
| boolean | 布尔值 | 是否启用 | - |

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

```php
[
    'code' => 0,
    'msg' => 'es索引删除成功',
    'status' => 1,
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

$result = EsFacade::deleteIndex($indexName);

if ($result['code'] === 0) {
    echo "索引删除成功";
}
```

**⚠️ 警告:** 删除索引会永久删除所有数据，请谨慎操作！

---

#### 4. 更新索引映射 - `updateMapping`

更新 ES 索引映射（新增字段，不删数据、不影响原有字段）。相当于 MySQL 的 `ALTER TABLE ADD COLUMN`。

##### 方法签名

```php
EsFacade::updateMapping(string $index, array $newFields): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名称 |
| newFields | array | 是 | 新增字段的映射配置，格式：`['字段名' => ['type' => '字段类型']]` |

##### 返回值

```php
[
    'code' => 0,
    'msg' => 'es索引更新成功',
    'status' => 1,
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

// 测试增加索引字段
EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

// 新增单个字段
$newFields = [
    'user_uid' => ['type' => 'keyword']
];

$result = EsFacade::updateMapping($indexName, $newFields);

if ($result['code'] === 0) {
    echo $result['msg'];
}

// 批量新增多个字段
$newFields = [
    'user_uid' => ['type' => 'keyword', 'ignore_above' => 256],
    'total_score' => ['type' => 'integer'],
    'last_login_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis']
];

$result = EsFacade::updateMapping($indexName, $newFields);
```

**⚠️ 注意事项:**

- 只能新增字段，不能修改或删除已有字段的映射
- ES 中已存在的字段类型一旦确定就不能修改（需要重建索引）
- 建议在索引创建前规划好字段映射，尽量减少后续修改
- 新增字段会自动应用默认设置（如 analyzer、ignore_above 等）

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

```php
[
    'code' => 0,
    'msg' => 'es文档创建成功',  // 或 'es文档更新成功'
    'status' => 1,               // 1=创建成功, 2=更新成功
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

// 查询用户数据
$userObject = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1)->where('account_name', 'develop');
    },
    'account_name',
    ['develop']
)->first();

$userInfoObject = ShardHelperFacade::queryByShardWithCache(UserInfo::class, $userObject->biz_id)->first();

// 构造ES文档数据
$data = [
    'phone' => $userObject->phone,
    'account_name' => $userObject->account_name,
    'email' => $userObject->email,
    'account_status' => $userObject->account_status,
    'real_auth_status' => $userObject->real_auth_status,
    'created_at' => $userObject->created_at,
    'id_number' => $userInfoObject->id_number,
    'nick_name' => $userInfoObject->nick_name,
    'real_name' => $userInfoObject->real_name,
    'sex' => $userInfoObject->sex,
    'solar_birthday_at' => $userInfoObject->solar_birthday_at,
    'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
];

// 指定ID创建文档
$result = EsFacade::createDoc($indexName, $data, $userObject->biz_id);

if ($result['code'] === 0) {
    echo $result['msg']; // es文档创建成功 或 es文档更新成功
}
```

---

#### 2. 获取单个文档 - `findDoc`

根据文档ID获取单个文档。

##### 方法签名

```php
EsFacade::findDoc(string $index, string $docId): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| docId | string | 是 | 文档ID |

##### 返回值

```php
[
    'code' => 0,
    'msg' => 'es文档查找成功',
    'status' => 1,
    'data' => [...],   // 文档数据(_source)
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

$userObject = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1)->where('account_name', 'develop');
    },
    'account_name',
    ['develop']
)->first();

$result = EsFacade::findDoc($indexName, $userObject->biz_id);

if ($result['code'] === 0) {
    echo "文档数据: ";
    print_r($result['data']);
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

```php
[
    'code' => 0,
    'msg' => 'es文档更新成功',  // 或 'es文档创建成功'/'es文档无更新（数据未变化）'
    'status' => 1,               // 1=创建成功, 2=更新成功, 3=无操作
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

$userObject = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1)->where('account_name', 'super');
    },
    'account_name',
    ['super']
)->first();

$userInfoObject = ShardHelperFacade::queryByShardWithCache(UserInfo::class, $userObject->biz_id)->first();

$data = [
    'phone' => '15688523141',
    'account_name' => $userObject->account_name,
    'email' => $userObject->email,
    'account_status' => $userObject->account_status,
    'real_auth_status' => $userObject->real_auth_status,
    'created_at' => $userObject->created_at,
    'id_number' => $userInfoObject->id_number,
    'nick_name' => $userInfoObject->nick_name,
    'real_name' => $userInfoObject->real_name,
    'sex' => $userInfoObject->sex,
    'solar_birthday_at' => $userInfoObject->solar_birthday_at,
    'chinese_birthday_at' => $userInfoObject->chinese_birthday_at,
];

// 局部更新(推荐)
$result = EsFacade::updateDoc($indexName, $userObject->biz_id, $data);

if ($result['code'] === 0) {
    echo $result['msg'];
}
```

---

#### 4. 删除单个文档 - `deleteDoc`

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

```php
[
    'code' => 0,
    'msg' => 'es文档无删除成功',  // 或 'es文档不存在'
    'status' => 1,               // 1=删除成功, 4=文档不存在
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

$userObject = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1)->where('account_name', 'develop');
    },
    'account_name',
    ['develop']
)->first();

$result = EsFacade::deleteDoc($indexName, $userObject->biz_id);

if ($result['code'] === 0) {
    echo $result['msg'];
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

```php
[
    'code' => 0,
    'msg' => 'es文档批量查询',
    'status' => 1,
    'data' => [               // ES原始搜索结果
        'hits' => [
            'total' => [...],
            'hits' => [...]
        ]
    ],
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

// 精确匹配搜索
$query = [
    'match' => ['account_name' => 'develop']
];

$result = EsFacade::searchDoc($indexName, $query);

if ($result['code'] === 0) {
    $hits = $result['data']['hits']['hits'];
    $total = $result['data']['hits']['total']['value'];
    
    echo "总共找到 {$total} 条记录\n";
    foreach ($hits as $hit) {
        echo "文档ID: {$hit['_id']}\n";
        echo "数据: ";
        print_r($hit['_source']);
    }
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

```php
[
    'code' => 0,
    'msg' => 'es文档批量删除成功',
    'status' => 1,
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

$query = [
    'match' => ['account_name' => 'develop']
];

$result = EsFacade::deleteByQuery($indexName, $query);

if ($result['code'] === 0) {
    echo $result['msg'];
}
```

---

### 三、批量操作

批量操作可以大幅提升性能，特别适合大量数据的写入和删除场景。

#### 1. 批量写入/更新文档 - `batchActDoc`

批量写入或更新文档（直接调用ES _bulk API）。

##### 方法签名

```php
EsFacade::batchActDoc(string $index, array $data): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| data | array | 是 | 批量数据，每条必须包含 `_docId` 字段 |

##### 返回值

```php
[
    'code' => 0,
    'msg' => 'es批量写入成功',
    'status' => 1,
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

// 查询用户数据集合
$userCollection = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1);
    },
);

// 构造批量数据
$data = [];
foreach ($userCollection as $key => $userObject) {
    $data[] = [
        '_docId' => $userObject->biz_id,  // 必须：文档ID
        'phone' => $userObject->phone,
        'account_name' => $userObject->account_name,
        'email' => $userObject->email,
        'account_status' => $userObject->account_status,
        'real_auth_status' => $userObject->real_auth_status,
        'created_at' => $userObject->created_at,
    ];
}

// 批量写入ES
$result = EsFacade::batchActDoc($indexName, $data);

if ($result['code'] === 0) {
    echo $result['msg'];
}
```

---

#### 2. 批量删除文档 - `batchDeleteDoc`

批量删除文档，支持单ID、多ID、条件删除三种模式。

##### 方法签名

```php
EsFacade::batchDeleteDoc(string $index, string|array $dataOrCondition): array
```

##### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| index | string | 是 | 索引名 |
| dataOrCondition | string\|array | 是 | 删除条件：字符串(单ID) / 一维数组(多ID) / 二维数组(查询条件) |

##### 返回值

```php
[
    'code' => 0,
    'msg' => 'es批量删除成功',
    'status' => 1,
    'error' => [...]
]
```

##### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

$userCollection = ShardHelperFacade::queryAllShards(
    User::class,
    function ($query) {
        $query->where('account_status', 1);
    },
);

// 1. 单个文档ID删除
$singleDocId = $userCollection->first()->biz_id;
$result1 = EsFacade::batchDeleteDoc($indexName, $singleDocId);

// 2. 多个文档ID删除（一维数组）
$multiDocIds = [
    $userCollection[1]->biz_id,
    $userCollection[2]->biz_id
];
$result2 = EsFacade::batchDeleteDoc($indexName, $multiDocIds);

// 3. 条件删除（二维数组）
$deleteQuery = [
    'match' => [
        'account_status' => 1
    ]
];
$result3 = EsFacade::batchDeleteDoc($indexName, $deleteQuery);

if ($result1['code'] === 0) {
    echo $result1['msg'];
}
```

---

### 四、高级功能

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

## 完整实战示例

### 示例1: 用户索引完整流程

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

// 1. 初始化ES
EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);

$indexName = config('common_es.indices.user.users');

// 2. 检查索引是否存在
if (!EsFacade::indexExists($indexName)) {
    echo "索引不存在，准备创建..." . PHP_EOL;
    
    // 3. 创建索引
    $result = EsFacade::createIndex($indexName, [
        'settings' => [
            'number_of_shards' => config('common_es.setting.shard_number'),
            'number_of_replicas' => config('common_es.setting.replicas_number'),
        ],
        'mappings' => [
            'properties' => [
                'account_name' => ['type' => 'keyword', 'ignore_above' => 256],
                'email' => ['type' => 'keyword', 'ignore_above' => 256],
                'account_status' => ['type' => 'integer'],
            ]
        ]
    ]);
    
    if ($result['code'] === 0) {
        echo "索引创建成功" . PHP_EOL;
    }
}

// 4. 创建文档
$userData = [
    'account_name' => 'test_user',
    'email' => 'test@example.com',
    'account_status' => 1,
];

$result = EsFacade::createDoc($indexName, $userData, 'user_001');
echo $result['msg'] . PHP_EOL;

// 5. 搜索文档
$searchResult = EsFacade::searchDoc($indexName, [
    'match' => ['account_name' => 'test_user']
]);

if ($searchResult['code'] === 0) {
    echo "找到 " . $searchResult['data']['hits']['total']['value'] . " 条记录\n";
}

// 6. 更新文档
$updateResult = EsFacade::updateDoc($indexName, 'user_001', [
    'email' => 'newemail@example.com'
]);
echo $updateResult['msg'] . PHP_EOL;

// 7. 删除文档
$deleteResult = EsFacade::deleteDoc($indexName, 'user_001');
echo $deleteResult['msg'] . PHP_EOL;
```

---

### 示例2: 批量同步用户数据到ES

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * 批量同步用户数据到ES
 */
function syncUsersToEs()
{
    // 初始化ES
    EsFacade::init(
        config('common_es.host'),
        config('common_es.user'),
        config('common_es.password')
    );
    
    $indexName = config('common_es.indices.user.users');
    
    // 查询需要同步的用户
    $userCollection = ShardHelperFacade::queryAllShards(
        User::class,
        function ($query) {
            $query->where('account_status', 1);
        },
    );
    
    if ($userCollection->isEmpty()) {
        echo "没有需要同步的用户";
        return;
    }
    
    // 构造批量数据
    $data = [];
    foreach ($userCollection as $userObject) {
        $data[] = [
            '_docId' => $userObject->biz_id,
            'phone' => $userObject->phone,
            'account_name' => $userObject->account_name,
            'email' => $userObject->email,
            'account_status' => $userObject->account_status,
            'real_auth_status' => $userObject->real_auth_status,
            'created_at' => $userObject->created_at,
        ];
    }
    
    // 批量写入ES
    $result = EsFacade::batchActDoc($indexName, $data);
    
    if ($result['code'] === 0) {
        echo "成功同步 {$userCollection->count()} 个用户到ES";
    } else {
        echo "同步失败: " . $result['msg'];
    }
}

// 执行同步
syncUsersToEs();
```

---

### 示例3: 批量清理无效用户数据

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * 批量清理ES中的无效用户数据
 */
function cleanInvalidUsersFromEs()
{
    // 初始化ES
    EsFacade::init(
        config('common_es.host'),
        config('common_es.user'),
        config('common_es.password')
    );
    
    $indexName = config('common_es.indices.user.users');
    
    // 方式1: 按条件批量删除
    $deleteQuery = [
        'term' => [
            'account_status' => 0  // 删除账户状态为0的用户
        ]
    ];
    
    $result = EsFacade::batchDeleteDoc($indexName, $deleteQuery);
    
    if ($result['code'] === 0) {
        echo "批量清理完成";
    }
    
    // 方式2: 按ID列表批量删除
    // $invalidUserIds = ['user_001', 'user_002', 'user_003'];
    // $result = EsFacade::batchDeleteDoc($indexName, $invalidUserIds);
}

// 执行清理
cleanInvalidUsersFromEs();
```

---

## 注意事项

### 1. 连接配置

- 默认连接地址: `http://127.0.0.1:9200`
- 支持账户密码认证
- 生产环境建议使用配置文件管理ES连接信息
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
5. 使用配置文件存储敏感信息

**Laravel配置示例:**

```env
# config/common_es.php
ES_HOST=http://your-es-server.com:9200
ES_USER=elastic
ES_PASSWORD=your-strong-password-here
```

```php
// 使用
EsFacade::init(
    config('common_es.host'),
    config('common_es.user'),
    config('common_es.password')
);
```

### 3. 性能优化

- **批量操作**: 使用 `batchActDoc` 和 `batchDeleteDoc` 进行批量操作，性能更好
- **分页查询**: 合理设置 `from` 和 `size` 参数，避免返回过多数据
- **索引优化**: 根据业务需求合理设计索引映射和分片配置
- **字段配置**: 不需要搜索的字段设置 `'index' => false` 可以节省存储空间

### 4. 数据一致性

- 更新操作建议使用局部更新(`isPartial = true`)
- 批量操作注意事务处理
- 数据同步时建议使用队列异步处理
- 定期检查ES与主数据库的数据一致性

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
