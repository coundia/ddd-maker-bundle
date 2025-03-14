<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\SubMaker;


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
 * Class MakeApiTest
 * Generates functional API tests for an entity.
 */
class MakeApiTest extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-crud-tests';
	}

	public static function getCommandDescription(): string{
		return 'Generates functional API tests for an entity in tests/Functional';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The FQCN of the entity'
		)->addOption(
			'force',
			null,
			InputOption::VALUE_OPTIONAL,
			'Overwrite existing files',
			true
		);
	}

	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{
		$entity = trim($input->getArgument('entity'));
		$entity = $this->resolveEntityNamespace($entity);
		$this->force = $input->getOption('force') == 'true' ? true : false;

		$entityClassDetails = $generator->createClassNameDetails(
			$entity,
			'Entity\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();
		$testNamespace = 'App\\Tests\\Functional';
		$attributes = $this->getEntityAttributes($entityClassDetails->getFullName());

		$testTemplates = [
			'CreateControllerTest.tpl.php',
			'DeleteControllerTest.tpl.php',
			'ListControllerTest.tpl.php',
			'UpdateControllerTest.tpl.php',
			'BulkControllerTest.tpl.php',
		];

		foreach ($testTemplates as $template){

			$fileName = $entityShort . str_replace(
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
					'entity_name_plural' => strtolower($entityShort) . 's',
					'entity_factory' => '\App\\' . $this->baseDir . '\Infrastructure\Factory\\' . $entityShort .
						'Factory', 'user_factory' => '\App\Security\Infrastructure\Factory\UserFactory',
				]
			);
		}

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}
}
