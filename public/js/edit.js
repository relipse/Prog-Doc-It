/**
 * edit.js - file to enable edit.php page
 * Much of which is for uploading images to the server (drag-drop, pasting or clicking "Upload Image" button"
 * And enabling the Ace Editor
 * @author James Kinsman
 * @copyright 2021 Hampton Roads Transit
 */
$(function(){
    $('.drop-zone').bind('drop', function(event){
        $(this).addClass('dropped');
        upload_file(event);
    });

    $(window).on('dragenter', function(event){
        event.preventDefault();
    });

    $('.drop-zone').bind('dragover', function(){
        $(this).addClass('drag-over');
        return false;
    });
    $('.drop-zone').bind('dragleave', function(){
        $(this).removeClass('drag-over');
    });
});

var themeData = [
    ["Chrome"         ],
    ["Clouds"         ],
    ["Crimson Editor" ],
    ["Dawn"           ],
    ["Dreamweaver"    ],
    ["Eclipse"        ],
    ["GitHub"         ],
    ["IPlastic"       ],
    ["Solarized Light"],
    ["TextMate"       ],
    ["Tomorrow"       ],
    ["Xcode"          ],
    ["Kuroir"],
    ["KatzenMilch"],
    ["SQL Server"           ,"sqlserver"               , "light"],
    ["Ambiance"             ,"ambiance"                ,  "dark"],
    ["Chaos"                ,"chaos"                   ,  "dark"],
    ["Clouds Midnight"      ,"clouds_midnight"         ,  "dark"],
    ["Dracula"              ,""                        ,  "dark"],
    ["Cobalt"               ,"cobalt"                  ,  "dark"],
    ["Gruvbox"              ,"gruvbox"                 ,  "dark"],
    ["Green on Black"       ,"gob"                     ,  "dark"],
    ["idle Fingers"         ,"idle_fingers"            ,  "dark"],
    ["krTheme"              ,"kr_theme"                ,  "dark"],
    ["Merbivore"            ,"merbivore"               ,  "dark"],
    ["Merbivore Soft"       ,"merbivore_soft"          ,  "dark"],
    ["Mono Industrial"      ,"mono_industrial"         ,  "dark"],
    ["Monokai"              ,"monokai"                 ,  "dark"],
    ["Nord Dark"            ,"nord_dark"               ,  "dark"],
    ["One Dark"             ,"one_dark"                ,  "dark"],
    ["Pastel on dark"       ,"pastel_on_dark"          ,  "dark"],
    ["Solarized Dark"       ,"solarized_dark"          ,  "dark"],
    ["Terminal"             ,"terminal"                ,  "dark"],
    ["Tomorrow Night"       ,"tomorrow_night"          ,  "dark"],
    ["Tomorrow Night Blue"  ,"tomorrow_night_blue"     ,  "dark"],
    ["Tomorrow Night Bright","tomorrow_night_bright"   ,  "dark"],
    ["Tomorrow Night 80s"   ,"tomorrow_night_eighties" ,  "dark"],
    ["Twilight"             ,"twilight"                ,  "dark"],
    ["Vibrant Ink"          ,"vibrant_ink"             ,  "dark"]
];
var themesByName = {};
var themes = themeData.map(function(data) {
    var name = data[1] || data[0].replace(/ /g, "_").toLowerCase();
    var theme = {
        caption: data[0],
        theme: "ace/theme/" + name,
        isDark: data[2] == "dark",
        name: name
    };
    themesByName[name] = theme;
    return theme;
});
for (var i = 0; i < themes.length; ++i){
    var selected = selected_theme == themes[i].theme ? ' selected' : '';
    $('#themes').append('<option value="'+themes[i].theme+'" '+selected+'>'+themes[i].caption+'</option>');
}

$('#savetheme').click(function(){

    $.get('ajax/savetheme.php?theme='+encodeURIComponent($('#themes').val()), function(data, status){
        alert("Saved");
    });
});

var editor = null;
// Hook up ACE editor to all textareas with data-editor attribute
//$(function () {
$('textarea[data-editor]').each(function () {
    var textarea = $(this);

    var mode = textarea.data('editor');

    var editDiv = $('<div>', {
        position: 'absolute',
        width: textarea.width(),
        height: textarea.height(),
        'class': textarea.attr('class')
    }).insertBefore(textarea);

    textarea.css('visibility', 'hidden');
    textarea.hide();

    editor = ace.edit(editDiv[0]);
    editor.renderer.setShowGutter(textarea.attr('ace-gutter'));
    editor.getSession().setValue(textarea.val());
    editor.getSession().setMode("ace/mode/" + mode);
    editor.renderer.setTheme(textarea.attr('ace-theme'));
    // editor.setTheme("ace/theme/idle_fingers");
    editor.setOption("wrap", true);

    // copy back to textarea on form submit...
    textarea.closest('form').submit(function () {
        textarea.val(editor.getSession().getValue());
    })

});
//});


$('#themes').change(function(){
    var editor = ace.edit($('div.htmleditor')[0]);
    editor.renderer.setTheme(this.value);
});

$('[name=saveasname]').on('input change', function(){
    if (this.value != p){
        $('[name=saveas]').removeAttr('disabled');
        $('[name=moveas]').removeAttr('disabled');
        $('#regularboringsave').attr('disabled', 'disabled');
    }else{
        //page equals starting page name
        $('#regularboringsave').removeAttr('disabled');
        $('[name=moveas]').attr('disabled', 'disabled');
        $('[name=saveas]').attr('disabled', 'disabled');
    }
}).change();

var fileobj;

document.onpaste = function (e) {
    var items = e.clipboardData.items;
    var files = [];
    for( var i = 0, len = items.length; i < len; ++i ) {
        var item = items[i];
        if( item.kind === "file" ) {
            ajax_file_upload(item.getAsFile());
        }
    }
};

function upload_file(e) {
    e.preventDefault();
    console.log(e);
    if (e.originalEvent && e.originalEvent.dataTransfer){
        fileobj = e.originalEvent.dataTransfer.files[0];
    }else {
        fileobj = e.dataTransfer.files[0];
    }
    ajax_file_upload(fileobj);
}

function file_explorer() {
    document.getElementById('selectfile').click();
    document.getElementById('selectfile').onchange = function() {
        fileobj = document.getElementById('selectfile').files[0];
        ajax_file_upload(fileobj);
    };
}

function ajax_file_upload(file_obj) {
    $('.upload-status').html('<div class="sk-fading-circle">\n' +
        '  <div class="sk-circle1 sk-circle"></div>\n' +
        '  <div class="sk-circle2 sk-circle"></div>\n' +
        '  <div class="sk-circle3 sk-circle"></div>\n' +
        '  <div class="sk-circle4 sk-circle"></div>\n' +
        '  <div class="sk-circle5 sk-circle"></div>\n' +
        '  <div class="sk-circle6 sk-circle"></div>\n' +
        '  <div class="sk-circle7 sk-circle"></div>\n' +
        '  <div class="sk-circle8 sk-circle"></div>\n' +
        '  <div class="sk-circle9 sk-circle"></div>\n' +
        '  <div class="sk-circle10 sk-circle"></div>\n' +
        '  <div class="sk-circle11 sk-circle"></div>\n' +
        '  <div class="sk-circle12 sk-circle"></div>\n' +
        '</div>');
    if(file_obj != undefined) {
        var form_data = new FormData();
        form_data.append('file', file_obj);
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "ajax/uploadfile.php", true);
        xhttp.onload = function(event) {
            $('.drop-zone').removeClass('dropped drag-over');
            $('.upload-status').html('');
            oOutput = document.querySelector('.upload-status');
            if (xhttp.status == 200) {
                if (editor) {
                    var cursorPosition = editor.getCursorPosition();
                    // Insert text (second argument) with given position
                    editor.session.insert(cursorPosition, this.responseText);
                }
            } else {
                oOutput.innerHTML = "Error " + xhttp.status + " occurred when trying to upload your file.";
            }
        }
        xhttp.send(form_data);
    }
}

let submitbuttonclicked = false;

$('#regularboringsave').click(function(){
    submitbuttonclicked = 'save';
});

$('[name=moveas]').click(function(){
    submitbuttonclicked = 'moveas';
});

$('[name=saveas]').click(function(){
    submitbuttonclicked = 'saveas';
})

$('[name=delete]').click(function(){
    submitbuttonclicked = 'delete';
})

// patching FORM - the style of data handling on server can remain untouched
$("form.frmCodeSave").on("submit", function(evt) {
    var data = {};
    var $form = $(evt.target);
    var arr = $form.serializeArray(); // an array of all form items
    for (var i=0; i<arr.length; i++) { // transforming the array to object
        data[arr[i].name] = arr[i].value;
    }
    if (submitbuttonclicked) {
        data[submitbuttonclicked] = 1;
    }
    data.return_type = "json"; // optional identifier - you can handle it on server and respond with JSON instead of HTML output
    console.log(data);
    $.ajax({
        url: 'ajax/savefile.php', // server script from form action attribute or document URL (if action is empty or not specified)
        type: $form.attr('method') || 'get', // method by form method or GET if not specified
        dataType: 'json', // we expect JSON in response
        data: data // object with all form items
    }).done(function(respond) {
        console.log("data handled on server - response:", respond);
        if (respond.success){
            if (respond.forwardto){
                location.href = respond.forwardto;
            }else {
                $('main.transformed_content').html(respond.contents_html);
            }
        }else if (respond.error){
            alert(respond.error);
        }
    }).fail(function(){
        alert("Server connection failed!");
    });

    return false; // suppress default submit action
});
