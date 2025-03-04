<?php

namespace Deco\Rates\Block;

use Deco\Rates\Helper\Data;
use Magento\Framework\View\Element\Template;

class Config extends Template
{
    protected $helperData;

    public function __construct(
        Template\Context $context,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    public function getConfig()
    {
        $config = [
            "enable" => $this->helperData->getEnable(),
            "installmentsFeesEnable" => $this->helperData->getInstallmentsFeesEnable(),
            "percentageRate" => $this->helperData->getPercentageRate(),
            "productPercentageRate" => $this->helperData->getProductPercentageRate(),
            "unalterableProductPercentageRate" => $this->helperData->getUnalterableProductPercentageRate(),
            "numericalRate" => $this->helperData->getNumericalRate(),
            "installmentsFees" => $this->helperData->getInstallmentsFees()
        ];

        return $config;
    }
}
