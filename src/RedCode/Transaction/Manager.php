<?php
/**
 * @author maZahaca
 */

namespace RedCode\Transaction;

use Doctrine\ORM\EntityManager;
use RedCode\Transaction\Event\TransactionEvent;
use RedCode\Transaction\Flow\FlowManager;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Manager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var Flow\FlowManager
     */
    private $flowManager;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Monolog\Logger $logger
     * @param Flow\FlowManager $flowManager
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, Logger $logger, FlowManager $flowManager)
    {
        $this->em               = $entityManager;
        $this->eventDispatcher  = $eventDispatcher;
        $this->logger           = $logger;
        $this->flowManager      = $flowManager;
    }

    /**
     * Process Transaction(s)
     * Can be called:
     * ->process(ITransaction[] $transactionsArray),
     * ->process(ITransaction $tr1 [, ..., ITransaction $trN])
     * @throws \Exception
     */
    public function process()
    {
        $transactions = func_get_args();
        if(count($transactions) == 1 && is_array($transactions[0])) {
            $transactions = $transactions[0];
        }

        $upperTransaction = $this->em->getConnection()->isTransactionActive();

        if(!$upperTransaction)
            $this->em->getConnection()->beginTransaction();

        try {

            foreach($transactions as $transaction) {
                if(!($transaction instanceof ITransaction)) {
                    throw new \Exception('Manger:process can execute only ITransaction instances');
                }
                $this->executeTransaction($transaction);
            }

            $this->em->flush();
            if(!$upperTransaction)
                $this->em->getConnection()->commit();
        }
        catch(\Exception $ex) {
            $this->getLogger()->err($ex->getMessage(), array('Transaction'));
            if(!$upperTransaction)
                $this->em->getConnection()->rollback();
            throw $ex;
        }
        return true;
    }

    private function executeTransaction(ITransaction $transaction)
    {
        // dispatch event before transaction execute
        $this->getEventDispatcher()->dispatch(TransactionEvent::BEFORE_EXECUTE, new TransactionEvent($transaction));

        $statusMovement = $this->getFlowManager()->getMovement($transaction);
        // execute current flow into transaction
        $this->getFlowManager()->getFlow($transaction)->execute($transaction, $statusMovement);

        $this->em->persist($transaction);

        // dispatch event after transaction execute
        $this->getEventDispatcher()->dispatch(TransactionEvent::AFTER_EXECUTE, new TransactionEvent($transaction));

        return $transaction;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return \Monolog\Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Flow\FlowManager
     */
    protected function getFlowManager()
    {
        return $this->flowManager;
    }


}
