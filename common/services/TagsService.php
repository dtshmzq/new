<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2020-01-23 11:40
 */

namespace common\services;

use common\models\Comment;
use common\models\Tags;
use backend\models\search\TagsSearch;

class TagsService extends Service implements TagsServiceInterface
{
    public function getSearchModel(array $options=[])
    {
        return new TagsSearch();
    }

    public function getModel($id, array $options = [])
    {
        return Tags::findOne($id);
    }

    public function newModel(array $options = [])
    {
        return new Tags();
    }

    public function getCountByPeriod($startAt=null, $endAt=null)
    {
        $model = Tags::find();
        if( $startAt != null && $endAt != null ){
            $model->andWhere(['between', 'created_at', $startAt, $endAt]);
        }else if ($startAt != null){
            $model->andwhere([">", 'created_at', $startAt]);
        } else if($endAt != null){
            $model->andWhere(["<", "created_at", $endAt]);
        }
        return $model->count('id');
    }
}