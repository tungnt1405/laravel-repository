# Laravel Repository

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](https://github.com/tungnt1405/laravel-repository/blob/main/LICENSE)

Repository setup inspired by the theanik/laravel-more-command package. This package is an extended, adjusted (but entirely independent) version of that, with its own interfaces.

## Install

Via Composer
``` bash
composer require tungnt/laravel-repository --dev
```
Or add the following to your composer.json's require-dev section and composer update
``` json 
"require-dev": {
    "tungnt/laravel-repository": "^0.0.3"
}
```

## Publish Package Configuration

``` bash
php artisan vendor:publish --provider="Tungnt\LaravelRepository\RepositoryServiceProvider" --tag="tungnt/config" --tag="tungnt/Repositories"
```

## To Change Default Namespace [config/repository.php]

``` php
<?php
return [
    'repository-namespace' => 'App', // Your Desire Namespace for Repository Classes
    'service-namespace' => 'App', // Your Desire Namespace for Service Classes
]
```

## Basic Usage

Create a repository Class.
``` bash
php artisan make:repository your-repository-name
```
Example:
``` bash
php artisan make:repository UserRepository
```
or
``` bash
php artisan make:repository User
```

Create a repository Class in the directory.
``` bash
php artisan make:repository folder/your-repository-name
```

Example:
``` bash
php artisan make:repository User/UserRepository
```

or
``` bash
php artisan make:repository User/User
```
The above will create a **Repositories** directory inside the **App** directory.\

__Create a repository with Interface.__
``` bash
php artisan make:repository UserRepository -i
```

or
``` bash
php artisan make:repository User/UserRepository -i
```

Here you need to put extra `-i` flag.
The above will create a **Repositories** directory inside the **App** directory.

__Create repository with Model.__

``` bash
php artisan make:repository UserRepository -m
```

or
``` bash
php artisan make:repository User/UserRepository -m
```

Here you need to put extra `-m` flag.

Or you can create repository with Model and Interface:
``` bash
php artisan make:repository User/UserRepository -mi
```

__Create repository resource.__

``` bash
php artisan make:repository UserRepository --resource
```

__An Example of created repository class:__

File repository
``` php
<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\User::class;
    }

    public function getUser()
    {
        return [];
    }
}
```

File Interface
``` php
<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getUser();
}
```

File Controller
``` php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface|\App\Repositories\Repository
     */
    protected $userRepo;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->userRepo = $user;
    }


    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $user = $this->userRepo->getAll();

        return view('', ['user' => $user]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        //... Validation here

        $user = $this->userRepo->create($data);

        return redirect()->route('');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->userRepo->find($id);

        return view('', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $data = $request->all();

        //... Validation here

        $user = $this->userRepo->update($id, $data);

        return redirect()->route('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->userRepo->delete($id);
        
        return redirect()->route('');
    }
}
```

## License

The MIT License (MIT). Please see [License](LICENSE) for more information.