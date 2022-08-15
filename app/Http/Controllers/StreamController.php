<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Stream;

class StreamController extends Controller
{
    public function index(): JsonResponse
    {
        $amountPerGame = Stream::select(
            'game_name',
            DB::raw('COUNT(*) as amount')
        )->groupBy('game_name')->get();

        $table = app(Stream::class)->getTable();
        $maxViewsQuery = Stream::select(
            'game_id',
            DB::raw('MAX(viewer_count) as max_views')
        )->groupBy('game_id');
        $maxViewsPerGame = Stream::select(
            'id',
            'user_name',
            't2.game_id',
            'game_name',
            'viewer_count'
        )->joinSub($maxViewsQuery, 't2', function ($join) use ($table) {
            $join->on("$table.game_id", '=', 't2.game_id')
                ->on("$table.viewer_count", '=', 't2.max_views');
        })->get();

        $avgViews = Stream::avg('viewer_count');

        $oddViews = Stream::select('user_name', 'game_name', 'viewer_count')
            ->where(DB::raw('viewer_count % 2'), '<>', 0)
            ->get();

        $evenViews = Stream::select('user_name', 'game_name', 'viewer_count')
            ->where(DB::raw('viewer_count % 2'), 0)
            ->get();

        $top100 = Stream::select('user_name', 'game_name', 'viewer_count')
            ->limit(100)
            ->get();

        $sameViewsQuery = Stream::select('id', 'user_name', 'game_name', 'viewer_count');
        $sameViews = Stream::select(
            "$table.id",
            "$table.user_name",
            "$table.game_name",
            "$table.viewer_count",
            'same.id AS same_id',
            'same.viewer_count as same_viewer_count',
            'same.game_name AS same_game_name',
            'same.user_name AS same_user_name',
        )->joinSub($sameViewsQuery, 'same', function ($join) use ($table) {
            $join->on("$table.viewer_count", '=', 'same.viewer_count')
                ->on("$table.id", '!=', 'same.id');
        })->get();

        return response()->json([
            'amountPerGame' => $amountPerGame,
            'maxViewsPerGame' => $maxViewsPerGame,
            'avgViews' => $avgViews,
            'oddViews' => $oddViews,
            'evenViews' => $evenViews,
            'top100' => $top100,
            'sameViews' => $sameViews,
        ]);
    }
}
