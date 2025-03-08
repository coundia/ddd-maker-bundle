<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Core\Domain\Aggregate\<?= $entity_class_name ?>Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
* Class <?= $class_name ?>
* abstract Transfer Object for <?= $entity_class_name ?>.
*/
abstract class <?= $entity_class_name ?>DTO
{
public function __construct(
<?php foreach ($attributes as $attribute): ?>
	#[Groups(['default'])]
	<?php if ($attribute->isEntity()): ?>
	public ?string $<?= $attribute->getName() ?>,
	<?php continue; endif; ?>
	public ?<?= $attribute->getType() ?> $<?= $attribute->getName() ?>,
<?php endforeach; ?>
) {}

}