# CalendarFacade 使用说明

## 概述

`CalendarFacade` 提供了农历和阳历日期的相互转换功能。

## 农历转阳历

### 数组格式转换

```php
use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

// 将农历 2023年11月25日 转换为阳历
$solar = CalendarFacade::lunarToSolar(2023, 11, 25);

// 返回: [2024, 1, 6]  (2024年1月6日)
echo $solar[0] . '-' . $solar[1] . '-' . $solar[2];
```

### 字符串格式转换

```php
use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

// 将农历日期字符串转换为阳历日期字符串
$solarStr = CalendarFacade::lunarToSolarString('2023-11-25');

// 返回: '2024-01-06'
echo $solarStr;
```

## 阳历转农历

### 数组格式转换

```php
use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

// 将阳历 2024年1月6日 转换为农历
$lunar = CalendarFacade::solarToLunar(2024, 1, 6);

// 返回: 包含农历信息的数组
print_r($lunar);
```

### 字符串格式转换

```php
use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

// 将阳历日期字符串转换为农历日期字符串
$lunarStr = CalendarFacade::solarToLunarString('2024-01-06');

// 返回: '2023-11-25'
echo $lunarStr;
```

## 完整示例

### 显示农历生日

```php
namespace App\Http\Controllers;

use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

class UserController extends Controller
{
    public function showBirthday($id)
    {
        $user = \App\Models\User::find($id);

        // 假设用户生日是农历 1990年5月20日
        $userLunarBirthday = '1990-05-20';

        // 转换为阳历
        $solarBirthday = CalendarFacade::lunarToSolarString($userLunarBirthday);

        return view('user.birthday', compact('user', 'solarBirthday'));
    }
}
```

### 显示今天是农历

```php
namespace App\Http\Controllers;

use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

class HomeController extends Controller
{
    public function index()
    {
        // 获取今天的阳历日期
        $today = date('Y-m-d');

        // 转换为农历
        $lunarDate = CalendarFacade::solarToLunarString($today);

        // 返回农历日期
        return response()->json([
            'solar' => $today,
            'lunar' => $lunarDate
        ]);
    }
}
```

### 农历节日提醒

```php
namespace App\Services;

use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

class HolidayService
{
    // 农历节日列表
    protected $holidays = [
        '01-01' => '春节',
        '01-15' => '元宵节',
        '05-05' => '端午节',
        '08-15' => '中秋节',
        '12-08' => '腊八节',
        '12-30' => '除夕'
    ];

    public function checkTodayHoliday()
    {
        // 获取今天的农历日期
        $today = date('Y-m-d');
        $lunarDate = CalendarFacade::solarToLunarString($today);

        // 提取月日
        $monthDay = substr($lunarDate, 5);

        // 检查是否是节日
        if (isset($this->holidays[$monthDay])) {
            return $this->holidays[$monthDay];
        }

        return null;
    }
}
```

### 用户的农历生日

```php
namespace App\Services;

use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;

class BirthdayService
{
    public function getNextBirthday($user)
    {
        // 假设用户的农历生日是 '1990-05-20'
        $userLunarBirthday = $user->lunar_birthday; // '1990-05-20'

        // 获取今天的阳历日期
        $today = date('Y-m-d');

        // 提取农历月日
        $lunarMonthDay = substr($userLunarBirthday, 5); // '05-20'

        // 尝试今年的农历生日
        $currentYearLunar = date('Y') . '-' . $lunarMonthDay;
        $currentYearSolar = CalendarFacade::lunarToSolarString($currentYearLunar);

        // 如果今年的已经过了，计算明年的
        if ($currentYearSolar < $today) {
            $nextYearLunar = (date('Y') + 1) . '-' . $lunarMonthDay;
            $nextYearSolar = CalendarFacade::lunarToSolarString($nextYearLunar);
            return $nextYearSolar;
        }

        return $currentYearSolar;
    }
}
```

## 方法说明

| 方法 | 说明 | 参数 | 返回值 |
|------|------|------|--------|
| `lunarToSolar()` | 农历转阳历(数组) | `$year`, `$month`, `$day` | `[年, 月, 日]` |
| `solarToLunar()` | 阳历转农历(数组) | `$year`, `$month`, `$day` | 农历信息数组 |
| `lunarToSolarString()` | 农历转阳历(字符串) | `'YYYY-MM-DD'` | `'YYYY-MM-DD'` |
| `solarToLunarString()` | 阳历转农历(字符串) | `'YYYY-MM-DD'` | `'YYYY-MM-DD'` |

## 注意事项

1. **日期格式**: 字符串格式必须为 `YYYY-MM-DD`，例如 `2024-01-06`
2. **年份范围**: 支持的年份范围是 1891-2100
3. **闰月**: 支持闰月的处理
4. **返回格式**:
   - 数组格式: `[年, 月, 日]`
   - 字符串格式: `'YYYY-MM-DD'`
