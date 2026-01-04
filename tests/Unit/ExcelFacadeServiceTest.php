<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use YouHuJun\Tool\App\Service\V1\Excel\ExcelFacadeService;
use YouHuJun\Tool\App\Exceptions\CommonException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriteXlsx;

class ExcelFacadeServiceTest extends TestCase
{
    public function testExportExcelDataSavesFile()
	{
		// 1. 准备测试数据
		$columns = [['姓名', '年龄', '城市']];
		$data = [
			['张三', 25, '北京'],
			['李四', 30, '上海'],
		];
		$title = '测试导出文件';

		// 2. 构建一个用于保存的目录路径
		$tempDir = sys_get_temp_dir();
		// 注意：这里我们只提供目录路径
		$saveDirectory = $tempDir . DIRECTORY_SEPARATOR . 'excel_tests';

		// 预期的完整文件路径
		$expectedFilename = rtrim($saveDirectory, '/\\') . DIRECTORY_SEPARATOR . $title . '.xlsx';

		// 确保测试前文件不存在
		if (file_exists($expectedFilename)) {
			unlink($expectedFilename);
		}

		// 3. 执行要测试的方法，并传入目录路径
		$service = new ExcelFacadeService();
		$service->exportExcelData($columns, $data, $title, $saveDirectory);

		// 4. 断言文件已经被创建在预期的路径
		$this->assertFileExists($expectedFilename, 'Excel 文件未能成功保存到指定目录。');

		// 5. 清理：删除测试生成的文件和目录
		if (file_exists($expectedFilename)) {
			unlink($expectedFilename);
			rmdir($saveDirectory); // 删除我们创建的测试目录
		}
	}

    /**
     * 测试导出 Excel 时传入无效数据是否会抛出异常
     */
    public function testExportExcelDataThrowsExceptionWithInvalidData()
    {
        // 1. 准备无效的测试数据
        $columns = [[]]; // 空的列头
        $data = [
            ['张三', 25, '北京'],
        ];
        $title = '测试异常';

        // 2. 预期会抛出 CommonException 异常
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('Excel export failed: Column headers or data is empty.'); // 断言异常消息

        // 3. 执行要测试的方法
        $service = new ExcelFacadeService();
        $service->exportExcelData($columns, $data, $title, 0);
    }

    /**
     * 测试读取 Excel 文件
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function testReadExcelData()
    {
        // 1. 准备一个用于测试的 Excel 文件
        // 为了测试的独立性，最好在测试中动态创建一个临时文件
        $tempFile = tempnam(sys_get_temp_dir(), 'test_excel_') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '姓名');
        $sheet->setCellValue('B1', '年龄');
        $sheet->setCellValue('A2', '王五');
        $sheet->setCellValue('B2', 28);

        $writer = new WriteXlsx($spreadsheet);
        $writer->save($tempFile);

        // 2. 执行读取操作
        $service = new ExcelFacadeService();
        $service->initReadExcel($tempFile);
        $service->setWorkSheet(0); // 选择第一个工作表
        $result = $service->getDataByRow();

        // 3. 断言读取的数据是否正确
        $expectedData = [
            0 => [0 => '姓名', 1 => '年龄'],
            1 => [0 => '王五', 1 => '28'],
        ];
        $this->assertEquals($expectedData, $result, '读取 Excel 数据与预期不符。');

        // 清理：删除临时文件
        unlink($tempFile);
    }
}