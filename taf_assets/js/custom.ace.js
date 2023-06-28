var config = {
    theme: "ace/theme/monokai",
    selectionStyle: "text",
    readOnly: true,
    showLineNumbers: false,
    showGutter: false
};
// configuration pour un code PHP
for (const one_element of document.querySelectorAll(".ace_php")) {
    var editor_ace = ace.edit(one_element, config);
    editor_ace.session.setMode({
        path: "ace/mode/php",
        inline: true
    });
    var beautify = ace.require("ace/ext/beautify");
    beautify.beautify(editor_ace.session);
}

// configuration pour un code JS
for (const one_element of document.querySelectorAll(".ace_js")) {
    var editor_ace = ace.edit(one_element, config);
    editor_ace.session.setMode({
        path: "ace/mode/javascript",
        inline: true
    });
    var beautify = ace.require("ace/ext/beautify");
    beautify.beautify(editor_ace.session);
}
// configuration pour un code html
for (const one_element of document.querySelectorAll(".ace_html")) {
    var editor_ace = ace.edit(one_element, config);
    editor_ace.session.setMode({
        path: "ace/mode/html",
        inline: false,

    });
    var beautify = ace.require("ace/ext/beautify");
    beautify.beautify(editor_ace.session);
}
