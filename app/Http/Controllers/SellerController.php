<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ListingFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function __invoke(Request $request, User $user): View
    {
        abort_if($user->isAdmin() || ! $user->is_active, 404);

        return view('sellers.show', [
            'seller' => $user,
            'listings' => (new ListingFilters($request))->paginate(userId: $user->id),
        ]);
    }
}
