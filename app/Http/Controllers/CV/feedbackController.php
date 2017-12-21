<?php
/**
 * Created by PhpStorm.
 * User: QuispeRoque
 * Date: 18/04/17
 * Time: 13:16
 */

namespace CVAdmin\Http\Controllers\CV;


use CVAdmin\CV\Repos\FeedbackRepo;
use CVAdmin\Http\Controllers\Controller;
use Illuminate\Http\Request;

class feedbackController extends Controller
{
    public $request;
    public $repo;

    public function __construct(Request $request, FeedbackRepo $feedbackRepo)
    {
        $this->request = $request;
        $this->repo = $feedbackRepo;
    }

    function getFeedback()
    {
        $rpta = (new FeedbackRepo)->getFeedback($this->request->all());
        if ($rpta['load']) {
            return $rpta;
        } else {
            return response()->json($rpta, 412);
        }
    }



}