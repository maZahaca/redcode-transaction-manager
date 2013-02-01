<?php
/**
 * @author maZahaca
 */

namespace RedCode\Transaction\Event;

use RedCode\Transaction\ITransaction;
use Symfony\Component\EventDispatcher\Event;

class TransactionEvent extends Event
{
    const BEFORE_EXECUTE = 'redcode.transaction.before.execute';
    const AFTER_EXECUTE = 'redcode.transaction.after.execute';

    /**
     * @var \RedCode\Transaction\ITransaction
     */
    private $transaction;

    public function __construct(ITransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return \RedCode\Transaction\ITransaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }


}
