<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->get();
        $companies->transform(function ($company) {
            return $company->append(['country_name','industry_name']);
        });

        return response()->json([
            'data' => $companies,
            'success' => true,
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'contact_email' => 'nullable', // email
            'address' => 'nullable',
            'city' => 'nullable',
            'country' => 'nullable',
            'state' => 'nullable',
            'industry' => 'nullable',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());
                $path = $image->storeAs('images', $filename, 'public');
                $imagePaths[] = $path;
            }
        }

        // missing contact number

        $company = Company::create([
            'name' => $validated['name'],
            'contact_email' => $validated['contact_email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'state' => $validated['state'] ?? null,
            'industry' => $validated['industry'] ?? null,
            'image_paths' => $imagePaths,
        ]);

        // Company::create($validated);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'contact_email' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'industry' => 'nullable',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        
        $imagePaths = $company->image_paths ?? []; // Keep existing images

       if ($request->hasFile('images')) {
            // 🧹 Delete old images first (if you want to replace)
            foreach ($imagePaths as $path) {
                if (\Storage::disk('public')->exists($path)) {
                    \Storage::disk('public')->delete($path);
                }
            }

            $imagePaths = []; // Reset and store new ones

            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());
                $path = $image->storeAs('images', $filename, 'public');
                $imagePaths[] = $path;
            }
        }

        $company->update([
            'name' => $validated['name'],
            'contact_email' => $validated['contact_email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'industry' => $validated['industry'] ?? null,
            'image_paths' => $imagePaths,
        ]);

        return response()->json(['success' => true]);
    }

    public function delete(Company $company)
    {
        // Delete associated images if any
        if (!empty($company->image_paths) && is_array($company->image_paths)) {
            foreach ($company->image_paths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $company->delete();

        return response()->json(['success' => true]);
    }

    public function getCompany(Request $request)
    {
        $company = Company::findOrFail($request->id);

        return response()->json(['company' => $company]);
    }

    public function search(Request $request)
    {
        $companies = Company::when($request->name, function($query) use ($request){
            $query->where('name', 'like', '%' . trim($request->name) . '%');
        })
        ->when($request->state, function($query) use ($request){
            $query->where('state', $request->state);
        })
        ->when($request->country, function($query) use ($request){
            $query->where('country', $request->country);
        })
        ->when($request->industry, function($query) use ($request){
            $query->where('industry', $request->industry);
        })
        ->orderBy('created_at','desc')
        ->get()
        ->append(['country_name','industry_name']);

        //  $companies->transform(function ($company) {
        //     return $company->append(['country_name','industry_name']);
        // });

        return response()->json([
            'success' => true,
            'companies' => $companies
        ]);
    }
}
