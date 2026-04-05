# ShardFacade 使用说明

## 概述

`ShardFacade` 提供了数据库分片计算功能,支持分库分表策略,适用于大流量、大数据量的业务场景。通过业务ID(用户UID/店铺UID等业务实体ID)计算分片信息,实现数据的水平拆分。

## 特性

- ✅ **分库分表**: 支持同时进行数据库分片和表分片
- ✅ **多配置支持**: 支持业务表和映射表等多场景独立配置
- ✅ **微服务适配**: 每个服务可配置独立的分片规则
- ✅ **简单易用**: 提供便捷的静态方法调用
- ✅ **强制传参**: 所有静态方法必须传`configKey`,避免配置混淆

## 安装

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
```

## 快速开始

### 基础使用

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

// 1. 初始化配置（服务提供者里）
ShardFacade::setMultiConfig('youhujun', [
    'db_count' => 2,
    'table_count' => 10,
    'db_prefix' => 'ds_',
    'default_db' => 'ds_0',
]);
ShardFacade::setMultiConfig('shard_map', [
    'db_count' => 1,
    'table_count' => 5,
    'db_prefix' => 'dssp_',
    'default_db' => 'dssp_0',
]);

// 2. 业务表调用（必须传configKey）
$shardInfo = ShardFacade::calc(123456, 'youhujun');
print_r($shardInfo);
// 输出:
// Array (
//     [db] => ds_0
//     [table_no] => 6
//     [shard_key] => 6
// )

// 3. 映射表调用（必须传configKey）
$shardInfo = ShardFacade::calc(123456, 'shard_map');
print_r($shardInfo);
// 输出:
// Array (
//     [db] => dssp_0
//     [table_no] => 1
//     [shard_key] => 1
// )
```

## 功能方法

### 1. 计算分片信息 - `calc`

根据业务ID(用户UID/店铺UID等业务实体ID)计算分片库、分片表和分片键。

#### 方法签名

```php
ShardFacade::calc(string|int $uid, string $configKey): array
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| uid | string\|int | 是 | 业务ID(用户UID/店铺UID等业务实体ID,所有模块的核心分片依据) |
| configKey | string | 是 | 配置标识(如youhujun/shard_map),必须传入,无默认值 |

#### 返回值

返回包含分片信息的数组:
- `db`: 数据库名
- `table_no`: 表编号
- `shard_key`: 分片键值

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

// 初始化配置
ShardFacade::setMultiConfig('youhujun', [
    'db_count' => 2,
    'table_count' => 4,
    'db_prefix' => 'ds_',
    'default_db' => 'ds_0',
]);

// 计算分片信息（必须传configKey）
$info = ShardFacade::calc(1001, 'youhujun');
echo "数据库: " . $info['db'] . "\n";        // ds_1
echo "表编号: " . $info['table_no'] . "\n";   // 1
echo "分片键: " . $info['shard_key'] . "\n";   // 1

$info = ShardFacade::calc(2002, 'youhujun');
echo "数据库: " . $info['db'] . "\n";        // ds_0
echo "表编号: " . $info['table_no'] . "\n";   // 2
echo "分片键: " . $info['shard_key'] . "\n";   // 2
```

---

### 2. 获取分片表名 - `getTableName`

根据业务ID和基础表名获取完整的分片表名。

#### 方法签名

```php
ShardFacade::getTableName(string|int $uid, string $baseTable, string $configKey): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| uid | string\|int | 是 | 业务ID(用户UID/店铺UID等业务实体ID) |
| baseTable | string | 是 | 基础表名（如 users/order/feed/shop） |
| configKey | string | 是 | 配置标识,必须传入,无默认值 |

#### 返回值

返回完整的分片表名，格式为 `{base_table}_{table_no}`。

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

ShardFacade::setMultiConfig('youhujun', [
    'db_count' => 2,
    'table_count' => 4,
    'db_prefix' => 'ds_',
    'default_db' => 'ds_0',
]);

$user1Id = 1001;
$user2Id = 2002;

// 获取用户分片表名（必须传configKey）
$user1Table = ShardFacade::getTableName($user1Id, 'users', 'youhujun');
$user2Table = ShardFacade::getTableName($user2Id, 'users', 'youhujun');

echo "用户1表名: " . $user1Table . "\n";  // users_1
echo "用户2表名: " . $user2Table . "\n";  // users_2

// 获取订单分片表名
$order1Table = ShardFacade::getTableName($user1Id, 'orders', 'youhujun');
$order2Table = ShardFacade::getTableName($user2Id, 'orders', 'youhujun');

echo "订单1表名: " . $order1Table . "\n";  // orders_1
echo "订单2表名: " . $order2Table . "\n";  // orders_2

// 获取动态内容分片表名
$feed1Table = ShardFacade::getTableName($user1Id, 'feeds', 'youhujun');
$feed2Table = ShardFacade::getTableName($user2Id, 'feeds', 'youhujun');

echo "动态1表名: " . $feed1Table . "\n";  // feeds_1
echo "动态2表名: " . $feed2Table . "\n";  // feeds_2
```

---

### 3. 获取数据库名 - `getDbName`

根据业务ID获取分片数据库名。

#### 方法签名

```php
ShardFacade::getDbName(string|int $uid, string $configKey): string
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| uid | string\|int | 是 | 业务ID(用户UID/店铺UID等业务实体ID) |
| configKey | string | 是 | 配置标识,必须传入,无默认值 |

#### 返回值

返回分片数据库名。

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

ShardFacade::setMultiConfig('youhujun', [
    'db_count' => 4,
    'db_prefix' => 'ds_',
    'default_db' => 'ds_0',
]);

$db1 = ShardFacade::getDbName(1001, 'youhujun');
$db2 = ShardFacade::getDbName(2002, 'youhujun');
$db3 = ShardFacade::getDbName(3003, 'youhujun');
$db4 = ShardFacade::getDbName(4004, 'youhujun');

echo "用户1数据库: " . $db1 . "\n";  // ds_1
echo "用户2数据库: " . $db2 . "\n";  // ds_2
echo "用户3数据库: " . $db3 . "\n";  // ds_3
echo "用户4数据库: " . $db4 . "\n";  // ds_0
```

---

### 4. 获取分片键 - `getShardKey`

根据业务ID获取分片键值。

#### 方法签名

```php
ShardFacade::getShardKey(string|int $uid, string $configKey): int
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| uid | string\|int | 是 | 业务ID(用户UID/店铺UID等业务实体ID) |
| configKey | string | 是 | 配置标识,必须传入,无默认值 |

#### 返回值

返回分片键值（整数）。

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

ShardFacade::setMultiConfig('youhujun', [
    'table_count' => 8,
    'db_prefix' => 'ds_',
    'default_db' => 'ds_0',
]);

$key1 = ShardFacade::getShardKey(1001, 'youhujun');
$key2 = ShardFacade::getShardKey(2002, 'youhujun');
$key3 = ShardFacade::getShardKey(3003, 'youhujun');

echo "用户1分片键: " . $key1 . "\n";  // 1
echo "用户2分片键: " . $key2 . "\n";  // 2
echo "用户3分片键: " . $key3 . "\n";  // 3
```

---

### 5. 配置管理

#### 设置多配置 - `setMultiConfig`

```php
ShardFacade::setMultiConfig(string $configKey, array $config): void
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| configKey | string | 是 | 配置标识（如youhujun/shard_map） |
| config | array | 是 | 配置数组 |

#### 支持的配置项

| 配置项 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| db_count | int | 1 | 分片库数量 |
| table_count | int | 1 | 每库分表数量 |
| db_prefix | string | `ds_` | 数据库前缀 |
| default_db | string | `ds_0` | 默认数据库名 |

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

// 设置业务表配置（在服务提供者里初始化）
ShardFacade::setMultiConfig('youhujun', [
    'db_count' => 4,
    'table_count' => 16,
    'db_prefix' => 'ds_',
    'default_db' => 'ds_0',
]);

// 设置映射表配置
ShardFacade::setMultiConfig('shard_map', [
    'db_count' => 1,
    'table_count' => 5,
    'db_prefix' => 'dssp_',
    'default_db' => 'dssp_0',
]);
```

#### 获取多配置 - `getMultiConfig`

```php
ShardFacade::getMultiConfig(string $configKey, string $key, mixed $default = null): mixed
```

#### 使用示例

```php
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

ShardFacade::setMultiConfig('youhujun', [
    'db_count' => 4,
    'table_count' => 16,
]);

// 获取配置
$dbCount = ShardFacade::getMultiConfig('youhujun', 'db_count');        // 4
$tableCount = ShardFacade::getMultiConfig('youhujun', 'table_count');   // 16
$dbPrefix = ShardFacade::getMultiConfig('youhujun', 'db_prefix');       // ds_

// 获取不存在的配置，使用默认值
$customValue = ShardFacade::getMultiConfig('youhujun', 'custom_key', 'default_value'); // default_value
```

---

## 完整示例

### 示例1: 用户数据分片

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

class UserRepository
{
    public function __construct()
    {
        // 初始化分片配置（在服务提供者里配置）
        ShardFacade::setMultiConfig('youhujun', [
            'db_count' => 4,
            'table_count' => 16,
            'db_prefix' => 'user_db_',
            'default_db' => 'user_db_0',
        ]);
    }

    /**
     * 获取用户
     *
     * @param int $userId 用户ID
     * @return array
     */
    public function getUser(int $userId): array
    {
        // 获取分片信息（必须传configKey）
        $shard = ShardFacade::calc($userId, 'youhujun');
        $tableName = ShardFacade::getTableName($userId, 'users', 'youhujun');

        // 根据分片信息查询数据库
        // $userObject = DB::connection($shard['db'])
        //           ->table($tableName)
        //           ->where('id', $userId)
        //           ->first();

        echo "数据库: " . $shard['db'] . "\n";
        echo "表名: " . $tableName . "\n";
        echo "分片键: " . $shard['shard_key'] . "\n";

        return ['id' => $userId, 'shard' => $shard];
    }

    /**
     * 创建用户
     *
     * @param array $userData 用户数据
     * @return int
     */
    public function createUser(array $userData): int
    {
        $userId = 123456; // 假设生成的用户ID

        // 获取分片表名
        $tableName = ShardFacade::getTableName($userId, 'users', 'youhujun');

        // 插入数据
        // DB::connection($shard['db'])
        //   ->table($tableName)
        //   ->insert($userData);

        echo "用户创建在表: " . $tableName . "\n";

        return $userId;
    }
}

// 使用示例
$repo = new UserRepository();
$userObject = $repo->getUser(1001);
// 输出:
// 数据库: user_db_1
// 表名: users_1
// 分片键: 1
```

### 示例2: 订单数据分片

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

class OrderRepository
{
    public function __construct()
    {
        ShardFacade::setMultiConfig('youhujun', [
            'db_count' => 8,
            'table_count' => 32,
            'db_prefix' => 'order_db_',
            'default_db' => 'order_db_0',
        ]);
    }

    /**
     * 获取用户订单列表
     *
     * @param int $userId 用户ID
     * @return array
     */
    public function getUserOrders(int $userId): array
    {
        $shard = ShardFacade::calc($userId, 'youhujun');
        $tableName = ShardFacade::getTableName($userId, 'orders', 'youhujun');

        echo "查询数据库: " . $shard['db'] . "\n";
        echo "查询表名: " . $tableName . "\n";

        // $orders = DB::connection($shard['db'])
        //            ->table($tableName)
        //            ->where('user_uid', $userId)
        //            ->get();

        return [];
    }

    /**
     * 创建订单
     *
     * @param int $userId 用户ID
     * @param array $orderData 订单数据
     * @return string
     */
    public function createOrder(int $userId, array $orderData): string
    {
        $orderId = time() . mt_rand(1000, 9999); // 生成订单号

        $shard = ShardFacade::calc($userId, 'youhujun');
        $tableName = ShardFacade::getTableName($userId, 'orders', 'youhujun');

        // DB::connection($shard['db'])
        //   ->table($tableName)
        //   ->insert([
        //       'id' => $orderId,
        //       'user_uid' => $userId,
        //       ...$orderData
        //   ]);

        echo "订单创建在: " . $shard['db'] . '.' . $tableName . "\n";

        return $orderId;
    }
}

// 使用示例
$orderRepo = new OrderRepository();
$orders = $orderRepo->getUserOrders(2002);
// 输出:
// 查询数据库: order_db_2
// 查询表名: orders_2
```

### 示例3: 动态内容分片

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

class FeedRepository
{
    public function __construct()
    {
        ShardFacade::setMultiConfig('youhujun', [
            'db_count' => 2,
            'table_count' => 8,
            'db_prefix' => 'feed_db_',
            'default_db' => 'feed_db_0',
        ]);
    }

    /**
     * 发布动态
     *
     * @param int $userId 用户ID
     * @param string $content 动态内容
     * @return int
     */
    public function publishFeed(int $userId, string $content): int
    {
        $feedId = time() . mt_rand(100, 999);

        $shard = ShardFacade::calc($userId, 'youhujun');
        $tableName = ShardFacade::getTableName($userId, 'feeds', 'youhujun');

        echo "动态存储在: " . $shard['db'] . '.' . $tableName . "\n";

        // DB::connection($shard['db'])
        //   ->table($tableName)
        //   ->insert([
        //       'id' => $feedId,
        //       'user_uid' => $userId,
        //       'content' => $content,
        //   ]);

        return $feedId;
    }

    /**
     * 获取用户动态
     *
     * @param int $userId 用户ID
     * @return array
     */
    public function getUserFeeds(int $userId): array
    {
        $shard = ShardFacade::calc($userId, 'youhujun');
        $tableName = ShardFacade::getTableName($userId, 'feeds', 'youhujun');

        echo "查询动态: " . $shard['db'] . '.' . $tableName . "\n";

        // $feeds = DB::connection($shard['db'])
        //           ->table($tableName)
        //           ->where('user_uid', $userId)
        //           ->orderBy('id', 'desc')
        //           ->get();

        return [];
    }
}

// 使用示例
$feedRepo = new FeedRepository();
$feedRepo->publishFeed(3003, '这是一条新的动态内容');
// 输出:
// 动态存储在: feed_db_3.feeds_3
```

### 示例4: 业务表与映射表分离

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

// 1. 初始化配置（服务提供者里配置）
ShardFacade::setMultiConfig('youhujun', [
    'db_count' => 4,
    'table_count' => 16,
    'db_prefix' => 'ds_',
    'default_db' => 'ds_0',
]);

ShardFacade::setMultiConfig('shard_map', [
    'db_count' => 1,
    'table_count' => 5,
    'db_prefix' => 'dssp_',
    'default_db' => 'dssp_0',
]);

// 2. 业务表操作（使用youhujun配置）
function getUserData(int $userId): array
{
    $shard = ShardFacade::calc($userId, 'youhujun');
    $userTable = ShardFacade::getTableName($userId, 'users', 'youhujun');
    echo "用户业务数据: " . $shard['db'] . '.' . $userTable . "\n";
    return [];
}

// 3. 映射表操作（使用shard_map配置）
function getShardMap(int $userId): array
{
    $shard = ShardFacade::calc($userId, 'shard_map');
    $mapTable = ShardFacade::getTableName($userId, 'shard_map', 'shard_map');
    echo "用户分片映射: " . $shard['db'] . '.' . $mapTable . "\n";
    return [];
}

// 4. 不同业务场景使用不同配置
$userId = 1001;
getUserData($userId);   // 用户业务数据: ds_1.users_1
getShardMap($userId);   // 用户分片映射: dssp_0.shard_map_1
```

### 示例5: 微服务场景多配置

```php
<?php

use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

// 1. 用户服务配置
ShardFacade::setMultiConfig('user_service', [
    'db_count' => 8,
    'table_count' => 32,
    'db_prefix' => 'user_db_',
    'default_db' => 'user_db_0',
]);

// 2. 订单服务配置
ShardFacade::setMultiConfig('order_service', [
    'db_count' => 16,
    'table_count' => 64,
    'db_prefix' => 'order_db_',
    'default_db' => 'order_db_0',
]);

// 3. 店铺服务配置
ShardFacade::setMultiConfig('shop_service', [
    'db_count' => 4,
    'table_count' => 16,
    'db_prefix' => 'shop_db_',
    'default_db' => 'shop_db_0',
]);

// 4. 各服务使用各自配置
function userOperation(int $userId): array
{
    $shard = ShardFacade::calc($userId, 'user_service');
    echo "用户服务: " . $shard['db'] . "\n";
    return [];
}

function orderOperation(int $orderId): array
{
    $shard = ShardFacade::calc($orderId, 'order_service');
    echo "订单服务: " . $shard['db'] . "\n";
    return [];
}

function shopOperation(int $shopId): array
{
    $shard = ShardFacade::calc($shopId, 'shop_service');
    echo "店铺服务: " . $shard['db'] . "\n";
    return [];
}

// 调用示例
userOperation(1001);     // 用户服务: user_db_1
orderOperation(2001);    // 订单服务: order_db_1
shopOperation(3001);    // 店铺服务: shop_db_1
```

---

## 分片策略

### 分片算法

当前采用 **取模分片** 策略:

```
数据库编号 = uid % db_count
表编号     = uid % table_count
分片键     = 表编号
```

### 示例计算

假设配置:
- `db_count = 2` (2个分片库)
- `table_count = 4` (每库4张表)

| 业务ID(用户ID/店铺ID等) | 数据库 | 表编号 | 分片键 | 完整表名 |
|-------------------|--------|--------|--------|----------|
| 1001 | ds_1 | 1 | 1 | users_1 |
| 1002 | ds_0 | 2 | 2 | users_2 |
| 1003 | ds_1 | 3 | 3 | users_3 |
| 1004 | ds_0 | 0 | 0 | users_0 |
| 2001 | ds_1 | 1 | 1 | users_1 |

### 分片优势

1. **数据均衡**: 通过取模算法实现数据均匀分布
2. **简单高效**: 计算简单,性能高
3. **易于扩展**: 增加分片库或表时容易调整
4. **全局统一**: 所有模块共享同一套规则

---

## 最佳实践

### 1. 应用启动时统一配置

```php
// bootstrap.php 或服务提供者中统一配置
ShardFacade::setMultiConfig('youhujun', [
    'db_count' => config('shard.db_count', 4),
    'table_count' => config('shard.table_count', 16),
    'db_prefix' => config('shard.db_prefix', 'ds_'),
    'default_db' => config('shard.default_db', 'ds_0'),
]);
```

### 2. 封装 Repository 层

```php
class BaseRepository
{
    protected string $baseTable;
    protected string $configKey;

    public function __construct(string $baseTable, string $configKey = 'youhujun')
    {
        $this->baseTable = $baseTable;
        $this->configKey = $configKey;
    }

    protected function getTableName(int $userId): string
    {
        return ShardFacade::getTableName($userId, $this->baseTable, $this->configKey);
    }

    protected function getDbName(int $userId): string
    {
        return ShardFacade::getDbName($userId, $this->configKey);
    }
}

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('users', 'youhujun');
    }

    public function find(int $userId): ?array
    {
        $tableName = $this->getTableName($userId);
        $dbName = $this->getDbName($userId);

        // 查询逻辑...
    }
}
```

### 3. 数据库连接管理

```php
class DBManager
{
    /**
     * 获取分片数据库连接
     */
    public static function getConnection(int $userId, string $configKey = 'youhujun')
    {
        $dbName = ShardFacade::getDbName($userId, $configKey);

        // 检查连接是否存在,不存在则创建
        if (!DB::connection($dbName)) {
            DB::connect($dbName);
        }

        return DB::connection($dbName);
    }

    /**
     * 在分片表上执行查询
     */
    public static function query(int $userId, string $table, callable $callback, string $configKey = 'youhujun')
    {
        $connection = self::getConnection($userId, $configKey);
        $tableName = ShardFacade::getTableName($userId, $table, $configKey);

        return $callback($connection->table($tableName));
    }
}

// 使用示例
$result = DBManager::query(1001, 'users', function($table) {
    return $table->where('id', 1001)->first();
}, 'youhujun');
```

### 4. 分片迁移

```php
class ShardMigration
{
    /**
     * 扩展分片数量
     */
    public static function expandShards(int $newDbCount, int $newTableCount, string $configKey = 'youhujun')
    {
        // 1. 更新配置
        $oldDbCount = ShardFacade::getMultiConfig($configKey, 'db_count');
        $oldTableCount = ShardFacade::getMultiConfig($configKey, 'table_count');

        ShardFacade::setMultiConfig($configKey, [
            'db_count' => $newDbCount,
            'table_count' => $newTableCount,
        ]);

        // 2. 迁移数据
        for ($uid = 0; $uid < 10000; $uid++) {
            // 计算新分片位置
            $newShard = ShardFacade::calc($uid, $configKey);
            $newTable = ShardFacade::getTableName($uid, 'users', $configKey);

            // 从旧分片读取数据
            // $oldData = $this->getDataFromOldShard($uid);

            // 写入新分片
            // $this->writeToNewShard($newTable, $oldData);
        }

        echo "分片迁移完成\n";
    }
}

// 执行迁移
ShardMigration::expandShards(8, 32, 'youhujun');
```

---

## 注意事项

### 1. 分片键选择

- **推荐使用稳定的业务ID**: 推荐使用用户UID、店铺UID等业务实体ID作为分片依据,保证同一实体的数据在同一分片
- **避免使用自增ID**: 自增ID会导致数据分布不均匀
- **保持稳定性**: 业务ID一旦确定,不要随意修改
- **通用性**: 业务ID可以是用户UID、店铺UID、商家UID等各种业务实体ID

### 2. 分片数量规划

- **初始规划**: 预估3-5年的数据量,预留扩展空间
- **避免频繁扩容**: 分片扩容需要数据迁移,成本较高
- **合理配置**: 根据实际业务量选择合适的分片数量

### 3. 跨分片查询

- **避免跨分片JOIN**: 不同用户的关联查询需要在应用层处理
- **聚合查询**: 需要遍历所有分片,在应用层聚合结果
- **事务处理**: 跨分片事务较复杂,建议在设计时避免

### 4. 数据迁移

- **停机迁移**: 扩容时可能需要停机迁移
- **双写策略**: 迁移期间新旧分片双写,降低风险
- **验证数据**: 迁移完成后需要验证数据一致性

---

## 常见问题

### Q1: 什么时候需要分片？

以下情况建议使用分片:
- 单表数据量超过千万级别
- 数据库单机性能瓶颈
- 需要水平扩展能力
- 写入/查询压力大

### Q2: 如何选择分片数量？

- **数据库数量**: 根据服务器资源和访问量决定,通常 2-8 个
- **表数量**: 根据数据量和查询压力决定,通常 16-64 张

### Q3: 分片后如何保证数据一致性？

- **单分片事务**: 同一用户的数据在同一分片,可以使用事务
- **跨分片**: 使用最终一致性,通过消息队列保证数据同步
- **分布式事务**: 考虑使用 TCC 或 Saga 模式

### Q4: 如何扩容分片？

1. 预先创建新的分片库和表
2. 更新分片配置
3. 按新规则迁移数据
4. 验证数据一致性
5. 切换流量到新分片

### Q5: 分片和主从复制可以同时使用吗？

可以,两者是互补的:
- **分片**: 解决数据量大和写入性能问题
- **主从复制**: 解决读取性能和高可用问题

通常架构: 分片库 → 主从复制

---

## 方法参考

| 方法 | 说明 | 参数 |
|------|------|------|
| `setMultiConfig()` | 设置多分片配置 | `$configKey` (配置标识), `$config` (配置数组) |
| `getMultiConfig()` | 获取多配置值 | `$configKey` (配置标识), `$key` (配置键), `$default` (默认值) |
| `calc()` | 计算分片信息 | `$uid` (业务ID), `$configKey` (配置标识) |
| `getTableName()` | 获取分片表名 | `$uid` (业务ID), `$baseTable` (基础表名), `$configKey` (配置标识) |
| `getDbName()` | 获取数据库名 | `$uid` (业务ID), `$configKey` (配置标识) |
| `getShardKey()` | 获取分片键 | `$uid` (业务ID), `$configKey` (配置标识) |
