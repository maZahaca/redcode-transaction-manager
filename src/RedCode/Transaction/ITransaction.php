<?php
/**
 * @author maZahaca
 */

namespace RedCode\Transaction;

interface ITransaction
{
    /**
     * Get Transaction Id
     * @return int
     */
    public function getId();

    /**
     * Get account from
     * @return IAccount
     */
    public function getAccountFrom();

    /**
     * Get account to
     * @return IAccount
     */
    public function getAccountTo();

    /**
     * Get created date
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get Executed date
     * @return \DateTime|null
     */
    public function getExecutedAt();

    /**
     * Set executed at
     * @param \DateTime $date
     * @return mixed
     */
    public function setExecutedAt(\DateTime $date);

    /**
     * Amount of transaction
     * @return float
     */
    public function getAmount();

    /**
     * Get transaction status
     * @return int
     */
    public function getStatus();
}
