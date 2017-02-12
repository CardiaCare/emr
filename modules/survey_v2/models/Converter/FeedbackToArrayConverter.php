<?php

namespace app\modules\survey_v2\models\Converter;

use app\modules\survey_v2\models\Feedback;
use app\modules\survey_v2\models\Respond;
use app\modules\survey_v2\models\Response;
use app\modules\survey_v2\models\ResponseItem;
use yii\helpers\ArrayHelper;

class FeedbackToArrayConverter
{
    /**
     * @param Feedback $feedback
     * @param bool $deep
     * @return array
     */
    public function convert(Feedback $feedback, $deep = true)
    {
        return ArrayHelper::toArray($feedback, [
            Feedback::className() => $feedback->fields() + ($deep ? [
                    'responds' => function (Feedback $model) {
                        return ArrayHelper::toArray($model->responds, [
                            Respond::className() => [
                                'id',
                                'feedback_id',
                                'question_id',
                                'responses' => function (Respond $model) {
                                    return $this->convertResponseToArray($model->responses);
                                }
                            ]
                        ]);
                    },
                ] : []),
        ]);
    }

    /**
     * @param Response[] $responses
     * @return array
     */
    private function convertResponseToArray($responses)
    {
        return ArrayHelper::toArray($responses, [
            Response::className() => [
                'id',
                'answer_id',
                'responseText' => 'text',
                'responseFile' => 'response_file_id',
                'responseItems' => function (Response $model) {
                    return ArrayHelper::toArray($model->responseItems, [
                        ResponseItem::className() => [
                            'id',
                            'responseScore' => 'score',
                            'answer_item_id',
                            'subResponses' => function (ResponseItem $model) {
                                return $this->convertResponseToArray($model->subResponses);
                            }
                        ]
                    ]);
                }
            ]
        ]);
    }
}
