<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\BackendController;
use App\Models\Kurir as Model;

class KurirController extends BackendController
{
    public function __construct(Model $model, $base = 'kurir')
    {
        parent::__construct($model, $base);
        $this->breadcrumb2Icon  = 'truck';
        $this->fields           = array_except($this->model->getFields(), ['id']);

        $this->view->share();
    }
}
