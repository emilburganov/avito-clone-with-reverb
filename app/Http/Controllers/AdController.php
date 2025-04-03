<?php

namespace App\Http\Controllers;

use App\Events\AdFeatured;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AdController extends Controller
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAcceptedAds(Request $request): JsonResponse
    {
        $ads = Ad::query()->where('status', '=', 'ACCEPTED');

        if (isset($request->title)) {
            $ads->where('title', 'like', '%' . $request->title . '%');
        }

        if (isset($request->min_price)) {
            $ads->where('price', '>=', $request->min_price);
        }

        if (isset($request->max_price)) {
            $ads->where('price', '<=', $request->max_price);
        }

        if (isset($request->sort_created) && Arr::exists(['desc', 'asc'], $request->sort_created)) {
            $ads->orderBy('created_at', $request->sort_created);
        }

        if (isset($request->category_ids)) {
            $ads->whereHas('categories', fn($q) => $q->whereIn('categories.id', $request->category_ids));
        }

        return response()->json(AdResource::collection($ads->get()));
    }

    /**
     * @return JsonResponse
     */
    public function getMyAds(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'data' => AdResource::collection($user->ads)
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'title' => 'required|string|min:4',
            'price' => 'required|integer|min:0',
            'categories' => 'required|array',
            'categories.*' => 'required|integer|exists:categories,id',
            'photos' => 'required|array',
            'photos.*' => 'required|image',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $data = $validator->safe();

        $ad = Ad::query()->create([
            'title' => $data->title,
            'price' => $data->price,
            'status' => 'TAKEN',
            'user_id' => auth()->id(),
        ]);

        $ad->categories()->attach($data->categories);

        foreach ($data->photos as $photo) {
            $photoName = Str::uuid()->toString() . '.' . $photo->extension();
            $photo->move(public_path('images'), $photoName);

            $ad->photos()->create([
                'url' => 'images/' . $photoName,
            ]);
        }

        return response()->json([
            'data' => [
                'message' => 'Объявление создано',
            ],
        ], 201);
    }

    /**
     * @param Request $request
     * @param Ad $ad
     * @return JsonResponse
     */
    public function update(Request $request, Ad $ad): JsonResponse
    {
        $validator = validator($request->all(), [
            'title' => 'sometimes|string|min:4',
            'price' => 'sometimes|integer|min:0',
            'categories' => 'sometimes|array',
            'categories.*' => 'required|integer|exists:categories,id',
            'photos' => 'sometimes|array',
            'photos.*' => 'required|image',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $data = $validator->safe();

        $ad->update(
            $data->merge(['status' => 'TAKEN'])->except('photos', 'categories')
        );

        if (isset($data->categories)) {
            $ad->categories()->sync($data->categories);
        }

        if (isset($data->photos)) {
            $ad->photos()->delete();

            foreach ($data->photos as $photo) {
                $photoName = Str::uuid()->toString() . '.' . $photo->extension();
                $photo->move(public_path('images'), $photoName);

                $ad->photos()->create([
                    'url' => 'images/' . $photoName,
                ]);
            }
        }

        return response()->json([
            'data' => [
                'message' => 'Объявление обновлено',
            ],
        ], 202);
    }

    /**
     * @param Ad $ad
     * @return JsonResponse
     */
    public function destroy(Ad $ad): JsonResponse
    {
        $ad->delete();

        return response()->json([
            'data' => [
                'message' => 'Объявление удалено',
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Ad $ad
     * @return JsonResponse|Response
     */
    public function changeAdStatus(Request $request, Ad $ad): Response|JsonResponse
    {
        $validator = validator($request->all(), [
            'status' => 'required|string|in:ACCEPTED,REJECTION',
            'admin_message' => 'required_if:status,REJECTION|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $data = $validator->safe();

        $ad->update([
            'status' => $data->status,
            'admin_message' => $data->admin_message ?? null,
        ]);

        return response()->noContent();
    }

    /**
     * @return JsonResponse
     */
    public function getTakenAds(): JsonResponse
    {
        $ads = Ad::query()->where('status', '=', 'TAKEN')->get();

        return response()->json(AdResource::collection($ads));
    }

    /**
     * @param Ad $ad
     * @return JsonResponse
     */
    public function addToFeaturedAd(Ad $ad): JsonResponse
    {
        $user = auth()->user();

        $user->featuredAds()->syncWithoutDetaching($ad);

        event(new AdFeatured($ad, $user));

        return response()->json([
            'data' => [
                'message' => 'Объявление добавлено в избранное',
            ],
        ]);
    }

    /**
     * @param Ad $ad
     * @return JsonResponse
     */
    public function removeFromFeaturedAd(Ad $ad): JsonResponse
    {
        $user = auth()->user();

        $user->featuredAds()->detach($ad);

        return response()->json([
            'data' => [
                'message' => 'Объявление удалено из избранного',
            ],
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getFeatured(): JsonResponse
    {
        $user = auth()->user();

        return response()->json(AdResource::collection($user->featuredAds));
    }
}
