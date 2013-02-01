<?php
/**
 * @author maZahaca
 */

namespace RedCode\Transaction;

interface IAccountBlockable
{
    /**
     * Get free amount
     * @return float
     */
    function getFreeAmount();

    /**
     * Add blocking to account
     * @param IBlockedMoney $blockedMoney
     * @return mixed
     */
    function addBlockedMoney(IBlockedMoney $blockedMoney);

    /**
     * Get blocked money blocked by transaction
     * @param ITransaction $transaction
     * @return IBlockedMoney|null
     */
    function getBlockedMoney(ITransaction $transaction);

    /**
     * Remove blocking to account
     * @param IBlockedMoney $blockedMoney
     * @return mixed
     */
    function removeBlockedMoney(IBlockedMoney $blockedMoney);
}
