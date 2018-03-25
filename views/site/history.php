<?php

/* @var $this yii\web\View */
/* @var $user app\models\User */

$this->title = 'Users balances';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Balance history!</h1>
    </div>

    <div class="content-container">

        <table class="table table-hover">
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Income/Outcome</th>
                <th>Balance</th>
            </tr>
            <?php foreach($history as $record) { ?>
                <tr>
                    <td>
                        <?= $record->created_at; ?>
                    </td>
                    <td>
                        <?= abs($record->amount); ?>
                    </td>
                    <td>
                        <?= $record->amount > 0 ? 'In' : 'Out'; ?>
                    </td>
                    <td>
                        <?= $record->balance; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

    </div>

</div>
