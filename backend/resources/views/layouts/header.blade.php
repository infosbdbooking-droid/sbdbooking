<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="color-scheme" content="light">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ config('app.name', 'Hommlie B2B') }}</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.svg') }}">
  <!-- CSS Links -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function preventDarkMode() {
      if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.style.backgroundColor = "#ffffff"; // White background
        document.documentElement.style.color = "#000000"; // Black text
        document.documentElement.setAttribute("data-theme", "light"); // Optional
      }
    }
    // Apply light mode on page load
    preventDarkMode();

    // Prevent dark mode if the user changes it later
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', preventDarkMode);
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <!-- <link rel="stylesheet" href="{{asset('dist/Bootstrap5.3/bootstrap.min.css')}}"> -->
  <link rel="stylesheet" href="{{asset('dist/Datatable2.18/dataTables.dataTables.min.css')}}">
  <link rel="stylesheet" href="{{asset('dist/Datatable2.18/buttons.dataTables.min.css')}}">
  <link rel="stylesheet" href="{{asset('dist/select2/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('dist/Toastr2.1.4/toastr.css')}}">
  <link rel="stylesheet" href="{{asset('dist/Jconfirm3.3/jquery-confirm.min.css')}}">
  <link rel="stylesheet" href="{{asset('dist/Lightbox2.1/lightbox.css')}}">
  <link rel="stylesheet" href="{{asset('dist/Flatpickr/flatpickr.min.css')}}">
  <script src="{{asset('dist/tinymce/tinymce.min.js')}}"></script>
  <link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tinymce.init({
      selector: '#mytextarea',
      height: 700,
      language: 'en',
      branding: false,
      promotion: false,
      plugins: `
      preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen
      image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist
      lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons spellchecker a11ychecker
      formatpainter permanentpen advtable advtemplate editimage tableofcontents footnotes
    `,
      menubar: 'file edit view insert format tools table help',
      toolbar: `
      undo redo | bold italic underline strikethrough superscript subscript code | fontfamily fontsize blocks styleselect |
      alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist |
      forecolor backcolor permanentpen formatpainter removeformat | table tabledelete advtable |
      insertfile image media link anchor codesample hr charmap emoticons | 
      pagebreak template toc footnotes | ltr rtl | spellchecker a11ycheck | fullscreen preview save print |
      visualblocks visualchars nonbreaking
    `,
      toolbar_sticky: true,
      autosave_ask_before_unload: true,
      autosave_interval: '20s',
      autosave_prefix: '{path}{query}-{id}-',
      autosave_restore_when_empty: false,
      autosave_retention: '5m',
      image_advtab: true,
      image_title: true,
      image_caption: true,
      imagetools_cors_hosts: ['www.tiny.cloud', 'www.google.com'],
      file_picker_types: 'file image media',

      /* ← this tells TinyMCE to POST files to your /upload route */
      images_upload_url: '/upload',
      automatic_uploads: true,
      images_reuse_filename: true,

      /* remove your old Base64 handler
      images_upload_handler: … 
      */

      templates: [
        {
          title: 'Starter Table',
          description: 'Basic 2x2 Table',
          content:
            '<table style="width: 100%; border-collapse: collapse;" border="1">' +
            '<tr><td>Cell 1</td><td>Cell 2</td></tr>' +
            '<tr><td>Cell 3</td><td>Cell 4</td></tr>' +
            '</table>'
        },
        {
          title: 'Callout Box',
          description: 'Styled callout box',
          content:
            '<div style="border: 2px solid #2196F3; padding: 10px; ' +
            'border-radius: 5px; background-color: #e3f2fd;">' +
            'Important notice here.' +
            '</div>'
        },
        {
          title: 'Image & Caption',
          description: 'Image with caption',
          content:
            '<figure><img src="https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png" ' +
            'alt="Example image"><figcaption>This is a caption</figcaption></figure>'
        }
      ],
      template_cdate_format: '[Created: %Y-%m-%d %H:%M:%S]',
      template_mdate_format: '[Modified: %Y-%m-%d %H:%M:%S]',
      content_style: `
      body { font-family:Helvetica,Arial,sans-serif; font-size:16px }
      img { max-width:100%; height:auto }
      table { width:100%; border-collapse: collapse; }
      th, td { border: 1px solid #ccc; padding: 8px; }
    `,
      quickbars_selection_toolbar:
        'bold italic underline | quicklink h2 h3 blockquote quickimage quicktable',
      quickbars_insert_toolbar: 'image media table hr',
      noneditable_class: 'mceNonEditable',
      contextmenu: 'link image table anchor',
      toolbar_mode: 'wrap',
      spellchecker_language: 'en',
      spellchecker_ignore_list: ['TinyMCE'],
      a11y_advanced_options: true,
      convert_urls: false
    });
  </script>
</head>

<body class="">