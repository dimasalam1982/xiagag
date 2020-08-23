<?php

namespace app\repository;

use app\database\Database;

class AnswerRepository
{
    public static function getStatisticForQuestion($questionId)
    {
        $data = (Database::getInstance())
            ->getAll('
                SELECT
                    u.name,
                    a.variant_id as variantId
                FROM
                    answer a
                    JOIN `user` u ON u.id=a.user_id
                WHERE
                    a.question_id=:question_id
            ',[
                'question_id' => $questionId
            ]);

        return $data;
    }
}
