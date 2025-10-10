<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class HotelAnalyticsValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'point_id' => 'required|integer|exists:locations,id',
            'radius' => 'required|numeric|min:0.1|max:100',
            'analysis_type' => 'sometimes|string|in:overview,clustering,recommendations,market_insights',
            'preferences' => 'sometimes|array',
            'preferences.max_distance' => 'sometimes|numeric|min:0.1|max:50',
            'preferences.min_rating' => 'sometimes|numeric|min:0|max:5',
            'preferences.max_price' => 'sometimes|numeric|min:0',
            'preferences.amenities' => 'sometimes|array',
            'preferences.amenities.*' => 'string|in:wifi,parking,pool,gym,restaurant,bar,spa,air_conditioning,pet_friendly,breakfast',
            'preferences.hotel_type' => 'sometimes|string|in:hotel,motel,hostel,resort,guesthouse',
            'preferences.limit' => 'sometimes|integer|min:1|max:50',
            'preferences.sort_by' => 'sometimes|string|in:distance,rating,price,name',
            'preferences.sort_order' => 'sometimes|string|in:asc,desc',
            'clustering_options' => 'sometimes|array',
            'clustering_options.algorithm' => 'sometimes|string|in:density,hierarchical,kmeans',
            'clustering_options.threshold' => 'sometimes|numeric|min:0.1|max:5.0',
            'clustering_options.max_clusters' => 'sometimes|integer|min:2|max:20',
            'market_analysis' => 'sometimes|array',
            'market_analysis.segments' => 'sometimes|array',
            'market_analysis.competition_level' => 'sometimes|string|in:low,moderate,high',
            'market_analysis.growth_factors' => 'sometimes|array',
            'filters' => 'sometimes|array',
            'filters.price_range' => 'sometimes|array',
            'filters.price_range.min' => 'sometimes|numeric|min:0',
            'filters.price_range.max' => 'sometimes|numeric|min:0',
            'filters.rating_range' => 'sometimes|array',
            'filters.rating_range.min' => 'sometimes|numeric|min:0|max:5',
            'filters.rating_range.max' => 'sometimes|numeric|min:0|max:5',
            'filters.distance_range' => 'sometimes|array',
            'filters.distance_range.min' => 'sometimes|numeric|min:0',
            'filters.distance_range.max' => 'sometimes|numeric|min:0',
            'filters.amenities_required' => 'sometimes|array',
            'filters.amenities_required.*' => 'string|in:wifi,parking,pool,gym,restaurant,bar,spa,air_conditioning,pet_friendly,breakfast',
            'filters.amenities_excluded' => 'sometimes|array',
            'filters.amenities_excluded.*' => 'string|in:wifi,parking,pool,gym,restaurant,bar,spa,air_conditioning,pet_friendly,breakfast',
            'output_format' => 'sometimes|string|in:json,xml,csv',
            'include_metadata' => 'sometimes|boolean',
            'include_debug_info' => 'sometimes|boolean',
            'cache_results' => 'sometimes|boolean',
            'cache_ttl' => 'sometimes|integer|min:60|max:86400', // 1 minute to 24 hours
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'point_id.required' => 'A center point ID is required for hotel analysis',
            'point_id.exists' => 'The specified center point does not exist in the database',
            'radius.required' => 'A search radius is required for hotel analysis',
            'radius.min' => 'The search radius must be at least 0.1 kilometers',
            'radius.max' => 'The search radius cannot exceed 100 kilometers',
            'analysis_type.in' => 'Invalid analysis type. Allowed types: overview, clustering, recommendations, market_insights',
            'preferences.max_distance.min' => 'Maximum distance preference must be at least 0.1 kilometers',
            'preferences.max_distance.max' => 'Maximum distance preference cannot exceed 50 kilometers',
            'preferences.min_rating.min' => 'Minimum rating must be at least 0',
            'preferences.min_rating.max' => 'Minimum rating cannot exceed 5',
            'preferences.max_price.min' => 'Maximum price must be at least 0',
            'preferences.amenities.*.in' => 'Invalid amenity specified. Allowed amenities: wifi, parking, pool, gym, restaurant, bar, spa, air_conditioning, pet_friendly, breakfast',
            'preferences.hotel_type.in' => 'Invalid hotel type. Allowed types: hotel, motel, hostel, resort, guesthouse',
            'preferences.limit.min' => 'Result limit must be at least 1',
            'preferences.limit.max' => 'Result limit cannot exceed 50',
            'preferences.sort_by.in' => 'Invalid sort field. Allowed fields: distance, rating, price, name',
            'preferences.sort_order.in' => 'Invalid sort order. Allowed options: asc, desc',
            'clustering_options.algorithm.in' => 'Invalid clustering algorithm. Allowed algorithms: density, hierarchical, kmeans',
            'clustering_options.threshold.min' => 'Clustering threshold must be at least 0.1 kilometers',
            'clustering_options.threshold.max' => 'Clustering threshold cannot exceed 5.0 kilometers',
            'clustering_options.max_clusters.min' => 'Maximum clusters must be at least 2',
            'clustering_options.max_clusters.max' => 'Maximum clusters cannot exceed 20',
            'market_analysis.competition_level.in' => 'Invalid competition level. Allowed levels: low, moderate, high',
            'filters.price_range.min.min' => 'Minimum price filter must be at least 0',
            'filters.price_range.max.min' => 'Maximum price filter must be at least 0',
            'filters.rating_range.min.min' => 'Minimum rating filter must be at least 0',
            'filters.rating_range.min.max' => 'Minimum rating filter cannot exceed 5',
            'filters.rating_range.max.min' => 'Maximum rating filter must be at least 0',
            'filters.rating_range.max.max' => 'Maximum rating filter cannot exceed 5',
            'filters.distance_range.min.min' => 'Minimum distance filter must be at least 0',
            'filters.distance_range.max.min' => 'Maximum distance filter must be at least 0',
            'filters.amenities_required.*.in' => 'Invalid required amenity. Allowed amenities: wifi, parking, pool, gym, restaurant, bar, spa, air_conditioning, pet_friendly, breakfast',
            'filters.amenities_excluded.*.in' => 'Invalid excluded amenity. Allowed amenities: wifi, parking, pool, gym, restaurant, bar, spa, air_conditioning, pet_friendly, breakfast',
            'output_format.in' => 'Invalid output format. Allowed formats: json, xml, csv',
            'cache_ttl.min' => 'Cache TTL must be at least 60 seconds',
            'cache_ttl.max' => 'Cache TTL cannot exceed 86400 seconds (24 hours)',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'point_id' => 'center point ID',
            'radius' => 'search radius',
            'analysis_type' => 'analysis type',
            'preferences' => 'user preferences',
            'preferences.max_distance' => 'maximum distance preference',
            'preferences.min_rating' => 'minimum rating preference',
            'preferences.max_price' => 'maximum price preference',
            'preferences.amenities' => 'preferred amenities',
            'preferences.hotel_type' => 'hotel type preference',
            'preferences.limit' => 'result limit',
            'preferences.sort_by' => 'sort field',
            'preferences.sort_order' => 'sort order',
            'clustering_options' => 'clustering options',
            'clustering_options.algorithm' => 'clustering algorithm',
            'clustering_options.threshold' => 'clustering threshold',
            'clustering_options.max_clusters' => 'maximum clusters',
            'market_analysis' => 'market analysis options',
            'market_analysis.segments' => 'market segments',
            'market_analysis.competition_level' => 'competition level',
            'market_analysis.growth_factors' => 'growth factors',
            'filters' => 'filters',
            'filters.price_range' => 'price range filter',
            'filters.price_range.min' => 'minimum price',
            'filters.price_range.max' => 'maximum price',
            'filters.rating_range' => 'rating range filter',
            'filters.rating_range.min' => 'minimum rating',
            'filters.rating_range.max' => 'maximum rating',
            'filters.distance_range' => 'distance range filter',
            'filters.distance_range.min' => 'minimum distance',
            'filters.distance_range.max' => 'maximum distance',
            'filters.amenities_required' => 'required amenities',
            'filters.amenities_excluded' => 'excluded amenities',
            'output_format' => 'output format',
            'include_metadata' => 'include metadata flag',
            'include_debug_info' => 'include debug info flag',
            'cache_results' => 'cache results flag',
            'cache_ttl' => 'cache TTL',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed for hotel analytics request',
                'errors' => $validator->errors(),
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid('hotel_analytics_'),
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string values to appropriate types
        if ($this->has('radius')) {
            $this->merge(['radius' => (float) $this->radius]);
        }

        if ($this->has('preferences.max_distance')) {
            $this->merge(['preferences' => array_merge($this->preferences ?? [], [
                'max_distance' => (float) $this->preferences['max_distance']
            ])]);
        }

        if ($this->has('preferences.min_rating')) {
            $this->merge(['preferences' => array_merge($this->preferences ?? [], [
                'min_rating' => (float) $this->preferences['min_rating']
            ])]);
        }

        if ($this->has('preferences.max_price')) {
            $this->merge(['preferences' => array_merge($this->preferences ?? [], [
                'max_price' => (float) $this->preferences['max_price']
            ])]);
        }

        if ($this->has('preferences.limit')) {
            $this->merge(['preferences' => array_merge($this->preferences ?? [], [
                'limit' => (int) $this->preferences['limit']
            ])]);
        }

        if ($this->has('clustering_options.threshold')) {
            $this->merge(['clustering_options' => array_merge($this->clustering_options ?? [], [
                'threshold' => (float) $this->clustering_options['threshold']
            ])]);
        }

        if ($this->has('clustering_options.max_clusters')) {
            $this->merge(['clustering_options' => array_merge($this->clustering_options ?? [], [
                'max_clusters' => (int) $this->clustering_options['max_clusters']
            ])]);
        }

        if ($this->has('filters.price_range.min')) {
            $this->merge(['filters' => array_merge($this->filters ?? [], [
                'price_range' => array_merge($this->filters['price_range'] ?? [], [
                    'min' => (float) $this->filters['price_range']['min']
                ])
            ])]);
        }

        if ($this->has('filters.price_range.max')) {
            $this->merge(['filters' => array_merge($this->filters ?? [], [
                'price_range' => array_merge($this->filters['price_range'] ?? [], [
                    'max' => (float) $this->filters['price_range']['max']
                ])
            ])]);
        }

        if ($this->has('filters.rating_range.min')) {
            $this->merge(['filters' => array_merge($this->filters ?? [], [
                'rating_range' => array_merge($this->filters['rating_range'] ?? [], [
                    'min' => (float) $this->filters['rating_range']['min']
                ])
            ])]);
        }

        if ($this->has('filters.rating_range.max')) {
            $this->merge(['filters' => array_merge($this->filters ?? [], [
                'rating_range' => array_merge($this->filters['rating_range'] ?? [], [
                    'max' => (float) $this->filters['rating_range']['max']
                ])
            ])]);
        }

        if ($this->has('filters.distance_range.min')) {
            $this->merge(['filters' => array_merge($this->filters ?? [], [
                'distance_range' => array_merge($this->filters['distance_range'] ?? [], [
                    'min' => (float) $this->filters['distance_range']['min']
                ])
            ])]);
        }

        if ($this->has('filters.distance_range.max')) {
            $this->merge(['filters' => array_merge($this->filters ?? [], [
                'distance_range' => array_merge($this->filters['distance_range'] ?? [], [
                    'max' => (float) $this->filters['distance_range']['max']
                ])
            ])]);
        }

        if ($this->has('cache_ttl')) {
            $this->merge(['cache_ttl' => (int) $this->cache_ttl]);
        }

        if ($this->has('include_metadata')) {
            $this->merge(['include_metadata' => (bool) $this->include_metadata]);
        }

        if ($this->has('include_debug_info')) {
            $this->merge(['include_debug_info' => (bool) $this->include_debug_info]);
        }

        if ($this->has('cache_results')) {
            $this->merge(['cache_results' => (bool) $this->cache_results]);
        }
    }

    /**
     * Get validated data with additional processing.
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        // Add default values for optional fields
        if (is_array($validated)) {
            $validated['analysis_type'] = $validated['analysis_type'] ?? 'overview';
            $validated['output_format'] = $validated['output_format'] ?? 'json';
            $validated['include_metadata'] = $validated['include_metadata'] ?? true;
            $validated['include_debug_info'] = $validated['include_debug_info'] ?? false;
            $validated['cache_results'] = $validated['cache_results'] ?? true;
            $validated['cache_ttl'] = $validated['cache_ttl'] ?? 3600; // 1 hour default

            // Set default preferences if not provided
            if (!isset($validated['preferences'])) {
                $validated['preferences'] = [
                    'limit' => 10,
                    'sort_by' => 'distance',
                    'sort_order' => 'asc'
                ];
            }

            // Set default clustering options if not provided
            if (!isset($validated['clustering_options'])) {
                $validated['clustering_options'] = [
                    'algorithm' => 'density',
                    'threshold' => 0.5,
                    'max_clusters' => 10
                ];
            }
        }

        return $validated;
    }
} 