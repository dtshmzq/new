<?php
/**
 * Author: feiber
 * Blog: 
 * Email:
 * Created at: 2017-03-15 21:16
 */

namespace frontend\controllers;

use common\models\Tags;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\Article;
use common\models\meta\TagIndexArticle;
use frontend\models\form\SignupForm;
use frontend\models\form\LoginForm;
use frontend\models\form\PasswordResetRequestForm;
use frontend\models\form\ResetPasswordForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        } else {
            Yii::$app->getUser()->setReturnUrl(Yii::$app->getRequest()->getHeaders()->get('referer'));

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout(false);

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     * @throws yii\base\Exception
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()
                         ->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()
                         ->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        }
        catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'New password was saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * website maintain shows page
     * when at "/admin/index.php?r=site/website" change website status to closed every request will execute this action
     */
    public function actionOffline()
    {
        Yii::$app->getResponse()->statusCode = 503;

        return "sorry, the site is temporary unserviceable";
    }


    /**
     * change view template
     * development website template first，then config according to yii2 document
     */
    public function actionView()
    {
        $view = Yii::$app->getRequest()->get('type', null);
        if (isset($view) && !empty($view)) {
            Yii::$app->session['view'] = $view;
        }
        $this->goBack(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * change language
     */
    public function actionLanguage()
    {
        $language = Yii::$app->getRequest()->get('lang');
        if (isset($language)) {
            $session = Yii::$app->getSession();
            $session['language'] = Html::encode($language);
        }
        $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * site
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSitetxt()
    {
        $articles = Article::find()
                           ->select(['id', 'cid'])
                           ->with('category')
                           ->where(['status' => Article::ARTICLE_PUBLISHED])
                           ->orderBy('created_at desc')
                           ->cache(7200)
                           ->all();
        foreach ($articles as $key => $model) {
            echo(Yii::$app->feiber->website_url . Url::to(['article/view', 'cat' => $model->category->alias, 'id' => $model->id]));
            echo PHP_EOL;
        }

        exit();
    }

    /**
     * sitemap
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSitemaps()
    {
        $xmlNode[] = ['loc' => Yii::$app->feiber->website_url, 'lastmod' => date('Y-m-d')];
        $articles = Article::find()->with('category')->where(['status' => Article::ARTICLE_PUBLISHED])->orderBy('updated_at desc')->limit(5000)->cache(7200)->all();
        foreach ($articles as $key => $model) {
            $xmlNode[] = [
                'loc'     => Yii::$app->feiber->website_url . Url::to(['article/view', 'cat' => $model->category->alias, 'id' => $model->id]),
                'lastmod' => date('Y-m-d', $model->updated_at),
            ];
        }
        // $tags = Tags::find()->select(['id'])->where(['key' => 'tag'])->cache(7200)->asArray()->all();
        // foreach ($tags as $tagName) {
        //     $xmlNode[] = ['loc' => Yii::$app->feiber->website_url . Url::to(['search/tag', 'id' => $tagName['id']])];
        // }
        $xmlNode[] = ['loc' => Yii::$app->feiber->website_url . '/chongwu/', 'lastmod' => date('Y-m-d')];
        $xmlNode[] = ['loc' => Yii::$app->feiber->website_url . '/dongwu/', 'lastmod' => date('Y-m-d')];
        $xmlNode[] = ['loc' => Yii::$app->feiber->website_url . '/weiyang/', 'lastmod' => date('Y-m-d')];
        $xmlNode[] = ['loc' => Yii::$app->feiber->website_url . '/zhiwu/', 'lastmod' => date('Y-m-d')];
        $xmlNode[] = ['loc' => Yii::$app->feiber->website_url . '/wenda/', 'lastmod' => date('Y-m-d')];

        $xmlData = [
            'class'      => 'yii\web\Response',
            'format'     => Response::FORMAT_XML,
            'formatters' => [
                Response::FORMAT_XML => [
                    'class'   => 'yii\web\XmlResponseFormatter',
                    'rootTag' => 'urlset', //根节点
                    'itemTag' => 'url', //单元
                ],
            ],
            'data'       => $xmlNode,
        ];

        return \Yii::createObject($xmlData);
    }
}
