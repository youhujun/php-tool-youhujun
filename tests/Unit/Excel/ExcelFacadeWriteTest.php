<?php

namespace Tests\Unit\Excel;

use PHPUnit\Framework\TestCase;
use YouHuJun\Tool\App\Facade\V1\Excel\ExcelFacade;
use YouHuJun\Tool\App\Exceptions\CommonException;

class ExcelFacadeWriteTest extends TestCase
{
    private $columns;
    private $data;
    private $title;
    private $saveDirectory;
    private $expectedFilename;

    protected function setUp(): void
    {
        parent::setUp();
        // 这里只保留为写操作准备的逻辑
        $this->columns = [['姓名', '年龄', '城市']];
        $this->data = [['张三', 25, '北京'], ['李四', 30, '上海']];
        $this->title = '测试导出文件';

        $tempDir = sys_get_temp_dir();
        $this->saveDirectory = $tempDir . DIRECTORY_SEPARATOR . 'excel_tests_write';
        $this->expectedFilename = rtrim($this->saveDirectory, '/\\') . DIRECTORY_SEPARATOR . $this->title . '.xlsx';

        if (file_exists($this->expectedFilename)) {
            unlink($this->expectedFilename);
        }
        if (is_dir($this->saveDirectory)) {
            rmdir($this->saveDirectory);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // 这里只保留为写操作清理的逻辑
        if (file_exists($this->expectedFilename)) {
            unlink($this->expectedFilename);
        }
        if (is_dir($this->saveDirectory)) {
            rmdir($this->saveDirectory);
        }
    }

    public function testExportExcelDataSavesFile()
    {
        ExcelFacade::exportExcelData($this->columns, $this->data, $this->title, $this->saveDirectory);
        $this->assertFileExists($this->expectedFilename, 'Excel 文件未能成功保存到指定目录。');
    }

    public function testExportExcelDataThrowsExceptionWithInvalidData()
    {
        $columns = [[]];
        $data = [['张三', 25, '北京']];
        $title = '测试异常';

        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('Excel export failed: Column headers or data is empty.');

        ExcelFacade::exportExcelData($columns, $data, $title);
    }
}