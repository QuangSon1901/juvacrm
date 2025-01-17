function quillTemplate(elementId, height) {
    let editor = document.querySelector(elementId);
    editor.style.height = height;
    editor.style.maxHeight = height;
    editor.style.overflowY = "auto";

    let toolbarOptions = [
        [{ font: [] }],
        [{ size: ["small", false, "large", "huge"] }], // custom dropdown
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        ["bold", "italic", "underline", "strike"], // toggled buttons
        [{ color: [] }, { background: [] }], // dropdown with defaults from theme
        [{ script: "sub" }, { script: "super" }], // superscript/subscript
        [{ blockquote: true }, { "code-block": true }],
        [{ list: "ordered" }, { list: "bullet" }],
        [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
        [{ direction: "rtl" }], // text direction
        [{ align: [] }],
        ["link", "image", "video"],
        ["formula"],
        ["clean"], // remove formatting button
    ];

    const quill = new Quill(editor, {
        modules: {
            toolbar: toolbarOptions,
        },
        theme: "snow",
    });

    // quill.getModule('toolbar').addHandler('image', function() {
    //     imageHandlerQuill(quill);
    // });

    // quill.on('text-change', function(delta, oldDelta, source) {
    //     if (source === 'user') {
    //         handleImagePasteQuill(quill);
    //     }
    // });

    return quill;
}

function imageHandlerQuill(quill) {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();
    input.onchange = async function() {
        if (input !== null && input.files !== null) {
            const file = input.files[0];
            $("#loading-full-size").addClass("show");
            const res = await uploadFileTemplate(file, 0);
            $("#loading-full-size").removeClass("show");
            const range = quill.getSelection();
            if (range) {
                quill.insertEmbed(range.index, 'image', domainGateway + res.data[0].data.path);
            }
        }
    };
}

function handleImagePasteQuill(quill) {
    let editor = quill.root;
    let images = editor.querySelectorAll('img');
    images.forEach( async function(img) {
        if (img.src.startsWith('data:')) {
            $("#loading-full-size").addClass("show");
            const file = base64ToFile(img.src, 'image.png');
            const res = await uploadFileTemplate(file, 0);
            $("#loading-full-size").removeClass("show");
            img.src = domainGateway + res.data[0].data.path; // Replace base64 with URL
        }
    });
}

function base64ToFile(base64, filename) {
    let arr = base64.split(',');
    let mime = arr[0].match(/:(.*?);/)[1];
    let bstr = atob(arr[1]);
    let n = bstr.length;
    let u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
}
