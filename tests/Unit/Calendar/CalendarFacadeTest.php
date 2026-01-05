<?php

namespace Tests\Unit\Calendar;

use PHPUnit\Framework\TestCase;
use YouHuJun\Tool\App\Facade\V1\Calendar\CalendarFacade;
use YouHuJun\Tool\App\Service\V1\Calendar\CalendarFacadeService;
use YouHuJun\Tool\App\Exceptions\CommonException;

class CalendarFacadeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 每次测试前重置单例实例
       /*  $reflection = new \ReflectionClass(CalendarFacade::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, null); */
		CalendarFacade::clearInstance();  // 简洁明了
    }

    /**
     * 测试阳历日期字符串转农历日期字符串
     *
     * @return void
     */
    public function testSolarToLunarString()
    {
        // 测试一个已知的日期："1988-07-03" -> "1988-05-20"
        $result = CalendarFacade::solarToLunarString("1988-07-03");
        $this->assertSame("1988-05-20", $result);

        // 测试另一个日期："2024-02-10" -> "2024-01-01"
        $result2 = CalendarFacade::solarToLunarString("2024-02-10");
        $this->assertSame("2024-01-01", $result2);

        // 测试更复杂的日期：2000年元旦
        $result3 = CalendarFacade::solarToLunarString("2000-01-01");
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result3);
    }

    /**
     * 测试农历日期字符串转阳历日期字符串
     *
     * @return void
     */
    public function testLunarToSolarString()
    {
        // 测试一个已知的日期："1988-05-20" -> "1988-07-03"
        $result = CalendarFacade::lunarToSolarString("1988-05-20");
        $this->assertSame("1988-07-03", $result);

        // 测试闰月：2023年闰二月，闰二月是第3个农历月
        // "2023-03-02" 表示2023年闰二月初二
        $result2 = CalendarFacade::lunarToSolarString("2023-03-02");
        $this->assertSame("2023-03-23", $result2);

        // 测试更复杂的日期：2024年正月初一
        $result3 = CalendarFacade::lunarToSolarString("2024-01-01");
        $this->assertSame("2024-02-10", $result3);
    }

    /**
     * 测试阳历和农历互相转换的往返一致性
     *
     * @return void
     */
    public function testRoundTripConversion()
    {
        // 阳历 -> 农历 -> 阳历 应该得到相同的结果
        $originalSolar = "1988-07-03";
        $lunar = CalendarFacade::solarToLunarString($originalSolar);
        $backToSolar = CalendarFacade::lunarToSolarString($lunar);
        $this->assertSame($originalSolar, $backToSolar);

        // 农历 -> 阳历 -> 农历 应该得到相同的结果
        $originalLunar = "1988-05-20";
        $solar = CalendarFacade::lunarToSolarString($originalLunar);
        $backToLunar = CalendarFacade::solarToLunarString($solar);
        $this->assertSame($originalLunar, $backToLunar);
    }

    /**
     * 测试无效的阳历日期格式
     *
     * @return void
     */
    public function testInvalidSolarDateStringFormat()
    {
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('阳历日期格式错误');

        // 使用斜杠分隔符
        CalendarFacade::solarToLunarString("1988/07/03");
    }

    /**
     * 测试无效的阳历日期（2月30日不存在）
     *
     * @return void
     */
    public function testInvalidSolarDate()
    {
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('阳历日期无效');

        CalendarFacade::solarToLunarString("2024-02-30");
    }

    /**
     * 测试无效的农历日期格式
     *
     * @return void
     */
    public function testInvalidLunarDateStringFormat()
    {
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('农历日期格式错误');

        CalendarFacade::lunarToSolarString("1988/05/20");
    }

    /**
     * 测试超出范围的农历年份
     *
     * @return void
     */
    public function testLunarYearOutOfRange()
    {
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('农历年份超出范围');

        // 年份小于1891
        CalendarFacade::lunarToSolarString("1800-01-01");
    }

    /**
     * 测试超出范围的农历月份
     *
     * @return void
     */
    public function testLunarMonthOutOfRange()
    {
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('农历月份超出范围');

        CalendarFacade::lunarToSolarString("1988-14-01");
    }

    /**
     * 测试超出范围的农历日期
     *
     * @return void
     */
    public function testLunarDayOutOfRange()
    {
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('农历日期超出范围');

        CalendarFacade::lunarToSolarString("1988-05-35");
    }

    /**
     * 测试边界日期
     *
     * @return void
     */
    public function testBoundaryDates()
    {
        // 测试1891年2月10日（接近最小边界）
        $result = CalendarFacade::solarToLunarString("1891-02-10");
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);

        // 测试2100年2月8日（接近最大边界）
        $result = CalendarFacade::solarToLunarString("2100-02-08");
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);
    }
}
