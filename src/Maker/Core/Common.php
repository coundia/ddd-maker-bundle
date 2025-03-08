<?php declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\Core;


final class Common{
	public const VERSION_API = 'v1';
	public const RESOURCE_DIR = __DIR__ . '/../../Resources/skeleton';
	public const SHARED_DOMAIN_SRC = '/src/Shared/Domain';
	public const SHARED_DOMAIN_APP = 'App\Shared\Domain';
	public const SHARED_INFRA_SRC = '/src/Shared/Infrastructure';
	public const SHARED_INFRA_APP = 'App\Shared\Infrastructure';
	public const SHARED_APPLICATION_SRC = '/src/Shared/Application';
	public const SHARED_APPLICATION_APP = 'App\Shared\Application';
	public const SHARED_PRESENTATION_SRC = '/src/Shared/Presentation';
	public const SHARED_PRESENTATION_APP = 'App\Shared\Presentation';

	public const APPLICATION_TPL_DIR = self::RESOURCE_DIR . '/Aggregate/Application/';

	public const possibleNamespaces = [
			'App\\Entity',
			'App\\Core\\Domain\\Entity',
			'App\Core\Infrastructure\Entity',
			'App\Infrastructure\Entity',
			'App\\Domain\\Entity',
			'\\Entity',
			'\\App',
			'\\',
		];
}
