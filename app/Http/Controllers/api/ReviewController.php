<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;


class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Review::all();
    }
    public function index_comments(Request $request)
    {
        $property_id = $request->input('property_id');
        $query = Review::query();
        $query->where('property_id', $property_id)->whereNull('rating');
        $reviews = $query->get();
        foreach ($reviews as $review) {
            $user = User::where('id', $review->user_id)->first();
            $review->user_name = $user->name;
            $review->user_email = $user->email; 
        }
        return $reviews;
    }
    public function index_rates(Request $request)
    {
        $property_id = $request->input('property_id');
        
        // Get the number of rows matching the conditions
        $rowCount = Review::where('property_id', $property_id)
                        ->whereNull('comment')
                        ->count();

        // Get the sum of all rating numbers for the matching rows
        $sumOfRates = Review::where('property_id', $property_id)
                        ->whereNull('comment')
                        ->sum('rating');
        
        // Return the number of rows and the sum of all rating numbers
        return response()->json(['rowCount' => $rowCount, 'sumOfRates' => $sumOfRates], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'max:5',
            'comment' => 'max:255'
        ]);

        $reviewFounded = Review::where('property_id', $request->property_id)
                       ->where('user_id', $request->user_id)
                       ->whereNotNull('rating')
                       ->first();

        
        if ($reviewFounded && $request->has('rating') && !empty($request->rating)) {
                $reviewFounded->rating = $request->rating;
                $reviewFounded->save();
                if($request->has('comment') && !empty($request->comment)){
                    $review = new Review;
                    $review->property_id = $request->property_id;
                    $review->user_id = $request->user_id;
                    $review->comment = $request->comment;
                    $review->save();
                }
                return response()->json(['message' => 'Rating updated successfully', 'reviewFounded' => $reviewFounded, 'review' => $review??null], 200);
        }else{
            $review = new Review;
            $review->property_id = $request->property_id;
            $review->user_id = $request->user_id;
            if($request->rating){
                $ratingReview = clone $review;
                $ratingReview->rating = $request->rating;
                $ratingReview->save();
                if($request->comment){
                    $commentReview = clone $review;
                    $commentReview->comment = $request->comment;
                    $commentReview->save();
                }
            }elseif($request->comment && !$request->rating){
                $review->comment = $request->comment;
                $review->save();
            }
            return response()->json(['message' => 'Review created successfully', 'review' => $review], 201);
        }
        // $review = Review::create($validatedData);

    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return $review;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validatedData = $request->validate([
            'comment' => 'string|max:255'
        ]);

        $review->update($validatedData);

        return response()->json(['message' => 'Review updated successfully', 'review' => $review], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully'], 200);
    }
}
