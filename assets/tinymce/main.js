import tinymce from 'tinymce';

import 'tinymce/themes/silver';
import 'tinymce/skins/content/default/content';
import 'tinymce/icons/default';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/quickbars';
import 'tinymce/plugins/autosave';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/link';
import 'tinymce/plugins/media';
import 'tinymce/plugins/table';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/media';
import 'tinymce/plugins/codesample';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/autolink';
import es from 'tinymce/plugins/help/js/i18n/keynav/es.js';
require('./content.min.css');

tinymce.init({
    selector: '.tinymce-editor',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
    editimage_cors_hosts: ['picsum.photos'],
    menubar: 'file edit view insert format tools table help',
    toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
    // autosave_ask_before_unload: true,
    // autosave_interval: '30s',
    // autosave_prefix: '{path}{query}-{id}-',
    // autosave_restore_when_empty: false,
    // autosave_retention: '2m',
    // image_advtab: true,
    // link_list: [
    //     { title: 'My page 1', value: 'https://www.tiny.cloud' },
    //     { title: 'My page 2', value: 'http://www.moxiecode.com' }
    // ],
    // image_list: [
    //     { title: 'My page 1', value: 'https://www.tiny.cloud' },
    //     { title: 'My page 2', value: 'http://www.moxiecode.com' }
    // ],
    // image_class_list: [
    //     { title: 'None', value: '' },
    //     { title: 'Some class', value: 'class-name' }
    // ],
    // importcss_append: true,
    // file_picker_callback: (callback, value, meta) => {
    //     /* Provide file and text for the link dialog */
    //     if (meta.filetype === 'file') {
    //         callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
    //     }
    //
    //     /* Provide image and alt text for the image dialog */
    //     if (meta.filetype === 'image') {
    //         callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
    //     }
    //
    //     /* Provide alternative source and posted for the media dialog */
    //     if (meta.filetype === 'media') {
    //         callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
    //     }
    // },
    height: 300,
    language: es,
    // image_caption: true,
    // quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    // noneditable_class: 'mceNonEditable',
    // toolbar_mode: 'sliding',
    // contextmenu: 'link image table',
    // skin: useDarkMode ? 'oxide-dark' : 'oxide',
    // content_css: useDarkMode ? 'dark' : 'default',
    // content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
});