<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Flow;

use RedCode\Transaction\ITransaction;
use RedCode\Transaction\IAccountBlockable;

class PerformFlow extends BaseFlow
{
    CONST TRANSACTION_STATUS_PERFORM = null;
    /**
     * Execute actions on transaction by current flow
     * @param \RedCode\Transaction\ITransaction $transaction
     * @param \RedCode\Transaction\Flow\FlowMovement $movement
     * @return \RedCode\Transaction\ITransaction
     */
    public function execute(ITransaction $transaction, FlowMovement $movement)
    {
        if($transaction->getAccountFrom()) {
            $account = $transaction->getAccountFrom();
            $blocked = null;
            if($account instanceof IAccountBlockable) {
                /** @var $account IAccountBlockable */
                $blocked = $account->getBlockedMoney($transaction);
            }
            if(!$blocked) {
                $this->checkAvailableAmount($transaction);
            }

            $this->changeAccountAmount($transaction->getAccountFrom(), -$transaction->getAmount());

            if($blocked) {
                $blocked->setTransaction(null);
                $account->removeBlockedMoney($blocked);
                $this->em->remove($blocked);
            }
        }

        if($transaction->getAccountTo()) {
            $this->changeAccountAmount($transaction->getAccountTo(), $transaction->getAmount());

            if($this->getHoldTime()) {
                /** @var IAccountBlockable $account */
                $account = $transaction->getAccountTo();
                $blockedMoney = $this->blockedMoneyProvider->newInstance();
                $blockedMoney->setAmount($transaction->getAmount());
                $blockedMoney->setAccount($account);
                $holdBefore = new \DateTime('now');
                $holdBefore->modify("+{$this->getHoldTime()} day");
                $blockedMoney->setExpiredAt($holdBefore);
                $blockedMoney->setTransaction($transaction);
                $account->addBlockedMoney($blockedMoney);
                $this->em->persist($blockedMoney);
            }
        }

        $transaction->setExecutedAt(new \DateTime('now'));

        return $transaction;
    }

    /**
     * Hold time in days (money hold after perform transaction)
     * @return int
     */
    protected function getHoldTime()
    {
        return 0;
    }
}