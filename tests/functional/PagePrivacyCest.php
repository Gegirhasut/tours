<?php

class PagePrivacyCest
{
    public function anonymousUser(\FunctionalTester $I)
    {
        $I->amOnRoute('/');
        $I->see('Users');
        $I->dontSeeElement('Send money');
        $I->dontSeeElement('History');
    }

    public function existsUser(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'exists_user',
        ]);
        $I->see('Users');
        $I->dontSeeElement('Send money');
        $I->dontSeeElement('History');
    }

    public function pageBalances(\FunctionalTester $I)
    {
        $I->amOnRoute('site/balances');
        $I->see('Users balances!', 'h1');
    }

    public function pageUserHistoryPrivacy(\FunctionalTester $I)
    {
        $I->amOnRoute('site/history');
        $I->seeCurrentUrlEquals('/index-test.php');
    }

    public function pageSendMoneyPrivacy(\FunctionalTester $I)
    {
        $I->amOnRoute('site/send');
        $I->seeCurrentUrlEquals('/index-test.php');
    }
}