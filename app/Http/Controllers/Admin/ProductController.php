<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductUpdatedEvent;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductCreateRequest;
use App\Jobs\ProductCreated;
use App\Jobs\ProductDeleted;
use App\Jobs\ProductUpdated;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class ProductController
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $this->userService->allows('view', 'products');

        $products = Product::paginate();

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $this->userService->allows('view','products');

        return new ProductResource(Product::find($id));
    }

    public function store(ProductCreateRequest $request)
    {
        $this->userService->allows('edit', 'products');

        $product = Product::create($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdatedEvent());

        ProductCreated::dispatch($product->toArray())->onQueue('checkout_queue');

        return response($product, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $this->userService->allows('edit', 'products');

        $product = Product::find($id);

        $product->update($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdatedEvent());

        ProductUpdated::dispatch($product->toArray())->onQueue('checkout_queue');

        return response($product, Response::HTTP_ACCEPTED);
    }

    public function destroy($id)
    {
        $this->userService->allows('edit', 'products');
        
        Product::destroy($id);

        ProductDeleted::dispatch($id)->onQueue('checkout_queue');

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
