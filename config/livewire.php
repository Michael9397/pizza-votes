<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root namespace for Livewire component classes to
    | be autoloaded from. It can be set to a single namespace, or to an
    | array of namespaces which will be autoloaded in the order they
    | are specified.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path where Livewire component views are stored.
    | It can be set to a single path, or to an array of paths which will
    | be searched in the order they are specified.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | The view that will be used as the layout when rendering a single
    | Livewire component.
    |
    */

    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |--------------------------------------------------------------------------
    |
    | The markup that Laravel Livewire will use to render placeholders for
    | components that are lazy loaded.
    |
    */

    'lazy_placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Upload Path
    |--------------------------------------------------------------------------
    |
    | The directory path where temporary files will be stored during upload.
    | Set to `null` to use Laravel's default temporary file storage path.
    |
    */

    'temporary_file_upload_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Upload URL
    |--------------------------------------------------------------------------
    |
    | The URL path where temporary files will be accessible from the web.
    | This is used for previewing images, etc. before they are stored.
    |
    */

    'temporary_file_upload_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Max File Upload Size
    |--------------------------------------------------------------------------
    |
    | The maximum file size (in bytes) that can be uploaded via Livewire.
    |
    */

    'max_file_upload_size' => 12 * 1024 * 1024, // 12MB

    /*
    |--------------------------------------------------------------------------
    | Livewire Assets URL
    |--------------------------------------------------------------------------
    |
    | This value sets the path where Livewire's JavaScript assets are served
    | from. You might want to set this to a CDN URL if you'd like to serve
    | Livewire's assets from a content delivery network.
    |
    */

    'asset_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Auto-inject Livewire's Assets
    |--------------------------------------------------------------------------
    |
    | This value should be set to `true` if your Livewire components are
    | served from a location that is different from the root of your
    | application. This will auto-inject Livewire's JavaScript assets
    | into the `<head>` tag of your layout.
    |
    */

    'inject_assets' => true,

    /*
    |--------------------------------------------------------------------------
    | Script Tag Attributes
    |--------------------------------------------------------------------------
    |
    | These attributes will be injected into the <script> tag that
    | bootstraps Livewire on the client side.
    |
    */

    'script_tag_attributes' => [
        // 'data-turbo-cache' => 'false',
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable Back Button Cache
    |--------------------------------------------------------------------------
    |
    | This value determines whether back button cache is enabled or not.
    | If enabled, Livewire will restore the page state when the user
    | clicks the back button.
    |
    */

    'enable_back_button_cache' => true,

];
