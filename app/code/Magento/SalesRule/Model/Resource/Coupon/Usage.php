<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRule\Model\Resource\Coupon;

/**
 * SalesRule Model Resource Coupon_Usage
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Usage extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('salesrule_coupon_usage', 'coupon_id');
    }

    /**
     * Increment times_used counter
     *
     * @param int $customerId
     * @param mixed $couponId
     * @return void
     */
    public function updateCustomerCouponTimesUsed($customerId, $couponId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select();
        $select->from(
            $this->getMainTable(),
            ['times_used']
        )->where(
            'coupon_id = :coupon_id'
        )->where(
            'customer_id = :customer_id'
        );

        $timesUsed = $adapter->fetchOne($select, [':coupon_id' => $couponId, ':customer_id' => $customerId]);

        if ($timesUsed > 0) {
            $this->getConnection()->update(
                $this->getMainTable(),
                ['times_used' => $timesUsed + 1],
                ['coupon_id = ?' => $couponId, 'customer_id = ?' => $customerId]
            );
        } else {
            $this->getConnection()->insert(
                $this->getMainTable(),
                ['coupon_id' => $couponId, 'customer_id' => $customerId, 'times_used' => 1]
            );
        }
    }

    /**
     * Load an object by customer_id & coupon_id
     *
     * @param \Magento\Framework\Object $object
     * @param int $customerId
     * @param mixed $couponId
     * @return $this
     */
    public function loadByCustomerCoupon(\Magento\Framework\Object $object, $customerId, $couponId)
    {
        $adapter = $this->getConnection();
        if ($adapter && $couponId && $customerId) {
            $select = $adapter->select()->from(
                $this->getMainTable()
            )->where(
                'customer_id =:customet_id'
            )->where(
                'coupon_id = :coupon_id'
            );
            $data = $adapter->fetchRow($select, [':coupon_id' => $couponId, ':customet_id' => $customerId]);
            if ($data) {
                $object->setData($data);
            }
        }
        if ($object instanceof \Magento\Framework\Model\AbstractModel) {
            $this->_afterLoad($object);
        }
        return $this;
    }
}
