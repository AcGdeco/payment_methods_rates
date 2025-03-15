<?php

namespace Deco\Rates\Plugin;

use Deco\Rates\Helper\Data as HelperData;
use PagBank\PaymentMagento\Gateway\Config\ConfigCc;
use PagBank\PaymentMagento\Model\Api\ConsultPSInstallments;

class ConsultPSInstallmentsGetPagBankInstallmentsPlugin
{
    protected $helperData;

    protected $configCc;

    protected $consultPSInstallments;
    
    public function __construct(
        HelperData $helperData,
        Configcc $configCc,
        ConsultPSInstallments $consultPSInstallments
    ) {
        $this->helperData = $helperData;
        $this->configCc = $configCc;
        $this->consultPSInstallments = $consultPSInstallments;
    }

    public function afterGetPagBankInstallments(
        \PagBank\PaymentMagento\Model\Api\ConsultPSInstallments $subject,
        $result,
        $storeId,
        $creditCardBin, 
        $amount
    ) {
        if($this->helperData->getInstallmentsFeesEnable()) {
            $maxNumbersInstallments = count($result);
            $installmentsFees = $this->helperData->getInstallmentsFees();

            for($i = 0; $i <= $maxNumbersInstallments - 1; $i++) {
                if($i == 0) {
                    $result[$i] = [
                        'installments'      => 1,
                        'installment_value' => $amount,
                        'interest_free'     => true,
                        'amount'            => [
                            'value' => $amount,
                            'currency' => 'BRL',
                        ],
                    ];
                } else {
                    $tax1 = $this->helperData->getPercentageRate();
                    $tax2 = $this->helperData->getNumericalRate();
                    $total = $amount;
                    $total = $amount / 100;
                    $total = $total - ($total * $tax1 / 100);
                    $total = $total - $tax2;
                    
                    $total = ($total + $tax2) / (1 - (($installmentsFees[$i] + $tax1) / 100));
                    $installment = $i + 1;
                    $installment_value = $total / $installment;
                    $interestTotal = $total - $amount / 100;

                    $interestTotal = $interestTotal * 100;
                    $total = $total * 100;
                    $installment_value = $installment_value * 100;

                    $result[$i] = [
                        'installments'      => $installment,
                        'installment_value' => $installment_value,
                        'interest_free'     => false,
                        'amount'            => [
                            'value' => $total,
                            'fees' => [
                                'buyer' => [
                                    'interest' => [
                                        'total' => $interestTotal,
                                        'installments' => $installment,
                                    ],
                                ],
                            ],
                            'currency' => 'BRL',
                        ],
                    ];
                }
            }

            $result = $this->consultPSInstallments->getAvailableInstallments($result, $storeId);
            return $result;
        }

        return $result;
    }
}