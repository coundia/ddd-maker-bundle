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
 * Class MakeRepositoryFromEntity
 * Generates a repository and repository interface for an entity.
 */
class MakeRepositoryFromEntity extends BaseMaker{
	private const PREFIX_DIR = '\\';

	public static function getCommandName(): string{
		return 'make:ddd-repository';
	}

	public static function getCommandDescription(): string{
		return 'Generates a repository and repository interface for an entity in the Infrastructure/Persistence directory.';
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
		$namespaceBase = 'App\\' . $context;
		$pathBase = 'src/' . $context;

		$files = [
			[
				'class_name' => $entityShort . 'RepositoryInterface',
				'namespace'  => $namespaceBase . '\\Domain\\Repository',
				'path' => $pathBase . '/Domain/Repository',
				'template'   => Common::RESOURCE_DIR . '/Aggregate/Domain/Repository/DomainRepositoryInterface.tpl.php',
			], [
				'class_name' => $entityShort . 'Repository',
				'namespace'  => $namespaceBase . '\\Infrastructure\\Persistence',
				'path'       => $pathBase . '/Infrastructure/Persistence',
				'template'   => Common::RESOURCE_DIR . '/Aggregate/Infrastructure/Repository/DomainRepository.tpl.php',
			],
		];

		foreach ($files as $file){

			$targetPath = sprintf(
				'%s/%s.php',
				$file['path'],
				$file['class_name']
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

			$generator->generateFile(
				$targetPath,
				$file['template'],
				[
					'context' => $context,
					'namespace' => $file['namespace'], 'entity_class_name' => $entityShort,
					'repository_interface' => self::PREFIX_DIR . $namespaceBase . '\\Domain\\Repository\\' .
						$entityShort . 'RepositoryInterface', 'model' => $this->getFullDomainName(
					$entityShort,
					$context
				), 'entity_full_class_id' => $this->getModelObjectValueId(
					$entityShort,
					$context
				), 'entity_full_class' => self::PREFIX_DIR . $entityClassDetails->getFullName(),
					'attributes' => $this->getEntityAttributes($entityClassDetails->getFullName()),
					'DTOResponse' => $this->getDTONamespace(
							$entityShort,
							$this->baseDir
						) . 'ResponseDTO',
				]
			);
		}

		$generator->writeChanges();
		$io->success('Repository and interface successfully generated.');
	}
}
