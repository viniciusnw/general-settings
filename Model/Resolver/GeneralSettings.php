<?php

namespace CastleIt\GeneralSettings\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GeneralSettings implements ResolverInterface
{
    protected $_scopeConfigInterface;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->_scopeConfigInterface = $scopeConfigInterface;
    }

    private function convertVales($settings)
    {
        foreach ($settings as $key => $value)
            foreach ($settings[$key] as $index => $value) {
                if ($key == 'payment_flags') $result[$key][$index] = (bool) $value;
                else $result[$key][$index] = $value;
            }

        return $result;
    }

    public function getGeneralSettings()
    {
        $settings = $this->_scopeConfigInterface
            ->getValue(
                "castle_general_settings",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

        if (!$settings) return null;

        return $this->convertVales(
            $settings
        );
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
            return $this->getGeneralSettings();
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
