<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Flow;

use RedCode\Transaction\ITransaction;
use RedCode\Transaction\IAccountBlockable;

class RejectFlow extends BaseFlow
{
    /**
     * Execute actions on transaction by current flow
     * @param \RedCode\Transaction\ITransaction $transaction
     * @param \RedCode\Transaction\Flow\FlowMovement $movement
     * @return \RedCode\Transaction\ITransaction
     */
    public function execute(ITransaction $transaction, FlowMovement $movement)
    {
        if($transaction->getAccountFrom()) {
            if($movement->getFrom() == PerformFlow::TRANSACTION_STATUS_PERFORM) {
                $this->changeAccountAmount($transaction->getAccountTo(), -$transaction->getAmount());
            }

            $account = $transaction->getAccountTo();
            if($account instanceof IAccountBlockable) {
                /** @var $account IAccountBlockable */
                $blocked = $account->getBlockedMoney($transaction);
                if($blocked) {
                    $blocked->setTransaction(null);
                    $account->removeBlockedMoney($blocked);
                    $this->em->remove($blocked);
                }
            }
        }

        if($transaction->getAccountFrom()) {
            if($movement->getAccountFrom() == PerformFlow::TRANSACTION_STATUS_PERFORM) {
                $this->changeAccountAmount($transaction->getAccountFrom(), $transaction->getAmount());
            }

            $account = $transaction->getAccountFrom();
            if($account instanceof IAccountBlockable) {
                /** @var $account IAccountBlockable */
                $blocked = $account->getBlockedMoney($transaction);
                if($blocked) {
                    $blocked->setTransaction(null);
                    $account->removeBlockedMoney($blocked);
                    $this->em->remove($blocked);
                }
            }
        }

        $transaction->setExecutedAt(new \DateTime('now'));

        return $transaction;
    }
}
