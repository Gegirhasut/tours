<?php

namespace app\controllers;

use app\models\History;
use app\models\SendForm;
use app\models\User;
use Yii;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Balances action.
     *
     * @return string
     */
    public function actionBalances() {
        $users = User::find()->all();

        return $this->render('balances', [
            'users' => $users,
        ]);
    }

    public function actionSend() {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SendForm();
        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->getSession()->addFlash('message', ['type' => 'success', 'message' => 'Money send successfully']);

            return $this->refresh();
        }

        return $this->render('send', [
            'model' => $model,
        ]);
    }

    public function actionHistory() {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $history = History::find()
            ->where(['user' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('history', [
            'history' => $history,
        ]);
    }
}
