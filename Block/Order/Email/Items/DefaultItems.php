<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kento\CustomInvoiceEmail\Block\Order\Email\Items;

use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Sales Order Email items default renderer
 *
 * @api
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class DefaultItems extends \Magento\Framework\View\Element\Template
{


    
    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getItem()->getOrder();
    }

    /**
     * @return array
     */
    public function getItemOptions($item)
    {
        if( $item->getSku()=='106891'){

            $orderItemId =$item->getData('order_item_id');

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderItemRepository = $objectManager->create('\Magento\Sales\Api\OrderItemRepositoryInterface');
            $searchCriteriaBuilder = $objectManager->create('\Magento\Framework\Api\SearchCriteriaBuilder');
            $filterBuilder = $objectManager->create('\Magento\Framework\Api\FilterBuilder');
            $filterGroupBuilder = $objectManager->create('\Magento\Framework\Api\Search\FilterGroupBuilder');

            $searchCriteriaBuilder->addFilter('item_id', $orderItemId , 'eq');
            $collection = $orderItemRepository->getList(
                $searchCriteriaBuilder->create()
            );
            $productOptions=($collection->getData()[0]['product_options']);
            $decodedResult=json_decode($productOptions);

            return $decodedResult->info_buyRequest->options;
        }
        else
            return null;
    }


    /**
     * @param string|array $value
     * @return string
     */
    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf(
                '%d',
                $value['qty']
            ) . ' x ' . $this->escapeHtml(
                $value['title']
            ) . " " . $this->getItem()->getOrder()->formatPrice(
                $value['price']
            );
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }
    // public function getId($item)
    // {

    //         return $item->getData();

    // }


    /**
     * Return product additional information block
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Get the html for item price
     *
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @return string
     */
    public function getItemPrice($item)
    {
        $block = $this->getLayout()->getBlock('item_price');
        $block->setItem($item);
        return $block->toHtml();
    }
}
