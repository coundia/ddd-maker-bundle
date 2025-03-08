<?php

declare(strict_types=1);

namespace Cnd\DddMakerBundle\Maker\Core;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use ReflectionClass;

final class EntityAttribute{
	public const TYPE_STRING = 'string';
	public const TYPE_INT = 'int';
	public const TYPE_FLOAT = 'float';
	public const TYPE_BOOL = 'bool';
	public const TYPE_DATE = 'date';
	public const TYPE_DATETIME = 'datetime';
	public const TYPE_ARRAY = 'array';
	public const TYPE_OBJECT = 'object';
	public const TYPE_UUID = 'uuid';
	public const dateDefaultType = '\DateTimeImmutable';
	public const dateInterface = '\DateTimeInterface';

	public static array $primitiveTypes = [
		self::TYPE_STRING, self::TYPE_INT, self::TYPE_FLOAT, self::TYPE_BOOL, self::TYPE_DATE, self::TYPE_DATETIME,
		DateTimeImmutable::class, DateTime::class, DateTimeInterface::class,
	];

	public static array $uuid = [
		self::TYPE_UUID, 'Ramsey\Uuid\Uuid', '\Ramsey\Uuid\Uuid', '\Symfony\Component\Uid\Uuid',
		'Symfony\Component\Uid\Uuid',
	];

	public static array $dateTimeTypes = [
		self::TYPE_DATE, self::TYPE_DATETIME, DateTimeImmutable::class, DateTime::class, DateTimeInterface::class,
	];

	public static array $exclu_to_value_objects = [];

	public function __construct(
		public string $entity,
		public string $name,
		public string $type,
		public bool $isId,
		private bool $isValueObject,
		public bool $isOnConstrutor,
		public string $entityName = "",
		public string $context = "Core",
	){

		if (in_array(
			$this->type,
			self::$dateTimeTypes,
			true
		)){
			$this->type = self::dateDefaultType;
		}
	}

	public function isTypeString(): bool{
		return $this->type === self::TYPE_STRING;
	}

	public function getType(string $type=""): string{

		if(empty($type)){
			$type = $this->type;
		}

		if (in_array(
			$type,
			self::$primitiveTypes,
			true
		)){
			return $type;
		}

		if($this->isTypeDate()){
			return self::dateDefaultType;
		}

		return '\\' . $type;
	}

	public function isPrimitiveType(): bool{
		return in_array(
				$this->type,
				self::$primitiveTypes,
				true
			) || $this->isTypeDate();
	}

	public function isTypeDate(): bool{
		return $this->type === self::dateDefaultType;
	}

	public function isTypeInt(): bool{
		return $this->type === self::TYPE_INT;
	}

	public function isTypeFloat(): bool{
		return $this->type === self::TYPE_FLOAT;
	}

	public function isTypeBool(): bool{
		return $this->type === self::TYPE_BOOL;
	}

	public function getInterface(): string{
		if ($this->isTypeDate()){
			return self::dateInterface;
		}

		return $this->type;
	}

	public function isTypeArray(): bool{
		return $this->type === self::TYPE_ARRAY;
	}

	public function isTypeObject(): bool{
		return $this->type === self::TYPE_OBJECT;
	}

	public function isTypeUuid(mixed $type): bool{
		return in_array(
			$type,
			self::$uuid,
			true
		);
	}

	public function isValidUuid(string $value): bool{
		return Uuid::isValid($value);
	}

	public function isValid(): bool{
		return in_array(
				$this->type,
				self::$primitiveTypes,
				true
			) || $this->isTypeDate();
	}

	public function isPrimaryKey(): bool{
		return $this->isId;
	}

	/**
	 * @param string $type
	 * @param string $class
	 * @param $classId
	 * @param bool $isDate
	 * @return string
	 * @throws \DateMalformedStringException
	 */
	public function generateFakerValue(string $type,string $class = "",$classId="->getId()",bool $isDate = false): string{

		if($this->isEntity()){
			return "\App\\".$this->context."\Infrastructure\Factory\\". ucfirst($this->name)."Factory::createOne()".$classId;
		}

		$date = $class."faker()->date('Y-m-d H:i:s')";

		if($isDate){
			$date = "new \DateTimeImmutable($date)";
		}
		return match ($type) {
			'string' => $class."faker()->sentence()",
			'int', 'integer' => $class."faker()->numberBetween(1, 1000)",
			'float', 'double' => $class."faker()->randomFloat(2, 0, 1000)",
			'bool', 'boolean' => $class."faker()->boolean()",
			'DateTimeImmutable', '\DateTimeImmutable', 'DateTime', 'DateTimeInterface' => $date,
			'uuid', 'Uuid', 'Ramsey\Uuid\Uuid' => $class."faker()->uuid()",
			default => "null",
		};
	}

	public function getSetterMethod(string $name): ?string{
		$reflection = new ReflectionClass($this->entity);

		$methodVariants = [
			'set' . ucfirst($name), 'update' . ucfirst($name), 'with' . ucfirst($name), 'change' . ucfirst($name),
			'replace' . ucfirst($name), 'assign' . ucfirst($name), 'define' . ucfirst($name), lcfirst($name),
		];

		foreach ($methodVariants as $method){
			if ($reflection->hasMethod($method)){
				return $method;
			}
		}

		return null;
	}

	public function getGetterMethod(string $name): ?string{
		$reflection = new ReflectionClass($this->entity);

		$methodVariants = [
			'get' . ucfirst($name), 'is' . ucfirst($name), 'has' . ucfirst($name), 'fetch' . ucfirst($name),
			'retrieve' . ucfirst($name), lcfirst($name),
		];

		foreach ($methodVariants as $method){
			if ($reflection->hasMethod($method)){
				return $method."()";
			}
		}

		return lcfirst($name);
	}

	public function isEntity(): bool{
		return !in_array(
				$this->type,
				self::$primitiveTypes,
				true
			) &&
			class_exists($this->type) &&
			!$this->isId()  &&
			!$this->isTypeDate();
	}

	public function isId(): bool{
		return $this->isId || strtolower($this->name) == 'id';
	}

	public function isValueObject(): bool{
		return !in_array(
			$this->type,
			self::$exclu_to_value_objects,
			true
		);
	}

	public function getObjectValue(string $name): string{
		return "\App\\" . $this->context . "\Domain\ValueObject\\" . $this->entityName . ucfirst($name);
	}

	public function  getTypeName(): string{
		return $this->type;
	}

	public function getName(): string{
		return $this->name;
	}
}
