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

/**
 * Class MakeDddEntity
 * Generates a DDD-compliant entity structure from an existing entity.
 *
 */
class MakeDomainAggregate extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-domain-aggregate-entity';
	}

	public static function getCommandDescription(): string{
		return 'Generates a DDD-compliant entity structure from an existing entity';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The FQCN of the source entity'
		);
	}

	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{
		$entity = trim($input->getArgument('entity'));
		$entity = $this->resolveEntityNamespace($entity);
		$entityClassDetails = $generator->createClassNameDetails(
			$entity,
			'Domain\Model\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();
		$attributes = $this->getEntityAttributes($entityClassDetails->getFullName());
		$this->force = $input->getOption('force') == 'true' ? true : false;

		$files = [
			'DomainModel' => $this->baseDir . "/Domain/Aggregate/{$entityShort}Model.php",
		];

		foreach ($files as $type => $path){
			$targetPath = "src/{$path}";

			if ($this->checkFileExists($targetPath)){
				$io->warning(
					sprintf(
						'File "%s" already exists. Skipping generation.',
						$targetPath
					)
				);
				continue;
			}

			$generator->generateFile(
				$targetPath,
				Common::RESOURCE_DIR . "/Aggregate/Domain/Aggregate/{$type}.tpl.php",
				[
					'namespace'             => 'App\\' . str_replace(
							'/',
							'\\',
							dirname($path)
						), 'class_name'     => basename(
					$path,
					'.php'
				),
					'entity_class_name'      => $entityShort,
					'context'      => $this->baseDir,
					'entity_full_class'     => $this->getFullDomainName($entityShort), 'attributes' => $attributes,
					'interface_name'        => "{$entityShort}QueryInterface", 'context' => '\\App\\' . $this->baseDir,
					'model_object_value_id' => $this->getModelObjectValueId(
						$entityShort,
						$this->baseDir
					),
				]
			);
		}

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}

}
