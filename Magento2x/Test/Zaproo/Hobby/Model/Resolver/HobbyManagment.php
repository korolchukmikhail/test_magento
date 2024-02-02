<?php
declare(strict_types=1);

namespace Zaproo\Hobby\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Config\Element\Type;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Zaproo\Hobby\Model\Hobby;

class HobbyManagment implements ResolverInterface
{
    public function __construct(
        protected Hobby $hobbyModel
    ) {
    }

    public function resolve(Type|Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): bool
    {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The request is allowed for logged in customer'));
        }

        $hobbies = $this->hobbyModel->getHobbies();

        if (!isset($args['input']) || !in_array($args['input'], $hobbies)) {
            throw new GraphQlAuthorizationException(__('Provided incorrect value.'));
        }

        $hobbyId = (int) array_search($args['input'], $hobbies);

        $this->hobbyModel->saveHobby($hobbyId, $context->getUserId());

        return true;
    }
}
