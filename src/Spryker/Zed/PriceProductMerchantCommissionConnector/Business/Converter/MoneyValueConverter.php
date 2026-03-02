<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductMerchantCommissionConnector\Business\Converter;

use Generated\Shared\Transfer\RuleEngineClauseTransfer;
use Spryker\Zed\PriceProductMerchantCommissionConnector\Dependency\Facade\PriceProductMerchantCommissionConnectorToMoneyFacadeInterface;

class MoneyValueConverter implements MoneyValueConverterInterface
{
    /**
     * @uses \Spryker\Zed\RuleEngine\Business\Comparator\Operator\IsInCompareOperator::EXPRESSION
     *
     * @var string
     */
    protected const EXPRESSION_IS_IN = 'is in';

    /**
     * @uses \Spryker\Zed\RuleEngine\Business\Comparator\Operator\IsNotInCompareOperator::EXPRESSION
     *
     * @var string
     */
    protected const EXPRESSION_IS_NOT_IN = 'is not in';

    /**
     * @uses \Spryker\Zed\RuleEngine\RuleEngineConfig::LIST_DELIMITER
     *
     * @phpstan-var non-empty-string
     *
     * @var string
     */
    public const LIST_DELIMITER = ';';

    /**
     * @var \Spryker\Zed\PriceProductMerchantCommissionConnector\Dependency\Facade\PriceProductMerchantCommissionConnectorToMoneyFacadeInterface
     */
    protected PriceProductMerchantCommissionConnectorToMoneyFacadeInterface $moneyFacade;

    public function __construct(PriceProductMerchantCommissionConnectorToMoneyFacadeInterface $moneyFacade)
    {
        $this->moneyFacade = $moneyFacade;
    }

    public function convertDecimalToCent(RuleEngineClauseTransfer $ruleEngineClauseTransfer): RuleEngineClauseTransfer
    {
        if (
            $ruleEngineClauseTransfer->getOperatorOrFail() === static::EXPRESSION_IS_NOT_IN ||
            $ruleEngineClauseTransfer->getOperatorOrFail() === static::EXPRESSION_IS_IN
        ) {
            return $this->convertListPrice($ruleEngineClauseTransfer);
        }

        return $this->convertSinglePrice($ruleEngineClauseTransfer);
    }

    protected function convertListPrice(RuleEngineClauseTransfer $ruleEngineClauseTransfer): RuleEngineClauseTransfer
    {
        $pricesExploded = explode(static::LIST_DELIMITER, $ruleEngineClauseTransfer->getValueOrFail());

        $pricesConverted = [];
        foreach ($pricesExploded as $price) {
            $pricesConverted[] = $this->moneyFacade->convertDecimalToInteger($this->formatValue($price));
        }

        return $ruleEngineClauseTransfer->setValue(implode(static::LIST_DELIMITER, $pricesConverted));
    }

    protected function convertSinglePrice(RuleEngineClauseTransfer $ruleEngineClauseTransfer): RuleEngineClauseTransfer
    {
        $priceConverted = $this->moneyFacade->convertDecimalToInteger(
            $this->formatValue($ruleEngineClauseTransfer->getValueOrFail()),
        );

        return $ruleEngineClauseTransfer->setValue((string)$priceConverted);
    }

    protected function formatValue(string $value): float
    {
        return (float)str_replace(',', '.', trim($value));
    }
}
