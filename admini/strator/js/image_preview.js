/* Function modified from:
    https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsDataURL */
function previewFile(preview_id, input_id) {
    const reader    = new FileReader();
    const form_data = new FormData();
    const preview   = document.getElementById(preview_id);
    const file      = document.getElementById(input_id).files[0];

    reader.addEventListener("load", () => { 
        preview.src = reader.result;
    }, false);

    if (file) {
        reader.readAsDataURL(file);

        jQuery.each(jQuery('#profile-avatar')[0].files, function(i, file) {
            form_data.append('file-'+i, file);
        });

        $.ajax({
            url: '/system/upload?type=avatar',
            method: 'POST',
            data: form_data,
            headers: {'X-Avatar-Upload': '1' },
            processData: false,
            contentType: false,
            cache: false,
            success : function(data) {
                $("#uploadHelp").fadeOut();
                $("#uploadHelp > small:nth-child(1)").css('color', 'green');
                $("#uploadHelp > small:nth-child(1)").text("Uploaded Successfully!"); 
                $("#uploadHelp").fadeIn("slow");
                $("#uploadHelp").fadeOut("slow");
                $("#upload-reply").innerText = data;
            },
            error: function(data) {
                $("#upload-reply").innerText = data;
            }
        });
    }
}