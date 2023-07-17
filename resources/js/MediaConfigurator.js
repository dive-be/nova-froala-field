import Endpoints from './Endpoints';

class MediaConfigurator {
    constructor(resource, field) {
        this.resource = resource;
        this.field = field;

        this.endpoints = new Endpoints(resource, field);
        this._token = document.head.querySelector('meta[name="csrf-token"]').content;
    }

    getConfig() {
        return _.merge(
            this.uploadConfig,
            this.eventsConfig,
            this.imageManagerLoadConfig,
            this.imageManagerDeleteConfig,
            this.videoUploadConfig,
            this.fileUploadConfig
        );
    }

    /**
     * Purge pending attachments for the draft
     */
    cleanUp() {
        if (this.field.withFiles) {
            Nova.request()
                .delete(this.endpoints.cleanUpUrl)
                .then(Function.prototype)
                .catch(error => {
                    Nova.error(error.message);
                });
        }
    }

    get uploadConfig() {
        return {
            // Set the image upload parameter.
            imageUploadParam: 'attachment',

            // Set the image upload URL.
            imageUploadURL: this.endpoints.imageUploadUrl,

            // Additional upload params.
            imageUploadParams: {
                _token: this._token,
                draftId: this.field.draftId,
            },

            // Set request type.
            imageUploadMethod: 'POST',
        };
    }

    get eventsConfig() {
        return {
            events: {
                'froalaEditor.image.removed': (e, editor, $img) => {
                    Nova.request()
                        .delete(this.endpoints.imageRemoveUrl, {
                            params: { attachmentUrl: $img.attr('src') },
                        })
                        .then(Function.prototype)
                        .catch(error => {
                            Nova.error(error.message);
                        });
                },
                'froalaEditor.image.error': (e, editor, error, response) => {
                    try {
                        response = JSON.parse(response);

                        if (typeof response.status !== 'undefined' && response.status === 409) {
                            Nova.error('A file with this name already exists.');

                            return;
                        }
                    } catch (e) {}

                    Nova.error(error.message);
                },
                'froalaEditor.imageManager.error': (e, editor, error) => {
                    Nova.error(error.message);
                },
                'froalaEditor.file.error': (e, editor, error) => {
                    Nova.error(error.message);
                },
            },
        };
    }

    get imageManagerLoadConfig() {
        return {
            imageManagerLoadURL: this.endpoints.imageManagerUrl,

            imageManagerLoadParams: {
                field: this.field.attribute,
            },
        };
    }

    get imageManagerDeleteConfig() {
        return {
            imageManagerDeleteURL: this.endpoints.imageManagerUrl,

            imageManagerDeleteMethod: 'DELETE',

            imageManagerDeleteParams: {
                _token: this._token,
                field: this.field.attribute,
            },
        };
    }

    get videoUploadConfig() {
        return {
            videoUploadURL: this.endpoints.videoUploadUrl,

            videoUploadParam: 'attachment',

            videoUploadParams: {
                _token: this._token,
                draftId: this.field.draftId,
            },
        };
    }

    get fileUploadConfig() {
        return {
            // Set the file upload parameter.
            fileUploadParam: 'attachment',

            // Set the file upload URL.
            fileUploadURL: this.endpoints.fileUploadUrl,

            // Additional upload params.
            fileUploadParams: {
                _token: this._token,
                draftId: this.field.draftId,
            },

            // Set request type.
            fileUploadMethod: 'POST',
        };
    }
}

export default MediaConfigurator;
