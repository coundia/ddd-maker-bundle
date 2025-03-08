<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\Cqrs;

use Cnd\DddMakerBundle\Maker\Core\BaseMaker;
use Cnd\DddMakerBundle\Maker\Core\Common;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MakeControllerForCommandCqrsFromEntity extends BaseMaker{

	public static function getCommandName(): string{
		return 'make:ddd-controller-command-from-entity';
	}

	public static function getCommandDescription(): string{
		return 'Generates Presentation controllers for commands for a given entity.';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
		$command->addArgument(
			'entity',
			InputArgument::REQUIRED,
			'The FQCN of the entity '
		)->addOption(
			'force',
			null,
			InputOption::VALUE_OPTIONAL,
			'Overwrite existing files',
			true
		);

		$command->addArgument('action', InputArgument::OPTIONAL, 'Name of the command action','Create');
	}
	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{
		$entity = trim($input->getArgument('entity'));
		$this->force = $input->getOption('force') == 'true' ? true : false;

		$action = ucfirst($input->getArgument('action'));

		$entity = $this->resolveEntityNamespace($entity);

		$entityClassDetails = $generator->createClassNameDetails(
			$entity,
			'Domain\\Entity\\',
			''
		);
		$entityShort = $entityClassDetails->getShortName();

		$moduleName = $this->baseDir;
		$modulePath = sprintf(
			'src/%s',
			$moduleName
		);
		$controllerNamespace = sprintf(
			'App\\%s\\Presentation\\Controller',
			$moduleName
		);

		$api_version_name = Common::VERSION_API;
		$api_version = $api_version_name . '/';

		$serviceNamespace = $this->getUseCaseNamespace($this->baseDir);

		$applicationNamespace = $this->getApplicationNamespace(
			$entityShort,
			$this->baseDir
		);

		$DTONamespace = $this->getDTONamespace(
			$entityShort,
			$this->baseDir
		);

		$controllers = [
			[
				'template'  => Common::RESOURCE_DIR . '/Aggregate/Presentation/Controller/CqrsEntityCreateController.tpl.php',
				'variables' => [
					'namespace'         => $controllerNamespace,
					'class_name' => $action.$entityShort . 'Controller',
					'route_path'        => '/api/' . $api_version . strtolower($entityShort) . 's/'.strtolower($action)."/",
					'route_name'        => 'api_command_' . $api_version_name . '_' . strtolower($entityShort) . '_'.strtolower($action),
					'entity_class_name' => $entityShort, 'serializer_interface' => 'SerializerInterface',
					'attributes' => $this->getEntityAttributes($entityClassDetails->getFullName()),
					'command_name' => $applicationNamespace.'\\Command\\'.$action.$entityShort.'Command',
				],
			]
		];

		foreach ($controllers as $controller){
			$targetPath = sprintf(
				'%s/Presentation/Controller/%s.php',
				$modulePath,
				$controller['variables']['class_name']
			);
			if ($this->checkFileExists($targetPath)){
				$io->warning(
					sprintf(
						'File "%s" already exists. Skipping generation.',
						$targetPath
					)
				);
				continue;
			}

			$variables = $controller['variables'];

			$variables["context"] = $moduleName;
			$variables["DTONamespace"] = $DTONamespace;
			$variables["serviceNamespace"] = $serviceNamespace;

			$variables["command_interface"] = '\App\Shared\Application\Bus\CommandBus';

			$variables["parent_class_name"] = "\\".Common::SHARED_PRESENTATION_APP.'\Controller\BaseController';

			$variables['entity_full_class_id'] = $this->getModelObjectValueId(
				$entityShort,
				$this->baseDir
			);

			$this->generateFile(
				$generator,
				$targetPath,
				$controller['template'],
				$variables
			);
		}

		$routeName = $entityShort . 's_routes';
		$routeConfiguration =
			"\n{$routeName}:\n    resource: '../src/{$this->baseDir}/Presentation/Controller/'\n    type: attribute\n";
		$routesFile = $generator->getRootDirectory() . '/config/routes.yaml';
		if (!file_exists($routesFile)){
			file_put_contents($routesFile,$routeConfiguration);
		}

		if (file_exists($routesFile)){
			$currentRoutes = file_get_contents($routesFile);
			if (strpos(
					$currentRoutes,
					"{$routeName}:"
				) === false){
				file_put_contents(
					$routesFile,
					$routeConfiguration,
					FILE_APPEND
				);
			}
		}

		(new MakeTestsCommandControllersFromEntity($this->baseDir))->generate(
			$input,
			$io,
			$generator
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


}
