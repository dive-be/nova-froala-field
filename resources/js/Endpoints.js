class Endpoints {
    constructor(resource, field) {
        this.resource = resource;
        this.field = field;
    }

    get cleanUpUrl() {
        return `/nova-vendor/froala/${this.resource}/attachments/${this.field.attribute}/${this.field.draftId}`;
    }

    get imageManagerUrl() {
        return `/nova-vendor/froala/${this.resource}/image-manager`;
    }

    get imageUploadUrl() {
        return `/nova-vendor/froala/${this.resource}/attachments/${this.field.attribute}`;
    }

    get imageRemoveUrl() {
        return this.imageUploadUrl;
    }

    get videoUploadUrl() {
        return this.imageUploadUrl;
    }

    get fileUploadUrl() {
        return this.imageUploadUrl;
    }
}

export default Endpoints;
