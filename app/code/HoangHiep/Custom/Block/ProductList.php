<?php
namespace HoangHiep\Custom\Block;
class ProductList extends \Magento\Framework\View\Element\Template
{    
    protected $_productCollectionFactory;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,    
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,    
        array $data = []
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        parent::__construct($context, $data);
        $this->listProductBlock = $listProductBlock;
    }
    
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        // $collection->setPageSize(8); // fetching only 8 products
        return $collection;
    }

    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }

    public function getCategoryOfProduct($productId){
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
 
        $categoryCollection = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
        
        // $appState = $objectManager->get('\Magento\Framework\App\State');
        // $appState->setAreaCode('frontend');
        
        $product = $productRepository->getById($productId);
        
        $categoryIds = $product->getCategoryIds();
        
        $categories = $categoryCollection->create()
                                        ->addAttributeToSelect('*')
                                        ->addIdFilter($categoryIds);
        
        return $categories;
    }

    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

            /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
?>