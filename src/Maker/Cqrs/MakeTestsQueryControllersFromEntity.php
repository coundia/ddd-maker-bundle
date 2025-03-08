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

class MakeTestsQueryControllersFromEntity extends BaseMaker{

	public static function getCommandName(): string{
		return 'make:ddd-test-query-controller-from-entity';
	}

	public static function getCommandDescription(): string{
		return 'Generates Presentation a Test controllers for query for a given entity.';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The FQCN of the entity '
		)->addOption(
			'force',
			null,
			InputOption::VALUE_OPTIONAL,
			'Overwrite existing files',
			true
		);

		$command->addArgument('query', InputArgument::OPTIONAL, 'Name of the query action',"Find");
		$command->addArgument('parameter', InputArgument::OPTIONAL, 'Find by parameter','id');
	}
	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{
		$entity = trim($input->getArgument('entity'));
		$entity = $this->resolveEntityNamespace($entity);
		$action = ucfirst($input->getArgument('query'));
		$action = ucfirst($input->getArgument('query'));
		$this->force = $input->getOption('force') == 'true' ? true : false;

		$parameter = ucfirst($input->getArgument('parameter'));
		$action  = ucfirst($action)."By".ucfirst($parameter);

		$entityClassDetails = $generator->createClassNameDetails(
			$entity,
			'Entity\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();
		$testNamespace = 'App\\Tests\\Functional';
		$attributes = $this->getEntityAttributes($entityClassDetails->getFullName());

		$testTemplates = [
			'QueryControllerTest.tpl.php',
		];

		foreach ($testTemplates as $template){

			$fileName = $action.$entityShort . str_replace(
					'.tpl.php',
					'.php',
					$template
				);

			$targetPath = sprintf(
				'tests/Functional/%s/%s',
				$entityShort,
				$fileName
			);


			if ($this->checkFileExists($targetPath)){
				$io->warning(
					sprintf(
						'File "%s" already exists. Skipping Response generation.',
						$targetPath
					)
				);
				continue;
			}
			$generator->generateFile(
				$targetPath,
				Common::RESOURCE_DIR . '/tests/Shared/Functional/' . $template,
				[
					'namespace' => $testNamespace . "\\" . $entityShort,
					'class_name' => str_replace(
						'.php',
						'',
						$fileName
					), 'entity_name' => $entityShort,
					'entity_full_class' => $entityClassDetails->getFullName(),
					'attributes' => $attributes, 'api_version' => Common::VERSION_API,
					'base_test_case' => '\App\Tests\Shared\BaseWebTestCase',
					'entity_name_plural' => strtolower($entityShort) . 's/'.strtolower($action),
					'entity_factory' => '\App\\' . $this->baseDir . '\Infrastructure\Factory\\' . $entityShort .
						'Factory', 'user_factory' => '\App\Security\Infrastructure\Factory\UserFactory',
					'parameter' => $parameter,
					'action' => $action,

				]
			);
		}

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}


}
