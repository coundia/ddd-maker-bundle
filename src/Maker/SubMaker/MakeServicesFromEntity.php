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

/**
 * Class MakeServicesFromEntity
 * Generates CRUD services for an entity in the Infrastructure\Service directory.
 */
class MakeServicesFromEntity extends BaseMaker{
	private const PREFIX_DIR = '\\';

	public static function getCommandName(): string{
		return 'make:ddd-services';
	}

	public static function getCommandDescription(): string{
		return 'Generates creation, deletion, query, and update services for an entity in the Infrastructure\Service directory.';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The fully qualified class name of the entity'
		);
	}

	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{
		$entityFqcn = trim($input->getArgument('entity'));
		$this->force = $input->getOption('force') == 'true' ? true : false;

		$entityFqcn = $this->resolveEntityNamespace($entityFqcn);
		$entityClassDetails = $generator->createClassNameDetails(
			$entityFqcn,
			'Entity\\',
			''
		);

		$entityShort = $entityClassDetails->getShortName();

		$context = $this->baseDir;

		$serviceNamespace = sprintf(
			'App\\%s\\Application\\Service',
			$context
		);
		$servicePath = sprintf(
			'src/%s/Application/Service',
			$context
		);

		$attributes = $this->getEntityAttributes($entityClassDetails->getFullName());

		$services = [
			'Create' => 'ServiceCreation.tpl.php', 'Delete' => 'ServiceDeletion.tpl.php',
			'Find'   => 'ServiceFind.tpl.php', 'Update' => 'ServiceUpdate.tpl.php',
		];

		foreach ($services as $suffix => $template){
			$className = $entityShort . $suffix;
			$targetPath = sprintf(
				'%s/%s.php',
				$servicePath,
				$className
			);

			if ($this->checkFileExists($targetPath)){
				$io->warning(
					sprintf(
						'File "%s" already exists.',
						$targetPath
					)
				);
				continue;
			}

			$variables = [
				'namespace' => $serviceNamespace, 'class_name' => $className, 'entity_full_class' => self::PREFIX_DIR .
					$entityClassDetails->getFullName(), 'entity_class_name' => $entityShort,
				'attributes' => $attributes, 'context' => $context, 'DTONamespace' => $this->getDTONamespace(
					$entityShort,
					$this->baseDir
				), 'model' => $this->getFullDomainName(
					$entityShort,
					$context
				), 'repository_interface' => self::PREFIX_DIR . 'App\\' . $context . '\\Domain\\Repository\\' .
					$entityShort . 'RepositoryInterface', 'use_case' => $this->getUseCase(
						$suffix,
						$entityShort,
						$context
					) . 'Interface',
			];
			$variables['model'] = $this->getFullDomainName(
				$entityShort,
				$context
			);
			$variables['entity_full_class_id'] = $this->getModelObjectValueId(
				$entityShort,
				$context
			);
			$variables['entity_full_class'] = $entityClassDetails->getFullName();

			$generator->generateFile(
				$targetPath,
				Common::RESOURCE_DIR . '/Aggregate/Application/Services/' . $template,
				$variables
			);
		}

		$generator->writeChanges();
		$io->success('CRUD services successfully generated.');
	}

	public function configureDependencies(DependencyBuilder $dependencies): void{
		$dependencies->addClassDependency(
			Command::class,
			'symfony/console'
		);
	}
}
