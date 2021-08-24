<?php

namespace App\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Action;

class Suggest extends Action
{
    /**
     * 执行行为
     *
     * @param  Fields  $fields
     * @param  Collection  $model
     * @return mixed
     */
    public function handle($fields, $model)
    {
        // 获取参数
        $search = $fields->search;
        $type = $fields->input('type','label');
        $model = $fields->model;

        switch ($model) {
            case 'article':

                // 文章
                $query = \App\Models\Post::query()
                ->select('title as label','id as value')
                ->where('type','ARTICLE');

                if($type === 'label') {
                    if(!empty($search)) {
                        $query->where('title','like','%'.$search.'%');
                    }
                }
                break;
            case 'page':

                // 单页
                $query = \App\Models\Post::query()
                ->select('title as label','id as value')
                ->where('type','PAGE');

                if($type === 'label') {
                    if(!empty($search)) {
                        $query->where('title','like','%'.$search.'%');
                    }
                }
                break;
            default:
            
                // 文章
                $query = \App\Models\Post::query()
                ->select('title as label','id as value')
                ->where('type','ARTICLE');

                if($type === 'label') {
                    if(!empty($search)) {
                        $query->where('title','like','%'.$search.'%');
                    }
                }
                break;
        }

        if($type === 'value') {
            if(!empty($search)) {
                $query->where('id', $search);
            }
        }

        // 查询列表
        $lists = $query
        ->limit(20)
        ->get()
        ->toArray();

        return success('获取成功！','',$lists);
    }
}