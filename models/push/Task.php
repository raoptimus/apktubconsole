<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 22.05.15
 * Time: 22:07
 */

namespace app\models\push;


use app\components\MongoActiveRecord;
use app\models\files\PushIcon;
use app\models\Language;
use app\models\Text;
use app\models\Country;
use MongoDate;
use Yii;
use yii\base\Model;
use app\models\users\Device;

/**
 * Class PushTask
 * @package models\push
 * @property int _id
 * @property string id
 * @property Text[] Message
 * @property string IconUrl
 * @property string Note
 * @property string GoUrl
 * @property Text[] Header
 * @property int[] DaysOfWeek
 * @property int Hour
 * @property int MaxHour
 * @property Repeat Repeat
 * @property int FrequencyHours
 * @property Action Action
 * @property array Options
 * @property State State
 * @property MongoDate AddedDate
 * @property boolean Enabled
 * @property boolean Deleted
 * @property int ActionForm
 * @property int PushSendedCount
 * @property int PushClickCount
 * @property string IconFile
 *
 */
class Task extends MongoActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('mongodb2');
    }

    public function getId(){
        return (string) $this->_id;
    }

    public function getTranslatedHeader($lang = "ru")
    {
        return Text::getTranslatedQuote($this->Header, $lang);
    }

    public function getIconUrlForm() {
        $uri = '';
        if (empty($this->IconFile)) {
            $uri = $this->getAttribute('IconUrl');
        } else {
            $type = 'png';
            $iconFile = PushIcon::findOne(['_id' => $this->IconFile]);

            if ($iconFile->contentType == 'image/jpeg') {
                $type = 'jpg';
            }
            $uri = "/icons/{$this->IconFile}.$type";
        }

        //если нет домена - подставить его обратно для вывода в форму
        if (preg_match('((http:\/\/|https:\/\/)+([\w\-\.]+)?[\w\-]+(!?\.[\w]{2,4}))',$uri)) {
            return $uri;
        } else {
            return 'http://' . Yii::$app->params['project'] . $uri;
        }
    }

    public function setIconUrlForm($input) {
        //а теперь в обратную сторону. Если к нам пришёл наш домен - удаляем его. остальные - оставляем.
        if (preg_match('((http:\/\/|https:\/\/)+([\w\-\.]+)?[\w\-]+(!?\.[\w]{2,4}))',$input)) {
            //какой-то домен есть.
            $domain = parse_url($input, PHP_URL_HOST);
            if (in_array($domain, Yii::$app->params['projects'])) {
                //отрезаем протокол
                $input = substr($input, strpos($input,'/') + 2);
                //режем к чёртовой матери. Не дожидаясь перитонита.
                $input = substr($input, strpos($input,'/'));
            }
        } else {
            //в начале должен быть слеш!
            if ($input[0] != '/') {
                $input = '/' . $input;
            }
        }
        $this->setAttribute('IconUrl',$input);
    }

    public function getTranslatedMessage($lang = "ru")
    {
        return Text::getTranslatedQuote($this->Message, $lang);
    }

    public function getHeaderForm()
    {
        if (empty($this->Header)) {
            return [Language::getKeys()[0] => ""];
        }
        $a = [];
        foreach ($this->Header as $h) {
            $a[$h['Language']] = $h['Quote'];
        }
        return $a;
    }

    public function setHeaderForm($v)
    {
        $a = [];
        foreach ($v as $l => $q) {
            if (empty($q)) {
                continue;
            }
            $a[] = [
                'Language' => $l,
                'Quote' => $q,
            ];
        }
        $this->Header = $a;
    }

    public function getMessageForm()
    {
        if (empty($this->Message)) {
            return [Language::getKeys()[0] => ""];
        }
        $a = [];
        foreach ($this->Message as $h) {
            $a[$h['Language']] = $h['Quote'];
        }
        return $a;
    }

    public function setMessageForm($v)
    {
        $a = [];
        foreach ($v as $l => $q) {
            if (empty($q)) {
                continue;
            }
            $a[] = [
                'Language' => $l,
                'Quote' => $q,
            ];
        }
        $this->Message = $a;
    }

    public function getActionForm()
    {
        return $this->Action;
    }

    public function setActionForm($value)
    {
        $this->Action = $value;
        $this->Options = $this->getActionModel()->getAttributes();
    }

    /**
     * return Model
     * @return Model
     */
    public function getActionModel()
    {
        /**
         * @var Model $a
         */
        $name = "app\\models\\push\\" . Action::getValue($this->Action);
        $a = new $name;
        $a->setAttributes($this->Options);

        return $a;
    }

    public function removeIcon() {
        $this->IconFile = null;
        $this->save();
    }

    public function getTokenForm() {
        return true;
    }
    public function setTokenForm($input) {
        $this->Options = [
            'Token' => array_values(array_filter(array_map('trim',explode(',',$input))))
        ];
    }

    public function getCountriesForm()
    {
        return array_fill_keys($this->Countries ?: [], 1);
    }

    public function setCountriesForm($value)
    {
        $this->Countries = array_keys(array_filter($value));
    }

    /*
     * override methods
     * =========================================================
     */

    public function init()
    {
        //set default values
        $this->_id = 0;
        $this->ActionForm = 0;
        $this->Repeat = 0;
        $this->State = 0;
        $this->Hour = 0;
        $this->MaxHour = 0;
        $this->DaysOfWeek = range(1, 7);
        $this->FrequencyHours = 72;
        $this->GoUrl = Yii::$app->params['pushGoUrlDefaultScheme'] . "://";
        $this->IconUrl = "/images/2x/app-block-icon@2x.png";
        $this->AddedDate = new \MongoDate();
        $this->Enabled = true;
        $this->Deleted = false;
    }

    public static function collectionName()
    {
        return "PushTask";
    }

    public function formName()
    {
        return "p";
    }

    public function beforeSave($insert)
    {
        $this->Repeat = (int)$this->Repeat;
        $this->Action = (int)$this->Action;
        $this->State = (int)$this->State;
        $this->Enabled = (bool)$this->Enabled;
        $this->Deleted = (bool)$this->Deleted;
        $this->Hour = (int)$this->Hour;
        $this->MaxHour = (int)$this->MaxHour;
        $this->FrequencyHours = (int)$this->FrequencyHours;
        $this->PushSendedCount = (int)$this->PushSendedCount;
        $this->PushClickCount = (int)$this->PushClickCount;
        $this->CarrierType = array_values($this->CarrierType);
        $this->Countries = array_values($this->Countries);

        return parent::beforeSave($insert);
    }


    public function rules()
    {
        return [
            [
                '_id',
                'integer',
                'min' => 1,
            ],
            [
                ['IconUrl','Note'],
                'string',
//                'defaultScheme' => 'http',
//                'message' => Yii::t('dict', 'URL is not a valid URL address.'),
            ],
            [
                'GoUrl',
                'url',
                'pattern' => '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)?/i',
                'validSchemes' => ['mobru.tube.app', 'mob.tube.app', 'mobile.tube.app', 'mobile.tube.apk', 'http', 'https'],
                'defaultScheme' => Yii::$app->params['pushGoUrlDefaultScheme'],
                'message' => Yii::t('dict', 'URL is not a valid URL address.'),
            ],
            [
                ['Hour', 'MaxHour'],
                'integer',
                'min' => 0,
                'max' => 23,
            ],
            [
                ['Header', 'Message'],
                'app\components\EmbedDocListValidator',
                'model' => 'app\models\Text',
                'errAttributes' => ["HeaderForm", "MessageForm"],
            ],
            [
                'Repeat',
                'in',
                'range' => Repeat::getKeys(),
            ],
            [
                'FrequencyHours',
                'integer',
                'min' => 1,
            ],
            [
                'Action',
                'in',
                'range' => Action::getKeys(),
            ],
            [
                'DaysOfWeek',
                function () {
                    //see beforeValidate; (array)"" -> [0 => 1], which trigger's next error
                    $days = array_unique(array_map('intval', $this->DaysOfWeek));

                    // Days Of week can contain up to 7 element in range from 1 to 7
                    if (count(array_unique(array_merge(range(1, 7), $days))) != 7) {
                        $this->addError("DaysOfWeek", Yii::t("dict", "Please select at least one day of week"));
                        return false;
                    }

                    return true;
                },
            ],
            [
                'Options',
                function () {
                    $a = $this->getActionModel();
                    if (!$a->validate()) {
                        foreach ($a->errors as $attr => $errs) {
                            foreach ($errs as $err) {
                                $this->addError("Options[{$attr}]", $err);
                            }
                        }
                        return false;
                    }
                    $this->Options = $a->getAttributes();
                    return true;
                },
            ],
            [
                ['Enabled','Deleted'],
                'boolean',
            ],
            [
                'State',
                'in',
                'range' => State::getKeys(),
            ],
            [
                ['MessageForm', 'HeaderForm', 'ActionForm','IconFileForm','IconUrlForm','TokenForm','CountriesForm','CarrierType'],
                'safe',
            ],
            [
                ['IconFile'],
                'string',
            ],
            [
                ['Hour', 'MaxHour', 'IconUrl', 'GoUrl', 'FrequencyHours', 'Enabled', 'Action','Countries','CarrierType'],
                'required',
            ],
        ];
    }

    public $IconFileForm;

    public function attributes()
    {
        return [
            '_id',
            'Message',
            'IconUrl',
            'IconFile',
            'GoUrl',
            'Header',
            'DaysOfWeek',
            'Hour',
            'MaxHour',
            'Repeat',
            'FrequencyHours',
            'Options',
            'State',
            'AddedDate',
            'Enabled',
            'Action',
            'PushSendedCount',
            'PushClickCount',
            'Countries',
            'CarrierType',
            'Note',
            'Deleted'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'Message' => Yii::t('dict', 'Message'),
            'MessageForm' => Yii::t('dict', 'Message'),
            'IconUrl' => Yii::t('dict', 'IconUrl'),
            'GoUrl' => Yii::t('dict', 'GoUrl'),
            'Header' => Yii::t('dict', 'Header'),
            'HeaderForm' => Yii::t('dict', 'Header'),
            'DaysOfWeek' => Yii::t('dict', 'Days of the week'),
            'Hour' => Yii::t('dict', 'Hour'),
            'MaxHour' => Yii::t('dict', 'Max hour'),
            'Repeat' => Yii::t('dict', 'Repeat'),
            'FrequencyHours' => Yii::t('dict', 'Frequency hours'),
            'Action' => Yii::t('dict', 'Action'),
            'ActionForm' => Yii::t('dict', 'Action'),
            'Options' => Yii::t('dict', 'Options'),
            'State' => Yii::t('dict', 'State'),
            'AddedDate' => Yii::t('dict', 'Creation date'),
            'Enabled' => Yii::t('dict', 'Enable the task immediately after creation'),
            'PushSendedCount' => Yii::t('dict', 'Push sended'),
            'PushClickCount' => Yii::t('dict', 'Push clicks'),
            'CountriesForm' => Yii::t('dict', 'Countries'),
            'CarrierType' => Yii::t('dict', 'CarrierType'),
            'Note' => Yii::t('dict', 'Note'),
        ];
    }

    public function attributeHints()
    {
        return [
            'FrequencyHours' => 'Через сколько часов можно повторить этот пуш для устройства',
            'Hour' => 'В какой час времени (по устройству) отправлять пуш',
            'MaxHour' => 'До какого часа времени (по устройству) можно отправлять пуш'
        ];
    }


}
