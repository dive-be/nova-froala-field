import Froala from 'vue-froala-wysiwyg';

import IndexField from './components/IndexField';
import DetailField from './components/DetailField';
import FormField from './components/FormField';

Nova.booting(app => {
    app.use(Froala);

    app.component('index-nova-froala-field', IndexField);
    app.component('detail-nova-froala-field', DetailField);
    app.component('form-nova-froala-field', FormField);
});
