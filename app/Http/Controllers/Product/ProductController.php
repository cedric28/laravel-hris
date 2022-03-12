<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;
use App\Category;
use App\Supplier;
use App\CategoryPerProduct;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Inventory::all();

        return view('product.index', [
            'products' => $products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view("product.create", [
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();

        try {
            //validate request value
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:50|unique:products,product_name',
                'generic_name' => 'required|string|max:50',
                'sku' => 'required|numeric|unique:products,sku',
                'content' => 'required|string|max:255',
                'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
                'supplier_id' => 'required|integer',
                'category_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //check current user
            $user = \Auth::user()->id;

            $originalImage = $request->file('image');
            $photo = time() . $originalImage->getClientOriginalName();

            //save data in the product table
            $product = new Inventory();
            $product->product_name = $request->product_name;
            $product->generic_name = $request->generic_name;
            $product->sku = $request->sku;
            $product->content = $request->content;
            $product->image = $photo;
            $product->supplier_id = $request->supplier_id;
            $product->creator_id = $user;
            $product->updater_id = $user;
            if ($product->save()) {
                $photoPath = public_path('images/' . $product->id . '/');

                if (!file_exists($photoPath)) {
                    mkdir($photoPath, 0777, true);
                }
                // create instance
                $img = \Image::make($originalImage->getRealPath());

                // resize image to fixed size
                $img->resize(100, 100);
                $img->save($photoPath . $photo);
            }

            $product->categories()->sync($request->category_id);
            /*
            | @End Transaction
            |---------------------------------------------*/
            \DB::commit();

            return redirect()->route('product.create')
                ->with('successMsg', 'Product Data Save Successful');
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //check schedule if exist then show the product details in show blade,
        //if fail error page will show
        $product = Inventory::withTrashed()->findOrFail($id);

        return view('product.show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //check the schedule if exist then show the product details in edit blade,
        // if fail error page will show
        $product = Inventory::withTrashed()->findOrFail($id);
        $product_category = CategoryPerProduct::where('product_id', $product->id)->pluck('category_id')->all();

        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('product.edit', [
            'product' => $product,
            'categoryId' => $product_category[0],
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
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
        \DB::beginTransaction();

        try {
            //check current User
            $user = \Auth::user();
            //check if the product data is exist,if not redirect to error page
            $product = Inventory::withTrashed()->findOrFail($id);

            //validate request value
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|unique:products,product_name,' . $product->id,
                'generic_name' => 'required|string|max:50',
                'sku' => 'required|numeric|unique:products,sku,' . $product->id,
                'content' => 'required|string',
                'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
                'category_id' => 'required|integer',
                'supplier_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            //update the product data 
            $originalImage = $request->file('image');
            $user = \Auth::user();
            $currentPhoto = $product->image;
            $photo = "";
            if ($originalImage) {
                $photo = time() . $originalImage->getClientOriginalName();
                $productPhoto = public_path('images/' . $product->id . '/') . $currentPhoto;
                $photoPath = public_path('images/' . $product->id . '/');
                if (!file_exists($productPhoto)) {
                    mkdir($photoPath, 0777, true);
                } else {
                    @unlink($productPhoto);
                }
                // create instance
                $img = \Image::make($originalImage->getRealPath());

                // resize image to fixed size
                $img->resize(100, 100);
                $img->save($photoPath . $photo);
            } else {
                $photo = $currentPhoto;
            }

            $product->product_name = $request->product_name;
            $product->generic_name = $request->generic_name;
            $product->sku = $request->sku;
            $product->content = $request->content;
            $product->image = $photo;
            $product->supplier_id = $request->supplier_id;
            $product->updater_id = $user->id;
            $product->update();

            $product->categories()->sync($request->category_id);

            \DB::commit();

            return back()->with("successMsg", "Product {$product->product_name} Update Successfully");
        } catch (\Exception $e) {
            //if error occurs rollback the data from it's previos state
            \DB::rollback();
            return back()->withErrors($e->getMessage());
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
        //delete product
        $product = Inventory::findOrFail($id);
        $product->delete();
    }
    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        \DB::beginTransaction();
        try {

            $product = Inventory::onlyTrashed()->findOrFail($id);

            /* Restore product */
            $product->restore();
            \DB::commit();

            return back()->with("successMsg", "Successfully Restore the data");
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->withErrors($e->getMessage());
        }
    }
}
