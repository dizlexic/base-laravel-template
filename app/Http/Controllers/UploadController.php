<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateUploadRequest;
use App\Models\Upload;

class UploadController extends Controller
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
    public function destroy(Upload $upload): void
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Upload $upload): void
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
    public function show(Upload $upload): void
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUploadRequest $request): void
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUploadRequest $request, Upload $upload): void
    {
    }
}
