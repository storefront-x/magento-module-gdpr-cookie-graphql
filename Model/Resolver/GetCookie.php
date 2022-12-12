<?php

declare(strict_types=1);

namespace StorefrontX\GdprCookieGraphQl\Model\Resolver;

use Amasty\GdprCookie\Model\CookieFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\ResourceConnection;

class GetCookie implements ResolverInterface
{

    /**
     * @var CookieFactory
     */
    private $cookieFactory;

    private $_resourceConnection;
    private $_connection;

    private $DB_TABLE_COOKIE = 'amasty_gdprcookie_cookie';
    private $DB_TABLE_COOKIE_STORE_DATA = 'amasty_gdprcookie_cookie_store_data';

    public function __construct(
        CookieFactory $cookieFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->cookieFactory = $cookieFactory;
        $this->_resourceConnection = $resourceConnection;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $this->_connection = $this->_resourceConnection->getConnection();

        $query = "SELECT id, name, description, provider, lifetime, type, is_enabled
        FROM $this->DB_TABLE_COOKIE WHERE (is_enabled = 1 AND group_id = ".$value['id'].")
        AND id NOT IN
        (SELECT cookie_id FROM $this->DB_TABLE_COOKIE_STORE_DATA WHERE store_id = ".$storeId."
        AND is_enabled = 0)";

        $collection = $this->_connection->fetchAll($query);

        return $collection;
    }
}
