<?php declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | The disk that should be used to preserve uploaded files when attachments
    | are enabled on an individual Froala field. This can still be overridden
    | on a per field basis.
    |
    */

    'disk' => env('FROALA_DISK', env('FILESYSTEM_DISK')),

    /*
    |--------------------------------------------------------------------------
    | Default Editor Options
    |--------------------------------------------------------------------------
    |
    | Setup default values for any Froala editor option.
    |
    | To view a list of all available options checkout the Froala documentation
    | {@link https://www.froala.com/wysiwyg-editor/docs/options}
    |
    */

    'options' => [
        'key' => env('FROALA_KEY'),

        'toolbarButtons' => [
            [
                'bold',
                'italic',
                'underline',
            ],
            [
                'formatOL',
                'formatUL',
            ],
            [
                'insertImage',
                'insertFile',
                'insertLink',
                'insertVideo',
            ],
            [
                'html',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preserve Attachments File Name
    |--------------------------------------------------------------------------
    |
    | Ability to preserve client original file name for uploaded
    | image, file or video.
    |
    */

    'preserve_file_names' => false,

    /*
    |--------------------------------------------------------------------------
    | Maximum Possible Size for Uploaded Files
    |--------------------------------------------------------------------------
    |
    | Customize max upload filesize for uploaded attachments.
    | By default it is set to "null", it means that default value is
    | retrieved from `upload_max_size` directive of php.ini file.
    |
    | Format is the same as for `uploaded_max_size` directive.
    | Check out FAQ page, to get more detail description.
    | {@link http://php.net/manual/en/faq.using.php#faq.using.shorthandbytes}
    |
    */

    'upload_max_filesize' => null,

    /*
    |--------------------------------------------------------------------------
    | Automatically Images Optimization
    |--------------------------------------------------------------------------
    |
    | Optimize all uploaded images by default.
    |
    | Currently not supported for cloud filesystems
    |
    */

    'optimize_images' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Path
    |--------------------------------------------------------------------------
    |
    | The path that should be used to preserve uploaded files when attachments
    | are enabled on an individual Froala field. This can still be overridden
    | on a per field basis.
    |
    */

    'path' => env('FROALA_PATH', DIRECTORY_SEPARATOR),
];
