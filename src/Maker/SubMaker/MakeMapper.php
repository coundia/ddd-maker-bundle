<?php

declare(strict_types=1);

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
 * Class MakeEntityMapper.
 * Generates a Mapper and a MapperInterface for a given entity.
 */
class MakeMapper extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-mapper';
	}

	public static function getCommandDescription(): string{
		return 'Generates a Mapper and its corresponding interface for a given entity.';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The FQCN of the entity (e.g. App\Todos\Domain\Entity\Todo)'
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
			'Domain\\Entity\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();

		$moduleName = $this->baseDir;
		$modulePath = sprintf(
			'src/%s',
			$moduleName
		);
		$mapperNamespace = sprintf(
			'App\\%s\\Application\\Mapper\\%s',
			$moduleName,
			$entityShort
		);

		$DTONamespace = $this->getDTONamespace(
			$entityShort,
			$this->baseDir
		);

		$mappers = [
			[
				'template'  => Common::RESOURCE_DIR . '/Aggregate/Application/DTO/EntityMapperInterface.tpl.php',
				'variables' => [
					'namespace'    => $mapperNamespace, 'interface_class' => $entityShort . 'MapperInterface',
					'class_name'   => $entityShort . 'MapperInterface', 'entity_class_name' => $entityShort,
					'DTONamespace' => $DTONamespace,
				],
			], [
				'template' => Common::RESOURCE_DIR . '/Aggregate/Application/DTO/EntityMapper.tpl.php', 'variables' => [
					'namespace'       => $mapperNamespace, 'class_name' => $entityShort . 'Mapper',
					'interface_class' => $entityShort . 'MapperInterface', 'entity_class_name' => $entityShort,
					'DTONamespace'    => $DTONamespace,
				],
			],
		];

		foreach ($mappers as $mapper){
			$targetPath = sprintf(
				'%s/Application/Mapper/%s/%s.php',
				$modulePath,
				$entityShort,
				$mapper['variables']['class_name']
			);
			if ($this->checkFileExists($targetPath)){
				$io->warning(
					sprintf(
						'File "%s" already exists. Skipping generation.',
						$targetPath
					)
				);
				continue;
			}
			$variables = $mapper['variables'];
			$variables["attributes"] = $this->getEntityAttributes($entityClassDetails->getFullName());
			$variables["context"] = $this->baseDir;
			$variables["entity_full_class"] = "\\" . $entityClassDetails->getFullName();
			$variables['entity_full_class_id'] = $this->getModelObjectValueId(
				$entityShort,
				$this->baseDir
			);
			$variables['entity_model_class'] = $this->getFullDomainName(
				$entityShort,
				$this->baseDir
			);

			$this->generateFile(
				$generator,
				$targetPath,
				$mapper['template'],
				$variables
			);
		}

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}

	private function generateFile(
		Generator $generator,
		string $path,
		string $template,
		array $variables
	): void{
		$generator->generateFile(
			$path,
			$template,
			$variables
		);
	}

	public function configureDependencies(DependencyBuilder $dependencies): void{
		$dependencies->addClassDependency(
			Command::class,
			'symfony/console'
		);
	}
}
