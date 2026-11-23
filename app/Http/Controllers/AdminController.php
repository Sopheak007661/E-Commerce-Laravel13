<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{



    public function index(){
        return view('admin.index');
    }



//1. View brand datas ======================================================================
    
    //View brand data 
        public function brands(){   
            $query = Brand::query();
            if($search = request('search')){
                $query->where('name','LIKE',"%{$search}%");
            }
            if(request() -> filled('status')){
                $query->where('status',request('status'));
            }
            $brands = $query->orderBy('id','DESC')->paginate(10)->withQueryString();
            return view('admin.brands',compact('brands'));
        }

    //View add-band layout
        public function brandAdd(){
            $parentCategories = Category::where('parent_id',null)->orderBy('name','ASC')->get();
            return view('admin.brand-add' ,compact('parentCategories'));
        }

    //Add brands datas
        public function brandStore(Request $request){
            $request->validate([
                'name'  => 'required|string|max:255',
                'slug'  => 'nullable|string|max:255|unique:brands,slug',
                'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            ]);
            $data = [
                'name'   => $request->name,
                'slug'   => Str::slug($request->slug ?: $request->name),
                'status' => $request->has('status') ? 1 : 0,
            ];
            if($request->hasFile('image')){
                try {
                    $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                    $this->generateThumbnailImage($request->image, $imageName, 'uploads/brands');
                    $request->image->move(public_path('uploads/brands'), $imageName);
                    $data['image'] = $imageName;
                } catch (\Throwable $e) {
                    dd($e->getMessage());
                }
            }
            Brand::create($data);
            return redirect()->route('admin.brands')->with('success','Brand added successfully!');
        }
    // 
        public function generateThumbnailImage($image, $imageName, $folder, $width = 124, $height = 124)
        {
            $thumbnailPath = public_path($folder . '/thumbnails');
            if(!file_exists($thumbnailPath)){
                mkdir($thumbnailPath, 0755, true);
            }

            $thumbnailImage = Image::decode($image->getRealPath());
            $thumbnailImage->cover($width, $height);
            $thumbnailImage->save($thumbnailPath . '/' . $imageName);

            return $folder . '/thumbnails/' . $imageName;
        }
    //View Edit brands layout

        public function brandEdit($id){
            $brand = Brand::findOrFail($id);
            return view('admin.brand-edit',compact('brand'));
        }

    //Edit brand data
        public function brandUpdate(Request $request,$id){
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:brands,slug,' .$id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'nullable|boolean',
                ]);
            $brand = Brand::findOrFail($id);
            $brand -> name = $request->name;
            $brand -> slug = $request->slug ? Str::slug($request->slug):Str::slug($request->name);
            $brand -> status = $request->has('status') ? 1 : 0;
            if($request->hasFile('image')){
                //Delete old image
                if($brand->image){
                    @unlink(public_path('uploads/brands/'.$brand->image));
                    @unlink(public_path('uploads/brands/thumbnails/'.$brand->image));
                }
                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $this->generateThumbnailImage($request->image,$imageName,'uploads/brands',124,124);
                $request->image->move(public_path('uploads/brands'),$imageName);
                $brand->image = $imageName;
            }
            $brand->save();
            return redirect()->route('admin.brands')->with('success','Brand updated successfully!');   
        }
    //Delete brand 

        public function brandDelete($id){
            $brand = Brand::findOrFail($id);
            //Delete images
            if($brand->image){
                @unlink(public_path('uploads/brands/'.$brand->image));
                @unlink(public_path('uploads/brands/thumbnails'.$brand->image));
            }

             //Delete from folder 
            $mainPath  = public_path('uploads/categories/' . $brand->image);
            $thumbPath = public_path('uploads/categories/thumbnails/' . $brand->image);

            if (File::exists($mainPath)) {
                File::delete($mainPath);
            }

            if (File::exists($thumbPath)) {
                File::delete($thumbPath);
            }


            $brand->delete();
            return back()->with('success','Brand delete successfully');
        }




//2. View Categories datas ======================================================================
  
    //View category data
        public function categories(){   
            $query = Category::with('parent');
            if ($search = request('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('parent', function ($parentQuery) use ($search) {
                        $parentQuery->where('name', 'LIKE', "%{$search}%");
                    });
                });
            }
            if (request()->filled('parent_id')) {
                $query->where('parent_id', request('parent_id'));
            }
            if (request('parent') === 'only_parents') {
                $query->whereNull('parent_id');
            } elseif (request('parent') === 'subcategories') {
                $query->whereNotNull('parent_id');
            }
            if(request() -> filled('status')){
                $query->where('status',request('status'));
            }
            $categories = $query->orderBy('id','DESC')->paginate(10)->withQueryString();
            //search parent 
            $parentCategories = Category::whereNull('parent_id')->orderBy('name', 'ASC')->get();
            return view('admin.categories',compact('categories','parentCategories'));
        }

    //View category add layout
        public function categoryAdd(){
            $parentCategories  = Category::where('parent_id',null)->orderBy('name','ASC')->get();
            return view('admin.category-add',compact('parentCategories'));
        }

    //Add category datas
        public function categoryStore(Request $request){
            $request->validate([
                'name'  => 'required|string|max:255',
                'slug'  => 'nullable|string|max:255|unique:categories,slug',
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
                'staus' => 'nullable|boolean',
            ]);
            $data = [
                'name'   => $request->name,
                'slug'   => Str::slug($request->slug ?: $request->name),
                'parent_id' => $request->parent_id,
                'status' => $request->has('status') ? 1 : 0,
            ];
            if($request->hasFile('image')){
                try {
                    $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                    $this->generateThumbnailImage($request->image, $imageName, 'uploads/categories');
                    $request->image->move(public_path('uploads/categories'), $imageName);
                    $data['image'] = $imageName;
                } catch (\Throwable $e) {
                    dd($e->getMessage());
                }
            }
            category::create($data);
            return redirect()->route('admin.categories')->with('success','category added successfully!');
        }
    //View categories edit layout

        public function categoryEdit($id){
            $category = Category::findOrFail($id);
            $parentCategories = Category::where('parent_id',null)->where('id','!=',$category->id)->orderBy('name','ASC')->get();
            return view('admin.category-edit',compact('category','parentCategories'));
        }

    //Add categories data
        public function categoryUpdate(Request $request,$id){
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:brands,slug,' .$id,
                'parent_id'=> 'nullable|exists:categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'nullable|boolean',
                ]);
            $category = Category::findOrFail($id);
            $category -> name = $request->name;
            $category -> slug = $request->slug ? Str::slug($request->slug):Str::slug($request->name);
            $category -> parent_id = $request->parent_id;
            $category -> status = $request->has('status') ? 1 : 0;
            if($request->hasFile('image')){
                //Delete old image
                if($category->image){
                    @unlink(public_path('uploads/categories/'.$category->image));
                    @unlink(public_path('uploads/categories/thumbnails/'.$category->image));
                }

                $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
                $this->generateThumbnailImage($request->image,$imageName,'uploads/categories',124,124);
                $request->image->move(public_path('uploads/categories'),$imageName);
                $category->image = $imageName;
            }
            $category->save();
            return redirect()->route('admin.categories')->with('success','Category updated successfully!');   
        }

    //Delete category 
        public function categoryDelete($id){
            $category = Category::findOrFail($id);

            //Delete images
            if($category->image){
                @unlink(public_path('uploads/categories/'.$category->image));
                @unlink(public_path('uploads/categories/thumbnails'.$category->image));
            }

            //Delete form folder 
            $mainPath  = public_path('uploads/categories/' . $category->image);
            $thumbPath = public_path('uploads/categories/thumbnails/' . $category->image);

            if (File::exists($mainPath)) {
                File::delete($mainPath);
            }

            if (File::exists($thumbPath)) {
                File::delete($thumbPath);
            }

            
            $category->delete();
            return back()->with('success','Category delete successfully');
        }
}


