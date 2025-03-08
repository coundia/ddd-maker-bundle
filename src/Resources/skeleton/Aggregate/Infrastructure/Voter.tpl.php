<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Security\Domain\Entity\User;
use <?= $entity_full_class ?>;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
* Manages <?= $entity_class_name ?> permissions.
* Allows owners and admins to edit, delete, and list <?= $entity_class_name ?> entities.
*/
class <?= $class_name ?> extends Voter
{
protected function supports(string $attribute, mixed $subject): bool
{
return in_array($attribute, ['EDIT', 'DELETE', 'LIST']) && $subject instanceof <?= $entity_class_name ?>;
}

protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
{


if (!property_exists($subject, 'owner')) {
return true;
}

$user = $token->getUser();
if (!$user instanceof User) {
return false;
}
if (in_array('ROLE_ADMIN', $user->getRoles())) {
return true;
}

if ($subject->owner && $subject->owner->getUserIdentifier() === $user->getUserIdentifier()) {
return true;
}
return false;
}
}
