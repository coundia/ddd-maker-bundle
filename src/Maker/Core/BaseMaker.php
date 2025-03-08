<?php

declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\Core;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;

/**
 * Class MakeCrudFromEntity
 *
 * Generates a complete CRUD structure.
 */
abstract class BaseMaker extends AbstractMaker{

	public function __construct(
		public string $baseDir = "Core",
		public bool $force = true
	){
	}

	public static function getFullDomainName(
		string $entityShort,
		string $context = 'Core'
	): string{
		return sprintf(
				'\App\\%s\\Domain\Aggregate\\%s',
				$context,
				ucfirst($entityShort)
			) . 'Model';
	}

	public function getModel(
		string $entityShort
	): string{
		return sprintf(
				'\App\\%s\\Domain\Aggregate\\%s',
				$this->baseDir,
				ucfirst($entityShort)
			) . 'Model';
	}

	public static function getModelObjectValueId(
		string $entityShort,
		string $context = 'Core'
	): string{
		return sprintf(
				'\App\\%s\\Domain\ValueObject\\%s',
				$context,
				ucfirst($entityShort)
			) . 'Id';
	}

	public static function getDTONamespace(
		string $entityShort,
		string $context = 'Core'
	): string{
		return sprintf(
			'\App\\%s\\Application\DTO\\%s',
			$context,
			ucfirst($entityShort)
		);
	}

	public static function getApplicationNamespace(
		string $entityShort,
		string $context = 'Core'
	): string{
		return sprintf(
			'\App\\%s\\Application',
			$context
		);
	}

	public static function getServiceNamespace(
		string $entityShort,
		string $context = 'Core'
	): string{
		return sprintf(
			'\App\\%s\\Application\Service\\%s',
			$context,
			ucfirst($entityShort)
		);
	}

	public static function getUseCase(
		string $name,
		string $entityShort,
		string $context = 'Core'
	): string{
		return sprintf(
				'\App\\%s\\Domain\UseCase\\%s',
				$context,
				ucfirst($entityShort)
			) . $name;
	}

	public static function getUseCaseNamespace(string $context = 'Core'): string{
		return sprintf(
			'\App\\%s\\Domain\UseCase',
			$context
		);
	}

	public function configureDependencies(DependencyBuilder $dependencies): void{
		$dependencies->addClassDependency(
			Command::class,
			'symfony/console'
		);
	}

	public function extractNamespace(string $fullClassName): string{
		return substr(
			$fullClassName,
			0,
			strrpos(
				$fullClassName,
				'\\'
			)
		);
	}

	public function getEntityAllAttributes(string $entityClass): array{
		$reflection = new ReflectionClass($entityClass);
		$properties = $reflection->getProperties(
			ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC
		);

		return array_map(function (
			ReflectionProperty $property
		) use
		(
			$entityClass,
			$reflection
		){
			$type = $property->getType();
			$typeName = $type instanceof ReflectionNamedType ? $type->getName() : 'string';
			$name = $property->getName();
			$isId = in_array(
				strtolower($name),
				['id', 'identity'],
				true
			);

			$isValueObject = in_array(
				$typeName,
				EntityAttribute::$primitiveTypes,
				true
			);

			return new EntityAttribute(
				entity: $entityClass,
				name: $name,
				type: $typeName,
				isId: $isId,
				isValueObject: $isValueObject,
				isOnConstrutor: false,
				entityName: $reflection->getShortName(),
				context: $this->baseDir
			);
		},
			$properties);
	}

	public function validate(string $entityClass): void{

		$entityClass = $this->resolveEntityNamespace($entityClass);

		if (!class_exists($entityClass)){
			throw new InvalidArgumentException("The entity class \"$entityClass\" does not exist.");
		}

		$reflection = new ReflectionClass($entityClass);

		$allAttributes = $this->getEntityAllAttributes($entityClass);

		if (count($allAttributes) === 0){
			throw new InvalidArgumentException("The entity class \"$entityClass\" must have at least one attribute in the constructor.");
		}

		if (!$reflection->isInstantiable()){
			throw new InvalidArgumentException("The entity class \"$entityClass\" cannot be instantiated.");
		}

		if (!class_exists($entityClass)){
			throw new InvalidArgumentException("The entity class \"$entityClass\" does not exist.");
		}

		$reflection = new ReflectionClass($entityClass);

		if (!$reflection->isInstantiable()){
			throw new InvalidArgumentException("The entity class \"$entityClass\" cannot be instantiated.");
		}

		$constructor = $reflection->getConstructor();
		if ($constructor && !$constructor->isPublic()){
			throw new InvalidArgumentException("The constructor of entity class \"$entityClass\" must be public.");
		}

		if (!$reflection->hasMethod('getId')){
			throw new InvalidArgumentException("The entity class \"$entityClass\" must have a public getId() method.");
		}

		$idMethod = $reflection->getMethod('getId');

		if (!$idMethod->isPublic()){
			throw new InvalidArgumentException("The getId() method of entity class \"$entityClass\" must be public.");
		}

		$returnType = $idMethod->getReturnType();
		if (!$returnType || !$returnType instanceof \ReflectionNamedType || !in_array($returnType->getName(),
				['string', 'int'],
				true)){
			throw new InvalidArgumentException("The getId() method of entity class \"$entityClass\" must return a string or an int.");
		}
	}

	public function resolveEntityNamespace(string $entityNameParams): ?string{
		if (class_exists($entityNameParams)){
			return $entityNameParams;
		}

		$entityName = ucfirst($entityNameParams);

		foreach (Common::possibleNamespaces as $namespace){
			$fullClassName = $namespace . '\\' . $entityName;
			if (class_exists($fullClassName)){
				return $fullClassName;
			}
		}

		throw new InvalidArgumentException("The entity class \"$entityNameParams\" does not exist.");
	}


	public function getEntityAttributes(string $entityClass): array{
		$reflection = new ReflectionClass($entityClass);
		$constructor = $reflection->getConstructor();

		if (!$constructor){
			return [];
		}

		$attributes = [];

		foreach ($constructor->getParameters() as $param){
			$type = $param->getType();
			$typeName = $type instanceof ReflectionNamedType ? $type->getName() : 'mixed';

			$name = $param->getName();
			$isId = strtolower($name) === 'id';

			$attributes[] = new EntityAttribute(
				entity: $entityClass,
				name: $name,
				type: $typeName,
				isId: $isId,
				isValueObject: false,
				isOnConstrutor: true,
				entityName: $reflection->getShortName(),
				context: $this->baseDir

			);
		}

		if (!array_filter(
			$attributes,
			fn(
				EntityAttribute $attr
			) => $attr->isId
		)){
			$idProperty = $reflection->hasProperty('id') ? $reflection->getProperty('id') : null;

			$attributes[] = new EntityAttribute(
				entity: $entityClass,
				name: 'id',
				type: $idProperty ? $idProperty->getType()
					?->getName() ?? 'Ramsey\Uuid\Uuid' : 'Ramsey\Uuid\Uuid',
				isId: true,
				isValueObject: true,
				isOnConstrutor: false,
				entityName: $reflection->getShortName(),
				context: $this->baseDir
			);
		}


		return $attributes;
	}


	public function hasMethod(
		string $className,
		string $methodName
	): bool{
		if (!class_exists($className)){
			throw new InvalidArgumentException(
				sprintf(
					'The class "%s" does not exist.',
					$className
				)
			);
		}

		$reflection = new ReflectionClass($className);
		return $reflection->hasMethod($methodName);
	}


	public function checkFileExists(
		string $path
	): bool{

		if (file_exists($path) && $this->force){
			unlink($path);
			return false;
		}

		return file_exists($path);
	}
}
