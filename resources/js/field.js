import Froala from 'vue-froala-wysiwyg';

import IndexField from './components/IndexField';
import DetailField from './components/DetailField';
import FormField from './components/FormField';

Nova.booting(app => {
    app.use(Froala);

    app.component('index-froala-field', IndexField);
    app.component('detail-froala-field', DetailField);
    app.component('form-froala-field', FormField);
});
