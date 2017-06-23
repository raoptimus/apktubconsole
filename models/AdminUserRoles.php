<?php

namespace app\models;

use app\components\MongoActiveRecord;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $_id
 * @property string $name
 * @property string $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 */
class AdminUserRoles extends MongoActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('authDb');
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'AuthItems';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'name', 'type', 'created_at', 'updated_at'], 'required'],
            [['type', 'data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['_id', 'name', 'description', 'rule_name'], 'string', 'max' => 255],
            ['name','unique'],
            ['permissions','safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'name' => Yii::t('dict', 'Name'),
            'type' => Yii::t('dict', 'Type'),
            'description' => Yii::t('dict', 'Description'),
            'rule_name' => Yii::t('dict', 'Rule Name'),
            'data' => Yii::t('dict', 'Data'),
            'created_at' => Yii::t('dict', 'Created At'),
            'updated_at' => Yii::t('dict', 'Updated At'),
        ];
    }

    public static function getDataProvider(){
        $query = self::find();
        $query->andWhere(['type' => 1]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public static function getPermissionsDataProvider(){
        $query = self::find();
        $query->andWhere(['type' => 2]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public static function getAllRoles($except = '') {
        $roles = self::find()->where(['type' => 1])->all();
        $returnArray = [];
        foreach ($roles as $role) {
            if ($role['name'] != $except) {
                $returnArray[$role['name']] = $role['name'];
            }
        }
        return $returnArray;
    }

    public static function getAllPermissions() {
        $roles = self::find()->where(['type' => 2])->all();
        $returnArray = [];
        foreach ($roles as $role) {
            $returnArray[(string)$role['name']] = $role['name'];
        }
        return $returnArray;
    }

    public function getPermissions() {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByRole($this->name);

        $returnArray = [];
        foreach ($permissions as $permission) {
            $returnArray[] = (string) $permission->name;
        }

        return $returnArray;
    }

    public function setPermissions($permissions) {
/*
        $auth = Yii::$app->authManager;
        $currentRole = $auth->getRole($this->name);
        $auth->removeChildren($currentRole);

        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                $permissionObj = $auth->getPermission($permission);
                $auth->addChild($currentRole,$permissionObj);
            }
        }*/

    }

    public function setRoles($roles) {

    }

    public function getRoles() {
        $auth = Yii::$app->authManager;
        $roles = $auth->getChildren($this->name);

        $returnArray = [];
        foreach ($roles as $role) {
            $returnArray[] = (string) $role->name;
        }
        return $returnArray;
    }

}
