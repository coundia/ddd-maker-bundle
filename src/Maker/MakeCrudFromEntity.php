<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker;

use Cnd\DddMakerBundle\Maker\Core\BaseMaker;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeApiTest;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeControllersFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomainAggregate;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomaineUseCaseFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomainEvent;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomainException;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDTOFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeFactoryFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeMapper;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeRepositoryFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeServicesFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeSetupCrudSharedFiles;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeValueObjectsDomain;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeVoterFromEntity;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeCrudFromEntity
 *
 * Generates a complete CRUD structure.
 */
class MakeCrudFromEntity extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-crud-full';
	}

	public static function getCommandDescription(): string{
		return 'Generates a complete CRUD structure.';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
				'entity',
				InputArgument::REQUIRED,
				'The FQCN of the entity'
			)
			->addOption(
				'dir',
				null,
				InputOption::VALUE_OPTIONAL,
				'The base directory for generated files',
				'Core'
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

		$this->validate(trim($input->getArgument('entity')));

		$baseDir = $input->getOption('dir') ?: 'Core';
		$this->force = $input->getOption('force') == 'true' ? true : false;

		(new MakeSetupCrudSharedFiles($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeDTOFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeControllersFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeFactoryFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeServicesFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeVoterFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeValueObjectsDomain($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeDomainAggregate($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeDomainEvent($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeDomainException($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeRepositoryFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeApiTest($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeDomaineUseCaseFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeMapper($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}


} 
