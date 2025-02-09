<?php
declare(strict_types=1);

namespace Deco\Rates\Plugin;

use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Deco\Rates\Helper\Data as HelperData;

class Shipping
{
    protected $helperData;
    
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function afterImportShippingRate(
        Rate $subject,
        Rate $result,
        AbstractResult $rate
    ): Rate {
        if($this->helperData->getEnable()) {
            if ($rate instanceof Method) {
                $ratePrice = $this->helperData->getNumericalPercentageRatePrice($result->getPrice());
                $result->setPrice($ratePrice);
            }
        }

        return $result;
    }
}