<?php
namespace OroCMS\Admin\Contracts;

interface EntityRepositoryInterface
{
    /**
     * Delete a specified data by given data id.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id);

    /**
     * Create a new data.
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data);

    /**
     * Get all data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Find data by given an identifier.
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findById($id);

    /**
     * Find data by specified column name and value.
     *
     * @param string $key
     * @param string $value
     * @param string $operator
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findBy($key, $value, $operator = '=');

    /**
     * Return paginated data.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function paginate($data);
}
