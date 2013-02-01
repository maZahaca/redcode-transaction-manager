<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Flow;

use Doctrine\ORM\EntityManager;
use RedCode\Transaction\Provider\IBlockedMoneyProvider;
use RedCode\Transaction\ITransaction;
use RedCode\Transaction\IAccountCredit;
use RedCode\Transaction\IAccountBlockable;
use RedCode\Transaction\Annotation\Account\AmountField;
use RedCode\Transaction\Annotation\Reader;

abstract class BaseFlow implements IFlow
{
    /**
     * Allowed movements
     * @var array
     */
    protected $movements = array ();

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \RedCode\Transaction\Annotation\Reader
     */
    protected $reader;

    /**
     * @var \RedCode\Transaction\Provider\IBlockedMoneyProvider|null
     */
    protected $blockedMoneyProvider;

    public function __construct(EntityManager $entityManager, Reader $reader, IBlockedMoneyProvider $blockedMoneyProvider = null)
    {
        $this->em = $entityManager;
        $this->reader = $reader;
        $this->blockedMoneyProvider = $blockedMoneyProvider;
    }

    /**
     * Check account for allow amount for transaction
     * @param \RedCode\Transaction\ITransaction $transaction
     * @return bool
     * @throws \Exception
     */
    protected function checkAvailableAmount(ITransaction $transaction)
    {
        $account = $transaction->getAccountFrom();
        return
            ($account === null) ||
            ($account instanceof IAccountCredit && $account->getAllowCredit()) ||
            ($account instanceof IAccountBlockable && $transaction->getAmount() <= $account->getFreeAmount()) ||
            ($transaction->getAmount() <= $account->getAmount())
        ;
    }

    protected function changeAccountAmount(\RedCode\Transaction\IAccount $account, $amount)
    {
        $class = get_class($account);
        $field = $this->reader->getFields($account, AmountField::className());
        if(!count($field)) {
            throw new \Exception('Not found account \'amount\' field. Mark it as ' . AmountField::className() . 'annotation');
        }
        $field = current($field);

        $dql   = "update {$class} c set c.{$field} = c.{$field} + :amount where c.id = :id";
        $query = $this->em->createQuery($dql);
        $query->setParameters(array(
                                   'amount' => $amount,
                                   'id'     => $account->getId()
                              ));
        $query->execute();
    }

    public function getMovements()
    {
        return $this->movements;
    }

    /**
     * @param array $movements
     */
    protected function setMovements($movements)
    {
        $this->movements = $movements;
    }
}
