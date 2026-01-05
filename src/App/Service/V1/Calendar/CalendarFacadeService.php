<?php
namespace YouHuJun\Tool\App\Service\V1\Calendar;

use DateTimeZone;
use YouHuJun\Tool\App\Exceptions\CommonException;
use YouHuJun\Tool\App\Service\V1\Calendar\CalendarConverter;

class CalendarFacadeService
{
    /**
     * @var CalendarConverter
     */
    private $converter;

    /**
     * CalendarFacadeService constructor.
     *
     * @param DateTimeZone|null $timezone The timezone to be used by the converter.
     */
    public function __construct()
    {
        // 创建 CalendarConverter 实例（不传时区参数）
        $this->converter = new CalendarConverter();
    }

    /**
     * 将农历日期转换为阳历日期
     *
     * @param int $year 农历年份
     * @param int $month 农历月份
     * @param int $day 农历日期
     * @return array [年, 月, 日]
     * @throws CommonException
     */
    public function lunarToSolar($year, $month, $day): array
    {
        // 直接调用转换器的核心方法并返回结果
        return $this->converter->convertLunarToSolar($year, $month, $day);
    }

    /**
     * 将阳历日期转换为农历日期
     *
     * @param int $year 阳历年份
     * @param int $month 阳历月份
     * @param int $day 阳历日期
     * @return array 农历数据数组
     * @throws CommonException
     */
    public function solarToLunar($year, $month, $day): array
    {
        return $this->converter->solarToLunar($year, $month, $day);
    }

    /**
     * 将阳历日期字符串转换为农历日期字符串。
     *
     * @param string $solarDateString 阳历日期字符串，格式：YYYY-MM-DD，例如 "1988-07-03"
     * @return string 农历日期字符串，格式：YYYY-MM-DD，例如 "1988-05-20"
     * @throws CommonException
     */
    public function solarToLunarString(string $solarDateString): string
    {
        // 验证日期格式
        if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $solarDateString)) {
            throw new CommonException('阳历日期格式错误，正确格式为：YYYY-MM-DD');
        }

        // 解析阳历日期
        $dateParts = explode('-', $solarDateString);
        $year = (int)$dateParts[0];
        $month = (int)$dateParts[1];
        $day = (int)$dateParts[2];

        // 验证日期有效性
        if (!checkdate($month, $day, $year)) {
            throw new CommonException('阳历日期无效：' . $solarDateString);
        }

        // 转换为农历
        $lunarArray = $this->converter->solarToLunar($year, $month, $day);

        // 返回格式：[农历年, 农历月, 农历日]
        $lunarYearMonthDay = $lunarArray[6];
        return sprintf('%04d-%02d-%02d', $lunarYearMonthDay[0], $lunarYearMonthDay[1], $lunarYearMonthDay[2]);
    }

    /**
     * 将农历日期字符串转换为阳历日期字符串。
     *
     * @param string $lunarDateString 农历日期字符串，格式：YYYY-MM-DD，例如 "1988-05-20"
     * @return string 阳历日期字符串，格式：YYYY-MM-DD，例如 "1988-07-03"
     * @throws CommonException
     */
    public function lunarToSolarString(string $lunarDateString): string
    {
        // 验证日期格式
        if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $lunarDateString)) {
            throw new CommonException('农历日期格式错误，正确格式为：YYYY-MM-DD');
        }

        // 解析农历日期
        $dateParts = explode('-', $lunarDateString);
        $year = (int)$dateParts[0];
        $month = (int)$dateParts[1];
        $day = (int)$dateParts[2];

        // 基本验证：年、月、日范围
        if ($year < 1891 || $year > 2100) {
            throw new CommonException('农历年份超出范围(1891-2100)：' . $year);
        }
        if ($month < 1 || $month > 13) {
            throw new CommonException('农历月份超出范围(1-13)：' . $month);
        }
        if ($day < 1 || $day > 30) {
            throw new CommonException('农历日期超出范围(1-30)：' . $day);
        }

        // 转换为阳历
        $solarArray = $this->converter->convertLunarToSolar($year, $month, $day);

        return sprintf('%04d-%02d-%02d', $solarArray[0], $solarArray[1], $solarArray[2]);
    }
}
