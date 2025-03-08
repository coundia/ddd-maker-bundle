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

class MakeControllerForQueryCqrsFromEntity extends BaseMaker{

	public static function getCommandName(): string{
		return 'make:ddd-controller-query-from-entity';
	}

	public static function getCommandDescription(): string{
		return 'Generates Presentation controllers for query for a given entity.';
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

		$command->addArgument('query', InputArgument::OPTIONAL, 'Name of the query action',"Find");
		$command->addArgument('parameter', InputArgument::OPTIONAL, 'Find by parameter','id');

	}
	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{
		$entity = trim($input->getArgument('entity'));
		$this->force = $input->getOption('force') == 'true' ? true : false;
		$action = ucfirst($input->getArgument('query'));
		$parameter = ucfirst($input->getArgument('parameter'));
		$action  = ucfirst($action)."By".ucfirst($parameter);

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
				'template'  => Common::RESOURCE_DIR . '/Aggregate/Presentation/Controller/CqrsEntityListController.tpl.php',
				'variables' => [
					'namespace'         => $controllerNamespace,
					'class_name' => $action.$entityShort . 'Controller',
					'route_path'        => '/api/' . $api_version . strtolower($entityShort) . 's/'.strtolower($action),
					'route_name'        => 'api_' . $api_version_name . '_' . strtolower($entityShort) . '_'.strtolower($action),
					'entity_class_name' => $entityShort, 'serializer_interface' => 'SerializerInterface',
					'attributes' => $this->getEntityAttributes($entityClassDetails->getFullName()),
					'query_name' => $applicationNamespace.'\\Query\\'.$action.$entityShort.'Query',
					'parameter' => $parameter,
					'entity_model_class' => $this->getModel($entityShort),
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

			$variables["query_interface"] = '\App\Shared\Application\Bus\QueryBus';

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


		(new MakeTestsQueryControllersFromEntity($this->baseDir))->generate(
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
