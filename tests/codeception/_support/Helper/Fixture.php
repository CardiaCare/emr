<?php

namespace Helper;

use app\tests\codeception\_fixtures\DoctorFixture;
use app\tests\codeception\_fixtures\InviteFixture;
use app\tests\codeception\_fixtures\OrganizationFixture;
use app\tests\codeception\_fixtures\PatientFixture;
use app\tests\codeception\_fixtures\TestFixture;
use Codeception\Module;
use Codeception\TestCase;
use app\tests\codeception\_fixtures\UserTokenFixture;
use app\tests\codeception\_fixtures\UserFixture;
use tests\codeception\_fixtures\BaseFixture;
use yii\test\FixtureTrait;
use yii\test\BaseActiveFixture;

/**
 * Fixture helper
 *
 * @method BaseActiveFixture getFixture($name)
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Fixture extends Module
{
    use FixtureTrait;

    /**
     * @var array
     */
    public static $excludeActions = ['loadFixtures', 'unloadFixtures', 'getFixtures', 'globalFixtures', 'fixtures'];

    /**
     * @param TestCase $testcase
     */
    public function _before(TestCase $testcase)
    {
        \Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        $this->unloadFixtures();
        $this->loadFixtures();
        \Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        parent::_before($testcase);
    }

    /**
     * @param TestCase $testcase
     */
    public function _after(TestCase $testcase)
    {
        \Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        $this->unloadFixtures();
        \Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        parent::_after($testcase);
    }

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user'         => UserFixture::className(),
            'user_token'   => UserTokenFixture::className(),
            'invite'       => InviteFixture::className(),
            'patient'      => PatientFixture::className(),
            'test'         => TestFixture::className(),
            'organization' => OrganizationFixture::className(),
            'doctor'       => DoctorFixture::className(),
            'base'         => BaseFixture::className(),
        ];
    }

    /**
     * @param  string $name
     * @return \app\modules\user\models\User
     * @throws \yii\base\InvalidConfigException
     */
    public function getUserFixture($name)
    {
        return $this->getFixture('user')->getModel($name);
    }

    /**
     * @param  string $name
     * @return \app\modules\user\models\UserToken
     * @throws \yii\base\InvalidConfigException
     */
    public function getTokenFixture($name)
    {
        return $this->getFixture('user_token')->getModel($name);
    }

    /**
     * @param  string $name
     * @return \app\modules\user\models\UserInvite
     * @throws \yii\base\InvalidConfigException
     */
    public function getInviteFixture($name)
    {
        return $this->getFixture('invite')->getModel($name);
    }
}