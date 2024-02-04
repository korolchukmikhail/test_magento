<?php
declare(strict_types=1);

namespace Zaproo\Hobby2\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Config\Element\Type;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Zaproo\Hobby2\Model\Hobby;

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
        $args['hobby'] = ucfirst(strtolower($args['hobby']));

        if (!isset($args['hobby']) || !in_array($args['hobby'], $hobbies)) {
            throw new GraphQlAuthorizationException(__('Provided incorrect value. Possible values:') . ' ' . implode($hobbies, ','));
        }

        $hobbyId = (int) array_search($args['hobby'], $hobbies);

        $this->hobbyModel->saveUserHobby($hobbyId, $context->getUserId());

        return true;
    }
}
