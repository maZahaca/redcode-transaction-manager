<?php
/**
 * @author maZahaca
 */

namespace RedCode\Transaction;

interface IAccount
{
    /**
     * Get id of Account
     * @return int
     */
    function getId();

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
}
