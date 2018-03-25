<?php

namespace tests\models;

use app\models\History;
use app\models\LoginForm;
use app\models\SendForm;
use app\models\User;

class SendFormTest extends \Codeception\Test\Unit
{
    private $model;

    public function testSendAnonymous()
    {
        $this->model = new SendForm([
            'username' => 'aaa',
            'amount' => 10
        ]);

        $user = User::findOne(['username' => 'aaa']);
        expect_that(empty($user));

        $user = User::getUser('aaa');
        expect($user->balance)->equals(0);
        $this->checkEmptyHistory();
    }

    public function testSendNormal()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => 'receiver',
            'amount' => 10.03
        ]);

        $this->model->send();

        $this->checkBalances(-10.03, 10.03);

        $sender = User::getUser('sender');
        $receiver = User::getUser('receiver');

        $history = History::findAll(['user' => $sender->getId()]);
        expect(count($history))->equals(1);
        expect($history[0]->amount)->equals('-10.03');
        expect($history[0]->balance)->equals('-10.03');

        $history = History::findAll(['user' => $receiver->getId()]);
        expect(count($history))->equals(1);
        expect($history[0]->amount)->equals('10.03');
        expect($history[0]->balance)->equals('10.03');
    }

    public function testSendTwice()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => 'receiver',
            'amount' => 10.03
        ]);

        $this->model->send();

        $this->model = new SendForm([
            'username' => 'receiver',
            'amount' => 20.05
        ]);

        $this->model->send();

        $this->checkBalances(-30.08, 30.08);

        $sender = User::getUser('sender');
        $receiver = User::getUser('receiver');

        $history = History::findAll(['user' => $sender->getId()]);
        expect(count($history))->equals(2);
        expect($history[0]->amount)->equals('-10.03');
        expect($history[0]->balance)->equals('-10.03');
        expect($history[1]->amount)->equals('-20.05');
        expect($history[1]->balance)->equals('-30.08');

        $history = History::findAll(['user' => $receiver->getId()]);
        expect(count($history))->equals(2);
        expect($history[0]->amount)->equals('10.03');
        expect($history[0]->balance)->equals('10.03');
        expect($history[1]->amount)->equals('20.05');
        expect($history[1]->balance)->equals('30.08');
    }

    public function testSendTooMuch()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => 'receiver',
            'amount' => 1001
        ]);

        $this->model->send();

        expect_that($this->model->hasErrors());
        expect($this->model->getErrors('amount')[0])->equals('Not enough amount of money. You will have less than -1000 after sending money.');

        $this->checkBalances();
        $this->checkEmptyHistory();
    }

    public function testSendYourself()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => 'sender',
            'amount' => 100
        ]);

        $this->model->send();

        expect_that($this->model->hasErrors());
        expect($this->model->getErrors('username')[0])->equals('You can send money only to other users');

        $this->checkBalances();
        $this->checkEmptyHistory();
    }

    public function testSendNobody()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => '',
            'amount' => 100
        ]);

        $this->model->send();

        expect_that($this->model->hasErrors());
        expect($this->model->getErrors('username')[0])->equals('Username cannot be blank.');

        $this->checkBalances(0, null);
        $this->checkEmptyHistory();
    }

    public function testSendNothing()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => 'receiver',
            'amount' => ''
        ]);

        $this->model->send();

        expect_that($this->model->hasErrors());
        expect($this->model->getErrors('amount')[0])->equals('Amount cannot be blank.');

        $this->checkBalances(0, null);
        $this->checkEmptyHistory();
    }

    public function testSendMinus()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => 'receiver',
            'amount' => -10
        ]);

        $this->model->send();

        expect_that($this->model->hasErrors());
        expect($this->model->getErrors('amount')[0])->equals('Amount must be no less than 0.01.');

        $this->checkBalances(0, null);
        $this->checkEmptyHistory();
    }

    public function testSendThreeDigitsAfterDot()
    {
        $this->model = new LoginForm([
            'username' => 'sender'
        ]);

        $this->model->login();

        $this->model = new SendForm([
            'username' => 'receiver',
            'amount' => 10.005
        ]);

        $this->model->send();

        expect_that($this->model->hasErrors());
        expect($this->model->getErrors('amount')[0])->equals('Amount should be numeric with 2 digits after dot');

        $this->checkBalances(0, null);
        $this->checkEmptyHistory();
    }

    private function checkBalances($senderBalance = 0, $receiverBalance = 0) {
        $sender = User::getUser('sender');
        expect($sender->balance)->equals($senderBalance);

        if (!is_null($receiverBalance)) {
            $receiver = User::getUser('receiver');
            expect($receiver->balance)->equals($receiverBalance);
        }
    }

    private function checkEmptyHistory() {
        $history = History::findAll([]);
        expect(count($history))->equals(0);
    }
}
