<?php

namespace YouHuJun\Tool\App\Service\V1\Calendar;

use YouHuJun\Tool\App\Exceptions\CommonException;

class CalendarConverter
{
    private int $minYear = 1891;
    private int $maxYear = 2100;

    private $year = 0;
    private $month = 0;
    private $date = 0;

    private $cnNumbers = ['零','一','二','三','四','五','六','七','八','九','十','十一','十二'];

    private $lunarInfo = [
            [0,2,9,21936], [6,1,30,9656], [0,2,17,9584], [0,2,6,21168], [5,1,26,43344], [0,2,13,59728],
            [0,2,2,27296], [3,1,22,44368], [0,2,10,43856], [8,1,30,19304], [0,2,19,19168], [0,2,8,42352],
            [5,1,29,21096], [0,2,16,53856], [0,2,4,55632], [4,1,25,27304], [0,2,13,22176], [0,2,2,39632],
            [2,1,22,19176], [0,2,10,19168], [6,1,30,42200], [0,2,18,42192], [0,2,6,53840], [5,1,26,54568],
            [0,2,14,46400], [0,2,3,54944], [2,1,23,38608], [0,2,11,38320], [7,2,1,18872], [0,2,20,18800],
            [0,2,8,42160], [5,1,28,45656], [0,2,16,27216], [0,2,5,27968], [4,1,24,44456], [0,2,13,11104],
            [0,2,2,38256], [2,1,23,18808], [0,2,10,18800], [6,1,30,25776], [0,2,17,54432], [0,2,6,59984],
            [5,1,26,27976], [0,2,14,23248], [0,2,4,11104], [3,1,24,37744], [0,2,11,37600], [7,1,31,51560],
            [0,2,19,51536], [0,2,8,54432], [6,1,27,55888], [0,2,15,46416], [0,2,5,22176], [4,1,25,43736],
            [0,2,13,9680], [0,2,2,37584], [2,1,22,51544], [0,2,10,43344], [7,1,29,46248], [0,2,17,27808],
            [0,2,6,46416], [5,1,27,21928], [0,2,14,19872], [0,2,3,42416], [3,1,24,21176], [0,2,12,21168],
            [8,1,31,43344], [0,2,18,59728], [0,2,8,27296], [6,1,28,44368], [0,2,15,43856], [0,2,5,19296],
            [4,1,25,42352], [0,2,13,42352], [0,2,2,21088], [3,1,21,59696], [0,2,9,55632], [7,1,30,23208],
            [0,2,17,22176], [0,2,6,38608], [5,1,27,19176], [0,2,15,19152], [0,2,3,42192], [4,1,23,53864],
            [0,2,11,53840], [8,1,31,54568], [0,2,18,46400], [0,2,7,46752], [6,1,28,38608], [0,2,16,38320],
            [0,2,5,18864], [4,1,25,42168], [0,2,13,42160], [10,2,2,45656], [0,2,20,27216], [0,2,9,27968],
            [6,1,29,44448], [0,2,17,43872], [0,2,6,38256], [5,1,27,18808], [0,2,15,18800], [0,2,4,25776],
            [3,1,23,27216], [0,2,10,59984], [8,1,31,27432], [0,2,19,23232], [0,2,7,43872], [5,1,28,37736],
            [0,2,16,37600], [0,2,5,51552], [4,1,24,54440], [0,2,12,54432], [0,2,1,55888], [2,1,22,23208],
            [0,2,9,22176], [7,1,29,43736], [0,2,18,9680], [0,2,7,37584], [5,1,26,51544], [0,2,14,43344],
            [0,2,3,46240], [4,1,23,46416], [0,2,10,44368], [9,1,31,21928], [0,2,19,19360], [0,2,8,42416],
            [6,1,28,21176], [0,2,16,21168], [0,2,5,43312], [4,1,25,29864], [0,2,12,27296], [0,2,1,44368],
            [2,1,22,19880], [0,2,10,19296], [6,1,29,42352], [0,2,17,42208], [0,2,6,53856], [5,1,26,59696],
            [0,2,13,54576], [0,2,3,23200], [3,1,23,27472], [0,2,11,38608], [11,1,31,19176], [0,2,19,19152],
            [0,2,8,42192], [6,1,28,53848], [0,2,15,53840], [0,2,4,54560], [5,1,24,55968], [0,2,12,46496],
            [0,2,1,22224], [2,1,22,19160], [0,2,10,18864], [7,1,30,42168], [0,2,17,42160], [0,2,6,43600],
            [5,1,26,46376], [0,2,14,27936], [0,2,2,44448], [3,1,23,21936], [0,2,11,37744], [8,2,1,18808],
            [0,2,19,18800], [0,2,8,25776], [6,1,28,27216], [0,2,15,59984], [0,2,4,27424], [4,1,24,43872],
            [0,2,12,43744], [0,2,2,37600], [3,1,21,51568], [0,2,9,51552], [7,1,29,54440], [0,2,17,54432],
            [0,2,5,55888], [5,1,26,23208], [0,2,14,22176], [0,2,3,42704], [4,1,23,21224], [0,2,11,21200],
            [8,1,31,43352], [0,2,19,43344], [0,2,7,46240], [6,1,27,46416], [0,2,15,44368], [0,2,5,21920],
            [4,1,24,42448], [0,2,12,42416], [0,2,2,21168], [3,1,22,43320], [0,2,9,26928], [7,1,29,29336],
            [0,2,17,27296], [0,2,6,44368], [5,1,26,19880], [0,2,14,19296], [0,2,3,42352], [4,1,24,21104],
            [0,2,10,53856], [8,1,30,59696], [0,2,18,54560], [0,2,7,55968], [6,1,27,27472], [0,2,15,22224],
            [0,2,5,19168], [4,1,25,42216], [0,2,12,42192], [0,2,1,53584], [2,1,21,55592], [0,2,9,54560]
        ];

    private $datetime = null;

    public function __construct($year = 0, $month = 0, $date = 0) {
        $this->datetime = $this->getDateTime($year, $month, $date);
    }

    private function getDateTime($year, $month = 0, $date = 0)
    {
        $timezone = new \DateTimeZone('Asia/Shanghai');
        $datetime = new \DateTime();
        $datetime->setTimezone($timezone);

        if(is_string($year) && $month == 0 && $date == 0) {
            $datetime->setTimestamp(strtotime($year));
        }
        if($year > 9999 && $month == 0 && $date == 0) {
            $datetime->setTimestamp($year);
        }
        if($year && $month && $date) {
            $datetime->setDate($year, $month, $date);
        }
        $this->isValidDate($datetime);
        return $datetime;
    }

    private function isValidDate($datetime): bool {
        $minTime = mktime(0, 0, 0, 2, 9, $this->minYear);
        $maxTime = mktime(0, 0, 0, 2, 9, $this->maxYear);
        if($datetime->getTimestamp() < $minTime || $datetime->getTimestamp() > $maxTime) {
            $date_string = $datetime->format('Y.n.j');
            throw new CommonException("日期超出有效范围(1891.2.9 - 2100.2.9): $date_string");
        }
        return true;
    }

    public function setDate($year = 0, $month = 0, $date = 0) {
        $this->isValidDate($this->getDateTime($year, $month, $date));
        $this->year = $year;
        $this->month = $month;
        $this->date = $date;
    }

    public function solarToLunar($year, $month = 0, $date = 0)
    {
        $datetime = $this->getDateTime($year, $month, $date);
        return $this->getLunarByBetween($datetime->format('Y'), $this->getDaysBetweenSolar($datetime->format('Y'), $datetime->format('n'), $datetime->format('j')) );
    }

    public function convertSolarMonthToLunar($year, $month): array {
        $this->isValidDate($this->getDateTime($year, $month, 1));
        $month_days_ary = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $dd = $month_days_ary[$month];
        if($this->isLeapYear($year) && $month == 2) $dd++;
        $lunar_ary = [];
        for ($i = 1; $i <= $dd; $i++) {
            $array = $this->getLunarByBetween($year, $this->getDaysBetweenSolar($year, $month, $i));
            $array[] = $year . '-' . $month . '-' . $i;
            $lunar_ary[$i] = $array;
        }
        return $lunar_ary;
    }

    public function isLeapYear($year): bool {
        return ($year%4 == 0 && $year%100 != 0) || ($year%400 == 0);
    }

    public function getLunarYearName($year) {
        $sky = ['庚', '辛', '壬', '癸', '甲', '乙', '丙', '丁', '戊', '己'];
        $earth = ['申', '酉', '戌', '亥', '子', '丑', '寅', '卯', '辰', '巳', '午', '未'];
        $year = (string)$year;
        return $sky[$year[3]] . $earth[$year%12];
    }

    public function getYearZodiac($year) {
        $zodiac = ['猴', '鸡', '狗', '猪', '鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊'];
        return $zodiac[$year%12];
    }

    public function convertLunarToSolar($year, $month, $date): array {
        $yearData = $this->lunarInfo[$year-$this->minYear];
        $between = $this->getDaysBetweenLunar($year, $month, $date);
        $res = mktime(0, 0, 0, $yearData[1], $yearData[2], $year);
        $res = date('Y-m-d', $res +$between*24*60*60);
        $day = explode('-', $res);
        return [$day[0], $day[1], $day[2]];
    }

    public function getSolarMonthDays($year, $month) {
        $monthHash = [
                '0' => 0,
                '1' => 31,
                '2' => $this->isLeapYear($year) ? 29 : 28,
                '3' => 31,
                '4' => 30,
                '5' => 31,
                '6' => 30,
                '7' => 31,
                '8' => 31,
                '9' => 30,
                '10' => 31,
                '11' => 30,
                '12' => 31
            ];
        return $monthHash[$month];
    }

    public function getLunarMonthDays($year, $month) {
        $monthData = $this->getLunarMonths($year);
        return $monthData[$month-1];
    }

    public function getLunarMonths($year) {
        $yearData = $this->lunarInfo[$year - $this->minYear];
        $leapMonth = $yearData[0];
        $bit = decbin($yearData[3]);
        $bitArray = array();
        for ($i = 0; $i < strlen($bit); $i++) {
            $bitArray[$i] = substr($bit, $i, 1);
        }
        for($k = 0, $klen = 16-count($bitArray); $k < $klen; $k++) {
            array_unshift($bitArray, '0');
        }
        $bitArray = array_slice($bitArray, 0, ($leapMonth == 0 ? 12:13));
        for($i = 0; $i < count($bitArray); $i++) {
            $bitArray[$i] = $bitArray[$i] + 29;
        }
        return $bitArray;
    }

    public function getLunarYearDays($year): int {
        $monthArray = $this->getLunarYearMonths($year);
        $len = count($monthArray);
        return $monthArray[$len-1] == 0 ? $monthArray[$len-2] : $monthArray[$len-1];
    }

    public function getLunarYearMonths($year): array {
        $monthData = $this->getLunarMonths($year);
        $res = [];
        $yearData = $this->lunarInfo[$year-$this->minYear];
        $len = ($yearData[0] == 0 ? 12:13);
        for($i = 0; $i < $len; $i++) {
            $temp = 0;
            for($j = 0; $j <= $i; $j++) {
                $temp += $monthData[$j];
            }
            array_push($res, $temp);
        }
        return $res;
    }

    public function getLeapMonth($year) {
        $yearData = $this->lunarInfo[$year-$this->minYear];
        return $yearData[0];
    }

    public function getDaysBetweenLunar($year, $month, $date) {
        $yearMonth = $this->getLunarMonths($year);
        $res = 0;
        for($i = 1; $i < $month; $i++) {
                $res += $yearMonth[$i-1];
        }
        $res += $date-1;
        return $res;
    }

    public function getDaysBetweenSolar($year, $month, $date) {
        $yearInfo = $this->lunarInfo[$year-$this->minYear];
        $a = mktime(0, 0, 0, $month, $date, $year);
        $b = mktime(0, 0, 0, $yearInfo[1], $yearInfo[2], $year);
        return (int)ceil( ($a-$b) / 24 / 3600 );
    }

    public function getLunarByBetween($year, $between): array {
        $lunarArray = [];
        $t = 0;
        $e = 0;
        $leapMonth = 0;
        if($between == 0) {
            array_push($lunarArray, $this->toYear($year), '正月', '初一');
            $t = 1;
            $e = 1;
        } else{
            $year = $between > 0 ? $year : ($year-1);
            $yearMonth = $this->getLunarYearMonths($year);
            $leapMonth = $this->getLeapMonth($year);
            $between = $between > 0 ? $between : ($this->getLunarYearDays($year) +$between);
            for($i = 0; $i < 13; $i++) {
                if(isset($yearMonth[$i]) && $between == $yearMonth[$i]) {
                    $t = $i +2;
                    $e = 1;
                    break;
                } else if(isset($yearMonth[$i]) && $between < $yearMonth[$i]) {
                    $t = $i +1;
                    $e = $between-(empty($yearMonth[$i-1]) ? 0 : $yearMonth[$i-1]) + 1;
                    break;
                }
            }
            $m = ($leapMonth!=0 && $t == $leapMonth+1) ? ('闰'.$this->getCapitalNum($t-1, true)) : $this->getCapitalNum(($leapMonth!=0 && $leapMonth+1 < $t ? ($t-1) : $t), true);
            array_push($lunarArray, $this->toYear($year), $m, $this->getCapitalNum($e, false));
        }
        array_push($lunarArray, $this->getLunarYearName($year));
        array_push($lunarArray, $this->getYearZodiac($year));
        array_push($lunarArray, $leapMonth ? '闰'.$this->cnNumbers[$leapMonth].'月': 0);
        array_push($lunarArray, [$year, $t, $e]);
        return $lunarArray;
    }

    public function toYear($year) {
        $year_arr = str_split($year);
        return $this->cnNumbers[$year_arr[0]].$this->cnNumbers[$year_arr[1]].$this->cnNumbers[$year_arr[2]].$this->cnNumbers[$year_arr[3]];
    }

    public function getCapitalNum($num, $isMonth = false) {
        $monthHash = ['', '正', '二', '三', '四', '五', '六', '七', '八', '九', '十', '冬', '腊'];
        $dateHash = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];
        if($isMonth) {
            return $monthHash[$num].'月';
        }
        $str = '';
        if($num <= 10) {
            $str = '初'.$dateHash[$num];
        } else if($num > 10&&$num < 20) {
            $str = '十'.$dateHash[$num-10];
        } else if($num == 20) {
            $str = "二十";
        } else if($num > 20&&$num < 30) {
            $str = "廿".$dateHash[$num-20];
        } else if($num == 30) {
            $str = "三十";
        }
        return $str;
    }

}
