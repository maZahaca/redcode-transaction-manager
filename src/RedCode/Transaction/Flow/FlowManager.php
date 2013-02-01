<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Flow;

use Doctrine\Bundle\DoctrineBundle\Registry;
use RedCode\Transaction\ITransaction;

class FlowManager
{
    /**
     * @var array
     */
    private $flows = array();

    /**
     * @var array
     */
    private $allowMovements = array ();

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine, $flows = array ())
    {
        $this->doctrine = $doctrine;
        $index = 0;
        /** @var \RedCode\Transaction\Flow\IFlow $service */
        foreach($flows as $service) {
            if(!($service instanceof IFlow))
                throw new \Exception('Transaction flow is unsupported');

            $this->flows[$index] = $service;
            foreach($service->getMovements() as $move) {
                $this->allowMovements[(string)$move] = &$this->flows[$index];
            }
            $index++;
        }

        $this->allowMovements['empty'] = new EmptyFlow();
    }

    /**
     * Get executive flow
     * @param \RedCode\Transaction\ITransaction $transaction
     * @return IFlow
     */
    public function getFlow(ITransaction $transaction)
    {
        $movement = $this->getMovement($transaction);
        if(array_key_exists((string)$movement, $this->allowMovements))
            return $this->allowMovements[(string)$movement];
        return $this->allowMovements['empty'];
    }

    /**
     * @param \RedCode\Transaction\ITransaction $transaction
     * @return \RedCode\Transaction\Flow\FlowMovement
     */
    public function getMovement(ITransaction $transaction)
    {
        $movement = new FlowMovement();
        if($transaction->getId()) {
            $transactionDb = $this->getDoctrine()->getRepository('RedCodeTransactionBundle:Transaction')->getTransactionPlain($transaction->getId());
            $movement->setFrom($transactionDb['status']);
            $movement->setTo($transaction->getStatus());
        }
        else {
            $movement->setTo($transaction->getStatus());
        }
        return $movement;
    }
}
