<?php

namespace app\models;

use app\database\Database;
use app\models\Question;
use app\models\Variant;
use app\repository\QuestionRepository;

class QuestionFactory
{

    /**
     * Create Question object from raw http request
     *
     * @param array|null $data
     */
    public function createQuestionFromRawData(array $data): Question
    {
        $questionTitle = $data['question']['title'] ?? null;

        $question = new Question($questionTitle);

        $variantsRaw = $data['variants'] ?? [];

        foreach ($variantsRaw as $item) {
            $variant = new Variant($item);

            if ($variant->validate()){
                $question->addVariant($variant);
            }
        }

        $question->save();

        return $question;
    }

    /**
     * Create from any UID
     *
     * @param $uid
     * @return \app\models\Question|null
     */
    public function createFromUid($uid): ?Question
    {
        $question = Question::findByUid($uid);

        if ($question) {
            $question->loadVariants();
        }

        return $question;
    }
}