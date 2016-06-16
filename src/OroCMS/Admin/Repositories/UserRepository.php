<?php
namespace OroCMS\Admin\Repositories;

use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use OroCMS\Admin\Entities\Role;
use OroCMS\Admin\Transformers\UserTransformer;
use OroCMS\Admin\Contracts\EntityRepositoryInterface;
use Illuminate\Support\Facades\Request;

class UserRepository implements EntityRepositoryInterface
{
    public function getModel($with_trashed = false)
    {
        $model_class = config('admin.models.user');
        $model = new $model_class;

        return $with_trashed ? $model->withTrashed() : $model;
    }

    public function create(array $data)
    {
        return $this->getModel()->create($data);
    }

    public function delete($id, $force_delete = false)
    {
        $user = $this->findById($id);
        return $force_delete ? $user->forceDelete() : $user->delete();
    }

    public function restore($id)
    {
        $article = $this->findById($id);
        $article and $article->restore();

        return $article;
    }

    public function perPage()
    {
        return Request::get('limit', config('admin.user.perpage', 10));

    }

    public function getAll()
    {
        return $this->search( Request::get('search') );
    }

    public function search($context = null)
    {
        $sort = Request::get('sort', 'created_at');
        $sort_dir = Request::get('order', 'desc');

        $resource = $this->getModel(true)
            ->with('roles')
            ->orderBy($sort, $sort_dir);

        if (!empty($context)) {
            $search = "%{$context}%";

            $resource->where(function($query) use($search) {
                $query->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        // publishing
        if (Request::has('published')) {
            $published = Request::get('published');

            $resource->where('published', $published)
                ->whereNull('deleted_at');
        }

        // get deleted
        if ($is_deleted = Request::get('deleted', null)) {
            $resource->whereNotNull('deleted_at');
        }

        return $this->paginate($resource);
    }

    public function findById($id)
    {
        return $this->getModel(true)->findorFail($id);
    }

    public function findBy($key, $value, $operator = '=')
    {
        return $this->getModel(true)->where($key, $operator, $value)->findorFail();
    }

    public function paginate($data) 
    {
        $limit = $this->perPage();

        $paginator = $data->paginate($limit);
        $items = $paginator->getCollection();

        $resource = new Collection($items, new UserTransformer);

        $resource = $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return (new Manager())->createData($resource)->toArray();      
    }
}
