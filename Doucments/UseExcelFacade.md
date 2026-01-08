# ExcelFacade 使用说明

## 概述

`ExcelFacade` 提供了Excel文件的导入导出功能，基于 PhpSpreadsheet 库实现。

## 导出Excel

### 基本导出

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

// 准备数据
$column = [['姓名', '年龄', '性别']];
$data = [
    ['张三', 20, '男'],
    ['李四', 25, '女'],
    ['王五', 30, '男']
];

// 导出到文件
ExcelFacade::exportExcelData(
    $column,       // 列名
    $data,         // 数据
    '用户列表',     // 文件标题
    storage_path('app/excel')  // 保存路径
);
```

### 直接下载

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

// 准备数据
$column = [['姓名', '年龄', '性别']];
$data = [
    ['张三', 20, '男'],
    ['李四', 25, '女']
];

// 直接下载文件（不指定保存路径）
ExcelFacade::exportExcelData(
    $column,
    $data,
    '用户列表'
    // 不传第4个参数，会直接下载
);
```

## 读取Excel

### 初始化读取

```php
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

// 初始化Excel文件
ExcelFacade::initReadExcel(storage_path('app/excel/用户列表.xlsx'));
```

### 获取工作表信息

```php
// 获取工作表数量和名称
ExcelFacade::getWorkSheet();

// 切换到指定工作表（索引从0开始）
ExcelFacade::setWorkSheet(0);

// 切换到指定工作表（通过名称）
ExcelFacade::setWorkSheet(1);
```

### 获取行列数

```php
// 获取当前工作表的行数和列数
ExcelFacade::getRowColumnNumber();
```

### 获取数据

```php
// 获取指定行的数据（行索引从1开始）
$rowData = ExcelFacade::getRowData(3);

// 获取指定列的数据（列索引从1开始）
$columnData = ExcelFacade::getColumnData(2);

// 获取所有数据（按行）
$allDataByRow = ExcelFacade::getDataByRow();

// 获取所有数据（按列）
$allDataByColumn = ExcelFacade::getDataByColumn();
```

## 完整示例

### 导出示例

```php
namespace App\Http\Controllers;

use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

class UserController extends Controller
{
    public function exportUsers()
    {
        // 从数据库获取数据
        $users = \App\Models\User::all(['name', 'age', 'gender']);

        // 准备列名
        $column = [['姓名', '年龄', '性别']];

        // 准备数据
        $data = $users->map(function($user) {
            return [
                $user->name,
                $user->age,
                $user->gender
            ];
        })->toArray();

        // 导出到文件
        ExcelFacade::exportExcelData(
            $column,
            $data,
            '用户列表_' . date('YmdHis'),
            storage_path('app/excel')
        );

        return response()->json(['message' => '导出成功']);
    }

    public function downloadUsers()
    {
        // 准备数据
        $column = [['姓名', '年龄', '性别']];
        $data = [['张三', 20, '男'], ['李四', 25, '女']];

        // 直接下载
        ExcelFacade::exportExcelData(
            $column,
            $data,
            '用户列表'
        );
        // 此处会自动下载文件
    }
}
```

### 导入示例

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;

class UserController extends Controller
{
    public function importUsers(Request $request)
    {
        // 保存上传的文件
        $file = $request->file('excel');
        $filePath = $file->storeAs('excel', $file->getClientOriginalName());

        // 初始化Excel读取
        ExcelFacade::initReadExcel(storage_path('app/' . $filePath));

        // 获取工作表
        ExcelFacade::setWorkSheet(0);

        // 获取所有数据
        $data = ExcelFacade::getDataByRow();

        // 处理数据（跳过前2行：标题和列名）
        foreach ($data as $row) {
            if ($row['row'] > 2) {
                // 保存到数据库
                \App\Models\User::create([
                    'name' => $row['data'][0],
                    'age' => $row['data'][1],
                    'gender' => $row['data'][2]
                ]);
            }
        }

        return response()->json(['message' => '导入成功']);
    }
}
```

## 方法说明

| 方法 | 说明 | 参数 |
|------|------|------|
| `exportExcelData()` | 导出数据到Excel | `$column` (列名), `$data` (数据), `$title` (标题), `$savePath` (保存路径) |
| `initReadExcel()` | 初始化Excel读取 | `$fileUrl` (文件路径) |
| `getWorkSheet()` | 获取工作表信息 | `$key` (索引或名称) |
| `setWorkSheet()` | 设置当前工作表 | `$index` (工作表索引) |
| `getRowColumnNumber()` | 获取行列数 | 无 |
| `getRowData()` | 获取指定行数据 | `$rowIndex` (行索引) |
| `getColumnData()` | 获取指定列数据 | `$columnIndex` (列索引) |
| `getDataByRow()` | 获取所有数据(按行) | 无 |
| `getDataByColumn()` | 获取所有数据(按列) | 无 |
