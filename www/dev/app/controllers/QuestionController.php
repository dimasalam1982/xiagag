<?php

namespace app\controllers;

use app\models\Question;
use app\models\QuestionFactory;
use app\request\Request;
use app\service\Registry;
use app\models\Answer;
use app\repository\AnswerRepository;

class QuestionController extends RestController
{
    /**
     * @OA\Post(
     *   path="/question/create",
     *     tags={"Question"},
     *     summary="Create new question",
     *   @OA\Response(
     *     response=400,
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Return information about question created"
     *   ),
     *     @OA\RequestBody(
     *         description="Question information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="question",
     *                     description="Main information about question",
     *                     type="object",
     *                     @OA\Property(
     *                       property="title",
     *                       description="Title of question",
     *                          type="string",
     *                          example="How old are you?"
     *                      )
     *                 ),
     *                 @OA\Property(
     *                     property="variants",
     *                     type="array",
     *                       @OA\Items(
     *                         allOf={@OA\Schema(ref="#/components/schemas/Variant")}),
     *                       )
     *                 )
     *          )
     *     )
     * )
     */
    public function actionCreate()
    {
        $questionFactory = new QuestionFactory;

        $data = Request::post();

        if (!is_array($data)) {
            return $this->responseBadRequest('Empty income data');
        }

        $question = $questionFactory->createQuestionFromRawData($data);

        if (is_null($question)) {
            return $this->responseBadRequest('Error occurred while creating Question');
        }

        if (empty($question->getVariants())){
            return $this->responseBadRequest('Can not set answers to question');
        }

        if (!$question->validate()){
            return $this->responseBadRequest(['errors' => $question->getErrors()]);
        }

        return $this->response(['question' => $question->toArray()]);
    }

    /**
     * @OA\Get(
     *   path="/question/show/{questionUid}",
     *     tags={"Question"},
     *      summary="Summary data to display question page. Method also returns information about how current user voted to this question",
     *   @OA\Parameter(
     *       name="questionUid",
     *       in="path",
     *       description="Question UID",
     *       required=true,
     *       @OA\Schema(
     *           type="string"
     *       )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Summary data to display question page"
     *   )
     * )
     */
    public function actionShow(string $questionUid)
    {
        $question = (new QuestionFactory)->createFromUid($questionUid);

        if (!$question) {
            return $this->responseBadRequest('Invalid question UID');
        }

        $user = Registry::get('user');

        $variant = Answer::getUserVote($user, $question);

        return $this->response([
            'question' => $question->toArray(),
            'user' => [
                'vote' => $variant ? $variant->toArray() : null,
                'name' => $user->getName()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *   path="/question/statistic/{questionUid}",
     *     tags={"Question"},
     *   summary="Poll statistic for Question",
     *   @OA\Parameter(
     *       name="questionUid",
     *       in="path",
     *       description="Question UID",
     *       required=true,
     *       @OA\Schema(
     *           type="string"
     *       )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Statistic data"
     *   )
     * )
     */
    public function actionStatistic($questionUid)
    {
        $question = Question::findByUid($questionUid);

        $statistic = $question
            ? AnswerRepository::getStatisticForQuestion($question->getId())
            : [];

        if (empty($question)){
            return $this->responseBadRequest('Question not found');
        }

        $question->loadVariants();

        return $this->response([
            'question' => $question->toArray(),
            'stat' => $statistic
        ]);
    }

}
