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
 * Class MakeDomaineUseCaseFromEntity
 * Generates CRUD interfaces for an entity in the Domain\Repository directory.
 */
class MakeDomaineUseCaseFromEntity extends BaseMaker{
	private const PREFIX_DIR = '\\';

	public static function getCommandName(): string{
		return 'make:ddd-domain-use-case';
	}

	public static function getCommandDescription(): string{
		return 'Generates creation, deletion, query, and update interfaces for an entity in the Domain\Repository directory.';
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
		$entityFqcn = $this->resolveEntityNamespace($entityFqcn);
		$this->force = $input->getOption('force') == 'true' ? true : false;

		$entityClassDetails = $generator->createClassNameDetails(
			$entityFqcn,
			'Entity\\',
			''
		);

		$entityShort = $entityClassDetails->getShortName();
		$context = $this->baseDir;

		$interfaceNamespace = sprintf(
			'App\\%s\\Domain\\UseCase',
			$context
		);
		$interfacePath = sprintf(
			'src/%s/Domain/UseCase',
			$context
		);

		$interfaces = [
			'CreateInterface' => 'CreateInterface.tpl.php', 'DeleteInterface' => 'DeleteInterface.tpl.php',
			'FindInterface'   => 'FindInterface.tpl.php', 'UpdateInterface' => 'UpdateInterface.tpl.php',
		];

		foreach ($interfaces as $suffix => $template){
			$className = $entityShort . $suffix;
			$targetPath = sprintf(
				'%s/%s.php',
				$interfacePath,
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
				'namespace'            => $interfaceNamespace, 'class_name' => $className,
				'entity_full_class'    => $this->getFullDomainName(
					$entityShort,
					$context
				), 'entity_class_name' => $entityShort, 'entity_full_class_id' => $this->getModelObjectValueId(
					$entityShort,
					$context
				), 'context'           => $context,
			];

			$generator->generateFile(
				$targetPath,
				Common::RESOURCE_DIR . '/Aggregate/Domain/UseCase/' . $template,
				$variables
			);
		}

		$generator->writeChanges();
		$io->success('CRUD interfaces successfully generated.');
	}

	public function configureDependencies(DependencyBuilder $dependencies): void{
		$dependencies->addClassDependency(
			Command::class,
			'symfony/console'
		);
	}
}
