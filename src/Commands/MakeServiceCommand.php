<?php

namespace Tungnt\LaravelRepository\Commands;

use Tungnt\LaravelRepository\Support\GenerateFile;
use Tungnt\LaravelRepository\Support\FileGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeServiceCommand extends CommandGenerator
{
    /**
     * argumentName
     *
     * @var string
     */
    public $argumentName = 'service';


    /**
     * Name and signature of Command.
     * name
     * @var string
     */
    protected $name = 'make:service';


    /**
     * command description.
     * description
     * @var string
     */
    protected $description = 'Create a new service class';



    /**
     * Get command arguments - EX : UserService
     * getArguments
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['service', InputArgument::REQUIRED, 'The name of the service class.'],
        ];
    }

    /**
     * Get command options - EX : -i
     * getOptions
     *
     * @return array[]
     */
    protected function getOptions(): array
    {
        return [
            ['interface', 'i', InputOption::VALUE_NONE, 'Flag to create associated Interface', null],
        ];
    }


    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Return Service name as convention
     * getServiceName
     *
     * @return string
     */
    private function getServiceName(): string
    {
        $service = Str::studly($this->argument('service'));

        if (Str::contains(strtolower($service), 'service') === false) {
            $service .= 'Service';
        }
        $changeService = explode('/', $service);
        foreach ($changeService as $key => $change) {
            if ($change == end($changeService)) {
                $changeService[$key] = ucfirst($change);
            }
            continue;
        }

        $service = implode('/', $changeService);

        return $service;
    }

    /**
     * Replace App with empty string for resolve namespace
     *
     * @return string
     */
    private function resolveNamespace(): string
    {
        if (strpos($this->getServiceNamespaceFromConfig(), self::APP_PATH) === 0) {
            return str_replace(self::APP_PATH, '', $this->getServiceNamespaceFromConfig());
        }
        return '/' . $this->getServiceNamespaceFromConfig();
    }

    /**
     * Return destination path for class file publish
     * getDestinationFilePath
     *
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        return app_path() . $this->resolveNamespace() . '/Services' . '/' . $this->getServiceName() . '.php';
    }

    /**
     * Return Inference name for this repository class
     * getInterfaceName
     *
     * @return string
     */
    protected function getInterfaceName(): string
    {
        return $this->getServiceName() . "Interface";
    }

    /**
     * Return Repo name for this repository class
     * getInterfaceName
     *
     * @return string
     */
    protected function getRepoName(): string
    {
        return preg_replace('/service/i', 'Repository', class_basename($this->getServiceName()));
    }

    /**
     * Return destination path for interface file publish
     * interfaceDestinationPath
     *
     * @return string
     */
    protected function interfaceDestinationPath(): string
    {
        return app_path() . $this->resolveNamespace() . "/Services/Interfaces" . '/' . $this->getInterfaceName() . '.php';
    }

    /**
     * Return only repository class name
     * getRepositoryNameWithoutNamespace
     *
     * @return string
     */
    private function getRepositoryNameWithoutNamespace(): string
    {
        return preg_replace('/service/i', '', class_basename($this->getServiceName())) . "Repository";
    }

    /**
     * Return Model name for this repository class
     * getInterfaceName
     *
     * @return string
     */
    protected function getModelName(): string
    {
        return preg_replace('/service/i', '', class_basename($this->getServiceName()));
    }

    /**
     * Return only service class name
     * getServiceNameWithoutNamespace
     *
     * @return string
     */
    private function getServiceNameWithoutNamespace(): string
    {
        return class_basename($this->getServiceName());
    }

    /**
     * Set Default Namespace
     * Override CommandGenerator class method
     * getDefaultNamespace
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        $configNamespace = $this->getServiceNamespaceFromConfig();
        return "$configNamespace\\Services";
    }

    /**
     * Return only repository interface name
     * getInterfaceNameWithoutNamespace
     *
     * @return string
     */
    private function getInterfaceNameWithoutNamespace(): string
    {
        return class_basename($this->getInterfaceName());
    }

    /**
     * Set Default interface Namespace
     * Override CommandGenerator class method
     * getDefaultInterfaceNamespace
     *
     * @return string
     */
    public function getDefaultInterfaceNamespace(): string
    {
        $configNamespace = $this->getRepositoryNamespaceFromConfig();
        return "$configNamespace\\Services\\Interfaces";
    }


    /**
     * Return stub file path
     * getStubFilePath
     *
     * @return string
     */
    protected function getStubFilePath(): string
    {
        if ($this->option('interface') === true) {
            $stub = '/stubs/service-interface.stub';
        } else {
            $stub = '/stubs/service.stub';
        }
        return $stub;
    }


    /**
     * Generate file content
     * getTemplateContents
     *
     * @return string
     */
    protected function getTemplateContents(): string
    {
        return (new GenerateFile(__DIR__ . $this->getStubFilePath(), [
            'CLASS_NAMESPACE'   => $this->getClassNamespace(),
            'CLASS'             => $this->getServiceNameWithoutNamespace(),
            'INTERFACE_NAMESPACE' => $this->getInterfaceNamespace() . '\\' . $this->getInterfaceNameWithoutNamespace(),
            'INTERFACE'         =>  $this->getInterfaceNameWithoutNamespace(),
            'REPOSITORY'        => str_replace('/', '\\', $this->getRepoName()),
            'REPO'              => $this->getRepoName()

        ]))->render();
    }

    /**
     * Generate interface file content
     * getInterfaceTemplateContents
     *
     * @return string
     */
    protected function getInterfaceTemplateContents(): string
    {
        if ($this->option('interface') == true) {
            return (new GenerateFile(__DIR__ . "/stubs/interface-service.stub", [
                'CLASS_NAMESPACE' => $this->getInterfaceNamespace(),
                'INTERFACE' => $this->getInterfaceNameWithoutNamespace(),
                'REPO' => $this->getRepoName()
            ]))->render();
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getTemplateContents();

        // For Interface
        if ($this->option('interface') == true) {
            //for Interface
            $interfacePath = str_replace('\\', '/', $this->interfaceDestinationPath());

            if (!$this->laravel['files']->isDirectory($dir = dirname($interfacePath))) {
                $this->laravel['files']->makeDirectory($dir, 0777, true);
            }

            $interfaceContents = $this->getInterfaceTemplateContents();
        }

        try {
            (new FileGenerator($path, $contents))->generate();

            $this->info("Created : {$path}");

            // For Interface
            if ($this->option('interface') == true) {

                (new FileGenerator($interfacePath, $interfaceContents))->generate();

                $this->info("Created : {$interfacePath}");
            }
        } catch (\Exception $e) {

            $this->error("File : {$e->getMessage()}");

            return E_ERROR;
        }

        return 0;
    }
}