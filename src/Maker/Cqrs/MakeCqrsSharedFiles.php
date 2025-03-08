<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\Cqrs;

use Cnd\DddMakerBundle\Maker\Core\BaseMaker;
use Cnd\DddMakerBundle\Maker\Core\Common;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * MakeSetupCrudSharedFiles.
 * Generates shared ddd files for Application, Domain, Infrastructure, Tests and UI sections.
 */
class MakeCqrsSharedFiles extends BaseMaker{
	public static function getCommandName(): string{
		return 'make:ddd-setup';
	}

	public static function getCommandDescription(): string{
		return 'Generates shared ddd files for Application, Domain, Infrastructure, Tests and UI sections for CQRS.';
	}

	public function configureCommand(
		Command $command,
		InputConfiguration $inputConfig
	): void{
	}

	public function generate(
		InputInterface $input,
		ConsoleStyle $io,
		Generator $generator
	): void{
		$files = [
			// Domain DTOs
			[
				'target'     => Common::SHARED_DOMAIN_SRC.'/DTO/ApiResponseDTO.php', 'namespace' => Common::SHARED_DOMAIN_APP.'\DTO',
				'class_name' => 'ApiResponseDTO',
				'template'   => Common::RESOURCE_DIR . '/Shared/Domain/DTO/ApiResponseDTO.tpl.php',
			], [
				'target'     => Common::SHARED_DOMAIN_SRC.'/DTO/ErrorResponseDTO.php', 'namespace' => Common::SHARED_DOMAIN_APP.'\DTO',
				'class_name' => 'ErrorResponseDTO',
				'template'   => Common::RESOURCE_DIR . '/Shared/Domain/DTO/ErrorResponseDTO.tpl.php',
			], [
				'target'    => Common::SHARED_DOMAIN_SRC.'/DTO/ValidationErrorResponseDTO.php',
				'namespace' => Common::SHARED_DOMAIN_APP.'\DTO', 'class_name' => 'ValidationErrorResponseDTO',
				'template'  => Common::RESOURCE_DIR . '/Shared/Domain/DTO/ValidationErrorResponseDTO.tpl.php',
			], // Domain Response files
			[
				'target'     => Common::SHARED_DOMAIN_SRC.'/Response/Response.php', 'namespace' => Common::SHARED_DOMAIN_APP.'\Response',
				'class_name' => 'Response',
				'template'   => Common::RESOURCE_DIR . '/Shared/Domain/Response/Response.tpl.php',
			], [
				'target'    => Common::SHARED_DOMAIN_SRC.'/Response/ResponseAssert.php',
				'namespace' => Common::SHARED_DOMAIN_APP.'\Response', 'class_name' => 'ResponseAssert',
				'template'  => Common::RESOURCE_DIR . '/Shared/Domain/Response/ResponseAssert.tpl.php',
			], [
				'target'     => Common::SHARED_DOMAIN_SRC.'/Response/ResponseType.php',
				'namespace'  => Common::SHARED_DOMAIN_APP.'\Response', 'class_name' => 'ResponseType',
				'template'   => Common::RESOURCE_DIR . '/Shared/Domain/Response/ResponseType.tpl.php',
			], [
				'target'    => Common::SHARED_DOMAIN_SRC.'/Aggregate/AggregateRoot.php',
				'namespace' => Common::SHARED_DOMAIN_APP.'\Aggregate', 'class_name' => 'AggregateRoot',
				'template'  => Common::RESOURCE_DIR . '/Shared/Domain/Aggregate/AggregateRoot.tpl.php',
			],

			[
				'target'    => '/tests/Shared/Assertions/ResponseAssertions.php',
				'namespace' => 'App\Tests\Shared\Assertions', 'class_name' => 'ResponseAssertions',
				'template'  => Common::RESOURCE_DIR . '/tests/Shared/Assertions/ResponseAssertions.tpl.php',
			], [
				'target'     => '/tests/Shared/BaseWebTestCase.php', 'namespace' => 'App\Tests\Shared',
				'class_name' => 'BaseWebTestCase',
				'template'   => Common::RESOURCE_DIR . '/tests/Shared/BaseWebTestCase.tpl.php',
			], //events
			[
				'target'    => Common::SHARED_DOMAIN_SRC.'/Event/DomainEventInterface.php',
				'namespace' => Common::SHARED_DOMAIN_APP.'\Event', 'class_name' => 'DomainEventInterface',
				'template'  => Common::RESOURCE_DIR . '/Shared/Domain/Event/DomainEventInterface.tpl.php',
			], //extra
			[
				'target'    => Common::SHARED_DOMAIN_SRC.'/ValueObject/ValueObject.php',
				'namespace' => Common::SHARED_DOMAIN_APP.'\ValueObject', 'class_name' => 'DomainEventInterface',
				'template'  => Common::RESOURCE_DIR . '/Shared/Domain/ValueObject/ValueObject.tpl.php',
			],//Application EventListener
			[
				'target'    => Common::SHARED_APPLICATION_SRC.'/EventListener/EventListener.php',
				'namespace' => Common::SHARED_APPLICATION_APP.'\EventListener', 'class_name' => 'EventListener',
				'template'  => Common::RESOURCE_DIR . '/Shared/Application/EventListener/EventListener.tpl.php',
			], //Application EventDispatcher
			[
				'target'    => Common::SHARED_APPLICATION_SRC.'/EventDispatcher/EventDispatcher.php',
				'namespace' => Common::SHARED_APPLICATION_APP.'\EventDispatcher', 'class_name' => 'EventDispatcher',
				'template'  => Common::RESOURCE_DIR . '/Shared/Application/EventDispatcher/EventDispatcher.tpl.php',
			], //Application bus
			[
				'target'     => Common::SHARED_APPLICATION_SRC.'/Bus/CommandBus.php',
				'namespace'  => Common::SHARED_APPLICATION_APP.'\Bus', 'class_name' => 'CommandBus',
				'template'   => Common::RESOURCE_DIR . '/Shared/Application/Bus/CommandBus.tpl.php',
			], [
				'target'    => Common::SHARED_APPLICATION_SRC.'/Bus/CommandHandler.php',
				'namespace' => Common::SHARED_APPLICATION_APP.'\Bus', 'class_name' => 'CommandHandler',
				'template'  => Common::RESOURCE_DIR . '/Shared/Application/Bus/CommandHandler.tpl.php',
			], [
				'target'    => Common::SHARED_APPLICATION_SRC.'/Bus/MessageAsync.php',
				'namespace' => Common::SHARED_APPLICATION_APP.'\Bus', 'class_name' => 'MessageAsync',
				'template'  => Common::RESOURCE_DIR . '/Shared/Application/Bus/MessageAsync.tpl.php',
			], [
				'target'     => Common::SHARED_APPLICATION_SRC.'/Bus/MessageBus.php',
				'namespace'  => Common::SHARED_APPLICATION_APP.'\Bus', 'class_name' => 'MessageBus',
				'template'   => Common::RESOURCE_DIR . '/Shared/Application/Bus/MessageBus.tpl.php',
			], [
				'target'    => Common::SHARED_APPLICATION_SRC.'/Bus/MessageHandler.php',
				'namespace' => Common::SHARED_APPLICATION_APP.'\Bus', 'class_name' => 'MessageHandler',
				'template'  => Common::RESOURCE_DIR . '/Shared/Application/Bus/MessageHandler.tpl.php',
			], [
				'target'     => Common::SHARED_APPLICATION_SRC.'/Bus/QueryBus.php', 'namespace' => Common::SHARED_APPLICATION_APP.'\Bus',
				'class_name' => 'QueryBus',
				'template'   => Common::RESOURCE_DIR . '/Shared/Application/Bus/QueryBus.tpl.php',
			], [
				'target'     => Common::SHARED_APPLICATION_SRC.'/Bus/QueryHandler.php',
				'namespace'  => Common::SHARED_APPLICATION_APP.'\Bus', 'class_name' => 'QueryHandler',
				'template'   => Common::RESOURCE_DIR . '/Shared/Application/Bus/QueryHandler.tpl.php',
			], [
				'target'     => Common::SHARED_APPLICATION_SRC.'/Bus/QueryHandler.php',
				'namespace'  => Common::SHARED_APPLICATION_APP.'\Bus', 'class_name' => 'QueryHandler',
				'template'   => Common::RESOURCE_DIR . '/Shared/Application/Bus/QueryHandler.tpl.php',
			], [
				'target'     => Common::SHARED_APPLICATION_SRC.'/Mail/Mailer.php', 'namespace' => Common::SHARED_APPLICATION_APP.'\Mail',
				'class_name' => 'Mailer',
				'template'   => Common::RESOURCE_DIR . '/Shared/Application/Mail/Mailer.tpl.php',
			],
			[
				'target'    => Common::SHARED_APPLICATION_SRC.'/EventListener/ExceptionListener.php',
				'namespace' => Common::SHARED_APPLICATION_APP.'\EventListener', 'class_name' => 'ExceptionListener',
				'template'  => Common::RESOURCE_DIR . '/Shared/Application/EventListener/ExceptionListener.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\EventListener\ExceptionListener',
			],
			//Infrastructure EventListener
			[
				'target'    => Common::SHARED_INFRA_SRC.'/EventListener/ExceptionListener.php',
				'namespace' => Common::SHARED_INFRA_APP.'\EventListener',
				'class_name' => 'ExceptionListener',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/EventListener/ExceptionListener.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\EventListener\ExceptionListener',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/EventDispatcher/EventDispatcher.php',
				'namespace' => Common::SHARED_INFRA_APP.'\EventDispatcher', 'class_name' => 'EventDispatcher',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/EventDispatcher/EventDispatcher.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\EventDispatcher\EventDispatcher',
			], //Infrastructure bus
			[
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/CommandBus.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'CommandBus',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/CommandBus.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\CommandBus',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/CommandHandler.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'CommandHandler',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/CommandHandler.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\CommandHandler',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/MessageAsync.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'MessageAsync',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/MessageAsync.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\MessageAsync',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/MessageAsyncBus.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'MessageAsyncBus',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/MessageBus.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\MessageBus',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/MessageHandler.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'MessageHandler',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/MessageHandler.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\MessageHandler',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/QueryBus.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'QueryBus',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/QueryBus.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\QueryBus',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/QueryHandler.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'QueryHandler',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/QueryHandler.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\QueryHandler',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Bus/QueryHandler.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Bus', 'class_name' => 'QueryHandler',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Bus/QueryHandler.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Bus\QueryHandler',
			], [
				'target'    => Common::SHARED_INFRA_SRC.'/Mail/Mailer.php',
				'namespace' => Common::SHARED_INFRA_APP.'\Mail', 'class_name' => 'Mailer',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/Mail/Mailer.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\Mail\Mailer',
			],
			//Infrastructure EventListener
			[
				'target'    => Common::SHARED_INFRA_SRC.'/EventListener/EventListener.php',
				'namespace' => Common::SHARED_INFRA_APP.'\EventListener', 'class_name' => 'EventListener',
				'template'  => Common::RESOURCE_DIR . '/Shared/Infrastructure/EventListener/EventListener.tpl.php',
				'interface' => '\\'.Common::SHARED_APPLICATION_APP.'\EventListener\EventListener',
			],
			//controllers
			[
				'target'    => Common::SHARED_PRESENTATION_SRC.'/Controller/BaseController.php',
				'namespace' => Common::SHARED_PRESENTATION_APP.'\Controller',
				'class_name' => 'BaseController',
				'parent_class' => '\Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
				'template'  => Common::RESOURCE_DIR . '/Shared/Presentation/Controller/BaseController.tpl.php',
			]

		];

		foreach ($files as $file){
			$targetPath = $generator->getRootDirectory() . $file['target'];
			if ($this->checkFileExists($targetPath)){
				$io->warning(
					sprintf(
						'File "%s" already exists. Skipping generation.',
						$targetPath
					)
				);
				continue;
			}
			$dir = dirname($targetPath);
			if (!is_dir($dir)){
				mkdir(
					$dir,
					0777,
					true
				);
			}
			$generator->generateFile(
				$targetPath,
				$file['template'],
				[
					'namespace' => $file['namespace'],
					'class_name' => $file['class_name'],
					'parent_class' => $file['parent_class'] ?? null,
					'interface' => $file['interface'] ?? null,
					'api_version' => 'v1',
				]
			);
		}

		$generator->writeChanges();
		$this->writeSuccessMessage($io);
	}

	public function configureDependencies(DependencyBuilder $dependencies): void{
		$dependencies->addClassDependency(
			Command::class,
			'symfony/console'
		);
	}
}
