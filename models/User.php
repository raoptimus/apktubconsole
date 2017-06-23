<?php

namespace app\models;

use app\components\MongoActiveRecord;
use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "auth_users".
 *
 * @property \MongoId $_id
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 * @property array $RolesList
 * @property array $Blocked
 * @property string PostBackUrl
 */
class User extends MongoActiveRecord implements IdentityInterface
{
    public static function getDb()
    {
        return \Yii::$app->get('authDb');
    }

    public $NewPassword;

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'AuthUsers';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'username', 'password', 'authKey', 'accessToken', 'Blocked', 'PostBackUrl'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password', 'authKey', 'accessToken'], 'string', 'max' => 255],
            [['Blocked'], 'integer'],
            [['NewPassword','RolesList'],'safe'],
            [['PostBackUrl'], 'url'],
            [['PostBackUrl'], function ($attribute) {
                if (!empty($this->$attribute) && $this->$attribute != 'http://example.com') {
                    if (strpos($this->$attribute,'{OFFER_ID}') === false) {
                        $this->addError($attribute, 'Отсутствует параметр OFFER_ID');
                    }
                    if (strpos($this->$attribute,'{AFFILIATE_ID}') === false) {
                        $this->addError($attribute, 'Отсутствует параметр AFFILIATE_ID');
                    }
                }
            }],
            ['username','unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'username' => Yii::t('dict', 'Username'),
            'password' => Yii::t('dict', 'Password'),
            'authKey' => Yii::t('dict', 'Auth Key'),
            'accessToken' => Yii::t('dict', 'Access Token'),
            'NewPassword' => Yii::t('dict', 'New Password'),
            'Blocked' => Yii::t('dict', 'Blocked?'),
            'PostBackUrl' => Yii::t('dict', 'PostBack Url'),
        ];
    }

    public function getAuthKey() {
        return $this->authKey;
    }

    public function init() {
        $this->PostBackUrl = 'http://example.com';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if(is_array($id)) {
            return static::findOne(['_id' => $id['$id']]);
        } else {
            return static::findOne(['_id' => $id]);
        }
    }

    public static function getNameById($id = '') {
        if (empty($id)) {
            return null;
        }
        $user = static::findOne(['_id' => $id]);
        return $user->username;
    }

    /**
     * @return id|null
     */
    public static function getIdByName($name = '') {
        if (empty($name)) {
            return null;
        }
        $user = static::findOne(['username' => $name]);
        return $user ? $user->id : null;
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return (string) $this->_id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'username', $this->username]);

        return $dataProvider;
    }

    public function getRolesList() {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser(strval($this->_id));
        $returnArray = [];
        foreach ($roles as $role) {
            $returnArray[] = $role->name;
        }
        return $returnArray;
    }

    public function setRolesList($roles) {
        if (!empty($this->_id)) {
            $auth = Yii::$app->authManager;
            $auth->revokeAll(strval($this->_id));

            if (!empty($roles)) {
                foreach ($roles as $role) {
                    $authRole = $auth->getRole($role);
                    $auth->assign($authRole,strval($this->_id));
                }
            }
        }
    }

    public function block() {
        return (bool) $this->updateAttributes([
            'Blocked' => 1,
            'authKey' => Yii::$app->security->generateRandomString()
        ]);
    }

    public function unBlock() {
        return (bool) $this->updateAttributes([
            'Blocked' => 0,
            'authKey' => Yii::$app->security->generateRandomString()
        ]);
    }

    public function beforeSave($insert) {
        if ($insert) {
            $this->setAttribute('authKey', Yii::$app->security->generateRandomString());
        }
        return parent::beforeSave($insert);
    }
}
