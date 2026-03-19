<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2020-01-30 14:40
 */

namespace common\services;

interface ArticleServiceInterface extends ServiceInterface
{
    const ServiceName = 'articleService';

    const ScenarioArticle = "article";

    public function newArticleContentModel(array $options= []);

    public function getArticleContentDetail($id, array $options = []);

    public function getFlagHeadLinesArticles($limit, $sort = SORT_DESC);


    public function getArticleById($aid);

    public function getArticlesCountByPeriod($startAt=null, $endAt=null);

    public function getFrontendURLManager();

    public function getSinglePages();

    public function state($id, array $postData, array $options=[]);
}