<?php
namespace Deco\Rates\Plugin;

use Magento\Config\Model\Config;
use Deco\Rates\Block\UpdateTotalPrice;
use Deco\Rates\Helper\Data;

class ConfigBackend
{
    /**
     * @var UpdateTotalPrice
     */
    protected $updateTotalPrice;
    
    /**
     * @var helperData
     */
    protected $helperData;

    /**
     * @var compareResult
     */
    protected $compareResult;

    /**
     * @var section
     */
    protected $section;

    /**
     * @param UpdateTotalPrice $updateTotalPrice
     */
    public function __construct(
        UpdateTotalPrice $updateTotalPrice,
        Data $helperData
    ) {
        $this->updateTotalPrice = $updateTotalPrice;
        $this->helperData = $helperData;
    }
    
    /**
     * Executa código antes de salvar a configuração.
     *
     * @param Config $subject
     * @return void
     */
    public function beforeSave(Config $subject)
    {
        $this->section = $subject->getSection();
        
        if($this->section == 'decorates'){
            $groups = $subject->getGroups();
            $this->compareResult = $this->helperData->getCompareValuesConfig($groups);
        }
    }

    public function afterSave(Config $subject)
    {
        if($this->section == 'decorates'){
            $compareResult = $this->compareResult;
            if($compareResult[0] == true){
                $this->updateTotalPrice->changeTotalPriceProductsValues();
            } else {
                if($compareResult[1] != false && $compareResult[1] != $this->helperData->getPercentageRate()) {
                    $this->updateTotalPrice->changeTotalPriceProductsValues();
                }
            }
        }
    }
}