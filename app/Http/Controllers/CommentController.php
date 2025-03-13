<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment): void
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment): void
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): void
    {
    }
}
