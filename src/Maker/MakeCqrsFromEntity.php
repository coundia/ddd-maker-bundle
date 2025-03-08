<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker;

use Cnd\DddMakerBundle\Maker\Core\BaseMaker;
use Cnd\DddMakerBundle\Maker\Cqrs\MakeCommandFromEntity;
use Cnd\DddMakerBundle\Maker\Cqrs\MakeCqrsSharedFiles;
use Cnd\DddMakerBundle\Maker\Cqrs\MakePaginatedQueryFromEntity;
use Cnd\DddMakerBundle\Maker\Cqrs\MakeQueryFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomainAggregate;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomaineUseCaseFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomainEvent;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDomainException;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeDTOFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeFactoryFromEntity;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeMapper;
use Cnd\DddMakerBundle\Maker\SubMaker\MakeRepositoryFromEntity;
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
 * Class MakeCqrsFromEntity
 *
 * Generates a complete DDD structure with CQRS Pattern.
 */
class MakeCqrsFromEntity extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-full';
	}

	public static function getCommandDescription(): string{
		return 'Generates a complete Domain-Driven Design (DDD) structure following the CQRS pattern, including commands, queries, value objects, repositories, factories, mappers, domain events, exceptions, and more.';
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

		$command->addArgument('query', InputArgument::OPTIONAL, 'Name of the query action',"Find");
		$command->addArgument('action', InputArgument::OPTIONAL, 'Name of the command action',"Create");
		$command->addArgument('parameter', InputArgument::OPTIONAL, 'Find by parameter','id');
	}

	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{

		$this->validate(trim($input->getArgument('entity')));
		$baseDir = $input->getOption('dir') ?: 'Core';
		$this->force = $input->getOption('force') == 'true' ? true : false;
		 
		(new MakeCqrsSharedFiles($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeDTOFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeFactoryFromEntity($baseDir, $this->force))->generate(
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
		(new MakeCommandFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakeQueryFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);
		(new MakePaginatedQueryFromEntity($baseDir, $this->force))->generate(
			$input,
			$io,
			$generator
		);

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}


} 
