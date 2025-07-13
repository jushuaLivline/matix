<?php

namespace App\Http\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'customer_id' => ['nullable'],
            'customer_person' => ['required'],
            'estimate_d' => ['required', 'date'],
            'answer_due_d' => ['required', 'date'],
            'base_product_code' => ['required', 'string', 'max:50'],
            'product_code' => ['required', 'string', 'max:50'],
            'product_name' => ['required', 'string', 'max:50'],
            'model_type' => ['required', 'string', 'max:20'],
            'per_month_reference_amount' => ['required', 'integer'],
            'sop_d' => ['required', 'date'],
            'message' => ['string'],
            'delete_flag' => ['nullable', 'boolean'],
            'attachments.*' => ['required', "mimes:" . collect(config("filesystems.attachment.accepted_extension"))->implode(",")],
            // [

            //     'images.*' => 'required|mimes:jpg,jpeg,png,bmp|max:2000'
        
            //   ],[
        
            //     'images.*.required' => 'Please upload an image only',
        
            //     'images.*.mimes' => 'Only jpeg, png, jpg and bmp images are allowed',
        
            //     'images.*.max' => 'Sorry! Maximum allowed size for an image is 2MB',
        
            // ]
        ];


        /*
              $table->date('answer_due_d');
            $table->string('', 50);
            $table->string('', 50);
            $table->string('');
            $table->string('');
            $table->integer('per_month_reference_amount');
            $table->dateTime('sop_d');
            $table->longText('message');
            $table->boolean('delete_flag')->default(0);

        */
    }
}
