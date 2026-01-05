<?php

namespace YouHuJun\Tool\App\Console\Commands;

use YouHuJun\Tool\App\Exceptions\CommonException;

class GeneratorCommand
{
    /**
     * @var string Facade 模板路径
     */
    private string $facadeStubPath;

    /**
     * @var string Service 模板路径
     */
    private string $serviceStubPath;

    /**
     * @var string 项目根目录
     */
    private string $basePath;

    /**
     * @var string 基础命名空间
     */
    private string $baseNamespace;

    public function __construct()
    {
        $this->basePath = dirname(__DIR__, 4);
        $this->facadeStubPath = $this->basePath . '/src/App/Console/Commands/stubs/facade.stub';
        $this->serviceStubPath = $this->basePath . '/src/App/Console/Commands/stubs/service.stub';
        $this->baseNamespace = 'YouHuJun\\Tool\\App';
    }

    /**
     * 解析路径并执行对应操作
     *
     * @param string $path 路径,如: Facade/V1/Wechat/Official/WechatOfficialWebAuth
     * @param string $description 描述信息(仅用于生成 Service)
     * @return array 生成的文件路径
     * @throws CommonException
     */
    public function generate(string $path, string $description = ''): array
    {
        $path = trim($path, '/\\');
        $parts = explode('/', $path);

        if (count($parts) < 2) {
            throw new CommonException('路径格式错误,正确格式: Facade/V1/Module/ClassName 或 Service/V1/Module/ClassName 或 V1/Module/ClassName');
        }

        $type = $parts[0]; // Facade 或 Service 或 V1
        $className = array_pop($parts); // 类名

        // 处理类型和版本
        $version = 'V1';
        $modulePath = '';

        if ($type === 'Facade') {
            // Facade/V1/Module/ClassName
            if (count($parts) < 3) {
                throw new CommonException('路径格式错误,Facade 路径应为: Facade/V1/Module/ClassName');
            }
            $version = $parts[1]; // V1
            $modulePath = implode('/', array_slice($parts, 2)); // 模块路径: Wechat/Official
            $generatedFiles[] = $this->generateFacadeFile($className, $modulePath, $version);
        } elseif ($type === 'Service') {
            // Service/V1/Module/ClassName
            if (count($parts) < 3) {
                throw new CommonException('路径格式错误,Service 路径应为: Service/V1/Module/ClassName');
            }
            $version = $parts[1]; // V1
            $modulePath = implode('/', array_slice($parts, 2)); // 模块路径: Wechat/Official
            $generatedFiles[] = $this->generateServiceFile($className, $modulePath, $version, $description);
        } elseif ($type === 'V1') {
            // V1/Module/ClassName - 同时生成 Facade 和 Service (call:facade 的实际调用)
            if (count($parts) < 2) {
                throw new CommonException('路径格式错误,V1 路径应为: V1/Module/ClassName');
            }
            $modulePath = implode('/', array_slice($parts, 1)); // 模块路径: Calendar
            // 直接使用原始类名,不再清理和添加后缀,由子方法处理
            $generatedFiles[] = $this->generateFacadeFile($className, $modulePath, $version);
            $generatedFiles[] = $this->generateServiceFile($className, $modulePath, $version, $description);
        } else {
            throw new CommonException('不支持的类型,只支持: Facade, Service, V1');
        }

        return $generatedFiles;
    }

    /**
     * 生成 Facade 文件
     */
    private function generateFacadeFile(string $className, string $modulePath, string $version = 'V1'): string
    {
        // 解析模块路径
        $modulePath = trim($modulePath, '/\\');
        $moduleNamespace = $modulePath ? '\\' . str_replace('/', '\\', $modulePath) : '';

        // 清理类名
        $className = $this->sanitizeClassName($className);
        $facadeClassName = $className;

        // 移除已有的 Facade 后缀以避免重复
        $facadeClassName = preg_replace('/Facade$/', '', $facadeClassName);
        $facadeClassName .= 'Facade';

        // 对应的 Service 类名
        $serviceClassName = $facadeClassName . 'Service';

        // 生成目录路径 - 基础路径是 Facade, 不包含 V1
        $facadeDir = $this->basePath . '/src/App/Facade/' . $version . ($modulePath ? '/' . $modulePath : '');

        // 创建目录
        $this->ensureDirectoryExists($facadeDir);

        // 文件路径
        $facadeFile = $facadeDir . '/' . $facadeClassName . '.php';

        // 检查文件是否已存在
        if (file_exists($facadeFile)) {
            throw new CommonException('Facade 文件已存在: ' . $facadeFile);
        }

        // 生成内容
        $stub = file_get_contents($this->facadeStubPath);
        $content = str_replace(
            [
                '{{Namespace}}',
                '{{ModulePath}}',
                '{{FacadeClass}}',
                '{{ServiceClass}}',
            ],
            [
                $this->baseNamespace,
                $moduleNamespace,
                $facadeClassName,
                $serviceClassName,
            ],
            $stub
        );
        file_put_contents($facadeFile, $content);

        return $facadeFile;
    }

    /**
     * 生成 Service 文件
     */
    private function generateServiceFile(string $className, string $modulePath, string $version = 'V1', string $description = ''): string
    {
        // 解析模块路径
        $modulePath = trim($modulePath, '/\\');
        $moduleNamespace = $modulePath ? '\\' . str_replace('/', '\\', $modulePath) : '';

        // 清理类名
        $className = $this->sanitizeClassName($className);

        // 确保以 FacadeService 结尾
        $className = preg_replace('/FacadeService$/', '', $className);
        $className .= 'FacadeService';

        // 生成目录路径 - 基础路径是 Service, 不包含 V1
        $serviceDir = $this->basePath . '/src/App/Service/' . $version . ($modulePath ? '/' . $modulePath : '');

        // 创建目录
        $this->ensureDirectoryExists($serviceDir);

        // 文件路径
        $serviceFile = $serviceDir . '/' . $className . '.php';

        // 检查文件是否已存在
        if (file_exists($serviceFile)) {
            throw new CommonException('Service 文件已存在: ' . $serviceFile);
        }

        // 生成内容
        $stub = file_get_contents($this->serviceStubPath);
        $content = str_replace(
            [
                '{{Namespace}}',
                '{{ModulePath}}',
                '{{ServiceClass}}',
                '{{Description}}',
                '{{Date}}',
                '{{Year}}',
            ],
            [
                $this->baseNamespace,
                $moduleNamespace,
                $className,
                $description ?: '自动生成的服务类',
                date('Y-m-d H:i:s'),
                date('Y'),
            ],
            $stub
        );
        file_put_contents($serviceFile, $content);

        return $serviceFile;
    }

    /**
     * 确保目录存在
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * 清理类名
     */
    private function sanitizeClassName(string $name): string
    {
        // 移除已有的后缀
        $name = str_replace(['Facade', 'FacadeService'], '', $name);
        // 只保留字母、数字和下划线
        return preg_replace('/[^a-zA-Z0-9_]/', '', $name);
    }
}
