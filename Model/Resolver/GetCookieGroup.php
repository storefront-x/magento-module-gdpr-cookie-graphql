<?php

declare(strict_types=1);

namespace StorefrontX\GdprCookieGraphQl\Model\Resolver;

use Amasty\GdprCookie\Model\CookieGroupFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\ResourceConnection;

class GetCookieGroup implements ResolverInterface
{

    /**
     * @var CookieGroupFactory
     */
    private $cookieGroupFactory;

    private $_resourceConnection;
    private $_connection;

    private $DB_TABLE_GROUP = 'amasty_gdprcookie_group';
    private $DB_TABLE_GROUP_STORE_DATA = 'amasty_gdprcookie_group_store_data';

    public function __construct(
        CookieGroupFactory $cookieGroupFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->cookieGroupFactory = $cookieGroupFactory;
        $this->_resourceConnection = $resourceConnection;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $this->_connection = $this->_resourceConnection->getConnection();

        $query = "SELECT id, name, description, is_essential, is_enabled
        FROM $this->DB_TABLE_GROUP WHERE (is_enabled = 1)
        AND id NOT IN
        (SELECT group_id FROM $this->DB_TABLE_GROUP_STORE_DATA WHERE (store_id = '.$storeId.'
        AND is_enabled = 0))";

        $collection = $this->_connection->fetchAll($query);

        return $collection;

    }
}
