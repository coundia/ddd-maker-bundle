<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\Cqrs;

use Cnd\DddMakerBundle\Maker\Core\BaseMaker;
use Cnd\DddMakerBundle\Maker\Core\Common;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeCommandFromEntity
 *
 * Generates a CQRS command and its handler for an entity.
 */
class MakeCommandFromEntity extends BaseMaker
{
	const PREFIX_DIR = '\\';
	public static function getCommandName(): string
	{
		return 'make:ddd-command-from-entity';
	}

	public static function getCommandDescription(): string
	{
		return 'Generates a command and its handler for a given entity using the CQRS pattern. The generated command can be used in a controller and includes integration tests.';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void {
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The FQCN of the entity (Fully Qualified Class Name)'
		)->addOption(
			'force',
			null,
			InputOption::VALUE_OPTIONAL,
			'Overwrite existing files',
			true
		);
		$command->addArgument('action', InputArgument::OPTIONAL, 'Name of the command action','Create');
	}

	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void {
		$entityFqcn = trim($input->getArgument('entity'));
		$entityFqcn = $this->resolveEntityNamespace($entityFqcn);
		$action = trim($input->getArgument('action'));
		$this->force = $input->getOption('force') == 'true' ? true : false;

		if(!$action){
			$action = "Create";
		}

		$action = ucfirst($action);

		$entityClassDetails = $generator->createClassNameDetails(
			$entityFqcn,
			'Entity\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();

		$namespaceBase = 'App\\' . $this->baseDir . '\\Application';
		$pathBase = 'src/' . $this->baseDir . '/Application';

		$command_full_class = "\\".$namespaceBase.'\\Command\\' . $action.$entityShort . 'Command';
		$mapper_full_class = "\\".$namespaceBase.'\\Mapper\\' . $entityShort . '\\'.$entityShort .'Mapper';

		$filesConfig = [
			[
				'suffix' => 'Command',
				'template' => Common::APPLICATION_TPL_DIR . 'Command/Command.tpl.php',
			],
			[
				'suffix' => 'CommandHandler',
				'template' => Common::APPLICATION_TPL_DIR . 'CommandHandler/CommandHandler.tpl.php',
			]
		];

		foreach ($filesConfig as $config) {
			$className = $action.$entityShort . $config['suffix'];
			$namespace = $namespaceBase."\\" .$config['suffix'];
			$filePath = sprintf('%s/%s.php', $pathBase . '/' . $config['suffix'], $className);

			if ($this->checkFileExists($filePath)) {
				$io->warning(sprintf('File "%s" already exists.', $filePath));
				continue;
			}

			if (!file_exists(dirname($filePath))) {
				mkdir(dirname($filePath), 0777, true);
			}

			$generator->generateFile(
				$filePath,
				$config['template'],
				[
					'context' => $this->baseDir,
					'class_name' => $className,
					'command_full_class' => $command_full_class,
					'namespace' => $namespace,
					'entity_class_name' => $entityShort,
					'entity_full_class' => self::PREFIX_DIR . $entityClassDetails->getFullName(),
					'attributes' => $this->getEntityAttributes($entityClassDetails->getFullName()),
					'mapper_full_class' => $mapper_full_class,
					'model' => $this->getFullDomainName($entityShort, $this->baseDir)
				]
			);
		}

		(new MakeControllerForCommandCqrsFromEntity($this->baseDir))->generate(
			$input,
			$io,
			$generator
		);


		$generator->writeChanges();
		$io->success('CQRS command and handler successfully generated.');
	}
}
