<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Flow;

use RedCode\Transaction\ITransaction;
use RedCode\Transaction\IAccountBlockable;

class CreateFlow extends BaseFlow
{
    /**
     * Execute actions on transaction by current flow
     * @param \RedCode\Transaction\ITransaction $transaction
     * @param \RedCode\Transaction\Flow\FlowMovement $movement
     * @throws \Exception
     * @return \RedCode\Transaction\ITransaction
     */
    public function execute(ITransaction $transaction, FlowMovement $movement)
    {
        if(!$this->checkAvailableAmount($transaction)) {
            throw new \Exception('Not money');
        }

        if($transaction->getAccountFrom() instanceof IAccountBlockable) {
            if($this->blockedMoneyProvider === null) {
                throw new \Exception('You must implement \RedCode\Transaction\Provider\IBlockedMoneyProvider and use it.');
            }
            /** @var IAccountBlockable $account */
            $account = $transaction->getAccountFrom();
            $blockedMoney = $this->blockedMoneyProvider->newInstance();
            $blockedMoney->setAmount($transaction->getAmount());
            $blockedMoney->setAccount($account);
            $blockedMoney->setTransaction($transaction);
            $account->addBlockedMoney($blockedMoney);
            $this->em->persist($blockedMoney);
        }
        return $transaction;
    }
}
