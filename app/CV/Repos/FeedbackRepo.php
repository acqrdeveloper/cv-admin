<?php
/**
 * Created by PhpStorm.
 * User: QuispeRoque
 * Date: 18/04/17
 * Time: 13:26
 */

namespace CVAdmin\CV\Repos;


use CVAdmin\Common\Repos\QueryRepo;
use CVAdmin\CV\Models\Feedback;
use CVAdmin\Http\Controllers\Controller;
use CVAdmin\User;
use Illuminate\Support\Facades\DB;
use PDOException;

class FeedbackRepo extends Controller
{

    function getFeedback($getparams)
    {
        return (new QueryRepo)->Q_feedback($getparams);
    }


}