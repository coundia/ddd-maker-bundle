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
 * Class MakeDTOFromEntity
 * Generates a Request DTO and a Response DTO for an entity.
 */
class MakeDTOFromEntity extends BaseMaker{
	const PREFIX_DIR = '\\';

	public static function getCommandName(): string{
		return 'make:ddd-crud-dto';
	}

	public static function getCommandDescription(): string{
		return 'Generates a Request DTO and a Response DTO for an entity in src/<Entity>';
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
		$baseNamespace = sprintf(
			'%s',
			$this->baseDir
		);

		$modulePath = sprintf(
			'src/%s',
			$this->baseDir
		);

		$attributes = $this->getEntityAttributes($entityClassDetails->getFullName());

		// DTO configurations
		$DTOConfigs = [
			[
				'suffix'  => 'RequestDTO', 'namespace' => sprintf(
				'%s\\Application\\DTO',
				$baseNamespace
			), 'template' => Common::RESOURCE_DIR . '/Aggregate/Application/DTO/MakeRequestDTOFromEntity.tpl.php',
			], [
				'suffix'      => 'DTO', 'namespace' => sprintf(
					'%s\\Application\\DTO',
					$baseNamespace
				), 'template' => Common::RESOURCE_DIR . '/Aggregate/Application/DTO/AbstractDTO.tpl.php',
			], [
				'suffix'      => 'ResponseDTO', 'namespace' => sprintf(
					'%s\\Application\\DTO',
					$baseNamespace
				), 'template' => Common::RESOURCE_DIR . '/Aggregate/Application/DTO/MakeResponseDTOFromEntity.tpl.php',
			],
		];

		foreach ($DTOConfigs as $config){
			$DTOClassDetails = $generator->createClassNameDetails(
				$entityShort . $config['suffix'],
				$config['namespace'],
				'DTO'
			);
			$DTONamespace = $this->extractNamespace($DTOClassDetails->getFullName());
			$DTOPath = sprintf(
				'%s/Application/DTO/%s.php',
				$modulePath,
				$DTOClassDetails->getShortName()
			);

			if ($this->checkFileExists($DTOPath)){
				$io->warning(
					sprintf(
						'The file "%s" already exists',
						$DTOPath
					)
				);
				continue;
			}

			$generator->generateFile(
				$DTOPath,
				$config['template'],
				[
					'namespace'         => $DTONamespace, 'class_name' => $DTOClassDetails->getShortName(),
					'entity_full_class' => self::PREFIX_DIR . $entityClassDetails->getFullName(),
					'entity_class_name' => $entityShort, 'attributes' => $attributes,
					'model'             => $this->getFullDomainName(
						$entityShort,
						$this->baseDir
					), 'context'        => $this->baseDir,
				]
			);
		}


		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}


}
