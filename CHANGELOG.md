# Changelog

All notable changes to `nova-froala-field` will be documented in this file.

## 1.3.0 - 2025-05-07

### Added

- Nova 5 support

### Removed

- Nova 4 support

## 1.2.0 - 2025-05-07

### Added

- PHP 8.4 support

### Removed

- PHP 8.3 support

## 1.1.0 - 2024-03-13

### Added

- PHP 8.3 support

### Removed

- PHP 8.2 support

## 1.0.0 - 2023-07-24

This fork is a continuation of the original, abandoned repository. 
A minimalistic version will be maintained indefinitely until all of our projects are migrated off Froala. 

### Added

- Laravel 10 support
- Nova 4 support
- Default `disk` & `path` config options
- Allow image optimizations on Cloud filesystems (e.g. S3)

### Removed
- Laravel 9 support
- Nova 3 support
- 3rd party plug-in support
- Custom image optimization settings (because defaults are good enough)
- Trix attachments support
- File name preservation (maintenance burden & no benefit other than vanity)
