<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\SubMaker;

use Cnd\DddMakerBundle\Maker\Core\BaseMaker;
use Cnd\DddMakerBundle\Maker\Core\Common;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 * Generates Value Objects from an existing entity's properties.
 */
class MakeValueObjectsDomain extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-vo';
	}

	public static function getCommandDescription(): string{
		return 'Generates Value Objects from an existing entity properties';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The FQCN of the source entity'
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

		$entity = $this->resolveEntityNamespace($entity);

		$entityClassDetails = $generator->createClassNameDetails(
			$entity,
			'Domain\\Model\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();

		$properties = $this->getEntityAttributes($entity);

		$valueObjectPath = "src/{$this->baseDir}/Domain/ValueObject";

		if (!is_dir($valueObjectPath)){
			mkdir(
				$valueObjectPath,
				0777,
				true
			);
		}

		foreach ($properties as $property){
			$propertyName = ucfirst($property->name);
			$propertyType = $property->getInterface();

			if (!$property->isValueObject()){
				continue;
			}

			if (!$property->isPrimitiveType()){
				$propertyType = 'string';
			}

			$valueObjectClass = "{$entityShort}{$propertyName}";
			$targetPath = "{$valueObjectPath}/{$valueObjectClass}.php";

			if ($this->checkFileExists($targetPath)){
				$io->warning("Value Object '{$valueObjectClass}.php' already exists. Skipping.");
				continue;
			}


			$generator->generateFile(
				$targetPath,
				Common::RESOURCE_DIR . '/Aggregate/Domain/ValueObject/ValueObject.tpl.php',
				[
					'namespace' => "App\\{$this->baseDir}\Domain\\ValueObject", 'class_name' => $valueObjectClass,
					'property_name' => lcfirst($propertyName),
					'property_type' => $propertyType,
					'context' => $this->baseDir,
				]
			);

			$io->success("Generated Value Object: {$valueObjectClass}.php");
		}

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}

	public function configureDependencies(DependencyBuilder $dependencies): void{
		$dependencies->addClassDependency(
			Command::class,
			'symfony/console'
		);
	}
}
