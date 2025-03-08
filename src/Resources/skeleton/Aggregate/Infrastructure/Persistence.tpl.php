<?= "<?php\n" ?>
declare(strict_types=1);

return [
'<?= $entity_full_class ?>' => [
'type' => 'entity',
'table' => '<?= strtolower($entity_class_name) ?>s',
'id' => [
'id' => [
'type' => 'uuid',
'generator' => ['strategy' => 'NONE'],
],
],
'fields' => [
<?php foreach ($attributes as $attribute): ?>
	'<?= $attribute->getName() ?>' => [
	'type' => '<?= $attribute->getType() ?>',
	<?php if ($attribute->getType() === 'string'): ?>'length' => 255,<?php endif; ?>
	],
<?php endforeach; ?>
],
],
];
