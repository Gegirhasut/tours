<?php

class BalancesPageCest
{
    public function balances(\FunctionalTester $I)
    {
        $u1 = \app\models\User::getUser('u1');
        $u2 = \app\models\User::getUser('u2');
        $u2->addBalance(30.03);
        $u3 = \app\models\User::getUser('u3');
        $u3->addBalance(-100);

        $I->amOnRoute('site/balances');
        $I->see('u1');
        $I->see('u1');
        $I->see('u2');
        $I->see('0.00');
        $I->see('30.03');
        $I->see('-100.00');
    }
}