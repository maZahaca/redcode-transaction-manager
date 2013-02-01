<?php
/**
 * User: matrlx
 * Date: 9/14/12
 * Time: 4:32 PM
 */
namespace RedCode\Transaction\Flow;

use RedCode\Transaction\ITransaction;

interface IFlow
{
    /**
     * Execute actions on transaction by current flow
     * @param \RedCode\Transaction\ITransaction $transaction
     * @param FlowMovement $movement
     * @return \RedCode\Transaction\ITransaction
     */
    public function execute(ITransaction $transaction, FlowMovement $movement);

    /**
     * Get allowed movement for flow
     * @return array
     */
    public function getMovements();
}
