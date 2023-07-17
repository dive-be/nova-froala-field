<?php declare(strict_types=1);

use Froala\Nova\Attachments\AttachmentController;
use Froala\Nova\Attachments\ImageManagerController;
use Froala\Nova\Attachments\PendingAttachmentController;

/** @var \Illuminate\Routing\Router $router */

$router->get('{resource}/image-manager', [ImageManagerController::class, 'index']);
$router->delete('{resource}/image-manager', [ImageManagerController::class, 'destroy']);

$router->delete('{resource}/attachments/{field}/{draftId}', [PendingAttachmentController::class, 'destroy']);
$router->post('{resource}/attachments/{field}', [PendingAttachmentController::class, 'store']);
$router->delete('{resource}/attachments/{field}', [AttachmentController::class, 'destroy']);
