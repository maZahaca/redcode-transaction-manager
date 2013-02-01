<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Flow;

class EmptyFlow extends BaseFlow
{
    public function __construct()
    {
        $this->setMovements(array());
    }
    /**
     * Execute actions on transaction by current flow
     * @param \RedCode\Transaction\ITransaction $transaction
     * @param FlowMovement $movement
     * @return \RedCode\Transaction\ITransaction
     */
    public function execute(\RedCode\Transaction\ITransaction $transaction, \RedCode\Transaction\Flow\FlowMovement $movement)
    {
        return $transaction;
    }
}
