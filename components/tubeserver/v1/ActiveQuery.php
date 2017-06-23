<?php
namespace app\components\tubeserver\v1;

use yii\base\Component;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveQueryTrait;
use yii\db\ActiveRelationTrait;
use yii\db\QueryTrait;

class ActiveQuery extends Component implements ActiveQueryInterface
{
    use QueryTrait;
    use ActiveQueryTrait;
    use ActiveRelationTrait;

    /**
     * @event Event an event that is triggered when the query is initialized via [[init()]].
     */
    const EVENT_INIT = 'init';


    /**
     * Constructor.
     * @param array $modelClass the model class associated with this query
     * @param array $config configurations to be applied to the newly created query object
     */
    public function __construct($modelClass, $config = [])
    {
        $this->modelClass = $modelClass;
        parent::__construct($config);
    }

    /**
     * Initializes the object.
     * This method is called at the end of the constructor. The default implementation will trigger
     * an [[EVENT_INIT]] event. If you override this method, make sure you call the parent implementation at the end
     * to ensure triggering of the event.
     */
    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

      /**
     * Executes the query and returns all results as an array.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return array the query results. If the query results in nothing, an empty array will be returned.
     */
    public function all($db = null)
    {
        $req = new Request();
        $req->Sort = $this->composeSort();
        $req->Query = $this->where;
        $req->Page = $this->composePage();
        $modelClass = $this->modelClass;
        if (is_null($db)) {
            $db = $modelClass::getDb();
        }
        $method = $modelClass::getControllerName() . ".List";
        $rows = $db->sendRequest($method, [$req]);
        return $this->populate($rows);
    }

    /**
     * Executes the query and returns a single row of result.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return array|boolean the first row (in terms of an array) of the query result. False is returned if the query
     * results in nothing.
     */
    public function one($db = null)
    {
        $req = new Request();
        $req->Query = $this->where;
        $modelClass = $this->modelClass;
        if (is_null($db)) {
            $db = $modelClass::getDb();
        }
        $method = $modelClass::getControllerName() . ".One";
        $row = $db->sendRequest($method, [$req]);
        if ($row !== false) {
            $models = $this->populate([$row]);
            return reset($models) ?: null;
        } else {
            return null;
        }
    }

    /**
     * Returns the number of records.
     * @param string $q the COUNT expression. Defaults to '*'.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return integer number of records.
     */
    public function count($q = '*', $db = null)
    {
        // TODO: Implement count() method.
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return boolean whether the query result contains any row of data.
     */
    public function exists($db = null)
    {
        // TODO: Implement exists() method.
    }

    /**
     * @return Page page specification
     */
    private function composePage()
    {
        return new Page($this->offset, $this->limit);
    }

    /**
     * Composes sort specification from raw [[orderBy]] value.
     * @return SortInfo sort specification.
     */
    private function composeSort()
    {
        foreach ($this->orderBy as $fieldName => $sortOrder) {
            $direct = ($sortOrder === SORT_DESC)
                ? SortInfo::DIRECT_DESC
                : SortInfo::DIRECT_ASC;
            return new SortInfo($fieldName, $direct);
        }
        return null;
    }

    /**
     * Converts the raw query results into the format as specified by this query.
     * This method is internally used to convert the data fetched from MongoDB
     * into the format as required by this query.
     * @param array $rows the raw query result from MongoDB
     * @return array the converted query result
     */
    public function populate($rows)
    {
        if (empty($rows)) {
            return [];
        }

        $models = $this->createModels($rows);
        if (!empty($this->with)) {
            $this->findWith($this->with, $models);
        }
        if (!$this->asArray) {
            foreach ($models as $model) {
                $model->afterFind();
            }
        }

        return $models;
    }
}