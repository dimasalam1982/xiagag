<?php

namespace app\models;

use app\database\Database;
use app\models\BaseModel;
use app\models\User;
use app\models\Question;
use app\models\Variant;

class Answer extends BaseModel
{
    protected $user;
    protected $question;
    protected $variant;

    public function save()
    {
        if (self::getUserVote($this->user, $this->question)) {
            return null;
        }

        $insertId = (Database::getInstance())
            ->insert('INSERT INTO answer SET user_id=:user_id, question_id=:question_id, variant_id=:variant_id', [
                'user_id' => $this->user->getId(),
                'question_id' => $this->question->getId(),
                'variant_id' => $this->variant->getId()
            ]);

        return Variant::findById($this->variant->getId());
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function setVariant(Variant $variant): self
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * Find user answer for required question
     *
     * @param \app\models\User $user
     * @param \app\models\Question $question
     * @return \app\models\Variant|null
     */
    public static function getUserVote(User $user, Question $question): ?Variant
    {
        $variantId = (Database::getInstance())
            ->getRow('SELECT variant_id FROM answer WHERE user_id=:user_id AND question_id=:question_id', [
                'user_id' => $user->getId(),
                'question_id' => $question->getId()
            ]);

        return $variantId ? Variant::findById($variantId['variant_id']) : null;
    }

}
