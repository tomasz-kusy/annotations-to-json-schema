<?php
namespace TKusy\JSchema\Command;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Yaml\Yaml;
use TKusy\JSchema\Configuration;
use TKusy\JSchema\JsonSchema\Generator;
use TKusy\JSchema\Property\PropertyReader;
use TKusy\JSchema\Writer\FileWriter;

class ConvertToJschema extends Command
{
    protected static $defaultName = 'convert2jschema';

    /**
     * @var ContainerBuilder
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->container->register('filesystem', Filesystem::class);
        $this->container->register('a2jschema.reader', PropertyReader::class);
        $this->container->register('a2jschema.writer', FileWriter::class)
            ->addArgument(new Reference('filesystem'));
        $this->container->register('a2jschema.generator', Generator::class)
            ->addArgument(new Reference('a2jschema.writer'));

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('className', InputArgument::REQUIRED, 'Entry class'),
        ]);
        $this->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Config file');
        $this->addOption('root-namespace', 'r', InputOption::VALUE_OPTIONAL, 'RootNamespace');
        $this->addOption('destination-path', 'o', InputOption::VALUE_OPTIONAL, 'Output directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->loadConfiguration($input->getOption('config'));

        $config['rootNamespace'] =
            $input->getOption('root-namespace')
            ?? (substr($config['rootNamespace'], -1, 1) === '\\'
                ? $config['rootNamespace']
                : $config['rootNamespace'] . '\\');

        $config['destination']['path'] = $input->getOption('destination-path') ?? $config['destination']['path'];

        $entry = $input->getArgument('className');
        if (!class_exists($entry)) {
            $entry = $config['rootNamespace'] . $input->getArgument('className');
            if (!class_exists($entry)) {
                throw new \RuntimeException(
                    "Can't find entry class. Properly configure rootNamespace or use entry class FQN"
                );
            }
        }

        $destPath = $config['destination']['path'] ?? $this->getDestinationPath($entry, $config['rootNamespace']);

        if ($destPath[strlen($destPath) - 1] !== '/') {
            $destPath .= '/';
        }

        $this->container->getDefinition('a2jschema.generator')->addArgument($config);
        $this->container->getDefinition('a2jschema.writer')
            ->addArgument($config['idPrefix'])->addArgument($destPath);

        /** @var Generator $generator */
        $generator = $this->container->get('a2jschema.generator');

        /** @var PropertyReader $reader */
        $reader = $this->container->get('a2jschema.reader');

        $generator->generate($reader->getPropertiesTree($entry));
        return 0;
    }

    private function getDestinationPath(string $entryClassName, string $rootNamespace)
    {
        $class = new \ReflectionClass($entryClassName);
        $directory = dirname($class->getFileName());

        $relative = preg_replace(['#' . preg_quote($rootNamespace, '#') . '#', '#\\\\#'], ['', '\\'], $entryClassName);
        for ($i = 0; $i < substr_count($relative, '\\'); $i++) {
            $directory = substr($directory, 0, strrpos($directory, '/'));
        }

        return $directory . '/';
    }

    protected function loadConfiguration(?string $filename)
    {
        if ($filename) {
            $fileLocator = new FileLocator('.');
            $configFile = $fileLocator->locate($filename);
            $configValues = Yaml::parse(file_get_contents($configFile));
        } else {
            $configValues = [];
        }

        $processor = new Processor();
        $configuration = new Configuration();
        return $processor->processConfiguration(
            $configuration,
            $configValues
        );
    }
}
