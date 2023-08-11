<?php

namespace CastleIt\GeneralSettings\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use stdClass;

class ViewSettings implements ResolverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Framework\Module\FullModuleList
     */
    protected $fullModuleList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Module\FullModuleList           $fullModuleList,
        \Magento\Framework\Module\Manager                  $moduleManager,
        \Magento\Framework\App\ProductMetadataInterface    $productMetadata
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->fullModuleList       = $fullModuleList;
        $this->moduleManager        = $moduleManager;
        $this->productMetadata      = $productMetadata;
    }

    /** 
     * @param ContextInterface $context 
     * */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            $listOfModules = [];
            $allModules    = $this->fullModuleList->getAll();

            foreach ($allModules as $key => $value) {
                $listOfModules[$key]['name']   = $value['name'];
                $listOfModules[$key]['status'] = $this->moduleManager->isEnabled($value['name']);
            }

            return [
                'magento' => [
                    'version' => $this->productMetadata->getVersion()  ?? '0.0.0',
                    'edition' =>  $this->productMetadata->getEdition() ?? 'Undefined',
                ],
                'modules' => $listOfModules
            ];
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
