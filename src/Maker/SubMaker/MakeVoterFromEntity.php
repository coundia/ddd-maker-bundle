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

/**
 * Class MakeVoterFromEntity
 * Generates a voter for an entity in the Infrastructure\Voters directory.
 */
class MakeVoterFromEntity extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-voter';
	}

	public static function getCommandDescription(): string{
		return 'Generates a voter for an entity in the Domain module.';
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
		$entity = trim($input->getArgument('entity'));
		$this->force = $input->getOption('force') == 'true' ? true : false;

		$entity = $this->resolveEntityNamespace($entity);
		$entityClassDetails = $generator->createClassNameDetails(
			$entity,
			'Domain\\Entity\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();

		$baseNamespace = sprintf(
			'%s',
			$entityShort
		);
		$modulePath = sprintf(
			'src/%s',
			$this->baseDir
		);

		$voterClassName = $entityShort . 'Voter';

		$voterNamespace = sprintf(
			'App\\%s\\Infrastructure\\Voters',
			$this->baseDir
		);
		$voterPath = sprintf(
			'%s/Infrastructure/Voters/%s.php',
			$modulePath,
			$voterClassName
		);

		if ($this->checkFileExists($voterPath)){
			$io->warning(
				sprintf(
					'The file "%s" already exists',
					$voterPath
				)
			);
			return;
		}

		$this->generateFile(
			$generator,
			$voterPath,
			Common::RESOURCE_DIR . '/Aggregate/Infrastructure/Voter.tpl.php',
			[
				'namespace' => $voterNamespace, 'class_name' => $voterClassName,
				'entity_full_class' => $entityClassDetails->getFullName(), 'entity_class_name' => $entityShort,
			]
		);

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
