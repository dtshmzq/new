<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2020-02-02 19:57
 */

namespace backend\models\search;


interface SearchInterface
{
    public function search(array $params = [], array $options = []);
}