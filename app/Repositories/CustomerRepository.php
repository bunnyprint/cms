<?php

namespace App\Repositories;

use App\Models\Customer;
use DB;

class CustomerRepository
{
    /**
     * @param $input
     * @return mixed
     */
    public function getOne($input)
    {
        return Customer::filter($input)->first();
    }

    /**
     * @param array $filter
     * @param array $sortBy
     * @param bool $pagination
     * @return mixed
     */
    public function all($filter = [], $sortBy = [], $pagination = false)
    {

        $query = Customer::query();
        if (!empty($filter)) {
            $query->filter($filter);
        }
        if (!empty($sortBy)) {
            $query->sortBy($sortBy);
        }
        // dd($query->get());
        if ($pagination) {
            return $query->paginate($pagination);
        }

        return $query->get();
    }

    /**
     * @param $input
     * @return Customer
     */
    public function create($input)
    {
        $model = new Customer();
        $model->fill($input);
        // $model->profile_id = $user->profile_id;
        $model->save();
        return $model;
    }

    /**
     * @param Customer $model
     * @param $input
     * @return Customer
     */
    public function update(Customer $model, $input)
    {
        $model->fill($input);
        $model->save();
        return $model;
    }
}
