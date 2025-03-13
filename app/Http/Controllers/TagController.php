<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
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
    public function destroy(Tag $tag): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): void
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Tag::paginate();
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag): void
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): void
    {
    }
}
