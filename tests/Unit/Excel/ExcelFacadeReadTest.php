<?php

namespace Tests\Unit\Excel;

use PHPUnit\Framework\TestCase;
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriteXlsx;

class ExcelFacadeReadTest extends TestCase
{
    private $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        // 这里只保留为读操作准备的逻辑
        $this->tempFile = tempnam(sys_get_temp_dir(), 'test_excel_read_') . '.xlsx';
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '姓名');
        $sheet->setCellValue('B1', '年龄');
        $sheet->setCellValue('A2', '王五');
        $sheet->setCellValue('B2', 28);

        $writer = new WriteXlsx($spreadsheet);
        $writer->save($this->tempFile);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // 这里只保留为读操作清理的逻辑
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testReadExcelData()
    {
        // 注意：这里使用的是门面 ExcelFacade
        ExcelFacade::initReadExcel($this->tempFile);
        ExcelFacade::setWorkSheet(0);
        $result = ExcelFacade::getDataByRow();

        // 移除表头后再进行断言，这更符合真实业务场景
        array_shift($result);
        
        $expectedData = [
            0 => [0 => '王五', 1 => '28'],
        ];
        
        $this->assertEquals($expectedData, $result, '通过门面读取 Excel 数据与预期不符。');
    }
}