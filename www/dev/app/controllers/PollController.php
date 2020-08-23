<?php

namespace app\controllers;

use app\models\Answer;
use app\models\Question;
use app\models\Variant;
use app\request\Request;
use app\service\Registry;

class PollController extends RestController
{
    /**
     * @OA\Post(
     *   path="/poll/vote",
     *     tags={"Poll"},
     *     summary="Save vote of user",
     *   @OA\Response(
     *     response=400,
     *     description="Error"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *     @OA\RequestBody(
     *         description="Vote information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="questionUid",
     *                     description="UID of question",
     *                     type="string",
     *                     example="15981979755f4290d7d1041"
     *                 ),
     *                 @OA\Property(
     *                     property="variantId",
     *                     description="UID of question",
     *                     type="integere",
     *                     example=315
     *                 ),
     *                 @OA\Property(
     *                     property="userName",
     *                     description="User name",
     *                     type="string",
     *                     example="Steve Jobs"
     *                 )
     *              )
     *          )
     *     )
     * )
     */
    public function actionVote()
    {
        $questionUid = Request::post('questionUid');
        $variantId = Request::post('variantId');

        $user = Registry::get('user');
        $userName = $user->getName();

        if (empty($userName)){
            return $this->responseBadRequest('User Name is required');
        }

        $question = Question::findByUid($questionUid);

        if (empty($question)){
            return $this->responseBadRequest('Question not found');
        }

        $question->loadVariants();

        $variant = $question->getVariantById($variantId);

        if (empty($question) || empty($variant)) {
            return $this->responseBadRequest('Question UID or variant ID is incorrect');
        }

        $answer = new Answer();
        $answer->setQuestion($question);
        $answer->setUser($user);
        $answer->setVariant($variant);
        $result = $answer->save();

        return $result
            ? $this->response(['variant' => $variant->toArray()])
            : $this->responseBadRequest('You have already voted for question "'.$question->getTitle().'"');
    }
}