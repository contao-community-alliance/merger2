<?php

declare(strict_types=1);

use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationBuilderInterface;
use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationInterface;
use Phpcq\PluginApi\Version10\DiagnosticsPluginInterface;
use Phpcq\PluginApi\Version10\EnvironmentInterface;
use Phpcq\PluginApi\Version10\Output\OutputInterface;
use Phpcq\PluginApi\Version10\Output\OutputTransformerFactoryInterface;
use Phpcq\PluginApi\Version10\Output\OutputTransformerInterface;
use Phpcq\PluginApi\Version10\Report\ReportInterface;
use Phpcq\PluginApi\Version10\Report\TaskReportInterface;
use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser;

return new class implements DiagnosticsPluginInterface {
    public function getName(): string
    {
        return 'author-validation';
    }

    public function describeConfiguration(PluginConfigurationBuilderInterface $configOptionsBuilder): void
    {
        $configOptionsBuilder->describeStringListOption(
            'custom_flags',
            'Any custom flags to pass to phpunit. For valid flags refer to the phpunit documentation.'
        );

        $configOptionsBuilder
            ->describeStringOption('config_file', 'The phpspec.yml configuration file')
            ->isRequired()
            ->withDefaultValue('.check-author.yml');

        $configOptionsBuilder
            ->describeStringOption('path', 'The path to the author validation binary')
            ->isRequired()
            ->withDefaultValue('vendor/bin/check-author.php');
    }

    public function createDiagnosticTasks(PluginConfigurationInterface $config, EnvironmentInterface $environment): iterable
    {
        $projectRoot = $environment->getProjectConfiguration()->getProjectRootPath();
        yield $environment
            ->getTaskFactory()
            ->buildPhpProcess('phpspec', $this->buildArguments($config))
            ->withWorkingDirectory($projectRoot)
            ->withOutputTransformer($this->createOutputTransformer())
            ->build();
    }

    private function buildArguments(PluginConfigurationInterface $config): array
    {
        $arguments = [
            $config->getString('path'),
            '--no-progress',
            '--php-files',
            '--diff',
            '--config',
            $config->getString('config_file')
        ];
        if ($config->has('custom_flags')) {
            foreach ($config->getStringList('custom_flags') as $flag) {
                $arguments[] = $flag;
            }
        }

        return $arguments;
    }

    private function createOutputTransformer(): OutputTransformerFactoryInterface
    {
        require __DIR__ . '/../../../vendor/autoload.php';

        return new class() implements OutputTransformerFactoryInterface {
            public function createFor(TaskReportInterface $report): OutputTransformerInterface
            {
                return new class($report) implements OutputTransformerInterface {
                    /** @var TaskReportInterface $report */
                    private $report;
                    /** @var string */
                    private $buffer = '';
                    /** @var string */
                    private $errors = '';

                    public function __construct(TaskReportInterface $report)
                    {
                        $this->report = $report;
                    }

                    public function write(string $data, int $channel): void
                    {
                        switch ($channel) {
                            case OutputInterface::CHANNEL_STDOUT:
                                $this->buffer .= $data;
                                break;
                            case OutputInterface::CHANNEL_STDERR:
                                $this->errors .= $data;
                        }
                    }

                    public function finish(int $exitCode): void
                    {
                        if ($this->errors) {
                            $this->report
                                ->addAttachment('error.log')
                                ->fromString($this->errors)
                                ->setMimeType('text/plain');
                        }

                        $parser = new Parser();
                        $diffs = $parser->parse($this->buffer);

                        foreach ($diffs as $diff) {
                            foreach ($diff->getChunks() as $chunk) {
                                $lineNumber = $chunk->getStart();
                                foreach ($chunk->getLines() as $line) {
                                    $lineNumber++;
                                    switch ($line->getType()) {
                                        case Line::ADDED:
                                            $message = 'Missing author ' . $this->extractAuthor($line->getContent());
                                            break;
                                        case Line::REMOVED:
                                            $message = 'Superfluous author ' . $this->extractAuthor($line->getContent());
                                            break;
                                        default:
                                            continue 2;
                                    }

                                    $this->report->addDiagnostic(TaskReportInterface::SEVERITY_MAJOR, $message)
                                        ->forFile($diff->getFrom())
                                        ->forRange($lineNumber);
                                }
                            }
                        }

                        $this->report->close(
                            $exitCode === 0 ? ReportInterface::STATUS_PASSED : ReportInterface::STATUS_FAILED
                        );
                    }

                    private function extractAuthor(string $content): string
                    {
                        preg_match('#@author\s*([^\s].*)$#', $content, $matches);

                        return $matches[1] ?? '';
                    }
                };
            }
        };
    }
};
