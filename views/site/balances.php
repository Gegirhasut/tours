<?php

/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->title = 'Users balances';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Users balances!</h1>
    </div>

    <div class="content-container">

        <table class="table table-hover">
            <tr>
                <th>Id</th>
                <th>Username</th>
                <th>Balance</th>
            </tr>
            <?php foreach($users as $user) { ?>
                <tr>
                    <td>
                        <?= $user->id; ?>
                    </td>
                    <td>
                        <?= $user->username; ?>
                    </td>
                    <td>
                        <?= $user->balance; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

    </div>

</div>
