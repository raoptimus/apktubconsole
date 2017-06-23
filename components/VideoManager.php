<?php
namespace app\components;

use Yii;
use yii\base\Component;
use app\models\video\VideoTaskForm;
use yii\data\ArrayDataProvider;
use app\components\tubeserver\v1\Connection;

/**
 * host and port is defined via config file
 * see jsonrpc component
 */
class VideoManager extends Component
{
    public function createTask(VideoTaskForm $form)
    {
        return $this->call("manager.CreateTask", $form);
    }

    public function retryTask($id)
    {
        return $this->call("manager.RetryTask", $id);
    }

    public function killTask($id)
    {
        return $this->call("manager.KillTask", $id);
    }

    public function restartTask($id)
    {
        return $this->call("manager.RestartTask", $id);
    }

    public function update()
    {
        return $this->call("manager.Update");
    }

    //TODO: use actual pagination
    public function getTaskList($state, $limit = 20, $offset = 0)
    {
        $this->update();

        $params = ["State" => $state, "Limit" => $limit, "Offset" => $offset];
        $list = $this->call("manager.GetTaskList", $params);

        return new ArrayDataProvider([
            "allModels" => $list,
            "key" => "Id",
        ]);
    }

    private function call($method, $params = null) {
        $conn = Yii::$app->jsonrpc;
        return $conn->sendRequest($method, [$params]);
    }
}