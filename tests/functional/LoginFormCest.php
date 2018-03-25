<?php

class LoginFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Login', 'h1');
        $I->dontSeeElement('Send money');
        $I->dontSeeElement('History');
        $I->see('Users');
    }

    public function loginWithEmptyUsername(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
    }

    public function loginNewUser(\FunctionalTester $I)
    {
        $I->dontSeeRecord('app\models\User', ['username' => 'new_user']);
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'new_user',
        ]);
        $I->seeRecord('app\models\User', ['username' => 'new_user']);
        $I->see('Logout (new_user)');
        $I->see('Send money');
        $I->see('History');
        $I->see('Users');
        $I->dontSeeElement('form#login-form');              
    }

    public function logout(\FunctionalTester $I)
    {
        $I->dontSeeRecord('app\models\User', ['username' => 'logout_user']);
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'logout_user',
        ]);
        $I->see('Logout (logout_user)');
        $I->click('Logout (logout_user)');
        $I->dontSeeElement('Send money');
        $I->dontSeeElement('History');
        $I->see('Login');
    }
}