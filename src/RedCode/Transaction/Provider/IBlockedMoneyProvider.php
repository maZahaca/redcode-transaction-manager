<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Provider;

interface IBlockedMoneyProvider
{
    /**
     * @return \RedCode\Transaction\IBlockedMoney
     */
    public function newInstance();
}
