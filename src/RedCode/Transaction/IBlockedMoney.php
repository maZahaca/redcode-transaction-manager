<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction;

interface IBlockedMoney
{
    /**
     * @return \DateTime
     */
    function getExpiredAt();

    /**
     * @param \DateTime $date
     * @return mixed
     */
    function setExpiredAt(\DateTime $date);

    /**
     * Get account amount
     * @return float
     */
    function getAmount();

    /**
     * Set account amount
     * @param float $amount
     * @return mixed
     */
    function setAmount($amount);

    /**
     * @return IAccountBlockable
     */
    function getAccount();

    /**
     * @param IAccountBlockable $account
     * @return mixed
     */
    function setAccount(IAccountBlockable $account);

    /**
     * @return ITransaction|null
     */
    function getTransaction();

    /**
     * @param ITransaction|null $transaction
     * @return mixed
     */
    function setTransaction(ITransaction $transaction);
}
