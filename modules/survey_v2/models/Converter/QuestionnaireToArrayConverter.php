<?php

namespace app\modules\survey_v2\models\Converter;

use app\modules\survey_v2\models\Questionnaire;
use yii\helpers\ArrayHelper;
use app\modules\survey_v2\models\Question;
use app\modules\survey_v2\models\Answer;
use app\modules\survey_v2\models\AnswerItem;

class QuestionnaireToArrayConverter
{
    /**
     * @param Questionnaire $questionnaire
     * @param bool $deep
     * @return array
     */
    public function convert(Questionnaire $questionnaire, $deep = true)
    {
        return ArrayHelper::toArray($questionnaire, [
            Questionnaire::className() => $questionnaire->fields() + ($deep ? [
                'questions' => function (Questionnaire $model) {
                    return ArrayHelper::toArray($model->questions, [
                        Question::className() => [
                            'id',
                            'description',
                            'uri',
                            'answers' => function (Question $model) {
                                return $this->convertAnswersToArray($model->answers);
                            }
                        ]
                    ]);
                },
            ] : []),
        ]);
    }

    /**
     * @param Answer[] $answers
     * @return array
     */
    private function convertAnswersToArray($answers)
    {
        return ArrayHelper::toArray($answers, [
            Answer::className() => [
                'id',
                'uri',
                'type' => function (Answer $model) {
                    return $model->answerType->description;
                },
                'items' => function (Answer $model) {
                    return ArrayHelper::toArray($model->items, [
                        AnswerItem::className() => [
                            'id',
                            'itemText' => 'text',
                            'itemScore' => 'score',
                            'uri',
                            'subAnswers' => function (AnswerItem $model) {
                                return $this->convertAnswersToArray($model->subAnswers);
                            }
                        ]
                    ]);
                }
            ]
        ]);
    }
}
