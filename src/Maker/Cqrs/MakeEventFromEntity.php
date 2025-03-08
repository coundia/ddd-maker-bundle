<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\Cqrs;

use Cnd\DddMakerBundle\Maker\Core\BaseMaker;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MakeEventFromEntity extends BaseMaker
{
	const PREFIX_DIR = '\\';

	public static function getCommandName(): string
	{
		return 'make:event-from-entity';
	}

	public static function getCommandDescription(): string
	{
		return 'Generates Created and Updated Event classes for an entity in src/<Entity>';
	}

	public function configureCommand(Command $command, InputConfiguration $inputConfig): void
	{
		$command->addArgument('entity',
			InputArgument::REQUIRED, 'The FQCN of the entity')
			->addOption(
			'force',
			null,
			InputOption::VALUE_OPTIONAL,
			'Overwrite existing files',
			true
		);
	}

	public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
	{
		$entity = trim($input->getArgument('entity'));
		$entity = $this->resolveEntityNamespace($entity);
		$entityClassDetails = $generator->createClassNameDetails($entity, 'Entity\\', '');
		$entityShort = $entityClassDetails->getShortName();
		$attributes = $this->getEntityAttributes($entityClassDetails->getFullName());
		$baseNamespace = sprintf('%s', $entityShort);
		$modulePath = sprintf('src/%s', $entityShort);
		$this->force = $input->getOption('force') == 'true' ? true : false;
		// Generate the "Created" event
		$createdEventClassDetails = $generator->createClassNameDetails($entityShort.'CreatedEvent', $baseNamespace.'\\Event\\', 'Event');
		$createdEventNamespace = $this->extractNamespace($createdEventClassDetails->getFullName());
		$createdEventPath = sprintf('%s/Event/%s.php', $modulePath, $createdEventClassDetails->getShortName());
		if ($this->checkFileExists($createdEventPath)) {
			unlink($createdEventPath);
		}
		$generator->generateFile(
			$createdEventPath,
			__DIR__.'/../Resources/skeleton/Event.tpl.php',
			[
				'namespace'         => $createdEventNamespace,
				'class_name'        => $createdEventClassDetails->getShortName(),
				'entity_full_class' => self::PREFIX_DIR.$entityClassDetails->getFullName(),
				'entity_class_name' => $entityShort,
				'attributes'        => $attributes,
				'event_name'        => strtolower($entityShort).'_created',
			]
		);

		// Generate the "Updated" event
		$updatedEventClassDetails = $generator->createClassNameDetails($entityShort.'UpdatedEvent', $baseNamespace.'\\Event\\', 'Event');
		$updatedEventNamespace = $this->extractNamespace($updatedEventClassDetails->getFullName());
		$updatedEventPath = sprintf('%s/Event/%s.php', $modulePath, $updatedEventClassDetails->getShortName());
		if ($this->checkFileExists($updatedEventPath)) {
			unlink($updatedEventPath);
		}
		$generator->generateFile(
			$updatedEventPath,
			__DIR__.'/../Resources/skeleton/Event.tpl.php',
			[
				'namespace'         => $updatedEventNamespace,
				'class_name'        => $updatedEventClassDetails->getShortName(),
				'entity_full_class' => self::PREFIX_DIR.$entityClassDetails->getFullName(),
				'entity_class_name' => $entityShort,
				'attributes'        => $attributes,
				'event_name'        => strtolower($entityShort).'_updated',
			]
		);

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}

	public function configureDependencies(DependencyBuilder $dependencies): void
	{
		// No additional dependencies required.
	}

}
