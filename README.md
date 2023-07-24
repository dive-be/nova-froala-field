<p align="center"><img alt="Nova Froala Field" src=".github/docs/froala-nova.png" width="380"></p>

<p align="center"><strong>Froala WYSIWYG Editor</strong> field for Laravel Nova</p>

## Introduction

This is a fork of the original `froala/nova-froala-field` repository. 
The original is no longer maintained. We created this fork because our company needs a working version of Froala in Nova.
This fork contains an updated version of Froala (v4) **without** 3rd party plugin support.

## Installation

You can install the package into a Laravel application that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require dive-be/nova-froala-field
```

## Usage

Just use the `Froala\Nova\Froala` field in your Nova resource:

```php
namespace App\Nova;

use Froala\Nova\Froala;

class Article extends Resource
{
    // ...

    public function fields(NovaRequest $request)
    {
        return [
            // ...

            Froala::make('Content'),

            // ...
        ];
    }
}
```

## Override Config Values

To change any of config values for _froala field_, publish a config file:

```bash
php artisan vendor:publish --tag=config --provider=Froala\\Nova\\FroalaServiceProvider
```

## Customize Editor Options

For changing any [Available Froala Option](https://www.froala.com/wysiwyg-editor/docs/options)
edit `froala.options` value:

```php
/*
|--------------------------------------------------------------------------
| Default Editor Options
|--------------------------------------------------------------------------
|
| Setup default values for any Froala editor option.
|
| To view a list of all available options check out the Froala documentation
| {@link https://www.froala.com/wysiwyg-editor/docs/options}
|
*/

'options' => [
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

//...
```

If you want to set options only to specific field, just pass them to `options` method:

```php
public function fields(NovaRequest $request)
{
    return [
        // ...

        Froala::make('Content')->options([
            'editorClass' => 'custom-class',
            'height' => 300,
        ]),

        // ...
    ];
}
```

## Attachments

> **Note** If you are not going to use Froala's attachment functionality, you should call the `Froala::ignoreMigrations` method in the register method of your application's `App\Providers\AppServiceProvider` class.

**Nova Froala Field** provides native attachments driver which works similar to [Trix File Uploads](https://nova.laravel.com/docs/1.0/resources/fields.html#file-uploads), but with ability to optimize images.

Run migrations:

```bash 
php artisan migrate
```

### Attachments Usage

To allow users to upload images, files and videos, just like with _Trix_ field, chain the `withFiles` method onto the field's definition. When calling the `withFiles` method, you should pass the name of the filesystem disk that photos should be stored on:

```php
use Froala\Nova\Froala;

Froala::make('Content')->withFiles('public');
```

And also, in your `app/Console/Kernel.php` file, you should register a [daily job](https://laravel.com/docs/5.7/scheduling) to prune any stale attachments from the pending attachments table and storage:

```php
use Froala\Nova\Attachments\PendingAttachment;

/**
* Define the application's command schedule.
*
* @param  \Illuminate\Console\Scheduling\Schedule  $schedule
* @return void
*/
protected function schedule(Schedule $schedule)
{
    $schedule->command('model:prune', [
        '--model' => [PendingAttachment::class],
    ])->daily();
}
```

#### Images Optimization

> **Note** Don't forget to check out [spatie/image-optimizer](https://github.com/spatie/image-optimizer)'s documentation e.g. to install the required binaries on your machine.

All uploaded images will be optimized by default by [spatie/image-optimizer](https://github.com/spatie/image-optimizer). 
You can disable image optimization in config file (not recommended):

```php
/*
|--------------------------------------------------------------------------
| Automatically Images Optimization
|--------------------------------------------------------------------------
|
| Optimize all uploaded images by default.
|
*/

'optimize_images' => false,

//...
```

### Upload Max Filesize

You can set max upload filesize for attachments. If set to `null`, max upload filesize equals to _php.ini_ `upload_max_filesize` directive value.

```php
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

//...
```

## Display Edited Content

According to _Froala_ [Display Edited Content](https://www.froala.com/wysiwyg-editor/docs/overview#frontend) documentation you should publish _Froala_ styles:

```bash
php artisan vendor:publish --tag=froala-styles --provider=Froala\\Nova\\FroalaServiceProvider 
```

include into view where an edited content is shown:

```blade
<!-- CSS rules for styling the element inside the editor such as p, h1, h2, etc. -->
<link href="{{ asset('css/vendor/froala_styles.min.css') }}" rel="stylesheet" type="text/css" />
```

Also, you should make sure that you put the edited content inside an element that has the `.fr-view` class:

```html
<div class="fr-view">
    {!! $article->content !!}
</div>
```

## Show on Index Page

You have an ability to show field content on resource index page in popup window:

```php
use Froala/Nova/Froala;

Froala::make('Content')->showOnIndex();
```

Just click **Show Content**

![Index Field](.github/docs/index-field.png)

## License Key

To setup your license key, set `FROALA_KEY` environment variable:

```php
// ...
'options' => [
    'key' => env('FROALA_KEY'),
    // ...
],
```

## Advanced

### Custom Event Handlers

If you want to setup custom event handlers for froala editor instance, create js file and assign `events` property to `window.froala`:

```javascript
window.froala = {
    events: {
        'image.error': (error, response) => {},
        'imageManager.error': (error, response) => {},
        'file.error': (error, response) => {},
    }
};
```

to all callbacks provided in `window.froala.events`, the context of _VueJS_ form field component is automatically applied, you can work with `this` inside callbacks like with _Vue_ instance component.

After that, load the js file into _Nova_ scripts in `NovaServiceProvider::boot` method:

```php
public function boot(): void
{
    Nova::serving(function (ServingNova $event) {
        Nova::script('froala-event-handlers', public_path('path/to/js/file.js'));
    });
    
    parent::boot();
}
```

### Customize Attachment Handlers

You can change any of attachment handlers by passing a `callable`:

```php
use App\Nova\Handlers\{
    StorePendingAttachment,
    DetachAttachment,
    DeleteAttachments,
    DiscardPendingAttachments,
    AttachedImagesList
};

Froala::make('Content')
    ->attach(new StorePendingAttachment)
    ->detach(new DetachAttachment)
    ->delete(new DeleteAttachments)
    ->discard(new DiscardPendingAttachments)
    ->images(new AttachedImagesList)
```

## Development

You may get started with this package as follows (after cloning the repository):
```bash
composer install
npm install
```

### Fixing code-style
PHP
```bash
composer format
```
JS
```bash
npm run format
```

### Testing

``` bash
composer test
```

### Building dev assets
```bash
npm run dev
```

### Building production assets
```bash
npm run prod
```

## Contributing

To contribute, simply make a pull request to this repository with your changes. Make sure they are documented well in your pull request description.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-contributors]: ../../contributors
