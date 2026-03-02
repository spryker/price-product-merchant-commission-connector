<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductMerchantCommissionConnector\Business\Converter;

use Generated\Shared\Transfer\RuleEngineClauseTransfer;

interface MoneyValueConverterInterface
{
    public function convertDecimalToCent(RuleEngineClauseTransfer $ruleEngineClauseTransfer): RuleEngineClauseTransfer;
}
