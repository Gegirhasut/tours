<?php

class SendMoneyCest
{
    public function sendNormal(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'sender',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => 'receiver',
            'SendForm[amount]' => '100',
        ]);
        $I->see('Money send successfully');
        $I->see('your balance: -100.00');

        $I->submitForm('#send-form', [
            'SendForm[username]' => 'receiver',
            'SendForm[amount]' => '50.23',
        ]);
        $I->see('Money send successfully');
        $I->see('your balance: -150.23');

        $I->amOnRoute('/site/history');
        $I->see('50.23', 'table tr:nth-child(3) td:nth-child(2)');
        $I->see('Out', 'table tr:nth-child(3) td:nth-child(3)');
        $I->see('-150.23', 'table tr:nth-child(3) td:nth-child(4)');
        $I->see('100', 'table tr:nth-child(2) td:nth-child(2)');
        $I->see('Out', 'table tr:nth-child(2) td:nth-child(3)');
        $I->see('-100.00', 'table tr:nth-child(2) td:nth-child(4)');

        $I->click('Logout (sender)');
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'receiver',
        ]);

        $I->amOnRoute('/site/history');
        $I->see('50.23', 'table tr:nth-child(3) td:nth-child(2)');
        $I->see('In', 'table tr:nth-child(3) td:nth-child(3)');
        $I->see('150.23', 'table tr:nth-child(3) td:nth-child(4)');
        $I->see('100', 'table tr:nth-child(2) td:nth-child(2)');
        $I->see('In', 'table tr:nth-child(2) td:nth-child(3)');
        $I->see('100.00', 'table tr:nth-child(2) td:nth-child(4)');
    }

    public function sendTooMuch(\FunctionalTester $I) {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'too_much',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => 'receiver',
            'SendForm[amount]' => '1001',
        ]);

        $I->expectTo('see validations errors');
        $I->see('Not enough amount of money. You will have less than -1000 after sending money.');
    }

    public function sendYourself(\FunctionalTester $I) {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'yourself',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => 'yourself',
            'SendForm[amount]' => '100',
        ]);

        $I->expectTo('see validations errors');
        $I->see('You can send money only to other users');
    }

    public function sendNoUsername(\FunctionalTester $I) {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'yourself',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => '',
            'SendForm[amount]' => '100',
        ]);

        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
    }

    public function sendNoMoney(\FunctionalTester $I) {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'sender',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => 'receiver',
            'SendForm[amount]' => '',
        ]);

        $I->expectTo('see validations errors');
        $I->see('Amount cannot be blank.');
    }

    public function sendTooLess(\FunctionalTester $I) {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'sender',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => 'receiver',
            'SendForm[amount]' => '-20',
        ]);

        $I->expectTo('see validations errors');
        $I->see('Amount must be no less than 0.01.');
    }

    public function sendDecimal(\FunctionalTester $I) {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'sender',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => 'receiver',
            'SendForm[amount]' => 'text',
        ]);

        $I->expectTo('see validations errors');
        $I->see('Amount must be a number.');
    }

    public function send3AfterDots(\FunctionalTester $I) {
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'sender',
        ]);
        $I->amOnRoute('/site/send');
        $I->submitForm('#send-form', [
            'SendForm[username]' => 'receiver',
            'SendForm[amount]' => '20.00008',
        ]);

        $I->expectTo('see validations errors');
        $I->see('Amount should be numeric with 2 digits after dot');
    }
}