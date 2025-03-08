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
 * Generates a Factory, Default Story, and DataFixtures for an entity.
 */
class MakeFactoryFromEntity extends BaseMaker{
	private const PREFIX_DIR = '\\';

	public static function getCommandName(): string{
		return 'make:ddd-factory';
	}

	public static function getCommandDescription(): string{
		return 'Generates a Factory for an entity in Domain\Factory, a Default Story in Infrastructure\Story, and a DataFixtures file in Infrastructure\DataFixtures.';
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
		$modulePath = sprintf(
			'src/%s',
			$this->baseDir
		);
		$nameBaseNamespace = "App\\" . $this->baseDir;

		$paths = [
			'factory'     => sprintf(
				'%s/Infrastructure/Factory/%sFactory.php',
				$modulePath,
				$entityShort
			), 'story'    => sprintf(
				'%s/Infrastructure/Story/%sStory.php',
				$modulePath,
				$entityShort
			), 'fixtures' => sprintf(
				'%s/Infrastructure/DataFixtures/%sFixtures.php',
				$modulePath,
				$entityShort
			),
		];

		$attributes = $this->getEntityAttributes($entityClassDetails->getFullName());

		$templates = [
			'Factory'     => [
				'template' => Common::RESOURCE_DIR . '/Aggregate/Infrastructure/Factory.tpl.php', 'variables' => [
					'namespace'         => sprintf(
						'%s\\Infrastructure\\Factory',
						$nameBaseNamespace
					), 'class_name'     => $entityShort . 'Factory',
					'entity_full_class' => self::PREFIX_DIR . $entityClassDetails->getFullName(),
					'entity_class_name' => $entityShort, 'attributes' => $attributes,
				],
			], 'Story'    => [
				'template' => Common::RESOURCE_DIR . '/Aggregate/Infrastructure/DefaultStory.tpl.php', 'variables' => [
					'namespace'         => sprintf(
						'%s\\Infrastructure\\Story',
						$nameBaseNamespace
					), 'class_name'     => $entityShort . 'Story',
					'entity_full_class' => self::PREFIX_DIR . $entityClassDetails->getFullName(),
					'entity_class_name' => $entityShort, 'attributes' => $attributes, 'factory_full_class' => sprintf(
						'%s\\Infrastructure\\Factory\\%sFactory',
						$nameBaseNamespace,
						$entityShort
					), 'count'          => 15,
				],
			], 'Fixtures' => [
				'template' => Common::RESOURCE_DIR . '/Aggregate/Infrastructure/Fixtures.tpl.php', 'variables' => [
					'namespace'      => sprintf(
						'%s\\Infrastructure\\DataFixtures',
						$nameBaseNamespace
					), 'class_name'  => $entityShort . 'Fixtures', 'story_full_class' => sprintf(
						'%s\\Infrastructure\\Story\\%sStory',
						$nameBaseNamespace,
						$entityShort
					), 'story_class' => $entityShort . 'Story',
				],
			],
		];

		foreach ($templates as $key => $template){
			if ($this->checkFileExists($paths[strtolower($key)])){
				$io->warning(
					sprintf(
						'File %s already exists.',
						$paths[strtolower($key)]
					)
				);
				continue;
			}
			$this->generateFile(
				$generator,
				$paths[strtolower($key)],
				$template['template'],
				$template['variables']
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
}
