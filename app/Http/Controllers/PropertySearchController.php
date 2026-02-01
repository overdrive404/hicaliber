<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertySearchRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

class PropertySearchController extends Controller
{
    public function __invoke(PropertySearchRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $query = Property::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        foreach (['bedrooms', 'bathrooms', 'storeys', 'garages'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        $minPrice = $filters['price_min'] ?? null;
        $maxPrice = $filters['price_max'] ?? null;

        if ($minPrice !== null && $maxPrice !== null) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        $properties = $query
            ->orderBy('price')
            ->get();

        return response()->json([
            'data' => $properties,
            'count' => $properties->count(),
        ]);
    }
}
