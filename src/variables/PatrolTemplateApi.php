<?php
namespace selvinortiz\Patrol\variables;

use selvinortiz\patrol\Patrol;

class PatrolTemplateApi
{
    /**
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\HttpException
     */
    public function watch()
    {
        Patrol::getInstance()->defaultService->watch();
    }
}
