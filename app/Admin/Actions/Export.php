<?php

namespace App\Admin\Actions;

use QuarkCMS\QuarkAdmin\Actions\Link;

class Export extends Link
{
    /**
     * 设置按钮类型,primary | ghost | dashed | link | text | default
     *
     * @var string
     */
    public $type = 'primary';

    /**
     * 导出数据表头
     *
     * @var array
     */
    public $exportTitles = [];

    /**
     * 导出数据字段
     *
     * @var array
     */
    public $exportFields = [];

    /**
     * 初始化
     *
     * @param  string  $name
     * 
     * @return void
     */
    public function __construct($name = null, $exportTitles = [], $exportFields = [])
    {
        if($name === '批量导出') {
            $this->type = 'link';
            $this->size = 'small';
        }

        $this->name = $name ? $name : '导出数据';
        $this->exportTitles = $exportTitles;
        $this->exportFields = $exportFields;
    }

    /**
     * 跳转链接
     *
     * @return string
     */
    public function href()
    {
        if($this->name === '批量导出') {
            return '/api/'.$this->api().'export_ids=${id}&token='.get_token();
        } else {
            return '/api/'.$this->api().'token='.get_token();
        }
    }

    /**
     * 相当于 a 链接的 target 属性，href 存在时生效
     *
     * @return string
     */
    public function target()
    {
        return '_blank';
    }

    /**
     * 执行行为
     *
     * @param  Fields  $fields
     * @param  Collection  $model
     * @return mixed
     */
    public function handle($fields, $model)
    {
        $exportIds = $fields->input('export_ids');
        
        if($exportIds) {
            $model = $model->whereIn('id', explode(',',$exportIds));
        }

        $lists = $model->orderBy('id','desc')
        ->select(...$this->exportFields)
        ->get()
        ->toArray();

        return export('data', $this->exportTitles, $lists);
    }
}