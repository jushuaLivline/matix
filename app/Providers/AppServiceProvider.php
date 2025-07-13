<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('downloadAttachment', function ($attachment) {

            $headers = [
                'Content-Disposition' => 'attachment; filename="'. $attachment->file_name .'"',
            ];
        
            return Response::make(base64_decode($attachment->file), 200, $headers);

        });

        Paginator::defaultView('partials._pagination');

        Validator::extend('exists_in_departments', function ($attribute, $value, $parameters, $validator) {
            return \DB::table('departments')
                ->where('code', $value)
                ->exists();
        });
    }
}
