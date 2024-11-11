<?php

namespace App\Http\Controllers\API;

use App\Helpers\Annotations\ApiTypeMarker;
use App\Http\Controllers\Controller;
use App\Sys\Res\Types\Stk\Root;
use Symfony\Component\HttpFoundation\Response as CodeOf;

class PhaseController extends Controller {
    #[ApiTypeMarker( Root\Api\Phase\Show::class)]
    public function show_phase() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Phase\ListPhases::class)]
    public function list_phases() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Phase\CutTree::class)]
    public function cut_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Phase\MoveTree::class)]
    public function move_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Phase\ReplaceTree::class)]
    public function replace_tree() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }

    #[ApiTypeMarker( Root\Api\Phase\Purge::class)]
    public function purge_phase() {
        return response()->json([], CodeOf::HTTP_NOT_IMPLEMENTED);
    }



}
