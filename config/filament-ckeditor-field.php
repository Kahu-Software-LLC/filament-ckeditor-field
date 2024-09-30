<?php

return [

    /**
     * Image upload enabled
     * 
     * WARNING: Setting this to false will use CKEditor's default Base64 upload method which is HIGHLY INEFFICIENT.
     * https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html#base64-adapter
     */
    'upload_enabled' => true,

    /**
     * Image URL to upload to if one is not specified on the form field's ->uploadUrl() method
     */
    'upload_url' => null,

];
