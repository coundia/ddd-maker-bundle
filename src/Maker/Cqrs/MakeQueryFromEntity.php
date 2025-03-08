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

class MakeQueryFromEntity extends BaseMaker
{
	const PREFIX_DIR = '\\';

	public static function getCommandName(): string
	{
		return 'make:ddd-query-from-entity';
	}

	public static function getCommandDescription(): string
	{
		return 'Generates a query and its handler for a given entity using the CQRS pattern. The generated query can be used in a controller and includes integration tests.';
	}

	public function configureCommand(Command $command, InputConfiguration $inputConfig): void
	{
		$command->addArgument('entity', InputArgument::REQUIRED, 'The FQCN of the entity')->addOption(
			'force',
			null,
			InputOption::VALUE_OPTIONAL,
			'Overwrite existing files',
			true
		);
		$command->addArgument('query', InputArgument::OPTIONAL, 'Name of the query action','Find');
		$command->addArgument('parameter', InputArgument::OPTIONAL, 'Find by parameter','id');
	}

	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void {
		$entityFqcn = trim($input->getArgument('entity'));
		$entityFqcn = $this->resolveEntityNamespace($entityFqcn);
		$query = trim($input->getArgument('query'));
		$parameter = trim($input->getArgument('parameter'));
		$this->force = $input->getOption('force') == 'true' ? true : false;
		if(!$parameter){
			$parameter = "id";
		}
		if(!$query){
			$query = "FindBy";
		}
		$query = ucfirst($query)."By".ucfirst($parameter);

		$entityClassDetails = $generator->createClassNameDetails(
			$entityFqcn,
			'Entity\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();

		$namespaceBase = 'App\\' . $this->baseDir . '\\Application';
		$pathBase = 'src/' . $this->baseDir . '/Application';

		$query_full_class = "\\".$namespaceBase.'\\Query\\' . $query.$entityShort . 'Query';
		$mapper_full_class = "\\".$namespaceBase.'\\Mapper\\' . $entityShort . '\\'.$entityShort .'Mapper';

		$filesConfig = [
			[
				'suffix' => 'Query',
				'template' => Common::APPLICATION_TPL_DIR . 'Query/Query.tpl.php',
			],
			[
				'suffix' => 'QueryHandler',
				'template' => Common::APPLICATION_TPL_DIR . 'QueryHandler/QueryHandler.tpl.php',
			]
		];

		foreach ($filesConfig as $config) {
			$className = $query.$entityShort . $config['suffix'];
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
					'query_full_class' => $query_full_class,
					'namespace' => $namespace,
					'entity_class_name' => $entityShort,
					'entity_full_class' => self::PREFIX_DIR . $entityClassDetails->getFullName(),
					'attributes' => $this->getEntityAttributes($entityClassDetails->getFullName()),
					'mapper_full_class' => $mapper_full_class,
					'model' => $this->getFullDomainName($entityShort, $this->baseDir),
					'parameter' => $parameter,
				]
			);
		}
		(new MakeControllerForQueryCqrsFromEntity($this->baseDir))->generate(
			$input,
			$io,
			$generator
		);

		$generator->writeChanges();
		$io->success('CQRS query and handler successfully generated.');
	}
	

}
