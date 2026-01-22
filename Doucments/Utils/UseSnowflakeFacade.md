# SnowflakeFacade 使用说明

## 概述

`SnowflakeFacade` 提供了基于雪花算法的分布式唯一ID生成功能，基于 `godruoyi/php-snowflake` 库实现。雪花算法生成的ID是递增的、唯一的、有序的，非常适合用于用户ID、订单ID等场景。

## 特性

- ✅ **全局唯一**: 在分布式系统中保证ID唯一性
- ✅ **时间有序**: ID按时间递增，便于排序
- ✅ **高性能**: 本地生成，无需网络请求
- ✅ **可配置**: 支持自定义机器ID和起始时间
- ✅ **无依赖**: 不依赖外部配置文件或环境变量

## 安装

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;
```

## 快速开始

### 生成雪花ID

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

// 最简单的调用（使用默认配置）
$userId = SnowflakeFacade::generate();
echo "用户ID: " . $userId;  // 输出: 用户ID: 1893980458660888577
```

## 功能方法

### 1. 生成雪花ID - `generate`

生成一个唯一的雪花ID，返回 int 类型（64位整数）。

#### 方法签名

```php
SnowflakeFacade::generate(?int $machineId = null, ?string $startTime = null): int
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| machineId | int\|null | 否 | 机器ID，不传则默认为1，用于分布式部署时不同节点配置不同值 |
| startTime | string\|null | 否 | 起始时间，格式：Y-m-d H:i:s，不传则使用当天零点 |

#### 返回值

返回 int 类型的雪花ID（64位整数）。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

// 基础调用 - 使用默认配置
$userId = SnowflakeFacade::generate();
echo "用户ID: " . $userId . "\n";

// 指定机器ID（分布式部署时不同节点使用不同machineId）
$order1Id = SnowflakeFacade::generate(1);  // 节点1生成的订单ID
$order2Id = SnowflakeFacade::generate(2);  // 节点2生成的订单ID
echo "订单1ID: " . $order1Id . "\n";
echo "订单2ID: " . $order2Id . "\n";

// 完全自定义（指定机器ID和起始时间）
$customId = SnowflakeFacade::generate(3, '2025-06-01 00:00:00');
echo "自定义ID: " . $customId . "\n";
```

---

### 2. 生成雪花ID（别名方法） - `id`

`generate()` 方法的别名，功能完全相同。

#### 方法签名

```php
SnowflakeFacade::id(?int $machineId = null, ?string $startTime = null): int
```

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

// 与 generate() 方法功能相同
$userId = SnowflakeFacade::id();
echo "用户ID: " . $userId . "\n";

// 指定参数
$orderId = SnowflakeFacade::id(2);
echo "订单ID: " . $orderId . "\n";
```

---

### 3. 解析雪花ID - `parse`

解析雪花ID，获取其中的时间戳、机器ID、序列号等信息。

#### 方法签名

```php
SnowflakeFacade::parse(string|int $id, ?int $machineId = null, ?string $startTime = null): array
```

#### 参数说明

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | string\|int | 是 | 雪花ID |
| machineId | int\|null | 否 | 机器ID，需要与生成时一致 |
| startTime | string\|null | 否 | 起始时间，格式：Y-m-d H:i:s，需要与生成时一致 |

#### 返回值

返回包含雪花ID详细信息的数组，包括时间戳、机器ID、序列号等。

#### 使用示例

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

// 生成ID
$snowflakeId = SnowflakeFacade::generate(1, '2025-01-01 00:00:00');
echo "生成的ID: " . $snowflakeId . "\n";

// 解析ID（需要使用相同的配置）
$detail = SnowflakeFacade::parse($snowflakeId, 1, '2025-01-01 00:00:00');
print_r($detail);

// 输出示例:
// Array (
//     [sequence] => 0
//     [worker_id] => 1
//     [datacenter_id] => 0
//     [timestamp] => 113415412
// )
```

---

## 完整示例

### 示例1: 用户ID生成

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

/**
 * 生成用户ID
 */
function generateUserId(): int
{
    // 使用默认配置生成用户ID
    return SnowflakeFacade::generate();
}

// 使用示例
$userId = generateUserId();
echo "新用户ID: " . $userId . "\n";

// 保存到数据库
// User::create([
//     'id' => $userId,
//     'name' => '张三',
//     'email' => 'zhangsan@example.com'
// ]);
```

### 示例2: 订单ID生成（分布式场景）

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

/**
 * 生成订单ID
 *
 * @param int $serverId 服务器ID（分布式部署时不同服务器使用不同ID）
 * @return int
 */
function generateOrderId(int $serverId): int
{
    // 不同服务器使用不同的machineId，确保全局唯一
    return SnowflakeFacade::generate($serverId);
}

// 使用示例
// 服务器1生成的订单
$orderId1 = generateOrderId(1);
echo "服务器1订单ID: " . $orderId1 . "\n";

// 服务器2生成的订单
$orderId2 = generateOrderId(2);
echo "服务器2订单ID: " . $orderId2 . "\n";

// 服务器3生成的订单
$orderId3 = generateOrderId(3);
echo "服务器3订单ID: " . $orderId3 . "\n";
```

### 示例3: 业务ID生成器封装

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

/**
 * 业务ID生成器
 */
class BusinessIdGenerator
{
    /**
     * 生成用户ID
     *
     * @return int
     */
    public static function generateUserId(): int
    {
        return SnowflakeFacade::generate(1); // 用户模块使用machineId=1
    }

    /**
     * 生成订单ID
     *
     * @return int
     */
    public static function generateOrderId(): int
    {
        return SnowflakeFacade::generate(2); // 订单模块使用machineId=2
    }

    /**
     * 生成商品ID
     *
     * @return int
     */
    public static function generateProductId(): int
    {
        return SnowflakeFacade::generate(3); // 商品模块使用machineId=3
    }

    /**
     * 生成支付流水号
     *
     * @return int
     */
    public static function generatePaymentId(): int
    {
        return SnowflakeFacade::generate(4); // 支付模块使用machineId=4
    }
}

// 使用示例
echo "用户ID: " . BusinessIdGenerator::generateUserId() . "\n";
echo "订单ID: " . BusinessIdGenerator::generateOrderId() . "\n";
echo "商品ID: " . BusinessIdGenerator::generateProductId() . "\n";
echo "支付ID: " . BusinessIdGenerator::generatePaymentId() . "\n";
```

### 示例4: 批量生成ID

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

/**
 * 批量生成ID
 *
 * @param int $count 生成数量
 * @param int|null $machineId 机器ID
 * @return array
 */
function batchGenerateIds(int $count, ?int $machineId = null): array
{
    $ids = [];
    for ($i = 0; $i < $count; $i++) {
        $ids[] = SnowflakeFacade::generate($machineId);
    }
    return $ids;
}

// 使用示例
$userIds = batchGenerateIds(10, 1); // 批量生成10个用户ID
echo "批量生成的用户ID:\n";
foreach ($userIds as $index => $id) {
    echo ($index + 1) . ". " . $id . "\n";
}
```

### 示例5: ID解析和验证

```php
<?php

use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

/**
 * 解析雪花ID并格式化输出
 *
 * @param string $id 雪花ID
 * @param int $machineId 机器ID
 * @param string $startTime 起始时间
 * @return void
 */
function analyzeSnowflakeId(string $id, int $machineId = 1, string $startTime = '2025-01-01 00:00:00'): void
{
    $detail = SnowflakeFacade::parse($id, $machineId, $startTime);

    echo "雪花ID分析:\n";
    echo "----------\n";
    echo "ID: " . $id . "\n";
    echo "机器ID: " . $detail['worker_id'] . "\n";
    echo "数据中心ID: " . $detail['datacenter_id'] . "\n";
    echo "序列号: " . $detail['sequence'] . "\n";
    echo "相对时间戳: " . $detail['timestamp'] . " 毫秒\n";

    // 计算实际时间
    $startTimestamp = strtotime($startTime) * 1000;
    $actualTimestamp = ($detail['timestamp'] + $startTimestamp) / 1000;
    $actualTime = date('Y-m-d H:i:s', $actualTimestamp);
    echo "生成时间: " . $actualTime . "\n";
    echo "----------\n";
}

// 使用示例
$userId = SnowflakeFacade::generate(1, '2025-01-01 00:00:00');
echo "生成的用户ID: " . $userId . "\n\n";

analyzeSnowflakeId($userId, 1, '2025-01-01 00:00:00');
```

---

## 雪花算法原理

### ID结构

雪花算法生成的64位ID由三部分组成：

```
0 | 00000000000000000000000000000000000000 | 0000000000 | 000000000000
---|---------------------------------------|------------|------------
 1 |              41位时间戳               | 10位机器ID | 12位序列号
```

| 部分 | 位数 | 说明 |
|------|------|------|
| 符号位 | 1位 | 始终为0，保证ID为正数 |
| 时间戳 | 41位 | 相对起始时间的毫秒数，可用约69年 |
| 机器ID | 10位 | 机器标识，最多支持1024台机器 |
| 序列号 | 12位 | 毫秒内序列号，每毫秒可生成4096个ID |

### 优势

1. **高性能**: 本地生成，无需网络请求或数据库查询
2. **唯一性**: 在分布式系统中保证ID全局唯一
3. **有序性**: ID按时间递增，便于排序和索引
4. **紧凑性**: 64位整数，存储空间小
5. **无状态**: 不需要中央协调器

---

## 分布式部署

### 机器ID分配

在分布式系统中，不同节点需要使用不同的机器ID：

```php
// 服务器配置
$servers = [
    'server1' => ['ip' => '192.168.1.10', 'machineId' => 1],
    'server2' => ['ip' => '192.168.1.11', 'machineId' => 2],
    'server3' => ['ip' => '192.168.1.12', 'machineId' => 3],
];

// 每个服务器使用自己的machineId
$orderId = SnowflakeFacade::generate($servers['server1']['machineId']);
```

### 微服务场景

不同微服务可以使用不同的机器ID段：

```php
// 用户服务 - machineId: 1-10
$userId = SnowflakeFacade::generate(1);

// 订单服务 - machineId: 11-20
$orderId = SnowflakeFacade::generate(11);

// 支付服务 - machineId: 21-30
$paymentId = SnowflakeFacade::generate(21);
```

---

## 最佳实践

### 1. 机器ID管理

```php
// 建议从配置文件读取机器ID
class SnowflakeConfig
{
    public static function getMachineId(): int
    {
        return config('snowflake.machine_id', 1);
    }

    public static function getStartTime(): string
    {
        return config('snowflake.start_time', date('Y-m-d') . ' 00:00:00');
    }
}

// 使用
$userId = SnowflakeFacade::generate(
    SnowflakeConfig::getMachineId(),
    SnowflakeConfig::getStartTime()
);
```

### 2. ID类型转换

```php
// 生成ID（int类型）
$id = SnowflakeFacade::generate();

// 存储到数据库（使用BIGINT）
DB::table('users')->insert([
    'id' => $id,  // int类型
    'name' => '张三'
]);

// 从数据库读取
$storedId = $user->id;  // 已是int类型
```

### 3. 错误处理

```php
use YouHuJun\Tool\App\Facade\V1\Utils\Snowflake\SnowflakeFacade;

try {
    $id = SnowflakeFacade::generate($machineId);
    // 保存ID到数据库或进行其他操作
} catch (\Exception $e) {
    // 记录错误日志
    error_log("生成雪花ID失败: " . $e->getMessage());
    // 降级处理：使用UUID或其他方案
    $id = uniqid('fallback_', true);
}
```

---

## 注意事项

### 1. 时钟回拨问题

雪花算法依赖服务器时间，如果服务器时钟发生回拨（时间倒退），可能导致ID重复。

**解决方案**：
- 使用NTP服务保持时间同步
- 监控服务器时钟
- 时钟回拨时暂停服务或使用备用方案

### 2. 机器ID唯一性

确保分布式环境中每个节点使用不同的机器ID，否则可能产生重复ID。

**解决方案**：
- 集中管理机器ID分配
- 使用服务发现机制自动分配
- 在配置文件中明确指定

### 3. 存储类型

雪花ID是64位整数，存储时需要注意：

**MySQL**:
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY,  -- 推荐：无符号BIGINT
    name VARCHAR(100)
);
```

**Redis**:
```php
// 字符串存储
Redis::set('user:' . $id, $userData);

// Hash存储
Redis::hset('users', $id, $userData);
```

### 4. 序列号溢出

在同一毫秒内如果请求量超过4096个，序列号会溢出，需要等待下一毫秒。

**解决方案**：
- 高并发场景需要考虑限流或分库分表
- 使用多个Snowflake实例（不同machineId）

---

## 常见问题

### Q1: 雪花ID和UUID有什么区别？

| 特性 | 雪花ID | UUID |
|------|--------|------|
| 长度 | 19位数字 | 36位字符串 |
| 有序性 | 时间有序 | 无序 |
| 性能 | 高性能（本地生成） | 较低（随机生成） |
| 索引友好 | 是 | 否 |
| 存储空间 | 小（8字节） | 大（16字节） |

### Q2: 如何确保机器ID不重复？

- 方案1：集中配置管理
- 方案2：使用数据库自增分配
- 方案3：使用ZooKeeper/etcd协调
- 方案4：使用MAC地址/IP地址哈希生成

### Q3: 雪花ID会耗尽吗？

雪花ID的41位时间戳部分可支持约69年（2^41毫秒），对于大多数应用场景足够。如果需要更长的时间范围，可以通过调整起始时间或使用其他ID生成方案。

### Q4: 为什么返回int类型而不是字符串？

雪花ID返回int类型（64位整数）的优势：
- 性能更好，无需类型转换
- 存储空间更小（8字节）
- 计算和比较更高效
- 适合64位PHP环境

**注意**: 在32位PHP环境中，雪花ID会超出整数最大值，建议使用64位PHP环境。

---

## 方法参考

| 方法 | 说明 | 参数 |
|------|------|------|
| `generate()` | 生成雪花ID | `$machineId` (可选), `$startTime` (可选) |
| `id()` | 生成雪花ID（别名） | `$machineId` (可选), `$startTime` (可选) |
| `parse()` | 解析雪花ID | `$id`, `$machineId` (可选), `$startTime` (可选) |
