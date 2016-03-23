<?php

namespace App\liveCMS\Controllers;

use Form;
use Datatables;
use ReflectionClass;
use App\Http\Requests;
use Illuminate\Http\Request;

use App\liveCMS\Models\Contracts\BaseModelInterface as Model;

class BackendController extends BaseController
{
    protected $model;
    protected $base;
    protected $baseClass;


    public function __construct(Model $model, $base = 'base')
    {
        parent::__construct();

        $this->model = $model;
        $this->base = $base;
        $reflection = new ReflectionClass($this);
        $this->baseClass = '\\'.$reflection->getName();

        $this->fields           = $this->model->getFields();
        $this->breadcrumb2      = title_case(snakeToStr($this->base));
        // $this->breadcrumb2Url   = route($this->baseClass.'.index');
        
        $this->view->share();
    }

    protected function processDatatables($datatables)
    {
        return $datatables;
    }

    protected function processRequest($request)
    {
        return $request;
    }

    protected function loadFormClasses()
    {
        //
    }

    protected function afterSaving($request)
    {
        return $this->model;
    }

    public function index()
    {
        $this->title        = title_case(snakeToStr($this->base));
        $this->description  = 'Semua Daftar '.title_case(snakeToStr($this->base));
        $this->breadcrumb3  = 'Lihat Semua';

        $this->view->share();

        return view('partials.appIndex');
    }

    protected function getDataFields()
    {
        return [null => $this->model->getKeyName()]+$this->model->getFillable();
    }

    protected function beforeDatatables($datas)
    {
        return $datas;
    }

    public function data()
    {
        $datas = $this->model->select($this->getDataFields());
        
        $datas = $this->beforeDatatables($datas);

        $datatables = Datatables::of($datas)
            ->addColumn('menu', function ($data) {
                return
                    '<a href="'.action($this->baseClass.'@edit', [$data->{$this->model->getKeyName()}]).'" 
                        class="btn btn-small btn-link">
                            <i class="fa fa-xs fa-pencil"></i> 
                            Edit
                    </a> '.
                    Form::open(['style' => 'display: inline!important', 'method' => 'delete', 
                        'action' => [$this->baseClass.'@destroy', $data->{$this->model->getKeyName()}]
                    ]).
                    '  <button type="submit" onClik="return confirm(\'Yakin mau menghapus?\');" 
                        class="btn btn-small btn-link">
                            <i class="fa fa-xs fa-trash-o"></i> 
                            Delete
                    </button>
                    </form>';
            });
        $datatables = $this->processDatatables($datatables);
        $result = $datatables
            ->make(true);

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = $this->model;
        ${camel_case($this->base)} = $model;

        $this->title        = 'Tambah Data '.title_case(snakeToStr($this->base));
        $this->description  = 'Untuk menambahkan data '.snakeToStr($this->base);
        $this->breadcrumb3  = 'Tambah';
        $this->action       = 'store';

        $this->view->share();

        $this->loadFormClasses();

        return view("admin.".camel_case($this->base).".form", compact(camel_case($this->base)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request = $this->processRequest($request);

        $this->validate($request, $this->model->rules());

        $this->model = $this->model->create($request->all());

        $saved = $this->afterSaving($request);

        if ($saved) {
            return redirect()->action($this->baseClass.'@index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = $this->model->findOrFail($id);
        ${camel_case($this->base)} = $model;

        $this->title        = 'Edit '.title_case(snakeToStr($this->base));
        $this->description  = 'Mengedit data '.snakeToStr($this->base);
        $this->breadcrumb3  = 'Edit';
        $this->action       = 'update';
        $this->params       = compact('id');
        
        $this->view->share();
        
        $this->loadFormClasses();

        return view("admin.".camel_case($this->base).".form", compact(camel_case($this->base)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->model = $this->model->findOrFail($id);

        $request = $this->processRequest($request);

        $this->validate($request, $this->model->rules());

        $this->model->update($request->all());

        $saved = $this->afterSaving($request);

        if ($saved) {
            return redirect()->action($this->baseClass.'@index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->model = $this->model->findOrFail($id);

        $deleted = $this->model->delete();

        if ($deleted) {
            return redirect()->action($this->baseClass.'@index');
        }
    }
}
