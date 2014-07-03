<?php


namespace frontend\replay;


use frontend\controllers\CompetitionController;
use common\models\ExecutionRecord;
use yii\base\Component;

/**
 * Class BaseReplayHandler
 * @package frontend\replay
 * @property CompetitionController $controller
 */
abstract class BaseReplayHandler extends Component
{
    /**
     * @param CompetitionController $controller
     */
    protected $controller;

    public function  __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param ExecutionRecord $record
     * @return mixed
     */
    public abstract function translateReplay($record);

    /**
     * @param ExecutionRecord $record
     * @return mixed
     */
    public abstract function handleReplay($record);
}
